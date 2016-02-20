<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Skill;
use AppBundle\Form\SkillType;

/**
 * Skill controller.
 *
 * @Route("/skills")
 */
class SkillController extends Controller
{
    /**
     * Lists all Skill entities.
     *
     * @Route("/", name="skills_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $skills = $em->getRepository('AppBundle:Skill')->findAll();

        return $this->render('skill/index.html.twig', array(
            'skills' => $skills,
        ));
    }

    /**
     * Creates a new Skill entity.
     *
     * @Route("/new", name="skills_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $skill = new Skill();
        $form = $this->createForm('AppBundle\Form\SkillType', $skill);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($skill);
            $em->flush();

            return $this->redirectToRoute('skills_show', array('id' => $skill->getId()));
        }

        return $this->render('skill/new.html.twig', array(
            'skill' => $skill,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Skill entity.
     *
     * @Route("/{id}", name="skills_show")
     * @Method("GET")
     */
    public function showAction(Skill $skill)
    {
        $deleteForm = $this->createDeleteForm($skill);

        return $this->render('skill/show.html.twig', array(
            'skill' => $skill,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Skill entity.
     *
     * @Route("/{id}/edit", name="skills_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Skill $skill)
    {
        $deleteForm = $this->createDeleteForm($skill);
        $editForm = $this->createForm('AppBundle\Form\SkillType', $skill);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($skill);
            $em->flush();

            return $this->redirectToRoute('skills_edit', array('id' => $skill->getId()));
        }

        return $this->render('skill/edit.html.twig', array(
            'skill' => $skill,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Skill entity.
     *
     * @Route("/{id}", name="skills_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Skill $skill)
    {
        $form = $this->createDeleteForm($skill);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($skill);
            $em->flush();
        }

        return $this->redirectToRoute('skills_index');
    }

    /**
     * Creates a form to delete a Skill entity.
     *
     * @param Skill $skill The Skill entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Skill $skill)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('skills_delete', array('id' => $skill->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
