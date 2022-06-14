<?php

namespace App\Controller;

use App\Entity\Device;
use App\Entity\Type;
use App\Entity\Vendor;
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

class DeviceController extends AbstractController
{
    /**
     * @Route("/admin/device", name="app_admin_device")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Device::class);
        return $this->render('device/index.html.twig', [
            'devices' => $repository->findAll(),
        ]);
    }

    /**
     * @Route("/admin/device/create", name="app_admin_device_create")
     */
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $device = new Device();
        $form = self::form($device, $entityManager);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
          $device->setCreatedAt();
          $device->setUpdatedAt();
          $entityManager->persist($device);
          $entityManager->flush();
          return $this->redirectToRoute('app_device');
        }
        return $this->renderForm('device/device_form.html.twig', [
            'deviceForm' => $form,
        ]);
    }

    /**
     * @Route("/admin/device/edit/{id}", name="app_admin_device_edit", requirements={"id"="\d+"})
     */
    public function edit(Request $request, ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $device = $entityManager->getRepository(Device::class)->find($id);
        $form = $this->form($device, $entityManager);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
          $device->setUpdatedAt();
          $entityManager->persist($device);
          $entityManager->flush();
          return $this->redirectToRoute('app_device');
        }
        return $this->renderForm('device/device_form.html.twig', [
            'deviceForm' => $form,
        ]);
    }

    /**
     * @Route("/admin/device/{id}", name="app_admin_device_destroy", requirements={"id"="\d+"})
     */
    public function destroy(Request $request, ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $device = $entityManager->getRepository(Device::class)->find($id);
        $entityManager->remove($device);
        $entityManager->flush();
        return $this->redirectToRoute('app_device');
    }

    private function form(Device $device, EntityManagerInterface $entityManager)
    {
        $types = $entityManager->getRepository(Type::class)->findAll();
        $vendors = $entityManager->getRepository(Vendor::class)->findAll();
        $form = $this->createFormBuilder($device)
            ->add('name', TextType::class)
            ->add('type', ChoiceType::class, [
                'choices'  => $types,
                'choice_label' => function(?Type $type) {
                    return $type ? $type->getName() : '';
                },
            ])
            ->add('vendor', ChoiceType::class, [
                'choices'  => $vendors,
                'choice_label' => function(?Vendor $vendor) {
                    return $vendor ? $vendor->getName() : '';
                },
            ])
            ->add('save', SubmitType::class, ['label' => 'Save'])
            ->add('id', HiddenType::class, ['data_class' => null, 'mapped' => false,]);
        return $form->getForm();
    }
}
