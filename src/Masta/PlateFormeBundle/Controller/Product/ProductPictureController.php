<?php
namespace Masta\PlateFormeBundle\Controller\Product;

use Masta\ProductBundle\Entity\ProductPicture;

use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Hateoas\Representation\Factory\PagerfantaFactory;
use Hateoas\Configuration\Route;

/**
 * Rest controller for product picture
 *
 * @package Masta\ProductBundle\Controller
 * @author Cristal Dibwe <cristallithos@gmail.com>
 */
class ProductPictureController extends FOSRestController
{

    /**
     * List all followers to specifique product.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing product votes.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many productvotes to return.")
     *
     * @Annotations\View()
     *
     * @param Request               $request      the request object
     * @param int                   $product_id      the product product_sid
     *
     * @return array
     */
    public function getProductPicturesAction(Request $request,$product_id)
    {
        $limit = $request->query->getInt('limit', 5);
        $page = $request->query->getInt('page', 1);

        $em = $this->getDoctrine()->getManager();
        $productFollowersPager = $em->getRepository('MastaProductBundle:ProductPicture')->getProductPictures($limit, $page,$product_id);

        $pagerFactory = new PagerfantaFactory();

        return $pagerFactory->createRepresentation(
            $productFollowersPager,
            new Route('get_product_pictures', array('limit' => $limit,'page' => $page,'product_id' => $product_id))
        );
    }

    /**
     * Get a single ProductFollower.
     *
     * @ApiDoc(
     *   output = "Masta\ProductBundle\Entity\ProductFollower",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the product vote is not found"
     *   }
     * )
     *
     * @Annotations\View(templateVar="product_followers")
     *
     * @param Request $request the request object
     * @param int     $id      the product followers id
     *
     * @return array
     *
     * @throws NotFoundHttpException when product not exist
     */
    public function getProductPictureAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $productFollower = $em->getRepository('MastaProductBundle:ProductFollower')->find($id);
        if (null === $productFollower) {
            throw $this->createNotFoundException("productFollower does not exist.");
        }

        $view = new View($productFollower);
        $group = $this->container->get('security.context')->isGranted('ROLE_API') ? 'restapi' : 'standard';
        $view->getSerializationContext()->setGroups(array('Default', $group));

        return $view;
    }


    /**
     * Creates a new product from the submitted data.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "Masta\ProductBundle\Form\ProductFollowerType",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *   template = "Masta\ProductBundle:Product:newProductVote.html.twig",
     *   statusCode = Response::HTTP_BAD_REQUEST
     * )
     *
     * @param Request $request the request object
     * @param int     $product_id     the product id
     *
     * @return FormTypeInterface[]|View
     */
    public function postProductPictureAction(Request $request,$product_id)
    {
        $product = new ProductFollower();
        //apres vote retourner success
        return array('succes');
    }

    /**
     * Removes a productVote.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     204="Returned when successful"
     *   }
     * )
     *
     * @param Request $request the request object
     * @param int     $product_id               the product product_id
     * @param int     $productFollower_id       the productFollower vote_id
     *
     * @return View
     */
    public function deleteProductPictureAction(Request $request,$product_id,$productFollower_id)
    {
        return "successful";
    }
}