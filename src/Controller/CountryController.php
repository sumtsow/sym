<?php

namespace App\Controller;

use App\Entity\Country;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CountryController extends AbstractController
{
    /**
     * @Route("/country", name="app_country")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Country::class);
        return $this->render('country/index.html.twig', [
            'countries' => $repository->findAll(),
        ]);
    }

    /**
     * @Route("/country/create", name="app_country_create")
     */
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $country = new Country();
        $form = self::form($country);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $country->setCreatedAt();
            $country->setUpdatedAt();
            $entityManager->persist($country);
            $entityManager->flush();
            return $this->redirectToRoute('app_country');
        }
        return $this->renderForm('country/country_form.html.twig', [
            'countryForm' => $form,
        ]);
    }

    /**
     * @Route("/country/edit/{id}", name="app_country_edit", requirements={"id"="\d+"})
     */
    public function edit(Request $request, ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $country = $entityManager->getRepository(Country::class)->find($id);
        $form = $this->form($country);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $country->setUpdatedAt();
            $entityManager->persist($country);
            $entityManager->flush();
            //return new Response('Saved new country with id '.$country->getId());
        }
        return $this->renderForm('country/country_form.html.twig', [
            'countryForm' => $form,
        ]);
    }

    /**
     * @Route("/country/{id}", name="app_country_destroy", requirements={"id"="\d+"})
     */
    public function destroy(Request $request, ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $country = $entityManager->getRepository(Country::class)->find($id);
        $entityManager->remove($country);
        $entityManager->flush();
        return $this->redirectToRoute('app_country');
    }

    private function form(Country $country)
    {
        $form = $this->createFormBuilder($country)
            ->add('name', TextType::class)
            ->add('abbr2', TextType::class)
            ->add('abbr3', TextType::class)
            ->add('code', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Save'])
            ->add('id', HiddenType::class, ['data_class' => null, 'mapped' => false,]);
        return $form->getForm();
    }
}
