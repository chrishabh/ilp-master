<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class videosDetails extends Model
{
    use HasFactory;

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    public static function uploadVideos($data)
    {
        return videosDetails::insertGetId($data);
    }

    public static function downloadVideos($id)
    {
        return videosDetails::select('video_path')->whereNull('deleted_at')->where('id',$id);
    }
}
