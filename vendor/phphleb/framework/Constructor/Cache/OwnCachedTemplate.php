<?php

declare(strict_types=1);

/*
 * User-specific cached template.
 *
 * Кешируемый шаблон с зависимостью от конкретного пользователя.
 */

namespace Hleb\Constructor\Cache;

class OwnCachedTemplate extends CachedTemplate
{
    // The name of the action to be displayed in the debug panel.
    // Возвращает название действия для вывода в отладочной панели.
    public function infoTemplateName() {
        return 'include<b>Own</b>CachedTemplate';
    }

    // Returns a string to make the template unique to a specific user.
    // Возвращает идентификатор для создания уникальности шаблона по отношению к конкретному пользователю.
    public function templateAreaKey() {
        return session_id();
    }
}


