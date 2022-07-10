<?php

namespace App\Controller;

use App\Entity\Device;
use App\Entity\AvParameter;
use App\Entity\Type;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatableMessage;


class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="app_default")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        return $this->render('default/index.html.twig', [
            'devices' => $doctrine->getRepository(Device::class)->findAll(),
            'types' => $doctrine->getRepository(Type::class)->findAll(),
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
     * @Route("/compare", name="app_compare", methods={"POST"})
     */
    public function compare (Request $request, ManagerRegistry $doctrine): Response
    {
        $ids = array_map('intval', (array) json_decode($request->get('devices')));
        $error = '';
        $parameters = [];
        $devices = [];
        $hasDifferentTypes = false;
        if (in_array(0, $ids)) {
          $error .= 'Compare list error!';
        } elseif(count($ids) > 10) {
          $error .= 'Compare list length is too big!';
        } else {
          $rep = $doctrine->getRepository(Device::class);
          $devices = $rep->findById($ids);
          $firstType = $devices[0]->getType();
          foreach ($devices as $device) {
            $hasDifferentTypes = $hasDifferentTypes || $device->getType() !== $firstType;
          }
          $list = $rep->getParameterList($ids);
          $parameters = $doctrine->getRepository(AvParameter::class)->findById($list);
          if (!$devices || !$parameters || $hasDifferentTypes) $error .= 'Compare list error!';
        }
        return $this->render('device/compare.html.twig', [
            'error' => $error,
            'devices' => $devices,
            'parameters' => $parameters
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
        $request->getSession()->set('_locale', $_locale);
        return $this->redirect($request->server->get('HTTP_REFERER'));
    }
}
