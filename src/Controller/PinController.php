<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Pin;
use App\Entity\User;
use App\Repository\PinRepository;
use App\Form\PinType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Id;

class PinController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(PinRepository $repo): Response
    {
        return $this->render('pin/index.html.twig', ['pins' => $repo->findAll()]);
    }

    /**
     * @Route("/pin/{id<[0-9]+>}", name="app_pin_show", methods="GET")
     */
    public function show(Pin $pin): Response
    {
        return $this->render('pin/show.html.twig', compact('pin'));
    }

    /**
     * @Route("/pin/create", name="app_pin_create", methods= {"GET","POST"})
     */
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('error', 'You have to be connected to create a pin');
            return $this->redirectToRoute('app_home');
        }
        $pin = new Pin;
        $form = $this->createForm(PinType::class, $pin);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $pin->setUser($this->getUser());
            $em->persist($pin);
            $em->flush();
            $this->addFlash('success', 'Pin successfully created !');
            return $this->redirectToRoute('app_home');
        }
        return $this->render('pin/create.html.twig', ['monForm' => $form->createView()]);
    }

    /**
     * @Route("/pin/{id<[0-9]+>}/edit", name="app_pin_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Pin $pin, EntityManagerInterface $em, User $user): Response
    {
        if (!$this->getUser() || $pin->getUser()->getId() != $user->getId()) {
            $this->addFlash('error', 'You have to be the owner of this pin to edit it');
            return $this->redirectToRoute('app_home');
        }
        $form = $this->createForm(PinType::class, $pin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Pin successfully updated !'); 
            return $this->redirectToRoute('app_home');
        }

        return $this->render('pin/edit.html.twig', [
            'pin' => $pin,
            'monForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/pin/{id<[0-9]+>}/delete", name="app_pin_delete")
     */
    public function delete(Pin $pin, EntityManagerInterface $em, User $user): Response
    {
        if (!$this->getUser() || $pin->getUser()->getId() != $user->getId()) {
            $this->addFlash('error', 'You have to be the owner of this pin to delete it');
            return $this->redirectToRoute('app_home');
        }
        $em->remove($pin);
        $em->flush();
        $this->addFlash('info', 'Pin successuly deleted!'); 
        return $this->redirectToRoute('app_home');
    }
}
