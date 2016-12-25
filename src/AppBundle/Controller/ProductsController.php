<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\User;
use AppBundle\Entity\Product;
use AppBundle\Form\ProductType;

class ProductsController extends Controller
{
    /**
     * @Route("/products", name="products")
     */
    public function indexAction(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Product');

        $user = $this->getUser();

        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            $products = $repository->findAll();
        } else {
            $products = $repository->findByUser($user->getId());
        }

        return $this->render('AppBundle:Products:index.html.twig', array('products' => $products));
    }

    /**
     * @Route("/product/add", name="product_add")
     */
    public function addAction(Request $request)
    {
        $user = $this->getUser();

        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            ////////////////////////////////////////////////////////////////////
            // Upload file
            ////////////////////////////////////////////////////////////////////
            // $file stores the uploaded PDF file
            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
            if ($product->getThumbnail()) {
                $file = $product->getThumbnail();

                // Generate a unique name for the file before saving it
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();

                // Move the file to the directory where brochures are stored
                $file->move(
                    $this->getParameter('thumbnail_directory'),
                    $fileName
                );

                // Update the 'brochure' property to store the PDF file name
                // instead of its contents
                $product->setThumbnail($fileName);
            }
            ////////////////////////////////////////////////////////////////////
            // End upload file
            ////////////////////////////////////////////////////////////////////

            $product->setUser($user);

            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            return $this->redirect($this->generateUrl('products'));
        }

        return $this->render('AppBundle:Products:add.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/product/edit/{id}", name = "product_edit", requirements = {"id" = "\d+"}, defaults = {"id" = null})
     */
    public function editAction($id, Request $request)
    {
        if (!$id) {
            throw $this->createNotFoundException('No id ' . $id);
        }

        $repository = $this->getDoctrine()->getRepository('AppBundle:Product');
        $product = $repository->find($id);

        $user = $this->getUser();

        if ($user->getId() != $product->getUser()->getId()) {
            return $this->redirect($this->generateUrl('products'));
        }

        $thumbnail = $product->getThumbnail();

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product->setUser($user);

            if ($product->getThumbnail()) {
                $file = $product->getThumbnail();

                // Generate a unique name for the file before saving it
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();

                // Move the file to the directory where brochures are stored
                $file->move(
                    $this->getParameter('thumbnail_directory'),
                    $fileName
                );

                $fileUnlink = $this->getParameter('thumbnail_directory') . $thumbnail;
                if (file_exists($fileUnlink)) {
                    unlink($this->getParameter('thumbnail_directory') . $thumbnail);
                }

                // Update the 'brochure' property to store the PDF file name
                // instead of its contents
                $product->setThumbnail($fileName);
            } else {
                $product->setThumbnail($thumbnail);
            }

            $em = $this->getDoctrine()->getManager();
            $em->flush();


            return $this->redirect($this->generateUrl('products'));
        }

        return $this->render('AppBundle:Products:edit.html.twig', ['form' => $form->createView(), 'thumbnail' => $thumbnail]);
    }

    /**
     * @Route("/product/delete/{id}", name = "product_delete", requirements = {"id" = "\d+"}, defaults = {"id" = null})

     */
    public function deleteAction($id)
    {
        if (!$id) {
            throw $this->createNotFoundException('No id ' . $id);
        }

        $repository = $this->getDoctrine()->getRepository('AppBundle:Product');
        $product = $repository->find($id);

        if (!$product) {
            throw $this->createNotFoundException('No product found for id ' . $id);
        }

        $user = $this->getUser();

        if (!in_array('ROLE_ADMIN', $user->getRoles())) {
            if ($user->getId() != $product->getUser()->getId()) {
                return $this->redirect($this->generateUrl('products'));
            }
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($product);
        $em->flush();

        return $this->redirect($this->generateUrl('products'));
    }
}
