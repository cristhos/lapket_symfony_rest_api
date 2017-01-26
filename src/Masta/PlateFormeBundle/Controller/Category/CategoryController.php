<?php

namespace Masta\PlateFormeBundle\Controller\Category;

use Masta\PlateFormeBundle\Form\Category\CategoryType;
use Masta\PlateFormeBundle\Entity\Category\Category;

use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Hateoas\Representation\Factory\PagerfantaFactory;
use Hateoas\Configuration\Route;

/**
 * Rest controller for categories
 *
 * @package Masta\CategoryBundle\Controller\Category
 * @author Cristal Dibwe <cristallithos@gmail.com>
 */
class CategoryController extends FOSRestController
{

    /**
     * List all categories.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="page", requirements="\d+", nullable=true, description="Page from which to start listing categories.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many categories to return.")
     *
     * @Annotations\View()
     *
     * @param Request $request the request object
     *
     * @return array
     */
    public function getCategoriesAction(Request $request)
    {
        $limit = $request->query->getInt('limit', 18);
        $page = $request->query->getInt('page', 1);

        $em = $this->getDoctrine()->getManager();
        $categoriesPager = $em->getRepository('MastaPlateFormeBundle:Category\Category')->getCategories($limit, $page);

        $pagerFactory = new PagerfantaFactory();

        //Verification
          $user = $this->getUser();
          $this->checkCategoriesAction($categoriesPager,$user);

        return $pagerFactory->createRepresentation(
            $categoriesPager,
            new Route('get_categories', array('limit' => $limit,'page' => $page))
        );
    }

    /**
     * List all categories.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="page", requirements="\d+", nullable=true, description="Page from which to start listing categories.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many categories to return.")
     *
     * @Annotations\View()
     *
     * @param Request $request the request object
     *
     * @return array
     */
    public function getMyFollowCategoriesAction(Request $request)
    {
        if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
          throw new AccessDeniedException();
        }

      $user = $this->get('security.token_storage')->getToken()->getUser();
        $limit = $request->query->getInt('limit', 12);
        $page = $request->query->getInt('page', 1);

        $em = $this->getDoctrine()->getManager();
        $categoriesPager = $em->getRepository('MastaPlateFormeBundle:Category\Category')->getCategoriesFollowByUser($limit, $page,$user);
        
        $pagerFactory = new PagerfantaFactory();

        //Verification
          $this->checkCategoriesAction($categoriesPager,$user);

        return $pagerFactory->createRepresentation(
            $categoriesPager,
            new Route('get_categories', array('limit' => $limit,'page' => $page))
        );
    }

    /**
     * Get a single category.
     *
     * @ApiDoc(
     *   output = "Masta\CategoryBundle\Entity\Category",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the category is not found"
     *   }
     * )
     *
     * @Annotations\View(templateVar="category")
     *
     * @param Request $request the request object
     * @param int     $id      the category id
     *
     * @return array
     *
     * @throws NotFoundHttpException when category not exist
     */
    public function getCategoryAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository('MastaPlateFormeBundle:Category\Category')->find($id);
        if (null === $category) {
            throw $this->createNotFoundException("product does not exist.");
        }

        //Verification
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $this->checkCategoryAction($category,$user);

        $view = new View($category);
    
        return $view;
    }

    


    public function checkCategoriesAction($categories,$user)
    {
      foreach ($categories as $category )
      {
        $this->checkCategoryAction($category,$user);
      }
    }

    public function checkCategoryAction($category,$user){
          $category->setIsFollow(false);
          $category->setIsAuthent(false);
          if($user != NULL)
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
