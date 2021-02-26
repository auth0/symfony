<?php

namespace Auth0\JWTAuthBundle\Security\Core;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * @author german
 */
class JWTInfoNotFoundException extends AuthenticationException
{

    /**
     * @var string
     */
    private $jwt;

    /**
     * {@inheritdoc}
     */
    public function getMessageKey()
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
     * @param string $jwt
     */
    public function setJWT($jwt): void
    {
        $this->jwt = $jwt;
    }

    /**
     * {@inheritdoc}
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
     * @param string $str
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
