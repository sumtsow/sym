<?php

namespace App\Controller;

use App\Entity\AvParameter;
use App\Entity\ParamOption;
use App\Entity\Type;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatableMessage;

class ParamOptionController extends AbstractController
{
    /**
     * @Route("/admin/param_option", name="app_admin_param_option")
     */
    public function index(Request $request, ManagerRegistry $doctrine): Response
    {
        return $this->render('param_option/index.html.twig', [
            'av_parameters' => $av_parameters = $doctrine->getRepository(AvParameter::class)->findAll(),
            'types' => $doctrine->getRepository(Type::class)->findAll(),
        ]);
    }

    /**
     * @Route("/api/param_option/{id?}", name="app_api_param_option")
     */
    public function list(Request $request, ManagerRegistry $doctrine, $id = 0): JsonResponse
    {
        $id = intval($id);
        $type_id = intval($request->query->get('type'));
        $type = false;
        $options = $params = [];
        if ($type_id) {
          $type = $doctrine->getRepository(Type::class)->find($type_id);
        }
        if ($id) {
          $avParameter = $doctrine->getRepository(AvParameter::class)->find($id);
          $options = $avParameter ? $avParameter->getParamOptions() : [];
          if ($type) {
            $avParameters = $type->getAvParameters();
            if ($avParameters) {
              foreach($avParameters as $avParameter) {
                $params[$avParameter->getId()] = [
                    'id' => $avParameter->getId(),
                    'name' => $avParameter->getName(),
                ];
              }
            }
          }
        } else {
          if ($type) {
            $avParameters = $type->getAvParameters();
            if ($avParameters) {
              foreach($avParameters as $avParameter) {
                $options = array_merge($options, $avParameter->getParamOptions()->toArray());
                $params[$avParameter->getId()] = [
                    'id' => $avParameter->getId(),
                    'name' => $avParameter->getName(),
                ];
              }
            }
          } else {
            $options = $doctrine->getRepository(ParamOption::class)->findAll();
          }
        }
        return $this->json([
          'rows' => ParamOption::toArray($options),
          'params' => $params,
        ]);
    }

    /**
     * @Route("/admin/param_option/create", name="app_admin_param_option_create")
     */
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $type_id = intval($request->query->get('type'));
        $paramOption = new ParamOption();
        $form = self::form($paramOption, $entityManager, $type_id);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
          $paramOption->setCreatedAt();
          $paramOption->setUpdatedAt();
          $entityManager->persist($paramOption);
          $entityManager->flush();
          return $this->redirectToRoute('app_admin_param_option');
        }
        return $this->renderForm('param_option/param_option_form.html.twig', [
            'paramOptionForm' => $form,
        ]);
    }

    /**
     * @Route("/admin/param_option/edit/{id}", name="app_admin_param_option_edit", requirements={"id"="\d+"})
     */
    public function edit(Request $request, ManagerRegistry $doctrine, int $id): Response
    {
        $type_id = intval($request->query->get('type'));
        $entityManager = $doctrine->getManager();
        $paramOption = $entityManager->getRepository(ParamOption::class)->find($id);
        $form = $this->form($paramOption, $entityManager, $type_id);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
          $paramOption->setUpdatedAt();
          $entityManager->persist($paramOption);
          $entityManager->flush();
          return $this->redirectToRoute('app_admin_param_option');
        }
        return $this->renderForm('param_option/param_option_form.html.twig', [
            'paramOptionForm' => $form,
        ]);
    }

    /**
     * @Route("/admin/param_option/{id}", name="app_admin_param_option_destroy", requirements={"id"="\d+"})
     */
    public function destroy(Request $request, ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $paramOption = $entityManager->getRepository(ParamOption::class)->find($id);
        $entityManager->remove($paramOption);
        $entityManager->flush();
        return $this->redirectToRoute('app_admin_param_option');
    }

    private function form(ParamOption $paramOption, EntityManagerInterface $entityManager, int $type_id = 0)
    {
        $type_id = intval($type_id);
        $avParameters = $entityManager->getRepository(AvParameter::class)->findAll();
        $form = $this->createFormBuilder($paramOption)
            ->add('av_parameter', ChoiceType::class, [
                'label' => new TranslatableMessage('Available parameter'),
                'attr' => ['class' => 'form-select'],
                'choices'  => $avParameters,
                'choice_label' => function(?AvParameter $avParameter) {
                    return $avParameter ? $avParameter->getName() : '';
                },
            ])
            ->add('value', TextareaType::class, [
                'attr' => ['class' => 'form-control'],
            ])
            ->add('save', SubmitType::class, [
                'label' => new TranslatableMessage('Save'),
                'attr' => ['class' => 'btn btn-primary mt-3'],
                ])
            ->add('id', HiddenType::class, ['data_class' => null, 'mapped' => false,]);
        return $form->getForm();
    }
}
