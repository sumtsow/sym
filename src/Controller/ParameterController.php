<?php

namespace App\Controller;

use App\Entity\AvParameter;
use App\Entity\Device;
use App\Entity\Parameter;
use App\Entity\ParamOption;
use App\Entity\Type;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatableMessage;

class ParameterController extends AbstractController
{
    /**
     * @Route("/admin/parameter", name="app_admin_parameter")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        return $this->render('parameter/index.html.twig', [
            'devices' => $doctrine->getRepository(Device::class)->findAll(),
        ]);
    }

    /**
     * @Route("/api/parameter/{id?}", name="app_api_parameter")
     */
    public function list(ManagerRegistry $doctrine, $id = 0): JsonResponse
    {
        $id = intval($id);
        if ($id) {
          $devices = $doctrine->getRepository(Device::class)->find($id);
          $parameters = $devices ? $devices->getParameters() : [];
        } else {
          $parameters = $doctrine->getRepository(Parameter::class)->findAll();
        }
        return $this->json(['rows' => Parameter::toArray($parameters)]);
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
          $prioExists = $entityManager->getRepository(Parameter::class)->findOneBy([
              'prio' => $parameter->getPrio(),
              'device' => $parameter->getDevice()
              ]);
          if (!$prioExists || $prioExists->getId() === $parameter->getId()) {
            $parameter->setCreatedAt();
            $parameter->setUpdatedAt();
            $entityManager->persist($parameter);
            $entityManager->flush();
            return $this->redirectToRoute('app_admin_parameter');
          }
          $error = new FormError('This prio is used');
          $form->get('prio')->addError($error);
        }
        return $this->renderForm('parameter/parameter_form.html.twig', [
            'parameterForm' => $form,
            'types' => $entityManager->getRepository(Type::class)->findAll(),
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
          $prioExists = $entityManager->getRepository(Parameter::class)->findOneBy([
              'prio' => $parameter->getPrio(),
              'device' => $parameter->getDevice()
              ]);
          if (!$prioExists || $prioExists->getId() === $parameter->getId()) {
            $parameter->setUpdatedAt();
            $entityManager->persist($parameter);
            $entityManager->flush();
            return $this->redirectToRoute('app_admin_parameter');
          }
          $error = new FormError('This prio is used');
          $form->get('prio')->addError($error);
        }
        return $this->renderForm('parameter/parameter_form.html.twig', [
            'parameterForm' => $form,
            'types' => $entityManager->getRepository(Type::class)->findAll(),
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
        return $this->redirectToRoute('app_admin_parameter');
    }

    private function form(Parameter $parameter, EntityManagerInterface $entityManager)
    {
        $avParameters = $entityManager->getRepository(AvParameter::class)->findAll();
        $devices = $entityManager->getRepository(Device::class)->findAll();
        $paramOptions = $entityManager->getRepository(ParamOption::class)->findAll();
        $form = $this->createFormBuilder($parameter)
            ->add('device', ChoiceType::class, [
                'attr' => ['class' => 'form-select'],
                'choices'  => $devices,
                'choice_label' => function(?Device $device) {
                    return $device ? $device->getName() : '';
                },
            ])
            ->add('prio', IntegerType::class, [
                'label' => new TranslatableMessage('Prio'),
                'attr' => ['class' => 'form-control'],
            ])
            ->add('av_parameter', ChoiceType::class, [
                'label' => 'Parameter',
                'attr' => [
                    'class' => 'form-select',
                    'data-admin-show-target' => 'select',
                    'data-action' => 'change->admin-show#changeParam',
                  ],
                'placeholder' => '-',
                'choices'  => $avParameters,
                'choice_label' => function(?AvParameter $avParameter) {
                    return $avParameter ? $avParameter->getName().' ('.$avParameter->getType()->getName().')' : '';
                },
            ])
            ->add('value', ChoiceType::class, [
                'attr' => [
                    'class' => 'form-select',
                    'data-admin-show-target' => 'rows',
                ],
                'placeholder' => '-',
                'required' => false,
                'choices'  => $paramOptions,
                'choice_label' => function(?paramOption $paramOption) {
                    return $paramOption ? $paramOption->getValue() : '';
                },
                'choice_attr' => function($choice) {
                    return ['data-parent' => $choice->getAvParameter()->getId()];
                },
            ])
            ->add('custom_value', TextareaType::class, [
                'label' => 'Custom value',
                'required' => false,
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
