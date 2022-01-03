<?php

namespace App\Currency\Aggregator;

use App\Invoice;
use Illuminate\Http\Request;

abstract class Aggregator
{
    abstract public function invoice(Invoice $invoice): string|array;

    abstract public function status(Request $request): string;

    abstract public function validate(Request $request): bool;

    abstract public function id(): string;

    abstract public function name(): string;

    abstract public function icon(): string;

    public static function list(): array
    {
        return [
            new FreeKassaAggregator(),
            new JumpOnline(),
        ];
    }

    public static function find(string $id): ?self
    {
        foreach (self::list() as $aggregator) {
            if ($aggregator->id() === $id) {
                return $aggregator;
            }
        }

        return null;
    }
}
