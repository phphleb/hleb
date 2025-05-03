<?php

/*declare(strict_types=1);*/

namespace Hleb;

use Hleb\Constructor\Attributes\NotFinal;
use Hleb\Static\Log;

#[NotFinal]
abstract class AsyncRouteException extends \AsyncExitException implements CoreException
{
    final public const HL00_ERROR = 'HL00_ERROR';

    final public const HL01_ERROR = 'HL01_ERROR';

    final public const HL02_ERROR = 'HL02_ERROR';

    final public const HL03_ERROR = 'HL03_ERROR';

    final public const HL04_ERROR = 'HL04_ERROR';

    final public const HL05_ERROR = 'HL05_ERROR';

    final public const HL06_ERROR = 'HL06_ERROR';

    final public const HL07_ERROR = 'HL07_ERROR';

    final public const HL08_ERROR = 'HL08_ERROR';

    final public const HL09_ERROR = 'HL09_ERROR';

    final public const HL10_ERROR = 'HL10_ERROR';

    final public const HL11_ERROR = 'HL11_ERROR';

    final public const HL12_ERROR = 'HL12_ERROR';

    final public const HL13_ERROR = 'HL13_ERROR';

    final public const HL14_ERROR = 'HL14_ERROR';

    final public const HL15_ERROR = 'HL15_ERROR';

    final public const HL16_ERROR = 'HL16_ERROR';

    final public const HL17_ERROR = 'HL17_ERROR';

    final public const HL18_ERROR = 'HL18_ERROR';

    final public const HL19_ERROR = 'HL19_ERROR';

    final public const HL20_ERROR = 'HL20_ERROR';

    final public const HL21_ERROR = 'HL21_ERROR';

    final public const HL22_ERROR = 'HL22_ERROR';

    final public const HL23_ERROR = 'HL23_ERROR';

    final public const HL24_ERROR = 'HL24_ERROR';

    final public const HL25_ERROR = 'HL25_ERROR';

    final public const HL26_ERROR = 'HL26_ERROR';

    final public const HL27_ERROR = 'HL27_ERROR';

    final public const HL28_ERROR = 'HL28_ERROR';

    final public const HL29_ERROR = 'HL29_ERROR';

    final public const HL30_ERROR = 'HL30_ERROR';

    final public const HL31_ERROR = 'HL31_ERROR';

    final public const HL32_ERROR = 'HL32_ERROR';

    final public const HL33_ERROR = 'HL33_ERROR';

    final public const HL34_ERROR = 'HL34_ERROR';

    final public const HL35_ERROR = 'HL35_ERROR';

    final public const HL36_ERROR = 'HL36_ERROR';

    final public const HL37_ERROR = 'HL37_ERROR';

    final public const HL38_ERROR = 'HL38_ERROR';

    final public const HL39_ERROR = 'HL39_ERROR';

    final public const HL40_ERROR = 'HL40_ERROR';

    final public const HL41_ERROR = 'HL41_ERROR';

