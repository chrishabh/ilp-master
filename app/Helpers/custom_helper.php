<?php

use App\Exceptions\AppException;
use App\Models\ApartmentDetails;
use App\Models\BlockDetails;
use App\Models\Floor;
use App\Models\MainDescritpion;
use App\Models\ProjectDetails;
use App\Models\SubDescritpion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

function pp($arr, $die="true")
	{
			echo '<pre>';
			print_r($arr);
			echo '</pre>';
			if($die == 'true')
			{
				die();
			}
	}
	function _print_r($array)
	{
		echo "<pre>";
		echo print_r($array);
		echo "<pre>";
	}

if (! function_exists('envparam')) {

	function envparam($key = null, $default = null)
    {
		if(empty($key))
		{
			return config($key,$default);
		}


        $configVal = config("env.".$key,$default);
		
		if(empty($configVal))
        {
            throw new AppException("No  envparam value found for key '$key'");
        }

        return $configVal;
	}
}
	function customiseLookupsData($array)
    {
    	foreach ($array as $key => &$value) {
            if($key == 'displaytext'){

        		$result_array = [];
        		foreach ($value as $k => $val) {
        			$result_array[$val['key']] = $val['text'];
        		}
        		$array[$key] = $result_array;
            }else{
                foreach ($value as $k => &$val) {
                    unset($val['type']);
                }
            }
    	}

        return $array;
    }

	function group_by($key, $data) {
        $result = array();
        foreach($data as &$val) {
            $val = (array)$val;
            if(array_key_exists($key, $val)){
                $result[$val[$key]][] = $val;
            }else{
                $result[""][] = $val;
            }
        }
        return $result;
    }

	function getXlsxFile($details,$file){
        //Give our xlsx file a name.
        $xlsxFileName = $file.'_'.date('Y_m_d_H_i_s').'.xlsx';

        // Set the Content-Type and Content-Disposition headers.
        //Open file pointer.
        $fp = fopen('php://output', 'w+');
        $doc = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $doc->getActiveSheet();
        $firstLineKeys = false;
        $uploaded = false;
        if(!empty($details)){
            //Loop through the associative array.
           foreach($details as $i => $row){
            if (empty($firstLineKeys)) {
                $firstLineKeys = array_keys($row);
                $j=1;
                foreach($firstLineKeys as $x_value){
                    $sheet->setCellValueByColumnAndRow($j,1,$x_value);
  		            $j=$j+1;
                }
            }
            $j=1;
            foreach($row as $x => $x_value) {
                $sheet->setCellValueByColumnAndRow($j,$i+2,$x_value);
                  $j=$j+1;
            }
              
            }
              // get last row and column for formatting
              $last_column = $doc->getActiveSheet()->getHighestColumn();
              $last_row = $doc->getActiveSheet()->getHighestRow();
  
              // autosize all columns to content width
              for ($k = 'A'; $k <= $last_column; $k++) {
                  $doc->getActiveSheet()->getColumnDimension($k)->setAutoSize(TRUE);
              }
  
              // if $keys, freeze the header row and make it bold
              if ($firstLineKeys) {
                  $doc->getActiveSheet()->freezePane('A2');
                  $doc->getActiveSheet()->getStyle('A1:' . $last_column . '1')->getFont()->setBold(true);
              }
              // format all columns as text
              $doc->getActiveSheet()->getStyle('A2:' . $last_column . $last_row)
                  ->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

                $doc->getActiveSheet()->getStyle('H1:H'.$last_row)->getAlignment()->setHorizontal('center');
                $doc->getActiveSheet()->getStyle('K1:K'.$last_row)->getAlignment()->setHorizontal('center');
                $doc->getActiveSheet()->getStyle('L1:L'.$last_row)->getAlignment()->setHorizontal('center');
                $doc->getActiveSheet()->getStyle('O1:O'.$last_row)->getAlignment()->setHorizontal('center');
                $doc->getActiveSheet()->getColumnDimension('G')->setAutoSize(FALSE);
                $doc->getActiveSheet()->getColumnDimension('G')->setWidth('16');
                $doc->getActiveSheet()->getColumnDimension('I')->setAutoSize(FALSE);
                $doc->getActiveSheet()->getColumnDimension('I')->setWidth('16');
                $doc->getActiveSheet()->getColumnDimension('J')->setAutoSize(FALSE);
                $doc->getActiveSheet()->getColumnDimension('J')->setWidth('16');
                $doc->getActiveSheet()->getColumnDimension('K')->setAutoSize(FALSE);
                $doc->getActiveSheet()->getColumnDimension('K')->setWidth('6');
                
            //   // Color
            // $doc->getActiveSheet()
            //     ->getStyle('A2:T'.$last_row)
            //     ->getFill()
            //     ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            //     ->getStartColor()
            //     ->setARGB('ffffff');
              // write and save the file
              //$writer = new Xlsx($doc); 
              $writer = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($doc);
              //$writer->save($fp);
			  ob_start();
			  	$writer->save($fp);
    			$content = ob_get_contents();
    			ob_end_clean();
				$uploaded = Storage::disk('wages_data')->put($xlsxFileName, $content); 
			//   $url['url'] = public_path().'/'.$xlsxFileName;
        }
        // $tempImage = tempnam(sys_get_temp_dir(), $xlsxFileName);
        // return $url;
        // fclose($fp);
        if($uploaded){
            $url = env('APP_URL').'/ipl/public/wages_data'.'/'.$xlsxFileName;
        }else{
            //throw new AppException('No wages exists to download',null,1001);
            $url = env('APP_URL');
        }
        return $url;
    }

    function getXlsxFiles($details,$file){
        //Give our xlsx file a name.
        ini_set('memory_limit', '128M');
        $xlsxFileName = $file.'_'.date('Y_m_d_H_i_s').'.xlsx';

        // Set the Content-Type and Content-Disposition headers.
        //Open file pointer.
        $fp = fopen('php://output', 'w+');
        $doc = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
       
        $firstLineKeys = false;
        $uploaded = false;
        $count = 0;
        if(!empty($details)){
            //Loop through the associative array.
          
           foreach($details as $key => $records){
               //if($count>0){
                $fp = fopen('php://output', 'w+');
                $sheet = $doc->createSheet();
                $sheet = $doc->getActiveSheet();
                $doc->setActiveSheetIndex(1);
               //}
            
            $sheet->setTitle($key);
            foreach($records as $i => $row){

                if ($i==0) {
                    $firstLineKeys = array_keys($row);
                    $j=1;
                    foreach($firstLineKeys as $x_value){
                        $sheet->setCellValueByColumnAndRow($j,1,$x_value);
                          $j=$j+1;
                    }
                }
                $j=1;
                foreach($row as $x => $x_value) {
                    $sheet->setCellValueByColumnAndRow($j,$i+2,$x_value);
                      $j=$j+1;
                }
                  
                }
                  // get last row and column for formatting
                  $last_column = $doc->getActiveSheet()->getHighestColumn();
                  $last_row = $doc->getActiveSheet()->getHighestRow();
      
                  // autosize all columns to content width
                  for ($k = 'A'; $k <= $last_column; $k++) {
                      $doc->getActiveSheet()->getColumnDimension($k)->setAutoSize(TRUE);
                  }
      
                  // if $keys, freeze the header row and make it bold
                  if ($firstLineKeys) {
                      $doc->getActiveSheet()->freezePane('A2');
                      $doc->getActiveSheet()->getStyle('A1:' . $last_column . '1')->getFont()->setBold(true);
                  }
                  // format all columns as text
                  $doc->getActiveSheet()->getStyle('A2:' . $last_column . $last_row)
                      ->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                  
                //   // Color
                //   $doc->getActiveSheet()
                //       ->getStyle('A1:A'.$last_row)
                //       ->getFill()
                //       ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                //       ->getStartColor();
                //   $doc->getActiveSheet()
                //       ->getStyle('D1:F'. $last_row)
                //       ->getFill()
                //       ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                //       ->getStartColor();    
                //   $doc->getActiveSheet()
                //       ->getStyle('I1')
                //       ->getFill()
                //       ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                //       ->getStartColor();
                  // write and save the file
                  //$writer = new Xlsx($doc); 
                 
                //   ob_start();
                //       $writer->save($fp);
                //     $content = ob_get_contents();
                //     ob_end_clean();
                //     $uploaded = Storage::disk('public')->put($xlsxFileName, $content); 
                //   $url['url'] = public_path().'/'.$xlsxFileName;

                $writer = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($doc);
              

            }
            $count++;
            $writer->save($fp);
            
        }
      
        $tempImage = tempnam(sys_get_temp_dir(), $xlsxFileName);
        return response()->download($tempImage, $xlsxFileName);
        fclose($fp);
        if($uploaded){
            $url = env('APP_URL').'/storage'.'/'.$xlsxFileName;
        }else{
           throw new AppException('No wages exists to download',null,1001);
        }
        return $url;
    }

    function importExcelToDB($file_path)
    {
        ini_set('memory_limit', '-1');
        //ini_set('max_execution_time', 240);
        if(!empty($file_path)){
            $excel_data = [];
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $spreadsheet = $reader->load($file_path);
            $sheet_count = $spreadsheet->getSheetCount();
            $insert_data = [];

            for($i=0; $i<$sheet_count; $i++){
                if($i == 0){
                    // $highestRow = $spreadsheet->getSheet($i)->getHighestRow();
                    // //$checkSheetData = $spreadsheet->getCell('')->toArray();
                    // $is_verified = false;
                    // for($k = 1; $k<=$highestRow; $k++){
                    //     $columnA =  $spreadsheet->getSheet($i)->getCellByColumnAndRow(1, $k)->getValue();
                    //     $columnB =  $spreadsheet->getSheet($i)->getCellByColumnAndRow(2, $k)->getValue();
                    //     if($columnA == "Main Description" && $columnB == "Sub Description"){
                    //         $is_verified = true;
                    //         continue;
                    //     }
                    //     if($is_verified){
                    //         if(!empty($columnA)){
                    //             $main_desc [] = $columnA;
                    //         }
                    //        if(!empty($columnB)){
                    //             $sub_desc [] = $columnB;
                    //        }
                           
                    //     }
                    // }
                    // $main_desc = array_unique($main_desc);
                    // $sub_desc = array_unique($sub_desc);
                    // MainDescritpion::checkMainDescription($main_desc);
                    // SubDescritpion::checckSubDescription($sub_desc);

                    continue;
                }
                if($i == 1){
                    continue;
                }
                $sheetData = $spreadsheet->getSheet($i)->toArray();
                $sheetData = array_map('array_filter', $sheetData);
                $sheetData = array_filter($sheetData);
                $key = $key1 = $key2 =0;
                //$block_id = 1;
                $total_insert = [];
                foreach($sheetData as $row_key => $row_data){
                   
                    if($row_key <= '5'){
                        foreach($row_data as $cell_key => $cell_data){ 
                            if(empty($cell_data)){
                                continue;
                            }
                            if(!empty($cell_data) && ltrim(trim($cell_data," ")) == "Project Name"){
                                $key = $cell_key;
                                $project_name = $row_data[++$key];
                                $project_id = ProjectDetails::getProjectId($project_name);

                            } elseif (!empty($cell_data) && ltrim(trim($cell_data," ")) == "Block"){
                                    $key1 = $cell_key;
                                    $block_name = $row_data[++$key1];
                                    $block_id = BlockDetails::getBlockId($block_name,$project_id);
                                
                            } elseif (!empty($cell_data) && ltrim(trim($cell_data," ")) == "Floor Number"){
                                $key2 = $cell_key;
                                $floor_name = $row_data[++$key2];
                                $floor_data = [
                                    "floor_name" => $floor_name,
                                    "block_id" => $block_id,
                                    "project_id" => $project_id
                                ];
                                $floor_id = Floor::addFloor($floor_data);
                            } 
                            elseif (!empty($cell_data) && ltrim(trim($cell_data," ")) == "Apartment Number"){
                                $key3 = $cell_key;
                                $apartment_name = $row_data[++$key3];
                                $apartment_id = ApartmentDetails::getApartmentId($apartment_name);
                                if(empty($apartment_id)){
                                    $data = [
                                        'project_id' => $project_id??1,
                                        'block_id' => $block_id,
                                        'apartment_number' => $apartment_name
                                    ];
                                    $apartment_id = ApartmentDetails::addApartmentDetails($data);
                                }
                            } elseif (!empty($cell_data) && $cell_data == "Floor Number"){
                                $key4 = $cell_key;
                                $floor_name = $row_data[++$key4];
                            }
                        }
                        continue;
                    }
                    if($row_key >= '8'){
                        if(!empty($row_data[0])){
                            $main_description_id = MainDescritpion::getMainDescriptionId($row_data[0]);
                        }
                    
                        if(!empty($row_data[1])){
                            $sub_description_id = SubDescritpion::getSubDescriptionId($row_data[1]);
                        }
                       
                        if(isEmptyArray($row_data)){
                            $insert_data    = [
                                'main_description_id' => $main_description_id,
                                'sub_description_id' => $sub_description_id,
                                'description' => null,
                                'area' => null,
                                'unit' => null,
                                'lab_rate' => null,
                                'total' => null,
                                'amount_booked' => null,
                                'name' => null,
                                'wages' => null,
                                'quantity' => null,
                                'booking_description' => null,
                                'floor' => null,
                            ];
                            foreach($row_data as $cell_key => $cell_value)
                            {
                                if($cell_key == '2'){
                                    $insert_data['description'] = (!empty($cell_value))?"'".str_replace("'","''",$cell_value)."'":NULL;
                                }elseif($cell_key == '3'){
                                    $insert_data['area'] = (!empty($cell_value))?$cell_value:0;
                                }elseif($cell_key == '4'){
                                    $insert_data['unit'] = (!empty($cell_value))?$cell_value:NULL;
                                }elseif($cell_key == '5'){
                                    $insert_data['lab_rate'] = (!empty($cell_value))?ltrim(trim($cell_value," "),'£'):NULL;
                                }elseif($cell_key == '6'){
                                    $insert_data['total'] = (!empty($cell_value))?ltrim(trim($cell_value," "),'£'):NULL;
                                }elseif($cell_key == '7'){
                                    $insert_data['amount_booked'] = (!empty($cell_value))?"'".ltrim(trim($cell_value," "),'£')."'":NULL;
                                }elseif($cell_key == '8'){
                                    $insert_data['name'] = (!empty($cell_value))?"'".$cell_value."'":NULL;
                                }elseif($cell_key == '9'){
                                    $insert_data['wages'] = (!empty($cell_value))?"'".$cell_value."'":NULL;
                                }elseif($cell_key == '10'){
                                    $insert_data['quantity'] = (!empty($cell_value))?"'".$cell_value."'":NULL;
                                }elseif($cell_key == '11'){
                                    $insert_data['booking_description'] = (!empty($cell_value))?"'".str_replace("'","''",$cell_value)."'":NULL;
                                }elseif($cell_key == '12'){
                                    $insert_data['floor'] = $floor_name??null;
                                }
                                
                            }
                            $insert_data['project_id'] = $project_id;
                            $insert_data['block_id'] = $block_id??1;
                            $insert_data['apartment_id'] = $apartment_id??null;
                            $insert_data['floor_id'] = $floor_id??null;
                            $total_insert [] = $insert_data;
                        }
                       
                       
                    }
                   
                }
                DB::table('construction_details')->insert($total_insert);

                if(($i+1) ==  $sheet_count || $i == 99){
                    ProjectDetails::updatedImportedFlag($project_id);
                    break;
                }
                
            }
        }
       
    }

    function downloadConstructionExcelFile($details,$file)
    {
         //Give our xlsx file a name.
         $xlsxFileName = $file.'_'.date('Y_m_d_H_i_s').'.xlsx';
    
         // Set the Content-Type and Content-Disposition headers.
         //Open file pointer.
         //$fp = fopen('php://output', 'w+');
         $doc = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
         $firstLineKeys = false;
         $uploaded = false;
         $count = 0;
         if(!empty($details)){
             foreach($details as $key => $records){
                 
                  $fp = fopen('php://output', 'w+');
                  //if($count>0){
                    $doc->setActiveSheetIndex($count);
                    $sheet = $doc->createSheet();
                  //}
                  $sheet = $doc->getActiveSheet();
                  
                   
                 //}
              
                 $sheet->setTitle($key);
                 //Loop through the associative array.
                 foreach($records as $i => $row){
                     unset($row['Apartment']);
                 if ($i ==0) {
                     $firstLineKeys = array_keys($row);
                     $j=1;
                     foreach($firstLineKeys as $x_value){
                         $sheet->setCellValueByColumnAndRow($j,1,$x_value);
                         $j=$j+1;
                     }
                 }
                 $j=1;
                 foreach($row as $x => $x_value) {
                     $sheet->setCellValueByColumnAndRow($j,$i+2,$x_value);
                     $j=$j+1;
                 }
                 
                 }
                 // get last row and column for formatting
                 $last_column = $doc->setActiveSheetIndex($count)->getHighestColumn();
                 $last_row = $doc->setActiveSheetIndex($count)->getHighestRow();
     
                 // autosize all columns to content width
                 for ($k = 'A'; $k <= $last_column; $k++) {
                     $doc->getActiveSheet()->getColumnDimension($k)->setAutoSize(TRUE);
                 }
     
                 // if $keys, freeze the header row and make it bold
                 if (!empty($firstLineKeys)) {
                     $doc->getActiveSheet()->freezePane('A2');
                     $doc->getActiveSheet()->getStyle('A1:' . $last_column . '1')->getFont()->setBold(true);
                 }
                 // format all columns as text
                 $doc->getActiveSheet()->getStyle('A2:' . $last_column . $last_row)
                     ->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                 
                 $writer = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($doc);
                 //$writer->save($fp);
                 ob_start();
                 $writer->save($fp);
               $content = ob_get_contents();
               ob_end_clean();
               $count++;
             }
              
                 $uploaded = Storage::disk('construction_data')->put($xlsxFileName, $content); 
             //   $url['url'] = public_path().'/'.$xlsxFileName;
         }
         // $tempImage = tempnam(sys_get_temp_dir(), $xlsxFileName);
         // return $url;
         // fclose($fp);
         if($uploaded){
             $url = env('APP_URL').'/ipl/public/construction_data'.'/'.$xlsxFileName;
         }else{
             $url = env('APP_URL');
         }
         return $url;
    }

    function isEmptyArray($data = []){
        foreach($data as $value){
            if(!empty($value)){
                return true;
            }
        }
        return false;
    }

    function roundOff($number,$upto =2){

        $number=(double)$number;
        $return=round($number,$upto);
        return $return;
    }

?>