<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="app_default")
     */
    public function index(): Response
    {
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController'
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
}
