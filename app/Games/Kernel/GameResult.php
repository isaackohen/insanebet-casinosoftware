<?php

namespace App\Games\Kernel;

interface GameResult
{
    public function toArray(Data $data): array;
}
