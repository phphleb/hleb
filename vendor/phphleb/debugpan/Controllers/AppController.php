<?php

declare(strict_types=1);

namespace Phphleb\Debugpan\Controllers;

use Hleb\Static\Request;
use Hleb\Static\System;

class AppController
{
    use ResponseTrait;

    /**
     * /{key}/app/terminal
     */
    public function actionTerminal(): ?string
    {
        $command = Request::get('query')->toString();

        if ($command === 'help') {
            $content = PHP_EOL .
                ' --help or -h          (displays a list of default console actions)' . PHP_EOL .
                ' --version or -v            (displays the version of the framework)' . PHP_EOL .
                ' --ping            (service health check, returns a constant value)' . PHP_EOL .
                ' --log-level                    (displays the active logging level)' . PHP_EOL .
                ' --routes or -r                            (forms a list of routes)' . PHP_EOL;

            return $this->getSuccessfulResponse(['data' => $content]);
        }

        if ($command === 'ping') {
            return $this->getSuccessfulResponse(['data' => 'PONG']);
        }

        if ($command === 'version') {
            return $this->getSuccessfulResponse(['data' => System::getHlebVersionAsConsoleFormat()]);
        }

        if ($command === 'routes') {
            return $this->getSuccessfulResponse(['data' => System::getRoutesAsConsoleFormat()]);
        }

        if ($command === 'log-level') {
            return $this->getSuccessfulResponse(['data' => System::getActualLogLevel()]);
        }

        return $this->getErrorResponse('The requested command does not exist');
    }

    /**
     * Return the standardized error.
     *
     * Возврат стандартизированной ошибки.
     */
    public function dataNotAvailable(): string
    {
        return $this->getSuccessfulResponse(['data' => '(!) Data is not available if debug mode is disabled or partially enabled.']);
    }
}
