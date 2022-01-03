<?php

namespace App\Games\Kernel\Module\General;

use App\Games\Crash;
use App\Games\Kernel\Module\Module;
use App\Games\Kernel\Module\ModuleConfigurationOption;
use App\Games\Mines;
use App\Games\Stairs;
use App\Games\Tower;
use App\Modules;

class TowerModule extends Module
{
    public function id(): string
    {
        return 'tower';
    }

    public function name(): string
    {
        return 'Tower-specific';
    }

    public function description(): string
    {
        return 'This module lets you configure static loss % for every game mode (1-4 mines).';
    }

    public function settings(): array
    {
        $settings = [];
        for ($mines = 1; $mines <= 4; $mines++) {
            array_push($settings, new class($mines) extends ModuleConfigurationOption {
                private $mines;

                public function __construct($mines)
                {
                    $this->mines = $mines;
                }

                public function id(): string
                {
                    return 'mines_'.$this->mines;
                }

                public function name(): string
                {
                    return 'Number of mines: '.$this->mines;
                }

                public function description(): string
                {
                    return 'Loss % with '.$this->mines.' mine(s) in the field';
                }

                public function defaultValue(): ?string
                {
                    return '1';
                }

                public function type(): string
                {
                    return 'input';
                }
            });
        }

        return $settings;
    }

    public function supports(): bool
    {
        return $this->game instanceof Tower;
    }

    public function lose(bool $demo): bool
    {
        return $this->chance(floatval(Modules::get($this->game, $demo)->get($this, 'mines_'.$this->game->getModuleData($this->dbGame))));
    }
}
