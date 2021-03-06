<?php

declare(strict_types=1);

namespace Codin\Session\Storage;

use League\Flysystem\Filesystem;

class FlysystemStorage implements StorageInterface
{
    use Encoding;

    protected $fs;

    protected $expire;

    public function __construct(Filesystem $fs, int $expire = 3600)
    {
        $this->fs = $fs;
        $this->expire = $expire;
    }

    public function purge()
    {
        foreach ($this->fs->listContents() as $object) {
            if ($object->isFile() && $this->expired($object->getTimestamp())) {
                $object->delete();
            }
        }
    }

    protected function expired(int $last, ?int $time = null): bool
    {
        $time = $time ?: time();
        return ($last + $this->expire) < $time;
    }

    protected function filename(string $id): string
    {
        return sprintf('%s.sess', $id);
    }

    public function read(string $id): array
    {
        if (!$this->exists($id)) {
            return [];
        }

        $file = $this->filename($id);
        $contents = $this->fs->read($file);

        return $this->decode($contents);
    }

    public function exists(string $id): bool
    {
        $file = $this->filename($id);
        return $this->fs->fileExists($file) && !$this->expired($this->fs->lastModified($file));
    }

    public function write(string $id, array $data): bool
    {
        $file = $this->filename($id);

        $contents = $this->encode($data);

        $this->fs->write($file, $contents);

        return true;
    }

    public function destroy(string $id): bool
    {
        if ($this->exists($id)) {
            $file = $this->filename($id);
            $this->fs->delete($file);
        }

        return !$this->exists($id);
    }
}
