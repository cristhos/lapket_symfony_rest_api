<?php

namespace Masta\PlateFormeBundle\Controller\Deal;

use Masta\PlateFormeBundle\Entity\Deal\Conversation;
use Masta\PlateFormeBundle\Entity\Deal\Message;
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
 * Rest controller for Deal Conversation Messages
 *
 * @package Masta\PlateFormeBundle\Controller\Deal
 * @author Cristal Dibwe <cristallithos@gmail.com>
 */
class MessageController extends FOSRestController
{

    /**
     * List all Messages.
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
     * @param Request               $request      the request object
     *
     * @return array
     */
    public function getMessagesAction(Request $request)
    {
        $limit = $request->query->getInt('limit', 12);
        $page = $request->query->getInt('page', 1);

        $em = $this->getDoctrine()->getManager();
        $messagesPager = $em->getRepository('MastaPlateFormeBundle:Deal\Message')->getConversationMessages($limit, $page);

        //Verification
        $this->checkDealsAction($messagesPager,$this->getUser());

        $pagerFactory = new PagerfantaFactory();

        return $pagerFactory->createRepresentation(
            $messagesPager,
            new Route('get_deals', array('limit' => $limit,'page' => $page))
        );
    }

    /**
     * List all messages of a specific conversation.
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
    public function getMessagesConversationAction(Request $request,Conversation $conversation)
    {

      if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
          throw new AccessDeniedException();
      }

        $user = $this->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        $limit = $request->query->getInt('limit', 6);
        $page = $request->query->getInt('page', 1);
        $messagesPager = $em->getRepository('MastaPlateFormeBundle:Deal\Message')->getMessagesConversation($limit, $page,$conversation,$user);

        //seen messages
        $this->messasgesSeenAction($messagesPager);
        //checking array()
        $this->container->get('masta_plateforme.checkor')->checkMessages($messagesPager);

        $pagerFactory = new PagerfantaFactory();
        return $pagerFactory->createRepresentation(
            $messagesPager,
            new Route('get_messages', array('limit' => $limit,'page' => $page,'conversation' => $conversation->getId()))
        );
    }


    /**
     * Get a single message.
     *
     * @ApiDoc(
     *   output = "Masta\DealBundle\Entity\Deal\Message",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the Deal is not found"
     *   }
     * )
     *
     * @Annotations\View(templateVar="Converation")
     *
     * @param Request $request the request object
     * @param int     $id      the Deal id
     *
     * @return array
     *
     * @throws NotFoundHttpException when Deal not exist
     */
    public function getMessageAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $conversation = $em->getRepository('MastaPlateFormeBundle:Deal\Message')->find($id);
        if (false === $deal) {
            throw $this->createNotFoundException("Conversation does not exist.");
        }

        $view = new View($Message);

        return $view;
    }


    /**
     * Creates a new Message from the submitted data.
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
    public function postMessageAction(Request $request, Conversation $conversation)
    {
      if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
          throw new AccessDeniedException();
      }

      $em = $this->getDoctrine()->getManager();
      $user = $this->get('security.token_storage')->getToken()->getUser();
      $content = $request->get('content');
      
      $message = new Message();
      $message->setAuthor($user);

      if($user == $conversation->getAuthor())
        $message->setReceiver($conversation->getProduct()->getAuthor());
      else
        $message->setReceiver($conversation->getAuthor());

      $message->setContent($content);
      $message->setConversation($conversation);
      $em->persist($message);
      $em->flush();

      $data = true;
      //il doit retourner la conversation
      return $data;
    }

    /**
     * Removes a Message.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     204="Returned when successful"
     *   }
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the Message id
     *
     * @return View
     */
    public function deleteMessageAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $message = $em->getRepository('MastaDealBundle:Deal/Message')->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($message);
        $em->flush();

        // There is a debate if this should be a 404 or a 204
        // see http://leedavis81.github.io/is-a-http-delete-requests-idempotent/
        return $this->routeRedirectView('get_message', array(), Response::HTTP_NO_CONTENT);
    }

    /**
     * Removes a Message.
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
    public function removeMessageAction(Request $request, $message_id)
    {
        return $this->deleteMessageAction($request, $message_id);
    }


    //update message if is seen
    public function messasgesSeenAction($messages)
    {
      $em = $this->getDoctrine()->getManager();
      foreach ($messages as $message)
      {
        if($message->getIsSeen() == false)
        {
            $message->setIsSeen(true);
            $em->persist($message);
            $em->flush();
            
            $user = $this->get('security.token_storage')->getToken()->getUser();
            $compteur = $user->getNbReceivedMessages();
            
            if($compteur>0)
                $user->setNbReceivedMessages($compteur-1);
            else
                $user->setNbReceivedMessages(0);
        
            $em->persist($user);
            $em->flush();
        }
        
      }
    }
}
