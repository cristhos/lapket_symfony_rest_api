<?php

namespace Masta\UserBundle\Controller;

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
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Hateoas\Representation\Factory\PagerfantaFactory;
use Hateoas\Configuration\Route;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;



/**
 * Rest controller for users
 *
 * @package Masta\UserBundle\Controller
 * @author Cristal Dibwe <cristallithos@gmail.com>
 */
class UserController extends FOSRestController
{
    /**
     * Get a single user.
     *
     * @ApiDoc(
     *   output = "Masta\UserBundle\Entity\User",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @Annotations\View(templateVar="user")
     *
     * @param Request $request the request object
     * @param(name="slug", requirements = ".+",description ="the user username or e-mail" )
     *
     * @return array
     *
     * @throws NotFoundHttpException when user not exist
     */
    public function getUserAction(Request $request, $slug)
    {
        $userManager = $this->container->get('fos_user.user_manager');
        $entity = $userManager->findUserByUsernameOrEmail($slug);

        //checking array()
        $this->container->get('masta_plateforme.checkor')->checkUser($entity);

        if (!$entity) {
            throw $this->createNotFoundException('User not found.');
        }

        $view = View::create();
        $view->setData($entity)->setStatusCode(200);

        return $view;
    }

    /**
     * Get a single user status.
     *
     * @ApiDoc(
     *   output = "Masta\UserBundle\Entity\User",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @Annotations\View(templateVar="user")
     *
     * @param Request $request the request object
     *
     * @return array
     *
     * @throws NotFoundHttpException when user not exist
     */
    public function getUserStatusAction(Request $request)
    {
      if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
          throw new AccessDeniedException();
      }

      $user = $this->get('security.token_storage')->getToken()->getUser();

