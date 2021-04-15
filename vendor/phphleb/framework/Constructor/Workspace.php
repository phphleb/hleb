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
    protected $block;

    protected $map;

    protected $hlDebugInfo = ['time' => [], 'block' => []];

    protected $admFooter;

    protected $controllerForepart = 'App\Controllers\\';

    protected $viewPath = '/resources/views/';

    public function __construct(array $block, array $map) {
        $this->block = $block;
        $this->hlDebugInfo['block'] = $block;
        $this->map = $map;
        $this->create($block);
    }

    // Calculate the execution time for the debug panel.
    // Расчёт времени выполнения для панели отладки.
    private function calculateTime($name) {
        $num = count($this->hlDebugInfo['time']) + 1;
        if(defined('HLEB_START')) {
            $this->hlDebugInfo['time'][$num . ' ' . $name] = round((microtime(true) - HLEB_START), 4);
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
                $this->allAction($action['before'], 'Before');
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
        if (HLEB_PROJECT_DEBUG_ON && $_SERVER['REQUEST_METHOD'] == 'GET' &&
            (new TryClass('Phphleb\Debugpan\DPanel'))->is_connect()) {
            DPanel::init($this->hlDebugInfo);
        }
        foreach ($actions as $key => $action) {
            if (isset($action['after'])) {
                $this->allAction($action['after'], 'After');
            }
        }
    }

    // Parse and display the main action for the route.
    // Разбор и вывод основного действия для роута.
    private function renderGetMethod($hlExcludedBlock) {
        $hlExcludedParams = $hlExcludedBlock['data_params'];
        if (count($hlExcludedParams) === 0) {
            $hlExcludedActions = $hlExcludedBlock['actions'];
            foreach ($hlExcludedActions as $k => $hlExc) {
                if (isset($hlExc['controller']) || isset($hlExc['adminPanController'])) {
                    $hlExcludedParams = isset($hlExc['controller']) ? $this->getController($hlExc['controller']) :
                        $this->getAdminPanController($hlExc['adminPanController'], $hlExcludedBlock);
                    if (is_array($hlExcludedParams)) {
                        if (isset($hlExcludedParams[2]) && $hlExcludedParams[2] == 'render') {
                            // render
                        } else {
                            $hlExcludedParams[0] = [$hlExcludedParams[0]];
                        }
                    } else {
                        echo $hlExcludedParams;
                        if (!empty($this->admFooter)) echo $this->admFooter;
                        return;
                    }
                    break;
                }
            }
        }
        // data()
        if (is_array($hlExcludedParams) && !empty($hlExcludedParams[1])) {
            Data::createData($hlExcludedParams[1]);
        }
        if (isset($hlExcludedParams['text']) && is_string($hlExcludedParams['text'])) {
            echo $hlExcludedParams['text'];
        } else if (isset($hlExcludedParams[2]) && $hlExcludedParams[2] == 'views') {
            //  view(...)
            $this->selectableViewFile($hlExcludedParams[0][0], 'view', 37);
        } else if (isset($hlExcludedParams[2]) && $hlExcludedParams[2] == 'render') {
            // render(...)
            $hlExcludedParamsMaps = $hlExcludedParams[0];
            Info::add('RenderMap', $hlExcludedParamsMaps);
            foreach ($hlExcludedParamsMaps as $hlExcludedParamsMap) {
                foreach ($this->map as $hlExcludedKey => $hlExcludedMaps) {
                    if ($hlExcludedKey == $hlExcludedParamsMap) {
                        foreach ($hlExcludedMaps as $hlExcluded_map) {
                            $this->selectableViewFile($hlExcluded_map, 'render', 27);
                        }
                    }
                }
            }
        }
        if (!empty($this->admFooter)) echo $this->admFooter;
    }

    // Implements the difference in the output of the standard and Twig template engines.
    // Реализует разницу в выводе стандартного и Twig шаблонизаторов.
    private function selectableViewFile(string $file, string $methodType, int $errorNum) {
        // View error 404
        if ($methodType === 'view' && trim($file) === '404') {
            hleb_bt3e3gl60pg8h71e00jep901_error_404();
        }

        $extension = false;
        $hlFile = trim($file, '\/ ');
        $hlFileParts = explode("/", $hlFile);
        $hlExcludedFile = str_replace(['\\', '//'], '/', HLEB_GLOBAL_DIRECTORY . $this->viewPath . $hlFile);
        // twig file
        if (file_exists($hlExcludedFile . ".php")) {
            $hlExcludedFile .= '.php';
        } else {
            $extension = strripos(end($hlFileParts), ".") !== false;
        }
        if (file_exists($hlExcludedFile)) {
            if (!HL_TWIG_CONNECTED && $extension) {
                $hlExt = strip_tags(array_reverse(explode(".", $hlFile))[0]);
                $hlExcludedErrors = 'HL041-VIEW_ERROR: The file has the `.' . $hlExt . '` extension and is not processed.' .
                    ' Probably the TWIG template engine is not connected.' . '~' .
                    ' Файл имеет расширение `.' . $hlExt . '`. и не обработан. Вероятно не подключён шаблонизатор TWIG.';
                ErrorOutput::get($hlExcludedErrors);
            } else {
                // view file
                $extension ? (new TwigCreator())->view($hlFile) : (new VCreator($hlExcludedFile))->view();
            }
        } else {
            $errorFile = str_replace(str_replace(['\\', '//'], '/', HLEB_GLOBAL_DIRECTORY), "", $hlExcludedFile) . ($extension ? "" : ".php");
            // Search to HL027-VIEW_ERROR or Search to HL037-VIEW_ERROR
            $hlExcludedErrors = 'HL0' . $errorNum . '-VIEW_ERROR: Error in function ' . $methodType . '() ! ' .
                'Missing file `' . $errorFile . '` . ~ ' .
                'Исключение в функции ' . $methodType . '() ! Отсутствует файл `' . $errorFile . '`';
            ErrorOutput::get($hlExcludedErrors);
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
            $hlExcludedErrors = 'HL043-ROUTE_ERROR: Сlass `' . $initiator . '` not exists. ~' .
                ' Класс `' . $initiator . '` не обнаружен.';
            ErrorOutput::get($hlExcludedErrors);
        }

        $initiatorObject = new $initiator;

        if (!is_callable([$initiatorObject, $method])) {
            $hlExcludedErrors = 'HL048-ROUTE_ERROR: Method `' . $method . '` in class `' . $initiator . '` not exists. ~' .
                ' Метод  `' . $method . '` для класса `' . $initiator . '` не обнаружен.';
            ErrorOutput::get($hlExcludedErrors);
            return null;
        }

        $initiatorObject->{$method}(...$arguments);
    }

    // Returns the initiated controller class.
    // Возвращает инициированный класс контроллера.
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
            if (!defined('HLEB_OPTIONAL_MODULE_SELECTION')) {
                define('HLEB_OPTIONAL_MODULE_SELECTION', file_exists(HLEB_GLOBAL_DIRECTORY . "/modules/"));
            }
            if (!HLEB_OPTIONAL_MODULE_SELECTION) {
                $hlExcludedErrors = 'HL044-ROUTE_ERROR: Error in method ->module() ! ' . 'The `/modules` directory is not found, you must create it. ~' .
                    ' Директория `/modules` не обнаружена, необходимо её создать.';
                ErrorOutput::get($hlExcludedErrors);
            }
            $this->controllerForepart = 'Modules\\';
            $searchToModule = explode("/", trim($className, '/\\'));
            if (count($searchToModule) && !defined('HLEB_MODULE_NAME')) {
                define('HLEB_MODULE_NAME', $searchToModule[0]);
            }
            $this->viewPath = "/modules/" . implode("/", array_slice($searchToModule, 0, count($searchToModule) - 1)) . "/";
            $className = implode("\\", array_map('ucfirst', $searchToModule));
        }

        $initiator = $this->controllerForepart . $className;

        if (!class_exists($initiator)) {
            if (!$searchTags) {
                $hlExcludedErrors = 'HL047-ROUTE_ERROR: Class `' . $initiator . '` not exists. ~' .
                    ' Класс  `' . $initiator . '` не обнаружен.';
                ErrorOutput::get($hlExcludedErrors);
                return null;
            } else {
                hleb_bt3e3gl60pg8h71e00jep901_error_404();
            }
        }

        $initiatorObject = new $initiator;

        if (!is_callable([$initiatorObject, $method])) {
            if (!$searchTags) {
                $hlExcludedErrors = 'HL042-ROUTE_ERROR: Method `' . $method . '` in class `' . $initiator . '` not exists. ~' .
                    ' Метод  `' . $method . '` для класса `' . $initiator . '` не обнаружен.';
                ErrorOutput::get($hlExcludedErrors);
                return null;
            } else {
                hleb_bt3e3gl60pg8h71e00jep901_error_404();
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
            $hlExcludedErrors = 'HL046-ROUTE_ERROR: Class `' . $initiator . '` from `AdminPanController` not exists. ~' .
                ' Класс  `' . $initiator . '` для `AdminPanController` не обнаружен.';
            ErrorOutput::get($hlExcludedErrors);
            return null;
        }

        $initiatorObject = new $initiator;

        if (!is_callable([$initiatorObject, $method])) {
            $hlExcludedErrors = 'HL049-ROUTE_ERROR: Method `' . $method . '` in class `' . $initiator . '` not exists. ~' .
                ' Метод  `' . $method . '` для класса `' . $initiator . '` не обнаружен.';
            ErrorOutput::get($hlExcludedErrors);
            return null;
        }

        $controller = $initiatorObject->{$method}(...$arguments);
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
}


