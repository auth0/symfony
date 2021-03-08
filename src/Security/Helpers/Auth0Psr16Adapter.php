<?php declare(strict_types=1);

namespace Auth0\JWTAuthBundle\Security\Helpers;

use Psr\SimpleCache\CacheInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * A PSR-16 interface for PSR-6 cache pools
 *
 * @package Auth0\JWTAuthBundle\Security
 */
class Auth0Psr16Adapter implements CacheInterface
{

    /**
     * Instance of a PSR-6 or PSR-16 compatible caching interface.
     *
     * @var CacheItemPoolInterface
     */
    protected $cache;

    /**
     * Auth0Psr16Adapter constructor.
     *
     * @param CacheItemPoolInterface $cache A PSR-6 or PSR-16 compatible cache interface.
     */
    public function __construct(CacheItemPoolInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $key     The unique key of this item in the cache.
     * @param mixed  $default Default value to return if the key does not exist.
     *
     * @return mixed The value of the item from the cache, or $default in case of cache miss.
     */
    public function get($key, $default = null)
    {
        $item = null;

        try {
            $item = $this->cache->getItem($key);
        } catch (\Throwable $th) {
            $item = null;
        }

        if ($item && $item->isHit()) {
            return $item->get();
        }

        return $default;
    }

    /**
     * {@inheritdoc}
     *
     * @param string                     $key   The key of the item to store.
     * @param mixed                      $value The value of the item to store. Must be serializable.
     * @param null|integer|\DateInterval $ttl   Optional. The TTL value of this item. If no value is sent and
     *                                          the driver supports TTL then the library may set a default value
     *                                          for it or let the driver take care of that.
     *
     * @return boolean True on success and false on failure.
     */
    public function set($key, $value, $ttl = null)
    {
        try {
            $item = $this->cache->getItem($key);
        } catch (\Throwable $th) {
            $item = null;
        }

        if ($item) {
            $item->expiresAfter($ttl);
            $item->set($value);
            return $this->cache->save($item);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $key The unique cache key of the item to delete.
     *
     * @return boolean True if the item was successfully removed. False if there was an error.
     */
    public function delete($key)
    {
        try {
            return $this->cache->deleteItem($key);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     *
     * @return boolean True on success and false on failure.
     */
    public function clear()
    {
        return $this->cache->clear();
    }

    /**
     * {@inheritdoc}
     *
     * @param array<string> $keys    A list of keys that can obtained in a single operation.
     * @param mixed         $default Default value to return for keys that do not exist.
     *
     * @return array<int|string,mixed> A list of key => value pairs. Missing cache keys will have $default as value.
     */
    // phpcs:ignore
    public function getMultiple($keys, $default = null)
    {
        $items    = $this->cache->getItems($keys);
        $response = [];

        foreach ($items as $key => $item) {
            /*
             * @type $item CacheItemInterface
             */

            if (! $item->isHit()) {
                $response[$key] = $default;
            }

            $response[$key] = $item->get();
        }

        return $response;
    }

    /**
     * {@inheritdoc}
     *
     * @param array<string,mixed>        $values A list of key => value pairs for a multiple-set operation.
     * @param null|integer|\DateInterval $ttl    Optional. The TTL value of this item. If no value is sent and
     *                                           the driver supports TTL then the library may set a default value
     *                                           for it or let the driver take care of that.
     *
     * @return boolean True on success and false on failure.
     */
    // phpcs:ignore
    public function setMultiple($values, $ttl = null)
    {
        $keys        = [];
        $arrayValues = [];

        foreach ($values as $key => $value) {
            $keys[]            = $key;
            $arrayValues[$key] = $value;
        }

        try {
            $items   = $this->cache->getItems($keys);
            $success = true;

            foreach ($items as $key => $item) {
                $item->set($arrayValues[$key]);
                $item->expiresAfter($ttl);

                if ($success) {
                    $success = $this->cache->saveDeferred($item);
                }
            }
        } catch (\Throwable $th) {
            $success = false;
        }

        if ($success) {
            $success = $this->cache->commit();
        }

        return $success;
    }

    /**
     * {@inheritdoc}
     *
     * @param array<string> $keys A list of string-based keys to be deleted.
     *
     * @return boolean True if the items were successfully removed. False if there was an error.
     */
    // phpcs:ignore
    public function deleteMultiple($keys)
    {
        return $this->cache->deleteItems($keys);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $key The cache item key.
     *
     * @return boolean
     */
    public function has($key)
    {
        return $this->cache->hasItem($key);
    }
}
