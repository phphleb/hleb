<?php

/*declare(strict_types=1);*/

namespace Hleb\Main\Console;

use Hleb\Constructor\Attributes\Accessible;
use Hleb\CoreException;
use Hleb\Static\Response;

#[Accessible]
final class WebConsoleOnPage extends WebConsole
{
    /**
     * Connects all the necessary methods to place
     * the Web console on the page.
     * You can display the WEB console on a specific
     * page of the site using the following code:
     *
     * Соединяет все необходимые методы для размещения
     * Web-консоли на странице.
     * Отобразить WEB-консоль на определенной странице
     * сайта можно при помощи следующего кода:
     *
     * /routes/map.php:
     * Route::match(['get', 'post'], '/web-console', view('console'));
     *
     * /resources/views/console.php:
     * <?php
     * (new \Hleb\Main\Console\WebConsoleOnPage())->run();
     *
     *
     * @throws CoreException
     */
    public function run(): void
    {
        Response::addHeaders(['Content-Type' => 'text/html; charset=utf-8']);
        ob_start();
        $console = '';
        $result = $this->load();
        $arguments = $this->getArgs();
        $arguments[] = '--strict-verbosity';
        $result and $console = (new ConsoleHandler($arguments))->run();
        $content = ob_get_clean();
        Response::addToBody($content . $this->addFooter($console));
    }
}
