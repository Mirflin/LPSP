<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ClientFetch implements ShouldBroadcast
{
    use SerializesModels, InteractsWithSockets, Dispatchable;

    public $client;

    public function __construct($client)
    {
        $this->client = $client;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('clients');
    }
    public function broadcastAs(){
        return 'newclient';
    }
}