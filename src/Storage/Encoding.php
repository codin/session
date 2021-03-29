<?php

declare(strict_types=1);

namespace Codin\Session\Storage;

use Codin\Session\Exceptions\EncodingError;

trait Encoding
{
    protected function encode(array $data): string
    {
        $json = json_encode($data);
        if (!is_string($json)) {
            throw new EncodingError(sprintf('[%u] %s', json_last_error(), json_last_error_msg()));
        }
        return $json;
    }

    protected function decode(string $contents): array
    {
        $data = json_decode($contents, true);
        if (!is_array($data)) {
            throw new EncodingError(sprintf('[%u] %s', json_last_error(), json_last_error_msg()));
        }
        return $data;
    }
}
