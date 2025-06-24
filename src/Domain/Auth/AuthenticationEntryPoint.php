<?php

namespace App\Domain\Auth;

use App\Domain\Auth\Event\AccessDeniedHandler;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\RememberMeToken;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\InsufficientAuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class AuthenticationEntryPoint implements AuthenticationEntryPointInterface
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly AccessDeniedHandler $accessDeniedHandler,
    ) {
    }

    public function start(Request $request, ?AuthenticationException $authException = null): Response
    {
        $previous = $authException ? $authException->getPrevious() : null;

        if (
            $authException instanceof InsufficientAuthenticationException
            && $previous instanceof AccessDeniedException
            && $authException->getToken() instanceof RememberMeToken
        ) {
            $this->accessDeniedHandler->handle($request, $previous);
        }

        return new RedirectResponse($this->urlGenerator->generate('app_login'));
    }
}
