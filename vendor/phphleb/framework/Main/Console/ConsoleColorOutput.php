<?php

declare(strict_types=1);

namespace Hleb\Main\Console;

class ConsoleColorOutput
{
    public function paintStandard(string $text)
    {
        return "\e[0m$text\e[0m";
    }

    public function paintRed(string $text) // thumbnail
    {
       return "\e[31;1m$text\e[0m";
    }

    public function paintGreen(string $text) // thumbnail
    {
        return "\e[32;1m$text\e[0m";
    }

    public function paintBlue(string $text) // thumbnail
    {
        return "\e[36;1m$text\e[0m";
    }

    public function paintYellow(string $text)
    {
        return "\e[33m$text\e[0m";
    }

    public function paintError(string $text) // thumbnail
    {
        return "\e[41;37;1m$text\e[0m";
    }

    public function paintSuccess(string $text) // thumbnail
    {
        return "\e[32;37;1m$text\e[0m";
    }

    public function paintInfo(string $text) // thumbnail
    {
        return "\e[34;1m$text\e[0m";
    }

    public function ptSd(string $text)
    {
        return $this->paintStandard($text);
    }

    public function ptR(string $text) // thumbnail
    {
        return $this->paintRed($text);
    }

    public function ptG(string $text) // thumbnail
    {
        return $this->paintGreen($text);
    }

    public function ptB(string $text) // thumbnail
    {
        return $this->paintBlue($text);
    }

    public function ptY(string $text)
    {
        return $this->paintYellow($text);
    }

    public function ptEr(string $text) // thumbnail
    {
        return $this->paintError($text);
    }

    public function ptSc(string $text) // thumbnail
    {
        return $this->paintSuccess($text);
    }

    public function ptInf(string $text) // thumbnail
    {
        return $this->paintInfo($text);
    }
}

