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
    protected static $data = '';

    protected static $dynamicData = [];

    protected static $showTypes = [];

    static public function setContent(string $data) {
        self::$data .= $data;
    }

    /**
     * @return string
     */
    static public function getContent() {
        foreach(self::$dynamicData as $key => $data) {
            if(self::$showTypes[$key]) {
                self::$data .= $data;
            }
        }
        return self::$data;
    }

    static public function setDynamicContent(string $name, string $data) {
        self::$showTypes[$name] = true;
        self::$dynamicData[$name] = $data;
    }

    /**
     * @param string $name
     * @return boolean|null
     */
    static public function getVisibleType(string $name) {
        return self::$showTypes[$name] ?? null;
    }

    static public function setVisibleType(string $name, bool $isShow) {
        self::$showTypes[$name] = $isShow;
    }

}


