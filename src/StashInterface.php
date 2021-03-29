<?php

declare(strict_types=1);

namespace Codin\Session;

interface StashInterface
{
    public function rotate();

    public function getStash(string $key);

    public function putStash(string $key, $value);
}
