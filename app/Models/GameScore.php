<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class GameScore extends Model {
    protected $fillable = ['player_name', 'score', 'attempts_used', 'won'];
}
