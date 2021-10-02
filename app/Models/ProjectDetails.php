<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectDetails extends Model
{
    use HasFactory;

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    public static function getProjectDetails($request)
    {
        $noOfRecord = $request['no_of_records'] ?? 10;
        $current_page = $request['page_number'] ?? 1;
        $offset = ($current_page*$noOfRecord)-$noOfRecord;

        $data = ProjectDetails::whereNull('deleted_at')->offset($offset)->limit($noOfRecord)->get();

        if(count($data)>0){
            return $data->toArray();
        }
        return [];
    }

    public static function getProjectTotalRecords()
    {
        return ProjectDetails::whereNull('deleted_at')->count('id');
    }
}
