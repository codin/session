<?php

declare(strict_types=1);

namespace Codin\Session\Storage;

use Codin\Session\Exceptions\StorageError;
use DateTimeImmutable;
use PDO;
use PDOException;

class PdoStorage implements StorageInterface
{
    use Encoding;

    protected $pdo;

    protected $expire;

    protected $table;

    public function __construct(PDO $pdo, string $table, int $expire = 3600)
    {
        $this->pdo = $pdo;
        $this->table = $table;
        $this->expire = $expire;
    }

    public function purge()
    {
        $expired = time() - $this->expire;
        $date = DateTimeImmutable::createFromFormat('U', (string) $expired);

        if (!$date instanceof DateTimeImmutable) {
            throw new StorageError('Failed to create date time');
        }

        try {
            $stm = $this->pdo->prepare(sprintf('DELETE FROM %s WHERE last_active < ?', $this->table));
            $stm->execute([
                $date->format('Y-m-d H:i:s'),
            ]);
        } catch (PDOException $e) {
            throw new StorageError($e->getMessage(), $e->getCode());
        }
    }

    public function read(string $id): array
    {
        try {
            $stm = $this->pdo->prepare(sprintf('SELECT data FROM %s WHERE id = ?', $this->table));
            $stm->execute([$id]);
            $contents = $stm->fetchColumn();
        } catch (PDOException $e) {
            throw new StorageError($e->getMessage(), $e->getCode());
        }

        if (!$contents) {
            return [];
        }

        return $this->decode($contents);
    }

    public function exists(string $id): bool
    {
        try {
            $stm = $this->pdo->prepare(sprintf('SELECT id FROM %s WHERE id = ?', $this->table));
            $stm->execute([$id]);
            $id = $stm->fetchColumn();
        } catch (PDOException $e) {
            throw new StorageError($e->getMessage(), $e->getCode());
        }

        return $id === false ? false : true;
    }

    public function write(string $id, array $data): bool
    {
        $now = new DateTimeImmutable();
        $contents = $this->encode($data);

        $action = $this->exists($id) ?
            'UPDATE %s SET last_active = ?, data = ? WHERE id = ?' :
            'INSERT INTO %s (id, last_active, data) VALUES(?, ?, ?)';

        try {
            $stm = $this->pdo->prepare(sprintf($action, $this->table));
            $stm->execute([
                $id,
                $now->format('Y-m-d H:i:s'),
                $contents,
            ]);
            $affected = $stm->rowCount();
        } catch (PDOException $e) {
            throw new StorageError($e->getMessage(), $e->getCode());
        }

        return $affected > 0;
    }

    public function destroy(string $id): bool
    {
        try {
            $stm = $this->pdo->prepare(sprintf('DELETE FROM %s WHERE id = ?', $this->table));
            $stm->execute([
                $id,
            ]);
            $affected = $stm->rowCount();
        } catch (PDOException $e) {
            throw new StorageError($e->getMessage(), $e->getCode());
        }

        return $affected > 0;
    }
}
