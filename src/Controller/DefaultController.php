<?php

namespace App\Controller;

use App\Entity\Device;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="app_default")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        return $this->render('default/index.html.twig', [
            'devices' => $doctrine->getRepository(Device::class)->findAll()
        ]);
    }

    /**
     * @Route("/device/{id}", name="app_device_edit")
     */
    public function show(ManagerRegistry $doctrine, int $id): Response
    {
        return $this->render('device/show.html.twig', [
            'device' => $doctrine->getRepository(Device::class)->find($id)
        ]);
    }

    /**
     * @Route("/home", name="app_home")
     */
    public function home(): Response
    {
        return $this->render('home/index.html.twig');
    }

    /**
     * @Route("/admin", name="app_admin")
     */
    public function admin(): Response
    {
        return $this->render('home/admin.html.twig');
    }

    /**
     * @Route("/profile", name="app_profile")
     */
    public function profile(): Response
    {
        return $this->render('profile/index.html.twig');
    }

    /**
     * @Route("/locale/{_locale}", name="app_locale", requirements={"_locale": "en|uk",})
     */
    public function locale(Request $request, $_locale): Response
    {
        $request->setLocale($_locale);
        return $this->redirect($request->server->get('HTTP_REFERER'));
    }
}
