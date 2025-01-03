<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
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
    'font' => array(
        'bold' => true
    ),
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

$whereProperty = '';
$shipmentId = $myDatabase->real_escape_string($_POST['shipmentId']);
$periodFrom = $myDatabase->real_escape_string($_POST['periodFrom']);
$periodTo = $myDatabase->real_escape_string($_POST['periodTo']);
$dateField = '';
$shipmentCode = 'All ';
//$stockpileId = $myDatabase->real_escape_string($_POST['stockpileId']);
// <editor-fold defaultstate="collapsed" desc="Query">


if($shipmentId != '') {
    $whereProperty = " AND sh.sales_id = {$shipmentId} ";
    
      $sql = "SELECT sh.*,s.stockpile_code, s.stockpile_name, sh.shipment_date FROM shipment sh INNER JOIN sales sl ON sh.sales_id = sl.sales_id INNER JOIN stockpile s ON sl.stockpile_id = s.stockpile_id  WHERE sh.sales_id = {$shipmentId}";
    $resultShipment = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    $rowShipment = $resultShipment->fetch_object();
    $shipmentCode = $rowShipment->shipment_no . " ";
	$stockpileCode = $rowShipment->stockpile_code . " - ";
	$stockpileName = $rowShipment->stockpile_name;
	$shipmentDate = $rowShipment->shipment_date;
}
/*if($stockpileId != '') {
    $whereProperty = " AND s.stockpile_id = {$stockpileId} ";
  */  
   // $sql = "SELECT * FROM shipment WHERE shipment_id = {$shipmentId}";
   // $resultShipment = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
   // $rowShipment = $resultShipment->fetch_object();
   // $shipmentCode = $rowShipment->shipment_code . " ";
