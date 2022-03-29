<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportExcelJobLogs extends Model
{
    use HasFactory;

    public static function insertFileException($data){

        return ImportExcelJobLogs::insertGetId($data);
    }
}
