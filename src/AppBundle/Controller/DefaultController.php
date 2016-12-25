<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        return $this->render('AppBundle:Default:index.html.twig');
    }

    /**
     * @Route("/secret", name="secret")
     */
    public function secretAction()
    {
        return $this->render('AppBundle:Default:secret.html.twig');
    }

    /**
     * @Route("/error403", name="error403")
     */
    public function error403Action()
    {
        return $this->render('AppBundle:Default:error403.html.twig');
    }
}
