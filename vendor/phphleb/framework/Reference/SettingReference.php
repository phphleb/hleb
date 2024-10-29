<?php

/*declare(strict_types=1);*/

namespace Hleb\Reference;

use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\AvailableAsParent;
use Hleb\Constructor\Data\DynamicParams;
use Hleb\Constructor\Data\SystemSettings;
use Hleb\Main\Insert\ContainerUniqueItem;
use Hleb\Static\Request;
use Hleb\Static\Session;

/**
 *
 * Provides methods for accessing the framework's system settings.
 * Wrapper class over:
 *
 * Предоставляет методы для обращения к системным параметрам фреймворка.
 * Класс-обёртка над:
 *
 * @see SystemSettings
 * @see DynamicParams
 */
#[Accessible] #[AvailableAsParent]
class SettingReference extends ContainerUniqueItem implements SettingInterface, Interface\Setting
{
    /** @inheritDoc */
    #[\Override]
    public function isStandardMode(): bool
    {
        return SystemSettings::isStandardMode();
    }

    /** @inheritDoc */
    #[\Override]
    public function isAsync(): bool
    {
        return SystemSettings::isAsync();
    }

    /** @inheritDoc */
    #[\Override]
    public function isCli(): bool
    {
        return SystemSettings::isCli();
    }

    /** @inheritDoc */
    #[\Override]
    public function isDebug(): bool
    {
        return DynamicParams::isDebug();
    }

    /** @inheritDoc */
    #[\Override]
    public function getRealPath(string $keyOrPath): false|string
    {
        return SystemSettings::getRealPath($keyOrPath);
    }

    /** @inheritDoc */
    #[\Override]
    public function getPath(string $keyOrPath): false|string
    {
        return SystemSettings::getPath($keyOrPath);
    }

    /** @inheritDoc */
    #[\Override]
    public function isEndingUrl(): bool
    {
        return DynamicParams::isEndingUrl();
    }

    /** @inheritDoc */
    #[\Override]
    public function getParam(string $name, string $key): mixed
    {
        return SystemSettings::getValue($name, $key);
    }

    /** @inheritDoc */
    #[\Override]
    public function common(string $key): mixed
    {
        return $this->getParam('common', $key);
    }

    /** @inheritDoc */
    #[\Override]
    public function main(string $key): mixed
    {
        return $this->getParam('main', $key);
    }

    /** @inheritDoc */
    #[\Override]
    public function database(string $key): mixed
    {
        return $this->getParam('database', $key);
    }

    /** @inheritDoc */
    #[\Override]
    public function system(string $key): mixed
    {
        return $this->getParam('system', $key);
    }

    /** @inheritDoc */
    #[\Override]
    public function getModuleName(): ?string
    {
        return DynamicParams::getModuleName();
    }

    /** @inheritDoc */
    #[\Override]
    public function getControllerMethodName(): ?string
    {
        return DynamicParams::getControllerMethodName();
    }

    /** @inheritDoc */
    #[\Override]
    public function getDefaultLang(): string
    {
        return SystemSettings::getValue('main', 'default.lang');
    }

    /** @inheritDoc */
    #[\Override]
    public function getAutodetectLang(): string
    {
        $allowed = self::getAllowedLanguages();

        $search = static function ($lang) use ($allowed): bool {
            return $lang && \in_array(\strtolower($lang), $allowed);
        };

        if ($search($lang = \explode('/', \trim(Request::getUri()->getPath(), '/'))[0])) {
            return $lang;
        }
        if ($search($lang = Request::param('lang')->value)) {
            return $lang;
        }
        if ($search($lang = Request::get('lang')->value)) {
            return $lang;
        }
        if ($search($lang = Request::post('lang')->value)) {
            return $lang;
        }
        if ($search($lang = Session::get('LANG'))) {
            return $lang;
        }

        return $this->getDefaultLang();
    }

    /** @inheritDoc */
    #[\Override]
    public function getAllowedLanguages(): array
    {
        return $this->getParam('main', 'allowed.languages');
    }


    /** @inheritDoc */
    #[\Override]
    public function getInitialRequest(): object
    {
        return DynamicParams::getDynamicOriginRequest();
    }
}
