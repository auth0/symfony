<?php
declare(strict_types=1);

namespace Auth0\JWTAuthBundle\Security\Guard;

use Auth0\JWTAuthBundle\Security\Auth0Service;
use Auth0\JWTAuthBundle\Security\Core\JWTInfoNotFoundException;
use Auth0\SDK\Exception\CoreException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class JwtAuthenticator extends AbstractAuthenticator
{
    use AuthenticatorTrait;

    /**
     * Reference to an instance of Auth0Service.
     *
     * @var Auth0Service
     */
    private $auth0Service;

    public function __construct(Auth0Service $auth0Service)
    {
        $this->auth0Service = $auth0Service;
    }

    public function authenticate(Request $request): PassportInterface
    {
        $jwtString = $request->headers->get('Authorization');
        if (!$jwtString) {
            throw new JWTInfoNotFoundException('JWT is missing in the request Authorization header');
        }

        if (0 !== strpos(strtolower($jwtString), 'bearer ')) {
            throw new JWTInfoNotFoundException('JWT is not a bearer token');
        }

        try {
            $jwtString = str_replace(['Bearer ', 'bearer '], ['',  ''], $jwtString);
            $jwt = $this->auth0Service->decodeJWT($jwtString);
        } catch (CoreException $exception) {
            throw new AuthenticationException($exception->getMessage(), $exception->getCode(), $exception);
        }

        if (!$jwt) {
            throw new AuthenticationException('Your JWT seems invalid');
        }

        return new SelfValidatingPassport(new UserBadge($jwtString));
    }
}
