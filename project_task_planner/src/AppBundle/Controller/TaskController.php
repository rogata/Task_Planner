<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Task;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;


/**
 * Class TaskController
 * @Route("/task")
 */
class TaskController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $tasks = $em->getRepository("AppBundle:Task")->findAll();

        return $this->render('task/index.html.twig', array(
            'tasks' => $tasks,
        ));

    }
    /**
     * @Route("/new", name="task_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $task = new Task();
        $form = $this->createForm('AppBundle\Form\TaskType', $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($task);
            $em->flush();

            return $this->redirectToRoute('task_show', array('id' => $task->getId()));
        }

        return $this->render('task/new.html.twig', array(
            'task' => $task,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/show/{id}", name="task_show")
     */
    public function showAction(Task $task)
    {
        $deleteForm = $this->createDeleteForm($task);

        return $this->render('task/show.html.twig', array(
            'task' => $task,
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * @Route("/edit/{id}", name="task_edit")
     * @Method({"GET","POST"})
     */
    public function editAction(Request $request, Task $task)
    {
        $editForm = $this->createForm('AppBundle\Form\TaskType', $task);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('app_task_index', array('id' => $task->getId()));
        }

        return $this->render('task/edit.html.twig', array(
            'task'=>$task,
            'edit_form'=>$editForm->createView(),
        ));

    }
    /**
     * @Route("/{id}", name="task_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Task $task)
    {
        $form = $this->createDeleteForm($task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($task);
            $em->flush();
        }

        return $this->redirectToRoute('app_task_index');

    }
    private function createDeleteForm(Task $task)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('task_delete', array('id' => $task->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }

    /**
     * @Route("/completed")
     */
    public function completedAction()
    {
        $em  = $this->getDoctrine()->getManager();
        $completed = $em ->getRepository('AppBundle:Task')->findBy(['checked'=> true]);


            return $this->render(':task:completed.html.twig', [
                'completed'=> $completed,
            ]);

    }

}
