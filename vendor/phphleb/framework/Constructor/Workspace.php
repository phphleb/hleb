<?php

declare(strict_types=1);

/*
 * Basic route processing.
 *
 * Основная обработка роута.
 */

namespace Hleb\Constructor;

use Hleb\Constructor\Handlers\Request;
use Hleb\Constructor\Routes\Data;
use Hleb\Main\Errors\ErrorOutput;
use Hleb\Main\TryClass;
use Phphleb\Debugpan\DPanel;
use Hleb\Main\Info;

final class Workspace
{
    protected $map;

    protected $debugTime = [];

    protected $admFooter = null;

    protected $controllerForepart = 'App\Controllers\\';

    protected $viewPath = '/resources/views/';

    public function __construct(array $block, array $map) {
        $this->map = $map;
        $this->create($block);
    }

    // Calculate the execution time for the debug panel.
    // Расчёт времени выполнения для панели отладки.
    private function calculateTime($name) {
        $num = count($this->debugTime) + 1;
        if(defined('HLEB_START')) {
            $this->debugTime[$num . ' ' . $name] = round((microtime(true) - HLEB_START), 4);
        }
    }

    // Parse the accompanying actions for the route and display them.
    // Разбор сопутствующих действий для роута и их вывод.
    private function create($block) {
        $this->calculateTime('Loading HLEB');
        $actions = $block['actions'];
        $types = [];
        foreach ($actions as $key => $action) {
            if (isset($action['before'])) {
                $beforeParams = $this->allAction($action['before'], 'Before');
                if ($beforeParams === false) {
                    hl_preliminary_exit();
                }
                if (is_array($beforeParams)) {
                    if (isset($beforeParams[HLEB_TAG_INTERNAL])) {
                        ErrorOutput::get('HL055-MIDDLEWARE_ERROR: Warning in the ' . ($action['before'][0] ?? 'undefined') .
                            '. From a preliminary class (middleware), the return of the view() and render() functions is not implied.' . '~' .
                            ' Из предваритеьного класса (middleware) возврат функций view() и render() не подразумевается.');
                    }
                    $this->addJsonData($beforeParams);
                }
                if ($beforeParams !== null) {
                    ErrorOutput::get('HL053-MIDDLEWARE_ERROR: Warning in the ' . ($action['before'][0] ?? 'undefined') .
                        '. A value of an unsupported type was returned (possible without returning, false or an array).' . '~' .
                        ' Возвращено значение не поддерживаемого типа (можно без возвращения, false или массив).');
                }
                $this->calculateTime('Class <i>' . $action['before'][0] . '</i>');
            }
            if (!empty($action['type'])) {
                $actionTypes = $action['type'];
                foreach ($actionTypes as $k => $actionType) {
                    $types[] = $actionType;
                }
            }
        }
        if (count($types) === 0) {
            $types = !empty($block['type']) ? $block['type'] : [];
        }
        if (count($types) === 0) {
            $types = ['get'];
        }
        $realType = strtolower($_SERVER['REQUEST_METHOD']);
        if ($realType === 'options' && implode($types) !== 'options') {
            $types[] = 'options';
            if (!headers_sent()) {
                http_response_code (200);
                header('Allow: ' . strtoupper(implode(',', array_unique($types))));
                header('Content-length: 0');
            }
            // End of script execution before starting the main project.
            hl_preliminary_exit();
        }
        $this->renderGetMethod($block);
        $this->calculateTime('Create Project');
        if (HLEB_PROJECT_DEBUG_ON &&
            (new TryClass('Phphleb\Debugpan\DPanel'))->is_connect()) {
            DPanel::init(['time' => $this->debugTime, 'block' => $block]);
        }
        foreach ($actions as $action) {
            if (isset($action['after'])) {
                if ($this->allAction($action['after'], 'After') === false) {
                    hl_preliminary_exit();
                }
            }
        }
    }

