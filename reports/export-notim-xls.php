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
$stockpileIds = $_POST['stockpileIds'];
$periodFrom = $myDatabase->real_escape_string($_POST['periodFrom']);
$periodTo = $myDatabase->real_escape_string($_POST['periodTo']);
$vendorIds = $_POST['vendorIdSplit'];
$stockpileName = 'All ';
$periodFull = '';
$tempTest = 0;
$tempTest2 = 0;

// <editor-fold defaultstate="collapsed" desc="Query">

if ($stockpileIds !== '') {
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
		
	/*	for ($i = 0; $i < sizeof($stockpile_name); $i++) {
                        if($stockpile_names == '') {
                            $stockpile_names .= "'". $stockpile_name[$i] ."'";
                        } else {
                            $stockpile_names .= ','. "'". $stockpile_name[$i] ."'";
                        }
                    }*/
				
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
}

if ($periodFrom != '' && $periodTo != '') {
    $whereProperty .= " AND IF(t.transaction_type = 1, t.unloading_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y'), t.transaction_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y')) ";
   	//$whereProperty .= " AND t.notim_status = 0 AND t.transaction_type = 1 AND  t.transaction_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
    $sumProperty .= " AND IF(t.transaction_type = 1, t.unloading_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
    $boolBalanceBefore = true;
    $periodFull = $periodFrom . " - " . $periodTo . " ";
} else if ($periodFrom != '' && $periodTo == '') {
    $whereProperty .= " AND IF(t.transaction_type = 1, t.unloading_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
   //	$whereProperty .= " AND t.notim_status = 0 AND t.transaction_type = 1 AND t.transaction_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')";
    $sumProperty .= " AND IF(t.transaction_type = 1, t.unloading_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
    $boolBalanceBefore = true;
    $periodFull = "From " . $periodFrom . " ";
} else if ($periodFrom == '' && $periodTo != '') {
    $whereProperty .= " AND IF(t.transaction_type = 1, t.unloading_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y'), t.transaction_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y')) ";
   	//$whereProperty .= " AND t.notim_status = 0 AND t.transaction_type = 1 AND t.transaction_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";
   $periodFull = "To " . $periodTo . " ";
}

if($vendorIds != '') {
    $whereProperty .= " AND v1.vendor_id IN ({$vendorIds}) ";
}

