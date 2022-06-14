<?php

namespace App\Controller;

use App\Entity\AvParameter;
use App\Entity\Device;
use App\Entity\Parameter;
use App\Entity\ParamOption;
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

class ParameterController extends AbstractController
{
    /**
     * @Route("/admin/parameter", name="app_admin_parameter")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Parameter::class);
        return $this->render('parameter/index.html.twig', [
            'parameters' => $repository->findAll(),
        ]);
    }

    /**
     * @Route("/admin/parameter/create", name="app_admin_parameter_create")
     */
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $parameter = new Parameter();
        $form = self::form($parameter, $entityManager);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
          $parameter->setCreatedAt();
          $parameter->setUpdatedAt();
          $entityManager->persist($parameter);
          $entityManager->flush();
          return $this->redirectToRoute('app_parameter');
        }
        return $this->renderForm('parameter/parameter_form.html.twig', [
            'parameterForm' => $form,
        ]);
    }

    /**
     * @Route("/admin/parameter/edit/{id}", name="app_admin_parameter_edit", requirements={"id"="\d+"})
     */
    public function edit(Request $request, ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $parameter = $entityManager->getRepository(Parameter::class)->find($id);
        $form = $this->form($parameter, $entityManager);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
          $parameter->setUpdatedAt();
          $entityManager->persist($parameter);
          $entityManager->flush();
          return $this->redirectToRoute('app_parameter');
        }
        return $this->renderForm('parameter/parameter_form.html.twig', [
            'parameterForm' => $form,
        ]);
    }

    /**
     * @Route("/admin/parameter/{id}", name="app_admin_parameter_destroy", requirements={"id"="\d+"})
     */
    public function destroy(Request $request, ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $parameter = $entityManager->getRepository(Parameter::class)->find($id);
        $entityManager->remove($parameter);
        $entityManager->flush();
        return $this->redirectToRoute('app_parameter');
    }

    private function form(Parameter $parameter, EntityManagerInterface $entityManager)
    {
        $avParameters = $entityManager->getRepository(AvParameter::class)->findAll();
        $devices = $entityManager->getRepository(Device::class)->findAll();
        $paramOptions = $entityManager->getRepository(ParamOption::class)->findAll();
        $form = $this->createFormBuilder($parameter)
            ->add('device', ChoiceType::class, [
                'choices'  => $devices,
                'choice_label' => function(?Device $device) {
                    return $device ? $device->getName() : '';
                },
            ])
            ->add('av_parameter', ChoiceType::class, [
                'label' => 'Parameter',
                'choices'  => $avParameters,
                'choice_label' => function(?AvParameter $avParameter) {
                    return $avParameter ? $avParameter->getName() : '';
                },
            ])
            ->add('value', ChoiceType::class, [
                'placeholder' => '-',
                'required' => false,
                'choices'  => $paramOptions,
                'choice_label' => function(?paramOption $paramOption) {
                    return $paramOption ? $paramOption->getValue() : '';
                },
            ])
            ->add('custom_value', TextType::class, ['label' => 'Custom value'])
            ->add('save', SubmitType::class, ['label' => 'Save'])
            ->add('id', HiddenType::class, ['data_class' => null, 'mapped' => false,]);
        return $form->getForm();
    }
}
