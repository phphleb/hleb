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

    private static function test_key(string $key)
    {

        $secret_key = self::key();

        if (strlen($secret_key) !== strlen($key)) return false;

        $identical = true;

        for ($i = 0; $i < strlen($secret_key); $i++) {

            if ($secret_key{$i} !== $key{$i}) $identical = false;

        }

        return $identical;


    }

    public static function testPage(array $block)
    {

        // При помощи protect() - имеет преимущество

        $actions = $block['actions'];

        $miss = false;

        foreach ($actions as $action) {

            if (isset($action['protect'])) {

                if (in_array("CSRF", $action['protect'])) {

                    self::blocked();

                }

                $miss = true;

            }
        }

        // При помощи getProtect()

        if (!$miss && isset($block['protect']) && in_array("CSRF", $block['protect'])) {

            self::blocked();

        }
    }

    public static function blocked()
    {
        $request = $_REQUEST['_token'] ?? "";

        if (!self::test_key($request)) {

            header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden");

            die("Protected from CSRF");
        }
    }


}