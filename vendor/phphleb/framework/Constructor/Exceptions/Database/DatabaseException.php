<?php

namespace Hleb;

use Hleb\Constructor\Attributes\NotFinal;

/**
 * An exception occurred while working with the database.
 *
 * Исключение возникло при работе с базой данных.
 *
 * @see \RuntimeException
 */
#[NotFinal]
class DatabaseException extends \PDOException implements CoreException
{
}
