<?php

declare(strict_types=1);

namespace Auth0\Symfony\Security;

use Auth0\SDK\Auth0;
use Auth0\SDK\Configuration\SdkConfiguration;
use Auth0\SDK\Contract\StoreInterface;
use Auth0\SDK\Store\CookieStore;
use Auth0\Symfony\Contracts\Security\ServiceInterface;
use Psr\Cache\CacheItemPoolInterface;

final class Service implements ServiceInterface
{
    public function __construct(
        private ?Auth0 $sdk = null,
        private ?SdkConfiguration $configuration = null,
        private ?StoreInterface $store = null,
        private ?CacheItemPoolInterface $cache = null
    ) {
    }

    public function getSdk()
    {
        if (null === $this->sdk) {
            $this->sdk = new Auth0($this->getConfiguration());
        }

        return $this->sdk;
    }

    public function getConfiguration(): ?SdkConfiguration
    {
        if (null === $this->configuration) {
            $this->configuration = new SdkConfiguration();
        }

        return $this->configuration;
    }

    public function getStore(): ?StoreInterface
    {
        if (null === $this->store) {
            $this->store = new CookieStore($this->getConfiguration());
        }

        return $this->store;
    }

    public function getCache(): ?CacheItemPoolInterface
    {
        return $this->cache;
    }
}