    // Parse and display the main action for the route.
    // Разбор и вывод основного действия для роута.
    private function renderGetMethod($block) {
        $params = $block['data_params'];
        if (count($params) === 0) {
            $actions = $block['actions'];
            foreach ($actions as $action) {
                if (isset($action['controller']) || isset($action['adminPanController'])) {
                    $params = $isController = isset($action['controller']) ? $this->getController($action['controller']) :
                        $this->getAdminPanController($action['adminPanController'], $block);
                    if ($params === false) {
                        hl_preliminary_exit();
                    }
                    if (is_object($params)) {
                        ErrorOutput::get('HL054-CONTROLLER_ERROR: Warning in the ' . ($action['controller'][0] ?? 'undefined') .
                            '. Returned value of unsupported type (object).' . '~' .
                            ' Возвращено значение не поддерживаемого типа (объект).');
                    }
                    if (is_array($params)) {
                        if (isset($params[2]) && $params[2] == 'render') {
                            // render
                        } else {
                            $params[0] = [$params[0]];
                        }
                    } else {
                        echo $params;
                        if (!empty($this->admFooter)) echo $this->admFooter;
                        return;
                    }
                    break;
                }
            }
        }
        // data()
        if (is_array($params)) {
            if (!empty($params[1])) {
                Data::createData($params[1]);
            }
            if (isset($params['text']) && is_string($params['text'])) {
                echo $params['text'];
            } else if (isset($params[2]) && $params[2] == 'views') {
                //  view(...)
                $this->selectableViewFile($params[0][0], 'view', 37);
            } else if (isset($params[2]) && $params[2] == 'render') {
                // render(...)
                $allMaps = $params[0];
                Info::add('RenderMap', $allMaps);
                foreach ($allMaps as $originMap) {
                    $select = 0;
                    foreach ($this->map as $key => $initMaps) {
                        if ($key === $originMap) {
                            if (empty($key) || $key[0] !== '#' || strpos($key, ' ') !== false) {
                                ErrorOutput::get('HL052-RENDER_ERROR: Warning in the "renderMap()" method. The set name must begin with a "#" character and not contain spaces. For example "#Main_map".' . '~' .
                                    ' В методе renderMap() первый символ названия должен быть "#", а само название не содержать пробелов. Образец "#Main_map".');
                            }
                            $select++;
                            foreach ($initMaps as $initPage) {
                                $this->selectableViewFile($initPage, 'render', 27);
                            }
                        }
                    }
                    if (!$select) {
                        $this->selectableViewFile($originMap, 'render', 27);
                    }
                }
            } elseif (!empty($isController) && is_array($params) && !isset($params[HLEB_TAG_INTERNAL])) {
                $this->addJsonData($params);
            }
        }
        if (!empty($this->admFooter)) echo $this->admFooter;
    }

    // Implements the difference in the output of the standard and Twig template engines.
    // Реализует разницу в выводе стандартного и Twig шаблонизаторов.
    private function selectableViewFile(string $file, string $methodType, int $errorNum) {
        // View error 404
        if ($methodType === 'view' && trim($file) === '404') {
            hleb_page_404();
        }

        $extension = false;
        $file = trim($file, '\/ ');
        $fileParts = explode('/', $file);
        $proFile = str_replace(['\\', '//'], '/', HLEB_GLOBAL_DIRECTORY . $this->viewPath . $file);
        // twig file
        if (file_exists($proFile . '.php')) {
            $proFile .= '.php';
        } else {
            $extension = strripos(end($fileParts), ".") !== false;
        }
        if (file_exists($proFile)) {
            if (!HL_TWIG_CONNECTED && $extension) {
                $ext = strip_tags(array_reverse(explode(".", $file))[0]);
                ErrorOutput::get('HL041-VIEW_ERROR: The file has the `.' . $ext . '` extension and is not processed.' .
                    ' Probably the TWIG template engine is not connected.' . '~' .
                    ' Файл имеет расширение `.' . $ext . '`. и не обработан. Вероятно не подключён шаблонизатор TWIG.');
            } else {
                // view file
                $extension ? (new TwigCreator())->view($file) : (new VCreator($proFile))->view();
            }
        } else {
            $errorFile = str_replace(str_replace(['\\', '//'], '/', HLEB_GLOBAL_DIRECTORY), "", $proFile) . ($extension ? '' : '.php');
            // Search to HL027-VIEW_ERROR or Search to HL037-VIEW_ERROR
            ErrorOutput::get('HL0' . $errorNum . '-VIEW_ERROR: Error in function ' . $methodType . '() ! ' .
                'Missing file `' . $errorFile . '` . ~ ' .
                'Исключение в функции ' . $methodType . '() ! Отсутствует файл `' . $errorFile . '`');
        }
    }