    private const ALL = [
        self::HL00_ERROR => [
            'en' => 'Sample error output number %value%',
            'ru' => 'Образец вывода ошибки номер %value%'
        ],
        self::HL01_ERROR => [
            'en' => 'No write permission! Failed to save file to folder `/storage/*`.  You need to change the web server permissions in this folder.',
            'ru' => 'Не удалось сохранить кэш!  Ошибка при записи файла в папку `/storage/*`. Необходимо расширить права веб-сервера для этой папки и вложений.'
        ],
        self::HL02_ERROR => [
            'en' => 'Route compilation error. The number of open and closed group tags in routes does not match.',
            'ru' => 'Ошибка составления маршрутов. Не совпадает количество открытых и закрытых тегов групп в маршрутах.'
        ],
        self::HL03_ERROR => [
            'en' => 'Route compilation error. The end tag endGroup was not found for the route group.',
            'ru' => 'Ошибка составления маршрутов. Не обнаружен завершающий тег endGroup для группы маршрутов.'
        ],
        self::HL04_ERROR => [
            'en' => 'Route compilation error. In the %method%() method, content is already passed as the second parameter, so the controller is invalid.',
            'ru' => 'Ошибка составления маршрутов. В методе %method%() контент уже передан как второй параметр, поэтому %controller%() недействителен.'
        ],
        self::HL05_ERROR => [
            'en' => 'Route compilation error. The keys of the where() method can only be strings and match the substituted value in the route.',
            'ru' => 'Ошибка составления маршрутов. Ключи метода where() могут быть только строковыми и соответствовать подставляемому значению в маршруте.'
        ],
        self::HL06_ERROR => [
            'en' => 'Route compilation error. In the where() method of a single route, the keys must not be repeated and must match the values being replaced.',
            'ru' => 'Ошибка составления маршрутов. В методе where() одного маршрута ключи не должны повторятся и должны соответствовать заменяемым значениям.'
        ],
        self::HL07_ERROR => [
            'en' => 'Route compilation error. In the where() method, the values passed must be valid regular expressions.',
            'ru' => 'Ошибка составления маршрутов. В методе where() переданные значения должны быть валидными регулярными выражениями.'
        ],
        self::HL08_ERROR => [
            'en' => 'Route compilation error. The number of open and closed characters `{` and `}` must match for a valid insertion of a dynamic value.',
            'ru' => 'Ошибка составления маршрутов. Количество открытых и закрытых символов `{` и `}` должно совпадать для валидной вставки динамического значения.'
        ],
        self::HL09_ERROR => [
            'en' => 'Route compilation error. Incorrect position of `{` and `}` characters for valid dynamic value insertion.',
            'ru' => 'Ошибка составления маршрутов. Некорректное положение символов `{` и `}` для правильной вставки динамического значения.'
        ],
        self::HL10_ERROR => [
            'en' => 'Route compilation error. Incorrect position of the `?` character to indicate the dynamic end of the address.',
            'ru' => 'Ошибка составления маршрутов. Некорректное положение символа `?` для обозначения динамического окончания адреса.'
        ],
        self::HL11_ERROR => [
            'en' => 'Route compilation error. The number of controllers for the %method%() route cannot be more than one.',
            'ru' => 'Ошибка составления маршрутов. Количество контроллеров у маршрута %method%() не может быть больше одного.'
        ],
        self::HL12_ERROR => [
            'en' => 'Route compilation error. No content assigned to %method%() method. It can be assigned using the controller or in the method itself as the second argument.',
            'ru' => 'Ошибка составления маршрутов. Не назначен контент для метода %method%(). Он может быть назначен при помощи контроллера или в самом методе вторым аргументом.'
        ],
        self::HL13_ERROR => [
            'en' => 'Route compilation error. HTTP method type not supported or specified incorrectly, available: %types%',
            'ru' => 'Ошибка составления маршрутов. Тип HTTP-метода не поддерживается или указан неправильно, доступны: %types%.'
        ],
        self::HL14_ERROR => [
            'en' => 'Route compilation error. When assigning HTTP methods to fallback(), they must not be repeated in different uses of fallback().',
            'ru' => 'Ошибка составления маршрутов. При назначении HTTP-методов  в fallback() они не должны повторяться в различных применениях fallback().'
        ],
        self::HL15_ERROR => [
            'en' => 'There is no class along the path: %path%.',
            'ru' => 'Отсутствует назначенный класс по пути: %path%.'
        ],
        self::HL16_ERROR => [
            'en' => 'Route compilation error. There is no designated template for the %method%() method along the path: %path%.',
            'ru' => 'Ошибка составления маршрутов. Отсутствует назначенный шаблон для метода %method%() по пути: %path%.'
        ],
        self::HL17_ERROR => [
            'en' => 'Route compilation error. Invalid combination of characters (two periods in a row) in the route address. If it is a variable route, use an ellipsis at the beginning of the condition of the last part of it.',
            'ru' => 'Ошибка составления маршрутов. Недопустимое сочетание символов (две точки подряд) в адресе маршрута. Если это вариативный маршрут, используйте троеточие в начале условия последней его части.'
        ],
        self::HL18_ERROR => [
            'en' => 'Route compilation error. Using both dynamic `/{example?}/` and variadic type `/...number/` in the same route is not allowed.',
            'ru' => 'Ошибка составления маршрутов. Использование одновременно динамического `/{example?}/` и вариативного типа `/...number/` в одном маршруте не допускается.'
        ],
        self::HL19_ERROR => [
            'en' => 'Route compilation error. The variable route is incorrectly located or transmitted in the wrong format. It must be located at the end of the route and assigned once.',
            'ru' => 'Ошибка составления маршрутов. Неправильно расположен вариативный маршрут или передан в неправильном формате. Он должен находиться в конечной части маршрута и назначаться один раз.'
        ],
        self::HL20_ERROR => [
            'en' => 'Route compilation error. The name() `%name%` for the %method%() route must be set once and contain only latin characters and numbers in any case. There may also be dashes and dots.',
            'ru' => 'Ошибка составления маршрутов. Название name() `%name%` для %method%() маршрута должно быть установлено один раз и содержать только латинские символы и цифры в любом регистре. Возможно еще наличие тире и точки.'
        ],
        self::HL21_ERROR => [
            'en' => 'Route compilation error. Controller class `%class%` or callable method `%method%` was not found in it.',
            'ru' => 'Ошибка составления маршрутов. Класс контроллера `%class%` или вызываемый метод `%method%` в нём не обнаружен.'
        ],
        self::HL22_ERROR => [
            'en' => 'Route compilation error. The name of the domain() method is incorrect: `%name%`. Make sure the value is in lower case.',
            'ru' => 'Ошибка составления маршрутов. Для метода domain() неправильно задано название: `%name%`. Убедитесь, что значение в нижнем регистре.'
        ],
        self::HL23_ERROR => [
            'en' => 'Route compilation error. The regular expression `%name%` was specified incorrectly for the domain() method.',
            'ru' => 'Ошибка составления маршрутов. Неправильно задано регулярное выражение `%name%` для метода domain().'
        ],
        self::HL24_ERROR => [
            'en' => 'Route compilation error. The level for the domain() method is incorrectly set.',
            'ru' => 'Ошибка составления маршрутов. Неправильно задан уровень для метода domain().'
        ],
        self::HL25_ERROR => [
            'en' => 'Route compilation error. In the controller class `%class%` the method called `%method%` does not match the given arguments: `%cells%`',
            'ru' => 'Ошибка составления маршрутов. В классе контроллера `%class%` вызываемый метод `%method%` следующие аргументы не соответствуют заданным: `%cells%`'
        ],
        self::HL26_ERROR => [
            'en' => 'Route compilation error. In the controller class `%class%` the called method `%method%` has incorrect arguments.',
            'ru' => 'Ошибка составления маршрутов. В классе контроллера `%class%` вызываемый метод `%method%` неправильно заданы аргументы.'
        ],
        self::HL27_ERROR => [
            'en' => 'Route compilation error. The route name `%name%` must not be repeated.',
            'ru' => 'Ошибка составления маршрутов.  Название `%name%` маршрута  не должно повторяться.'
        ],
        self::HL28_ERROR => [
            'en' => 'Route compilation error. A route can only have one name.',
            'ru' => 'Ошибка составления маршрутов. У маршрута может быть только одно имя.'
        ],
        self::HL29_ERROR => [
            'en' => 'Route compilation error. Along with the `page()` controller, there must be a name() method.',
            'ru' => 'Ошибка составления маршрутов. Вместе с контроллером `page()` должен быть метод name().'
        ],
        self::HL30_ERROR => [
            'en' => 'Route compilation error. The `page()` controller must have the correct type.',
            'ru' => 'Ошибка составления маршрутов. В контроллере `page()`должен быть корректный тип.'
        ],
        self::HL31_ERROR => [
            'en' => 'Error compiling the page() route or the `phphleb/adminpan` library is not included. ' .
                'In the first case, you need to install the library. ' .
                'In the second case, it is necessary to check that the `%name%` type for the page() controller is correctly specified, it must correspond to the configuration along the %path% path.',
            'ru' => 'Ошибка составления маршрута page() или не подключена библиотека `phphleb/adminpan`. ' .
                'В первом случае нужно установить библиотеку. ' .
                'Во втором случае - необходимо проверить, что правильно указан тип `%name%` для контроллера page(), он должен соответствовать конфигурации по пути %path%.'
        ],
        self::HL32_ERROR => [
            'en' => 'Route compilation error. The address `%route%` part `%part%` must be completely dynamic: /{...}/ or /{...?}/',
            'ru' => 'Ошибка составления маршрутов. Часть `%part%` адреса `%route%` должна быть полностью динамической: /{...}/ или /{...?}/'
        ],
        self::HL33_ERROR => [
            'en' => 'Route compilation error. The @ tag is set incorrectly; it can only be at the beginning of part of the route.',
            'ru' => 'Ошибка составления маршрутов. Неправильно установлен тег @, он может быть только в начале части маршрута.'
        ],
        self::HL34_ERROR => [
            'en' => 'The protect() method of a route cannot be used in conjunction with the plain() method.',
            'ru' => 'Метод protect() маршрута не может быть использован совместно с методом plain().'
        ],
        self::HL35_ERROR => [
            'en' => 'Class autoloader error. Could not find controller class: %class%.',
            'ru' => 'Ошибка автозагрузчика классов. Не удалось найти класс контроллера : %class%.'
        ],
        self::HL36_ERROR => [
            'en' => 'Route compilation error. The module name (%name%) must consist of lowercase Latin letters, numbers, a hyphen and the \'/\' symbol and can be converted into a class name.',
            'ru' => 'Ошибка составления маршрутов. Название модуля (%name%) должно состоять из из латинских букв в нижнем регистре, цифр, дефиса и символа \'/\' и иметь возможность быть преобразованным в название класса.'
        ],
        self::HL37_ERROR => [
            'en' => 'The `%method%` method of the controller `%class%` returned an unsupported data type. Available types: %types%',
            'ru' => 'Метод `%method% контроллера `%class%` вернул неподдерживаемый тип данных. Доступные типы: %types%'
        ],
        self::HL38_ERROR => [
            'en' => 'Route compilation error. Duplicate {%key%:%value%} key for dynamic address %address%.',
            'ru' => 'Ошибка составления маршрутов. Дублирование ключа {%key%:%value%} для динамического адреса %address%.'
        ],
        self::HL39_ERROR => [
            'en' => 'Error in creating routes. The redirect() method status code can be in the range 300-308.',
            'ru' => 'Ошибка составления маршрутов. Код статуса метода redirect() может быть в диапазоне 300-308.'
        ],
        self::HL40_ERROR => [
            'en' => 'Error in creating routes. Only one noDebug() method can be added to a route.',
            'ru' => 'Ошибка составления маршрутов. К маршруту может быть добавлен только один метод noDebug().'
        ],
        self::HL41_ERROR => [
            'en' => 'Error in creating routes. No route alias found for `%target%` >>> `%origin%`.',
            'ru' => 'Ошибка составления маршрутов. Не найден алиас маршрута для `%target%` >>> `%origin%`.'
        ],
    ];

