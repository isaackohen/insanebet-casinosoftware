<?php

namespace App\Currency\Local;

use App\Currency\Option\WalletOption;

class Rub extends LocalCurrency
{
    public function id(): string
    {
        return 'local_rub';
    }

    public function walletId(): string
    {
        return 'rub';
    }

    public function name(): string
    {
        return 'RUB';
    }

    public function alias(): string
    {
        return 'rub';
    }

    public function displayName(): string
    {
        return 'RUB';
    }

    public function icon(): string
    {
        return 'rub';
    }

    protected function options(): array
    {
        return [
            new class extends WalletOption {
                public function id()
                {
                    return 'fk_merchant_id';
                }

                public function name(): string
                {
                    return 'Freekassa Merchant id';
                }
            },
            new class extends WalletOption {
                public function id()
                {
                    return 'fk_secret1';
                }

                public function name(): string
                {
                    return 'Freekassa secret word 1';
                }
            },
            new class extends WalletOption {
                public function id()
                {
                    return 'fk_secret2';
                }

                public function name(): string
                {
                    return 'Freekassa secret word 2';
                }
            },
            new class extends WalletOption {
                public function id()
                {
                    return 'min_deposit';
                }

                public function name(): string
                {
                    return 'Min deposit';
                }
            },
        ];
    }

    public function tokenPrice()
    {
        return 1;
    }
}