$sql = "SELECT l.laborRules, t.*,
            DATE_FORMAT(t.transaction_date, '%d %b %Y') AS transaction_date2,
			DATE_FORMAT(t.modify_date, '%d %b %Y') AS modify_date,
            CASE WHEN t.transaction_type = 1 THEN s.stockpile_name ELSE s2.stockpile_name END AS stockpile_name,  
            CASE WHEN t.transaction_type = 1 THEN con.po_no ELSE sh.shipment_code END AS po_no, 
            CASE WHEN t.transaction_type = 1 THEN con.contract_no ELSE sl.sales_no END AS contract_no, 
            CASE WHEN t.transaction_type = 1 THEN vh.vehicle_name ELSE '' END AS vehicle_name,
            CASE WHEN t.transaction_type = 1 THEN t.vehicle_no ELSE t.vehicle_no END AS vehicle_no,
            CASE WHEN t.transaction_type = 1 THEN DATE_FORMAT(t.unloading_date, '%d %b %Y') ELSE DATE_FORMAT(t.transaction_date, '%d %b %Y') END AS unloading_date2,
            DATE_FORMAT(t.loading_date, '%d %b %Y') AS loading_date2,
            CASE WHEN t.transaction_type = 1 THEN 'IN' ELSE 'OUT' END AS transaction_type2,
            CASE WHEN t.transaction_type = 1 THEN CONCAT(f.freight_code, '-', v2.vendor_code)
			ELSE (SELECT CONCAT(a.freight_code, ' - ', c.customer_code) FROM freight_local_sales a
LEFT JOIN freight_cost_local_sales b ON a.`freight_id` = b.`freight_id`
LEFT JOIN customer c ON c.`customer_id` = b.`vendor_id` WHERE b.`freight_cost_id` = t.freight_cost_id) END AS freight_code, f.freight_id, f.freight_rule,
            v1.vendor_name, hv.vendor_handling_id, hv.vendor_handling_name, hv.vendor_handling_rule, hv.pph_tax_id AS hc_pph_id, hv.pph AS hc_pph, hvtx.tax_category AS hc_pph_category,
            CASE WHEN t.transaction_type = 1 THEN v3.vendor_name ELSE cust.customer_name END AS supplier,
            CASE WHEN con.contract_type = 'P' THEN 'PKS' ELSE 'Curah' END AS contract_type2,
            CASE WHEN t.transaction_type = 1 THEN t.quantity ELSE -1*t.send_weight END AS quantity2,

			CASE WHEN ( t.mutasi_id > 0) THEN t.unit_cost 
                    WHEN t.transaction_type = 2 THEN 
                        (SELECT amount FROM general_ledger WHERE transaction_id = t.transaction_id AND account_id = 8) 
                        ELSE con.price_converted END AS pks_price,

            fc.price AS fc_price, ftx.tax_id AS fc_pph_id, ftx.tax_value AS fc_pph, 
            ftx.tax_category AS fc_pph_category,
			CASE WHEN t.slip_retur LIKE '%-R' THEN t.unloading_price ELSE t.unloading_price END AS uc_price,
			utx.tax_value AS uc_pph, utx.tax_category AS uc_pph_category, u.user_name,
			CASE WHEN t.transaction_type = 1 THEN (SELECT shi.shipment_no FROM shipment shi LEFT JOIN delivery d ON d.shipment_id = shi.shipment_id WHERE d.transaction_id = t.transaction_id LIMIT 1 )
		 ELSE sh.shipment_no END AS shipment_no2,
		 fp.payment_no AS fPayment, up.payment_no AS uPayment, hp.payment_no AS hPayment,ts.`trx_shrink_claim` AS shrink_claim, 
               
			    ROUND(CASE WHEN ts.trx_shrink_tolerance_kg > 0 AND ((t.shrink * -1) - ts.trx_shrink_tolerance_kg) > 0 AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL AND t.slip_retur IS NOT NULL THEN ((t.shrink *-1) - ts.trx_shrink_tolerance_kg) *-1
				
				WHEN ts.trx_shrink_tolerance_kg > 0 AND (t.shrink - ts.trx_shrink_tolerance_kg) > 0 AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL THEN t.shrink - ts.trx_shrink_tolerance_kg
				
				WHEN ts.trx_shrink_tolerance_persen > 0 AND ((t.shrink/t.send_weight) * 100 > ts.trx_shrink_tolerance_persen) AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL AND t.slip_retur IS NOT NULL THEN ((t.shrink *-1) - (SELECT weight_persen FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id))*-1 
                
				WHEN ts.trx_shrink_tolerance_persen > 0 AND ((t.shrink/t.send_weight) * 100 > ts.trx_shrink_tolerance_persen) AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL THEN t.shrink - (SELECT weight_persen FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id)
                ELSE 0 END,10) AS qtyClaim,
                fc.contract_pkhoa, l.labor_name, cpd.contract_pks_detail_id, vc.vendor_curah_name as vendor_curah, tas.qty_add_shrink, sc.stockpile_contract_id, fc.freight_cost_id, vhc.handling_cost_id, l.labor_id

        FROM TRANSACTION t
		LEFT JOIN transaction_additional_shrink tas ON t.transaction_id = tas.transaction_id
		LEFT JOIN transaction_shrink_weight ts
				ON t.transaction_id = ts.transaction_id
        LEFT JOIN stockpile_contract sc
            ON sc.stockpile_contract_id = t.stockpile_contract_id
        LEFT JOIN stockpile s
            ON s.stockpile_id = sc.stockpile_id
        LEFT JOIN contract con
            ON con.contract_id = sc.contract_id
		LEFT JOIN contract_pks_detail cpd
            ON con.contract_id = cpd.contract_id
        LEFT JOIN vendor_curah vc
            ON vc.vendor_curah_id = cpd.vendor_curah_id
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
            ON sh.shipment_id = t.shipment_id
        LEFT JOIN sales sl
            ON sl.sales_id = sh.sales_id
        LEFT JOIN stockpile s2
            ON s2.stockpile_id = sl.stockpile_id
        LEFT JOIN customer cust
            ON cust.customer_id = sl.customer_id
		LEFT JOIN tax ftx
	    	ON ftx.tax_id = t.fc_tax_id
		LEFT JOIN tax utx
	    	ON utx.tax_id = t.uc_tax_id
		LEFT JOIN USER u
			ON u.user_id = t.modify_by
		LEFT JOIN vendor_handling_cost vhc
			ON vhc.handling_cost_id = t.handling_cost_id
		LEFT JOIN vendor_handling hv
			ON hv.vendor_handling_id = vhc.vendor_handling_id
		LEFT JOIN tax hvtx ON hv.pph_tax_id = hvtx.tax_id
	LEFT JOIN payment fp ON fp.payment_id = t.fc_payment_id
	LEFT JOIN payment up ON up.payment_id = t.uc_payment_id
	LEFT JOIN payment hp ON hp.payment_id = t.hc_payment_id
	LEFT JOIN labor l ON l.labor_id = t.labor_id
        WHERE 1=1 
        AND t.transaction_type = 1 -- AND con.contract_type = 'C'  
        AND t.company_id = {$_SESSION['companyId']}
        AND t.notim_status = 0
        AND t.slip_retur IS NULL
        {$whereProperty} GROUP BY t.transaction_id ORDER BY t.slip_no ASC";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
