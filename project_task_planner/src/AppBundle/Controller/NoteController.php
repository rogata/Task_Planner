<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Note;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/note")
 */
class NoteController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $notes = $em->getRepository('AppBundle:Note')->findAll();

        return $this->render('task/show.html.twig',[
            'notes' => $notes,
    ]);
    }
    /**
     * @Route("/new")
     */
    public function newAction(Request $request)
    {
        $note = new Note();
        $form = $this->createFormBuilder($note)
            ->add('text', 'textarea')
            ->getForm();

        $form->handleRequest($request);

        return $this->render('task/show.html.twig', [
            'note_form' => $form->createView(),
        ]);
    }
}
