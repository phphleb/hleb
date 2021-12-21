<?php

declare(strict_types=1);

/*
 * Outputting content for a cached template.
 *
 * Вывод контента для кешируемого шаблона.
 */

namespace Hleb\Constructor;

#[AllowDynamicProperties]
final class TCreator
{
    /** @internal */
    private $hlTemplatePath = '';

    /** @internal */
    private $hlTemplateData = [];

    /** @internal */
    private $hlCacheTime = 0;

    /** @internal */
    public function __construct($content, $data = []) {
        $this->hlTemplatePath = $content;
        $this->hlTemplateData = $data;
    }

    /**
     * To set the caching time inside the template.
     *
     * Устанавливает время кеширования для контента шаблона.
     *
     * ~ ... $this->setCacheTime(60); ...
     * @param int $seconds
     *
     * @internal
     */
    public function setCacheTime(int $seconds) {
        $this->hlCacheTime =  $seconds;
    }

    // Assigns route parameters to class variables and properties with content display.
    // Назначает параметры маршрута в переменные и свойства класса с выводом контента.
    /**
     * @return integer
     *
     * @internal
     */
    public function include() {
        extract($this->hlTemplateData);
        foreach ($this->hlTemplateData as $key => $value) {
            if (!in_array($key, ['hlTemplatePath', 'hlTemplateData', 'hlCacheTime'])) {
                $this->$key = $value;
            }
        }
        require $this->templatePath();

        return  !defined('HLEB_TEMPLATE_CACHE') || (defined('HLEB_TEMPLATE_CACHE') && HLEB_TEMPLATE_CACHE) ? $this->hlCacheTime : 0;
    }

    // Returns the path to the content file.
    // Возвращает путь до файла с контентом.
    /**
     * @return string
     *
     * @internal
     */
    public function templatePath() {
        return $this->hlTemplatePath;
    }

    // Output the template.
    // Ввывод шаблона.
    /** @internal */
    public function print() {
        return print $this->hlTemplatePath;
    }

    // Return result.
    // Возвращает результат.
    /** @internal */
    public function getString() {
        ob_start();
        $this->include();
        $result = ob_get_contents();
        ob_end_clean();
        return $result;
    }

    // Output the template.
    // Отображение шаблона.
    /** @internal */
    public function __toString() {
        return strval($this->hlTemplatePath);
    }

}

