<?php

/*declare(strict_types=1);*/

namespace Hleb\Main\Console\Extreme;

use JetBrains\PhpStorm\NoReturn;

/**
 * Contains possible manipulations with the location address of the terminal.
 *
 * Содержит возможные манипуляции с адресом размещения терминала.
 *
 * @internal
 */
final class ExtremeRequest
{
    /** @internal */
   public static function getUri(): string
   {
       $uri = \explode('?', $_SERVER['REQUEST_URI'] ?? '/');
       return \current($uri);
   }

    /**
     * @throws \AsyncExitException
     * @internal
     */
   #[NoReturn] public static function redirect(string $uri): void
   {
       \async_exit('', 302, ['Location' => $uri]);
   }
}