//}
if ($periodFrom != '' && $periodTo != '') {
    $whereProperty .= " AND sh.shipment_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
    $periodFull = $periodFrom . " - " . $periodTo . " ";
} else if ($periodFrom != '' && $periodTo == '') {
    $whereProperty .= " AND sh.shipment_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";
    $periodFull = "From " . $periodFrom . " ";
} else if ($periodFrom == '' && $periodTo != '') {
    $whereProperty .= " AND sh.shipment_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
} 
$sql2 = "SELECT d.*,
            CASE WHEN t.transaction_type = 1 THEN s.stockpile_name ELSE s2.stockpile_name END AS stockpile_name,  
            DATE_FORMAT(t.unloading_date, '%d %b %Y') AS transaction_date2,
            t.slip_no, 
            CASE WHEN con.contract_type = 'P' THEN 'PKS' ELSE 'Curah' END AS contract_type2,
            con.po_no, 
            CONCAT(f.freight_code, '-', v2.vendor_code) AS freight_code,
            v3.vendor_name AS supplier,
            v1.vendor_name, 
            sh.shipment_code,
            t.send_weight, t.netto_weight, d.quantity, d.qty_rsb, d.qty_ggl, d.qty_rsb_ggl, d.qty_uncertified,
			CASE WHEN t.mutasi_id IS NOT NULL AND t.mutasi_id <> 0 THEN t.unit_cost
			WHEN t.adjustmentAudit_id IS NOT NULL THEN t.unit_price
			ELSE con.price_converted END AS price_converted,
           CASE WHEN d.qty_rsb <> 0 THEN(CASE WHEN t.mutasi_id IS NOT NULL AND t.mutasi_id <> 0 THEN d.qty_rsb * t.unit_cost
			WHEN t.adjustmentAudit_id IS NOT NULL THEN d.qty_rsb * t.unit_price
			ELSE d.qty_rsb * con.price_converted END )ELSE 0 END AS cogs_amountR,
		   CASE WHEN d.qty_ggl <> 0 THEN(CASE WHEN t.mutasi_id IS NOT NULL AND t.mutasi_id <> 0 THEN d.qty_ggl * t.unit_cost
				WHEN t.adjustmentAudit_id IS NOT NULL THEN d.qty_ggl * t.unit_price
				ELSE d.qty_ggl * con.price_converted END )ELSE 0 END AS cogs_amountG,
		   CASE WHEN d.qty_rsb_ggl <> 0 THEN(CASE WHEN t.mutasi_id IS NOT NULL AND t.mutasi_id <> 0 THEN d.qty_rsb_ggl * t.unit_cost
				WHEN t.adjustmentAudit_id IS NOT NULL THEN d.qty_rsb_ggl * t.unit_price
				ELSE d.qty_rsb_ggl * con.price_converted END )ELSE 0 END AS cogs_amountRG,
		   CASE WHEN d.qty_uncertified <> 0 THEN(CASE WHEN t.mutasi_id IS NOT NULL AND t.mutasi_id <> 0 THEN d.quantity * t.unit_cost
				WHEN t.adjustmentAudit_id IS NOT NULL THEN d.quantity * t.unit_price
				ELSE d.quantity * con.price_converted END )ELSE 0 END AS cogs_amountUN,
				t.freight_quantity, t.freight_price, 
			CASE WHEN d.qty_rsb <> 0 THEN (CASE WHEN t.delivery_status = 2 THEN (d.percent_taken / 100) * (t.quantity * t.freight_price)
				ELSE (d.percent_taken / 100) * (t.freight_quantity * t.freight_price) 
			END ) ELSE 0 END AS freight_totalR,
			CASE WHEN d.qty_ggl <> 0 THEN (CASE WHEN t.delivery_status = 2 THEN (d.percent_taken / 100) * (t.quantity * t.freight_price)
				ELSE (d.percent_taken / 100) * (t.freight_quantity * t.freight_price) 
			END ) ELSE 0 END AS freight_totalG,
			CASE WHEN d.qty_rsb_ggl <> 0 THEN (CASE WHEN t.delivery_status = 2 THEN (d.percent_taken / 100) * (t.quantity * t.freight_price)
				ELSE (d.percent_taken / 100) * (t.freight_quantity * t.freight_price) 
			END ) ELSE 0 END AS freight_totalRG,
			CASE WHEN d.qty_uncertified <> 0 THEN (CASE WHEN t.delivery_status = 2 THEN (d.percent_taken / 100) * (t.quantity * t.freight_price)
				ELSE (d.percent_taken / 100) * (t.freight_quantity * t.freight_price) 
			END ) ELSE 0 END AS freight_totalUN,
			CASE WHEN d.qty_rsb <> 0 THEN (CASE WHEN t.delivery_status = 2 AND t.freight_cost_id IS NOT NULL THEN (d.qty_rsb/t.freight_quantity) * COALESCE((SELECT amt_claim FROM transaction_shrink_weight WHERE transaction_id = d.transaction_id),0)
			 WHEN t.freight_cost_id IS NOT NULL THEN (d.percent_taken / 100) * COALESCE((SELECT amt_claim FROM transaction_shrink_weight WHERE transaction_id = d.transaction_id),0) ELSE 0 END 
			) ELSE 0 END AS freight_shrinkR,
			CASE WHEN d.qty_ggl <> 0 THEN (CASE WHEN t.delivery_status = 2 AND t.freight_cost_id IS NOT NULL THEN (d.qty_ggl/t.freight_quantity) * COALESCE((SELECT amt_claim FROM transaction_shrink_weight WHERE transaction_id = d.transaction_id),0)
			 WHEN t.freight_cost_id IS NOT NULL THEN (d.percent_taken / 100) * COALESCE((SELECT amt_claim FROM transaction_shrink_weight WHERE transaction_id = d.transaction_id),0) ELSE 0 END 
			) ELSE 0 END AS freight_shrinkG,
			CASE WHEN d.qty_rsb_ggl <> 0 THEN (CASE WHEN t.delivery_status = 2 AND t.freight_cost_id IS NOT NULL THEN (d.qty_rsb_ggl/t.freight_quantity) * COALESCE((SELECT amt_claim FROM transaction_shrink_weight WHERE transaction_id = d.transaction_id),0)
			 WHEN t.freight_cost_id IS NOT NULL THEN (d.percent_taken / 100) * COALESCE((SELECT amt_claim FROM transaction_shrink_weight WHERE transaction_id = d.transaction_id),0) ELSE 0 END 
			) ELSE 0 END AS freight_shrinkRG,
			CASE WHEN d.qty_uncertified <> 0 THEN (CASE WHEN t.delivery_status = 2 AND t.freight_cost_id IS NOT NULL THEN (d.quantity/t.freight_quantity) * COALESCE((SELECT amt_claim FROM transaction_shrink_weight WHERE transaction_id = d.transaction_id),0)
			 WHEN t.freight_cost_id IS NOT NULL THEN (d.percent_taken / 100) * COALESCE((SELECT amt_claim FROM transaction_shrink_weight WHERE transaction_id = d.transaction_id),0) ELSE 0 END 
			) ELSE 0 END AS freight_shrinkUN,
			
			CASE WHEN d.qty_uncertified <> 0 THEN (CASE WHEN t.delivery_status = 2 AND t.freight_cost_id IS NOT NULL AND t.`transaction_date` > '2022-09-15' THEN (d.quantity/t.freight_quantity) * COALESCE((SELECT amt_claim FROM transaction_additional_shrink WHERE transaction_id = d.transaction_id),0)
WHEN t.freight_cost_id IS NOT NULL AND t.`transaction_date` > '2022-09-15' THEN (d.percent_taken / 100) * COALESCE((SELECT amt_claim FROM transaction_additional_shrink WHERE transaction_id = d.transaction_id),0) ELSE 0 END) ELSE 0 END AS freight_shrinkUN2,


				t.unloading_price, 
				CASE WHEN d.qty_rsb <> 0 THEN ((d.percent_taken / 100) * t.unloading_price) ELSE 0 END AS unloading_totalR,
				CASE WHEN d.qty_ggl <> 0 THEN ((d.percent_taken / 100) * t.unloading_price) ELSE 0 END AS unloading_totalG,
				CASE WHEN d.qty_rsb_ggl <> 0 THEN ((d.percent_taken / 100) * t.unloading_price) ELSE 0 END AS unloading_totalRG,
				CASE WHEN d.qty_uncertified <> 0 THEN ((d.percent_taken / 100) * t.unloading_price) ELSE 0 END AS unloading_totalUN,
				t.handling_price AS vh_price, t.handling_quantity,
			CASE WHEN d.qty_rsb <> 0 THEN (CASE WHEN t.delivery_status = 2 THEN (d.percent_taken / 100) * (t.handling_quantity * t.handling_price)
			 ELSE (d.percent_taken / 100) * (t.handling_quantity * t.handling_price) END 
			 ) ELSE 0 END AS handling_totalR,
			 CASE WHEN d.qty_ggl <> 0 THEN (CASE WHEN t.delivery_status = 2 THEN (d.percent_taken / 100) * (t.handling_quantity * t.handling_price)
			 ELSE (d.percent_taken / 100) * (t.handling_quantity * t.handling_price) END 
			 ) ELSE 0 END AS handling_totalG,
			CASE WHEN d.qty_rsb_ggl <> 0 THEN (CASE WHEN t.delivery_status = 2 THEN (d.percent_taken / 100) * (t.handling_quantity * t.handling_price)
			 ELSE (d.percent_taken / 100) * (t.handling_quantity * t.handling_price) END 
			 ) ELSE 0 END AS handling_totalRG,
			  CASE WHEN d.qty_uncertified > 0 THEN (CASE WHEN t.delivery_status = 2 THEN (d.percent_taken / 100) * (t.handling_quantity * t.handling_price)
			 ELSE (d.percent_taken / 100) * (t.handling_quantity * t.handling_price) END 
			 ) ELSE 0 END AS handling_totalUN,
			vh1.pph_tax_id AS vh_pph_tax_id, vh1.pph AS vh_pph, vhtx.tax_category AS vh_pph_tax_category,
				f.ppn_tax_id AS fc_ppn_tax_id, f.ppn AS fc_ppn, fctxppn.tax_category AS fc_ppn_tax_category,
				t.fc_tax_id AS fc_pph_tax_id, fctxpph.tax_value AS fc_pph, fctxpph.tax_category AS fc_pph_tax_category,
				l.ppn_tax_id AS uc_ppn_tax_id, l.ppn AS uc_ppn, uctxppn.tax_category AS uc_ppn_tax_category,
				l.pph_tax_id AS uc_pph_tax_id, l.pph AS uc_pph, uctxpph.tax_category AS uc_pph_tax_category,
				l.labor_id,t.freight_cost_id, f.freight_id,
		(SELECT slip_no FROM TRANSACTION WHERE notim_status != 1 AND slip_retur IS NULL AND shipment_id = (SELECT shipment_id FROM shipment WHERE sales_id = sl.sales_id ORDER BY shipment_id ASC LIMIT 1)LIMIT 1) AS slipOut,
		(SELECT transaction_date FROM TRANSACTION WHERE notim_status != 1 AND slip_retur IS NULL AND shipment_id = (SELECT shipment_id FROM shipment WHERE sales_id = sl.sales_id ORDER BY shipment_id ASC LIMIT 1)LIMIT 1) AS transactionDate,
		(SELECT SUBSTRING(slip_no,1,3) FROM TRANSACTION WHERE notim_status != 1 AND slip_retur IS NULL AND shipment_id = (SELECT shipment_id FROM shipment WHERE sales_id = sl.sales_id ORDER BY shipment_id ASC LIMIT 1)LIMIT 1) AS slipOutCode,
		(SELECT vehicle_no FROM TRANSACTION WHERE notim_status != 1 AND slip_retur IS NULL AND shipment_id = (SELECT shipment_id FROM shipment WHERE sales_id = sl.sales_id ORDER BY shipment_id ASC LIMIT 1)LIMIT 1) AS vessel_name, sh.shipment_no
        FROM delivery d
        LEFT JOIN `transaction` t
        	ON t.transaction_id = d.transaction_id
        LEFT JOIN stockpile_contract sc
            ON sc.stockpile_contract_id = t.stockpile_contract_id
        LEFT JOIN stockpile s
            ON s.stockpile_id = sc.stockpile_id
        LEFT JOIN contract con
            ON con.contract_id = sc.contract_id
        LEFT JOIN vendor v1
            ON v1.vendor_id = con.vendor_id
        LEFT JOIN unloading_cost uc
            ON uc.unloading_cost_id = t.unloading_cost_id
        LEFT JOIN vehicle vh
            ON vh.vehicle_id = uc.vehicle_id
        LEFT JOIN freight_cost fc
            ON fc.freight_cost_id = t.freight_cost_id
        LEFT JOIN freight f
            ON f.freight_id = fc.freight_id
        LEFT JOIN vendor v2
            ON v2.vendor_id = fc.vendor_id
        LEFT JOIN vendor v3
            ON v3.vendor_id = t.vendor_id
        LEFT JOIN shipment sh
            ON sh.shipment_id = d.shipment_id
        LEFT JOIN sales sl
            ON sl.sales_id = sh.sales_id
        LEFT JOIN stockpile s2
            ON s2.stockpile_id = sl.stockpile_id
        LEFT JOIN customer cust
            ON cust.customer_id = sl.customer_id
        LEFT JOIN tax fctxpph
	        ON fctxpph.tax_id = t.fc_tax_id
        LEFT JOIN tax fctxppn
	        ON fctxppn.tax_id = f.ppn_tax_id
	    LEFT JOIN labor l
            ON l.labor_id = t.labor_id
	    LEFT JOIN tax uctxpph
	        ON uctxpph.tax_id = l.pph_tax_id
        LEFT JOIN tax uctxppn
	        ON uctxppn.tax_id = l.ppn_tax_id	
		LEFT JOIN vendor_handling_cost vhc
			ON vhc.handling_cost_id = t.handling_cost_id
		LEFT JOIN vendor_handling vh1
			ON vh1.vendor_handling_id = vhc.vendor_handling_id
		LEFT JOIN tax vhtx
			ON vh1.pph_tax_id = vhtx.tax_id
        WHERE 1=1 
        AND sl.company_id = {$_SESSION['companyId']}
        {$whereProperty} ORDER BY d.`transaction_id` ASC";
$result2 = $myDatabase->query($sql2, MYSQLI_STORE_RESULT);


            


// </editor-fold>

$fileName = $stockpileCode . "COGS Report " . $shipmentCode . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumn = "W";

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

