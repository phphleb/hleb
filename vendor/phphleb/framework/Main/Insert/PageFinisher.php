<?php

declare(strict_types=1);

namespace Hleb\Main\Insert;

class PageFinisher
{
    use \DeterminantStaticUncreated;

    protected static $data = null;

    static public function setContent(string $data) {
            self::$data .= $data;
    }

    static public function getContent() {
        return self::$data;
    }

}


