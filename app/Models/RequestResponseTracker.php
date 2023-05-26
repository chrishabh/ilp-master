<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestResponseTracker extends Model
{
    use HasFactory;

    public static function saveLogs($array = [])
    {
        RequestResponseTracker::insert($array);
    }
}
