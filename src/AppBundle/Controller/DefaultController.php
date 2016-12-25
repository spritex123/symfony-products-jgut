<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        return $this->render('AppBundle:Default:index.html.twig');
    }

    /**
     * @Route("/secret", name="secret")
     */
    public function secretAction(Request $request)
    {
        return $this->render('AppBundle:Default:secret.html.twig');
    }

    /**
     * @Route("/error403", name="error403")
     */
    public function error403Action(Request $request)
    {
        return $this->render('AppBundle:Default:error403.html.twig');
    }
}
