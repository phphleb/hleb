<?php

/*declare(strict_types=1);*/

namespace Hleb\Main\Console;

use Hleb\Main\Console\Extreme\{ExtremeDataTransfer, ExtremeIdentifier, ExtremeRegister, ExtremeTerminal};
use Hleb\Base\RollbackInterface;
use Hleb\Constructor\Attributes\Accessible;
use Hleb\Static\Request;
use Hleb\Static\Settings;

/**
 * A mechanism that allows a console application to act as a WEB site.
 * Attention! The console gives access to the project's internal commands.
 * If you change `bootstrap.php` to `console.php` in the index.php boot file,
 * then the WEB-console will be loaded with the ability to execute console
 * commands of the framework through the browser.
 * This may be needed to install the framework on a hosting
 * where there is no access to console commands.
 *
 * Механизм, позволяющий работать консольному приложению как WEB-сайту.
 * Внимание! Консоль даёт доступ к внутренним командам проекта.
 * Если в загрузочном файле index.php изменить `bootstrap.php` на `console.php`,
 * то будет загружена WEB-консоль c возможностью выполнять
 * консольные команды фреймворка через браузер.
 * Это может понадобиться для установки фреймворка на хостинге,
 * где нет доступа к консольным командам.
 */
#[Accessible]
class WebConsole implements RollbackInterface
{
    private const GET_COMMANDS = ['php console --help', 'php console --list'];

    private readonly array $params;

    private static bool $used = false;

    private array $args = [];

    private bool $isAsync = false;

    public function __construct(?array $params = null)
    {
        $this->params = $params ?? $_GET ?: $_POST ?: $_REQUEST ?: [];
    }

    /**
     * Whether the console was used in Web mode.
     *
     * Была ли консоль использована в режиме Web.
     */
    public static function isUsed(): bool
    {
        return self::$used;
    }

    /**
     * Returns the success status of the terminal loading.
     *
     * Возвращает статус успешности загрузки терминала.
     */
    public function load(): bool
    {
        self::$used = true;
        $this->isAsync = Settings::isAsync();

        $params = $this->params;
        $method = $_SERVER['REQUEST_METHOD'] ?? Request::getMethod();
        if (!$this->isAsync) {
            \session_id() or \session_start();
        }
        if (!$this->register() || !in_array($method, ['GET', 'POST'])) {
            return (new ExtremeRegister())->run();
        }
        $transfer = new ExtremeDataTransfer();
        if ($method === 'POST') {
            if (empty($params)) {
                (new ExtremeIdentifier())->exit();
            }
        }
        if ($method === 'GET' && !empty($params['command'])) {
            if (!in_array($params['command'], self::GET_COMMANDS)) {
                // External links with unresolved commands should not work.
                // Внешние ссылки с неразрешенными командами не должны работать.
                $params = [];
            }
        }
        $transfer->run($params);
        $this->args = $transfer->convertCommand();

        return (new ExtremeTerminal($transfer->singleGetCommand()))->get();
    }

    public function addFooter(string|bool|int $code): string
    {
        $code = \is_string($code) ? \htmlspecialchars($code, ENT_NOQUOTES) : '';

        return $code . '</pre>';
    }

    /**
     * Returns converted arguments from query parameters.
     *
     * Возвращает преобразованные аргументы из параметров запроса.
     */
    public function getArgs(): array
    {
        return $this->args;
    }

    /**
     * Checking the access key to enter the terminal.
     *
     * Проверка ключа доступа для входа в терминал.
     */
    private function register(): bool
    {
        $checker = new ExtremeIdentifier($this->params);
        if ($checker->advance()) {
            return true;
        }
        $_POST = [];
        if ($checker->verification()) {
            $this->isAsync or \session_commit();
            return true;
        }
        return false;
    }

    /**
     * State rollback is called on an asynchronous request.
     *
     * Откат состояния вызывается при асинхронном запросе.
     *
     * @internal
     */
    public static function rollback(): void
    {
        self::$used = false;
    }
}
