<?php

namespace App\Controller;

use App\Entity\Type;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TypeController extends AbstractController
{
    /**
     * @Route("/type", name="app_type")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Type::class);
        return $this->render('type/index.html.twig', [
            'types' => $repository->findAll(),
        ]);
    }

    /**
     * @Route("/type/create", name="app_type_create")
     */
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $type = new Type();
        $form = self::form($type);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
          $type->setCreatedAt();
          $type->setUpdatedAt();
          $entityManager->persist($type);
          $entityManager->flush();
          return $this->redirectToRoute('app_type');
        }
        return $this->renderForm('type/type_form.html.twig', [
            'typeForm' => $form,
        ]);
    }

    /**
     * @Route("/type/edit/{id}", name="app_type_edit", requirements={"id"="\d+"})
     */
    public function edit(Request $request, ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $type = $entityManager->getRepository(Type::class)->find($id);
        $form = $this->form(type);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
          $type->setUpdatedAt();
          $entityManager->persist($type);
          $entityManager->flush();
          return $this->redirectToRoute('app_type');
        }
        return $this->renderForm('type/type_form.html.twig', [
            'typeForm' => $form,
        ]);
    }

    /**
     * @Route("/type/{id}", name="app_type_destroy", requirements={"id"="\d+"})
     */
    public function destroy(Request $request, ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $type = $entityManager->getRepository(Type::class)->find($id);
        $entityManager->remove($type);
        $entityManager->flush();
        return $this->redirectToRoute('app_type');
    }

    private function form(Type $type)
    {
        $form = $this->createFormBuilder($type)
            ->add('name', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Save'])
            ->add('id', HiddenType::class, ['data_class' => null, 'mapped' => false,]);
        return $form->getForm();
    }
}