// echo $sql;
// die();

//</editor-fold>

$fileName = "Export Nota Timbang " . $stockpileCodes . $periodFull . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumn = "AW";

// <editor-fold defaultstate="collapsed" desc="Create Excel and Define Header">
$objPHPExcel = new PHPExcel();
PHPExcel_Cell::setValueBinder(new PHPExcel_Cell_AdvancedValueBinder());

$objPHPExcel->setActiveSheetIndex($onSheet);
$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(75);
$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');
$objPHPExcel->getDefaultStyle()->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(15);

$rowActive++;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "No.");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "No. Slip");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Stockpile");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Transaction Date");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "No. Pol");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Kendaraan");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Tanggal Muat");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Supplier Freight");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "No. Surat Jalan");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "No. PO/Shipment Code");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "Nama PKS");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "Supplier/Customer");
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "No. Kontrak");
$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", "Type");
$objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", "Berat Kirim");
$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", "Berat Bruto");
$objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", "Berat Tarra");
$objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", "Berat Netto");
$objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", "Susut");
$objPHPExcel->getActiveSheet()->setCellValue("T{$rowActive}", "Additional Shrink");
$objPHPExcel->getActiveSheet()->setCellValue("U{$rowActive}", "Total /Kg");
$objPHPExcel->getActiveSheet()->setCellValue("V{$rowActive}", "Total");
$objPHPExcel->getActiveSheet()->setCellValue("W{$rowActive}", "Catatan");
$objPHPExcel->getActiveSheet()->setCellValue("X{$rowActive}", "Supir");

$objPHPExcel->getActiveSheet()->setCellValue("Y{$rowActive}", "Jumlah Angkut");
$objPHPExcel->getActiveSheet()->setCellValue("Z{$rowActive}", "Biaya Angkut (/Kg)");
$objPHPExcel->getActiveSheet()->setCellValue("AA{$rowActive}", "Biaya Angkut");
$objPHPExcel->getActiveSheet()->setCellValue("AB{$rowActive}", "Klaim Susut");
$objPHPExcel->getActiveSheet()->setCellValue("AC{$rowActive}", "Total Biaya Angkut");

$objPHPExcel->getActiveSheet()->setCellValue("AD{$rowActive}", "Biaya Bongkar");
$objPHPExcel->getActiveSheet()->setCellValue("AE{$rowActive}", "Total Biaya Bongkar");
$objPHPExcel->getActiveSheet()->setCellValue("AF{$rowActive}", "Vendor Handling");

