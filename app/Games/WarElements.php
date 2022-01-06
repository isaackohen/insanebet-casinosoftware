<?php

namespace App\Games;

use App\Currency\Currency;
use App\Events\MultiplayerTimerStart;
use App\Game;
use App\Games\Kernel\Data;
use App\Games\Kernel\GameCategory;
use App\Games\Kernel\Metadata;
use App\Games\Kernel\Module\General\HouseEdgeModule;
use App\Games\Kernel\Multiplayer\MultiplayerGame;
use App\Games\Kernel\ProvablyFair;
use App\Games\Kernel\ProvablyFairResult;
use App\Jobs\MultiplayerDisableBetAccepting;
use App\Jobs\MultiplayerFinishAndSetupNextGame;
use App\Jobs\MultiplayerUpdateData;
use App\Jobs\MultiplayerUpdateTimestamp;
use App\Settings;

class WarElements extends MultiplayerGame
{
    public function metadata(): Metadata
    {
        return new class extends Metadata {
            public function id(): string
            {
                return 'warofelements';
            }

            public function name(): string
            {
                return 'War of Elements';
            }

            public function icon(): string
            {
                return 'baccarat';
            }

            public function category(): array
            {
                return [GameCategory::$originals, GameCategory::$table];
            }
        };
    }

    protected function getPlayerData(Game $game): array
    {
        return ['bet' => $this->userData($game)['data']['bet']];
    }

    public function NextGameLive() {
        $this->state()->data(['event' => null]);
        $this->state()->resetPlayers();
        $this->state()->clientSeed('This game is not verifiable using "Provably Fair" tools.');
        $this->state()->serverSeed(ProvablyFair::generateServerSeed());
        $this->state()->nonce(-1);
        $this->state()->timestamp(now()->timestamp);
        $this->state()->betting(true);
        event(new MultiplayerTimerStart($this));
    }

    public function DisableBetAcceptingGameLive() {
        $this->state()->betting(false);
    }
    
    public function FinishGameLive() {
        $result = $this->getResult($this->state()->data()['result']);
        event(new MultiplayerGameFinished($this, $this->state()->data()));
        foreach($this->getActiveGames() as $game) {
            $multiplier = 0;
            $profit = 0;
            foreach((array) $this->userData($game)['data']['bet'] as $key => $value) {
                if($value == 0) continue;

                if($result['status'] === 'draw' && $key === 'draw') {
                    $multiplier += 8;
                    $profit += $value * 8;
                } else if($result['status'] === 'player' && $key === 'player') {
                    $multiplier += 2;
                    $profit += $value * 2;
                } else if($result['status'] === 'dealer' && $key === 'dealer') {
                    $multiplier += 1.95;
                    $profit += $value * 1.95;
                }
            }

            $this->win($game, $multiplier, 1000, $profit);
        }
    }
    private function getResult(array $result) {
        \Illuminate\Support\Facades\Log::info($result);
        $player = $result['player'];
        $dealer = $result['dealer'];
        $player_score = 0;
        $dealer_score = 0;
        foreach($player as $key => $value) {
            $card = substr($value, 1);
            if($card == 'K' || $card == 'J' || $card == 'Q' || $card == '10') continue;
            if($card == 'A') {
                $player_score += 1;
                continue;
            }
            $player_score += $card;
        }
        foreach($dealer as $key => $value) {
            $card = substr($value, 1);
            if($card == 'K' || $card == 'J' || $card == 'Q' || $card == '10') continue;
            if($card == 'A') {
                $player_score += 1;
                continue;
            }
            $player_score += $card;
        }
        if($player_score > 10) $player_score -= 10;
        if($dealer_score > 10) $player_score -= 10;
        return [
            'status' => $player_score == $dealer_score ? 'draw' : ($player_score > $dealer_score ? 'player' : 'dealer')
        ];
    }

    public function customWagerCalculation(Data $data): ?bool {
        $totalBet = 0;
        foreach((array) $data->game()->bet as $key => $value) $totalBet += $value;
        if($totalBet < Currency::find($data->currency())->minBet() || ($data->user() != null && $data->user()->balance(Currency::find($data->currency()))->demo($data->demo())->get() < $totalBet)) return false;
        $data->bet($totalBet);
        return true;
    }
    
    public function nextGame() {}
    public function onDispatchedFinish() {}
    public function startChain() {}
    function result(ProvablyFairResult $result): array {}





}
