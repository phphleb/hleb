<?php

declare(strict_types=1);

namespace Hleb\Constructor\Routes;

use DeterminantStaticUncreated;

class Data
{
    use DeterminantStaticUncreated;

    private static $data = null;

    public static function create_data(array $array)
    {
        if(is_null(self::$data)) self::$data = $array;
    }

    public static function return_data()
    {
        return self::$data ?? [];
    }


}

