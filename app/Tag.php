<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = [
        'nombre','username'
    ];

    public static function selTags()
    {
        return Tag::select('id AS id', 'nombre AS nombre')
            ->orderBy('id', 'asc')
            ->get();

    }
}
