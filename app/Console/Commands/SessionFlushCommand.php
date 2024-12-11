<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SessionFlushCommand extends Command
{
    protected $signature = 'session:flush';

    protected $description = 'Command description';

    public function handle(): void
    {
        \Session::flush();
    }
}
