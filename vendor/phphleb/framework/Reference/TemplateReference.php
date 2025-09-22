<?php

/*declare(strict_types=1);*/

namespace Hleb\Reference;

use App\Bootstrap\BaseContainer;
use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\AvailableAsParent;
use Hleb\Constructor\Data\DebugAnalytics;
use Hleb\Constructor\Data\DynamicParams;
use Hleb\Constructor\Data\SystemSettings;
use Hleb\Constructor\Templates\Template;
use Hleb\CoreProcessException;
use Hleb\Main\Insert\ContainerUniqueItem;
use Hleb\Static\Cache;
use Hleb\Static\Settings;
use App\Bootstrap\ContainerInterface;

#[Accessible] #[AvailableAsParent]
class TemplateReference extends ContainerUniqueItem implements TemplateInterface, Interface\Template
{
    /** @inheritDoc */
    #[\Override]
    public function get(string $viewPath, array $extractParams = [], array $config = []): string
    {
        if (Settings::isDebug()) {
            DebugAnalytics::addData(DebugAnalytics::INSERT_TEMPLATE, ['name' => 'content']);
        }
        \ob_start();
        $this->insert($viewPath, $extractParams, $config);

        return (string)\ob_get_clean();
    }

    /** @inheritDoc */
    #[\Override]
    public function insert(string $viewPath, array $extractParams = [], array $config = []): void
    {
        unset($extractParams['viewPath'], $extractParams['container']);
        $module = DynamicParams::getModuleName();
        $moduleType = $module ? Settings::getParam('main', 'module.view.type') : null;
        $viewDir = $moduleType === 'closed' ? '@modules/' . $module . '/views' : '@views';
        if ($moduleType === 'closed' && $viewPath === 'error' && !SystemSettings::getRealPath($viewDir . '/error.php')) {
            $viewDir = '@views';
        }
        $viewPath = $viewDir . '/' . $viewPath;

        $hlStartTemplateTime = \microtime(true);
        if (\is_object($config['container'] ?? null)) {
            $container = \is_a($config['container'], ContainerInterface::class) ? $config['container'] : null;
        } else {
            $container = BaseContainer::instance();
        }
        if (\str_ends_with($viewPath, '.twig')) {
            $dirs = [];
            $module and $dirs['modules'] = SystemSettings::getRealPath('modules');
            $dirs['views'] = SystemSettings::getRealPath($viewDir);
            (new Template(Template::TWIG))
                ->setData($extractParams)
                ->setPath($viewPath)
                ->setCachePath(SystemSettings::getRealPath('storage') . '/cache/twig/compilation')
                ->setViewPaths($dirs)
                ->setRootPath(SystemSettings::getRealPath('global'))
                ->setInvertedPath((array)SystemSettings::getValue('common', 'twig.cache.inverted'))
                ->setRealPath(SystemSettings::getRealPath($viewPath))
                ->setContainer($container)
                ->view();
        } else {
            unset($module, $dirs, $config);
            \extract($extractParams);
            /**
             * Additional variables that will be active in the template.
             *
             * Дополнительные переменные, которые будут активны в шаблоне.
             *
             * @param $viewPath
             * @param $hlStartTemplateTime
             * @var $container - container for templates.
             *                 - контейнер для шаблонов.
             *
             * @var $extractParams - initial data set.
             *                     - первоначальный массив данных.
             *
             */
            require SystemSettings::getRealPath("$viewPath.php") ?: SystemSettings::getRealPath($viewPath);
        }
    }

    /** @inheritDoc */
    #[\Override]
    public function insertCache(string $viewPath, array $extractParams = [], int $sec = Cache::DEFAULT_TIME, array $config = []): void
    {
        $hlStartTemplateTime = \microtime(true);
        $activeModule = Settings::getModuleName();
        $key = $activeModule . '@' . $viewPath . ':' . __FUNCTION__ . ':' . \sha1(\json_encode($extractParams, JSON_THROW_ON_ERROR));
        $cache = Cache::get($key);

        if ($cache) {
            echo $cache;

            $path = $activeModule ? '@modules/' . $activeModule . '/views/' . $viewPath : "@views/$viewPath";
            if (Settings::isDebug()) {
                $exp = Cache::getExpire($key) ?: $sec;
                $module = $activeModule ? "[$activeModule]" : '';
                DebugAnalytics::addData(
                    DebugAnalytics::INSERT_TEMPLATE,
                    ['name' => "cached.template:{$module}{$exp}/{$sec}sec", 'path' => $path, 'ms' => \round(\microtime(true) - $hlStartTemplateTime, 5) * 1000]
                );
            }
            return;
        }
        $data = $this->get($viewPath, $extractParams, $config);
        if (!Cache::set($key, $data, $sec)) {
            throw new CoreProcessException('Failed to save cache for template ' . $viewPath);
        }

        echo $data;
    }
}
