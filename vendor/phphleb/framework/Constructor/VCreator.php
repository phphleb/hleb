<?php

declare(strict_types=1);

/*
 * Displaying content from a file.
 *
 * Отображение контента из файла.
 */

namespace Hleb\Constructor;

class VCreator
{
    private $hlTemplatePath = '';

    function __construct(string $includePath) {
        $this->hlTemplatePath = $includePath;
        $data = hleb_to0me1cd6vo7gd_data();
        foreach ($data as $key => $value) {
            if (!in_array($key, ['hlTemplatePath', 'hlTemplateData', 'hlCacheTime'])) {
                $this->$key = $value;
            }
        }
    }

    // Returns the path to the content file.
    // Возвращает путь до файла с контентом.
    /** @return string */
    public function templatePath() {
        return $this->hlTemplatePath;
    }

    // Display content.
    // Отображение контента.
    public function view() {
        extract(hleb_to0me1cd6vo7gd_data());
        require $this->templatePath();
    }
}