      //checking array()
      $this->container->get('masta_plateforme.checkor')->checkUser($user);
      $view = View::create();
      $view->setData($user)->setStatusCode(200);
      return $view;
    }

    /**
     * Presents the form to use to create a new user.
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
    public function newUserAction()
    {
        if (false === $this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw new AccessDeniedException();
        }
        return $this->createForm(new UserType());
    }

    /**
     * Creates a new user from the submitted data.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "Masta\UserBundle\Form\UserType",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     *
     * @param Request $request the request object
     *
     */
    public function postUserAction(Request $request)
    {
        $userManager = $this->container->get('fos_user.user_manager');
        $email = $request->get('email');
        $password = $request->get('password');
        $parts = explode("@", $email);
        $username = $parts[0];

        $email_exist = $userManager->findUserByUsernameOrEmail($email);
        $username_exist = $userManager->findUserByUsername($username);

        $user = new User();
        
        if($email_exist && $username_exist)
        {
            $response = [
                'register_state' => false,
                'email' => true
            ];
            $view = View::create();
            $view->setData($response)->setStatusCode(200);
            return $view;
        }
        else if($email_exist)
        {
            $response = [
                'register_state' => false,
                'email' => true,
            ];
            $view = View::create();
            $view->setData($response)->setStatusCode(200);
            return $view;

        }
        else if($username_exist)
        { 
            $username = $username."".rand(1,15);
            $register = true;
        }
        else
        {
           $register = true;
        }
       
        if($register)
        {
            $user = $userManager->createUser();
            
            $user->setUsername($username);
            $user->setEmail($email);

            $user->setEnabled(true);
            $user->setPlainPassword($password);

            $em = $this->getDoctrine()->getManager(); 
            $stat = $em->getRepository('MastaPlateFormeBundle:Stat\Stat')->findOneByName('statistique');
            $user->setStat($stat);
            
            $userManager->updateUser($user, true);

            //après inscription envoi automatique d'un e-mail de bienvenu

            $message = \Swift_Message::newInstance()
                    ->setSubject("Bienvenu")
                    ->setFrom("monsite@domain.com")
                    ->setTo($user->getEmail())
                    ->setBody($this->renderView(
                             'MastaUserBundle:Email:welcom.html.twig',array('user' => $user),
                             'text/html'
                             ));
            $this->get('swiftmailer.mailer')->send($message);

            $response = [
                    'register_state' => true,
                    'username'  => $username,
                    'password'  => $password
            ];
            $view = View::create();
            $view->setData($response)->setStatusCode(200);
            return $view;
          }

 

        $response = [
            'register_state' => false,
        ];

        $view = View::create();
        $view->setData($response)->setStatusCode(200);

        return $view;
    }


    /**
     * Update existing user from the submitted data or create a new user at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "Masta\UserBundle\Form\UserType",
     *   statusCodes = {
     *     201 = "Returned when a new resource is created",
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *   template="Masta\UserBundle:User:editUser.html.twig",
     *   templateVar="form"
     * )
     *
     * @param Request $request the request object
     *
     * @return array
     *
     * @throws AccessDeniedException
     */
    public function putUserAction(Request $request)
    {
      if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
          throw new AccessDeniedException();
      }
      $user = $this->get('security.token_storage')->getToken()->getUser();
      $userManager =  $this->container->get('fos_user.user_manager');
      $em = $this->getDoctrine()->getManager();

      $picture = $request->get('picture');

      $fullName = $request->get('fullName');
      $telephone = $request->get('telephone');
      $birthday = $request->get('birthday');
      $description = $request->get('description');
      $website = $request->get('web_site');
      $gender = $request->get('gender');
      $isMailNotification = $request->get('is_mail_notification');
      $country =  $em->getRepository('MastaUserBundle:Country')->find($request->get('country'));
      $city = $em->getRepository('MastaUserBundle:City')->find($request->get('city'));
      $response = [];

       if($picture != null)
       {
            $oldProfilePicture = $user->getProfilePicture();
            $newProfilePicture = $em->getRepository('MastaPlateFormeBundle:Picture\Picture')->find($picture); 
            $user->setProfilePicture($newProfilePicture); 
          //remove old picture (spagetti)
           if($oldProfilePicture != null )
           {
               $em->remove($oldProfilePicture);
               $em->flush();
           }
        }
        if($fullName != NULL) $user->setFullName($fullName);
        if($country != NULL)
        {
             
             $compteur = $country->getUsers()->count();
            if($user->getCountry() != $country)
            {
                $country->setNbUsers($compteur);   
            }
            else
            {
                $country->setNbUsers($compteur +1);
            }
            $user->setCountry($country);
        }
        if($city != NULL)
        {
            
            $compteur = $city->getUsers()->count();
            if($user->getCity() != $city)
            {
                $city->setNbUsers($compteur);   
            }
            else
            {
                $city->setNbUsers($compteur +1);
            }
            $user->setCity($city);
        } 
        if($telephone !=NULL) $user->setPhoneNumber($telephone);
        if($website != NULL) $user->setWebSite($website);
        if($birthday != NULL) $user->setBirthday($birthday);
        if($description != NULL) $user->setDescription($description);
        if($gender != NULL) $user->setGender($gender);
        if($isMailNotification != NULL) $user->setIsMailNotification($isMailNotification);

        $userManager->updateUser($user);

        $view = View::create();
        $view->setData($user)->setStatusCode(200);
        return $view;
    }


    /**
     * Update password for this user.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     201 = "Returned when a new resource is created",
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @param Request $request the request object
     *
     * @return array
     *
     * @throws AccessDeniedException
     */
    public function putPasswordAction(Request $request)
    {
    
      $password = $request->get('password');
      $confirmePassword = $request->get('confirmePassword');
      $tokenConfirmation = $request->get('tokenConfirmation');
      
      $userManager =  $this->container->get('fos_user.user_manager');
      if($tokenConfirmation != null)
      {
          $user = $userManager->findUserByConfirmationToken($tokenConfirmation);
          if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with "confirmation token" does not exist for value "%s"', $token));
          }
      }
      else
      {
        if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
          throw new AccessDeniedException();
        }
        $user = $this->get('security.token_storage')->getToken()->getUser();
      }

      if($password == $confirmePassword)
      {
        $user->setPlainPassword($confirmePassword);
        $userManager->updateUser($user);

        $response = ['status' => true];
      }
      else
      {
        $response = ['status' => false];
      }

      $view = View::create();
      $view->setData($response)->setStatusCode(200);
      return $view;
    }

    /**
     * Password Resetting Request.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     201 = "Returned when a new resource is created",
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @param Request $request the request object
     *
     * @return array
     *
     * @throws AccessDeniedException
     */
    public function resetPasswordRequestAction(Request $request)
    {
        $slug = $request->query->get('slug');
        $host = $request->query->get('host');
        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->findUserByUsernameOrEmail($slug);

        if (null === $user) {
            throw $this->createNotFoundException();
        }

        if ($user->isPasswordRequestNonExpired($this->container->getParameter('fos_user.resetting.token_ttl'))) {
            throw new BadRequestHttpException('Password request alerady requested');
        }

        if (null === $user->getConfirmationToken()) {
            /** @var $tokenGenerator \FOS\UserBundle\Util\TokenGeneratorInterface */
            $tokenGenerator = $this->get('fos_user.util.token_generator');
            $user->setConfirmationToken($tokenGenerator->generateToken());
        }
        $message = \Swift_Message::newInstance()
                    ->setSubject("Changer de mot de passe")
                    ->setFrom("cristallithos@gmail.com")
                    ->setTo($user->getEmail())
                    ->setBody($this->renderView(
                             'MastaUserBundle:Email:password_request.html.twig',array('user' => $user,'host'=>$host),
                             'text/html'
                             ));
        $this->get('swiftmailer.mailer')->send($message);

        
        //$this->get('fos_user.mailer')->sendResettingEmailMessage($user);
        $user->setPasswordRequestedAt(new \DateTime());
        $this->get('fos_user.user_manager')->updateUser($user);

        return new Response(Response::HTTP_OK);
    }

    /**
     * Removes a user.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     204="Returned when successful"
     *   }
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the user id
     *
     * @return View
     */
    public function deleteUserAction(Request $request, $id)
    {
        if (false === $this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw new AccessDeniedException();
        }
        $userManager =  $this->container->get('fos_user.user_manager');
        $user = $userManager->findUserBy(array('id'=>$id));

        if (!$user) {
            throw $this->createNotFoundException('User not found.');
        }

        $userManager->deleteUser($user);

        // There is a debate if this should be a 404 or a 204
        // see http://leedavis81.github.io/is-a-http-delete-requests-idempotent/
        return $this->routeRedirectView('get_user', array(), Response::HTTP_NO_CONTENT);
    }

    /**
     * Removes a user.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     204="Returned when successful"
     *   }
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the user id
     *
     * @return View
     */
    public function removeUserAction(Request $request, $id)
    {
        return $this->deleteUserAction($request, $id);
    }

    /**
     * List suggestion users.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="page", requirements="\d+", nullable=true, description="Page from which to start listing users.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many users to return.")
     *
     * @Annotations\View()
     *
     * @param Request               $request      the request object
     *
     * @return array
     */
    public function getUserSugestionsAction(Request $request)
    {
      if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
          throw $this->createAccessDeniedException();
      }
        $user = $this->getUser();

        $limit = $request->query->getInt('limit', 12);
        $page = $request->query->getInt('page', 1);

        $em = $this->getDoctrine()->getManager();
        $usersPager = $em->getRepository('MastaUserBundle:User')->getSuggestions($limit, $page, $user);


        //checking array()
        $this->container->get('masta_plateforme.checkor')->checkUsers($usersPager);

        $pagerFactory = new PagerfantaFactory();

        return $pagerFactory->createRepresentation(
            $usersPager,
            new Route('get_user_sugestions', array('limit' => $limit,'page' => $page))
        );

    }
}
