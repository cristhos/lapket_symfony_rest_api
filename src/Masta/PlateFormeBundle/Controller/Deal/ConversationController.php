<?php

namespace Masta\PlateFormeBundle\Controller\Deal;

use Masta\PlateFormeBundle\Entity\Deal\Conversation;
use Masta\PlateFormeBundle\Entity\Product\Product;
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

use Hateoas\Representation\Factory\PagerfantaFactory;
use Hateoas\Configuration\Route;

/**
 * Rest controller for Deal Conversations
 *
 * @package Masta\PlateFormeBundle\Controller\Deal
 * @author Cristal Dibwe <cristallithos@gmail.com>
 */
class ConversationController extends FOSRestController
{


    /**
     * List all conversations for user session.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="page", requirements="\d+", nullable=true, description="Page from which to start listing Deals.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many Deals to return.")
     *
     * @Annotations\View()
     *
     * @param Request               $request               the request object
     *
     * @return array
     */
    public function getMyConversationsAction(Request $request)
    {

      if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
          throw new AccessDeniedException();
      }

        $user = $this->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        $limit = $request->query->getInt('limit', 8);
        $page = $request->query->getInt('page', 1);
        $conversationsPager = $em->getRepository('MastaPlateFormeBundle:Deal\Conversation')->getMyConversations($limit, $page,$user);

        //checking array()
        $this->container->get('masta_plateforme.checkor')->checkConversations($conversationsPager);

        $pagerFactory = new PagerfantaFactory();
        return $pagerFactory->createRepresentation(
            $conversationsPager,
            new Route('get_my_conversations', array('limit' => $limit,'page' => $page))
        );
    }


    /**
     * Get a single conversation.
     *
     * @ApiDoc(
     *   output = "Masta\DealBundle\Entity\Deal\Conversation",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the Deal is not found"
     *   }
     * )
     *
     * @Annotations\View(templateVar="Converation")
     *
     * @param Request $request the request object
     * @param int     $id      the Conversation id
     *
     * @return array
     *
     * @throws NotFoundHttpException when Deal not exist
     */
    public function getConversationAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $conversation = $em->getRepository('MastaPlateFormeBundle:Deal\Conversation')->find($id);
        if (false === $conversation) {
            throw $this->createNotFoundException("Conversation does not exist.");
        }
        
         //checking array()
        $this->container->get('masta_plateforme.checkor')->checkConversation($conversation);
        $view = new View($conversation);

        return $view;
    }


    /**
     * Creates a new Conversation from the submitted data.
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
     * @param Request $request the request object
     * @param $product_id
     * @return array()
     */
    public function postConversationAction(Request $request, Product $product_id)
    {
      if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
          throw new AccessDeniedException();
      }

      //cree une conversation si elle n'existe pas et on mais le message

      $em = $this->getDoctrine()->getManager();

      $user = $this->get('security.token_storage')->getToken()->getUser();



      if($product_id->getAuthor() != $user)
      {
        $conversation = $em->getRepository('MastaPlateFormeBundle:Deal\Conversation')
                          ->
                          findOneBy(array('author' =>$user,'product'=> $product_id));
        if($conversation == null)
        {
          $conversation = new Conversation();
          $conversation->setAuthor($user);
          $conversation->setProduct($product_id);
          $em->persist($conversation);
          $em->flush();
          $conversation1 = $em->getRepository('MastaPlateFormeBundle:Deal\Conversation')
                            ->findOneBy(array('author' =>$user,'product'=> $product_id));
          $view = new View($conversation1);
        }else {
          $view = new View($conversation);
        }
      }else{
        throw new AccessDeniedException();
      }
      return $view;
    }

    /**
     * Removes a Conversation.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     204="Returned when successful"
     *   }
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the Conversation id
     *
     * @return View
     */
    public function deleteConversationAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $conversation = $em->getRepository('MastaDealBundle:Deal/Conversation')->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($conversation);
        $em->flush();

        // There is a debate if this should be a 404 or a 204
        // see http://leedavis81.github.io/is-a-http-delete-requests-idempotent/
        return $this->routeRedirectView('get_conversation', array(), Response::HTTP_NO_CONTENT);
    }

    /**
     * Removes a Conversation.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     204="Returned when successful"
     *   }
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the Deal id
     *
     * @return View
     */
    public function removeConversationAction(Request $request, $conversation_id)
    {
        return $this->deleteConversationAction($request, $conversation_id);
    }

    
}