$objPHPExcel->getActiveSheet()->setCellValue("AG{$rowActive}", "Jumlah Handling");
$objPHPExcel->getActiveSheet()->setCellValue("AH{$rowActive}", "Biaya Handling");
$objPHPExcel->getActiveSheet()->setCellValue("AI{$rowActive}", "Biaya Handling (/Kg)");
$objPHPExcel->getActiveSheet()->setCellValue("AJ{$rowActive}", "R");
$objPHPExcel->getActiveSheet()->setCellValue("AK{$rowActive}", "R (Tanggal)");
$objPHPExcel->getActiveSheet()->setCellValue("AL{$rowActive}", "Slip Reference");
$objPHPExcel->getActiveSheet()->setCellValue("AM{$rowActive}", "Type");
$objPHPExcel->getActiveSheet()->setCellValue("AN{$rowActive}", "Inventory");

$objPHPExcel->getActiveSheet()->setCellValue("AO{$rowActive}", "Balance (Q)");
$objPHPExcel->getActiveSheet()->setCellValue("AP{$rowActive}", "Stockpile Contract ID");
$objPHPExcel->getActiveSheet()->setCellValue("AQ{$rowActive}", "Freight Cost ID");
$objPHPExcel->getActiveSheet()->setCellValue("AR{$rowActive}", "Handling Cost ID");
$objPHPExcel->getActiveSheet()->setCellValue("AS{$rowActive}", "Labor ID");
$objPHPExcel->getActiveSheet()->setCellValue("AT{$rowActive}", "Unloading Cost ID");
$objPHPExcel->getActiveSheet()->setCellValue("AU{$rowActive}", "Vendor Bongkar");
$objPHPExcel->getActiveSheet()->setCellValue("AV{$rowActive}", "Sumber PKS");



$objPHPExcel->getActiveSheet()->setCellValue("AW{$rowActive}", "Labor Rules Price");
//$objPHPExcel->getActiveSheet()->setCellValue("AQ{$rowActive}", "Entry Date");

$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray4);
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Body">

    $balanceQuantity = $balanceBefore;
            $no = 1;
            while($row = $result->fetch_object()) {
                $balanceQuantity = $balanceQuantity + $row->quantity2;
				$pks_price = $row->pks_price;
				
				if($row->transaction_type == 2){
					if($row->quantity < 0){
						$quantity = $row->quantity * -1;
					}else{
						$quantity = '-' .$row->quantity;
					}
				}else{
					$quantity = $row->quantity;
				}
				
				// if($row->contract_type2 == 'Curah' && $row->transaction_type == 1){
				// 	$shrink = 0;
                //     // $shrink = $row->shrink;
                //     // echo "shrink2 " . $tempTest . " shrink ori2 " . $tempTest2;
				// }else{
				// 	$shrink = $row->shrink;
                //     // $tempTest = $tempTest + $row->shrink;
                //     // $tempTest2 = $tempTest2 + $shrink;
                //     // echo "shrink " . $tempTest . " shrink ori " . $tempTest2;
				// }
                $shrink = $row->shrink;

				// die();
				if($row->freight_rule == 1){
					$fp = $row->freight_quantity * $row->freight_price;
				}else{
					$fp = $row->freight_quantity * $row->freight_price;
				}
				
				if($row->vendor_handling_rule == 1){
					$hp = $row->handling_quantity * $row->handling_price;
				}else{
					$hp = $row->handling_quantity * $row->handling_price;
				}
				
				if($row->freight_cost_id != 0 && $row->fc_pph_id != 0 && $row->fc_pph_category == 1){
					$fc = $fp;
					$fc_shrink = ($row->qtyClaim * $row->shrink_claim) / ((100 - $row->fc_pph) / 100);
					$fcTotal = $fc / ((100 - $row->fc_pph) / 100);
					$fc_total = $fcTotal - $fc_shrink;
				}elseif($row->freight_cost_id != 0){
					$fc_shrink = ($row->qtyClaim * $row->shrink_claim);
					$fcTotal = $fp;
					$fc_total = $fp - $fc_shrink;
				}else{
					$fc_shrink = 0;
					$fcTotal = 0;
					$fc_total = 0;
				}
				
				if($row->handling_cost_id != 0 && $row->hc_pph_id != 0 && $row->hc_pph_category == 1){
					$hc = $hp;
					$hc_total = $hc / ((100 - $row->hc_pph) / 100);
				}elseif($row->handling_cost_id != 0){
					$hc_total = $hp;
				}else{
					$hc_total = 0;
				}
				
				if($row->unloading_cost_id != 0){
					$uc_total = $row->uc_price;
				}else{
					$uc_total = 0;
				}
			
			if($row->slip_no == 'DUM-000000092A'){
				$pks_price = 575;
			
			}
			
			if($row->transaction_type == 2){
				$pks_price2 = $row->pks_price;
				$pks_price = $row->pks_price/$row->quantity;
			}else{
                $pks_price2 = $pks_price * $row->quantity;
            }
           
    $rowActive++;
    
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $no);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("B{$rowActive}", $row->slip_no, PHPExcel_Cell_DataType::TYPE_STRING);
	//$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $row->slip_no);
    $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $row->stockpile_name);
    $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $row->unloading_date2);
