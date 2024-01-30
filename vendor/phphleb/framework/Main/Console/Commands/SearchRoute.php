<?php

declare(strict_types=1);

namespace Hleb\Main\Console\Commands;

use Hleb\HlebBootstrap;
use Hleb\Main\Routes\Search\FindRoute;

/**
 * @internal
 */
final class SearchRoute
{
    use FindRouteTrait;

    private int $code = 0;

    /**
     * Returns the code of the executed command or a default value.
     *
     * Возвращает код выполненной команды или значение по умолчанию.
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * Returns a message about the route address that matches the conditions.
     *
     * Возвращает сообщение об адресе маршрута, который соответствует условиям.
     */
   public function run(null|string $url, null|string $httpMethod, null|string $domain): string
   {
       if ($url === null) {
           return 'Error! Required argument `url` not specified: php console --find-route <url> [method] [domain]' . PHP_EOL;
       }

       [$url, $domain] = $this->splitUrl($url, $domain);

       $block = $this->getBlock($url, $httpMethod, $domain);
       if (\is_string($block)) {
           $this->code = 1;
           return $block;
       }

       return ($block ? 'OK' : 'Not found.') . PHP_EOL;
   }
}
