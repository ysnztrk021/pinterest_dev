<?php

namespace App\Controller;

use App\Entity\Pin;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PinRepository;

class PinController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(PinRepository $repo): Response
    {


        // die;
        return $this->render('pin/index.html.twig', ['pins' => $repo->findAll()]);
        // return $this->render('pin/index.html.twig', [
        //     'controller_name' => 'PinController',
        // ]);
        // return new Response('Hello World');
        // return $this->json(['message'=>'Hello World']);
    }
    /**
     * @Route("/pin/{id<[0-9]+>}", name="app_pin_show", methods="GET")
     */
    public function show(Pin $pin): Response
    {
        return $this->render('pin/show.html.twig', compact('pin'));
    }
}
