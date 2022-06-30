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
use Symfony\Component\HttpFoundation\JsonResponse;
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
        return $this->render('av_parameter/index.html.twig', [
            'types' => $doctrine->getRepository(Type::class)->findAll(),
        ]);
    }

    /**
     * @Route("/api/av_parameter/{id?}", name="app_api_av_parameter")
     */
    public function list(ManagerRegistry $doctrine, $id = 0): JsonResponse
    {
        $id = intval($id);
        if ($id) {
          $devices = $doctrine->getRepository(Type::class)->find($id);
          $avParameters = $devices ? $devices->getAvParameters() : [];
        } else {
          $avParameters = $doctrine->getRepository(AvParameter::class)->findAll();
        }
        return $this->json(['rows' => AvParameter::toArray($avParameters)]);
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
          return $this->redirectToRoute('app_admin_av_parameter');
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
          return $this->redirectToRoute('app_admin_av_parameter');
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
        return $this->redirectToRoute('app_admin_av_parameter');
    }

    private function form(AvParameter $avParameter, EntityManagerInterface $entityManager)
    {
        $types = $entityManager->getRepository(Type::class)->findAll();
        $form = $this->createFormBuilder($avParameter)
            ->add('name', TextType::class, [
                'attr' => ['class' => 'form-control'],
            ])
            ->add('type', ChoiceType::class, [
                'attr' => ['class' => 'form-select'],
                'choices'  => $types,
                'choice_label' => function(?Type $type) {
                    return $type ? $type->getName() : '';
                },
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Save',
                'attr' => ['class' => 'btn btn-primary mt-3'],
                ])
            ->add('id', HiddenType::class, ['data_class' => null, 'mapped' => false,]);
        return $form->getForm();
    }
}
