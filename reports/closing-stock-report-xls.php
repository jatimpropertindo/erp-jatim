<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

require_once PATH_EXTENSION . DS . 'PHPExcel.php';
require_once PATH_EXTENSION . DS . 'PHPExcel/IOFactory.php';
require_once PATH_EXTENSION . DS . 'PHPExcel/Cell/AdvancedValueBinder.php';


// <editor-fold defaultstate="collapsed" desc="Define Style for excel">
$styleArray = array(
    'font' => array(
        'bold' => true
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
    ),
    'borders' => array(
        'top' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        )
    ),
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
        'rotation' => 90,
        'startcolor' => array(
            'argb' => 'FFA0A0A0'
        ),
        'endcolor' => array(
            'argb' => 'FFFFFFFF'
        )
    )
);

$styleArray1 = array(
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
    )
);

$styleArray2 = array(
    'font' => array(
        'bold' => true,
        'size' => 14
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
    )
);

$styleArray3 = array(
    'font' => array(
        'bold' => true
    )
);

$styleArray4 = array(
    'font' => array(
        'bold' => true
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
    ),
    'borders' => array(
        'top' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        ),
        'left' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        ),
        'right' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        ),
        'bottom' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        )
    )
);

$styleArray5 = array(
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
    ),
    'borders' => array(
        'top' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        ),
        'left' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        ),
        'right' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        ),
        'bottom' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        )
    )
);

$styleArray6 = array(
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
    ),
    'borders' => array(
        'top' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        ),
        'left' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        ),
        'right' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        ),
        'bottom' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        )
    )
);

$styleArray7 = array(
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    ),
    'borders' => array(
        'top' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        ),
        'left' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        ),
        'right' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        ),
        'bottom' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        )
    )
);

$styleArray8 = array(
    'font' => array(
        'bold' => true
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
    )
);
$styleArray9 = array(
    'font' => array(
        'bold' => true,
		'color' => array('rgb' => 'FF0000')
    )
);
// </editor-fold>

$whereProperty = '';
$sumProperty = '';
$balanceBefore = 0;
$boolBalanceBefore = false;
//$stockpileId = $myDatabase->real_escape_string($_POST['stockpileId']);
//$stockpileIds = $_POST['stockpileId'];
$periodFrom = $myDatabase->real_escape_string($_POST['periodFrom']);
$periodTo = $myDatabase->real_escape_string($_POST['periodTo']);
// $stockpileName = 'All ';
$periodFull = '';
$tempTest = 0;
$tempTest2 = 0;

// <editor-fold defaultstate="collapsed" desc="Query">

/*if ($stockpileIds !== '') {
   // $stockpileId = $_POST['stockpileId'];
    $stockpile_name = array();
	$stockpile_code = array();
	$stockpileNames = '';
	$stockpileCodes = '';
    $sql = "SELECT stockpile_code, stockpile_name FROM stockpile WHERE stockpile_id IN ({$stockpileIds})";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
	if($result !== false && $result->num_rows > 0){
		while($row = mysqli_fetch_array($result)){
		$stockpile_name[] = $row['stockpile_name'];
		$stockpile_code[] = $row['stockpile_code'];
		

				
	$stockpileNames =  "'" . implode("','", $stockpile_name) . "'";	
	$stockpileCodes =  "'" . implode("','", $stockpile_code) . "'";		
	
	}
}
    
    $whereProperty .= " AND SUBSTRING(t.slip_no,1,3) IN ({$stockpileCodes}) ";
    $sumProperty .= " AND SUBSTRING(t.slip_no,1,3) IN ({$stockpileCodes}) ";
    
//    $whereProperty .= " AND t.slip_no like '{$stockpileId}%' ";
//    $sumProperty .= " AND t.slip_no like '{$stockpileId}%' ";

//    $sql = "SELECT * FROM stockpile WHERE stockpile_id = {$stockpileId}";
//    $resultStockpile = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
//    $rowStockpile = $resultStockpile->fetch_object();
    //$stockpileName = $row->stockpile_name . " ";
}*/

if ($periodFrom != '' && $periodTo != '') {
    /*$whereProperty .= " AND IF(t.transaction_type = 1, t.unloading_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y'), t.transaction_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y')) ";
   	//$whereProperty .= " AND t.notim_status = 0 AND t.transaction_type = 1 AND  t.transaction_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
    $sumProperty .= " AND IF(t.transaction_type = 1, t.unloading_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
    $boolBalanceBefore = true;*/
    $periodFull = $periodFrom . " - " . $periodTo . " ";
} else if ($periodFrom != '' && $periodTo == '') {
    /*$whereProperty .= " AND IF(t.transaction_type = 1, t.unloading_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
   //	$whereProperty .= " AND t.notim_status = 0 AND t.transaction_type = 1 AND t.transaction_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')";
    $sumProperty .= " AND IF(t.transaction_type = 1, t.unloading_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
    $boolBalanceBefore = true;*/
    $periodFull = "From " . $periodFrom . " ";
} else if ($periodFrom == '' && $periodTo != '') {
    //$whereProperty .= " AND IF(t.transaction_type = 1, t.unloading_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y'), t.transaction_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y')) ";
   	//$whereProperty .= " AND t.notim_status = 0 AND t.transaction_type = 1 AND t.transaction_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";
   $periodFull = "To " . $periodTo . " ";
}
/*
if($stockpileIds != ''){
    $sql = "SELECT stockpile_name FROM stockpile WHERE stockpile_code = '$stockpileIds'";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
	if($result !== false && $result->num_rows > 0){
		while($row = mysqli_fetch_array($result)){

            $stockpileName = $row->stockpile_name;

       }
    }
}*/

