<?php

namespace Masta\UserBundle\Controller;

use Masta\UserBundle\Entity\City;
use Masta\UserBundle\Entity\User;
use Masta\UserBundle\Entity\Country;

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
 * Rest controller for user City
 *
 * @package Masta\UserBundle\Controller
 * @author Cristal Dibwe <cristallithos@gmail.com>
 */
class CityController extends FOSRestController
{

    /**
     * List all Cities 
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing user cities.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many user cities to return.")
     *
     * @Annotations\View()
     *
     * @param Request               $request      the request object
     *
     * @return array
     */
    public function getCitiesAction(Request $request)
    {
        $limit = $request->query->getInt('limit', 30);
        $page = $request->query->getInt('page', 1);

        $em = $this->getDoctrine()->getManager();
        $citiesPager = $em->getRepository('MastaUserBundle:City')->getCities($limit, $page);

        $pagerFactory = new PagerfantaFactory();

        return $pagerFactory->createRepresentation(
            $citiesPager,
            new Route('get_cities', array('limit' => $limit,'page' => $page))
        );
    }

        /**
     * List all Cities 
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing user cities.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many user cities to return.")
     *
     * @Annotations\View()
     *
     * @param Request               $request      the request object
     * @param Request               $country      the coutry id param transform in country object
     *
     * @return array
     */
    public function getCitiesCountryAction(Request $request,Country $country)
    {
        $limit = $request->query->getInt('limit', 30);
        $page = $request->query->getInt('page', 1);

        $em = $this->getDoctrine()->getManager();
        $citiesPager = $em->getRepository('MastaUserBundle:City')->getCitiesInCountry($limit, $page,$country);

        $pagerFactory = new PagerfantaFactory();

        return $pagerFactory->createRepresentation(
            $citiesPager,
            new Route('get_cities_country', array('limit' => $limit,'page' => $page,'country'=>$country->getId()))
        );
    }


    /**
     * Get a single UserCity.
     *
     * @ApiDoc(
     *   output = "Masta\UserBundle\Entity\City",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user City is not found"
     *   }
     * )
     *
     * @Annotations\View(templateVar="user_city")
     *
     * @param Request $request the request object
     * @param int     $id      the city id
     *
     * @return array
     *
     * @throws NotFoundHttpException when product not exist
     */
    public function getCityAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $city = $em->getRepository('MastaUserBundle:City')->find($id);
        if (null === $city) {
            throw $this->createNotFoundException("user City does not exist.");
        }

        $view = new View($city);
        return $view;
    }


}
