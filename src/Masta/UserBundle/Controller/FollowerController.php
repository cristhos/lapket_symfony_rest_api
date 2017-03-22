<?php

namespace Masta\UserBundle\Controller;

use Masta\UserBundle\Entity\Follower;
use Masta\UserBundle\Entity\User;

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
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Hateoas\Representation\Factory\PagerfantaFactory;
use Hateoas\Configuration\Route;

/**
 * Rest controller for user follower
 *
 * @package Masta\UserBundle\Controller
 * @author Cristal Dibwe <cristallithos@gmail.com>
 */
class FollowerController extends FOSRestController
{

    /**
     * List all followers to specifique user.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing user followers.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many user Followers to return.")
     *
     * @Annotations\View()
     *
     * @param Request               $request      the request object
     * @param int                   $user_id     the product user_id
     *
     * @return array
     */
    public function getFollowersAction(Request $request,$slug)
    {
        $limit = $request->query->getInt('limit', 18);
        $page = $request->query->getInt('page', 1);

        $em = $this->getDoctrine()->getManager();
        $author = $em->getRepository('MastaUserBundle:User')->findByUsername($slug);
        $followersPager = $em->getRepository('MastaUserBundle:Follower')->getFollowersAuthor($limit, $page,$author);

        //checking array()
        $this->container->get('masta_plateforme.checkor')->checkFollowers($followersPager);

        $pagerFactory = new PagerfantaFactory();

        return $pagerFactory->createRepresentation(
            $followersPager,
            new Route('get_followers', array('limit' => $limit,'page' => $page,'slug' =>$slug))
        );
    }

    /**
     * List all followers to specifique user.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing user followers.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many user Followers to return.")
     *
     * @Annotations\View()
     *
     * @param Request               $request      the request object
     * @param int                   $user_id     the product user_id
     *
     * @return array
     */
    public function getFollowingAction(Request $request,$slug)
    {
        $limit = $request->query->getInt('limit', 18);
        $page = $request->query->getInt('page', 1);

        $em = $this->getDoctrine()->getManager();
        $author = $em->getRepository('MastaUserBundle:User')->findByUsername($slug);
        $followersPager = $em->getRepository('MastaUserBundle:Follower')->getFollowingAuthor($limit, $page,$author);

        //checking array()
        $this->container->get('masta_plateforme.checkor')->checkFollows($followersPager);
        $pagerFactory = new PagerfantaFactory();

        return $pagerFactory->createRepresentation(
            $followersPager,
            new Route('get_following', array('limit' => $limit,'page' => $page,'slug' =>$slug))
        );
    }

    /**
     * Get a single UserFollower.
     *
     * @ApiDoc(
     *   output = "Masta\UserBundle\Entity\Follower",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user follower is not found"
     *   }
     * )
     *
     * @Annotations\View(templateVar="user_followers")
     *
     * @param Request $request the request object
     * @param int     $user_id      the user followers id
     *
     * @return array
     *
     * @throws NotFoundHttpException when product not exist
     */
    public function getFollowerAction(Request $request, $user_id)
    {
        $em = $this->getDoctrine()->getManager();
        $user_follower = $em->getRepository('MastaUserBundle:Follower')->findOneByAuthor($user_id);
        if (null === $user_follower) {
            throw $this->createNotFoundException("user follower does not exist.");
        }

        $view = new View($user_follower);
        $group = $this->container->get('security.context')->isGranted('ROLE_API') ? 'restapi' : 'standard';
        $view->getSerializationContext()->setGroups(array('Default', $group));

        return $view;
    }


    /**
     * follow interessed.
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
     * @param int     $user_id      the user id
     * @param Request $request the request object
     *
     */
    public function postFollowerAction(Request $request,$user_id)
    {
      if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
          throw new AccessDeniedException();
      }

        $user = $this->getUser();
        $pass = true;
        $em = $this->getDoctrine()->getManager();
        $user_followed = $em->getRepository('MastaUserBundle:User')->find($user_id);

        foreach($user->getFollowers() as $u_followed)
        {
          if($u_followed->getUserFollowed() == $user_followed)
          {
              $pass = false;
              break;
          }
        }

        if($user != $user_followed){
          if($pass){
            $user_follower = new Follower();
            $user_follower->setAuthor($user);
            $user_follower->setUserFollowed($user_followed);

            $em->persist($user_follower);
            $em->flush();
            //service de notification
            $o_followed = $em->getRepository('MastaUserBundle:Follower')->findOneByAuthor($user);
            $notice = $this->container->get('masta_plateforme.notificator')->nofify($o_followed);
          }
        }

        $slug = $user_followed->getUsername();
        $userManager = $this->container->get('fos_user.user_manager');
        $entity = $userManager->findUserByUsernameOrEmail($slug);
        $this->container->get('masta_plateforme.checkor')->checkUser($entity);
        
        $view = View::create();
        $view->setData($entity)->setStatusCode(200);

        return $view;
    }



    /**
     * Removes a follower.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     204="Returned when successful"
     *   }
     * )
     *
     * @param Request $request the request object
     * @param int     $user_id      the follower id
     *
     * @return View
     */
    public function deleteFollowerAction(Request $request, $user_id)
    {
        $em = $this->getDoctrine()->getManager();
        $user_follower = $em->getRepository('MastaUserBundle:User')->find($user_id);
        $followed = $em->getRepository('MastaUserBundle:Follower')->findOneByUserFollowed($user_follower);

        $em = $this->getDoctrine()->getManager();
        $em->remove($followed);
        $em->flush();

        $slug = $user_follower->getUsername();

        $entity = $em->getRepository('MastaUserBundle:User')->findOneByUsername($slug);
        
        $this->container->get('masta_plateforme.checkor')->checkUser($entity);
        
        $view = View::create();
        $view->setData($entity)->setStatusCode(200);

        return $view;
    }

    /**
     * Removes a follower.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     204="Returned when successful"
     *   }
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the user follower id
     *
     * @return View
     */
    public function removeFollowerAction(Request $request, $id)
    {
        return $this->deleteFollowerAction($request, $id);
    }
}
