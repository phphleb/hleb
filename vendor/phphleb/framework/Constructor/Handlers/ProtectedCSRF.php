<?php

declare(strict_types=1);

/*
 * Built-in protection against CSRF attacks in one class.
 *
 * Встроенная защита от CSRF-атак в одном классе.
 */

namespace Hleb\Constructor\Handlers;

use Hleb\Main\Insert\BaseSingleton;

final class ProtectedCSRF extends BaseSingleton
{
    private static $secretKey = null;

    // Returns the public security key.
    // Возвращает публичный защитный ключ.
    public static function key() {
        if (empty(self::$secretKey)) self::$secretKey = md5(session_id() . Key::get());
        return self::$secretKey;
    }

    // Checking the data of the current route for the set check parameters.
    // Проверка данных текущего роута на установленные параметры защиты.
    public static function testPage(array $block) {

        // With protect() - takes precedence (last)
        // При помощи protect() - имеет преимущество (по последнему)
        $actions = $block['actions'];
        $miss = "";
        foreach ($actions as $key => $action) {
            if (isset($action['protect'])) {
                $miss = $action['protect'][0];
            }
        }

        // With getProtect() (last)
        // При помощи getProtect() (по последнему)
        if (($miss === 'CSRF' || (empty($miss)) && isset($block['protect']) &&
                count($block['protect']) && array_reverse($block['protect'])[0] == 'CSRF')) {
            self::blocked();
        }
    }

    // Uncompromising interruption of page output.
    // Бескомпромиссное прерывание вывода страницы.
    public static function blocked() {
        $request = $_REQUEST['_token'] ?? '';
        if (!self::checkKey($request)) {
            http_response_code (403);
            // End of script execution before starting the main project.
            hl_preliminary_exit('Protected from CSRF');
        }
    }

    // Safe key verification.
    // Безопасная проверка ключа.
    private static function checkKey(string $key) {
        $secretKey = self::key();
        if (strlen($secretKey) !== strlen($key)) return false;
        $identical = true;
        for ($i = 0; $i < strlen($secretKey); $i++) {
            if ($secretKey[$i] !== $key[$i]) $identical = false;
        }
        return $identical;
    }
}

