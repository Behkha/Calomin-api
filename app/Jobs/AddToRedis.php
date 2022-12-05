<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;

class AddToRedis implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $key;

    private $id;
    
    public function __construct($key, $id)
    {
        $this->key = $key;

        $this->id = $id;
    }

    public function handle()
    {
        switch ($this->key) {
            case 'sellers':
                Redis::ZADD('sellers:views', 0, $this->id);

                Redis::ZADD('sellers:bookmarks', 0, $this->id);

                Redis::ZADD('sellers:ratings', 0, $this->id);

                Redis::ZADD('sellers:sells', 0, $this->id);

                break;
        }
    }
}