$sql = "Call SP_notimReportSummary('{$periodFrom}','{$periodTo}')";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
// echo $sql;
// die();

//</editor-fold>

$fileName = "Closing Stock Report" . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumn = "F";

// <editor-fold defaultstate="collapsed" desc="Create Excel and Define Header">
$objPHPExcel = new PHPExcel();
PHPExcel_Cell::setValueBinder(new PHPExcel_Cell_AdvancedValueBinder());

$objPHPExcel->setActiveSheetIndex($onSheet);
$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(75);
$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');
$objPHPExcel->getDefaultStyle()->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(15);

$rowActive = 1;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray1);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Print Date: " . date("d F Y"));

/*
if ($stockpileName != "") {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Stockpile = {$stockpileName}");
}*/

if ($periodFull != "") {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Period = {$periodFull}");
}


$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Closing Stock Report (Kg)");

$rowActive++;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "STOCKPILE");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "GGL");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "ISCC");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "GGL + ISCC");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "NON CERTIFIED");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "TOTAL");



$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray4);
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Body">
/*if($boolBalanceBefore) {
    $sql2 = "SELECT CASE WHEN t.transaction_type = 1 THEN t.quantity ELSE -1*t.send_weight END AS quantity2
                        FROM transaction t
                        LEFT JOIN stockpile_contract sc
                            ON sc.stockpile_contract_id = t.stockpile_contract_id
                        WHERE 1=1 {$sumProperty}";
    $result2 = $myDatabase->query($sql2, MYSQLI_STORE_RESULT);

    if($result2->num_rows > 0) {
        while($row2 = $result2->fetch_object()) {
            $balanceBefore = $balanceBefore + $row2->quantity2;
        }
        
        $rowActive++;
        $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("T{$rowActive}", "");
       
    }
}*/

    $balanceQuantity = $balanceBefore;
            $no = 1;
            while($row = $result->fetch_object()) {
                //$balanceQuantity = $balanceQuantity + $row->quantity2;
				
			/*	if($row->transaction_type == 2){
					if($row->quantity < 0){
						$quantity = $row->quantity * -1;
					}else{
						$quantity = '-' .$row->quantity;
					}
				}else{
					$quantity = $row->quantity;
				}*/

           
    $rowActive++;
    
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $row->stockpile_name);
	$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $row->ggl);
    $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $row->rsb);
    $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $row->rsb_ggl);
    $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $row->uncertified);
    $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $row->total);
    
    $objPHPExcel->getActiveSheet()->getStyle("F{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray3);
  
	

	//$objPHPExcel->getActiveSheet()->setCellValue("AQ{$rowActive}", $row->entry_date);

    $no++;
            }

$bodyRowEnd = $rowActive;

$objPHPExcel->getActiveSheet()->getStyle("A{$bodyRowEnd}:{$lastColumn}{$bodyRowEnd}")->applyFromArray($styleArray3);



// <editor-fold defaultstate="collapsed" desc="Formating Excel">
// Set column width
for ($temp = ord("A"); $temp <= ord("F"); $temp++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
}
$objPHPExcel->getActiveSheet()->getColumnDimension("F")->setAutoSize(true);

// Set format date in cell
if ($bodyRowEnd > $headerRow) {
    //$objPHPExcel->getActiveSheet()->getStyle("D" . ($headerRow + 1) . ":D{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
    //$objPHPExcel->getActiveSheet()->getStyle("G" . ($headerRow + 1) . ":G{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
}

//$objPHPExcel->getActiveSheet()->getStyle("E" . ($headerRow + 1) . ":E{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

// Set number format for Amount 
$objPHPExcel->getActiveSheet()->getStyle("B" . ($headerRow + 1) . ":F{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
 //$objPHPExcel->getActiveSheet()->getStyle("M" . ($headerRow + 1) . ":N{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

// Set border for table
$objPHPExcel->getActiveSheet()->getStyle("A" . ($headerRow) . ":{$lastColumn}{$bodyRowEnd}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Save Excel and return to browser">
ob_end_clean();
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="' . $fileName . '"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
// </editor-fold>
exit();