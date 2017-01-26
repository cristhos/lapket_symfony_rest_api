<?php

namespace Masta\PlateFormeBundle\Controller\Album;

use Masta\PlateFormeBundle\Entity\Album\Album;
use Masta\PlateFormeBundle\Entity\Category\Category;

use Masta\PlateFormeBundle\Form\Album\AlbumType;

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
 * Rest controller for albums
 *
 * @package Masta\AlbumBundle\Controller\Album
 * @author Cristal Dibwe <cristallithos@gmail.com>
 */
class AlbumController extends FOSRestController
{

    /**
     * List all albums.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="page", requirements="\d+", nullable=true, description="Page from which to start listing albums.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many albums to return.")
     *
     * @Annotations\View()
     *
     * @param Request               $request      the request object
     *
     * @return array
     */
    public function getAlbumsAction(Request $request)
    {
        $limit = $request->query->getInt('limit', 12);
        $page = $request->query->getInt('page', 1);

        $em = $this->getDoctrine()->getManager();
        $albumsPager = $em->getRepository('MastaPlateFormeBundle:Album\Album')->getAlbums($limit, $page);

        //checking array()
        $this->container->get('masta_plateforme.checkor')->checkAlbums($albumsPager);

        $pagerFactory = new PagerfantaFactory();

        return $pagerFactory->createRepresentation(
            $albumsPager,
            new Route('get_albums', array('limit' => $limit,'page' => $page))
        );
    }

    /**
     * List all albums.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="page", requirements="\d+", nullable=true, description="Page from which to start listing albums.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many albums to return.")
     *
     * @Annotations\View()
     *
     * @param Request               $request      the request object
     *
     * @return array
     */
    public function getMyAlbumsAction(Request $request)
    {
        $user = $this->getUser();
        $limit = $request->query->getInt('limit', 12);
        $page = $request->query->getInt('page', 1);
        $em = $this->getDoctrine()->getManager();
        $albumsPager = $em->getRepository('MastaPlateFormeBundle:Album\Album')->getAlbumsByUser($limit, $page, $user->getUsername());

        //checking array()
        $this->container->get('masta_plateforme.checkor')->checkAlbums($albumsPager);

        $pagerFactory = new PagerfantaFactory();

        return $pagerFactory->createRepresentation(
            $albumsPager,
            new Route('get_my_albums', array('limit' => $limit,'page' => $page))
        );
    }

    /**
     * List all albums for category.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="page", requirements="\d+", nullable=true, description="Page from which to start listing albums.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many albums to return.")
     *
     * @Annotations\View()
     *
     * @param Request               $request               the request object
     * @param int                   $category_id           the category id
     *
     * @return array
     */
    public function getAlbumsCategoryAction(Request $request,$category_id)
    {
        $limit = $request->query->getInt('limit', 12);
        $page = $request->query->getInt('page', 1);

        $em = $this->getDoctrine()->getManager();
        $albumsPager = $em->getRepository('MastaPlateFormeBundle:Album\Album')->getAlbumsCategory($limit, $page,$category_id);

        //checking array()
        $this->container->get('masta_plateforme.checkor')->checkAlbums($albumsPager);

        $pagerFactory = new PagerfantaFactory();

        return $pagerFactory->createRepresentation(
            $albumsPager,
            new Route('get_albums_category', array('limit' => $limit,'page' => $page,'category_id' => $category_id))
        );
    }


    /**
     *  Short List all albums for category.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="page", requirements="\d+", nullable=true, description="Page from which to start listing albums.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many albums to return.")
     *
     * @Annotations\View()
     *
     * @param Request               $request               the request object
     * @param int                   $category_id           the category id
     *
     * @return array
     */
    public function getShortAlbumsCategoryAction(Request $request,$category_id)
    {
        $limit = $request->query->getInt('limit', 2);
        $page = $request->query->getInt('page', 1);

        $em = $this->getDoctrine()->getManager();
        $albumsPager = $em->getRepository('MastaPlateFormeBundle:Album\Album')->getAlbumsCategory($limit, $page,$category_id);


        //checking array()
        $this->container->get('masta_plateforme.checkor')->checkAlbums($albumsPager);

        $pagerFactory = new PagerfantaFactory();

        return $pagerFactory->createRepresentation(
            $albumsPager,
            new Route('get_albums_category', array('limit' => $limit,'page' => $page,'category_id' => $category_id))
        );
    }

    /**
     * List albums by author.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many products to return.")
     * @Annotations\QueryParam(name="page", requirements="\d+", nullable=true, description="Page from which to start listing products.")
     *
     * @Annotations\View()
     *
     * @param Request       $request      the request object
     * @param string        $slug         the username or e-mail
     *
     * @return array
     */
    public function getAlbumsAuthorAction(Request $request,$slug)
    {
        $limit = $request->query->getInt('limit', 6);
        $page = $request->query->getInt('page', 1);

        $em = $this->getDoctrine()->getManager();
        $albumsPager = $em->getRepository('MastaPlateFormeBundle:Album\Album')->getAlbumsByUser($limit, $page, $slug);

        //checking array()
        $this->container->get('masta_plateforme.checkor')->checkAlbums($albumsPager);

        $pagerFactory = new PagerfantaFactory();

        return $pagerFactory->createRepresentation(
            $albumsPager,
            new Route('get_albums_author', array('limit' => $limit,'page' => $page,'slug' => $slug))
        );
    }