//    $objPHPExcel->getActiveSheet()->getCell("D{$rowActive}")->setValueExplicit(PHPExcel_Shared_Date::stringToExcel($rowPolicy->unloading_date2), PHPExcel_Cell_DataType::TYPE_NUMERIC);
    $objPHPExcel->getActiveSheet()->getCell("E{$rowActive}")->setValueExplicit($row->vehicle_no, PHPExcel_Cell_DataType::TYPE_STRING);
//    $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $row->vehicle_no);
    $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $row->vehicle_name);
    $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $row->loading_date2);
	$objPHPExcel->getActiveSheet()->getCell("H{$rowActive}")->setValueExplicit($row->freight_code, PHPExcel_Cell_DataType::TYPE_STRING);
   // $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $row->freight_code);
    $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $row->permit_no);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("J{$rowActive}", $row->po_no, PHPExcel_Cell_DataType::TYPE_STRING);
    //$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $row->po_no);
    $objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $row->vendor_name);
    $objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $row->supplier);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("M{$rowActive}", $row->contract_no, PHPExcel_Cell_DataType::TYPE_STRING);
    //$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $row->contract_no);
    $objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", $row->transaction_type2);
    $objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", $row->send_weight);
    $objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", $row->bruto_weight);
    $objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", $row->tarra_weight);
    $objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", $row->netto_weight);
	$objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", $shrink);
	$objPHPExcel->getActiveSheet()->setCellValue("T{$rowActive}", $row->qty_add_shrink);
	$objPHPExcel->getActiveSheet()->setCellValue("U{$rowActive}", $pks_price);
	$objPHPExcel->getActiveSheet()->setCellValue("V{$rowActive}", $pks_price2);
    $objPHPExcel->getActiveSheet()->setCellValue("W{$rowActive}", $row->notes);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("X{$rowActive}", $row->driver, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("Y{$rowActive}", $row->freight_quantity);
    $objPHPExcel->getActiveSheet()->setCellValue("Z{$rowActive}", $row->freight_price);
	$objPHPExcel->getActiveSheet()->setCellValue("AA{$rowActive}", $fcTotal);
	$objPHPExcel->getActiveSheet()->setCellValue("AB{$rowActive}", $fc_shrink);
	$objPHPExcel->getActiveSheet()->setCellValue("AC{$rowActive}", $fc_total);
    $objPHPExcel->getActiveSheet()->setCellValue("AD{$rowActive}", $row->unloading_price);
    $objPHPExcel->getActiveSheet()->setCellValue("AE{$rowActive}", $uc_total);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("AF{$rowActive}", $row->vendor_handling_name, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("AG{$rowActive}", $row->handling_quantity);

    $objPHPExcel->getActiveSheet()->setCellValue("AH{$rowActive}", $hc_total);
	$objPHPExcel->getActiveSheet()->setCellValue("AI{$rowActive}", $row->handling_price);
    $objPHPExcel->getActiveSheet()->getStyle("AJ{$rowActive}:AK{$rowActive}")->applyFromArray($styleArray9);
    $objPHPExcel->getActiveSheet()->setCellValue("AJ{$rowActive}", $row->user_name);
	$objPHPExcel->getActiveSheet()->setCellValue("AK{$rowActive}", $row->modify_date);
    $objPHPExcel->getActiveSheet()->setCellValue("AL{$rowActive}", $row->slip_retur);
    $objPHPExcel->getActiveSheet()->setCellValue("AM{$rowActive}", $row->contract_type2);
    $objPHPExcel->getActiveSheet()->setCellValue("AN{$rowActive}", $quantity);
    
    $objPHPExcel->getActiveSheet()->setCellValue("AO{$rowActive}", $balanceQuantity);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("AP{$rowActive}");
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("AQ{$rowActive}");
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("AR{$rowActive}");
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("AS{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("AT{$rowActive}");
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("AU{$rowActive}", $row->labor_name, PHPExcel_Cell_DataType::TYPE_STRING);
	if($row->contract_pks_detail_id != '') {
        $objPHPExcel->getActiveSheet()->setCellValueExplicit("AV{$rowActive}", $row->vendor_curah, PHPExcel_Cell_DataType::TYPE_STRING);
    } else {
	    $objPHPExcel->getActiveSheet()->setCellValueExplicit("AV{$rowActive}", $row->vendor_name, PHPExcel_Cell_DataType::TYPE_STRING);
    }
    // $objPHPExcel->getActiveSheet()->setCellValue("AR{$rowActive}", $row->freight_quantity); //X
    // $objPHPExcel->getActiveSheet()->setCellValue("AS{$rowActive}", $row->quantity); //AC
    // $objPHPExcel->getActiveSheet()->setCellValue("AU{$rowActive}", $row->unloading_price); //AF
    $objPHPExcel->getActiveSheet()->setCellValue("AW{$rowActive}", $row->laborRules);



	//$objPHPExcel->getActiveSheet()->setCellValue("AQ{$rowActive}", $row->entry_date);

    $no++;
   
}
$bodyRowEnd = $rowActive;

//        if ($bodyRowEnd > $headerRow + 1) {
//            $rowActive++;
//
//            $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:K{$rowActive}");
//            $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "T O T A L");
//            $objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "=SUM(L" . ($headerRow + 1) . ":L{$bodyRowEnd})");
//            $objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "=SUM(M" . ($headerRow + 1) . ":M{$bodyRowEnd})");
//
//            // Set number format for Amount 
//            $objPHPExcel->getActiveSheet()->getStyle("L{$rowActive}:M{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
//            
//
//            // Set border for table
//            $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
//
//            // Set row TOTAL to bold
//            $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->getFont()->setBold(true);
//        }
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Formating Excel">
// Set column width
for ($temp = ord("A"); $temp <= ord("AV"); $temp++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
}
$objPHPExcel->getActiveSheet()->getColumnDimension("AF")->setAutoSize(true);

// Set format date in cell
if ($bodyRowEnd > $headerRow) {
    $objPHPExcel->getActiveSheet()->getStyle("D" . ($headerRow + 1) . ":D{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
    $objPHPExcel->getActiveSheet()->getStyle("G" . ($headerRow + 1) . ":G{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
	$objPHPExcel->getActiveSheet()->getStyle("AJ" . ($headerRow + 1) . ":AJ{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
}

//$objPHPExcel->getActiveSheet()->getStyle("E" . ($headerRow + 1) . ":E{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

// Set number format for Amount 
$objPHPExcel->getActiveSheet()->getStyle("O" . ($headerRow + 1) . ":V{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle("X" . ($headerRow + 1) . ":AH{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle("AM" . ($headerRow + 1) . ":AN{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
// $objPHPExcel->getActiveSheet()->getStyle("AQ" . ($headerRow + 1) . ":AR{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
//  $objPHPExcel->getActiveSheet()->getStyle("AR" . ($headerRow + 1) . ":AU{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

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