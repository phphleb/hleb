<?php

require HLEB_PROJECT_DIRECTORY . '/Main/Insert/DeterminantStaticUncreated.php';

require_once HLEB_PROJECT_DIRECTORY . '/Main/Insert/BaseSingleton.php';

require HLEB_PROJECT_DIRECTORY . '/Main/Info.php';

require HLEB_PROJECT_DIRECTORY . '/Scheme/App/Commands/MainTask.php';

require HLEB_PROJECT_DIRECTORY . '/Scheme/App/Controllers/BaseController.php';

require HLEB_PROJECT_DIRECTORY . '/Scheme/App/Controllers/MainController.php';

require HLEB_PROJECT_DIRECTORY . '/Scheme/App/Middleware/MainMiddleware.php';

require HLEB_PROJECT_DIRECTORY . '/Scheme/App/Models/MainModel.php';

require HLEB_PROJECT_DIRECTORY . '/Scheme/Home/Main/Connector.php';

require HLEB_GLOBAL_DIRECTORY . '/app/Optional/MainConnector.php';

require HLEB_PROJECT_DIRECTORY . '/Main/MainAutoloader.php';

require HLEB_PROJECT_DIRECTORY . '/Main/HomeConnector.php';

// Third party class autoloader.
// Сторонний автозагрузчик классов.
if (file_exists(HLEB_VENDOR_DIRECTORY . '/autoload.php')) {
    require HLEB_VENDOR_DIRECTORY . '/autoload.php';
}

// Custom class autoloader.
// Собственный автозагрузчик классов.
/**
 * @param string $class - class name.
 *
 * @internal
 */
function hl_main_autoloader($class) {
    \Hleb\Main\MainAutoloader::get($class);
}

if (HLEB_PROJECT_CLASSES_AUTOLOAD) spl_autoload_register('hl_main_autoloader', true, true);
