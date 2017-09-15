<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CategoryController
 * @Route("/category")
 */
class CategoryController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository('AppBundle:Category')->findAll();

        return $this->render('category/index.html.twig', array(
            'category'=>$category,
        ));

    }
    /**
     * @Route("/new")
     * @Method({"GET","POST"})
     */
    public function newAction(Request $request)
    {
        $category = new Category();
        $form = $this->createForm('AppBundle\Form\CategoryType', $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

        }

        return $this->render('category/new.html.twig', array(
            'form'=>$form->createView()
        ));

    }
    /**
     * @Route("/edit/{id}", name="category_edit")
     * @Method({"GET","POST"})
     */
    public function editAction(Request $request, Category $category)
    {
        $editForm = $this->createForm('AppBundle\Form\CategoryType', $category);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('category_edit', array('id' => $category->getId()));
        }

        return $this->render('category/edit.html.twig', array(
            'category'=>$category,
            'edit_form'=>$editForm->createView(),
        ));

    }
}
