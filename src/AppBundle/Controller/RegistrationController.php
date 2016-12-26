<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;
use AppBundle\Form\RegistrationType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class RegistrationController extends Controller
{
    /**
     * @Route("/registration", name="registration")
     *
     * @param Request $request
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setEmail(mb_strtolower($user->getEmail()));
            $user->setPassword(md5($user->getPassword()));
            $user->setToken(md5($user->getEmail() . '1'));

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $enabled = $this->getParameter('enabled_parameter') . $user->getToken();

            $message = \Swift_Message::newInstance()
                ->setSubject('Hello Email')
                ->setFrom($this->getParameter('email_parameter'))
                ->setTo($user->getEmail())
                ->setBody($this->renderView('AppBundle:Default:mail.html.twig', ['enabled' => $enabled]), 'text/html');

            $this->get('mailer')->send($message);

            $this->addFlash('notice', 'User added!');

            return $this->redirect($this->generateUrl('homepage'));
        }

        return $this->render('AppBundle:Registration:index.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/enabled/{token}", name="enabled", defaults={"token" = null})
     * @ParamConverter("token", options = {"token" = "token"})
     *
     * @param $token
     * @return RedirectResponse
     */
    public function enabledAction($token)
    {
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->getByToken($token);
        if (!$user) {
            throw $this->createNotFoundException('No user found ' . $token);
        }

        $user->setEnabled(true);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirect($this->generateUrl('logout'));
    }
}
