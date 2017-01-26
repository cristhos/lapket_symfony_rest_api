<?php

namespace Masta\PlateFormeBundle\Controller\Product;

use Masta\ProductBundle\Entity\ProductHashtag;

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
 * Rest controller for product hashtags
 *
 * @package Masta\ProductBundle\Controller
 * @author Cristal Dibwe <cristallithos@gmail.com>
 */
class ProductHashtagController extends FOSRestController
{

    /**
     * List all hashtags to specifique product.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing product hashtags.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many product hashtags to return.")
     *
     * @Annotations\View()
     *
     * @param Request               $request      the request object
     * @param int                   $product_id   the product id
     *
     * @return array
     */
    public function getProducthashtagsAction(Request $request,$product_id)
    {
        $limit = $request->query->getInt('limit', 5);
        $page = $request->query->getInt('page', 1);

        $em = $this->getDoctrine()->getManager();
        $productViewsPager = $em->getRepository('MastaProductBundle:ProductHashtag')->getProductHashtags($limit, $page,$product_id);

        $pagerFactory = new PagerfantaFactory();

        return $pagerFactory->createRepresentation(
            $productViewsPager,
            new Route('get_product_views', array('limit' => $limit,'page' => $page,'product_id' =>$product_id))
        );
    }

    /**
     * Get a single Product View.
     *
     * @ApiDoc(
     *   output = "Masta\ProductBundle\Entity\ProductView",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the product views is not found"
     *   }
     * )
     *
     * @Annotations\View(templateVar="product_hashtag")
     *
     * @param Request $request the request object
     * @param int     $id      the product hashtag id
     *
     * @return array
     *
     * @throws NotFoundHttpException when product hashtag not exist
     */
    public function getProductHashtagAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $product_hashtag = $em->getRepository('MastaProductBundle:ProductHashtag')->find($id);
        if (null === $product_hashtag) {
            throw $this->createNotFoundException("product view does not exist.");
        }

        $view = new View($product_hashtag);
        $group = $this->container->get('security.context')->isGranted('ROLE_API') ? 'restapi' : 'standard';
        $view->getSerializationContext()->setGroups(array('Default', $group));

        return $view;
    }


    /**
     * Creates a new product hashtag .
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
     * @param int     $product_id      the product id
     * @param Request $request        the request object
     *
     * @return array
     */
    public function postProductHashtagAction(Request $request,$product_id)
    {
        $product_hashtag = new ProductHashtag();
        $product_hashtag->setProduct($product_id);
        $em = $this->getDoctrine()->getManager();
        $em->persist($product_hashtag);
        $em->flush();

        return $this->routeRedirectView('get_product_hashtag', array('id' => $product_hashtag->getId()));
    }



    /**
     * Removes a product hashtag.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     204="Returned when successful"
     *   }
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the product view id
     *
     * @return View
     */
    public function deleteProductHashtagAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $product_hashtag = $em->getRepository('MastaProductBundle:ProductHashtag')->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($product_hashtag);
        $em->flush();

        // There is a debate if this should be a 404 or a 204
        // see http://leedavis81.github.io/is-a-http-delete-requests-idempotent/
        return $this->routeRedirectView('get_product_vote', array(), Response::HTTP_NO_CONTENT);
    }

    /**
     * Removes a product hashtag.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     204="Returned when successful"
     *   }
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the product hashtag id
     *
     * @return View
     */
    public function removeProductHashtagAction(Request $request, $id)
    {
        return $this->deleteProductHashtagAction($request, $id);
    }
}
