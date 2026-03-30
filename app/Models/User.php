<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribute casting
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    

    public function games()
    {
        return $this->belongsToMany(Game::class, 'user_games')
            ->withPivot('status', 'progress', 'started_at', 'completed_at')
            ->withTimestamps();
    }

    public function reviews()
    {
        return $this->hasMany(GameReview::class);
    }

    public function tips()
    {
        return $this->hasMany(GameTip::class);
    }
    public function notes()
{
    return $this->hasMany(Note::class);
}
}