if ($shipmentCode != "") {
 
//RSB
$rowActive++;
	$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}")->applyFromArray($styleArray4);
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}","Stockpile");
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->applyFromArray($styleArray4);
	$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}",$stockpileName);
	
	//GGL	
	$objPHPExcel->getActiveSheet()->getStyle("C{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("C{$rowActive}")->applyFromArray($styleArray4);
	$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}",$stockpileName);
	
	//RSB + GGL
	$objPHPExcel->getActiveSheet()->getStyle("D{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("D{$rowActive}")->applyFromArray($styleArray4);
	$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}",$stockpileName);
	
	//Uncertified
	$objPHPExcel->getActiveSheet()->getStyle("E{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("E{$rowActive}")->applyFromArray($styleArray4);
	$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}",$stockpileName);
	
	//TOTAL//Uncertified
	$objPHPExcel->getActiveSheet()->getStyle("F{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("F{$rowActive}")->applyFromArray($styleArray4);
	$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}",$stockpileName);

    //$objPHPExcel->getActiveSheet()->mergeCells("C{$rowActive}:{$lastColumn}{$rowActive}");
$rowActive++;
	$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}")->applyFromArray($styleArray4);
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}","Certification");
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->applyFromArray($styleArray4);
	$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}","RSB");
    //$objPHPExcel->getActiveSheet()->mergeCells("C{$rowActive}:{$lastColumn}{$rowActive}");
	$objPHPExcel->getActiveSheet()->getStyle("C{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("C{$rowActive}")->applyFromArray($styleArray4);
	$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}","GGL");
	
	$objPHPExcel->getActiveSheet()->getStyle("D{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("D{$rowActive}")->applyFromArray($styleArray4);
	$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}","RSB + GGL");
	
	$objPHPExcel->getActiveSheet()->getStyle("E{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("E{$rowActive}")->applyFromArray($styleArray4);
	$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}","Uncertified");
	
	$objPHPExcel->getActiveSheet()->getStyle("F{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("F{$rowActive}")->applyFromArray($styleArray4);
	$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}","TOTAL");

$rowActive++;
	$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}")->applyFromArray($styleArray4);
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}","Shipment Code");
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->applyFromArray($styleArray4);
	$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}",$shipmentCode);
   // $objPHPExcel->getActiveSheet()->mergeCells("C{$rowActive}:{$lastColumn}{$rowActive}");
   	$objPHPExcel->getActiveSheet()->getStyle("C{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("C{$rowActive}")->applyFromArray($styleArray4);
	$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}",$shipmentCode);
	
	//RSB + GGL 	
	$objPHPExcel->getActiveSheet()->getStyle("D{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("D{$rowActive}")->applyFromArray($styleArray4);
	$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}",$shipmentCode);
	
	//Uncertified
	 	$objPHPExcel->getActiveSheet()->getStyle("E{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("E{$rowActive}")->applyFromArray($styleArray4);
	$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}",$shipmentCode);
	
	//TOTAL 	
	$objPHPExcel->getActiveSheet()->getStyle("F{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("F{$rowActive}")->applyFromArray($styleArray4);
	$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}",$shipmentCode);

$rowActive++;
	$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}")->applyFromArray($styleArray4);
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}","Shipment Date");
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->applyFromArray($styleArray4);
	$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}",$shipmentDate);
  //  $objPHPExcel->getActiveSheet()->mergeCells("C{$rowActive}:{$lastColumn}{$rowActive}");
  	$objPHPExcel->getActiveSheet()->getStyle("C{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("C{$rowActive}")->applyFromArray($styleArray4);
	$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}",$shipmentDate);
	
	//RSB + GGL
	$objPHPExcel->getActiveSheet()->getStyle("D{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("D{$rowActive}")->applyFromArray($styleArray4);
	$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}",$shipmentDate);
	
	//Uncertified
	$objPHPExcel->getActiveSheet()->getStyle("E{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("E{$rowActive}")->applyFromArray($styleArray4);
	$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}",$shipmentDate);
	//TOTAL
	$objPHPExcel->getActiveSheet()->getStyle("F{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("F{$rowActive}")->applyFromArray($styleArray4);
	$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}",$shipmentDate);
}

