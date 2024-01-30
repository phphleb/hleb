<?php

/*declare(strict_types=1);*/

/**
 * Outputting native CSS resources of the framework.
 *
 * Вывод собственных CSS-ресурсов фреймворка.
 */

use Hleb\Constructor\Data\SystemSettings;
use Hleb\HttpMethods\External\SystemRequest;
use Hleb\Static\Response;

/**
 * @var SystemRequest $request
 */
$address = \explode('/', \trim($request->getUri()->getPath(), '/'));
$name = \end($address);

if (\in_array($name, ['error', 'default'])) {
    $file = SystemSettings::getRealPath('@library/framework/web/css/' . $name . '.css');

    if (!$file) {
        return false;
    }
    Response::addHeaders([
        'Content-Type' => 'text/css; charset=UTF-8',
    ]);
} else if ($name === 'logo') {
    $file = SystemSettings::getRealPath('@library/framework/web/svg/' . $name . '.svg');

    if (!$file) {
        return false;
    }
    Response::addHeaders([
        'Content-Type' => 'image/svg+xml; charset=UTF-8',
    ]);
} else {
    return false;
}
Response::addHeaders([
    'Cache-Control' => 'public, max-age=31536000',
    'Pragma' => 'cache'
]);
isset($file) and Response::setBody(\file_get_contents($file));

return true;
