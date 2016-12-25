<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ProfileController extends Controller
{
    /**
     * @Route("/profile", name="profile")
     */
    public function indexAction(Request $request)
    {
        $user = $this->getUser();
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            $repository = $this->getDoctrine()->getRepository('AppBundle:User');
            $users = $repository->findAll();

            return $this->render('AppBundle:Profile:index.html.twig', array('users' => $users));
        }
        return $this->render('AppBundle:Profile:index.html.twig');
    }
}
