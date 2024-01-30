<?php

declare(strict_types=1);

namespace Hleb\Main\Routes\Prepare;

/**
 * @internal
 */
class Defender
{
    /**
     * So that the output to the cache does not break due to invalid characters,
     * the final data must be processed.
     *
     * Чтобы вывод в кеш не поломался из-за недопустимых символов,
     * конечные данные необходимо обработать.
     */
    public function handle(array &$data): void
    {
        \array_walk_recursive($data, static function (&$value) {
            if (\is_string($value)) {
                if (\str_contains($value, "'") || \str_ends_with($value, '\\')) {
                    $value = \addcslashes($value, "'\\");
                }
            }
        });
    }
}
