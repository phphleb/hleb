<?php

declare(strict_types=1);

/*
 * Outputting content for a cached template.
 *
 * Вывод контента для кешируемого шаблона.
 */

namespace Hleb\Constructor;

class TCreator
{
    private $hlTemplatePath = '';

    private $hlTemplateData = [];

    private $hlCacheTime = 0;

    function __construct($content, $data = []) {
        $this->hlTemplatePath = $content;
        $this->hlTemplateData = $data;
    }

    /**
     * To set the caching time inside the template.
     *  ~ ... $this->setCacheTime(60); ...
     * @param int $seconds
     */
    /**
     * Устанавливает время кеширования для контента шаблона.
     * ~ ... $this->setCacheTime(60); ...
     * @param int $seconds
     */
    public function setCacheTime(int $seconds) {
        $this->hlCacheTime = $seconds;
    }

    // Assigns route parameters to class variables and properties with content display.
    // Назначает параметры маршрута в переменные и свойства класса с выводом контента.
    /** @return integer */
    public function include() {
        extract($this->hlTemplateData);
        foreach ($this->hlTemplateData as $key => $value) {
            if (!in_array($key, ['hlTemplatePath', 'hlTemplateData', 'hlCacheTime'])) {
                $this->$key = $value;
            }
        }
        require $this->templatePath();;

        return $this->hlCacheTime;
    }

    // Returns the path to the content file.
    // Возвращает путь до файла с контентом.
    /** @return string */
    public function templatePath() {
        return $this->hlTemplatePath;
    }

    // Output the template.
    // Вывод шаблона.
    public function print() {
        return print $this->hlTemplatePath;
    }

    // Return result.
    // Возвращает результат.
    public function toString() {
        ob_start();
        $this->include();
        $result = ob_get_contents();
        ob_end_clean();
        return $result;
    }

}

