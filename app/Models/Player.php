<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'login';

    /**
     * Indicates if the model's ID is auto-incrementing.
     */
    public $incrementing = false;

    /**
     * The data type of the ID.
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'login',
        'nick',
        'score',
        'banned', // dangerous, should not be here
        'auto_upload_replay'
    ];

    protected $attributes = [
        'nick' => 'Unkown',
        'banned' => false,
        'auto_upload_replay' => true,
    ];

    protected $casts = [
        'banned' => 'boolean',
        'auto_upload_replay' => 'boolean',
    ];

    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param DateTimeInterface $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('U');
    }

    public function getSerializedCreatedAt(): string {
        return $this->created_at->getTimestamp();
    }

    public function getSerializedUpdatedAt(): string {
        return $this->updated_at->getTimestamp();
    }

    /**
     * Make a player that serves as a fallback, if a record should be created while
     * the corresponding player doesn't exist.
     *
     * @param string $login
     * @return void
     */
    public static function makeEmptyDefaultPlayer(string $login): Player { // todo remove
        $player = new Player();
        $player->login = $login;
        $player->nick = 'Unkown';
        $player->banned = false;
        $player->auto_upload_replay = true;
        return $player;
    }

}
