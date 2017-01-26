<?php

namespace Masta\PlateFormeBundle\Controller\Deal;

use Masta\PlateFormeBundle\Entity\Deal\Deal;
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
 * Rest controller for Deals
 *
 * @package Masta\PlateFormeBundle\Controller\Deal
 * @author Cristal Dibwe <cristallithos@gmail.com>
 */
class DealController extends FOSRestController
{

    /**
     * List all Deals.
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
    public function getDealsAction(Request $request)
    {
        $limit = $request->query->getInt('limit', 12);
        $page = $request->query->getInt('page', 1);

        $em = $this->getDoctrine()->getManager();
        $dealsPager = $em->getRepository('MastaPlateFormeBundle:Deal\Deal')->getDeals($limit, $page);

        //Verification
        $this->checkDealsAction($dealsPager,$this->getUser());

        $pagerFactory = new PagerfantaFactory();

        return $pagerFactory->createRepresentation(
            $DealsPager,
            new Route('get_deals', array('limit' => $limit,'page' => $page))
        );
    }

    /**
     * List all Deals.
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
    public function getMyDealsAction(Request $request)
    {
        $user = $this->getUser();
        $limit = $request->query->getInt('limit', 12);
        $page = $request->query->getInt('page', 1);
        $em = $this->getDoctrine()->getManager();
        $dealsPager = $em->getRepository('MastaPlateFormeBundle:Deal\Deal')->getDealsByUser($limit, $page, $user);
        //Verification
        $this->checkDealsAction($dealsPager,$this->getUser());

        $pagerFactory = new PagerfantaFactory();

        return $pagerFactory->createRepresentation(
            $DealsPager,
            new Route('get_my_deals', array('limit' => $limit,'page' => $page))
        );
    }

    /**
     * List all Deals for specifique products.
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
     * @param int                   $product_id            the product id
     *
     * @return array
     */
    public function getDealsProductAction(Request $request,Product $product_id)
    {

      if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
          throw new AccessDeniedException();
      }
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $limit = $request->query->getInt('limit', 12);
        $page = $request->query->getInt('page', 1);
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $dealsPager = $em->getRepository('MastaPlateFormeBundle:Deal\Deal')->getDealsProduct($limit, $page,$product_id,$user);

        //Verification
        $this->checkDealsAction($dealsPager,$user);

        $pagerFactory = new PagerfantaFactory();

        return $pagerFactory->createRepresentation(
            $dealsPager,
            new Route('get_deals_product', array('limit' => $limit,'page' => $page,'product_id' => $product_id->getId()))
        );
    }






    /**
     * Get a single Deal.
     *
     * @ApiDoc(
     *   output = "Masta\DealBundle\Entity\Deal",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the Deal is not found"
     *   }
     * )
     *
     * @Annotations\View(templateVar="Deal")
     *
     * @param Request $request the request object
     * @param int     $id      the Deal id
     *
     * @return array
     *
     * @throws NotFoundHttpException when Deal not exist
     */
    public function getDealAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $deal = $em->getRepository('MastaPlateFormeBundle:Deal\Deal')->find($id);
        if (false === $deal) {
            throw $this->createNotFoundException("Deal does not exist.");
        }

        //Verification
        $this->checkDealAction($deal,$this->getUser());

        $view = new View($deal);

        return $view;
    }


    /**
     * Creates a new Deal from the submitted data.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "Masta\DealBundle\Form\DealType",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     *
     * @param Request $request the request object
     * @param $product_id
     * @param $destinathor_id
     * @return array()
     */
    public function postDealAction(Request $request, Product $product_id)
    {
      if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
          throw new AccessDeniedException();
      }

      $em = $this->getDoctrine()->getManager();

      $user = $this->get('security.token_storage')->getToken()->getUser();

      $content = $request->get('body');
      $destinathor_id = $request->get('destinathor_id');
      $destinathor = $em->getRepository('MastaUserBundle:User')->find($destinathor_id);

      if($user != $destinathor){
        $deal = new Deal();
        $deal->setAuthor($user);
        $deal->setContent($content);
        $deal->setProduct($product_id);
        $deal->setDestinathor($destinathor);
        $em->persist($deal);
        $em->flush();
        $data = 1;
      }else{
        $data = 0;
      }

      return $data;
    }

    /**
     * Removes a Deal.
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
    public function deleteDealAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $deal = $em->getRepository('MastaDealBundle:Deal/Deal')->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($deal);
        $em->flush();

        // There is a debate if this should be a 404 or a 204
        // see http://leedavis81.github.io/is-a-http-delete-requests-idempotent/
        return $this->routeRedirectView('get_deal', array(), Response::HTTP_NO_CONTENT);
    }

    /**
     * Removes a Deal.
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
    public function removeDealAction(Request $request, $deal_id)
    {
        return $this->deleteDealAction($request, $deal_id);
    }

    public function checkDealsAction($deals,$user)
    {
      foreach ($deals as $deal )
      {
        $this->checkDealAction($deal,$user);
      }
    }
    public function checkDealAction($deal,$user){
      if($deal->getAuthor()->getProfilePicture() !== null)
      {

          $deal->getAuthor()->getProfilePicture()->setWebPath('http://apis.mapket.com/web/'.$deal->getAuthor()->getProfilePicture()->getWebPath());

      }
        if($deal->getAuthor() == $user)
        {
          $deal->setIsAuthor(true);
        }
        else
        {
          $deal->setIsAuthor(false);
        }
    }
}
