<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;

class IncrTopRedis implements ShouldQueue
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
        Redis::ZINCRBY($this->key, 1, $this->id);
    }
}
