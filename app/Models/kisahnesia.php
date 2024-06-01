<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class kisahnesia extends Model
{
    use HasFactory;

    protected $fillable = [
        "slug", "title", "writer", "content", "tags", "thumbnail"
    ];
}
