<?php

namespace App\Console\Commands;

require_once app_path('NonComposerDependencies/trackmania-ws.php');

use App\Models\Player;
use Illuminate\Console\Command;
use TrackMania_Exception;
use TrackMania_Players;

class RefreshNicknames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'refresh-nicknames {--start-offset=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh nicknames based on the TMF Webservice';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $username = env('TMF_WEBSERVICE_USERNAME');
        $password = env('TMF_WEBSERVICE_PASSWORD');

        $webservice = new TrackMania_Players($username, $password);

        $players = Player::all();
        $playerCount = count($players);

        for ($i = $this->option("start-offset"); $i<$playerCount; $i++) {
            $player = $players[$i];
            try
            {
                $player->nick = $webservice->get($player->login)->nickname;
                $player->save();
            }
            catch(TrackMania_Exception $e)
            {
                printf('ERROR: HTTP Response: %d %s', $e->getHTTPStatusCode(),
                    $e->getHTTPStatusMessage());
                echo "\n";
                printf('ERROR: API Response: %s (%d)', $e->getMessage(), $e->getCode());
                echo "\n";
            }

            $this->info("Saved player number $i out of $playerCount, login: $player->login");

            sleep(11); // 360 per hour allowed -> maximum 1 every 10 seconds.
        }

        return 0;
    }
}
