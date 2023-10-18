<?php

namespace App\Controller;

use App\Entity\Student;
use App\Form\StudentType;
use App\Repository\StudentRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StudentController extends AbstractController
{
    #[Route('/student', name: 'app_student')]
    public function index(): Response
    {
        return $this->render('student/index.html.twig', [
            'controller_name' => 'StudentController',
        ]);
    }

    #[Route('/list', name: 'list')]
    public function list(StudentRepository $repo): Response
    {
        $list=$repo->findAll();
        return $this->render('student/list.html.twig',[
            'list'=> $list
        ]);
    }

    public function addStatic(ManagerRegistry $manager): Response
    {
        $st=new Student();
        $st->setName('anwer');
        $st->setEmail('anwer@esprit.tn');
        $st->setAge(23);
        $em=$manager->getManager();
        $em->persist($st);
        $em->flush();
        return new Response('student added');
    }

    #[Route('/add', name: 'add')]
    public function add(Request $req,ManagerRegistry $manager):Response
    {
        $em=$manager->getManager();
        $student=new Student();
        $form=$this->createForm(StudentType::class, $student);
        $form->handleRequest($req);
        if ($form->isSubmitted()){
           $em->persist($student);
           $em->flush();
           return $this->redirectToRoute('list');
        }
        return $this->renderForm('student/add.html.twig',[
            'f' =>$form
        ]);
    }

    #[Route('/edit/{id}',name:'edit')]
    public function edit(ManagerRegistry $manager, $id,Request $req, StudentRepository $repo): Response
    {
        $em=$manager->getManager();
        $student=$repo->find($id);
        $form=$this->createForm(StudentType::class,$student);
        $form->handleRequest($req);
        if($form->isSubmitted()){
            $em->persist($student);
            $em->flush();
            return $this->redirectToRoute('list');
        }
        return $this->renderForm('student/edit.html.twig',[
            'f'=>$form
        ]);
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(ManagerRegistry $manager, $id,StudentRepository $repo): Response
    {
        $em=$manager->getManager();
        $student=$repo->find($id);
        $em->remove($student);
        $em->flush();
        return $this->redirectToRoute('list');
    }
}
