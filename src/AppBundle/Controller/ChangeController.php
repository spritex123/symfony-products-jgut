<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\User;
use AppBundle\Form\ChangeEmailType;
use AppBundle\Form\ChangePasswordType;
use AppBundle\Form\ChangeNameType;

class ChangeController extends Controller
{
    /**
     * @Route("/change/email", name="change_email")
     */
    public function changeEmailAction(Request $request)
    {
        $user = $this->getUser();

        $form = $this->createForm(ChangeEmailType::class, $user);

        $userEmail = $user->getEmail();

        $repository = $this->getDoctrine()->getRepository('AppBundle:User');
        $users = $repository->findAll();

        $count = count($users);

        for ($i = 0; $i < $count; $i++) {
            $users_db[] = $users[$i]->getEmail();
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            for ($i = 0; $i < $count; $i++) {
                if ($users_db[$i] == $form->getData()->getEmail()) {
                    return $this->redirect($this->generateUrl('homepage'));
                }
            }

            $user->setEmail(mb_strtolower($form->getData()->getEmail()));
            $user->setEnabled(false);
            $user->setToken(md5($user->getEmail() . '1'));

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $enabled = $this->getParameter('enabled_parameter') . $user->getToken();

            $message = \Swift_Message::newInstance()
                ->setSubject('Hello Email')
                ->setFrom($this->getParameter('email_parameter'))
                ->setTo($user->getEmail())
                ->setBody($this->renderView('AppBundle:Default:mail.html.twig', array('enabled' => $enabled)), 'text/html');

            $this->get('mailer')->send($message);

            return $this->redirect($this->generateUrl('logout'));
        }

        return $this->render('AppBundle:Change:email.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/change/password", name="change_password")
     */
    public function changePasswordAction(Request $request)
    {
        $user = $this->getUser();

        $form = $this->createForm(ChangePasswordType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($user->getPassword());

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirect($this->generateUrl('profile'));
        }

        return $this->render('AppBundle:Change:password.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/change/name", name="change_name")
     */
    public function changeNameAction(Request $request)
    {
        $user = $this->getUser();

        $form = $this->createForm(ChangeNameType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setName($user->getName());

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirect($this->generateUrl('profile'));
        }

        return $this->render('AppBundle:Change:name.html.twig', ['form' => $form->createView()]);
    }
}
