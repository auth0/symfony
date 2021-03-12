<?php declare(strict_types=1);

namespace Auth0\JWTAuthBundle\Security\Core;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * AuthenticationException extension for JWT token handling.
 */
class JWTInfoNotFoundException extends AuthenticationException
{

    /**
     * Store for our JWT.
     *
     * @var string
     */
    private $jwt;

    /**
     * {@inheritdoc}
     *
     * @return string;
     */
    public function getMessageKey(): string
    {
        return 'JWT could not be found.';
    }

    /**
     * Get the user jwt.
     *
     * @return string
     */
    public function getJWT(): string
    {
        return $this->jwt;
    }

    /**
     * Set the user jwt.
     *
     * @param string $jwt A valid JWT object.
     *
     * @return void
     */
    public function setJWT($jwt): void
    {
        $this->jwt = $jwt;
    }

    /**
     * {@inheritdoc}
     *
     * @return string The string representing the serialized JWT.
     */
    public function serialize(): string
    {
        return serialize([
            $this->jwt,
            parent::serialize(),
        ]);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $str A string representing a serialized JWT.
     *
     * @return void
     */
    public function unserialize($str)
    {
        list($this->jwt, $parentData) = unserialize($str);

        parent::unserialize($parentData);
    }

    /**
     * {@inheritdoc}
     *
     * @return array<string,mixed>
     */
    public function getMessageData(): array
    {
        return ['{{ jwt }}' => $this->jwt];
    }
}
