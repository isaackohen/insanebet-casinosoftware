<?php

namespace App\Events;

use App\BonusBattles;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BonusBattleUpdate implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $bonusbattle;

    public function __construct(BonusBattles $bonusbattle)
    {
        $this->bonusbattle = $bonusbattle;
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
        return [
			'bonusbattle'=> $this->bonusbattle
		];

    }
}
