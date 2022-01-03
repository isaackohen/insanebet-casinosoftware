<?php

namespace App\Events;

use App\Game;
use App\Gameslist;
use App\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class LiveFeedGame implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $game;

    private $delay;

    public function __construct(Game $game, $delay)
    {
        $this->game = $game;
        $this->delay = $delay;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel
     */
    public function broadcastOn()
    {
        return new Channel('Everyone');
    }

    public function broadcastWith()
    {
        if ($this->game->type === 'external') {
            $game_id = $this->game->game;
            $getgamename = Gameslist::cachedList()->where('id', '=', $game_id)->first();
            $image = 'Image/https://games.cdn4.dk/games'.$getgamename->image.'?q=30&mask=ellipse&auto=compress&sharp=10&w=20&h=20&fit=crop&usm=5&fm=png';
            $meta = ['id' => $game_id, 'icon' => $image, 'name' => $getgamename->name, 'category' => [$getgamename->category]];
            $delay = env('DELAY_LIVEFEED_EXTERNALGAMES') ?? 0;
        } else {
            $meta = \App\Games\Kernel\Game::find($this->game->game)->metadata()->toArray() ?? 0;
            $delay = $this->delay ?? 0;
        }

        return [
            'game' => $this->game->toArray(),
            'user' => User::where('_id', $this->game->user)->first()->toArray(),
            'metadata' => $meta,
            'delay' => $delay,
        ];
    }
}
 