$AmountTotalQty = 0; $AmountTotalPKS = 0; $AmountTotalFC = 0; $AmountTotalVH = 0; $AmountTotalUC = 0; $AmountTotalCOGS = 0;
$AmountTotalB = 0; $AmountTotalE = 0;
    while($row2 = $result2->fetch_object()) {
			$value='';
			$no1=1;
			
			$slipOut = $row2->slipOut;
			$shipmentNo = $row2->shipment_no;
			$slipOutCode = $row2->slipOutCode;
			$transactionDate = $row2->transactionDate;
				
				if($row2->slip_no >= 'SAM-0000000001' && $row2->slip_no <= 'SAM-0000001925'){
				$fc_pph2 = 4 ;
			}else{
				$fc_pph2	 = $row2->fc_pph;
			}
			
			if($row2->slip_no >= 'MAR-0000000001' && $row2->slip_no <= 'MAR-0000007138'){
				$fc_pph2 = 4 ;
			}else{
				$fc_pph2	 = $row2->fc_pph;
			}
			
			if($row2->vh_pph_tax_category == 1 && $row2->vh_pph_tax_id != ''){
			        // $pphvh2 = ($row2->handling_total / ((100 - $row2->vh_pph) / 100)) - $row2->handling_total;
					 $pphvh2R = ($row2->handling_totalR / ((100 - $row2->vh_pph) / 100)) - $row2->handling_totalR;
					 $pphvh2G = ($row2->handling_totalG / ((100 - $row2->vh_pph) / 100)) - $row2->handling_totalG;
					 $pphvh2RG = ($row2->handling_totalRG / ((100 - $row2->vh_pph) / 100)) - $row2->handling_totalRG;
					 $pphvh2UN = ($row2->handling_totalUN / ((100 - $row2->vh_pph) / 100)) - $row2->handling_totalUN;
				 
				 }elseif($row2->vh_pph_tax_category == 0 && $row2->vh_pph_tax_id != ''){
					 // $pphvh2 =  0;  
					  $pphvh2R =  0;
					  $pphvh2G =  0;
					  $pphvh2RG =  0;
					  $pphvh2UN =  0;
					 //$pphfc =  $row->freight_total - ($row->freight_total * ((100 - $fc_pph) / 100));
				 }else{
				 	 $pphvh2R =  0;
					 $pphvh2G =  0;
					 $pphvh2RG =  0;
					 $pphvh2UN =  0;
				 }
				 
				 //$handlingTotal2 = $row2->handling_total - $pphvh2;	
				 $handlingTotal2R = $row2->handling_totalR - $pphvh2R;	
				 $handlingTotal2G = $row2->handling_totalG - $pphvh2G;	
				 $handlingTotal2RG = $row2->handling_totalRG - $pphvh2RG;	
				 $handlingTotal2UN = $row2->handling_totalUN - $pphvh2UN;	
				
               
		         if($row2->fc_pph_tax_category == 1 && $row2->fc_pph_tax_id != ''){
			        // $pphfc2 = ($row2->freight_total / ((100 - $fc_pph2) / 100)) - $row2->freight_total;
					 $pphfc2R = ($row2->freight_totalR / ((100 - $fc_pph2) / 100)) - $row2->freight_totalR;
					 $pphfc2G = ($row2->freight_totalG / ((100 - $fc_pph2) / 100)) - $row2->freight_totalG;
					 $pphfc2RG = ($row2->freight_totalRG / ((100 - $fc_pph2) / 100)) - $row2->freight_totalRG;
					 $pphfc2UN = ($row2->freight_totalUN / ((100 - $fc_pph2) / 100)) - $row2->freight_totalUN;
					 
					 //$pphfcShrink2 = ($row2->freight_shrink / ((100 - $fc_pph2) / 100)) - $row2->freight_shrink;
					 $pphfcShrink2R = ($row2->freight_shrinkR / ((100 - $fc_pph2) / 100)) - $row2->freight_shrinkR;
					 $pphfcShrink2G = ($row2->freight_shrinkG / ((100 - $fc_pph2) / 100)) - $row2->freight_shrinkG;
					 $pphfcShrink2RG = ($row2->freight_shrinkRG / ((100 - $fc_pph2) / 100)) - $row2->freight_shrinkRG;
					 $pphfcShrink2UN = ($row2->freight_shrinkUN / ((100 - $fc_pph2) / 100)) - $row2->freight_shrinkUN;
					 $pphfcShrink2UN = ($row2->freight_shrinkUN2 / ((100 - $fc_pph2) / 100)) - $row2->freight_shrinkUN2;
				 
				 }elseif($row2->fc_pph_tax_category == 0 && $row2->fc_pph_tax_id != ''){
					  $pphfc2R =  0;
						$pphfcShrink2R = 0;
						 $pphfc2G =  0;
						$pphfcShrink2G = 0;
						$pphfc2RG =  0;
						$pphfcShrink2RG = 0;
						$pphfc2UN =  0;
						$pphfcShrink2UN = 0;
						$pphfcShrink2UN2 = 0;
					 //$pphfc =  $row->freight_total - ($row->freight_total * ((100 - $fc_pph) / 100));
				 }else{
				 	$pphfc2R = 0;
					$pphfcShrink2R = 0;
					$pphfc2G = 0;
					$pphfcShrink2G = 0;
					$pphfc2RG = 0;
					$pphfcShrink2RG = 0;
					$pphfc2UN = 0;
					$pphfcShrink2UN = 0;
					$pphfcShrink2UN2 = 0;
				 }
				 /*
				 if($row->fc_ppn_tax_id != ''){
					 $ppnfc = ($row->freight_total * ((100 + $row->fc_ppn) / 100)) - $row->freight_total;
				 }else{
				     $ppnfc = 0;
			     }*/
				 
				// $freightTotal2 = ($row2->freight_total + $ppnfc2 + $pphfc2) - ($row2->freight_shrink + $pphfcShrink2);	
				 $freightTotal2R = ($row2->freight_totalR + $ppnfc2R + $pphfc2R) - ($row2->freight_shrinkR + $pphfcShrink2R);
				$freightTotal2G = ($row2->freight_totalG + $ppnfc2G + $pphfc2G) - ($row2->freight_shrinkG + $pphfcShrink2G);
				$freightTotal2RG = ($row2->freight_totalRG + $ppnfc2RG + $pphfc2RG) - ($row2->freight_shrinkRG + $pphfcShrink2RG);	
				$freightTotal2UN = ($row2->freight_totalUN + $ppnfc2UN + $pphfc2UN) - ($row2->freight_shrinkUN + $pphfcShrink2UN) - ($row2->freight_shrinkUN2 + $pphfcShrink2UN2);					
				 
				 
				 if($row2->uc_pph_tax_category == 1 &&$row2->uc_pph_tax_id != ''){
			         //$pphuc2 = ($row2->unloading_total / ((100 - $row2->uc_pph) / 100)) - $row2->unloading_total;
					 $pphuc2R = ($row2->unloading_totalR / ((100 - $row2->uc_pph) / 100)) - $row2->unloading_totalR;
					 $pphuc2G = ($row2->unloading_totalG / ((100 - $row2->uc_pph) / 100)) - $row2->unloading_totalG;
					 $pphuc2RG = ($row2->unloading_totalRG / ((100 - $row2->uc_pph) / 100)) - $row2->unloading_totalRG;
					 $pphuc2UN = ($row2->unloading_totalUN / ((100 - $row2->uc_pph) / 100)) - $row2->unloading_totalUN;
					 
				 }elseif($row2->uc_pph_tax_category == 0 && $row2->uc_pph_tax_id != ''){
					// $pphuc2 =  0;
					 $pphuc2R =  0;
					 $pphuc2G =  0;
					 $pphuc2RG =  0;
					 $pphuc2RG =  0;
					 //$pphuc =  $row->unloading_total - ($row->unloading_total * ((100 - $row->uc_pph) / 100));
				 }else{
				 	$pphuc2R =  0;
					$pphuc2G =  0;
					$pphuc2RG =  0;
					$pphuc2RG =  0;
				 }
				 
				// $unloadingTotal2 = $row2->unloading_total + $ppnuc2 + $pphuc2;
				$unloadingTotal2R = $row2->unloading_totalR + $ppnuc2R + $pphuc2R;
				$unloadingTotal2G = $row2->unloading_totalG + $ppnuc2G + $pphuc2G;
				$unloadingTotal2RG = $row2->unloading_totalRG + $ppnuc2RG + $pphuc2RG;
				$unloadingTotal2UN = $row2->unloading_totalUN + $ppnuc2UN + $pphuc2UN;					
    
    // $totalCogs2 = $row2->cogs_amount + $freightTotal2 + $unloadingTotal2 + $handlingTotal2;
	 $totalCogs2R = $row2->cogs_amountR + $freightTotal2R + $unloadingTotal2R + $handlingTotal2R;
	 $totalCogs2G = $row2->cogs_amountG + $freightTotal2G + $unloadingTotal2G + $handlingTotal2G;
	 $totalCogs2RG = $row2->cogs_amountRG + $freightTotal2RG + $unloadingTotal2RG + $handlingTotal2RG;
	 $totalCogs2UN = $row2->cogs_amountUN + $freightTotal2UN + $unloadingTotal2UN + $handlingTotal2UN;
	 
	 //$quantity_total= $row2->quantity;
	 $quantity_totalR= $row2->qty_rsb;
	 $quantity_totalG= $row2->qty_ggl;
	 $quantity_totalRG= $row2->qty_rsb_ggl;
	 $quantity_totalUN= $row2->qty_uncertified;
	 
	 //$total_quantity = $quantity_total+$total_quantity;
	  $total_quantityR = $total_quantityR + $quantity_totalR;
	  $total_quantityG = $total_quantityG + $quantity_totalG;
	  $total_quantityRG = $total_quantityRG + $quantity_totalRG;
	  $total_quantityUN = $total_quantityUN + $quantity_totalUN;
	 
	// $pks_total = $row2->cogs_amount;
	// $total_pks = $pks_total+$total_pks;
	$pks_totalR = $row2->cogs_amountR;
	$total_pksR = $total_pksR + $pks_totalR;
	$pks_totalG = $row2->cogs_amountG;
	$total_pksG = $total_pksG + $pks_totalG;
	$pks_totalRG = $row2->cogs_amountRG;
	$total_pksRG = $total_pksRG + $pks_totalRG;
	$pks_totalUN = $row2->cogs_amountUN;
	$total_pksUN = $total_pksUN + $pks_totalUN;
	 
	// $fc_total = $freightTotal2;
	// $total_fc = $fc_total+$total_fc;
	 $fc_totalR = $freightTotal2R;
	 $total_fcR = $fc_totalR+$total_fcR;
	 $fc_totalG = $freightTotal2G;
	 $total_fcG = $fc_totalG + $total_fcG;
	 $fc_totalRG = $freightTotal2RG;
	 $total_fcRG = $fc_totalRG + $total_fcRG;
	 $fc_totalUN = $freightTotal2UN;
	 $total_fcUN = $fc_totalUN + $total_fcUN;
	 
	// $vh_total = $handlingTotal2;
	// $total_vh = $vh_total+$total_vh;
	$vh_totalR = $handlingTotal2R;
	 $total_vhR = $vh_totalR + $total_vhR;
	 $vh_totalG = $handlingTotal2G;
	 $total_vhG = $vh_totalG + $total_vhG;
	 $vh_totalRG = $handlingTotal2RG;
	 $total_vhRG = $vh_totalRG + $total_vhRG;
	 $vh_totalUN = $handlingTotal2UN;
	 $total_vhUN = $vh_totalUN + $total_vhUN;
	 
	 //$uc_total = $unloadingTotal2;
	 //$total_uc = $uc_total+$total_uc;
	  $uc_totalR = $unloadingTotal2R;
	 $total_ucR = $uc_totalR + $total_ucR;
	 $uc_totalG = $unloadingTotal2G;
	 $total_ucG = $uc_totalG + $total_ucG;
	 $uc_totalRG = $unloadingTotal2RG;
	 $total_ucRG = $uc_totalRG + $total_ucRG;
	 $uc_totalUN = $unloadingTotal2UN;
	 $total_ucUN = $uc_totalUN + $total_ucUN;
	 
	// $cogs_total = $totalCogs2;
	// $total_cogs = $cogs_total+$total_cogs;
	$cogs_totalR = $totalCogs2R;
	 $total_cogsR = $cogs_totalR + $total_cogsR;
	 $cogs_totalG = $totalCogs2G;
	 $total_cogsG = $cogs_totalG + $total_cogsG;
	 $cogs_totalRG = $totalCogs2RG;
	 $total_cogsRG = $cogs_totalRG + $total_cogsRG;
	 $cogs_totalUN = $totalCogs2UN;
	 $total_cogsUN = $cogs_totalUN + $total_cogsUN;
	 
	 $vessel_name = $row2->vessel_name;
	  
	  $no1++;
}

