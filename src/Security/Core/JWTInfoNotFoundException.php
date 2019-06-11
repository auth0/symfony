<?php

namespace Auth0\JWTAuthBundle\Security\Core;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * @author german
 */
class JWTInfoNotFoundException extends AuthenticationException
{
    private $jwt;

    /**
     * {@inheritdoc}
     */
    public function getMessageKey()
    {
        return 'JWT could not be found.';
    }

    /**
     * Get the username.
     *
     * @return string
     */
    public function getJWT()
    {
        return $this->jwt;
    }

    /**
     * Set the username.
     *
     * @param string $username
     */
    public function setJWT($jwt)
    {
        $this->jwt = $jwt;
    }

    /**
     * {@inheritdoc}
     */
    public function __serialize(): array
    {
        return serialize([
            $this->jwt,
            parent::__serialize(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function __unserialize(array $data): void
    {
        [$this->jwt, $parentData] = unserialize($data);

        parent::__unserialize($parentData);
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageData()
    {
        return array('{{ jwt }}' => $this->jwt);
    }
}
