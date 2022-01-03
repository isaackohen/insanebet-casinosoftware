<?php

namespace App\Games\Kernel\Module\General;

use App\Games\Kernel\Data;
use App\Games\Kernel\Game;
use App\Games\Kernel\Module\General\Wrapper\MultiplierCanBeLimited;
use App\Games\Kernel\Module\Module;
use App\Games\Kernel\Module\ModuleConfigurationOption;
use App\Games\Kernel\ProvablyFairResult;
use App\Modules;

class MultiplierLimiterModule extends Module
{
    public function id(): string
    {
        return 'multiplier_limiter';
    }

    public function name(): string
    {
        return 'Payout limit';
    }

    public function description(): string
    {
        return "Lose game if payout reaches specific value.<br>It's possible to generate more real results.";
    }

    public function supports(): bool
    {
        return $this->game instanceof MultiplierCanBeLimited;
    }

    public function lose(bool $demo): bool
    {
        return Modules::get($this->game, $demo)->get($this, 'pass') !== '-1' && $this->chance(floatval(Modules::get($this->game, $demo)->get($this, 'pass'))) ? false
            : $this->game->multiplier($this->dbGame, $this->data, $this->result) > floatval(Modules::get($this->game, $demo)->get($this, 'multiplier'));
    }

    public function settings(): array
    {
        return [
            new class extends ModuleConfigurationOption {
                public function id(): string
                {
                    return 'multiplier';
                }

                public function name(): string
                {
                    return 'Max payout';
                }

                public function description(): string
                {
                    return 'Instantly lose if payout reaches specific value.';
                }

                public function defaultValue(): ?string
                {
                    return '100.00';
                }

                public function type(): string
                {
                    return 'input';
                }
            },
            new class extends ModuleConfigurationOption {
                public function id(): string
                {
                    return 'pass';
                }

                public function name(): string
                {
                    return '% that this module won\'t work';
                }

                public function description(): string
                {
                    return 'Use -1 to disable.';
                }

                public function defaultValue(): ?string
                {
                    return '-1';
                }

                public function type(): string
                {
                    return 'input';
                }
            },
        ];
    }
}
