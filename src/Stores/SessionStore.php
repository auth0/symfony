<?php

declare(strict_types=1);

namespace Auth0\Symfony\Stores;

use Auth0\SDK\Contract\StoreInterface;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\{Request, RequestStack};
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Throwable;

use function gettype;
use function is_array;
use function is_string;

final class SessionStore implements StoreInterface
{
    /**
     * @param string          $namespace
     * @param RequestStack    $requestStack
     * @param LoggerInterface $logger
     *
     * @psalm-suppress DocblockTypeContradiction
     */
    public function __construct(
        private $namespace,
        private RequestStack $requestStack,
        private LoggerInterface $logger,
    ) {
        if (! is_string($namespace)) {
            $this->logger->error('SessionStore: $namespace must be a string, {type} given.', [
                'type' => gettype($namespace),
            ]);

            throw new InvalidArgumentException('$namespace must be a string.');
        }
    }

    /**
     * This has no effect when using sessions as the storage medium.
     *
     * @param bool $deferring whether to defer persisting the storage state
     *
     * @codeCoverageIgnore
     */
    public function defer(
        bool $deferring,
    ): void {
    }

    /**
     * Removes a persisted value identified by $key.
     *
     * @param string $key session key to delete
     */
    public function delete(
        string $key,
    ): void {
        $manifest = $this->session()?->get($this->namespace, []);

        if (! is_array($manifest) || [] === $manifest) {
            return;
        }

        if (isset($manifest[$key])) {
            unset($manifest[$key]);

            if ([] === $manifest) {
                $this->session()?->remove($this->namespace);

                return;
            }

            $this->session()?->set($this->namespace, $manifest);
        }
    }

    /**
     * Gets persisted values identified by $key.
     * If the value is not set, returns $default.
     *
     * @param string $key     session key to set
     * @param mixed  $default default to return if nothing was found
     *
     * @return mixed
     */
    public function get(
        string $key,
        $default = null,
    ) {
        $manifest = $this->session()?->get($this->namespace, []);

        if (! is_array($manifest) || [] === $manifest || ! isset($manifest[$key])) {
            return $default;
        }

        return $manifest[$key];
    }

    /**
     * Removes all persisted values.
     */
    public function purge(): void
    {
        $this->session()?->remove($this->namespace);
    }

    /**
     * Persists $value on $_SESSION, identified by $key.
     *
     * @param string $key   session key to set
     * @param mixed  $value value to use
     */
    public function set(
        string $key,
        $value,
    ): void {
        $manifest = $this->session()?->get($this->namespace, []);

        if (! is_array($manifest)) {
            $manifest = [];
        }

        $manifest[$key] = $value;

        $this->session()?->set($this->namespace, $manifest);
    }

    private function session(
        ?Request $request = null,
    ): ?SessionInterface {
        if (PHP_SESSION_DISABLED === session_status()) {
            return null;
        }

        $request ??= $this->requestStack->getCurrentRequest();
        $session = null;

        if (! $request instanceof Request) {
            return null;
        }

        try {
            $session = $request->getSession();
        } catch (Throwable) {
        }

        if ($session instanceof SessionInterface) {
            if (! $session->isStarted()) {
                $session->start();
            }

            return $session;
        }

        return null;
    }
}
