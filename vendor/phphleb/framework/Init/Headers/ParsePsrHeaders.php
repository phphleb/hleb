<?php

/*declare(strict_types=1);*/

namespace Hleb\Init\Headers;

/**
 * Parsing headers from a PSR-7 Request object.
 *
 * Разбор заголовков из объекта PSR-7 Request.
 */
class ParsePsrHeaders
{
    public function update(mixed $headers): array
    {
        if (empty($headers)) {
            return [];
        }
        $headers = (array)$headers;

        foreach ($headers as $n => $i) {
            // If the list contains duplicate headers by name.
            // Если список содержит дубликаты заголовков по названию.
            if ($i && \is_array($i)) {
                $headers[$n] = \trim(\implode(',', $i));
            }
        }
        foreach ($headers as $name => $header) {
            if (!\is_array($header)) {
                $items = [];
                $header = \trim((string)$header);
                foreach (\explode(',', $header) as $p) {
                    $r = \trim($p);
                    if (!\in_array($r, $items)) {
                        $items[] = $r;
                    }
                }
                $headers[$name] = $items;
            }
        }
        return $headers;
    }
}
