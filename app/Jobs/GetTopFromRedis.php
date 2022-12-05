<?php

namespace App\Jobs;


use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Redis;

class GetTopFromRedis
{
    use Dispatchable;
    
    private $key;

    private $page;

    public function __construct($key, $page)
    {
        $this->key = $key;

        $this->page = $page;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $start = ($this->page -1) * 15;

        $end = ($this->page * 15) - 1;

        return Redis::ZREVRANGE($this->key, $start, $end);
    }
}
