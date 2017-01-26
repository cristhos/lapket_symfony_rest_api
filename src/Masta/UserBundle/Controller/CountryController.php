<?php

namespace Masta\UserBundle\Controller;

use Masta\UserBundle\Entity\Country;
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
 * Rest controller for user Country
 *
 * @package Masta\UserBundle\Controller
 * @author Cristal Dibwe <cristallithos@gmail.com>
 */
class CountryController extends FOSRestController
{

    /**
     * List all Countries 
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing user Countrys.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many user Countrys to return.")
     *
     * @Annotations\View()
     *
     * @param Request               $request      the request object
     *
     * @return array
     */
    public function getCountriesAction(Request $request)
    {
        $limit = $request->query->getInt('limit', 18);
        $page = $request->query->getInt('page', 1);

        $em = $this->getDoctrine()->getManager();
        $CountrysPager = $em->getRepository('MastaUserBundle:Country')->getCountries($limit, $page);

        $pagerFactory = new PagerfantaFactory();

        return $pagerFactory->createRepresentation(
            $CountrysPager,
            new Route('get_countries', array('limit' => $limit,'page' => $page))
        );
    }


    /**
     * Get a single UserCountry.
     *
     * @ApiDoc(
     *   output = "Masta\UserBundle\Entity\Country",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user Country is not found"
     *   }
     * )
     *
     * @Annotations\View(templateVar="user_country")
     *
     * @param Request $request the request object
     * @param int     $id      the country id
     *
     * @return array
     *
     * @throws NotFoundHttpException when product not exist
     */
    public function getCountryAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $country = $em->getRepository('MastaUserBundle:Country')->findr($id);
        if (null === $country) {
            throw $this->createNotFoundException("user Country does not exist.");
        }

        $view = new View($country);
        return $view;
    }


}
