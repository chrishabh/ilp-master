<?php

use App\Exceptions\AppException;
use App\Models\ApartmentDetails;
use App\Models\BlockDetails;
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
              $writer = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($doc);
              //$writer->save($fp);
			  ob_start();
			  	$writer->save($fp);
    			$content = ob_get_contents();
    			ob_end_clean();
				$uploaded = Storage::disk('public')->put($xlsxFileName, $content); 
			//   $url['url'] = public_path().'/'.$xlsxFileName;
        }
        // $tempImage = tempnam(sys_get_temp_dir(), $xlsxFileName);
        // return $url;
        // fclose($fp);
        if($uploaded){
            $url = env('APP_URL').'/storage'.'/'.$xlsxFileName;
        }else{
            $url = env('APP_URL');
        }
        return $url;
    }

    function getXlsxFiles($details,$file){
        //Give our xlsx file a name.
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
            $url = env('APP_URL');
        }
        return $url;
    }

    function importExcelToDB($file_path)
    {
        if(!empty($file_path)){
            $excel_data = [];
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $spreadsheet = $reader->load($file_path);
            $sheet_count = $spreadsheet->getSheetCount();
            $insert_data = [];

            for($i=0; $i<$sheet_count; $i++){
                $sheetData = $spreadsheet->getSheet($i)->toArray();
                $key = $key1 = $key2 =0;
                foreach($sheetData as $row_key => $row_data){
                    if($row_key <= '3'){
                        foreach($row_data as $cell_key => $cell_data){ 
                            if(!empty($cell_data) && $cell_data == "Project Name"){
                                $key = $cell_key;
                                $project_name = $row_data[++$key];
                                $project_id = ProjectDetails::getProjectId($project_name);
                            } elseif (!empty($cell_data) && $cell_data == "Block Number"){
                                $key1 = $cell_key;
                                $block_name = $row_data[++$key1];
                                $block_id = BlockDetails::getBlockId($block_name);
                            } elseif (!empty($cell_data) && $cell_data == "Apartment Number"){
                                $key2 = $cell_key;
                                $apartment_name = $row_data[++$key2];
                                $apartment_id = ApartmentDetails::getApartmentId($apartment_name);
                            }
                        }
                    }

                    if($row_key >= '5'){
                        if(!empty($row_data[0])){
                            $main_description_id = MainDescritpion::getMainDescriptionId($row_data[0]);
                        }
                    
                        if(!empty($row_data[1])){
                            $sub_description_id = SubDescritpion::getSubDescriptionId($row_data[1]);
                        }
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
                        if($row_data){
                            foreach($row_data as $cell_key => $cell_value)
                            {
                                if($cell_key == '2'){
                                    $insert_data['description'] = $cell_value;
                                }elseif($cell_key == '3'){
                                    $insert_data['area'] = $cell_value;
                                }elseif($cell_key == '4'){
                                    $insert_data['unit'] = $cell_value;
                                }elseif($cell_key == '5'){
                                    $insert_data['lab_rate'] = $cell_value;
                                }elseif($cell_key == '6'){
                                    $insert_data['total'] = $cell_value;
                                }elseif($cell_key == '7'){
                                    $insert_data['amount_booked'] = $cell_value;
                                }elseif($cell_key == '8'){
                                    $insert_data['name'] = $cell_value;
                                }elseif($cell_key == '9'){
                                    $insert_data['wages'] = $cell_value;
                                }elseif($cell_key == '10'){
                                    $insert_data['quantity'] = $cell_value;
                                }elseif($cell_key == '11'){
                                    $insert_data['booking_description'] = $cell_value;
                                }elseif($cell_key == '12'){
                                    $insert_data['floor'] = $cell_value;
                                }
                                
                            }
                        }
                        $insert_data['project_id'] = $project_id;
                        $insert_data['block_id'] = $block_id;
                        $insert_data['apartment_id'] = $apartment_id;
                        $total_insert [] = $insert_data;
                    }
                   
                }

                DB::table('construction_details')->insert($total_insert);
            }
        }
       
    }

    function roundOff($number,$upto =2){

        $number=(double)$number;
        $return=round($number,$upto);
        return $return;
    }

?>