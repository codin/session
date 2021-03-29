<?php

declare(strict_types=1);

namespace Codin\Session;

use Psr\Http\Message\ServerRequestInterface;

class Cookies implements CookiesInterface
{
    protected $cookies;

    final public function __construct(array $cookies = [])
    {
        $this->cookies = $cookies;
    }

    public static function fromGlobals()
    {
        return new static($_COOKIE);
    }

    public static function fromRequest(ServerRequestInterface $request)
    {
        return new static($request->getCookieParams());
    }

    public function has(string $name): bool
    {
        return array_key_exists($name, $this->cookies);
    }

    public function get(string $name, $default = null)
    {
        return $this->has($name) ? $this->cookies[$name] : $default;
    }
}
