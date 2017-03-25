<?php

namespace Masta\PlateFormeBundle\Controller\Product;

use Masta\PlateFormeBundle\Entity\Product\ProductVote;

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

use Hateoas\Representation\Factory\PagerfantaFactory;
use Hateoas\Configuration\Route;

/**
 * Rest controller for product votes
 *
 * @package Masta\ProductBundle\Controller
 * @author Cristal Dibwe <cristallithos@gmail.com>
 */
class ProductVoteController extends FOSRestController
{

    /**
     * List all votes to specifique product.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing product votes.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many procut votes to return.")
     *
     * @Annotations\View()
     *
     * @param Request               $request      the request object
     * @param int                   $product_id   the product id
     *
     * @return array
     */
    public function getProductVotesAction(Request $request,$product_id)
    {
        $limit = $request->query->getInt('limit', 5);
        $page = $request->query->getInt('page', 1);

        $em = $this->getDoctrine()->getManager();
        $productVotesPager = $em->getRepository('MastaPlateFormeBundle:Product\ProductVote')->getProductVotes($limit, $page,$product_id);

        $pagerFactory = new PagerfantaFactory();

        return $pagerFactory->createRepresentation(
            $productVotesPager,
            new Route('get_product_votes', array('limit' => $limit,'page' => $page,'product_id' =>$product_id))
        );
    }

    /**
     * Get a single Product Vote.
     *
     * @ApiDoc(
     *   output = "Masta\PlateFormeBundle\Entity\Product\ProductVote",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the product vote is not found"
     *   }
     * )
     *
     * @Annotations\View(templateVar="product_vote")
     *
     * @param Request $request the request object
     * @param int     $id      the product vote id
     *
     * @return array
     *
     * @throws NotFoundHttpException when product vote not exist
     */
    public function getProductVoteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $productVote = $em->getRepository('MastaPlateFormeBundle:Product\ProductVote')->find($id);
        if (null === $productVote) {
            throw $this->createNotFoundException("product vote does not exist.");
        }

        $view = new View($productVote);
        $group = $this->container->get('security.context')->isGranted('ROLE_API') ? 'restapi' : 'standard';
        $view->getSerializationContext()->setGroups(array('Default', $group));

        return $view;
    }


    /**
     * Creates a new product vote .
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
     * @param Request $request the request object
     *
     * @return View
     */
    public function postProductVoteAction(Request $request)
    {
      if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
           throw new AccessDeniedException();
       }
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();
        $product_id = $request->get('product_id');
        $product = $em->getRepository('MastaPlateFormeBundle:Product\Product')->findOneById($product_id);
        
        //checking
        $pass = true;
        $view = View::create();

        foreach($product->getVotes() as $vote)
        {
          if($vote->getAuthor() == $user)
          {
            $pass = false;
            break;
          }
        }
        if($pass)
        {
          $productVote = new ProductVote();
          $productVote->setAuthor($user);
          $productVote->setProduct($product);
          $em->persist($productVote);
          $em->flush();
          
          //service de notification
          $notice = $this->container->get('masta_plateforme.notificator')->nofify($product);
          //envoi de mail
           if($productVote->getAuthor() != $product->getAuthor())
           {
                $message = \Swift_Message::newInstance()
                    ->setSubject("Vous avez recus un vote")
                    ->setFrom("monsite@domain.com")
                    ->setTo($productVote->getProduct()->getAuthor()->getEmail())
                    ->setBody($this->renderView(
                             'MastaPlateFormeBundle:Email:product_vote.html.twig',array('product_vote' => $productVote),
                             'text/html'
                             ));
            $this->get('swiftmailer.mailer')->send($message);
           }

            $data = ['status' => true ];
            $view->setData($data)->setStatusCode(200);
            
            return $view;
        }
        else
        {
            $data = ['status' => false ];
            $view->setData($data)->setStatusCode(400);
            
            return $view;
        }

    }



    /**
     * Removes a product vote.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     204="Returned when successful"
     *   }
     * )
     *
     * @param Request $request the request object
     * @param int     $product_id      the product vote id
     *
     * @return View
     */
    public function deleteProductVoteAction(Request $request, $product_id)
    {
      if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
          throw new AccessDeniedException();
      }

      $user = $this->get('security.token_storage')->getToken()->getUser();
      $em = $this->getDoctrine()->getManager();
      $product_vote = $em->getRepository('MastaPlateFormeBundle:Product\ProductVote')
                         ->findOneBy(array('author' => $user,'product' => $product_id));

      $em = $this->getDoctrine()->getManager();
      $em->remove($product_vote);
      $em->flush();

      $view = View::create();
      $data = ['status' => false ];
      $view->setData($data)->setStatusCode(200);
      return $view;
    }

    /**
     * Removes a product vote.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     204="Returned when successful"
     *   }
     * )
     *
     * @param Request $request the request object
     * @param int     $product_id      the product id
     *
     * @return View
     */
    public function removeVoteAction(Request $request, $product_id)
    {
        return $this->deleteProductVoteAction($request, $product_id);
    }

    


    /**
     * desactive vote notification.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing product votes.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many procut votes to return.")
     *
     * @Annotations\View()
     *
     * @param Request               $request      the request object
     * @param int                   $product_id   the product id
     *
     * @return array
     */
    public function putVoteDesactiveNotifyAction(Request $request,$product_id)
    {
        if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
          throw new AccessDeniedException();
        }
        $user = $this->get('security.token_storage')->getToken()->getUser();
        
        $em = $this->getDoctrine()->getManager();
        $productVote = $em->getRepository('MastaPlateFormeBundle:Product\ProductVote')
                           ->findOneBy(array('author' => $user,'product' => $product_id));
        $productVote->setIsNotify(false);

        $em->persist($productVote);
        $em->flush();
        return 1;
    }

    /**
     * desactive vote notification.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing product votes.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many procut votes to return.")
     *
     * @Annotations\View()
     *
     * @param Request               $request      the request object
     * @param int                   $product_id   the product id
     *
     * @return array
     */
    public function putVoteActiveNotifyAction(Request $request,$product_id)
    {
        if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
          throw new AccessDeniedException();
        }
        $user = $this->get('security.token_storage')->getToken()->getUser();
        
        $em = $this->getDoctrine()->getManager();
        $productVote = $em->getRepository('MastaPlateFormeBundle:Product\ProductVote')
                           ->findOneBy(array('author' => $user,'product' => $product_id));
        $productVote->setIsNotify(true);
        
        $em->persist($productVote);
        $em->flush();
        return 1;
    }

}
