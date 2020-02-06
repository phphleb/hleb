<?php

declare(strict_types=1);

namespace Hleb\Constructor\Handlers;

use DeterminantStaticUncreated;

class ProtectedCSRF
{
    use DeterminantStaticUncreated;

    private static $secret_key = null;


    public static function key()
    {
        if (empty(self::$secret_key)) self::$secret_key = md5(session_id() . Key::get());

        return self::$secret_key;

    }


    public static function testPage(array $block)
    {

        // При помощи protect() - имеет преимущество (по последнему)

        $actions = $block['actions'];

        $miss = "";

        foreach ($actions as $key => $action) {

            if (isset($action['protect'])) {
                $miss = $action['protect'][0];
            }
        }

        // При помощи getProtect() (по последнему)

        if ($miss === 'CSRF' || (empty($miss) && isset($block['protect']) &&
                count($block['protect']) && array_reverse($block['protect'])[0] == 'CSRF')) {

            self::blocked();

        }
    }

    public static function blocked()
    {
        $request = $_REQUEST['_token'] ?? '';

        if (!self::test_key($request)) {

            header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');

            die('Protected from CSRF');
        }
    }

    private static function test_key(string $key)
    {

        $secret_key = self::key();

        if (strlen($secret_key) !== strlen($key)) return false;

        $identical = true;

        for ($i = 0; $i < strlen($secret_key); $i++) {

            if ($secret_key[$i] !== $key[$i]) $identical = false;

        }

        return $identical;


    }


}

