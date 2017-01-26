<?php

namespace Masta\PlateFormeBundle\Controller\Product;

use Masta\PlateFormeBundle\Form\Product\ProductType;
use Masta\PlateFormeBundle\Entity\Product\Product;
use Masta\PlateFormeBundle\Entity\Product\ProductPicture;
use Masta\PlateFormeBundle\Entity\Album\Album;
use Masta\PlateFormeBundle\Entity\Picture\Picture;

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
 * Rest controller for products
 *
 * @package Masta\PlateFormeBundle\Controller\Product
 * @author Cristal Dibwe <cristallithos@gmail.com>
 */
class ProductController extends FOSRestController
{

       /**
     * List all product last.
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
     * @param Request               $request      the request object
     *
     * @return array
     */
    public function getLastProductsAction(Request $request)
    {
        $limit = $request->query->getInt('limit', 6);
        $page = $request->query->getInt('page', 1);

        $em = $this->getDoctrine()->getManager();
        $productsPager = $em->getRepository('MastaPlateFormeBundle:Product\Product')->getLastProducts($limit, $page);

        $pagerFactory = new PagerfantaFactory();

        //checking
        $this->container->get('masta_plateforme.checkor')->checkProducts($productsPager);

        //viewit
        $this->container->get('masta_plateforme.viewor')->viewIts($productsPager);
        

        return $pagerFactory->createRepresentation(
            $productsPager,
            new Route('get_last_products', array('limit' => $limit,'page' => $page))
        );
    }
    
    /**
     * List all product fil.
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
     * @param Request               $request      the request object
     *
     * @return array
     */
    public function getFilProductsAction(Request $request)
    {
      if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
          throw new AccessDeniedException();
      }
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $limit = $request->query->getInt('limit', 4);
        $page = $request->query->getInt('page', 1);

        $em = $this->getDoctrine()->getManager();
        $productsPager = $em->getRepository('MastaPlateFormeBundle:Product\Product')->getFilProducts($limit, $page, $user);

        $pagerFactory = new PagerfantaFactory();


        //checking
        $this->container->get('masta_plateforme.checkor')->checkProducts($productsPager);

        //viewit
        $this->container->get('masta_plateforme.viewor')->viewIts($productsPager);
        

