<?php

namespace Masta\PlateFormeBundle\Controller\Category;

use Masta\PlateFormeBundle\Entity\Category\CategoryFollower;

use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
//use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Hateoas\Representation\Factory\PagerfantaFactory;
use Hateoas\Configuration\Route;

/**
 * Rest controller for category followers
 *
 * @package Masta\AlbumBundle\Controller
 * @author Cristal Dibwe <cristallithos@gmail.com>
 */
class CategoryFollowerController extends FOSRestController
{

    /**
     * List all followers to specifique category.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing category followers.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many category Followers to return.")
     *
     * @Annotations\View()
     *
     * @param Request               $request      the request object
     * @param int                   $category_id     the category id
     *
     * @return array
     */
    public function getCategoryFollowersAction(Request $request,$category_id)
    {
        $limit = $request->query->getInt('limit', 5);
        $page = $request->query->getInt('page', 1);

        $em = $this->getDoctrine()->getManager();
        $categoryFollowersPager = $em->getRepository('MastaPlateFormeBundle:Category\CategoryFollower')->getCategoryFollowers($limit, $page,$category_id);

        $pagerFactory = new PagerfantaFactory();

        return $pagerFactory->createRepresentation(
            $categoryFollowersPager,
            new Route('get_category_followers', array('limit' => $limit,'page' => $page,'category_id' =>$category_id))
        );
    }

    /**
     * Get a single CategoryFollower.
     *
     * @ApiDoc(
     *   output = "Masta\CategoryBundle\Entity\CategoryFollower",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the product follower is not found"
     *   }
     * )
     *
     * @Annotations\View(templateVar="product_followers")
     *
     * @param Request $request the request object
     * @param int     $id      the category followers id
     *
     * @return array
     *
     * @throws NotFoundHttpException when product not exist
     */
    public function getCategoryFollowerAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $productFollower = $em->getRepository('MastaPlateFormeBundle:Category\CategoryFollower')->find($id);
        if (null === $productFollower) {
            throw $this->createNotFoundException("category followers does not exist.");
        }

        $view = new View($productFollower);
        $group = $this->container->get('security.context')->isGranted('ROLE_API') ? 'restapi' : 'standard';
        $view->getSerializationContext()->setGroups(array('Default', $group));

        return $view;
    }


    /**
     * post a category follow.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     *
     * @param int     $category_id      the category id
     */
    public function postCategoryFollowerAction(Request $request,$category_id)
    {
      if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
          throw new AccessDeniedException();
      }

      $user = $this->get('security.token_storage')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository('MastaPlateFormeBundle:Category\Category')->findOneById($category_id);

        $pass = true;
        foreach($category->getCategoryFollowers() as $c_follower)
        {
          if($c_follower->getAuthor() == $user)
          {
              $pass = false;
              break;
          }
        }
        if($pass){
          $category_followers = new CategoryFollower();
          $category_followers->setAuthor($user);
          $category_followers->setCategory($category);
          $em->persist($category_followers);
          $em->flush();
        }

        $this->checkCategoryAction($category,$user);
        $view = View::create();
        $view->setData($category)->setStatusCode(200);

        return $view;
    }



    /**
     * Removes a category follower.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     204="Returned when successful"
     *   }
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the category id
     *
     * @return View
     */
    public function deleteCategoryFollowerAction(Request $request, $category_id)
    {

      if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
          throw new AccessDeniedException();
      }

      $user = $this->get('security.token_storage')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();

        $category_follower = $em->getRepository('MastaPlateFormeBundle:Category\CategoryFollower')
                                ->findOneBy(array('author' => $user,'category' => $category_id));



        $em->remove($category_follower);
        $em->flush();

        $category = $em->getRepository('MastaPlateFormeBundle:Category\Category')
                                ->find($category_id);

        $this->checkCategoryAction($category,$user);

        $view = View::create();
        $view->setData($category)->setStatusCode(200);

        return $view;
    }

    /**
     * Removes a category.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     204="Returned when successful"
     *   }
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the category category_id
     *
     * @return View
     */
    public function removeCategoryFollowerAction(Request $request, $category_id)
    {
        return $this->deleteCategoryFollowerAction($request, $category_id);
    }

    public function checkCategoryAction($category,$user){
          $category->setIsFollow(false);
          $category->setIsAuthent(false);
          if($user !== NULL)
          {
            $category->setIsAuthent(true);
            foreach($category->getCategoryFollowers() as $categoryFollower )
            {
                if($categoryFollower->getAuthor() == $user)
                {
                  $category->setIsFollow(true);
                  break;
                }
            }
          }


    }
}
