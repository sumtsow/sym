<?php

namespace App\Controller;

use App\Entity\ParamOption;
use App\Entity\AvParameter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ParamOptionController extends AbstractController
{
    /**
     * @Route("/admin/param_option", name="app_admin_param_option")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        return $this->render('param_option/index.html.twig', [
            'param_options' => $doctrine->getRepository(ParamOption::class)->findAll(),
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
        //$type_id = intval($type_id);
        $rsm = new ResultSetMapping();
        $query = $entityManager->createNativeQuery('SELECT id, type_id, name, created_at, updated_at FROM av_parameter' . ($type_id ? ' WHERE type_id = ?' : ''), $rsm);
        $query->setParameter(1, $type_id);
        $avParameters = $query->getResult();
        $form = $this->createFormBuilder($paramOption)
            ->add('av_parameter', ChoiceType::class, [
                'attr' => ['class' => 'form-select'],
                'choices'  => $avParameters,
                'choice_label' => function(?AvParameter $avParameter) {
                    return $avParameter ? $avParameter->getName() : '';
                },
            ])
            ->add('value', TextType::class, [
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
