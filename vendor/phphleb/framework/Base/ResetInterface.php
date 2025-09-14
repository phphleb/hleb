<?php
/**
 * @author  Foma Tuturov <fomiash@yandex.ru>
 */

namespace Hleb\Base;

interface ResetInterface
{
    /**
     * Called automatically when an asynchronous request is made,
     * and/or in long-running mode, to reset the state
     * of the service in the container.
     * Must be an idempotent operation.
     * There is also RollbackInterface to reset the static state
     * of all classes loaded by the autoloader.
     *
     * Вызывается автоматически при выполнении асинхронного запроса,
     * и/или в режиме long-running, для сброса состояния сервиса в контейнере.
     * Должен быть идемпотентной операцией.
     * Также существует RollbackInterface для сброса статического состояния
     * всех загруженных классов.
     */
    public function reset(): void;
}
