<?php

namespace App\Security;

use Firebase\JWT\JWT;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class JwtAutenticador implements AuthenticatorInterface
{

    public function supports(Request $request): ?bool
    {
        return $request->getPathInfo() != '/login';
    }

    public function authenticate(Request $request): Passport
    {
        $token = str_replace(
            'Bearer ',
            '',
            $request->headers->get('Authorization'));
        return JWT::decode($token, 'chave', ['HS256']);
    }

    public function createToken(Passport $passport, string $firewallName): TokenInterface
    {
        // TODO: Implement createToken() method.
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse([
            'erro' => 'Falha na autenticação'
        ], Response::HTTP_UNAUTHORIZED);
    }
}