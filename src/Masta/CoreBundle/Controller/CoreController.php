<?php
namespace Masta\CoreBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CoreController extends Controller
{
    public function indexAction()
    {
       return $this->render('MastaCoreBundle:Core:index.html.twig');
    }
}
