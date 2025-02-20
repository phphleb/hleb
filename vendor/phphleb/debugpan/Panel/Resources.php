<?php

declare(strict_types=1);

namespace Phphleb\Debugpan\Panel;

use Hleb\Helpers\ResourceViewHelper;
use Hleb\HttpMethods\External\SystemRequest;
use Hleb\Static\Settings;
use Phphleb\Debugpan\Controllers\AppController;

final class Resources
{
    private const  ALLOWED_EXT = ['css', 'js', 'svg'];

    private const  ALLOWED_NAMES = [
        'debugpanstyle',
        'debugpanbutton',
        'debugpanscript',
        'debugpantemplate',
        'debugpanterminal',
        'info',
    ];

    private const ALLOWED_CONTROLLERS = ['App', 'State'];

    public function __construct(private readonly SystemRequest $request)
    {
    }

    /**
     * Returns the result of a system query to the library.
     *
     * Возвращает результат вызова системного запроса к библиотеке.
     */
    public function get(): bool
    {
        $address = \explode('/', \trim($this->request->getUri()->getPath(), '/'));

        $part = \array_pop($address);
        $ext = \array_pop($address);
        $controller = \end($address);

        if ($controller === 'controller') {
            $class = \ucfirst($ext);
            if (!\in_array($class, self::ALLOWED_CONTROLLERS)) {
                return false;
            }
            $method = 'action' . \ucfirst($part);
            $file = Settings::getRealPath('@library/debugpan/Controllers/' . $class . 'Controller.php');
            if (!$file) {
                return false;
            }
            $class = "Phphleb\Debugpan\Controllers\\{$class}Controller";
            if (!\class_exists($class, false)) {
                require $file;
            }
            // Debug mode can only be partially enabled, in which case a warning is sent.
            // Режим отладки может быть включен только частично, в этом случае отсылается предупреждение.
            if ($class === AppController::class && !Settings::isDebug()) {
                echo (new AppController())->dataNotAvailable();
                return true;
            }
            $initiator = new $class;
            if (!\method_exists($initiator, $method)) {
                return false;
            }
            echo (new $class)->$method() ?? null;

            return true;
        }

        if (!\in_array($ext, self::ALLOWED_EXT) || !\in_array($part, self::ALLOWED_NAMES)) {
            return false;
        }

        $file = Settings::getRealPath('@library/debugpan/web/' . $ext . '/' . $part . '.' . $ext);
        if (!$file) {
            return false;
        }

        return (new ResourceViewHelper())->add($file);
    }
}
