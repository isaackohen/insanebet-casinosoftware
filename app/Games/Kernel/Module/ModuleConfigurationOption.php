<?php

namespace App\Games\Kernel\Module;

abstract class ModuleConfigurationOption
{
    abstract public function id(): string;

    abstract public function name(): string;

    abstract public function description(): string;

    abstract public function defaultValue(): ?string;

    /**
     * Supported values:
     *  - input
     * @return string
     */
    abstract public function type(): string;
}
