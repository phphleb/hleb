<?php

declare(strict_types=1);

namespace Hleb\Main\Console\Commands;

use Hleb\HlebBootstrap;
use Hleb\Main\Routes\Search\FindRoute;

/**
 * @internal
 */
trait FindRouteTrait
{
   public function getBlock(null|string $url, null|string $httpMethod, null|string $domain): bool|string|array
   {
       $method = \strtoupper($httpMethod ?: 'get');
       if (!\in_array($method, HlebBootstrap::HTTP_TYPES)) {
           return 'Error! Incorrect HTTP request method specified. Allowed:' .
               \implode(', ', HlebBootstrap::HTTP_TYPES) . PHP_EOL;
       }
       $handler = (new FindRoute($url));
       $search = $handler->one($method, $domain ?: '');
       if ($handler->isBlocked()) {
           return 'Routes are blocked.' . PHP_EOL;
       }
       $errors = $handler->getError();
       if ($errors) {
           return 'Error! ' . \reset($errors) . PHP_EOL;
       }
       if (\is_array($search)) {
           $search['name'] = $handler->getRouteName();
           $search['params'] = $handler->getData();
       }

       return $search;
   }

   protected function splitUrl(string $url, ?string $domain): array
   {
       $parse = \parse_url($url);
       $path = $parse['path'] ?? '/';
       $domain = $parse['host'] ?? $domain;

       return [$path, $domain];
   }
}
