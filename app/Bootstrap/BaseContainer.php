<?php

declare(strict_types=1);

namespace App\Bootstrap;

use App\Bootstrap\Services\RequestIdInterface;
use Hleb\Constructor\Attributes\Dependency;
use Hleb\Constructor\Containers\CoreContainer;

/**
 * The services defined in the container are available in classes inherited from `Hleb\Base\Container`.
 * Standard classes of the framework - controllers, mediators, commands, etc.
 * already have such an implementation.
 *
 * To naturally determine the contents of the container - its configuration is
 * this class and its `ContainerInterface` interface. To add your class or service
 * into the container, you must specify a match in the match operator, so that
 * when requested, only those data that corresponded to the request were created and returned.
 *
 * If the object being invoked is too complex to place the initialization in the match response,
 * then you can do the initialization separately, for example in a class
 * App\Bootstrap\Services\ServiceName and assign the latter as a singleton.
 *
 * In order for the IDE to generate adequate hints for the returned class, it is necessary
 * add a method in the current class with the return type of the interface of this class,
 * as already done for `requestId`.
 * After that, you need to define a new method in the `ContainerInterface` interface.
 * For example, to call in the controller $this->container->requestId() would be interfaced
 * is defined and the IDE will list the available actions.
 *
 *
 * Определённые в контейнере сервисы доступны в классах унаследованных от `Hleb\Base\Container`.
 * Стандартные классы фреймворка - контроллеры, посредники, команды и т.д.
 * уже имеют такую реализацию.
 *
 * Для естественного определения содержимого контейнера - его конфигурацию составляют
 * этот класс и его интерфейс `ContainerInterface`. Чтобы добавить свой класс или сервис
 * в контейнер, необходимо указать соответствие в операторе match, таким образом, чтобы
 * при запросе создавались и отдавались только те данные, которые соответствуют запросу.
 *
 * Если инициируемый объект слишком сложен, чтобы поместить инициализацию в ответ match,
 * то можно сделать инициализацию отдельно, например в классе
 * App\Bootstrap\Services\ServiceName и назначить последний как singleton.
 *
 * Чтобы IDE создавало адекватные подсказки по возвращаемому классу, необходимо
 * добавить в текущем классе метод с возвращаемым типом интерфейса этого класса,
 * как это уже сделано для `requestId`.
 * После этого нужно новый метод определить в интерфейсе `ContainerInterface`.
 * Например, для вызова в контроллере $this->container->requestId() будет
 * определён интерфейс и IDE выведет список доступных действий.
 */
#[Dependency]
final class BaseContainer extends CoreContainer implements ContainerInterface
{
    /**
     * Calling an initiated container by interface or class name.
     *
     * Вызов инициированного контейнера по интерфейсу или названию класса.
     */
    #[\Override]
    final public function get(string $id): mixed
    {
        // If a singleton is needed, then the object must be created in a `ContainerFactory`.
        // Если нужен singleton, то объект должен быть создан в `ContainerFactory`.
        return ContainerFactory::getSingleton($id) ?? match ($id) {
            // Here you can map the interface (or name) to the creation of the corresponding object.
            // Здесь можно сопоставить интерфейс (или название) с созданием соответствующего объекта.
            // ... //

            // Getting standard containers (singletons), with the ability to override them in ContainerFactory.
            // Получение стандартных контейнеров (singletons), с возможностью переопределить в ContainerFactory.
            default => parent::get($id),
        };
    }


    /*
     * Methods for a specific service each, after adding a method it will be available
     * like $this->container->{method}() in classes inherited from Hleb\Base\Container.
     *
     * Методы для конкретного сервиса каждый, после добавления метода он будет доступен
     * как $this->container->{method}() в классах унаследованных от Hleb\Base\Container.
     */

    /** @inheritDoc */
    #[\Override]
    final public function requestId(): RequestIdInterface
    {
        return $this->get(RequestIdInterface::class);
    }

    // ... //
}
