<?php

namespace Hleb\Main\Console\Commands\Deployer;

/**
 * Required methods to deploy/rollback the library.
 *
 * Необходимые методы для развёртывания/отката библиотеки.
 */
interface DeploymentLibInterface
{
    /**
     * @param array $config - configuration for deploying the library.
     *                      - конфигурация для развертывания библиотеки.
     */
    public function __construct(array $config);

    /**
     * Returns the help text for a particular library.
     *
     * Возвращает текст подсказки для развертывания библиотеки.
     */
    public function help(): string|false;

    /**
     * Deploys the necessary files to the project
     * and returns the execution code.
     *
     * Производит развертывание необходимых файлов
     * в проект и возвращает код выполнения.
     */
    public function add(): int;

    /**
     * Rolls back all changes made using the add() method.
     *
     * Откатывает все изменения, произведённые при помощи метода add().
     */
    public function remove(): int;

    /**
     * Returns the list of classes needed to load the library.
     * Return array in the format: classname => realpath
     *
     * Возвращает список классов, необходимый для загрузки библиотеки.
     * Возвращаемый массив в формате: classname => realpath
     */
    public function classmap(): array;

    /**
     * Disable interactive mode for command execution.
     * When disabled, questions should be skipped with default values assigned.
     * 
     * Отключение интерактивного режима для выполнения команды.
     * При отключении вопросы должны пропускаться с присвоением дефолтных значений.
     */
    public function noInteraction(): void;

    /**
     * Disable script output with interactive mode disabled.
     *
     * Отключение вывода скрипта с отключением интерактивного режима.
     */
    public function quiet(): void;
}
