<?php

namespace App\Services\Discord;

use App\Services\Discord\Discord;

class Guilds extends Discord
{
    public function getGuild()
    {
        try {
            return $this->makeRequest('GET', '/guilds/'.config('services.discord.server_id'));
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function getUserRoles()
    {
        $user = \Auth::user();
        $guildId = config('services.discord.server_id');
        try {
            $call = $this->makeRequestBot('GET', "/guilds/{$guildId}/members");
            return $call;
        }catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }
}
