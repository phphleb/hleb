<?php

declare(strict_types=1);

/*
 * Collection and output of data at the end of the page.
 *
 * Сбор и вывод данных в конце страницы.
 */

namespace Hleb\Main\Insert;

final class PageFinisher extends BaseSingleton
{
    protected static $data = null;

    static public function setContent(string $data) {
        self::$data .= $data;
    }

    static public function getContent() {
        return self::$data;
    }

}