    /**
     * Get a single album.
     *
     * @ApiDoc(
     *   output = "Masta\AlbumBundle\Entity\Album",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the album is not found"
     *   }
     * )
     *
     * @Annotations\View(templateVar="album")
     *
     * @param Request $request the request object
     * @param int     $id      the album id
     *
     * @return array
     *
     * @throws NotFoundHttpException when album not exist
     */
    public function getAlbumAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $album = $em->getRepository('MastaPlateFormeBundle:Album\Album')->find($id);
        if (false === $album) {
            throw $this->createNotFoundException("album does not exist.");
        }


        //checking
        $this->container->get('masta_plateforme.checkor')->checkAlbum($album);

        $view = new View($album);
        $group = $this->container->get('security.context')->isGranted('ROLE_API') ? 'restapi' : 'standard';
        $view->getSerializationContext()->setGroups(array('Default', $group));

        return $view;
    }

    /**
     * Presents the form to use to create a new album.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\View()
     *
     * @return FormTypeInterface
     */
    public function newAlbumAction()
    {
        $tokenStorage = $this->get('security.token_storage');
        return $this->createForm(new AlbumType($tokenStorage));
    }

    /**
     * Creates a new album from the submitted data.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "Masta\AlbumBundle\Form\AlbumType",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface[]|View
     */
    public function postAlbumAction(Request $request)
    {
      if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) throw new AccessDeniedException();
      
      $em = $this->getDoctrine()->getManager();

      $user = $this->get('security.token_storage')->getToken()->getUser();
      $name = $request->get('name');
      $description =  $request->get('description');
      $category = $em->getRepository('MastaPlateFormeBundle:Category\Category')->find($request->get('category'));
      $stat = $em->getRepository('MastaPlateFormeBundle:Stat\Stat')->findOneByName('statistique');

      $album = new Album();
      $album->setAuthor($user);
      $album->setName($name);
      $album->setDescription($description);
      $album->setCategory($category);
      $album->setStat($stat);

      $em->persist($album);
      $em->flush();

      $data = 1;
      return $data;
    }

    /**
     * Presents the form to use to update an existing album.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     200 = "Returned when successful",
     *     404 = "Returned when the album is not found"
     *   }
     * )
     *
     * @Annotations\View()
     *
     * @param Request $request the request object
     * @param int     $id      the album id
     *
     * @return FormTypeInterface
     *
     * @throws NotFoundHttpException when album not exist
     */
    public function editAlbumAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $album = $em->getRepository('MastaPlateFormeBundle:Album')->find($id);;
        if (false === $album) {
            throw $this->createNotFoundException("album does not exist.");
        }

        $form = $this->createForm(new AlbumType(), $album);

        return $form;
    }

    /**
     * Update existing album from the submitted data or create a new album at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "Masta\AlbumBundle\Form\AlbumType",
     *   statusCodes = {
     *     201 = "Returned when a new resource is created",
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *   template="Masta\AlbumBundle:Album:editAlbum.html.twig",
     *   templateVar="form"
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the album id
     *
     * @return FormTypeInterface|RouteRedirectView
     *
     * @throws NotFoundHttpException when album not exist
     */
    public function putAlbumAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $album = $em->getRepository('MastaPlateFormeBundle:Album')->find($id);
        if (false === $album) {
            $album = new Album();
            $album->setId($id);
            $statusCode = Response::HTTP_CREATED;
        } else {
            $statusCode = Response::HTTP_NO_CONTENT;
        }

        $form = $this->createForm(new AlbumType(), $album);

        $form->submit($request);
        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($album);
            $em->flush();
            return $this->routeRedirectView('get_album', array('id' => $album->getId()), $statusCode);
        }

        return $form;
    }

    /**
     * Removes a album.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     204="Returned when successful"
     *   }
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the album id
     *
     * @return View
     */
    public function deleteAlbumAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $album = $em->getRepository('MastaAlbumBundle:Album')->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($album);
        $em->flush();

        // There is a debate if this should be a 404 or a 204
        // see http://leedavis81.github.io/is-a-http-delete-requests-idempotent/
        return $this->routeRedirectView('get_album', array(), Response::HTTP_NO_CONTENT);
    }

    /**
     * Removes a album.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     204="Returned when successful"
     *   }
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the album id
     *
     * @return View
     */
    public function removeAlbumAction(Request $request, $id)
    {
        return $this->deleteAlbumAction($request, $id);
    }
}
