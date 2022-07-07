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
     * @Route("/admin/type", name="app_admin_type")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        return $this->render('type/index.html.twig', [
            'types' => $doctrine->getRepository(Type::class)->findAll(),
        ]);
    }

    /**
     * @Route("/admin/type/create", name="app_admin_type_create")
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
          return $this->redirectToRoute('app_admin_type');
        }
        return $this->renderForm('type/type_form.html.twig', [
            'typeForm' => $form,
        ]);
    }

    /**
     * @Route("/admin/type/edit/{id}", name="app_admin_type_edit", requirements={"id"="\d+"})
     */
    public function edit(Request $request, ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $type = $entityManager->getRepository(Type::class)->find($id);
        $form = $this->form($type);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
          $type->setUpdatedAt();
          $entityManager->persist($type);
          $entityManager->flush();
          return $this->redirectToRoute('app_admin_type');
        }
        return $this->renderForm('type/type_form.html.twig', [
            'typeForm' => $form,
        ]);
    }

    /**
     * @Route("/admin/type/{id}", name="app_admin_type_destroy", requirements={"id"="\d+"})
     */
    public function destroy(Request $request, ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $type = $entityManager->getRepository(Type::class)->find($id);
        $entityManager->remove($type);
        $entityManager->flush();
        return $this->redirectToRoute('app_admin_type');
    }

    private function form(Type $type)
    {
        $form = $this->createFormBuilder($type)
            ->add('name', TextType::class, [
                'attr' => ['class' => 'form-control'],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Save',
                'attr' => ['class' => 'btn btn-primary mt-3'],
                ])
            ->add('id', HiddenType::class, ['data_class' => null, 'mapped' => false,]);
        return $form->getForm();
    }
}