    // Handle intermediaries.
    // Обработка посредников.
    private function allAction(array $action, string $type) {
        // Call the class with the method
        //Вызов класса с методом.
        $arguments = $action[1] ?? [];
        $call = explode('@', $action[0]);
        $className = trim($call[0], '\\');
        $method = $call[1] ?? 'index';

        $initiator = 'App\Middleware\\' . $type . '\\' . $className;

        if (!class_exists($initiator)) {
            ErrorOutput::get('HL043-ROUTE_ERROR: Сlass `' . $initiator . '` not exists. ~' .
                ' Класс `' . $initiator . '` не обнаружен.');
        }

        $initiatorObject = new $initiator;

        if (!is_callable([$initiatorObject, $method])) {
            ErrorOutput::get('HL048-ROUTE_ERROR: Method `' . $method . '` in class `' . $initiator . '` not exists. ~' .
                ' Метод  `' . $method . '` для класса `' . $initiator . '` не обнаружен.');
            return null;
        }

        return $initiatorObject->{$method}(...$arguments);
    }

    // Returns the result of executing the method on the triggered controller class.
    // Возвращает результат выполнения метода в инициированном классе контроллера.
    private function getController(array $action) {
        $arguments = $action[1] ?? [];
        $call = explode('@', $action[0]);
        $className = trim($call[0], '\\');
        $method = $call[1] ?? 'index';

        $searchTags = strip_tags($className . $method) !== $className . $method;

        if($searchTags) {
            $searchTags = $this->getCalledClassAndMethodConverter($className, $method);
            $className = $searchTags['class'];
            $method = $searchTags['method'];
        }

        if (isset($action[2]) && $action[2] == 'module') {
            defined('HLEB_OPTIONAL_MODULE_SELECTION') or define('HLEB_OPTIONAL_MODULE_SELECTION', file_exists(HLEB_GLOBAL_DIRECTORY . "/modules/"));

            if (!HLEB_OPTIONAL_MODULE_SELECTION) {
                ErrorOutput::get('HL044-ROUTE_ERROR: Error in method ->module() ! ' . 'The `/modules` directory is not found, you must create it. ~' .
                    ' Директория `/modules` не обнаружена, необходимо её создать.');
            }
            $this->controllerForepart = 'Modules\\';
            $searchToModule = explode("/", trim($className, '/\\'));
            if (count($searchToModule) && !defined('HLEB_MODULE_NAME')) {
                define('HLEB_MODULE_NAME', $searchToModule[0]);
            }
            $this->viewPath = '/modules/' . implode('/', array_slice($searchToModule, 0, count($searchToModule) - 1)) . '/';
            $className = implode("\\", array_map('ucfirst', $searchToModule));
        }

        $initiator = $this->controllerForepart . $className;

        if (!class_exists($initiator)) {
            if (!$searchTags) {
                $errors = 'HL047-ROUTE_ERROR: Class `' . $initiator . '` not exists. ~' .
                    ' Класс  `' . $initiator . '` не обнаружен.';
                ErrorOutput::get($errors);
                return null;
            } else {
                hleb_page_404();
            }
        }

        $initiatorObject = new $initiator;

        if (!is_callable([$initiatorObject, $method])) {
            if (!$searchTags) {
                $errors = 'HL042-ROUTE_ERROR: Method `' . $method . '` in class `' . $initiator . '` not exists. ~' .
                    ' Метод  `' . $method . '` для класса `' . $initiator . '` не обнаружен.';
                ErrorOutput::get($errors);
                return null;
            } else {
                hleb_page_404();
            }
        }

        return $initiatorObject->{$method}(...$arguments);
    }

