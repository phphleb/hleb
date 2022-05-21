<?php

declare(strict_types=1);

namespace Hleb\Main\Insert\Examples;


use Hleb\Scheme\Home\Main\LoggerInterface;

/**
 * @see ExampleApp
 */
class ExampleLog implements LoggerInterface
{

    private $logger = [];

    /**
     * @param array $list -  Adding test values to be returned in methods.
     *
     *                    -  Добавление тестовых значений, которые будут возвращены в методах.
     */
    public function __construct(array $list)
    {
        $this->logger = $list;
    }

    // Call stub.
    public static function getInstance()
    {
        return new self;
    }

    public function emergency(string $message, array $context = [])
    {
        return $this->logger['emergency'];
    }

    public function alert(string $message, array $context = [])
    {
        return $this->logger['alert'];
    }

    public function critical(string $message, array $context = [])
    {
        return $this->logger['critical'];
    }

    public function error($message, array $context = [])
    {
        return $this->logger['error'];
    }

    public function warning(string $message, array $context = [])
    {
        return $this->logger['warning'];
    }

    public function notice(string $message, array $context = [])
    {
        return $this->logger['notice'];
    }

    public function info(string $message, array $context = [])
    {
        return $this->logger['info'];
    }

    public function debug(string $message, array $context = [])
    {
        return $this->logger['debug'];
    }

    public function log($level, string $message, array $context = [])
    {
        return $this->logger['log'];
    }
}