$AmountTotalQty = $total_quantityR + $total_quantityG + $total_quantityRG + $total_quantityUN;
$AmountTotalPKS = $total_pksR + $total_pksG + $total_pksRG + $total_pksUN ;
$AmountTotalFC = $total_fcR + $total_fcG + $total_fcRG + $total_fcUN;
$AmountTotalVH = $total_vhR + $total_vhG + $total_vhRG + $total_vhUN;
$AmountTotalUC = $total_ucR + $total_ucG + $total_ucRG + $total_ucUN;
$AmountTotalCOGS = $total_cogsR + $total_cogsG + $total_cogsRG + $total_cogsUN;

/*$sql = " SELECT ROUND(
		SUM(
		       CASE WHEN t.transaction_type = 1 AND t.rsb = 1 AND t.ggl = 0
				THEN t.quantity ELSE 0 END)
				- SUM(CASE WHEN t.transaction_type = 2 AND t.rsb = 1 THEN t.shrink ELSE 0 END),2
			) AS qty_availableR,
			ROUND(
				SUM(
					   CASE WHEN t.transaction_type = 1 AND t.rsb = 0 AND t.ggl = 1
						THEN t.quantity ELSE 0 END)- SUM(CASE WHEN t.transaction_type = 2 AND t.ggl = 1 THEN t.shrink ELSE 0 END),2
			) AS qty_availableG,
			ROUND(
				SUM(
					   CASE WHEN t.transaction_type = 1 AND t.rsb = 1 AND t.ggl = 1
						THEN t.quantity ELSE 0 END)- SUM(CASE WHEN t.transaction_type = 2 AND t.rsb_ggl =1 THEN t.shrink ELSE 0 END),2
			) AS qty_availableRG,
			ROUND(
				SUM(
					   CASE WHEN t.transaction_type = 1 AND t.rsb = 0 AND t.ggl = 0
						THEN t.quantity ELSE 0 END)- SUM(CASE WHEN t.transaction_type = 2 AND (t.rsb = 0 AND t.ggl = 0 AND t.`rsb_ggl` = 0) THEN t.shrink ELSE 0 END),2
			) AS qty_availableUN
		FROM `transaction` t WHERE 1=1 AND t.slip_no < '{$slipOut}' 
		AND t.transaction_date <= '{$transactionDate}' 
		AND SUBSTRING(t.slip_no,1,3) = '{$slipOutCode}' 
		ORDER BY t.transaction_date ASC ";*/
$sql ="SELECT ROUND(SUM(CASE WHEN t.transaction_type = 1 AND t.rsb = 1 THEN t.quantity ELSE 0 END)
            - SUM(CASE WHEN t.transaction_type = 2 AND t.rsb = 1 THEN t.shrink ELSE 0 END)) AS qty_availableR, 
            ROUND(SUM( CASE WHEN t.transaction_type = 1 AND  t.ggl = 1 THEN t.quantity ELSE 0 END)
            - SUM(CASE WHEN t.transaction_type = 2 AND t.ggl = 1 THEN t.shrink ELSE 0 END)) AS qty_availableG, 
            ROUND(SUM( CASE WHEN t.transaction_type = 1 AND t.rsb_ggl = 1 THEN t.quantity ELSE 0 END)
            - SUM(CASE WHEN t.transaction_type = 2 AND t.`rsb_ggl` = 1 THEN t.shrink ELSE 0 END)) AS qty_availableRG,
            ROUND(SUM( CASE WHEN t.transaction_type = 1 AND (t.uncertified = 1 OR t.uncertified = 0 OR t.uncertified IS NULL) THEN t.quantity ELSE 0 END)
            - SUM(CASE WHEN t.transaction_type = 2  AND (t.uncertified = 1 OR t.uncertified = 0 OR t.uncertified IS NULL) THEN t.shrink ELSE 0 END))
            AS qty_availableUN,
            ROUND(SUM(CASE WHEN t.transaction_type = 2 THEN t.shrink ELSE 0 END),2 ) AS shrink
        FROM `transaction` t
		WHERE 1=1 AND t.slip_no < '{$slipOut}' 
		AND t.transaction_date <=  '{$transactionDate}' 
		AND SUBSTRING(t.slip_no,1,3) = '{$slipOutCode}'  ORDER BY t.transaction_date ASC";

$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
			if($result !== false && $result->num_rows == 1) {
				 $row = $result->fetch_object();
		
			   // $begining1 = $row->qty_available;
			   $begining1_R = $row->qty_availableR;
			   $begining1_G = $row->qty_availableG;
			   $begining1_RG = $row->qty_availableRG;
			   $begining1_UN = $row->qty_availableUN;
			   $susut = $row->shrink;
				
			}
			
			//update by yeni
/* $sql3 = "SELECT SUM(qtyRSB) AS RS, SUM(qtyGGL) AS GG, SUM(qtyRG) AS RG, SUM(qtyUN) AS UN FROM ( 
		SELECT CASE WHEN d.qty_rsb <> 0 THEN SUM(d.qty_rsb) ELSE 0 END AS qtyRSB, 
			CASE WHEN d.qty_ggl <> 0 THEN SUM(d.qty_ggl) ELSE 0 END AS qtyGGL, 
			CASE WHEN d.qty_rsb_ggl <> 0 THEN SUM(d.qty_rsb_ggl) ELSE 0 END AS qtyRG, 
			CASE WHEN d.qty_uncertified <> 0 THEN SUM(d.qty_uncertified) ELSE 0 END AS qtyUN, 
			t2.slip_no, t2.`transaction_date` 
		FROM `delivery` d INNER JOIN shipment sh ON sh.shipment_id = d.`shipment_id` 
		LEFT JOIN TRANSACTION t2 ON t2.`shipment_id` = sh.`shipment_id` WHERE 1=1 
		AND SUBSTRING(t2.slip_no,1,3) = '{$slipOutCode}' AND t2.transaction_type = 2 AND sh.`shipment_no` NOT LIKE '%{$shipmentNo}%'
		GROUP BY d.qty_rsb, d.qty_ggl, d.qty_rsb_ggl, d.qty_uncertified
) a 
WHERE a.slip_no < '{$slipOut}' AND a.transaction_date <= '{$transactionDate}' ";
		//	echo ' QUERY - 2 '. $sql3;
$result3 = $myDatabase->query($sql3, MYSQLI_STORE_RESULT);			
if($result3->num_rows == 1) {
	while($row3 = $result3->fetch_object()) {
		//	$begining2 = $begining2 + $row3->qty;
		//	$begining2 = $begining2 + $row3->begining2;
		$begining2_R =  $row3->RS;
		$begining2_G = $row3->GG;
		$begining2_RG = $row3->RG;
		$begining2_UN = $row3->UN;
	}	
}

	
$beginingR = $begining1_R - $begining2_R;
$beginingG = $begining1_G - $begining2_G;
$beginingRG = $begining1_RG - $begining2_RG;
$beginingUN = $begining1_UN - $begining2_UN;*/

//Update By Idris ngambil notim Out
		$sql3 = "SELECT * FROM (
SELECT case when (t2.rsb = 1) then SUM(t2.quantity) else 0 end AS qtyRsb,
case when (t2.ggl = 1) then SUM(t2.quantity) ELSE 0 END AS qtyGgl,
case WHEN (t2.rsb_ggl = 1) THEN SUM(t2.quantity) ELSE 0 END  AS qtyRsbGgl,
case WHEN (t2.uncertified = 1 OR t2.uncertified = 0 OR t2.uncertified IS NULL) THEN SUM(t2.quantity)ELSE 0 END AS qtyUncertifeid, 
t2.slip_no, t2.`transaction_date` 
FROM `transaction` t2 INNER JOIN shipment sh ON sh.shipment_id = t2.shipment_id WHERE 1=1
AND SUBSTRING(t2.slip_no,1,3) = '{$slipOutCode}' AND t2.transaction_type = 2 AND sh.`shipment_no` NOT LIKE '%{$shipmentNo}%') a";
$result3 = $myDatabase->query($sql3, MYSQLI_STORE_RESULT);
//echo $sql3;
		
