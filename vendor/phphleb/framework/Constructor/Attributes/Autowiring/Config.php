<?php

declare(strict_types=1);

namespace Hleb\Constructor\Attributes\Autowiring;

/**
 * Provides the ability to set the required value for auto-substitution
 * from the framework configuration by type (name) of the configuration and key.
 * Can be used to automatically resolve dependencies.
 *
 * Предоставляет возможность задать необходимое значение для авто-подстановки
 * из конфигурации фреймворка по типу(названию) конфигурации и ключа.
 * Может быть применен для автоматического разрешения зависимостей.
 */
#[\Attribute(\Attribute::TARGET_PARAMETER)]
readonly class Config
{
    /**
     * Setting up a substitution for the Dependency Injection parameter.
     *
     * Установка подмены для Dependency Injection параметра.
     *
     *  ```php
     *  class ExampleController extends Controller
     *  {
     *      public function index(
     *          #[Config('main', 'default.lang')]
     *          string $defaultLanguage,
     *      ) {
     *        //...//
     *      }
     *  }
     * ```
     *
     * @param string $name - the name of the configuration type, for example 'common'.
     *                     - название типа конфигурации, например 'common'.
     *
     * @param string $key - key to get the value.
     *                    - ключ для получения значения.
     */
    public function __construct(public string $name, public string $key)
    {
    }
}
