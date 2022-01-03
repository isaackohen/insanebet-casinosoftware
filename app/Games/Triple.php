<?php

namespace App\Games;

use App\Game;
use App\Games\Kernel\Data;
use App\Games\Kernel\GameCategory;
use App\Games\Kernel\Metadata;
use App\Games\Kernel\Module\General\Wrapper\MultiplierCanBeLimited;
use App\Games\Kernel\ProvablyFair;
use App\Games\Kernel\ProvablyFairResult;
use App\Games\Kernel\Quick\QuickGame;
use App\Games\Kernel\Quick\SuccessfulQuickGameResult;
use App\Games\Kernel\RejectedGameResult;

class Triple extends QuickGame implements MultiplierCanBeLimited
{
    public function metadata(): Metadata
    {
        return new class extends Metadata {
            public function id(): string
            {
                return 'triple';
            }

            public function name(): string
            {
                return 'Triple';
            }

            public function icon(): string
            {
                return 'fas fa-diamond';
            }

            public function category(): array
            {
                return [GameCategory::$originals, GameCategory::$table, GameCategory::$instant];
            }
        };
    }

    public function start($user, Data $data)
    {
        if (!isset($data->game()->tiles) || sizeof($data->game()->tiles) < 3 || sizeof($data->game()->tiles) > 3) {
            return new RejectedGameResult(1);
        }
        return new SuccessfulQuickGameResult((new ProvablyFair($this))->result(), function (SuccessfulQuickGameResult $result, array $transformedResults) use ($user, $data) {
            $result->win($user, $data, $this->getMultiplier($transformedResults, $data->intArray('tiles')));
			$result->delay(1000);
			$user_tiles = $data->intArray('tiles');
			sort($user_tiles);
            return [
                'tiles' => $transformedResults,
                'user_tiles' => [$user_tiles[0], $user_tiles[1], $user_tiles[2]]
            ];
        });
    }

    private function getMultiplier($transformedResults, $tiles)
    {
		if(!isset($tiles) || sizeof($tiles) < 3 || sizeof($tiles) > 3) return 0;
	    $o = [];
		sort($tiles);
		array_push($o, "".$transformedResults[$tiles[0]]."", "".$transformedResults[$tiles[1]]."", "".$transformedResults[$tiles[2]]."");
        // 0 = yellow, 1 = blue, 2 = purple
        if($o == [2,2,0]) return 0.10;
		if($o == [0,2,2]) return 0.10;
		if($o == [2,0,2]) return 0.10;
        if($o == [0,1,1]) return 0.25;
		if($o == [1,0,1]) return 0.25;
		if($o == [1,1,0]) return 0.25;
        if($o == [2,1,1]) return 0.40;
		if($o == [1,2,1]) return 0.40;
		if($o == [1,1,2]) return 0.40;
        if($o == [2,0,1]) return 0.60;
		if($o == [0,2,1]) return 0.60;
		if($o == [2,0,1]) return 0.60;
		if($o == [1,2,0]) return 0.60;
		if($o == [0,1,2]) return 0.60;
        if($o == [0,0,1]) return 1.50;
		if($o == [1,0,0]) return 1.50;
		if($o == [0,1,0]) return 1.50;
        if($o == [2,2,1]) return 2.00;
		if($o == [1,2,2]) return 2.00;
		if($o == [2,1,2]) return 2.00;
        if($o == [0,0,0]) return 3.50;
        if($o == [1,1,1]) return 7.50;
        if($o == [2,2,2]) return 14.00;
		return 0;
    }

    public function isLoss(ProvablyFairResult $result, Data $data): bool
    {
        return $this->getMultiplier($result->result(), $data->intArray('tiles')) <= 1;
    }

    public function result(ProvablyFairResult $result): array
    {
        $icons = [0,1,2];
        return array_map(function($value) use($icons) {
            return $icons[floor($value * 3)];
        }, $result->extractFloats(36));
    }

    public function multiplier(?Game $game, ?Data $data, ProvablyFairResult $result): float
    {
        return $this->getMultiplier($this->result($result), $data->intArray('tiles'));
    }

    public function getBotData(): array
    {
        $tiles = [];

        for ($i = 0; $i < 3; $i++) {
            $rnd = mt_rand(1, 35);
            if (in_array($rnd, $tiles)) {
                $i--;
                continue;
            }

            array_push($tiles, $rnd);
        }

        return [
            'tiles' => $tiles,
        ];
    }
}