if($result3->num_rows > 0) {
	while($row3 = $result3->fetch_object()) {
		
		//	$begining2 = $begining2 + $row3->qty;
		//	$begining2 = $begining2 + $row3->begining2;
		$begining2_R = $begining2_R + $row3->qtyRsb;
		$begining2_G = $begining2_G + $row3->qtyGgl;
		$begining2_RG = $begining2_RG + $row3->qtyRsbGgl;
		$begining2_UN = $begining2_UN + $row3->qtyUncertifeid;
	}	
}


// $begining = $begining1 - $begining2;		
// $ending = $begining - $total_quantity;	
$AmountTotalB = 0; $AmountTotalE = 0;

$beginingR = $begining1_R - $begining2_R;
$beginingG = $begining1_G - $begining2_G;
$beginingRG = $begining1_RG - $begining2_RG;
$beginingUN = $begining1_UN - $begining2_UN;

$endingR = $beginingR - $total_quantityR;	
$endingG = $beginingG - $total_quantityG;	
$endingRG = $beginingRG - $total_quantityRG;	
$endingUN = $beginingUN - $total_quantityUN;	

// $AmountTotalB = ($beginingR + $beginingG + $beginingRG + $beginingUN) - $susut;
// $AmountTotalE = ($endingR + $endingG + $endingRG + $endingUN) - $susut;
$AmountTotalB = ($beginingR + $beginingG + $beginingRG + $beginingUN) ;
$AmountTotalE = ($endingR + $endingG + $endingRG + $endingUN) ;

//RSB
$rowActive++;
	$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}")->applyFromArray($styleArray4);
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}","Vessel Name");
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->applyFromArray($styleArray4);
	$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}",$vessel_name);
  //  $objPHPExcel->getActiveSheet()->mergeCells("C{$rowActive}:{$lastColumn}{$rowActive}"); 
  
  //GGL
  	$objPHPExcel->getActiveSheet()->getStyle("C{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("C{$rowActive}")->applyFromArray($styleArray4);
	$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}",$vessel_name);
	
//RSB + GGL
  	$objPHPExcel->getActiveSheet()->getStyle("D{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("D{$rowActive}")->applyFromArray($styleArray4);
	$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}",$vessel_name);
	
