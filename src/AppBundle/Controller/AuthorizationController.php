<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


// delete
use Symfony\Component\HttpFoundation\Response;




use AppBundle\Entity\User;
use AppBundle\Form\ForgotPasswordType;
use AppBundle\Form\ForgotSetPasswordType;

class AuthorizationController extends Controller
{
    /**
     * @Route("/authorization", name="authorization")
     */
    public function indexAction(Request $request)
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('AppBundle:Authorization:index.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction()
    {
        throw new \Exception('@Route("/logout", name="logout")');
    }

    /**
     * @Route("/forgotpassword", name="forgot_password")
     */
    public function forgotPasswordAction(Request $request)
    {
        $user = new User();

        $form = $this->createForm(ForgotPasswordType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repository = $this->getDoctrine()->getRepository('AppBundle:User');
            $users = $repository->findAll();

            $count = count($users);

            for ($i = 0; $i < $count; $i++) {
                if ($users[$i]->getEmail() == $form->getData()->getEmail()) {

                    $users[$i]->setForgotPassword(true);

                    $em = $this->getDoctrine()->getManager();
                    $em->flush();

                    $forgotPassword = $this->getParameter('new_password_parameter') . $users[$i]->getToken();

                    $message = \Swift_Message::newInstance()
                        ->setSubject('Hello Email')
                        ->setFrom($this->getParameter('email_parameter'))
                        ->setTo($form->getData()->getEmail())
                        ->setBody($this->renderView('AppBundle:Authorization:mail.html.twig', array('forgotPassword' => $forgotPassword)), 'text/html');

                    $this->get('mailer')->send($message);

                    return $this->redirect($this->generateUrl('homepage'));
                }
            }
        }

        return $this->render('AppBundle:Authorization:forgotpassword.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/newpassword/{token}", name="new_password", defaults={"token" = null})
     */
    public function newPasswordAction($token, Request $request)
    {
        if (!$token) {
            throw $this->createNotFoundException('No token' . $token);
        }

        $user = new User();

        $form = $this->createForm(ForgotSetPasswordType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repository = $this->getDoctrine()->getRepository('AppBundle:User');
            $users = $repository->findAll();

            $count = count($users);

            for ($i = 0; $i < $count; $i++) {
                if ($users[$i]->getForgotPassword() && $users[$i]->getToken() == $token) {

                        $users[$i]->setForgotPassword(false);
                        $users[$i]->setPassword(md5($form->getData()->getPassword()));

                        $em = $this->getDoctrine()->getManager();
                        $em->flush();

                        return $this->redirect($this->generateUrl('logout'));
                    }
            }

            return $this->redirect($this->generateUrl('new_password', array('token' => $token)));
        }

        return $this->render('AppBundle:Authorization:forgotsetpassword.html.twig', ['form' => $form->createView()]);
    }
}
