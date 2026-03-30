<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = [
        'rawg_id',
        'title',
        'cover_url',
        'release_date'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_games')
                    ->withPivot('status', 'progress', 'started_at', 'completed_at')
                    ->withTimestamps();
    }

    public function notes()
    {
        return $this->hasMany(Note::class, 'game_id');
    }
    public function screenshots()
{
    return $this->hasMany(Screenshot::class, 'game_id');
}
}