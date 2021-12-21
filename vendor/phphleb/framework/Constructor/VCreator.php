<?php

declare(strict_types=1);

/*
 * Displaying content from a file.
 *
 * Отображение контента из файла.
 */

namespace Hleb\Constructor;

#[AllowDynamicProperties]
final class VCreator
{
    /** @internal */
    private $hlTemplatePath = '';

    /**
     * @param string $includePath
     *
     * @internal
     */
    public function __construct(string $includePath) {
        $this->hlTemplatePath = $includePath;
        $data = hleb_data();
        foreach ($data as $key => $value) {
            if (!in_array($key, ['hlTemplatePath', 'hlTemplateData', 'hlCacheTime'])) {
                $this->$key = $value;
            }
        }
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

    // Display content.
    // Отображение контента.
    /** @internal */
    public function view() {
        extract($this->getData());
        require $this->templatePath();
    }

    // Getting variables.
    // Получение переменных.
    /** @internal */
    private function getData() {
        $data = hleb_data();
        return is_array($data) ? $data : [];
    }
}

