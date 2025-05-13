<?php

declare(strict_types=1);

namespace Hleb\Main\Routes\Methods\Traits\Group;


use Hleb\Route\Group\GroupDomain;

trait GroupDomainTrait
{
    /**
     * Sets a domain or subdomain limit for the route.
     * The $name parameter can be an array containing a list of values.
     * If you need to set a condition for all levels above, just set '*'
     * into an array.
     * The $level parameter is responsible for the domain level. The domain
     * itself is usually under level 2.
     * Scheme: subdomain(3).domain(2).com(1) or subdomain(4).domain(3).net(2).com(1)
     * For example:
     * domain('test') - matches all types of test.com, test.net, etc.
     * domain('test')->domain('com', 1) - test.com only. Usually directed to the site
     * a specific domain, so it is unnecessary to specify in such detail.
     * domain(['test1', 'test2'])->domain('com', 1) - test1.com only, test1.com only
     * or test2.com, or test2.net.
     * domain('sub', 3)->domain('domain') - sub.domain.com or sub.domain.net etc.
     * domain('*', 3)->domain('test') - all third-level subdomains for test.com
     * or test.net and so on.
     *
     * Устанавливает ограничение по домену или поддомену для маршрута.
     * Параметр $name может быть массивом, содержащим перечень значений.
     * Если нужно задать условие для всех уровней выше, достаточно установить '*'
     * в массив.
     * Параметр $level отвечает за уровень домена. Сам домен обычно под уровнем 2.
     * Схема: subdomain(3).domain(2).com(1) или subdomain(4).domain(3).net(2).com(1)
     * Например:
     * domain('test') - соотносится со всеми видами test.com, test.ru и тд.
     * domain('test')->domain('com', 1) - только test.com. Обычно на сайт направлен
     * определенный домен, так что настолько подробно указывать излишне.
     * domain(['test1', 'test2'])->domain('com', 1) - только test1.com, только test1.com
     * или test2.com, или test2.ru.
     * domain('sub', 3)->domain('domain') - sub.domain.com или sub.domain.ru и тд.
     * domain('*', 3)->domain('test') - все поддомены третьего уровня для test.com
     * или test.net и тд.
     *
     * ```php
     *  Route::toGroup()->domain('test');
     *    // ... //
     *  Route::endGroup();
     * ```
     *
     * @param string|array $name - the name of the domain or subdomain for this level.
     *                           - название домена или поддомена для этого уровня.
     *
     * @param int $level - уровень назначения от 0. По умолчанию 2.
     *                   - assignment level from 0. Default 2.
     *
     * @return GroupDomain
     */
    public function domain(string|array $name, int $level = 2): GroupDomain
    {
        return new GroupDomain($name, $level);
    }
}
