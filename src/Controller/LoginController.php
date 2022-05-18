<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Firebase\JWT\JWT;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{
    private UserRepository $repository;
    protected UserPasswordHasherInterface $hasher;

    public function __construct(UserRepository $repository, UserPasswordHasherInterface $hasher)
    {

        $this->repository = $repository;
        $this->hasher = $hasher;
    }

    #[Route('/login', name: 'app_login')]
    public function index(Request $request): Response
    {

        $dadosEmJson = json_decode($request->getContent());
        if (is_null($dadosEmJson->usuario) || is_null($dadosEmJson->senha)) {
            return new JsonResponse([
              'erro' => 'Favor enviar usuário e senha'
            ], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->repository->findOneBy(['username' => $dadosEmJson->usuario]);
        if (!$this->hasher->isPasswordValid($user, $dadosEmJson->senha)) {
            return new JsonResponse([
                'erro' => 'Usuário ou senha inválidos'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $token = JWT::encode(['username' => $user->getUsername()],'chave', 'HS256');

        return new JsonResponse([
            'access_token' => $token
        ]);
    }
}
