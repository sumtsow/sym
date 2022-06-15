<?php

namespace App\Controller;

use App\Entity\Country;
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

class VendorController extends AbstractController
{
    /**
     * @Route("/admin/vendor", name="app_admin_vendor")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Vendor::class);
        return $this->render('vendor/index.html.twig', [
            'vendors' => $repository->findAll(),
        ]);
    }

    /**
     * @Route("/admin/vendor/create", name="app_admin_vendor_create")
     */
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $vendor = new Vendor();
        $form = self::form($vendor, $entityManager);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
          $vendor->setCreatedAt();
          $vendor->setUpdatedAt();
          $entityManager->persist($vendor);
          $entityManager->flush();
          return $this->redirectToRoute('app_admin_vendor');
        }
        return $this->renderForm('vendor/vendor_form.html.twig', [
            'vendorForm' => $form,
        ]);
    }

    /**
     * @Route("/admin/vendor/edit/{id}", name="app_admin_vendor_edit", requirements={"id"="\d+"})
     */
    public function edit(Request $request, ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $vendor = $entityManager->getRepository(Vendor::class)->find($id);
        $form = $this->form($vendor, $entityManager);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
          $vendor->setUpdatedAt();
          $entityManager->persist($vendor);
          $entityManager->flush();
          return $this->redirectToRoute('app_admin_vendor');
        }
        return $this->renderForm('vendor/vendor_form.html.twig', [
            'vendorForm' => $form,
        ]);
    }

    /**
     * @Route("/admin/vendor/{id}", name="app_admin_vendor_destroy", requirements={"id"="\d+"})
     */
    public function destroy(Request $request, ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $vendor = $entityManager->getRepository(Vendor::class)->find($id);
        $entityManager->remove($vendor);
        $entityManager->flush();
        return $this->redirectToRoute('app_admin_vendor');
    }

    private function form(Vendor $vendor, EntityManagerInterface $entityManager)
    {
        $countries = $entityManager->getRepository(Country::class)->findAll();
        $form = $this->createFormBuilder($vendor)
            ->add('name', TextType::class)
            ->add('country', ChoiceType::class, [
                'choices'  => $countries,
                'choice_label' => function(?Country $country) {
                    return $country ? $country->getName() : '';
                },
                'choice_attr' => function($country, $key, $value) {
                    return [
                        'class' => 'country-option',
                        //'data-code' => $country->getCode(),
                        //'data-abbr2' => $country->getAbbr2(),
                        //'data-abbr3' => $country->getAbbr3(),
                        'style' => 'background-image:url(/images/flags/'.$country->getAbbr2().'.png);',
                    ];
                }
            ])
            ->add('save', SubmitType::class, ['label' => 'Save'])
            ->add('id', HiddenType::class, ['data_class' => null, 'mapped' => false,]);
        return $form->getForm();
    }
}
