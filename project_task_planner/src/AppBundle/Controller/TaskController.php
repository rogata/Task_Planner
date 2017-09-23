<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Note;
use AppBundle\Entity\Task;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


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
        $tasks = $em->getRepository("AppBundle:Task")->findBy([],['date'=>'ASC']);

        return $this->render('task/index.html.twig', [
            'tasks' => $tasks,
        ]);

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

            return $this->redirectToRoute('task_show', ['id' => $task->getId()]);
        }

        return $this->render('task/new.html.twig', [
            'task' => $task,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/show/{id}", name="task_show")
     * @Method({"GET","POST"})
     */
    public function showAction(Task $task)
    {
        $note = new Note();
        $form = $this->createFormBuilder($note)
            ->setAction($this->generateUrl('app_task_addnote',['id'=>$task->getId()]))
            ->add('text', 'text')
            ->getForm();

        $em = $this->getDoctrine()->getManager();
        $notes = $em->getRepository('AppBundle:Note')->findBy(['task'=>$task->getId()]);


        $deleteForm = $this->createDeleteForm($task);

        return $this->render('task/show.html.twig', [
            'notes'=>$notes,
            'task' => $task,
            'note_form' =>$form->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }
    /**
     * @Route("/edit/{id}", name="task_edit")
     * @Method({"GET","POST"})
     */
    public function editAction(Request $request, Task $task)
    {
        $editForm = $this->createForm('AppBundle\Form\TaskType', $task);
        $editForm->handleRequest($request);

        $checkedForm = $this->createFormBuilder($task)
            ->add('checked')
            ->getForm();
        $checkedForm->handleRequest($request);

        if ($checkedForm ->isSubmitted()) {
            $task = $checkedForm->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($task);
            $em->flush();

            return $this->redirectToRoute('app_task_index');
        }

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('task_show', ['id' => $task->getId()]);
        }

        return $this->render('task/edit.html.twig', [
            'task'=>$task,
            'edit_form'=>$editForm->createView(),
            'checked_form' =>$checkedForm->createView(),
        ]);

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
            ->setAction($this->generateUrl('task_delete', ['id' => $task->getId()]))
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

    /**
     * @Route("/add_note/{id}")
     */
    public function addNoteAction(Request $request, $id)
    {
        $note = new Note();
        $task = $this->getDoctrine()->getRepository('AppBundle:Task')->find($id);
        $form = $this->createFormBuilder($note)
            ->add('text', 'text')
            ->getForm();
        $form->handleRequest($request);

        if(!$task){
            throw $this->createNotFoundException('Task not found!');
        }


        if ($form->isSubmitted() && $form->isValid()) {
            $note->setTask($task);
            $em = $this->getDoctrine()->getManager();
            $em->persist($note);
            $em->flush();

        }

        return $this->redirectToRoute('task_show',['id'=>$id]);
    }

}
