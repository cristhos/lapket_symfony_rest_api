<?php
namespace Masta\PlateFormeBundle\Controller\Notification;

use Masta\PlateFormeBundle\Entity\Notification\Notification;

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
 * Rest controller for notifcation
 *
 * @package Masta\NotificationBundle\Controller
 * @author Cristal Dibwe <cristallithos@gmail.com>
 */
class NotificationController extends FOSRestController
{

    /**
     * List all Notifications for user session.
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
     * @param Request               $request      the request object
     *
     * @return array
     */
    public function getMyNotificationsAction(Request $request)
    {
        if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
          throw new AccessDeniedException();
        }
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $limit = $request->query->getInt('limit', 8);
        $page = $request->query->getInt('page', 1);
        $em = $this->getDoctrine()->getManager();
        $notificationsPager = $em->getRepository('MastaPlateFormeBundle:Notification\Notification')->getMyNotifications($limit, $page, $user);

        //notifications vue()
        $notice = $this->container->get('masta_plateforme.notificator')->seen($notificationsPager);

        //checking array()
        $this->container->get('masta_plateforme.checkor')->checkNotifications($notificationsPager);


        $pagerFactory = new PagerfantaFactory();

        return $pagerFactory->createRepresentation(
            $notificationsPager,
            new Route('get_my_notifications', array('limit' => $limit,'page' => $page))
        );
    }


    /**
     * Get a single notification.
     *
     * @ApiDoc(
     *   output = "Masta\NotificationBundle\Entity\Notification",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the notification is not found"
     *   }
     * )
     *
     * @Annotations\View(templateVar="notification")
     *
     * @param Request $request the request object
     * @param int     $id      the notification id
     *
     * @return array
     *
     * @throws NotFoundHttpException when notification not exist
     */
    public function getNotificationAction(Request $request, $notification_id)
    {
        $em = $this->getDoctrine()->getManager();
        $notification = $em->getRepository('MastaPlateFormeBundle:Notification\Notification')->find($notification_id);
        if (null === $notification) {
            throw $this->createNotFoundException("notification does not exist.");
        }

        $view = new View($notification);
        $group = $this->container->get('security.context')->isGranted('ROLE_API') ? 'restapi' : 'standard';
        $view->getSerializationContext()->setGroups(array('Default', $group));

        return $view;
    }



    /**
     * Removes a notification.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     204="Returned when successful"
     *   }
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the notification id
     *
     * @return View
     */
    public function deleteNotificationAction(Request $request, $notification_id)
    {
        $em = $this->getDoctrine()->getManager();
        $notification = $em->getRepository('MastaPlateFormeBundle:Notification\Notification')->find($notification_id);
        $em->remove($notification);
        $em->flush();
        return 1;
    }

    /**
     * Removes a notification.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     204="Returned when successful"
     *   }
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the notification id
     *
     * @return View
     */
    public function removeNotificationAction(Request $request, $notification_id)
    {
        return $this->deleteNotificationAction($request, $notification_id);
    }

    /**
     * remove all notifications.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     204="Returned when successful"
     *   }
     * )
     *
     * @param Request $request the request object
     *
     * @return View
     */
    public function purgeMyNotificationAction(Request $request)
    {
        if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw new AccessDeniedException();
        }
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();
        $notifications = $em->getRepository('MastaPlateFormeBundle:Notification\Notification')->findByDestinator($user);
        foreach ($notifications  as $notification) 
        {
            $em->remove($notification);
            $em->flussh();
        }

        return 1;
    }
}