//Uncertified
  	$objPHPExcel->getActiveSheet()->getStyle("E{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("E{$rowActive}")->applyFromArray($styleArray4);
	$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}",$vessel_name);
	
	//TOTAL
	  	$objPHPExcel->getActiveSheet()->getStyle("F{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("F{$rowActive}")->applyFromArray($styleArray4);
	$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}",$vessel_name);



 $rowActive++;
    $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}")->applyFromArray($styleArray4);
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}","Begining Balance");
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}",$beginingR);
  //  $objPHPExcel->getActiveSheet()->mergeCells("C{$rowActive}:{$lastColumn}{$rowActive}");
  //GGL
  	$objPHPExcel->getActiveSheet()->getStyle("C{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("C{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}",$beginingG);
	
	//RSB + GGL
	$objPHPExcel->getActiveSheet()->getStyle("D{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("D{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}",$beginingRG);
	
	//Uncertified
	$objPHPExcel->getActiveSheet()->getStyle("E{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("E{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}",$beginingUN);
	
	//TOTAL
	$objPHPExcel->getActiveSheet()->getStyle("F{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("F{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}",$AmountTotalB);




$rowActive++;
    $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}")->applyFromArray($styleArray4);
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}","Total Quantity");
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}",$total_quantityR);
   // $objPHPExcel->getActiveSheet()->mergeCells("C{$rowActive}:{$lastColumn}{$rowActive}");
   //GGL
   	$objPHPExcel->getActiveSheet()->getStyle("C{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("C{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}",$total_quantityG);
	
	//RSB + GGL
   	$objPHPExcel->getActiveSheet()->getStyle("D{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("D{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}",$total_quantityRG);
	
	//Uncertified
   	$objPHPExcel->getActiveSheet()->getStyle("E{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("E{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}",$total_quantityUN);
	
	//TOTAL   	
	$objPHPExcel->getActiveSheet()->getStyle("F{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("F{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}",$AmountTotalQty);

$rowActive++;
    $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}")->applyFromArray($styleArray4);
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}","Ending Balance");
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}",$endingR);
   // $objPHPExcel->getActiveSheet()->mergeCells("C{$rowActive}:{$lastColumn}{$rowActive}");
   
   //GGL
   	$objPHPExcel->getActiveSheet()->getStyle("C{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("C{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}",$endingG);
	
	//RSB + GGL
   	$objPHPExcel->getActiveSheet()->getStyle("D{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("D{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}",$endingRG);
	
	//Uncertified
   	$objPHPExcel->getActiveSheet()->getStyle("E{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("E{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}",$endingUN);
	
	//TOTAL
	   	$objPHPExcel->getActiveSheet()->getStyle("F{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("F{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}",$AmountTotalE);




 $rowActive++;
    $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}")->applyFromArray($styleArray4);
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}","Total COGS PKS Amount");
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}",$total_pksR);
   // $objPHPExcel->getActiveSheet()->mergeCells("C{$rowActive}:{$lastColumn}{$rowActive}");
   //GGL
   	$objPHPExcel->getActiveSheet()->getStyle("C{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("C{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}",$total_pksG);
	
	//RSB + GGL
   	$objPHPExcel->getActiveSheet()->getStyle("D{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("D{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}",$total_pksRG);
	
	//Uncertified
   	$objPHPExcel->getActiveSheet()->getStyle("E{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("E{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}",$total_pksUN);
	
	//TOTAL
	$objPHPExcel->getActiveSheet()->getStyle("F{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("F{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}",$AmountTotalPKS);




 $rowActive++;
 	$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}")->applyFromArray($styleArray4);
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}","Total Freight Cost");
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}",$total_fcR);
   // $objPHPExcel->getActiveSheet()->mergeCells("C{$rowActive}:{$lastColumn}{$rowActive}");
   //GGL
   	$objPHPExcel->getActiveSheet()->getStyle("C{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("C{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}",$total_fcG);
	
	//RSB + GGL
   	$objPHPExcel->getActiveSheet()->getStyle("D{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("D{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}",$total_fcRG);
	
	//Certification
   	$objPHPExcel->getActiveSheet()->getStyle("E{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("E{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}",$total_fcUN);
	
	//TOTAL
	$objPHPExcel->getActiveSheet()->getStyle("F{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("F{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}",$AmountTotalFC);




 $rowActive++;
 	$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}")->applyFromArray($styleArray4);
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}","Total Unloading Cost");
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}",$total_ucR);
  //  $objPHPExcel->getActiveSheet()->mergeCells("C{$rowActive}:{$lastColumn}{$rowActive}");
  //GGL
  	$objPHPExcel->getActiveSheet()->getStyle("C{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("C{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}",$total_ucG);
	
	//RSB + GGL
  	$objPHPExcel->getActiveSheet()->getStyle("D{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("D{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}",$total_ucRG);
	
	//Uncertified
  	$objPHPExcel->getActiveSheet()->getStyle("E{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("E{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}",$total_ucUN);
	
	$objPHPExcel->getActiveSheet()->getStyle("F{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("F{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}",$AmountTotalUC);



 $rowActive++;
 	$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}")->applyFromArray($styleArray4);
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}","Total Handling Cost");
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}",$total_vhR);
  //  $objPHPExcel->getActiveSheet()->mergeCells("C{$rowActive}:{$lastColumn}{$rowActive}");
  //GGL
  	$objPHPExcel->getActiveSheet()->getStyle("C{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("C{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}",$total_vhG);
	
	//RSB + GGL
  	$objPHPExcel->getActiveSheet()->getStyle("D{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("D{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}",$total_vhRG);
	
	//Certification
  	$objPHPExcel->getActiveSheet()->getStyle("E{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("E{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}",$total_vhUN);
	
	//TOtal
	$objPHPExcel->getActiveSheet()->getStyle("E{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("E{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}",$AmountTotalVH);




 $rowActive++;
 	$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}")->applyFromArray($styleArray4);
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}","Total COGS");
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}",$total_cogsR);
    //$objPHPExcel->getActiveSheet()->mergeCells("C{$rowActive}:{$lastColumn}{$rowActive}");
	//GGL
		$objPHPExcel->getActiveSheet()->getStyle("C{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("C{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}",$total_cogsG);
	
	//RSB + GGL
	$objPHPExcel->getActiveSheet()->getStyle("D{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("D{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}",$total_cogsRG);
	
	//Uncertified
	$objPHPExcel->getActiveSheet()->getStyle("E{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("E{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}",$total_cogsUN);
	
	//TOTAL
		$objPHPExcel->getActiveSheet()->getStyle("F{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("F{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}",$AmountTotalCOGS);



	
$sql = "SELECT d.*,
            CASE WHEN t.transaction_type = 1 THEN s.stockpile_name ELSE s2.stockpile_name END AS stockpile_name,  
            DATE_FORMAT(t.unloading_date, '%d %b %Y') AS transaction_date2,
            t.slip_no, 
            CASE WHEN con.contract_type = 'P' THEN 'PKS' ELSE 'Curah' END AS contract_type2,
            con.po_no, 
            CONCAT(f.freight_code, '-', v2.vendor_code) AS freight_code,
            v3.vendor_name AS supplier,
            v1.vendor_name, 
			t.permit_no as doNo,
            sh.shipment_no,
            t.send_weight, t.netto_weight, d.quantity, CASE WHEN t.mutasi_id IS NOT NULL AND t.mutasi_id <> 0 THEN t.unit_cost
			WHEN t.adjustmentAudit_id IS NOT NULL THEN t.unit_price
			ELSE con.price_converted END AS price_converted,
			CASE WHEN t.mutasi_id IS NOT NULL AND t.mutasi_id <> 0 THEN d.quantity * t.unit_cost
			WHEN t.adjustmentAudit_id IS NOT NULL THEN d.quantity * t.unit_price
			ELSE d.quantity * con.price_converted END AS cogs_amount,
            t.freight_quantity, t.freight_price, v1d.distance, v1d.ghg,
            CASE WHEN t.delivery_status = 2 THEN (d.percent_taken / 100) * (t.quantity * t.freight_price)
			ELSE (d.percent_taken / 100) * (t.freight_quantity * t.freight_price) END AS freight_total,
			 CASE WHEN t.delivery_status = 2 AND t.freight_cost_id IS NOT NULL THEN (d.quantity/t.freight_quantity) * COALESCE((SELECT amt_claim FROM transaction_shrink_weight WHERE transaction_id = d.transaction_id),0)
	    WHEN t.freight_cost_id IS NOT NULL THEN (d.percent_taken / 100) * COALESCE((SELECT amt_claim FROM transaction_shrink_weight WHERE transaction_id = d.transaction_id),0) ELSE 0 END AS freight_shrink,
		
		CASE WHEN t.delivery_status = 2 AND t.freight_cost_id IS NOT NULL AND t.`transaction_date` > '2022-09-15' THEN (d.quantity/t.freight_quantity) * COALESCE((SELECT amt_claim FROM transaction_additional_shrink WHERE transaction_id = d.transaction_id),0)
		WHEN t.freight_cost_id IS NOT NULL AND t.`transaction_date` > '2022-09-15' THEN (d.percent_taken / 100) * COALESCE((SELECT amt_claim FROM transaction_additional_shrink WHERE transaction_id = d.transaction_id),0) ELSE 0 END AS freight_shrink2,

            t.unloading_price,
            (d.percent_taken / 100) * t.unloading_price AS unloading_total,
			t.handling_price AS vh_price, t.handling_quantity,
			CASE WHEN t.delivery_status = 2 THEN (d.percent_taken / 100) * (t.handling_quantity * t.handling_price)
			ELSE (d.percent_taken / 100) * (t.handling_quantity * t.handling_price) END AS handling_total,
			vh1.pph_tax_id AS vh_pph_tax_id, vh1.pph AS vh_pph, vhtx.tax_category AS vh_pph_tax_category,
            f.ppn_tax_id AS fc_ppn_tax_id, f.ppn AS fc_ppn, fctxppn.tax_category AS fc_ppn_tax_category,
            t.fc_tax_id AS fc_pph_tax_id, fctxpph.tax_value AS fc_pph, fctxpph.tax_category AS fc_pph_tax_category,
            l.ppn_tax_id AS uc_ppn_tax_id, l.ppn AS uc_ppn, uctxppn.tax_category AS uc_ppn_tax_category,
            l.pph_tax_id AS uc_pph_tax_id, l.pph AS uc_pph, uctxpph.tax_category AS uc_pph_tax_category,
			l.labor_id,t.freight_cost_id, f.freight_id,
			CASE WHEN d.qty_ggl <> 0 THEN 'GGL'
				 WHEN d.qty_rsb <> 0  THEN 'RSB'
				 WHEN d.qty_rsb_ggl <> 0 THEN 'RSB + GGL'
				 WHEN d.qty_uncertified <> 0 THEN 'Uncertified'
			ELSE 'Uncertified' END AS sertifikat
        FROM delivery d
        INNER JOIN `transaction` t
        	ON t.transaction_id = d.transaction_id
        LEFT JOIN stockpile_contract sc
            ON sc.stockpile_contract_id = t.stockpile_contract_id
        LEFT JOIN stockpile s
            ON s.stockpile_id = sc.stockpile_id
        LEFT JOIN contract con
            ON con.contract_id = sc.contract_id
        LEFT JOIN vendor v1
            ON v1.vendor_id = con.vendor_id
		LEFT JOIN vendor_detail v1d
			ON v1d.vendor_id = v1.vendor_id
        LEFT JOIN unloading_cost uc
            ON uc.unloading_cost_id = t.unloading_cost_id
        LEFT JOIN vehicle vh
            ON vh.vehicle_id = uc.vehicle_id
        LEFT JOIN freight_cost fc
            ON fc.freight_cost_id = t.freight_cost_id
        LEFT JOIN freight f
            ON f.freight_id = fc.freight_id
        LEFT JOIN vendor v2
            ON v2.vendor_id = fc.vendor_id
        LEFT JOIN vendor v3
            ON v3.vendor_id = t.vendor_id
        LEFT JOIN shipment sh
            ON sh.shipment_id = d.shipment_id
        LEFT JOIN sales sl
            ON sl.sales_id = sh.sales_id
        LEFT JOIN stockpile s2
            ON s2.stockpile_id = sl.stockpile_id
        LEFT JOIN customer cust
            ON cust.customer_id = sl.customer_id
        LEFT JOIN tax fctxpph
	        ON fctxpph.tax_id = t.fc_tax_id
        LEFT JOIN tax fctxppn
	        ON fctxppn.tax_id = f.ppn_tax_id
	    LEFT JOIN labor l
            ON l.labor_id = t.labor_id
	    LEFT JOIN tax uctxpph
	        ON uctxpph.tax_id = l.pph_tax_id
        LEFT JOIN tax uctxppn
	        ON uctxppn.tax_id = l.ppn_tax_id	
		lEFT JOIN vendor_handling_cost vhc
			ON vhc.handling_cost_id = t.handling_cost_id
		LEFT JOIN vendor_handling vh1
			ON vh1.vendor_handling_id = vhc.vendor_handling_id
		LEFT JOIN tax vhtx
			ON vh1.pph_tax_id = vhtx.tax_id
        WHERE 1=1 
        AND sl.company_id = {$_SESSION['companyId']}
        {$whereProperty} ORDER BY t.slip_no ASC, d.`transaction_id` ASC";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);	

$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "COGS REPORT");

$rowActive++;
$rowMerge = $rowActive + 1;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:A{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Area");
$objPHPExcel->getActiveSheet()->mergeCells("B{$rowActive}:B{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Transaction Date");
$objPHPExcel->getActiveSheet()->mergeCells("C{$rowActive}:C{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Slip No.");
$objPHPExcel->getActiveSheet()->mergeCells("D{$rowActive}:D{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Purchase Type");
$objPHPExcel->getActiveSheet()->mergeCells("E{$rowActive}:E{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "PO No.");

$objPHPExcel->getActiveSheet()->mergeCells("F{$rowActive}:F{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "DO No.");

$objPHPExcel->getActiveSheet()->mergeCells("G{$rowActive}:G{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "SUPPLIER NO");
$objPHPExcel->getActiveSheet()->mergeCells("H{$rowActive}:H{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "SUPPLIER NAME");
$objPHPExcel->getActiveSheet()->mergeCells("I{$rowActive}:I{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "PKS SOURCE");
$objPHPExcel->getActiveSheet()->mergeCells("J{$rowActive}:J{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "CERTIFICATION");

$objPHPExcel->getActiveSheet()->mergeCells("K{$rowActive}:K{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "DESTINATION (Km)");
$objPHPExcel->getActiveSheet()->mergeCells("L{$rowActive}:L{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "GHG AMOUNT");

$objPHPExcel->getActiveSheet()->mergeCells("M{$rowActive}:M{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "SHIPMENT CODE");
// $objPHPExcel->getActiveSheet()->mergeCells("L{$rowActive}:L{$rowMerge}");
// $objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "FREIGHT SUPPLIER");
// $objPHPExcel->getActiveSheet()->mergeCells("M{$rowActive}:M{$rowMerge}");
// $objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "FREIGHT PRICE");
// $objPHPExcel->getActiveSheet()->mergeCells("N{$rowActive}:N{$rowMerge}");
// $objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", "LABOR");
// $objPHPExcel->getActiveSheet()->mergeCells("O{$rowActive}:O{$rowMerge}");
// $objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", "UNLOADING PRICE");
$objPHPExcel->getActiveSheet()->mergeCells("N{$rowActive}:S{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", "Product (PKS)");

$objPHPExcel->getActiveSheet()->setCellValue("N{$rowMerge}", "Berat Kirim (kg)");
$objPHPExcel->getActiveSheet()->setCellValue("O{$rowMerge}", "Berat Netto (kg)");
$objPHPExcel->getActiveSheet()->setCellValue("P{$rowMerge}", "Inventory (kg)");
$objPHPExcel->getActiveSheet()->setCellValue("Q{$rowMerge}", "Berat Susut (kg)");
$objPHPExcel->getActiveSheet()->setCellValue("R{$rowMerge}", "Price /kg");
$objPHPExcel->getActiveSheet()->setCellValue("S{$rowMerge}", "COGS PKS Amount");

$objPHPExcel->getActiveSheet()->mergeCells("T{$rowActive}:T{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("T{$rowActive}", "FREIGHT COST");
$objPHPExcel->getActiveSheet()->mergeCells("U{$rowActive}:U{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("U{$rowActive}", "UNLOADING COST");
$objPHPExcel->getActiveSheet()->mergeCells("V{$rowActive}:V{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("V{$rowActive}", "HANDLING COST");
$objPHPExcel->getActiveSheet()->mergeCells("W{$rowActive}:W{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("W{$rowActive}", "TOTAL COGS");

$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray4);
$objPHPExcel->getActiveSheet()->getStyle("A{$rowMerge}:{$lastColumn}{$rowMerge}")->applyFromArray($styleArray4);

$rowActive = $rowMerge;
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Body">

while($row = $result->fetch_object()) {
    $rowActive++;
	
		if($row->slip_no >= 'SAM-0000000001' && $row->slip_no <= 'SAM-0000001925'){
				$fc_pph = 4 ;
			}else{
				$fc_pph	 = $row->fc_pph;
			}
			
			if($row->slip_no >= 'MAR-0000000001' && $row->slip_no <= 'MAR-0000007138'){
				$fc_pph = 4 ;
			}else{
				$fc_pph	 = $row->fc_pph;
			}
				
				if($row->vh_pph_tax_category == 1 && $row->vh_pph_tax_id != ''){
			         $pphvh = ($row2->handling_total / ((100 - $row->vh_pph) / 100)) - $row->handling_total;
				 
				 }elseif($row->vh_pph_tax_category == 0 && $row->vh_pph_tax_id != ''){
					  $pphvh =  0;  
					 //$pphfc =  $row->freight_total - ($row->freight_total * ((100 - $fc_pph) / 100));
				 }else{
				 	$pphvh = 0;
				 }
				 
				 $handlingTotal = $row->handling_total - $pphvh;
               
		         if($row->fc_pph_tax_category == 1 && $row->fc_pph_tax_id != ''){
			         $pphfc = ($row->freight_total / ((100 - $fc_pph) / 100)) - $row->freight_total;
				 
				 }elseif($row->fc_pph_tax_category == 0 && $row->fc_pph_tax_id != ''){
					  $pphfc =  0;  
					 //$pphfc =  $row->freight_total - ($row->freight_total * ((100 - $fc_pph) / 100));
				 }else{
				 	$pphfc = 0;
				 }
				 /*
				 if($row->fc_ppn_tax_id != ''){
					 $ppnfc = ($row->freight_total * ((100 + $row->fc_ppn) / 100)) - $row->freight_total;
				 }else{
				     $ppnfc = 0;
			     }*/
				 
				 $freightTotal = ($row->freight_total + $ppnfc + $pphfc) - $row->freight_shrink - $row->freight_shrink2;	
				 
				 
				 if($row->uc_pph_tax_category == 1 && $row->uc_pph_tax_id != ''){
			         $pphuc = ($row->unloading_total / ((100 - $row->uc_pph) / 100)) - $row->unloading_total;
					 
				 }elseif($row->uc_pph_tax_category == 0 && $row->uc_pph_tax_id != ''){
					 $pphuc =  0;
					 //$pphuc =  $row->unloading_total - ($row->unloading_total * ((100 - $row->uc_pph) / 100));
				 }else{
				 	$pphuc = 0;
				 }
				 
				 
				 $unloadingTotal = $row->unloading_total + $ppnuc + $pphuc;	
    
     $totalCogs = $row->cogs_amount + $freightTotal + $unloadingTotal + $handlingTotal;
    
     $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $row->stockpile_name);
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $row->transaction_date2);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("C{$rowActive}", $row->slip_no, PHPExcel_Cell_DataType::TYPE_STRING);
    //$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $row->slip_no);
    $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $row->contract_type2);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("E{$rowActive}", $row->po_no, PHPExcel_Cell_DataType::TYPE_STRING);
    //$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $row->po_no);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("F{$rowActive}", $row->doNo, PHPExcel_Cell_DataType::TYPE_STRING);
    //$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $row->permit_no);

	$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $row->freight_code);
    $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $row->supplier);
    $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $row->vendor_name);
	$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $row->sertifikat);

	$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $row->distance);
	$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $row->ghg);

    $objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $row->shipment_no);
	// $objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $row->freight_supplier);
	// $objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $row->freight_price);
	// $objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", $row->labor_name);
	// $objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", $row->unloading_price);
    $objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", $row->send_weight);
    $objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", $row->netto_weight);
    $objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", $row->quantity);
    $objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", "");
    $objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", $row->price_converted);
    $objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", $row->cogs_amount);
    $objPHPExcel->getActiveSheet()->setCellValue("T{$rowActive}", $freightTotal);
    $objPHPExcel->getActiveSheet()->setCellValue("U{$rowActive}", $unloadingTotal);
	$objPHPExcel->getActiveSheet()->setCellValue("V{$rowActive}", $handlingTotal);
    $objPHPExcel->getActiveSheet()->setCellValue("W{$rowActive}", $totalCogs);
}
$bodyRowEnd = $rowActive;

// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Formating Excel">
// Set column width
for ($temp = ord("A"); $temp <= ord("Z"); $temp++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
}
$objPHPExcel->getActiveSheet()->getColumnDimension("AA")->setAutoSize(true);

// Set format date in cell
if ($bodyRowEnd > $headerRow) {
    $objPHPExcel->getActiveSheet()->getStyle("B" . ($headerRow + 1) . ":B{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
}

// Set number format for Amount 
$objPHPExcel->getActiveSheet()->getStyle("K" . ($headerRow + 1) . ":L{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
// $objPHPExcel->getActiveSheet()->getStyle("L" . ($headerRow + 1) . ":L{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);  
$objPHPExcel->getActiveSheet()->getStyle("N" . ($headerRow + 1) . ":W{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1); 
$objPHPExcel->getActiveSheet()->getStyle("N" . ($headerRow + 1) . ":W{$bodyRowEnd}")->getNumberFormat()->setFormatCode("#,##0.0000");

// Set border for table
$objPHPExcel->getActiveSheet()->getStyle("A" . ($headerRow) . ":{$lastColumn}{$bodyRowEnd}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
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