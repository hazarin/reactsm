<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontEndController extends AbstractController
{
    /**
     * @Route("/", name="front_end")
     */
    public function index(): Response
    {
        return $this->render('front_end/index.html.twig', [
            'controller_name' => 'FrontEndController',
        ]);
    }
}
