<?php

namespace App\Events;

use App\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BonusBattleWrongBet implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private User $user;

    private string $bbgame;

    private string $bbid;

    public function __construct($user, string $bbid, string $bbgame)
    {
        $this->bbid = $bbid;
        $this->user = $user;
        $this->bbgame = $bbgame;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel
     */
    public function broadcastOn()
    {
        return new PrivateChannel('App.User.'.$this->user->id);
    }

    public function broadcastWith()
    {
        return ['bbid'=> $this->bbid, 'bbgame'=> $this->bbgame];
    }
}
