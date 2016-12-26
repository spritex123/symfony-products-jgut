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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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

            foreach ($users as $user) {
                if ($user->getEmail() == $form->getData()->getEmail()) {
                    $user->setForgotPassword(true);

                    $forgotPassword = $this->getParameter('new_password_parameter') . $user->getToken();

                    $em = $this->getDoctrine()->getManager();
                    $em->flush();

                    $message = \Swift_Message::newInstance()
                        ->setSubject('Hello Email')
                        ->setFrom($this->getParameter('email_parameter'))
                        ->setTo($form->getData()->getEmail())
                        ->setBody($this->renderView(
                            'AppBundle:Authorization:mail.html.twig',
                            ['forgotPassword' => $forgotPassword]
                        ), 'text/html');

                    $this->get('mailer')->send($message);

                    $this->addFlash('notice', 'A letter sent!');

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
     * @param User $user
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function newPasswordAction(User $user, Request $request)
    {

        $form = $this->createForm(ForgotSetPasswordType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($user->getForgotPassword()) {
                $user->setForgotPassword(false);
                $user->setPassword(md5($form->getData()->getPassword()));

                $this->getDoctrine()->getManager()->flush();

                $this->addFlash('notice', 'Password changed!');

                return $this->redirect($this->generateUrl('logout'));
            }
        }

        return $this->render('AppBundle:Authorization:forgotsetpassword.html.twig', ['form' => $form->createView()]);
    }
}
