<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    use HasFactory;

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'id';

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
    protected $fillable = ['id', 'player_login', 'map_uid', 'score'];

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

    public function player() {
        return $this->belongsTo(Player::class, 'player_login', 'login');
    }
}
