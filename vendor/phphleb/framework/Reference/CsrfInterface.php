<?php

namespace Hleb\Reference;

interface CsrfInterface
{
    /**
     * The token() method returns the protected token for protection against CSRF attacks.
     *
     * Метод token() возвращает защищённый токен для защиты от CSRF-атак.
     */
    public function token(): string;

    /**
     * The field() method returns HTML content to be inserted
     * into the form to protect against CSRF attacks.
     *
     * Метод field() возвращает HTML-контент для вставки
     * в форму для защиты от CSRF-атак.
     */
    public function field(): string;


    /**
     * The framework checks the token for protection against CSRF.
     *
     * Проверка фреймворком токена для защиты от CSRF.
     */
    public function validate(?string $key): bool;


    /**
     * Returns the found token in the request data or null.
     *
     * Возвращает найденный токен в данных запроса или null.
     */
    public function discover(): string|null;
}
