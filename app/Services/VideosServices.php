<?php

namespace App\Services;

use App\Models\videosDetails;
use Carbon\Carbon;

class VideosServices{

    public static function uploadVideos($request)
    {
        $user_id = 5;
        if (isset($_FILES) && !empty($_FILES['request']['name']['file'])) {
            $dir_name =  $_SERVER['DOCUMENT_ROOT'].$user_id;
            if (!is_dir($dir_name)) {
                @mkdir($dir_name, "0777", true);
            }

            $current_timestamp  = Carbon::now()->timestamp;
            $video_saved_name = $current_timestamp . $_FILES['request']['name']['file'];
            

            $video_data['video_name'] =  $_FILES['request']['name']['file'];
            $video_data['video_path'] =  $user_id."//".$video_saved_name;
            $request->file->move($dir_name, $_FILES['request']['name']['file']);
            echo $dir_name;
            $video_id['video_id'] = videosDetails::uploadVideos($video_data);
            return $video_id;

        }
    }

    public static function downloadVideos($request)
    {
        $path = videosDetails::downloadVideos($request->id);
        return ['download_path'=>env('APP_URL').$path->video_path];
    }

}
