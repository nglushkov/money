<?php

namespace App\Service;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    private $cache;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    public function set(string $key, $value, int $seconds): void
    {
        $this->cache::put($key, $value, $seconds);
    }

    /**
     * Store a value in the cache forever.
     *
     * @param string $key
     * @param $value
     * @return void
     */
    public function forever(string $key, $value): void
    {
        $this->cache::forever($key, $value);
    }

    /**
     * Get the value of the given key.
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key): mixed
    {
        try {
            return $this->cache::get($key);

        } catch (\Exception $e) {
            logger()->error($e->getMessage(), ['key' => $key]);
        }
    }
}
