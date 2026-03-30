<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'columns';

    protected $fillable = ['name', 'board_id'];

    public function board()
    {
        return $this->belongsTo(Board::class);
    }

    public function games()
    {
        return $this->hasMany(Game::class, 'column_id');
    }
}