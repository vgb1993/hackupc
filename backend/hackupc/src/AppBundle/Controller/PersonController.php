<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Person;
use AppBundle\Form\PersonType;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Person controller.
 *
 * @Route("/users")
 */
class PersonController extends Controller
{
    /**
     * Lists all Person entities.
     *
     * @Route("/", name="users_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $people = $em->getRepository('AppBundle:Person')->findAll();

        return $this->render('person/index.html.twig', array(
            'people' => $people,
        ));
    }

    /**
     * Creates a new Person entity.
     *
     * @Route("/new", name="users_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $person = new Person();
        $form = $this->createForm('AppBundle\Form\PersonType', $person);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($person);
            $em->flush();

            return $this->redirectToRoute('users_show', array('id' => $person->getId()));
        }

        return $this->render('person/new.html.twig', array(
            'person' => $person,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Person entity.
     *
     * @Route("/{id}", name="users_show")
     * @Method("GET")
     */
    public function showAction(Person $person)
    {

        $response = new Response();
        $response->headers->set('Access-Control-Allow-Origin', '*');

        // $project = $this->getDoctrine()->getRepository('AppBundle:Project')->findOneById(1);
        // $project->setPerson($person);
        // $em = $this->getDoctrine()->getManager();
        // $em->persist($project);
        // $em->flush();

        // $project = $this->getDoctrine()->getRepository('AppBundle:Project')->findOneById(1);
        // $skill = $this->getDoctrine()->getRepository('AppBundle:Skill')->findOneById(1);
        // $project->addSkill($skill);
        // $em = $this->getDoctrine()->getManager();
        // $em->persist($project);
        // $em->flush();


        $myProjects = array();
        foreach ($person->getProjects() as $project) 
        {

            $projectSkills = array();
            foreach ($project->getSkills() as $skill) 
            {
                $projectSkills[] = array(
                    'name' => $skill->getName(),
                    'value' => $skill->getValue(),
                );
            }

            $myProjects[] = array(
                'id' => $project->getId(),
                'name' => $project->getName(),
                'imageUrl' => $project->getImageUrl(),
                'description' => $project->getDescription(),
                'date' => $project->getDate(),
                'skills' => $projectSkills
            );
        }

        $mySkills = array();
        foreach ($person->getSkills() as $skill) 
        {
            $mySkills[] = array(
                'name' => $skill->getName(),
                'value' => $skill->getValue(),
            );
        }

        $response->setContent(json_encode(array(
            'id' => $person->getId(),
            'name' => $person->getName(),
            'profileImageUrl' => $person->getProfileImageUrl(),
            'description' => $person->getDescription(),
            'city' => $person->getCity(),
            'country' => $person->getCountry(),
            'projects' => $myProjects,
            'skills' => $mySkills,
        )));


        return $response;  

        // $response->setData();
        // $container->get('serializer')->serialize($users, 'json'));
        

        // $deleteForm = $this->createDeleteForm($person);

        // return $this->render('person/show.html.twig', array(
        //     'person' => $person,
        //     'delete_form' => $deleteForm->createView(),
        // ));
    }







    /**
     * Finds and displays a Person entity.
     *
     * @Route("/{id}/projects", name="users_new_project_show")
     * @Method("GET")
     */
    public function newProjectAction(Request $request, Person $person)
    {
        $project = new Project();
        $project->setName( $request->get('name') );
        $project->setDate( $request->get('date') );
        $project->setImageURL( $request->get('imageUrl') );
        $em = $this->getDoctrine()->getManager();
        $em->persist($project);
        $em->flush();
    }




        /**
     * Finds and displays a Person entity.
     *
     * @Route("/{userId}/projects/{projectId}", name="users_project_show")
     * @Method("GET")
     */
    public function showProjectAction($userId,$projectId)
    {
        
        $project = $this->getDoctrine()->getRepository('AppBundle:Project')->findOneById($projectId);

        $response = new Response();
        $response->setContent(json_encode(array(
            'id' => $project->getId(),
            'name' => $project->getName(),
            'imageUrl' => $project->getImageUrl(),
            'description' => $project->getDescription(),
            'date' => $project->getDate()
        )));

        return $response;  

    }




    /**
     * Displays a form to edit an existing Person entity.
     *
     * @Route("/{id}/edit", name="users_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Person $person)
    {
        $deleteForm = $this->createDeleteForm($person);
        $editForm = $this->createForm('AppBundle\Form\PersonType', $person);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($person);
            $em->flush();

            return $this->redirectToRoute('users_edit', array('id' => $person->getId()));
        }

        return $this->render('person/edit.html.twig', array(
            'person' => $person,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Person entity.
     *
     * @Route("/{id}", name="users_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Person $person)
    {
        $form = $this->createDeleteForm($person);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($person);
            $em->flush();
        }

        return $this->redirectToRoute('users_index');
    }

    /**
     * Creates a form to delete a Person entity.
     *
     * @param Person $person The Person entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Person $person)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('users_delete', array('id' => $person->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
