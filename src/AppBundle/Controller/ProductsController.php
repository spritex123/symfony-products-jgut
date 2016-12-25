<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Product;
use AppBundle\Form\ProductType;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ProductsController extends Controller
{
    /**
     * @Route("/products", name="products")
     *
     * @return Response
     */
    public function indexAction()
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Product');

        $user = $this->getUser();

        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            $products = $repository->findAll();
        } else {
            $products = $repository->findByUser($user->getId());
        }

        return $this->render('AppBundle:Products:index.html.twig', ['products' => $products]);
    }

    /**
     * @Route("/product/add", name="product_add")
     *
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function addAction(Request $request)
    {
        $user = $this->getUser();

        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            if ($product->getThumbnail()) {
                $file = $product->getThumbnail();
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                $file->move(
                    $this->getParameter('thumbnail_directory'),
                    $fileName
                );

                $product->setThumbnail($fileName);
            }

            $product->setUser($user);

            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            $this->addFlash('notice', 'Product added!');

            return $this->redirect($this->generateUrl('products'));
        }

        return $this->render('AppBundle:Products:add.html.twig', ['form' => $form->createView()]);
    }

    /*
    * @ParamConverter("id", class = "AppBundle:Product", options = {"id" = "id"})
    *
    * @param $id
    *
    public function editAction(Product $product, Request $request) {
        $product = $id
        ...
    }*/

    /**
     * @Route("/product/edit/{id}", name = "product_edit", requirements = {"id" = "\d+"})
     *
     * @param Product $product
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function editAction(Product $product, Request $request)
    {
        $user = $this->getUser();

        if ($user->getId() != $product->getUser()->getId()) {
            return $this->redirect($this->generateUrl('products'));
        }

        $thumbnail = $product->getThumbnail();

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product->setUser($user);

            /** @var UploadedFile $file */
            if ($product->getThumbnail()) {
                $file = $product->getThumbnail();
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                $file->move(
                    $this->getParameter('thumbnail_directory'),
                    $fileName
                );
                $fileUnlink = $this->getParameter('thumbnail_directory') . $thumbnail;
                if (file_exists($fileUnlink)) {
                    unlink($this->getParameter('thumbnail_directory') . $thumbnail);
                }

                $product->setThumbnail($fileName);
            } else {
                $product->setThumbnail($thumbnail);
            }

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash('notice', 'Product edited!');

            return $this->redirect($this->generateUrl('products'));
        }

        return $this->render('AppBundle:Products:edit.html.twig', ['form' => $form->createView(), 'thumbnail' => $thumbnail]);
    }

    /**
     * @Route("/product/delete/{id}", name = "product_delete", requirements = {"id" = "\d+"}, defaults = {"id" = null})
     *
     * @param $id
     * @return RedirectResponse
     */
    public function deleteAction($id)
    {
        if (!$id) {
            throw $this->createNotFoundException('No id ' . $id);
        }

        $product = $this->getDoctrine()->getRepository('AppBundle:Product')->find($id);
        if (!$product) {
            throw $this->createNotFoundException('No product found for id ' . $id);
        }

        $user = $this->getUser();

        if (!in_array('ROLE_ADMIN', $user->getRoles())) {
            if ($user->getId() != $product->getUser()->getId()) {
                $this->addFlash('notice', 'Not your product!');
                return $this->redirect($this->generateUrl('products'));
            }
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($product);
        $em->flush();

        $this->addFlash('notice', 'Product deleted!');

        return $this->redirect($this->generateUrl('products'));
    }
}
