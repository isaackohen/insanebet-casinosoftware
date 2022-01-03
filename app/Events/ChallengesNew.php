<?php

namespace App\Events;

use App\Challenges;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChallengesNew implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $challenge;

    public function __construct(Challenges $challenge)
    {
        $this->challenge = $challenge;
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
			'challenge'=> $this->challenge
		];
    }
}
