<?php

namespace App\Controller;

use App\Entity\AvParameter;
use App\Entity\Type;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AvParameterController extends AbstractController
{
    /**
     * @Route("/admin/av_parameter", name="app_admin_av_parameter")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(AvParameter::class);
        return $this->render('av_parameter/index.html.twig', [
            'av_parameters' => $repository->findAll(),
        ]);
    }

    /**
     * @Route("/admin/av_parameter/create", name="app_admin_av_parameter_create")
     */
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $avParameter = new AvParameter();
        $form = self::form($avParameter, $entityManager);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
          $avParameter->setCreatedAt();
          $avParameter->setUpdatedAt();
          $entityManager->persist($avParameter);
          $entityManager->flush();
          return $this->redirectToRoute('app_av_parameter');
        }
        return $this->renderForm('av_parameter/av_parameter_form.html.twig', [
            'avParameterForm' => $form,
        ]);
    }

    /**
     * @Route("/admin/av_parameter/edit/{id}", name="app_admin_av_parameter_edit", requirements={"id"="\d+"})
     */
    public function edit(Request $request, ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $avParameter = $entityManager->getRepository(AvParameter::class)->find($id);
        $form = $this->form($avParameter, $entityManager);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
          $avParameter->setUpdatedAt();
          $entityManager->persist($avParameter);
          $entityManager->flush();
          return $this->redirectToRoute('app_av_parameter');
        }
        return $this->renderForm('av_parameter/av_parameter_form.html.twig', [
            'avParameterForm' => $form,
        ]);
    }

    /**
     * @Route("/admin/av_parameter/{id}", name="app_admin_av_parameter_destroy", requirements={"id"="\d+"})
     */
    public function destroy(Request $request, ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $avParameter = $entityManager->getRepository(AvParameter::class)->find($id);
        $entityManager->remove($avParameter);
        $entityManager->flush();
        return $this->redirectToRoute('app_av_parameter');
    }

    private function form(AvParameter $avParameter, EntityManagerInterface $entityManager)
    {
        $types = $entityManager->getRepository(Type::class)->findAll();
        $form = $this->createFormBuilder($avParameter)
            ->add('name', TextType::class)
            ->add('type', ChoiceType::class, [
                'choices'  => $types,
                'choice_label' => function(?Type $type) {
                    return $type ? $type->getName() : '';
                },
            ])
            ->add('save', SubmitType::class, ['label' => 'Save'])
            ->add('id', HiddenType::class, ['data_class' => null, 'mapped' => false,]);
        return $form->getForm();
    }
}
