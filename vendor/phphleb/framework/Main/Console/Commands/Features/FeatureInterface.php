<?php

namespace Hleb\Main\Console\Commands\Features;

interface FeatureInterface
{
    public function run(array $argv): string|false;

    /**
     * Returns a brief description of the purpose of the application.
     *
     * Возвращает краткое описание предназначения приложения.
     */
    public static function getDescription(): string;

    /**
     * Returns the success code of execution.
     *
     * Возвращает код успешности выполнения.
     */
    public function getCode(): int;
}
