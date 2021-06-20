<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class FrontEndController extends AbstractController
{
    public function index(): Response
    {
        return $this->render('front_end/index.html.twig', [
            'controller_name' => 'FrontEndController',
        ]);
    }
}
