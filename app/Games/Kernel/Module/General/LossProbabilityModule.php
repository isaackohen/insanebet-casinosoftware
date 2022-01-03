<?php

namespace App\Games\Kernel\Module\General;

use App\Games\Crash;
use App\Games\Kernel\Module\Module;
use App\Games\Kernel\Module\ModuleConfigurationOption;
use App\Games\Kernel\Multiplayer\MultiplayerGame;
use App\Modules;

class LossProbabilityModule extends Module
{
    public function id(): string
    {
        return 'loss_probability';
    }

    public function name(): string
    {
        return 'Static Loss %';
    }

    public function description(): string
    {
        return 'Additional static loss %.<br>Recommended only for *Quick games.';
    }

    public function settings(): array
    {
        return [
            new class extends ModuleConfigurationOption {
                public function id(): string
                {
                    return 'static_percent';
                }

                public function name(): string
                {
                    return 'Loss %';
                }

                public function description(): string
                {
                    return 'Loss %';
                }

                public function defaultValue(): ?string
                {
                    return '1';
                }

                public function type(): string
                {
                    return 'input';
                }
            },
        ];
    }

    public function supports(): bool
    {
        return ! ($this->game instanceof MultiplayerGame);
    }

    public function lose(bool $demo): bool
    {
        return $this->chance(floatval(Modules::get($this->game, $demo)->get($this, 'static_percent')));
    }
}
