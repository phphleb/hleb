<?php

declare(strict_types=1);

/*
 * Simple console output colorizer (not supported everywhere).
 *
 * Простой раскрашиватель консольного вывода (поддерживается не везде).
 */

namespace Hleb\Main\Console;

class ConsoleColorOutput
{
    public function paintStandard(string $text) {
        return "\e[0m$text\e[0m";
    }

    // thumbnail
    public function paintRed(string $text) {
        return "\e[31;1m$text\e[0m";
    }

    // thumbnail
    public function paintGreen(string $text) {
        return "\e[32;1m$text\e[0m";
    }

    // thumbnail
    public function paintBlue(string $text) {
        return "\e[36;1m$text\e[0m";
    }

    public function paintYellow(string $text) {
        return "\e[33m$text\e[0m";
    }

    // thumbnail
    public function paintError(string $text) {
        return "\e[41;37;1m$text\e[0m";
    }

    // thumbnail
    public function paintSuccess(string $text) {
        return "\e[32;37;1m$text\e[0m";
    }

    // thumbnail
    public function paintInfo(string $text) {
        return "\e[34;1m$text\e[0m";
    }

    public function ptSd(string $text) {
        return $this->paintStandard($text);
    }

    // thumbnail
    public function ptR(string $text) {
        return $this->paintRed($text);
    }

    // thumbnail
    public function ptG(string $text) {
        return $this->paintGreen($text);
    }

    // thumbnail
    public function ptB(string $text) {
        return $this->paintBlue($text);
    }

    public function ptY(string $text) {
        return $this->paintYellow($text);
    }

    // thumbnail
    public function ptEr(string $text) {
        return $this->paintError($text);
    }

    // thumbnail
    public function ptSc(string $text) {
        return $this->paintSuccess($text);
    }

    // thumbnail
    public function ptInf(string $text) {
        return $this->paintInfo($text);
    }
}

