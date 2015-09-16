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
    public function serialize()
    {
        return serialize(array(
            $this->jwt,
            parent::serialize(),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($str)
    {
        list($this->jwt, $parentData) = unserialize($str);

        parent::unserialize($parentData);
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageData()
    {
        return array('{{ jwt }}' => $this->jwt);
    }
}
