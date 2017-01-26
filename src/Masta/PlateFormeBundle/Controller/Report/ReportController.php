<?php

namespace Masta\PlateFormeBundle\Controller\Report;

use Masta\PlateFormeBundle\Form\Report\ReportType;
use Masta\PlateFormeBundle\Entity\Report\Report;

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
 * Rest controller for categories
 *
 * @package Masta\CategoryBundle\Controller\Report
 * @author Cristal Dibwe <cristallithos@gmail.com>
 */
class ReportController extends FOSRestController
{

    /**
     * List all reports.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="page", requirements="\d+", nullable=true, description="Page from which to start listing reports.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many reports to return.")
     *
     * @Annotations\View()
     *
     * @param Request $request the request object
     *
     * @return array
     */
    public function getReportsAction(Request $request)
    {
        $limit = $request->query->getInt('limit', 5);
        $page = $request->query->getInt('page', 1);

        $em = $this->getDoctrine()->getManager();
        $categoriesPager = $em->getRepository('MastaReportBundle:Report')->getReports($limit, $page);

        $pagerFactory = new PagerfantaFactory();

        return $pagerFactory->createRepresentation(
            $categoriesPager,
            new Route('get_reports', array('limit' => $limit,'page' => $page))
        );
    }

    /**
     * Get a single report.
     *
     * @ApiDoc(
     *   output = "Masta\ReportBundle\Entity\Report",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the category is not found"
     *   }
     * )
     *
     * @Annotations\View(templateVar="category")
     *
     * @param Request $request the request object
     * @param int     $id      the report id
     *
     * @return array
     *
     * @throws NotFoundHttpException when category not exist
     */
    public function getReportAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository('MastaReportBundle:Report')->find($id);
        if (null === $category) {
            throw $this->createNotFoundException("report does not exist.");
        }

        $view = new View($category);
        $group = $this->container->get('security.context')->isGranted('ROLE_API') ? 'restapi' : 'standard';
        $view->getSerializationContext()->setGroups(array('Default', $group));

        return $view;
    }



    /**
     * Creates a report from the submitted data.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "Masta\CategoryBundle\Form\CategoryType",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *   template = "Masta\CategoryBundle:Category:newCategory.html.twig",
     *   statusCode = Response::HTTP_BAD_REQUEST
     * )
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface[]|View
     */
    public function postReportAction(Request $request)
    {
        $report = new Report();
        $form = $this->createForm(new CategoryType(), $report);

        $form->submit($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($report);
            $em->flush();
            return $this->routeRedirectView('get_report', array('id' => $report->getId()));
        }

        return array(
            'form' => $form
        );
    }

    /**
     * Presents the form to use to update an existing report.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     200 = "Returned when successful",
     *     404 = "Returned when the category is not found"
     *   }
     * )
     *
     * @Annotations\View()
     *
     * @param Request $request the request object
     * @param int     $id      the category id
     *
     * @return FormTypeInterface
     *
     * @throws NotFoundHttpException when category not exist
     */
    public function editReportAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository('MastaReportBundle:Report')->find($id);
        if (false === $category) {
            throw $this->createNotFoundException("report does not exist.");
        }

        $form = $this->createForm(new CategoryType(), $category);

        return $form;
    }

    /**
     * Update existing report from the submitted data or create a new report at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "Masta\CategoryBundle\Form\CategoryType",
     *   statusCodes = {
     *     201 = "Returned when a new resource is created",
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *   template="Masta\CategoryBundle:Category:editCategory.html.twig",
     *   templateVar="form"
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the report id
     *
     * @return FormTypeInterface|RouteRedirectView
     *
     * @throws NotFoundHttpException when category not exist
     */
    public function putReportAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $report = $em->getRepository('MastaReportBundle:Report')->find($id);
        if (false === $report) {
            $report = new Report();
            $report->setId($id);
            $statusCode = Response::HTTP_CREATED;
        } else {
            $statusCode = Response::HTTP_NO_CONTENT;
        }

        $form = $this->createForm(new CategoryType(), $report);

        $form->submit($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($report);
            $em->flush();
            return $this->routeRedirectView('get_report', array('id' => $report->getId()), $statusCode);
        }

        return $form;
    }

    /**
     * Removes a category.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     204="Returned when successful"
     *   }
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the report id
     *
     * @return View
     */
    public function deleteReportAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $report = $em->getRepository('MastaReportBundle:Report')->find($id);
        $em->remove($report);
        $em->flush();

        // There is a debate if this should be a 404 or a 204
        // see http://leedavis81.github.io/is-a-http-delete-requests-idempotent/
        return $this->routeRedirectView('get_report', array(), Response::HTTP_NO_CONTENT);
    }

    /**
     * Removes a report.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     204="Returned when successful"
     *   }
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the report id
     *
     * @return View
     */
    public function removeReportAction(Request $request, $id)
    {
        return $this->deleteReportAction($request, $id);
    }
}
