<?php

namespace App\Bootstrap;

use App\Bootstrap\Services\RequestIdInterface;
use Hleb\Constructor\Attributes\Dependency;
use Hleb\Constructor\Containers\CoreContainerInterface;

#[Dependency]
interface ContainerInterface extends CoreContainerInterface
{
    /**
     * An example of a method for a container that returns a request ID.
     *
     * Пример метода для контейнера, который возвращает идентификатор запроса.
     */
    public function requestId(): RequestIdInterface;

    // ... //
}