    // Returns the initiated controller class for the admin panel.
    // Возвращает инициированный класс контроллера для админпанели.
    private function getAdminPanController(array $action, $block) {
        // Проверка подключения админ панели
        if (!class_exists('Phphleb\Adminpan\MainAdminPanel')) {
            ErrorOutput::get('HL030-ADMIN_PANEL_ERROR: Error in method adminPanController() ! ' .
                'Library <a href="https://github.com/phphleb/adminpan">phphleb/adminpan</a> not connected ! ~' .
                'Библиотека <a href="https://github.com/phphleb/adminpan">phphleb/adminpan</a> не подключена !'
            );
            return null;
        }

        $arguments = $action[1] ?? [];
        $call = explode('@', $action[0]);
        $className = trim($call[0], '\\');
        $method = $call[1] ?? 'index';

        $initiator = 'App\Controllers\\' . $className;

        if (!class_exists($initiator)) {
            $errors = 'HL046-ROUTE_ERROR: Class `' . $initiator . '` from `AdminPanController` not exists. ~' .
                ' Класс  `' . $initiator . '` для `AdminPanController` не обнаружен.';
            ErrorOutput::get($errors);
            return null;
        }

        $initiatorObject = new $initiator;

        if (!is_callable([$initiatorObject, $method])) {
            $errors = 'HL049-ROUTE_ERROR: Method `' . $method . '` in class `' . $initiator . '` not exists. ~' .
                ' Метод  `' . $method . '` для класса `' . $initiator . '` не обнаружен.';
            ErrorOutput::get($errors);
            return null;
        }

        $controller = $initiatorObject->{$method}(...$arguments);
        if ($controller === false) {
            return false;
        }
        $admObj = new \Phphleb\Adminpan\Add\AdminPanHandler();
        $this->admFooter = $admObj->getFooter();
        echo $admObj->getHeader($block['number'], $block['_AdminPanelData']);

        return $controller;
    }

    // Поиск вариативных названий в тегах класса и метода
    private function getCalledClassAndMethodConverter(string $controllerName, string $methodName) {
        $params = Request::get();

        $data = ['class' => $controllerName, 'method' => $methodName];
        if (empty($params)) {
            ErrorOutput::get('HL051-ROUTE_SYNTAX_ERROR: Invalid controller call syntax ! ' .
                'Parameters are not specified in the route address for replacement in the controller `' . htmlentities($controllerName . '@' . $methodName) . '`. ~' .
                'Не указаны параметры в адресе роута для замещения в контроллере `' . htmlentities($controllerName . '@' . $methodName) . '`'
            );
            return $data;
        }
        $fullTarget = $controllerName . '@' . $methodName;

        foreach ($params as $key => $value) {
            if($key === $methodName) {
                $fullTarget = str_replace('<' . $key . '>', strval($value), $fullTarget);
            } else {
                $reformatValue = $this->reformatValue(strval($value));
                $fullTarget = $reformatValue !== false ? str_replace('<' . $key . '>', $reformatValue, $fullTarget) : '@';
            }
        }
        $fullTargetList = explode('@', $fullTarget);

        // Если Неправильный формат и есть другие теги
        if (count($fullTargetList) !== 2 || strip_tags($fullTarget) !== $fullTarget) {
            ErrorOutput::get('HL045-ROUTE_SYNTAX_ERROR: Invalid controller call syntax ! ' .
                'Incorrectly set controller name `' . htmlentities($controllerName . '@' . $methodName) . '` or method substitution &ltname&gt;@&lt;method&gt;. ~' .
                'Неправильно задано замещение  &ltname&gt;@&lt;method&gt; имени `' . htmlentities($controllerName . '@' . $methodName) . '` или метода контроллера.'
            );
            return $data;
        }
        return ['class' => $fullTargetList[0], 'method' => $fullTargetList[1]];
    }

    // Преобразует значение в camelСase наименование
    private function reformatValue(string $value) {
        $parts = explode('-', $value);
        $result = '';
        foreach($parts as $part) {
            if($part == '') {
                return false;
            }
            $result .= ucfirst($part);
        }
        return $result;
    }

    // Вывод массива в json-формате.
    private function addJsonData(array $params) {
        headers_sent() or header("Content-Type: application/json");
        $json = json_encode($params);
        if ($json === false) {
            http_response_code(500);
            $json = '{"error": "json_encode"}';
        }
        hl_preliminary_exit($json);
    }
}


