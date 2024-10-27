<?php

namespace App\Controller\Api;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class ApiLoginController extends AbstractController
{

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function index(#[CurrentUser] ?User $user): Response
    {
        if(!$user){
            return $this->json([
               'message' => 'You must be logged in to access this page'
            ], 401);
        }
        return $this->json([
            'message' => 'Welcome to the API',
            'path'    => 'src/Controller/ApiLoginController.php'
        ]);
    }
}