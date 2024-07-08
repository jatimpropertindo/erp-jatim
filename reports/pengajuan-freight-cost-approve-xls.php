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
// </editor-fold>

$statusId = $myDatabase->real_escape_string($_POST['statusId']);

$sql = "SELECT pfc.*, CONCAT(f.freight_code, '-', v.vendor_code, ' - ', f.freight_supplier, ' - ', CONCAT(v.vendor_name, ' (', v.vendor_code, ')')) AS freight_full, 
CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full, cur.currency_code, 
CASE WHEN pfc.active_from IS NULL THEN DATE_FORMAT(pfc.entry_date, '%d %b %Y %H:%i:%s')
ELSE DATE_FORMAT(pfc.active_from, '%d %b %Y') END AS active_from,
CASE WHEN pfc.status = 1 THEN DATE_FORMAT(pfc.entry_date, '%d %b %Y %H:%i:%s')
WHEN pfc.status = 2 THEN DATE_FORMAT(pfc.approved_date, '%d %b %Y %H:%i:%s')
 ELSE DATE_FORMAT(pfc.cancel_date, '%d %b %Y %H:%i:%s')  END  AS action_date,
 CASE WHEN pfc.status = 1 THEN DATEDIFF(CURRENT_DATE,pfc.entry_date)
 WHEN pfc.status = 2 THEN DATEDIFF(pfc.approved_date,pfc.entry_date)
 ELSE  DATEDIFF(pfc.cancel_date,pfc.entry_date) END AS aging, 
 DATE_FORMAT(pfc.entry_date, '%d %b %Y %H:%i:%s') as entry_date, u.user_name AS approved_by
FROM pengajuan_freight_cost pfc
INNER JOIN freight f
ON f.freight_id = pfc.freight_id
INNER JOIN vendor v
ON v.vendor_id = pfc.vendor_id
INNER JOIN stockpile s
ON s.stockpile_id = pfc.stockpile_id
INNER JOIN currency cur
ON cur.currency_id = pfc.currency_id
LEFT JOIN freight_cost fc
ON fc.freight_cost_id = pfc.freight_cost_id
LEFT JOIN USER u
ON u.user_id = fc.entry_by
WHERE pfc.status = {$statusId}
ORDER BY pfc.entry_date DESC LIMIT 1000";
$result= $myDatabase->query($sql, MYSQLI_STORE_RESULT);

//</editor-fold>

$fileName = "Pengajuan Freight Cost Approved" . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumn = "I";

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

$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Pengajuan Freight Cost Approved");

$rowActive++;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "No.");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Stockpile");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Freight");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Price/KG");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "Entry Date");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Approve Date");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Approve By");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Active From");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "Remarks");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray4);
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Body">

$no = 1;
while($row = $result->fetch_object()) {
   			
			
    $rowActive++; 
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $no);
	$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $row->stockpile_full);
	$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $row->freight_full);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("D{$rowActive}", $row->price_converted, PHPExcel_Cell_DataType::TYPE_NUMERIC);
    $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $row->entry_date);
    $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $row->approved_date);
    $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $row->approved_by);
	$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $row->active_from);
	$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $row->remarks);  
    $no++;
}
$bodyRowEnd = $rowActive;

//        
for ($temp = ord("A"); $temp <= ord("I"); $temp++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
}
$objPHPExcel->getActiveSheet()->getColumnDimension("I")->setAutoSize(true);

// Set format date in cell
if ($bodyRowEnd > $headerRow) {
    $objPHPExcel->getActiveSheet()->getStyle("E" . ($headerRow + 1) . ":E{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY hh:mm:ss");
    $objPHPExcel->getActiveSheet()->getStyle("F" . ($headerRow + 1) . ":F{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY hh:mm:ss");
    $objPHPExcel->getActiveSheet()->getStyle("H" . ($headerRow + 1) . ":H{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
}

//$objPHPExcel->getActiveSheet()->getStyle("E" . ($headerRow + 1) . ":E{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

// Set number format for Amount 
$objPHPExcel->getActiveSheet()->getStyle("D" . ($headerRow + 1) . ":D{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);


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