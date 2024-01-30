<?php
/**
 * @author  Foma Tuturov <fomiash@yandex.ru>
 */

declare(strict_types=1);

namespace Hleb\Base;

use Hleb\Constructor\Attributes\AvailableAsParent;
use Hleb\Constructor\Data\SystemSettings;

/**
 * The base class for displaying auxiliary resources of the generated page.
 * All controllers obtained via the route's page() method
 * must inherit from this class.
 * (!) Assigned data gets to the page unchanged, so if it comes from user input,
 * then it needs to be checked.
 *
 * Базовый класс для вывода вспомогательных ресурсов генерируемой страницы.
 * Все контроллеры, полученные через метод page() маршрута
 * должны быть унаследованы от этого класса.
 * (!) Назначаемые данные попадают на страницу в неизменном виде,
 * поэтому, если они попадают из пользовательского ввода, их нужно проверить.
 */
#[AvailableAsParent]
abstract class PageController extends Controller
{
    /**
     * The theme color in HEX format, for example `#FFD4DD`.
     *
     * Цвет темы в формате HEX, например `#FFD4DD`.
     */
    public ?string $themeColor = null;

    /**
     * Value for meta name=viewport <meta name=viewport content=... >
     * or null if not used.
     *
     * Значение для meta name=viewport <meta name=viewport content=... >
     * или null, если не используется.
     */
    public ?string $viewportContent = 'width=device-width, initial-scale=1.0';

    /**
     * Title for meta information in the head of the page.
     *
     * Заголовок для мета-информации в head страницы.
     */
    public ?string $title = null;

    /**
     * The language value for the page, such as 'en',
     * must be allowed in the configuration.
     *
     * Значение языка для страницы, например 'en',
     * должен быть разрешён в конфигурации.
     */
    public ?string $language = null;

    /**
     * Description for the meta information in the head of the page.
     *
     * Описание для мета-информации в head страницы.
     */
    public ?string $description = null;

    /**
     * Link for page icon.
     *
     * Ссылка для значка страницы.
     */
    public ?string $faviconUri = '/favicon.ico';

    /**
     * Link to the logo to display in the panel menu.
     * Recommended size 230x55px in PNG, JPG or SVG format.
     *
     * Ссылка на логотип для вывода в меню панели.
     * Рекомендуемый размер 230x55px в формате PNG, JPG или SVG.
     */
    public ?string $logoUri = null;

    /**
     * Links to CSS resources to add.
     * An example of adding a link from a controller:
     *
     * Ссылки на добавляемые ресурсы CSS.
     * Пример добавления ссылки из контроллера:
     *
     * $this->cssResources[] = '/css/main.css';
     */
    public array $cssResources = [];

    /**
     * Links to JS resources to add.
     * An example of adding a link from a controller:
     *
     * Ссылки на добавляемые ресурсы JS.
     * Пример добавления ссылки из контроллера:
     *
     * $this->cssResources[] = '/js/main.js';
     */
    public array $jsResources = [];

    /**
     * Adding an arbitrary line to the head of the page, for example:
     *
     * Добавление произвольной строки в head страницы, например:
     *
     * $this->metaRows[] = '<meta name="author" content="Kant I." />';
     */
    public array $metaRows = [];

    /**
     * Display arbitrary HTML at the top of the page.
     * Maximum block sizes: 380px/50px
     *
     * Вывод произвольного HTML в верхней части страницы.
     * Максимальные размеры блока: 380px/50px
     */
    public string $showcaseCenterHtml = '<!-- Showcase center -->';

    /**
     * Custom HTML output at the top right of the page.
     * Maximum block sizes: 200px/50px
     *
     * Вывод произвольного HTML в верхней правой части страницы.
     * Максимальные размеры блока: 200px/50px
     */
    public string $showcaseRightHtml = '<!-- Showcase right -->';

    /**
     * System getting a list of settings.
     *
     * Системное получение списка настроек.
     *
     * @internal
     */
    final public function getHeadData(): array
    {
        return [
            'themeColor' => $this->themeColor,
            'title' => $this->title,
            'description' => $this->description,
            'faviconUri' => $this->faviconUri,
            'logoUri' => $this->logoUri,
            'lang' => $this->language,
            'viewportContent' => $this->viewportContent,
            'cssResources' => \array_values($this->cssResources),
            'jsResources' => \array_values($this->jsResources),
            'metaRows' => \array_values($this->metaRows),
            'showcaseRight' => $this->showcaseRightHtml,
            'showcaseCenter' => $this->showcaseCenterHtml,
        ];
    }

    /**
     * Determines if inherited controllers can be accessed
     * if the user is not an administrator (in any case).
     * The rule is also relevant in the absence of
     * the hlogin registration library, in this case,
     * the pages will be blocked.
     * Configurable in the framework configuration.
     *
     * Определяет, может ли быть доступ к унаследованным контроллерам
     * если пользователь не является администратором (в любом случае).
     * Правило актуально и при отсутствии библиотеки регистрации hlogin,
     * в таком случае страницы будут заблокированы.
     * Настраивается в конфигурации фреймворка.
     *
     * @internal
     */
    final public function getExternalAccess(): bool
    {
        if (!SystemSettings::getSystemValue('page.external.access')) {
            if (!SystemSettings::getRealPath('@library/hlogin')) {
                return false;
            }
            if (!\Phphleb\Hlogin\App\RegType::check(\Phphleb\Hlogin\App\RegType::REGISTERED_ADMIN, '>=')) {
                return false;
            }
        }
        return true;
    }
}
