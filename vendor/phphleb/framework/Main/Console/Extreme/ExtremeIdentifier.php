<?php

/*declare(strict_types=1);*/

namespace Hleb\Main\Console\Extreme;

use Hleb\Constructor\Data\SystemSettings;
use Hleb\Helpers\Abracadabra;
use Hleb\Static\Session;
use JetBrains\PhpStorm\NoReturn;

/**
 * Protection of the Web terminal from unauthorized access.
 *
 * Защита Веб-терминала от доступа посторонних.
 *
 * @internal
 */
final class ExtremeIdentifier
{
    final public const KEY_PATH = 'storage/keys/web-console.key';

    final public const KEY_NAME = 'HLEB_WEB_CONSOLE';

    final public const KEY_ON = 'HLEB_WEB_CONSOLE_ON';

    private bool $isAsync = false;

    public function __construct(private readonly array $regData = [])
    {
        $this->isAsync = SystemSettings::isAsync();
    }

    /**
     * Fast access check based on a parameter in the session.
     *
     * Быстрая проверка доступа на основе параметра в сессии.
     */
    public function advance(): bool
    {
        $double = false;
        if ($this->isAsync) {
            $double = (bool)Session::get(self::KEY_ON);
        }
        return !empty($_SESSION[self::KEY_ON]) || $double;
    }

    /**
     * Exit from all project terminals.
     *
     * Выход из всех терминалов проекта.
     *
     * @throws \AsyncExitException
     */
    #[NoReturn] public function exit(): void
    {
        if ($this->isAsync) {
            Session::clear();
        } else {
            \session_destroy();
        }
        ExtremeRequest::redirect(ExtremeRequest::getUri());
    }

    /**
     * Basic access check with the result entered into the session.
     *
     * Основная проверка доступа с занесением результата в сессию.
     */
    public function verification(): bool
    {
        // Protection from brute force.
        // Защита от brute force.
        $regularUpdate = \mt_rand(0, 100) === 1;

        if (!$regularUpdate && $this->advance()) {
            return true;
        }
        $createKey = $this->getKeyOrCreate($regularUpdate);
        $key = \trim($this->regData[self::KEY_NAME] ?? '');
        if (!$key) {
            return false;
        }
        $check = $createKey === $key;

        if ($check) {
            if ($this->isAsync) {
                Session::set(self::KEY_ON, 1);
            }
            $_SESSION[self::KEY_ON] = 1;
        }

        return $check;
    }

    /**
     * Generate and save a verification key in the absence or force.
     *
     * Генерация и сохранение проверочного ключа при отсутствии или принудительно.
     */
    private function getKeyOrCreate(bool $force): string
    {
        $file = SystemSettings::getPath('@' . self::KEY_PATH);
        if (!\file_exists($file) || $force) {
            \file_put_contents($file, Abracadabra::generate(72));
        }
        $key = \file_get_contents($file);
        if (!$key) {
            throw new \RuntimeException("Failed to save key in " . self::KEY_PATH);
        }

        return $key;
    }
}
