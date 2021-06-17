<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AuthController
 * @package App\Controller\Api
 * @Route ("/api/auth")
 */
class AuthController extends AbstractController
{
    /**
     * @Route("/register", name="api_auth_register", methods={"POST"})
     */
    public function register(): Response
    {
        return $this->render('api/auth/index.html.twig', [
            'controller_name' => 'AuthController',
        ]);
    }
}
