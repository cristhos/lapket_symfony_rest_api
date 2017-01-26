<?php
namespace Masta\PlateFormeBundle\Controller\Picture;

use Masta\PlateFormeBundle\Entity\Picture\Picture;
use Masta\PlateFormeBundle\Form\Picture\PictureType;
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
use Symfony\Component\HttpFoundation\File\UploadedFile;
/**
 * Rest controller for Pictures
 *
 * @package Masta\PictureBundle\Controller\Picture
 * @author Cristal Dibwe <cristallithos@gmail.com>
 */
class PictureController extends FOSRestController
{


    /**
     * Get a single picture.
     *
     * @ApiDoc(
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the picture is not found"
     *   }
     * )
     *
     * @Annotations\View(templateVar="picture")
     *
     * @param Request $request the request object
     * @param int     $id      the picture id
     *
     * @return array
     *
     * @throws NotFoundHttpException when picture not exist
     */
    public function getPictureAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $picture = $em->getRepository('MastaPlateFormeBundle:Picture\Picture')->find($id);
        if (false === $picture) {
            throw $this->createNotFoundException("picture does not exist.");
        }

        //checking array()
       $this->container->get('masta_plateforme.checkor')->checkPicture($picture);

        $view = new View($picture);

        return $view;
    }



    /**
     * Creates a new picture from the submitted data.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "Masta\PictureBundle\Form\PictureType",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     *
     * @param Request $request the request object
     *
     * @return array()
     */
    public function postPictureAction(Request $request)
    {
        $picture = new Picture();
        $picture->setFile($request->files->get('file'));
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($picture);
        $em->flush();
        
        //checking array()
        $this->container->get('masta_plateforme.checkor')->checkPicture($picture);

        $view = new View($picture);
        return $view;
    }



    /**
     * Removes a picture.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     204="Returned when successful"
     *   }
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the picture id
     *
     * @return View
     */
    public function deletePictureAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $picture = $em->getRepository('MastaPlateFormeBundle:Picture\Picture')->find($id);
        $em->remove($picture);
        $em->flush();
        // There is a debate if this should be a 404 or a 204
        // see http://leedavis81.github.io/is-a-http-delete-requests-idempotent/
        return $this->routeRedirectView('get_picture', array(), Response::HTTP_NO_CONTENT);
    }

    /**
     * Removes a picture.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     204="Returned when successful"
     *   }
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the picture id
     *
     * @return View
     */
    public function removePictureAction(Request $request, $id)
    {
        return $this->deletePictureAction($request, $id);
    }
}
