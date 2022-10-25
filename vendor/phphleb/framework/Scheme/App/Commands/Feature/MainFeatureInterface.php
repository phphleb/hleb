<?php


namespace Hleb\Scheme\App\Commands\Feature;


interface MainFeatureInterface
{
    public function run(array $arguments = []): void;
}

