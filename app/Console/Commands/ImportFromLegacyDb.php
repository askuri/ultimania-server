<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class ImportFromLegacyDb extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import-from-legacy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'import from legacy db';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // must be the same as mysql server timezone.
        // if not, you will end up trying to insert a date that in CE(S)T would be 2020-03-29 02:29,
        // a time which doesn't exist in this timezone. It's in the transition of daylight savings time!
        // date_default_timezone_set('Europe/Berlin');

        // memory leak workaround
        DB::connection()->unsetEventDispatcher();

        $mapPlayer = function ($player) {
            return [
                'login' => $player['login'],
                'nick' => $player['nick'],
                'banned' => ! empty($player['ban_reason']),
                'allow_replay_download' => true,
                'created_at' => $this->timestampToMysql($player['add_time']),
                'updated_at' => $this->timestampToMysql($player['add_time']),
            ];
        };

        $mapMap = function ($map) {
            return [
                'uid' => $map['uid'],
                'name' => $map['mapname'],
                'created_at' => $this->timestampToMysql($map['add_time']),
                'updated_at' => $this->timestampToMysql($map['add_time']),
            ];
        };

        $mapRecord = function ($record) {
            return [
                'id' => Uuid::uuid4(),
                'player_login' => $record['login'],
                'map_uid' => $record['uid'],
                'score' => $record['score'],
                'created_at' => $this->timestampToMysql($record['add_time']),
                'updated_at' => $this->timestampToMysql($record['add_time']),
            ];
        };

        require 'import/player_with_ban_reason.php';
        $player_with_ban_reason = [];
        $this->chunkedMapAndInsert('players', $player_with_ban_reason, $mapPlayer);
        unset($players);

        require 'import/maps.php';
        $maps = [];
        $this->chunkedMapAndInsert('maps', $maps, $mapMap);
        unset($maps);

        require 'import/record_joined_players_maps.php';
        $record_joined_players_maps = [];
        $this->chunkedMapAndInsert('records', $record_joined_players_maps, $mapRecord);
        unset($record_joined_players_maps);

        $this->info('Peak memory usage: ' . memory_get_peak_usage() / 1024 / 1024 . 'MB');

        return 0;
    }

    private function chunkedMapAndInsert($newTableName, $legacyTableContent, $mapper) {
        $chunksize = 50;

        $insertTmp = [];
        foreach ($legacyTableContent as $i => $row) {
            $insertTmp[] = $mapper($row);

            if ($i % $chunksize == 0) {
                DB::table($newTableName)->insert($insertTmp);
                $insertTmp = [];
                $this->info("Inserting in $newTableName number $i");
                $this->info('Memory usage: ' . memory_get_usage() / 1024 / 1024 . 'MB');
            }
        }
        // insert leftovers
        DB::table($newTableName)->insert($insertTmp);
    }

    private function timestampToMysql($timestamp) {
        return date("Y-m-d H:i:s", $timestamp);
    }
}
