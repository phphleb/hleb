<?php

declare(strict_types=1);

namespace Hleb\Constructor;

use Hleb\Constructor\Routes\Data;
use Hleb\Main\Errors\ErrorOutput;
use Hleb\Main\TryClass;
use Phphleb\Debugpan\DPanel;
use Hleb\Main\Info;

class Workspace
{
    ///Основная обработка роута

    protected $block;

    protected $map;

    protected $hlDebugInfo = ['time' => [], 'block' => []];

    protected $admFooter;

    protected $controllerForepart = 'App\Controllers\\';

    protected $viewPath = '/resources/views/';

    /**
     * Workspace constructor.
     * @param array $block
     * @param array $map
     */
    function __construct(array $block, array $map)
    {
        $this->block = $block;

        $this->hlDebugInfo['block'] = $block;

        $this->map = $map;

        $this->create($block);
    }


    private function calculateTime($name)
    {
        $num = count($this->hlDebugInfo['time']) + 1;
        $this->hlDebugInfo['time'][$num . ' ' . $name] = round((microtime(true) - HLEB_START), 4);
    }

    private function create($block)
    {
        $this->calculateTime('Loading HLEB');

        $actions = $block['actions'];

        $types = [];

        foreach ($actions as $key => $action) {

            if (isset($action['before'])) {

                $this->allAction($action['before'], 'Before');

                $this->calculateTime('Class <i>' . $action['before'][0] . '</i>');

            }

            if (!empty($action['type'])) { // Определяется тип действия

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

        if($realType === 'options' && implode($types) !== 'options'){

            $types[] = 'options';

            if (!headers_sent()) {
                header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK');
                header('Allow: ' . strtoupper(implode(',' , array_unique($types))));
                header('Content-length: 0');
            }
            exit();
        }

        //Обработчик get()

        $this->renderGetMethod($block);

        $this->calculateTime('Create Project');

        if (HLEB_PROJECT_DEBUG && $_SERVER['REQUEST_METHOD'] == 'GET' &&
            (new TryClass('Phphleb\Debugpan\DPanel'))->is_connect()) {

            DPanel::init($this->hlDebugInfo);
        }

        foreach ($actions as $key => $action) {

            if (isset($action['after'])) {

                $this->allAction($action['after'], 'After');

            }
        }

    }

    private function renderGetMethod($hlExcludedBlock)
    {
        $hlExcludedParams = $hlExcludedBlock['data_params'];

        if (count($hlExcludedParams) === 0) {

            //Загрузка контроллера

            $hlExcludedActions = $hlExcludedBlock['actions'];

            foreach ($hlExcludedActions as $k =>$hlExc) {

               if (isset($hlExc['controller']) || isset($hlExc['adminPanController'])) {

                   $hlExcludedParams =  isset($hlExc['controller']) ? self::getController($hlExc['controller']) :
                            self::getAdminPanController($hlExc['adminPanController'], $hlExcludedBlock);

                    if (is_array($hlExcludedParams)) {
                        if (isset($hlExcludedParams[2]) && $hlExcludedParams[2] == 'render') {
                            // render
                        } else {
                            $hlExcludedParams[0] = [$hlExcludedParams[0]];
                        }
                    } else {

                        echo $hlExcludedParams;
                        if(!empty($this->admFooter)) echo $this->admFooter;
                        return;

                    }
                    break;
                }
            }
        }

        // Создание data() v2'

        if (is_array($hlExcludedParams) && !empty($hlExcludedParams[1])) {

            Data::create_data($hlExcludedParams[1]);
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

        if(!empty($this->admFooter)) echo $this->admFooter;
    }

    private function selectableViewFile(string $file, string $methodType, int $errorNum)
    {
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
            $errorFile = str_replace(str_replace(['\\', '//'], '/',HLEB_GLOBAL_DIRECTORY), "", $hlExcludedFile) . ($extension ? "" : ".php");

            // Search to HL027-VIEW_ERROR or Search to HL037-VIEW_ERROR
            $hlExcludedErrors = 'HL0' . $errorNum . '-VIEW_ERROR: Error in function ' . $methodType . '() ! ' .
                'Missing file `' . $errorFile . '` . ~ ' .
                'Исключение в функции ' . $methodType . '() ! Отсутствует файл `' . $errorFile . '`';

            ErrorOutput::get($hlExcludedErrors);
        }
    }

    private function allAction(array $action, string $type)
    {
        //Вызов класса с методом

        $arguments = $action[1] ?? [];

        $call = explode('@', $action[0]);

        $initiator = 'App\Middleware\\' . $type . '\\' . trim($call[0], '\\');

        $method = $call[1] ?? 'index';

        if(!class_exists($initiator)){
            $hlExcludedErrors = 'HL043-ROUTE_ERROR: Сlass `' . $initiator . '` not exists. ~' .
                ' Класс `' . $initiator . '` не обнаружен.';

            ErrorOutput::get($hlExcludedErrors);
        }

        (new $initiator())->{$method}(...$arguments);

    }


    private function getController(array $action)
    {
        //Вызов controller

        $arguments = $action[1] ?? [];

        $call = explode('@', $action[0]);

        $className = trim($call[0], '\\');

        if(isset($action[2]) && $action[2] == 'module'){

            if(!file_exists(HLEB_GLOBAL_DIRECTORY . "/modules/")){
                $hlExcludedErrors = 'HL044-ROUTE_ERROR: Error in method ->module() ! ' . 'The `/modules` directory is not found, you must create it. ~' .
                    ' Директория `/modules` не обнаружена, необходимо её создать.';

                ErrorOutput::get($hlExcludedErrors);
            }

            $this->controllerForepart = 'Modules\\';

            $searchToModule = explode("/", $className);

            $this->viewPath = "/modules/" . implode("/", array_slice($searchToModule, 0, count($searchToModule) - 1)) . "/";

            $className = implode("\\", array_map('ucfirst', $searchToModule));
        }

        $initiator = $this->controllerForepart . $className;

        $method = $call[1] ?? 'index';

        if(!class_exists($initiator)){
           $hlExcludedErrors = 'HL042-ROUTE_ERROR: Class `' . $initiator . '` not exists. ~' .
                ' Класс  `' . $initiator . '` не обнаружен.';

            ErrorOutput::get($hlExcludedErrors);

            return null;
        }

        return (new $initiator())->{$method}(...$arguments);
    }

    private function getAdminPanController(array $action, $block)
    {
        //Вызов adminPanController

        $arguments = $action[1] ?? [];

        $call = explode('@', $action[0]);

        $initiator = 'App\Controllers\\' . trim($call[0], '\\');

        $method = $call[1] ?? 'index';

        if(!class_exists('Phphleb\Adminpan\MainAdminPanel')){

            ErrorOutput::get('HL030-ADMIN_PANEL_ERROR: Error in method adminPanController() ! ' .
                'Library <a href="https://github.com/phphleb/adminpan">phphleb/adminpan</a> not connected ! ~' .
                'Библиотека <a href="https://github.com/phphleb/adminpan">phphleb/adminpan</a> не подключена !'
            );

            return null;
        }

        $controller = (new $initiator())->{$method}(...$arguments);

        $admObj = new \Phphleb\Adminpan\Add\AdminPanHandler();

        $this->admFooter = $admObj->getFooter();

        echo $admObj->getHeader($block['number'], $block['_AdminPanelData']);

        return $controller;
      }

}


