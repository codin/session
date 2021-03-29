<?php

declare(strict_types=1);

namespace Codin\Session\Storage;

class MemoryStorage implements StorageInterface
{
    use Encoding;

    protected $items = [];

    public function read(string $id): array
    {
        return $this->decode($this->items[$id]);
    }

    public function exists(string $id): bool
    {
        return isset($this->items[$id]);
    }

    public function write(string $id, array $data): bool
    {
        $this->items[$id] = $this->encode($data);

        return true;
    }

    public function destroy(string $id): bool
    {
        unset($this->items[$id]);

        return isset($this->items[$id]);
    }
}
