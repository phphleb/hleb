<?php

/*declare(strict_types=1);*/

namespace Hleb\Constructor\Script;

use App\Middlewares\Hlogin\Registrar;
use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Data\SystemSettings;
use Hleb\Helpers\ArrayHelper;
use Hleb\Static\Settings;
use Hleb\Static\System;
use Phphleb\Hlogin\App\Content\AuthDesign;
use Phphleb\Hlogin\App\Content\AuthLang;
use Phphleb\Hlogin\App\Content\ScriptLoader;

#[Accessible]
final class Hlogin
{
    /**
     * Returns the script for initializing registration panels.
     *
     * Возвращает скрипт для инициализации панелей регистрации.
     */
    public static function get(): ?string
    {
        if (self::has()) {
            return ScriptLoader::get(loadMode: Registrar::DEFAULT_PANEL);
        }
        return null;
    }

    /**
     * Checking for the presence of a library with registration.
     *
     * Проверка наличия библиотеки с регистрацией.
     */
    public static function has(): bool
    {
      return SystemSettings::getRealPath('@library/hlogin') && \class_exists(Registrar::class);
    }

    /**
     * Returns a prepared block of text with links if registered.
     *
     * Возвращает подготовленный блок текста с ссылками при наличии регистрации.
     */
    public static function info(): ?string
    {
        if (self::has()) {
            $version = System::getApiVersion();
            $langOptions = [];
            foreach (AuthLang::getAll() as $l) {
                $langOptions[$l] = \str_repeat(' ', 20) . "<option value=\"$l\">$l</option>";
            }
            $langOptions = ArrayHelper::moveToFirst($langOptions, Settings::getAutodetectLang());

            $designOptions = [];
            foreach (AuthDesign::getAll() as $d) {
                if ($d !== 'blank') {
                    $designOptions[$d] = \str_repeat(' ', 20) . "<option value=\"$d\">$d</option>";
                }
            }
            $actual = AuthDesign::getActual();
            if (!in_array($actual, $designOptions)) {
                $actual = 'base';
            }
            $designOptions = ArrayHelper::moveToFirst($designOptions, $actual);

            return '            
            <div align="center" id="hlogin-info-block">
                <img src="/hlresource/hlogin/v' . $version . '/svg/hloginlogo" width="180" height="60" alt="HLOGIN"><br>
                <!-- Links to registration pages -->
            <div class="hlogin-info-selector">
                <a href="/en/login/profile/" id="hlogin-path-link">Registration panel</a> in </span>
                <select id="hlogin-path-selector">' . PHP_EOL . \implode(PHP_EOL, $langOptions) . ' 
                </select>
                <br><br>
                <span>Show panel </span>
                <select id="hlogin-select-action">' . PHP_EOL . \implode(PHP_EOL, $designOptions) . '
                </select>
                <span > type </span>
            </div>' . PHP_EOL;
        }
        return null;
    }
}