    protected array $errorInfo = [];

    protected bool $isDebug = false;

    protected string $tag = '';

    public function __construct(string $messageKey = "")
    {
        parent::__construct();

        $this->setStatus(500);

        $this->tag = $messageKey;

        $this->errorInfo = self::ALL[(string)\constant(self::class . '::'. $messageKey)];
    }

    /**
     * Extends the output of a message and performs related actions.
     *
     * Расширяет вывод сообщения и осуществляет сопутствующие действия.
     */
    public function complete(bool $isDebug, array $replacements = [], bool $sendToLog = true): static
    {
        $this->isDebug = $isDebug;
        $this->setReplacements(\array_map('_e', $replacements));
        if ($sendToLog) {
            Log::error( $this->tag . ': ' . $this->errorInfo['en']);
        }

        return $this;
    }

    /**
     * Returns the error in plain text.
     *
     * Возвращает ошибку в виде простого текста.
     */
    public function getError(): string
    {
        return $this->errorInfo['en'] ?? 'Unrecognized error output.';
    }

    /**
     * Substring replacement, for example for HL000_ROUTE_ERROR it could be ['value' => 0].
     *
     * Замена подстрок, например для HL000_ROUTE_ERROR это может быть ['value' => 0].
     */
    private function setReplacements(array $trans): void
    {
        foreach ($trans as $key => $val) {
            $trans["%$key%"] = (string)$val;
            unset($trans[$key]);
        }

        if (!isset(self::ALL[$this->tag])) {
            $this->message = "Undefined {$this->message}";
            return;
        }

        foreach (self::ALL[$this->tag] as $key => $item) {
            $this->errorInfo[$key] = \strtr($item, $trans);
        }

        if ($this->isDebug) {
            $this->message = $this->coloredMessage();
        }
    }

   abstract protected function coloredMessage(): string;
}
