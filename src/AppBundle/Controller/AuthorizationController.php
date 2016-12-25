<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;
use AppBundle\Form\ForgotPasswordType;
use AppBundle\Form\ForgotSetPasswordType;
use Symfony\Component\HttpFoundation\Response;

class AuthorizationController extends Controller
{
    /**
     * @Route("/authorization", name="authorization")
     *
     * @return Response
     */
    public function indexAction()
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('AppBundle:Authorization:index.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
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
     *
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function forgotPasswordAction(Request $request)
    {
        $user = new User();

        $form = $this->createForm(ForgotPasswordType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repository = $this->getDoctrine()->getRepository('AppBundle:User');
            $users = $repository->findAll();

            /** @var User $user */
            foreach ($users as $user) {
                if ($user->getEmail() == $form->getData()->getEmail()) {
                    $user->setForgotPassword(true);

                    $em = $this->getDoctrine()->getManager();
                    $em->flush();

                    $forgotPassword = $this->getParameter('new_password_parameter') . $user->getToken();

                    $message = \Swift_Message::newInstance()
                        ->setSubject('Hello Email')
                        ->setFrom($this->getParameter('email_parameter'))
                        ->setTo($form->getData()->getEmail())
                        ->setBody($this->renderView(
                            'AppBundle:Authorization:mail.html.twig',
                            ['forgotPassword' => $forgotPassword]
                        ), 'text/html');

                    $this->get('mailer')->send($message);

                    return $this->redirect($this->generateUrl('homepage'));
                }
            }
        }

        return $this->render('AppBundle:Authorization:forgotpassword.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/newpassword/{token}", name="new_password", defaults={"token" = null})
     *
     * @param $token
     * @param Request $request
     * @return RedirectResponse|Response
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

            /** @var User $user */
            foreach ($users as $user) {
                if ($user->getForgotPassword() && $user->getToken() == $token) {
                    $user->setForgotPassword(false);
                    $user->setPassword(md5($form->getData()->getPassword()));

                    $em = $this->getDoctrine()->getManager();
                    $em->flush();

                    return $this->redirect($this->generateUrl('logout'));
                }
            }

            return $this->redirect($this->generateUrl('new_password', ['token' => $token]));
        }

        return $this->render('AppBundle:Authorization:forgotsetpassword.html.twig', ['form' => $form->createView()]);
    }
}
