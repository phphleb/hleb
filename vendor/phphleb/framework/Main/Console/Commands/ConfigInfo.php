<?php

declare(strict_types=1);

namespace Hleb\Main\Console\Commands;

/**
 * @internal
 */
final class ConfigInfo
{
    /**
     * Returns a list of basic configuration settings, or a setting by name.
     *
     * Возвращает список базовых настроек конфигурации или настройку по названию.
     */
   public function run(array $config, ?string $paramName): false|string
   {
       if ($paramName !== null) {
           $sub = $config['common'][$paramName] ?? null;
           if ($sub !== null) {
               if (\is_bool($sub)) {
                   return ($sub ? '1' : '0') . PHP_EOL;
               }
               if (\is_array($sub)) {
                   return \implode(',', $sub) . PHP_EOL;
               }
               return $sub . PHP_EOL;
           }
           return false;
       }

       // Display the complete list.
       // Вывод полного списка.
       $list = [];
       foreach ($config['common'] as $name => $value) {
           if ($name === 'error.reporting') {
               continue;
           }
           if (\is_bool($value)) {
               $value = $value ? 'true' : 'false';
           }
           if (\is_array($value)) {
               continue;
           }
           $list[] = "$name: $value";
       }

       return \implode(PHP_EOL, $list) . PHP_EOL;
   }
}
