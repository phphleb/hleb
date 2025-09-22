<?php

/*declare(strict_types=1);*/

namespace Hleb\Init\Headers;

class ParseSwooleHeaders
{
    public function update(mixed $headers): array
    {
        if (empty($headers)) {
            return [];
        }
        $type = 0;
        foreach ((array)$headers as $name => $header) {
            if (is_int($name) && is_string($header)) {
                $parts = explode(':', $header);
                if (count($parts) > 1) {
                    $name = array_shift($parts);
                    $header = explode(',', implode(':', $parts));
                    $type = 1;
                } else {
                    throw new \InvalidArgumentException('Failed to parse headers.');
                }
            } else if ($type === 1) {
                throw new \InvalidArgumentException('Wrong headers format.');
            }
            if (is_string($name) && is_string($header)) {
                $header = explode(',', $header);
            }
            $headers[$name] = $header;
        }

        return $headers;
    }
}