        return $pagerFactory->createRepresentation(
            $productsPager,
            new Route('get_fil_products', array('limit' => $limit,'page' => $page))
        );
    }

    /**
     * List prodroducts by album.
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
     * @param Request               $request      the request object
     * @param int                   $album_id      the album id
     *
     * @return array
     */
    public function getProductsAlbumAction(Request $request,$album_id)
    {
        $user = $this->getUser();
        $limit = $request->query->getInt('limit', 12);
        $page = $request->query->getInt('page', 1);


        $em = $this->getDoctrine()->getManager();
        $productsPager = $em->getRepository('MastaPlateFormeBundle:Product\Product')->getProductsByAlbum($limit, $page,$album_id);

        $pagerFactory = new PagerfantaFactory();

        //checking
        $this->container->get('masta_plateforme.checkor')->checkProducts($productsPager);

        return $pagerFactory->createRepresentation(
            $productsPager,
            new Route('get_products_album', array('limit' => $limit,'page' => $page,'album_id' => $album_id))
        );
    }


        /**
         * List prodroducts by cagegory.
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
         * @param Request               $request      the request object
         * @param int                   $album_id      the album id
         *
         * @return array
         */
        public function getProductsCategoryAction(Request $request,$category_id)
        {
            $user = $this->getUser();
            $limit = $request->query->getInt('limit', 8);
            $page = $request->query->getInt('page', 1);



            $em = $this->getDoctrine()->getManager();
            $productsPager = $em->getRepository('MastaPlateFormeBundle:Product\Product')->getProductsByCategory($limit, $page,$category_id);

            $pagerFactory = new PagerfantaFactory();

            //checking array()
            $this->container->get('masta_plateforme.checkor')->checkProducts($productsPager);

            return $pagerFactory->createRepresentation(
                $productsPager,
                new Route('get_products_category', array('limit' => $limit,'page' => $page,'category_id' => $category_id))
            );
        }


    /**
     * short List prodroducts by album.
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
     * @param Request               $request      the request object
     * @param int                   $album_id      the album id
     *
     * @return array
     */
    public function getShortProductsAlbumAction(Request $request,$album_id)
    {
        $user = $this->getUser();
        $limit = $request->query->getInt('limit', 2);
        $page = $request->query->getInt('page', 1);


        $em = $this->getDoctrine()->getManager();
        $productsPager = $em->getRepository('MastaPlateFormeBundle:Product\Product')->getProductsByAlbum($limit, $page,$album_id);

        $pagerFactory = new PagerfantaFactory();

        //checking array()
        $this->container->get('masta_plateforme.checkor')->checkProducts($productsPager);

        return $pagerFactory->createRepresentation(
            $productsPager,
            new Route('get_products_album', array('limit' => $limit,'page' => $page,'album_id' => $album_id))
        );
    }

    /**
     * List products by author.
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
     * @param Request               $request      the request object
     * @param string                $slug         the username or e-mail
     *
     * @return array
     */
    public function getProductsAuthorAction(Request $request,$slug)
    {
        $limit = $request->query->getInt('limit', 12);
        $page = $request->query->getInt('page', 1);

        $em = $this->getDoctrine()->getManager();
        $author = $em->getRepository('MastaUserBundle:User')->findOneByUsername($slug);
        $productsPager = $em->getRepository('MastaPlateFormeBundle:Product\Product')->getProductsByUser($limit, $page,$slug);

        $pagerFactory = new PagerfantaFactory();

        //checking array()
        $this->container->get('masta_plateforme.checkor')->checkProducts($productsPager);

        return $pagerFactory->createRepresentation(
            $productsPager,
            new Route('get_products_author', array('limit' => $limit,'page' => $page,'slug' => $slug))
        );
    }

    /**
     * List products by author.
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
     * @param Request               $request      the request object
     * @param string                $slug         the username or e-mail
     *
     * @return array
     */
    public function getProductsVoteByAction(Request $request,$slug)
    {
        $limit = $request->query->getInt('limit', 5);
        $page = $request->query->getInt('page', 1);

        $em = $this->getDoctrine()->getManager();
        $author = $em->getRepository('MastaUserBundle:User')->findOneByUsername($slug);
        $productsPager = $em->getRepository('MastaPlateFormeBundle:Product\Product')->getProductsVoteByUser($limit, $page, $author);

        $pagerFactory = new PagerfantaFactory();

        //checking array()
        $this->container->get('masta_plateforme.checkor')->checkProducts($productsPager);

        return $pagerFactory->createRepresentation(
            $productsPager,
            new Route('get_products_vote_by', array('limit' => $limit,'page' => $page,'slug' => $slug))
        );
    }

    /**
     * List products  expired for user session.
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
     * @param Request               $request      the request object
     *
     * @return array
     */
    public function getMyProductsExpiredAction(Request $request)
    {
        if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
          throw new AccessDeniedException();
       }
      $user = $this->get('security.token_storage')->getToken()->getUser();

        $limit = $request->query->getInt('limit', 5);
        $page = $request->query->getInt('page', 1);

        $em = $this->getDoctrine()->getManager();
        $productsPager = $em->getRepository('MastaPlateFormeBundle:Product\Product')->getProductsExpired($limit, $page, $user);

        $pagerFactory = new PagerfantaFactory();

        //checking array()
        $this->container->get('masta_plateforme.checkor')->checkProducts($productsPager);

        return $pagerFactory->createRepresentation(
            $productsPager,
            new Route('get_my_products_expired', array('limit' => $limit,'page' => $page))
        );
    }

    /**
     * List products by term.
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
     * @Annotations\QueryParam(name="term", requirements="\d", nullable=true, description="term from which to start listing products.")
     *
     * @Annotations\View()
     *
     * @param Request               $request      the request object
     * @param string                $term         the username or e-mail
     *
     * @return array
     */
    public function getProductsTermAction(Request $request)
    {
        $limit = $request->query->getInt('limit', 5);
        $page = $request->query->getInt('page', 1);
        $term = $request->query->get('term');

        $em = $this->getDoctrine()->getManager();
        $productsPager = $em->getRepository('MastaPlateFormeBundle:Product\Product')->getProductsTerm($limit, $page, $term);

        $pagerFactory = new PagerfantaFactory();

        //checking array()
        $this->container->get('masta_plateforme.checkor')->checkProducts($productsPager);

        return $pagerFactory->createRepresentation(
            $productsPager,
            new Route('get_products_term', array('limit' => $limit,'page' => $page,'term' => $term))
        );
    }

    /**
     * Get a single product.
     *
     * @ApiDoc(
     *   output = "Masta\ProductBundle\Entity\Product",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the product is not found"
     *   }
     * )
     *
     * @Annotations\View(templateVar="product")
     *
     * @param Request $request the request object
     * @param int     $id      the product id
     *
     * @return array
     *
     * @throws NotFoundHttpException when product not exist
     */
    public function getProductAction(Request $request, $id)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $product = $em->getRepository('MastaPlateFormeBundle:Product\Product')->find($id);
        if (null === $product) {
            throw $this->createNotFoundException("product does not exist.");
        }

        //viewit for one product
        $this->container->get('masta_plateforme.viewor')->viewIt($product);
        //checking one product
        $this->container->get('masta_plateforme.checkor')->checkProduct($product);

        $view = new View($product);
        $group = $this->container->get('security.context')->isGranted('ROLE_API') ? 'restapi' : 'standard';
        $view->getSerializationContext()->setGroups(array('Default', $group));



        return $view;
    }


    /**
     * Creates a new product from the submitted data.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "Masta\ProductBundle\Form\ProductType",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @param Request $request the request object
     * @param Request $album   convert id to Album object
     *
     * @return View
     */
    public function postProductAction(Request $request)
    {
        if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw new AccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); 

        $user = $this->get('security.token_storage')->getToken()->getUser();
        $name = $request->get('name');
        $description = $request->get('description');
        $price = $request->get('price');
        $picture = $em->getRepository('MastaPlateFormeBundle:Picture\Picture')->find($request->get('picture'));
        $album = $em->getRepository('MastaPlateFormeBundle:Album\Album')->find($request->get('album_id'));
        $stat = $em->getRepository('MastaPlateFormeBundle:Stat\Stat')->findOneByName('statistique');
        
        $product = new Product();
        $product->setAuthor($user);
        $product->setDescription($description);
        $product->setName($name);
        $product->setPrice($price);
        $product->setAlbum($album);
        $product->setPicture($picture);
        $product->setStat($stat);
               
        $em->persist($product);
        $em->flush();
        
        $view = new View($product);
        return $view;
    }

    /**
     * Update existing product from the submitted data or create a new product at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "Masta\ProductBundle\Form\ProductType",
     *   statusCodes = {
     *     201 = "Returned when a new resource is created",
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *   template="Masta\ProductBundle:Product:editProduct.html.twig",
     *   templateVar="form"
     * )
     *
     * @param Request $request the request object
     * @param int     $product      the id converted to product object
     *
     * @return array()
     *
     * @throws NotFoundHttpException when product not exist
     */
    public function putProductAction(Request $request, Product $product)
    {
        if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw new AccessDeniedException();
        }
        $user = $this->get('security.token_storage')->getToken()->getUser();
    
        $em = $this->getDoctrine()->getManager();
       
        $name = $request->get('name');
        $description = $request->get('description');
        $price = $request->get('price');

        $oldPicture = $product->getPicture();
        $picture = $em->getRepository('MastaPlateFormeBundle:Picture\Picture')->find($request->get('picture'));
        if($oldPicture != $picture)
        {
            $product->setPicture($picture);
            $em->remove($oldPicture);
            $em->flush();
        }
        else
        {
            $product->setPicture($oldPicture);
        }

        $album = $em->getRepository('MastaPlateFormeBundle:Album\Album')->find($request->get('album_id'));
        $product->setAuthor($user);
        $product->setDescription($description);
        $product->setName($name);
        $product->setPrice($price);
        $product->setAlbum($album);

        $em->persist($product);
        $em->flush();

         //viewit for one product
        $this->container->get('masta_plateforme.viewor')->viewIt($product);

        //checking one product
        $this->container->get('masta_plateforme.checkor')->checkProduct($product);
        
        $view = new View($product);
        return $view;
    }

    /**
     * Update existing product (put expired : true or false).
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "Masta\ProductBundle\Form\ProductType",
     *   statusCodes = {
     *     201 = "Returned when a new resource is created",
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @param Request $request the request object
     * @param int     $product      the id converted to product object
     *
     * @return array()
     *
     * @throws NotFoundHttpException when product not exist
     */
    public function putProductExpiredAction(Request $request, Product $product)
    {
        if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw new AccessDeniedException();
        }
        $user = $this->get('security.token_storage')->getToken()->getUser();
    
        $em = $this->getDoctrine()->getManager();
        
        if($product->getAuthor() == $user)
        {
            if($product->getIsExpired())
            {
                $product->setIsExpired(false);
            }
            else
            {
                $product->setIsExpired(true);
            }
            $em->persist($product);
            $em->flush();
        }
        
         //viewit for one product
        $this->container->get('masta_plateforme.viewor')->viewIt($product);

        //checking one product
        $this->container->get('masta_plateforme.checkor')->checkProduct($product);
        
        $view = new View($product);
        return $view;
    }

    /**
     * Removes a prodroduct.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     204="Returned when successful"
     *   }
     * )
     *
     * @param Request $request the request object
     * @param int     $product      the product id
     *
     * @return View
     */
    public function deleteProductAction(Request $request, Product $product)
    {
         $em = $this->getDoctrine()->getManager();
         $em->remove($product); 
         $em->flush();

        return 1;
    }

    /**
     * Removes a product.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     204="Returned when successful"
     *   }
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the product id
     *
     * @return array()
     */
    public function removeProductAction(Request $request, Product $product)
    {
        if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw new AccessDeniedException();
        }

        $user = $this->get('security.token_storage')->getToken()->getUser();

        if($product->getAuthor() == $user){
            return $this->deleteProductAction($request, $product);
        }
        
        return 0;
    }

}
