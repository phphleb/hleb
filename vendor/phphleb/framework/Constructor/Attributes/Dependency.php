<?php

namespace Hleb\Constructor\Attributes;

/**
 * The #[Dependency] tag denotes a framework class
 * that is waiting for external code to be added
 * to the implementation of a particular functionality.
 * The existence and naming of this class in the project
 * is as necessary as the presence of the library
 * of the framework itself.
 *
 * Меткой #[Dependency] обозначается класс фреймворка,
 * ожидающий внесения внешнего кода в реализацию
 * той или иной функциональности.
 * Существование и именование этого класса в проекте
 * так-же обязательно, как и наличие библиотеки
 * самого фреймворка.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class Dependency
{
}
