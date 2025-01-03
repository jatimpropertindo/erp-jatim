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
		'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
		'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
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
		'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
		'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
	)
);

$styleArray2 = array(
	'font' => array(
		'bold' => true,
		'size' => 14
	),
	'alignment' => array(
		'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
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
		'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
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
		'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
		'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
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
		'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
		'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
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
		'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
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
		'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
		'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
	)
);

$styleArray9 = array(
	'font' => array(
		'bold' => true,
		'size' => 14
	),
	'alignment' => array(
		'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
		'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
	)
);
// </editor-fold>

$whereProperty = '';
$whereProperty3 = '';
//$sumProperty = '';
//$balanceBefore = 0;
//$boolBalanceBefore = false;
//$stockpileId = $myDatabase->real_escape_string($_POST['stockpileId']);
$periodFrom = $myDatabase->real_escape_string($_POST['periodFrom']);
$periodTo = $myDatabase->real_escape_string($_POST['periodTo']);
$pTypes = $_POST['pTypes'];
$pLocations = $_POST['pLocations'];
$paymentNos = $_POST['paymentNos'];
$bankIds = $_POST['bankIds'];
$paymentIds = $_POST['paymentIds'];
$batchUpload = $_POST['batchUpload'];
$paymentTypes = $_POST['paymentTypes'];
$fileFormat = $_POST['fileFormat'];
//$stockpileName = 'All ';
$periodFull = '';
//$from = new DateTime('$periodFrom');
//$newPeriodFrom = date_format('$periodFrom','d F Y');
// <editor-fold defaultstate="collapsed" desc="Query">
/*
if ($stockpileId != '') {
    $stockpileId = $_POST['stockpileId'];
    $sql = "SELECT stockpile_code, stockpile_name FROM stockpile WHERE stockpile_id = {$stockpileId}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    $row = $result->fetch_object();
    
    $whereProperty .= " AND t.slip_no like '{$row->stockpile_code}%' ";
    $sumProperty .= " AND t.slip_no like '{$row->stockpile_code}%' ";
    
//    $whereProperty .= " AND t.slip_no like '{$stockpileId}%' ";
//    $sumProperty .= " AND t.slip_no like '{$stockpileId}%' ";

//    $sql = "SELECT * FROM stockpile WHERE stockpile_id = {$stockpileId}";
//    $resultStockpile = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
//    $rowStockpile = $resultStockpile->fetch_object();
    $stockpileName = $row->stockpile_name . " ";
}
*/
$sql = "SELECT * FROM user_module WHERE user_id = {$_SESSION['userId']} AND module_id = 27";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
if ($result->num_rows > 0) {
	while ($row = $result->fetch_object()) {
		if ($row->module_id == 27) {
			$whereProperty = "";
			if ($pLocations != '') {
				$whereProperty3 .= "AND (CASE WHEN p.payment_location = 0 THEN 'HO' ELSE 'Stockpile' END) IN ({$pLocations})";
			}
		} else {
			$whereProperty = "AND p.entry_by = {$_SESSION['userId']}";
		}
	}
}
if ($pTypes != '') {
	$whereProperty .= "AND p.payment_type2 IN ({$pTypes})";
}

if ($paymentNos != '') {
	$whereProperty .= "AND p.payment_no IN ({$paymentNos})";
}

// if ($bankIds != '') {
// 	$whereProperty .= "AND b.bank_id = {$bankIds}";
// }

if ($paymentIds != '') {
	$whereProperty .= "AND p.payment_id IN ({$paymentIds})";
}


if ($periodFrom != '' && $periodTo != '') {
	$whereProperty .= " AND DATE_FORMAT(p.payment_date, '%Y-%m-%d') BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
	// $sumProperty .= " AND IF(t.transaction_type = 1, t.unloading_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
	// $boolBalanceBefore = true;
	$periodFull = $periodFrom . " - " . $periodTo . " ";
} else if ($periodFrom != '' && $periodTo == '') {
	$whereProperty .= " AND DATE_FORMAT(p.payment_date, '%Y-%m-%d') >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";
	//$sumProperty .= " AND IF(t.transaction_type = 1, t.unloading_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
	// $boolBalanceBefore = true;
	$periodFull = "From " . $periodFrom . " ";
} else if ($periodFrom == '' && $periodTo != '') {
	$whereProperty .= " AND DATE_FORMAT(p.payment_date, '%Y-%m-%d') <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";
	$periodFull = "To " . $periodTo . " ";
}

$sql = "SELECT p.payment_id, p.payment_no, p.payment_date, p.entry_date, p.payment_type,
CASE WHEN p.payment_location = 0 THEN 'HOF'
ELSE ps.stockpile_name END AS payment_location,
CASE WHEN p.payment_location = 0 THEN 'HO'
ELSE 'Stockpile' END AS payment_location2,
CASE WHEN p.payment_type = 1 THEN 'IN'
ELSE 'OUT' END AS paymentType,
b.bank_code, b.bank_type, b.bank_account_no, pcur.currency_code AS pcur_currency_code, p.payment_type2,
CASE WHEN p.payment_type2 = 1 THEN 'TT'
				WHEN p.payment_type2 = 2 THEN 'Cek/Giro'
				WHEN p.payment_type2 = 3 THEN 'Tunai'
				WHEN p.payment_type2 = 4 THEN 'Bill Payment'
				WHEN p.payment_type2 = 5 THEN 'Auto Debet'
			ELSE 'TT' END AS p_type,

CASE WHEN p.stockpile_contract_id IS NOT NULL AND p.payment_date > '2019-10-12' THEN (SELECT TRIM(REPLACE(REPLACE(REPLACE(account_no,'-',''),'.',''),' ','')) FROM vendor_bank WHERE v_bank_id = p.vendor_bank_id)
	WHEN p.invoice_id IS NOT NULL AND p.payment_date > '2019-10-12' THEN (SELECT TRIM(REPLACE(REPLACE(REPLACE(account_no,'-',''),'.',''),' ','')) FROM general_vendor_bank WHERE gv_bank_id = p.vendor_bank_id)
	WHEN p.payment_cash_id IS NOT NULL AND p.payment_date > '2019-10-12' THEN (SELECT TRIM(REPLACE(REPLACE(REPLACE(account_no,'-',''),'.',''),' ','')) FROM general_vendor_bank WHERE gv_bank_id = p.vendor_bank_id)
	WHEN p.freight_id IS NOT NULL AND p.payment_date > '2019-10-12' THEN (SELECT TRIM(REPLACE(REPLACE(REPLACE(account_no,'-',''),'.',''),' ','')) FROM freight_bank WHERE f_bank_id = p.vendor_bank_id)
	WHEN p.vendor_handling_id IS NOT NULL AND p.payment_date > '2019-10-12' THEN (SELECT TRIM(REPLACE(REPLACE(REPLACE(account_no,'-',''),'.',''),' ','')) FROM vendor_handling_bank WHERE vh_bank_id = p.vendor_bank_id)
	WHEN p.labor_id IS NOT NULL AND p.payment_date > '2019-10-12' THEN (SELECT TRIM(REPLACE(REPLACE(REPLACE(account_no,'-',''),'.',''),' ','')) FROM labor_bank WHERE l_bank_id = p.vendor_bank_id)
	WHEN p.vendor_id IS NOT NULL AND p.payment_date > '2019-10-12' THEN (SELECT TRIM(REPLACE(REPLACE(REPLACE(account_no,'-',''),'.',''),' ','')) FROM vendor_bank WHERE v_bank_id = p.vendor_bank_id)
	WHEN p.general_vendor_id IS NOT NULL THEN TRIM(REPLACE(REPLACE(REPLACE(gv.account_no,'-',''),'.',''),' ',''))
	WHEN p.stockpile_contract_id IS NOT NULL THEN cv.account_no
	WHEN p.invoice_id IS NOT NULL THEN (SELECT TRIM(REPLACE(REPLACE(REPLACE(gv.account_no,'-',''),'.',''),' ','')) FROM general_vendor gv LEFT JOIN invoice_detail id ON gv.general_vendor_id = id.general_vendor_id WHERE id.invoice_id = p.invoice_id LIMIT 1)
	WHEN p.payment_cash_id IS NOT NULL THEN (SELECT TRIM(REPLACE(REPLACE(REPLACE(gv.account_no,'-',''),'.',''),' ','')) FROM general_vendor gv LEFT JOIN payment_cash pc ON gv.general_vendor_id = pc.general_vendor_id WHERE pc.payment_id = p.payment_id LIMIT 1)
	WHEN p.vendor_id IS NOT NULL THEN TRIM(REPLACE(REPLACE(REPLACE(v.account_no,'-',''),'.',''),' ',''))
	WHEN p.sales_id IS NOT NULL THEN TRIM(REPLACE(REPLACE(REPLACE(cust.account_no,'-',''),'.',''),' ',''))
	WHEN p.freight_id IS NOT NULL THEN TRIM(REPLACE(REPLACE(REPLACE(f.account_no,'-',''),'.',''),' ',''))
	WHEN p.invoice_sales_oa_id IS NOT NULL THEN TRIM(REPLACE(REPLACE(REPLACE(flsb.account_no,'-',''),'.',''),' ',''))
	WHEN p.vendor_handling_id IS NOT NULL THEN TRIM(REPLACE(REPLACE(REPLACE(vh.account_no,'-',''),'.',''),' ',''))
	WHEN p.labor_id IS NOT NULL THEN TRIM(REPLACE(REPLACE(REPLACE(l.account_no,'-',''),'.',''),' ',''))
ELSE (SELECT TRIM(REPLACE(REPLACE(REPLACE(no_rek,'-',''),'.',''),' ','')) FROM vendor_pettycash WHERE account_no = a.account_no) END AS no_rek,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN cv.vendor_name
	WHEN p.invoice_id IS NOT NULL THEN (SELECT gv.general_vendor_name FROM general_vendor gv LEFT JOIN invoice_detail id ON gv.general_vendor_id = id.general_vendor_id WHERE id.invoice_id = p.invoice_id LIMIT 1)
	WHEN p.payment_cash_id IS NOT NULL THEN (SELECT gv.general_vendor_name FROM general_vendor gv LEFT JOIN payment_cash pc ON gv.general_vendor_id = pc.general_vendor_id WHERE pc.payment_id = p.payment_id LIMIT 1)
	WHEN p.vendor_id IS NOT NULL THEN v.vendor_name
	WHEN p.sales_id IS NOT NULL THEN cust.customer_name
	WHEN p.freight_id IS NOT NULL THEN f.freight_supplier
	WHEN p.invoice_sales_oa_id IS NOT NULL THEN fls.freight_supplier
	WHEN p.vendor_handling_id IS NOT NULL THEN vh.vendor_handling_name
	WHEN p.labor_id IS NOT NULL THEN l.labor_name
	WHEN p.general_vendor_id IS NOT NULL THEN gv.general_vendor_name
ELSE (SELECT vendor_name FROM vendor_pettycash WHERE account_no = a.account_no) END AS vendor_name,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN CONCAT('(Contract No: ',c.contract_no,') - ',p.remarks)
	WHEN p.invoice_id IS NOT NULL THEN i.remarks
	WHEN p.payment_cash_id IS NOT NULL THEN p.remarks
	WHEN p.vendor_id IS NOT NULL THEN p.remarks
	WHEN p.sales_id IS NOT NULL THEN p.remarks
	WHEN p.freight_id IS NOT NULL THEN p.remarks
	WHEN p.vendor_handling_id IS NOT NULL THEN p.remarks
	WHEN p.labor_id IS NOT NULL THEN p.remarks
	WHEN p.general_vendor_id IS NOT NULL THEN p.remarks
ELSE p.remarks END AS keterangan,
CASE WHEN p.stockpile_contract_id IS NOT NULL AND p.payment_date > '2019-10-12' THEN (SELECT bank_name FROM vendor_bank WHERE v_bank_id = p.vendor_bank_id)
	WHEN p.vendor_id IS NOT NULL AND p.payment_date > '2019-10-12' THEN (SELECT bank_name FROM vendor_bank WHERE v_bank_id = p.vendor_bank_id)
	WHEN p.invoice_id IS NOT NULL AND p.payment_date > '2019-10-12' THEN (SELECT bank_name FROM general_vendor_bank WHERE gv_bank_id = p.vendor_bank_id)
	WHEN p.payment_cash_id IS NOT NULL AND p.payment_date > '2019-10-12' THEN (SELECT bank_name FROM general_vendor_bank WHERE gv_bank_id = p.vendor_bank_id)

	WHEN p.freight_id IS NOT NULL AND p.payment_date > '2019-10-12' THEN (SELECT bank_name FROM freight_bank WHERE f_bank_id = p.vendor_bank_id)
	WHEN p.vendor_handling_id IS NOT NULL AND p.payment_date > '2019-10-12' THEN (SELECT bank_name FROM vendor_handling_bank WHERE vh_bank_id = p.vendor_bank_id)
	WHEN p.labor_id IS NOT NULL AND p.payment_date > '2019-10-12' THEN (SELECT bank_name FROM labor_bank WHERE l_bank_id = p.vendor_bank_id)

	WHEN p.stockpile_contract_id IS NOT NULL THEN cv.bank_name
	WHEN p.invoice_sales_oa_id IS NOT NULL THEN flsb.bank_name
	WHEN p.invoice_id IS NOT NULL THEN (SELECT gv.bank_name FROM general_vendor gv LEFT JOIN invoice_detail id ON gv.general_vendor_id = id.general_vendor_id WHERE id.invoice_id = p.invoice_id LIMIT 1)
	WHEN p.payment_cash_id IS NOT NULL THEN (SELECT gv.bank_name FROM general_vendor gv LEFT JOIN payment_cash pc ON gv.general_vendor_id = pc.general_vendor_id WHERE pc.payment_id = p.payment_id LIMIT 1)
	WHEN p.vendor_id IS NOT NULL THEN v.bank_name
	WHEN p.sales_id IS NOT NULL THEN cust.bank_name
	WHEN p.freight_id IS NOT NULL THEN f.bank_name
	WHEN p.vendor_handling_id IS NOT NULL THEN vh.bank_name
	WHEN p.labor_id IS NOT NULL THEN l.bank_name
	WHEN p.general_vendor_id IS NOT NULL THEN gv.bank_name
	
ELSE (SELECT bank FROM vendor_pettycash WHERE account_no = a.account_no) END AS bank_name,


CASE WHEN p.stockpile_contract_id IS NOT NULL THEN (SELECT kode1 FROM master_bank mb LEFT JOIN vendor_bank vb ON mb.master_bank_id = vb.master_bank_id WHERE vb.v_bank_id = p.vendor_bank_id)
	WHEN p.invoice_id IS NOT NULL THEN (SELECT kode1 FROM master_bank mb LEFT JOIN general_vendor_bank gvb ON mb.master_bank_id = gvb.master_bank_id WHERE gvb.gv_bank_id = p.vendor_bank_id)
	WHEN p.payment_cash_id IS NOT NULL THEN (SELECT kode1 FROM master_bank mb LEFT JOIN general_vendor_bank gvb ON mb.master_bank_id = gvb.master_bank_id WHERE gvb.gv_bank_id = p.vendor_bank_id)
  WHEN p.freight_id IS NOT NULL THEN (SELECT kode1 FROM master_bank mb LEFT JOIN freight_bank fb ON fb.master_bank_id=mb.master_bank_id WHERE fb.f_bank_id = p.vendor_bank_id)
	WHEN p.vendor_handling_id IS NOT NULL THEN (SELECT kode1 FROM master_bank mb LEFT JOIN vendor_handling_bank vhb ON vhb.master_bank_id = mb.master_bank_id WHERE vhb.vh_bank_id = p.vendor_bank_id)
	WHEN p.labor_id IS NOT NULL THEN (SELECT kode1 FROM master_bank mb LEFT JOIN labor_bank lb ON lb.master_bank_id=mb.master_bank_id WHERE lb.l_bank_id = p.vendor_bank_id)
	WHEN p.vendor_id IS NOT NULL THEN  (SELECT kode1 FROM master_bank mb LEFT JOIN vendor_bank vb ON mb.master_bank_id = vb.master_bank_id WHERE vb.v_bank_id = p.vendor_bank_id)
	WHEN p.sales_id IS NOT NULL THEN ''
	WHEN p.general_vendor_id IS NOT NULL THEN (SELECT kode1 FROM master_bank mb LEFT JOIN general_vendor_bank gvb ON mb.master_bank_id = gvb.master_bank_id WHERE gvb.gv_bank_id = p.vendor_bank_id)
ELSE (SELECT kode1 FROM master_bank mb LEFT JOIN vendor_pettycash vpc ON vpc.master_bank_id=mb.master_bank_id WHERE vpc.account_no = a.account_no) END AS kode1,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN (SELECT kode2 FROM master_bank mb LEFT JOIN vendor_bank vb ON mb.master_bank_id = vb.master_bank_id WHERE vb.v_bank_id = p.vendor_bank_id)
	WHEN p.invoice_id IS NOT NULL THEN (SELECT kode2 FROM master_bank mb LEFT JOIN general_vendor_bank gvb ON mb.master_bank_id = gvb.master_bank_id WHERE gvb.gv_bank_id = p.vendor_bank_id)
	WHEN p.payment_cash_id IS NOT NULL THEN (SELECT kode2 FROM master_bank mb LEFT JOIN general_vendor_bank gvb ON mb.master_bank_id = gvb.master_bank_id WHERE gvb.gv_bank_id = p.vendor_bank_id)
	WHEN p.freight_id IS NOT NULL THEN (SELECT kode2 FROM master_bank mb LEFT JOIN freight_bank fb ON fb.master_bank_id=mb.master_bank_id WHERE fb.f_bank_id = p.vendor_bank_id)
	WHEN p.vendor_handling_id IS NOT NULL THEN (SELECT kode2 FROM master_bank mb LEFT JOIN vendor_handling_bank vhb ON vhb.master_bank_id = mb.master_bank_id WHERE vhb.vh_bank_id = p.vendor_bank_id)
	WHEN p.labor_id IS NOT NULL THEN (SELECT kode2 FROM master_bank mb LEFT JOIN labor_bank lb ON lb.master_bank_id=mb.master_bank_id WHERE lb.l_bank_id = p.vendor_bank_id)
	WHEN p.vendor_id IS NOT NULL THEN  (SELECT kode2 FROM master_bank mb LEFT JOIN vendor_bank vb ON mb.master_bank_id = vb.master_bank_id WHERE vb.v_bank_id = p.vendor_bank_id)
	WHEN p.sales_id IS NOT NULL THEN ''
	WHEN p.general_vendor_id IS NOT NULL THEN (SELECT kode2 FROM master_bank mb LEFT JOIN general_vendor_bank gvb ON mb.master_bank_id = gvb.master_bank_id WHERE gvb.gv_bank_id = p.vendor_bank_id)
ELSE (SELECT kode2 FROM master_bank mb LEFT JOIN vendor_pettycash vpc ON vpc.master_bank_id=mb.master_bank_id WHERE vpc.account_no = a.account_no) END AS kode2,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN 'PKS'
	WHEN p.invoice_id IS NOT NULL THEN 'INVOICE'
	WHEN p.payment_cash_id IS NOT NULL THEN 'PETTY CASH'
	WHEN p.freight_id IS NOT NULL THEN 'FREIGHT COST'
	WHEN p.vendor_handling_id IS NOT NULL THEN 'HANDLING COST'
	WHEN p.labor_id IS NOT NULL THEN 'UNLOADING COST'
	WHEN p.vendor_id IS NOT NULL THEN 'PKS CURAH'
	WHEN p.sales_id IS NOT NULL THEN ''
	WHEN p.general_vendor_id IS NOT NULL THEN ''
	WHEN p.invoice_sales_oa_id IS NOT NULL THEN 'SHIPMENT'
	WHEN p.invoice_sales_id IS NOT NULL THEN 'SHIPMENT'
ELSE 'INTERNAL TRANSFER' END AS kode3,

CASE WHEN p.stockpile_contract_id IS NOT NULL AND p.payment_date > '2019-10-12'  THEN (SELECT branch FROM vendor_bank WHERE v_bank_id = p.vendor_bank_id)
	WHEN p.vendor_id IS NOT NULL AND p.payment_date > '2019-10-12'  THEN (SELECT branch FROM vendor_bank WHERE v_bank_id = p.vendor_bank_id)
	WHEN p.invoice_id IS NOT NULL  AND p.payment_date > '2019-10-12' THEN (SELECT branch FROM general_vendor_bank WHERE gv_bank_id = p.vendor_bank_id)
	WHEN p.payment_cash_id IS NOT NULL AND p.payment_date > '2019-10-12' THEN (SELECT branch FROM general_vendor_bank WHERE gv_bank_id = p.vendor_bank_id)
	WHEN p.invoice_sales_oa_id IS NOT NULL THEN flsb.branch

	WHEN p.freight_id IS NOT NULL AND p.payment_date > '2019-10-12' THEN (SELECT branch FROM freight_bank WHERE f_bank_id = p.vendor_bank_id)
	WHEN p.vendor_handling_id IS NOT NULL AND p.payment_date > '2019-10-12' THEN (SELECT branch FROM vendor_handling_bank WHERE vh_bank_id = p.vendor_bank_id)
	WHEN p.labor_id IS NOT NULL AND p.payment_date > '2019-10-12' THEN (SELECT branch FROM labor_bank WHERE l_bank_id = p.vendor_bank_id)

	WHEN p.stockpile_contract_id IS NOT NULL THEN cv.branch
	WHEN p.invoice_id IS NOT NULL THEN (SELECT gv.branch FROM general_vendor gv LEFT JOIN invoice_detail id ON gv.general_vendor_id = id.general_vendor_id WHERE id.invoice_id = p.invoice_id LIMIT 1)
	WHEN p.payment_cash_id IS NOT NULL THEN (SELECT gv.branch FROM general_vendor gv LEFT JOIN payment_cash pc ON gv.general_vendor_id = pc.general_vendor_id WHERE pc.payment_id = p.payment_id LIMIT 1)
	WHEN p.vendor_id IS NOT NULL THEN v.branch
	WHEN p.sales_id IS NOT NULL THEN cust.branch
	WHEN p.freight_id IS NOT NULL THEN f.branch
	WHEN p.vendor_handling_id IS NOT NULL THEN vh.branch
	WHEN p.labor_id IS NOT NULL THEN l.branch
	WHEN p.general_vendor_id IS NOT NULL THEN gv.branch
ELSE (SELECT branch FROM vendor_pettycash WHERE account_no = a.account_no) END AS branch,
CASE WHEN p.stockpile_contract_id IS NOT NULL AND p.payment_date > '2019-10-12' THEN (SELECT beneficiary FROM vendor_bank WHERE v_bank_id = p.vendor_bank_id)
	WHEN p.vendor_id IS NOT NULL AND p.payment_date > '2019-10-12' THEN (SELECT beneficiary FROM vendor_bank WHERE v_bank_id = p.vendor_bank_id)
	WHEN p.invoice_id IS NOT NULL AND p.payment_date > '2019-10-12' THEN (SELECT beneficiary FROM general_vendor_bank WHERE gv_bank_id = p.vendor_bank_id)
	WHEN p.payment_cash_id IS NOT NULL AND p.payment_date > '2019-10-12'  THEN (SELECT beneficiary FROM general_vendor_bank WHERE gv_bank_id = p.vendor_bank_id)
	WHEN p.invoice_sales_oa_id IS NOT NULL THEN flsb.beneficiary

	WHEN p.freight_id IS NOT NULL AND p.payment_date > '2019-10-12' THEN (SELECT beneficiary FROM freight_bank WHERE f_bank_id = p.vendor_bank_id)
	WHEN p.vendor_handling_id IS NOT NULL AND p.payment_date > '2019-10-12' THEN (SELECT beneficiary FROM vendor_handling_bank WHERE vh_bank_id = p.vendor_bank_id)
	WHEN p.labor_id IS NOT NULL AND p.payment_date > '2019-10-12' THEN (SELECT beneficiary FROM labor_bank WHERE l_bank_id = p.vendor_bank_id)
	WHEN p.stockpile_contract_id IS NOT NULL THEN cv.beneficiary
	WHEN p.invoice_id IS NOT NULL THEN (SELECT gv.beneficiary FROM general_vendor gv LEFT JOIN invoice_detail id ON gv.general_vendor_id = id.general_vendor_id WHERE id.invoice_id = p.invoice_id LIMIT 1)
	WHEN p.payment_cash_id IS NOT NULL THEN (SELECT gv.beneficiary FROM general_vendor gv LEFT JOIN payment_cash pc ON gv.general_vendor_id = pc.general_vendor_id WHERE pc.payment_id = p.payment_id LIMIT 1)
	WHEN p.vendor_id IS NOT NULL THEN v.beneficiary
	WHEN p.sales_id IS NOT NULL THEN cust.beneficiary
	WHEN p.freight_id IS NOT NULL THEN f.beneficiary
	WHEN p.vendor_handling_id IS NOT NULL THEN vh.beneficiary
	WHEN p.labor_id IS NOT NULL THEN l.beneficiary
	WHEN p.general_vendor_id IS NOT NULL THEN gv.beneficiary
ELSE (SELECT beneficiary FROM vendor_pettycash WHERE account_no = a.account_no) END AS beneficiary,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN c.quantity
	WHEN p.invoice_id IS NOT NULL AND ((SELECT account_id FROM invoice_detail WHERE invoice_id = p.invoice_id LIMIT 1) = 249 OR (SELECT account_id FROM invoice_detail WHERE invoice_id = p.invoice_id LIMIT 1) = 167) THEN (SELECT qty FROM invoice_detail WHERE invoice_id = p.invoice_id LIMIT 1)
	WHEN p.invoice_id IS NOT NULL THEN 0
	WHEN p.payment_cash_id IS NOT NULL THEN 0
	WHEN p.vendor_id IS NOT NULL THEN p.qty
	WHEN p.sales_id IS NOT NULL THEN sl.quantity
	WHEN p.freight_id IS NOT NULL THEN p.qty
	WHEN p.vendor_handling_id IS NOT NULL THEN p.qty
	WHEN p.labor_id IS NOT NULL THEN p.qty
	WHEN p.general_vendor_id IS NOT NULL THEN p.qty
ELSE p.qty END AS quantity,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN c.price_converted
	WHEN p.invoice_id IS NOT NULL AND ((SELECT account_id FROM invoice_detail WHERE invoice_id = p.invoice_id LIMIT 1) = 249 OR (SELECT account_id FROM invoice_detail WHERE invoice_id = p.invoice_id LIMIT 1) = 167) THEN (SELECT price FROM invoice_detail WHERE invoice_id = p.invoice_id LIMIT 1)
	WHEN p.invoice_id IS NOT NULL THEN 0
	WHEN p.payment_cash_id IS NOT NULL THEN 0
	WHEN p.vendor_id IS NOT NULL AND p.payment_date >= '2022-11-01' THEN (SELECT price FROM payment_curah WHERE payment_id = p.payment_id GROUP BY payment_id)
	WHEN p.vendor_id IS NOT NULL THEN p.price
	WHEN p.sales_id IS NOT NULL THEN sl.price_converted
	WHEN p.freight_id IS NOT NULL THEN p.price
	WHEN p.vendor_handling_id IS NOT NULL THEN p.price
	WHEN p.labor_id IS NOT NULL THEN p.price
	WHEN p.general_vendor_id IS NOT NULL THEN p.price
ELSE p.price END AS price_converted,
CASE WHEN p.currency_id = 2 THEN p.amount
	WHEN p.stockpile_contract_id IS NOT NULL THEN (p.amount_converted - p.ppn_amount)
	WHEN p.invoice_id IS NOT NULL THEN (SELECT SUM(amount_converted) FROM invoice_detail WHERE invoice_id = p.invoice_id)
	WHEN p.payment_cash_id IS NOT NULL THEN (SELECT SUM(amount_converted) FROM payment_cash WHERE payment_id = p.payment_id)
	WHEN p.vendor_id IS NOT NULL AND p.ppn_amount_converted > 0 THEN p.amount_converted - p.ppn_amount_converted
	WHEN p.vendor_id IS NOT NULL THEN p.amount_converted
	when p.invoice_sales_oa_id is not null THEN (p.original_amount_converted + p.pph_amount_converted) - p.ppn_amount_converted
	WHEN p.sales_id IS NOT NULL THEN p.amount_converted
	WHEN p.freight_id IS NOT NULL AND p.payment_method = 2 THEN (p.amount_converted)
	WHEN p.freight_id IS NOT NULL AND p.ppn_amount_converted > 0 THEN (p.original_amount_converted + p.pph_amount_converted) - p.ppn_amount_converted
	WHEN p.freight_id IS NOT NULL THEN (p.original_amount_converted + p.pph_amount_converted)
	WHEN p.vendor_handling_id IS NOT NULL AND p.payment_method = 2 THEN p.amount_converted
	WHEN p.vendor_handling_id IS NOT NULL THEN (p.amount_converted)
	WHEN p.labor_id IS NOT NULL THEN (p.original_amount_converted + p.pph_amount_converted) - p.ppn_amount_converted
	WHEN p.general_vendor_id IS NOT NULL THEN p.amount_converted
ELSE p.amount_converted END AS dpp,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN p.ppn_amount
	WHEN p.invoice_id IS NOT NULL THEN (SELECT SUM(ppn_converted) FROM invoice_detail WHERE invoice_id = p.invoice_id)
	WHEN p.payment_cash_id IS NOT NULL THEN (SELECT SUM(ppn_converted) FROM payment_cash WHERE payment_id = p.payment_id)
	WHEN p.vendor_id IS NOT NULL THEN p.ppn_amount
	WHEN p.sales_id IS NOT NULL THEN p.ppn_amount
	WHEN p.freight_id IS NOT NULL THEN p.ppn_amount
	WHEN p.vendor_handling_id IS NOT NULL THEN p.ppn_amount
	WHEN p.labor_id IS NOT NULL THEN p.ppn_amount
	WHEN p.general_vendor_id IS NOT NULL THEN p.ppn_amount
ELSE p.ppn_amount END AS ppn_amount,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN p.pph_amount
	WHEN p.invoice_id IS NOT NULL AND p.currency_id != 1 THEN (SELECT SUM(pph)FROM invoice_detail WHERE invoice_id = p.invoice_id)
	WHEN p.invoice_id IS NOT NULL THEN (SELECT SUM(pph_converted) FROM invoice_detail WHERE invoice_id = p.invoice_id)
	WHEN p.payment_cash_id IS NOT NULL THEN (SELECT SUM(pph_converted) FROM payment_cash WHERE payment_id = p.payment_id)
	WHEN p.vendor_id IS NOT NULL THEN p.pph_amount
	WHEN p.sales_id IS NOT NULL THEN p.pph_amount
	WHEN p.freight_id IS NOT NULL THEN p.pph_amount
	WHEN p.vendor_handling_id IS NOT NULL THEN p.pph_amount
	WHEN p.labor_id IS NOT NULL THEN p.pph_amount
	WHEN p.general_vendor_id IS NOT NULL THEN p.pph_amount
ELSE p.pph_amount END AS pph_amount,
CASE WHEN p.currency_id = 2 THEN p.amount
	WHEN p.stockpile_contract_id IS NOT NULL THEN p.amount_converted
	WHEN p.invoice_id IS NOT NULL THEN (((SELECT SUM(amount_converted) FROM invoice_detail WHERE invoice_id = p.invoice_id) + (SELECT SUM(ppn_converted) FROM invoice_detail WHERE invoice_id = p.invoice_id)) - (SELECT SUM(pph_converted) FROM invoice_detail WHERE invoice_id = p.invoice_id))
	WHEN p.payment_cash_id IS NOT NULL THEN (((SELECT SUM(amount_converted) FROM payment_cash WHERE payment_id = p.payment_id) + (SELECT SUM(ppn_converted) FROM payment_cash WHERE payment_id = p.payment_id)) - (SELECT SUM(pph_converted) FROM payment_cash WHERE payment_id = p.payment_id))
	WHEN p.vendor_id IS NOT NULL THEN p.amount_converted
	WHEN p.sales_id IS NOT NULL THEN (p.amount_converted + p.ppn_amount)
	WHEN p.freight_id IS NOT NULL AND p.payment_method = 2 THEN ((p.amount_converted + p.ppn_amount) - p.pph_amount)
	
	WHEN p.freight_id IS NOT NULL THEN p.original_amount_converted
	WHEN p.vendor_handling_id IS NOT NULL AND p.payment_method = 2 THEN ((p.amount_converted + p.ppn_amount) - p.pph_amount)
	WHEN p.vendor_handling_id IS NOT NULL THEN ((p.amount_converted + p.ppn_amount) - p.pph_amount)
	WHEN p.labor_id IS NOT NULL THEN p.original_amount_converted
	WHEN p.general_vendor_id IS NOT NULL THEN ((p.amount_converted + p.ppn_amount) - p.pph_amount)
ELSE p.amount_converted END AS total,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN (p.amount_converted - p.original_amount)
	WHEN p.invoice_id IS NOT NULL THEN (SELECT (COALESCE(SUM(idp.amount_payment),0) + COALESCE(SUM(CASE WHEN iddp.`ppn` != 0 THEN idp.amount_payment * (ppn.`tax_value`/100) ELSE 0 END),0)) -
COALESCE(SUM(CASE WHEN iddp.pph != 0 THEN idp.amount_payment * (pph.`tax_value`/100) ELSE 0 END),0) FROM invoice_detail id
	LEFT JOIN invoice_dp idp ON idp.invoice_detail_id = id.invoice_detail_id
	LEFT JOIN invoice_detail iddp ON iddp.invoice_detail_id = idp.invoice_detail_dp
	LEFT JOIN tax ppn ON ppn.`tax_id` = iddp.`ppnID`
	LEFT JOIN tax pph ON pph.`tax_id` = iddp.`pphID`
	WHERE id.invoice_id = p.invoice_id AND idp.status = 0)
	WHEN p.payment_cash_id IS NOT NULL THEN (SELECT COALESCE(GROUP_CONCAT((SELECT ROUND(SUM(tamount),2) FROM payment_cash WHERE payment_cash_dp = pc.payment_cash_id)),0) FROM payment_cash pc WHERE pc.payment_id = p.payment_id)
	WHEN p.freight_id IS NOT NULL AND p.ppn_amount_converted > 0 THEN (p.original_amount_converted - p.amount_journal)
	WHEN p.freight_id IS NOT NULL THEN (p.amount_converted - p.amount_journal)
	WHEN p.vendor_handling_id IS NOT NULL THEN (((p.amount_converted + p.ppn_amount) - p.pph_amount) - p.original_amount)
	WHEN p.vendor_id IS NOT NULL THEN (p.amount_converted - p.original_amount)
ELSE 0 END AS dp,
CASE WHEN p.currency_id = 2 THEN p.amount
    WHEN p.stockpile_contract_id IS NOT NULL THEN (p.amount_converted - (p.amount_converted - p.original_amount))
	WHEN p.invoice_id IS NOT NULL THEN ((SELECT SUM((amount_converted + ppn_converted) - pph_converted) FROM invoice_detail WHERE invoice_id = p.invoice_id) - (SELECT (COALESCE(SUM(idp.amount_payment),0) + COALESCE(SUM(CASE WHEN iddp.`ppn` != 0 THEN idp.amount_payment * (ppn.`tax_value`/100) ELSE 0 END),0)) -
COALESCE(SUM(CASE WHEN iddp.pph != 0 THEN idp.amount_payment * (pph.`tax_value`/100) ELSE 0 END),0) FROM invoice_detail id
	LEFT JOIN invoice_dp idp ON idp.invoice_detail_id = id.invoice_detail_id
	LEFT JOIN invoice_detail iddp ON iddp.invoice_detail_id = idp.invoice_detail_dp
	LEFT JOIN tax ppn ON ppn.`tax_id` = iddp.`ppnID`
	LEFT JOIN tax pph ON pph.`tax_id` = iddp.`pphID`
	WHERE id.invoice_id = p.invoice_id AND idp.status = 0))
	WHEN p.payment_cash_id IS NOT NULL THEN ((SELECT SUM((amount_converted + ppn_converted) - pph_converted) FROM payment_cash WHERE payment_id = p.payment_id) - (SELECT COALESCE(GROUP_CONCAT((SELECT ROUND(SUM(tamount),2) FROM payment_cash WHERE payment_cash_dp = pc.payment_cash_id)),0) FROM payment_cash pc WHERE pc.payment_id = p.payment_id))
	WHEN p.vendor_id IS NOT NULL THEN p.original_amount
	WHEN p.invoice_sales_oa_id IS NOT NULL THEN p.amount_converted
	WHEN p.sales_id IS NOT NULL THEN (p.amount_converted + p.ppn_amount)
	WHEN p.freight_id IS NOT NULL AND p.payment_method = 2 THEN ((p.amount_converted + p.ppn_amount) - p.pph_amount) - (p.amount_converted - p.original_amount)
	WHEN p.freight_id IS NOT NULL AND p.ppn_amount_converted > 0 THEN (p.amount_converted - (p.amount_converted - p.amount_journal))
	WHEN p.freight_id IS NOT NULL THEN (p.original_amount_converted - (p.amount_converted - p.amount_journal))
	WHEN p.general_vendor_id IS NOT NULL THEN (((p.amount_converted + p.ppn_amount) - p.pph_amount) - (p.amount_converted - p.original_amount))
	WHEN p.vendor_handling_id IS NOT NULL AND p.payment_method = 2 THEN (p.amount_converted - p.pph_amount)
	WHEN p.vendor_handling_id IS NOT NULL THEN p.amount_converted
	WHEN p.labor_id IS NOT NULL THEN p.amount_converted
ELSE p.amount_converted END AS grand_total,
s.stockpile_name, u.user_name,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN cvppn.tax_name
	WHEN p.invoice_id IS NOT NULL THEN (SELECT tx.tax_name FROM tax tx LEFT JOIN general_vendor gv ON gv.ppn_tax_id = tx.tax_id LEFT JOIN invoice_detail id ON gv.general_vendor_id = id.general_vendor_id WHERE id.invoice_id = p.invoice_id LIMIT 1)
	WHEN p.payment_cash_id IS NOT NULL THEN (SELECT tx.tax_name FROM tax tx LEFT JOIN general_vendor gv ON gv.ppn_tax_id = tx.tax_id LEFT JOIN payment_cash pc ON gv.general_vendor_id = pc.general_vendor_id WHERE pc.payment_id = p.payment_id LIMIT 1)
	WHEN p.vendor_id IS NOT NULL THEN vppn.tax_name
	WHEN p.sales_id IS NOT NULL THEN custppn.tax_name
	WHEN p.freight_id IS NOT NULL THEN fppn.tax_name
	WHEN p.vendor_handling_id IS NOT NULL THEN vhppn.tax_name
	WHEN p.labor_id IS NOT NULL THEN lppn.tax_name
	WHEN p.general_vendor_id IS NOT NULL THEN gvppn.tax_name
ELSE '' END AS ppn,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN cvpph.tax_name
	WHEN p.invoice_id IS NOT NULL THEN (SELECT tx.tax_name FROM tax tx LEFT JOIN general_vendor gv ON gv.pph_tax_id = tx.tax_id LEFT JOIN invoice_detail id ON gv.general_vendor_id = id.general_vendor_id WHERE id.invoice_id = p.invoice_id LIMIT 1)
	WHEN p.payment_cash_id IS NOT NULL THEN (SELECT tx.tax_name FROM tax tx LEFT JOIN general_vendor gv ON gv.pph_tax_id = tx.tax_id LEFT JOIN payment_cash pc ON gv.general_vendor_id = pc.general_vendor_id WHERE pc.payment_id = p.payment_id LIMIT 1)
	WHEN p.vendor_id IS NOT NULL THEN vpph.tax_name
	WHEN p.sales_id IS NOT NULL THEN custpph.tax_name
	WHEN p.freight_id IS NOT NULL THEN fpph.tax_name
	WHEN p.vendor_handling_id IS NOT NULL THEN vhpph.tax_name
	WHEN p.labor_id IS NOT NULL THEN lpph.tax_name
	WHEN p.general_vendor_id IS NOT NULL THEN gvpph.tax_name
ELSE '' END AS pph,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN (SELECT  u.user_name FROM USER u 
		LEFT JOIN purchasing ps ON ps.entry_by = u.user_id 
		LEFT JOIN po_pks pks ON ps.purchasing_id = pks.purchasing_id 
		LEFT JOIN po_contract po ON pks.po_pks_id = po.po_pks_id
		LEFT JOIN contract c ON po.contract_id = c.contract_id
		LEFT JOIN stockpile_contract sc ON sc.contract_id = c.contract_id 
		WHERE p.stockpile_contract_id = sc.stockpile_contract_id LIMIT 1)
	WHEN p.invoice_id IS NOT NULL THEN IFNULL((SELECT u.user_name FROM USER u 
		LEFT JOIN pengajuan_general pg ON pg.entry_by = u.user_id LEFT JOIN invoice i
		ON i.invoice_id = pg.invoice_id WHERE p.invoice_id = i.invoice_id LIMIT 1),(SELECT u.user_name FROM USER WHERE p.entry_by = u.user_id LIMIT 1))
	WHEN p.payment_cash_id IS NOT NULL THEN (SELECT u.user_name FROM USER u 
		LEFT JOIN payment_cash pc ON u.user_id = pc.entry_by 
		WHERE pc.payment_id = p.payment_id LIMIT 1)
	WHEN p.vendor_id IS NOT NULL THEN (SELECT u.user_name FROM USER u 
		LEFT JOIN pengajuan_payment pp ON pp.user = u.user_id 
		LEFT JOIN payment_curah pc
		ON pc.idPP = pp.idPP WHERE pc.payment_id = p.payment_id LIMIT 1)
	WHEN p.freight_id IS NOT NULL THEN IFNULL((SELECT u.user_name FROM USER u 
		LEFT JOIN pengajuan_payment pp ON pp.user = u.user_id 
		LEFT JOIN payment_oa poa ON poa.idPP = pp.idPP 
		WHERE poa.payment_id = p.payment_id LIMIT 1),(SELECT u.user_name FROM USER WHERE p.entry_by = u.user_id LIMIT 1))
	ELSE (SELECT u.user_name FROM USER WHERE p.entry_by = u.user_id LIMIT 1) END AS entry_by,
	bud.batch_code
FROM payment p
LEFT JOIN stockpile_contract sc ON sc.stockpile_contract_id = p.`stockpile_contract_id`
LEFT JOIN contract c ON c.`contract_id` = sc.`contract_id`
LEFT JOIN vendor cv ON cv.`vendor_id` = c.`vendor_id`
LEFT JOIN invoice i ON i.invoice_id = p.invoice_id
LEFT JOIN vendor v ON v.`vendor_id` = p.`vendor_id`
LEFT JOIN freight f ON f.`freight_id` = p.`freight_id`
LEFT JOIN labor l ON l.`labor_id` = p.`labor_id`
LEFT JOIN vendor_handling vh ON vh.`vendor_handling_id` = p.`vendor_handling_id`
LEFT JOIN sales sl ON sl.`sales_id` = p.`sales_id`
LEFT JOIN customer cust ON cust.customer_id = sl.customer_id
LEFT JOIN general_vendor gv ON gv.`general_vendor_id` = p.`general_vendor_id`
LEFT JOIN stockpile s ON s.stockpile_id = p.stockpile_location
LEFT JOIN `user` u ON u.user_id = p.entry_by
LEFT JOIN tax cvppn ON cvppn.tax_id = cv.ppn_tax_id
LEFT JOIN tax cvpph ON cvpph.tax_id = cv.pph_tax_id
LEFT JOIN tax vppn ON vppn.tax_id = v.ppn_tax_id
LEFT JOIN tax vpph ON vpph.tax_id = v.pph_tax_id
LEFT JOIN tax custppn ON custppn.tax_id = cust.ppn_tax_id
LEFT JOIN tax custpph ON custpph.tax_id = cust.pph_tax_id
LEFT JOIN tax fppn ON fppn.tax_id = f.ppn_tax_id
LEFT JOIN tax fpph ON fpph.tax_id = f.pph_tax_id
LEFT JOIN tax vhppn ON vhppn.tax_id = vh.ppn_tax_id
LEFT JOIN tax vhpph ON vhpph.tax_id = vh.pph_tax_id
LEFT JOIN tax lppn ON lppn.tax_id = l.ppn_tax_id
LEFT JOIN tax lpph ON lpph.tax_id = l.pph_tax_id
LEFT JOIN tax gvppn ON gvppn.tax_id = gv.ppn_tax_id
LEFT JOIN tax gvpph ON gvpph.tax_id = gv.pph_tax_id
LEFT JOIN stockpile ps ON ps.stockpile_id = p.payment_location
LEFT JOIN bank b ON b.bank_id = p.bank_id
LEFT JOIN currency pcur ON pcur.currency_id = p.currency_id
LEFT JOIN account a ON a.account_id = p.account_id
LEFT JOIN invoice_sales_oa iso ON iso.inv_notim_id = p.invoice_sales_oa_id
LEFT JOIN freight_local_sales fls ON fls.freight_id = iso.freightId
LEFT JOIN freight_local_sales_bank flsb ON flsb.freight_id = fls.freight_id
LEFT JOIN batch_upload_detail bud ON bud.payment_id = p.payment_id
WHERE 1=1 AND p.payment_status = 0 
{$whereProperty}{$whereProperty2}{$whereProperty3} ORDER BY p.payment_id ASC";

echo $sql;
die();
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

//</editor-fold>

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
/*
if ($stockpileName != "") {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Stockpile = {$stockpileName}");
}
*/
$rowActive++;
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:F{$rowActive}")->applyFromArray($styleArray9);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:F{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Rincian Pembayaran");

$rowActive++;
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:F{$rowActive}")->applyFromArray($styleArray9);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:B{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Nama Perusahaan");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", ":");
$objPHPExcel->getActiveSheet()->getStyle("C{$rowActive}")->getAlignment()->applyFromArray(
	array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
);
$objPHPExcel->getActiveSheet()->mergeCells("D{$rowActive}:F{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "PT. Jatim Propertindo Jaya");

$rowActive++;
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:F{$rowActive}")->applyFromArray($styleArray9);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:B{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Batch Upload");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", ":");
$objPHPExcel->getActiveSheet()->getStyle("C{$rowActive}")->getAlignment()->applyFromArray(
	array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
);
$objPHPExcel->getActiveSheet()->mergeCells("D{$rowActive}:F{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "{$batchUpload}");

if ($periodFull != "") {
	$rowActive++;
	$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:F{$rowActive}")->applyFromArray($styleArray9);
	$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
	$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:B{$rowActive}");
	$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Tanggal Pembayaran");
	$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", ":");
	$objPHPExcel->getActiveSheet()->getStyle("C{$rowActive}")->getAlignment()->applyFromArray(
		array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
	);
	$objPHPExcel->getActiveSheet()->mergeCells("D{$rowActive}:F{$rowActive}");
	$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "{$periodFull}");
}

if ($paymentTypes != "") {
	$rowActive++;
	$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:F{$rowActive}")->applyFromArray($styleArray9);
	$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
	$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:B{$rowActive}");
	$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Metode Pembayaran");
	$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", ":");
	$objPHPExcel->getActiveSheet()->getStyle("C{$rowActive}")->getAlignment()->applyFromArray(
		array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
	);
	$objPHPExcel->getActiveSheet()->mergeCells("D{$rowActive}:F{$rowActive}");
	$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "{$paymentTypes}");
}

if ($bankIds != '') {
	$sqlBank = "SELECT b.bank_account_no FROM bank b WHERE b.bank_id IN ({$bankIds})";
	$resultBank = $myDatabase->query($sqlBank, MYSQLI_STORE_RESULT);
	$bankAccountNo = '';
	if ($resultBank->num_rows > 0) {
		while ($rowData = mysqli_fetch_array($resultBank)) {
			if ($bankAccountNo == '') {
				$bankAccountNo .= "" . $rowData['bank_account_no'] . "";
			} else {
				$bankAccountNo .= ', ' . "" . $rowData['bank_account_no'] . "";
			}
		}
	}

	$rowActive++;
	$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:F{$rowActive}")->applyFromArray($styleArray9);
	$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
	$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:B{$rowActive}");
	$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Sumber Dana");
	$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", ":");
	$objPHPExcel->getActiveSheet()->getStyle("C{$rowActive}")->getAlignment()->applyFromArray(
		array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
	);
	$objPHPExcel->getActiveSheet()->mergeCells("D{$rowActive}:F{$rowActive}");
	$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "{$bankAccountNo}");
	$objPHPExcel->getActiveSheet()
		->getStyle("D{$rowActive}")
		->getNumberFormat()
		->setFormatCode(
			'#'
		);
}

/*
$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Approval Sheets");
*/
$rowActive++;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "No.");

$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Payment No");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Payment Type");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Approval");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "No Rek/Cek");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Vendor");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Keterangan");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Bank");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "Cabang Bank");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "Nama Akun Bank");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "Qty (MT/KG/HR/DW)");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "Harga");
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "DPP");
$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", "PPN");
$objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", "PPh");
$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", "Total Rincian");
$objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", "DP");
$objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", "Type");
$objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", "Grand Total");
$objPHPExcel->getActiveSheet()->setCellValue("T{$rowActive}", "Stockpile");
$objPHPExcel->getActiveSheet()->setCellValue("U{$rowActive}", "PIC Finance");
$objPHPExcel->getActiveSheet()->setCellValue("V{$rowActive}", "PPN");
$objPHPExcel->getActiveSheet()->setCellValue("W{$rowActive}", "PPh");

// $objPHPExcel->getActiveSheet()->setCellValue("W{$rowActive}", "Documents");
// $objPHPExcel->getActiveSheet()->setCellValue("X{$rowActive}", "Note");
// $objPHPExcel->getActiveSheet()->setCellValue("Y{$rowActive}", "Email");
// $objPHPExcel->getActiveSheet()->setCellValue("Z{$rowActive}", "Entry Date");
// $objPHPExcel->getActiveSheet()->setCellValue("AA{$rowActive}", "Category");
// $objPHPExcel->getActiveSheet()->setCellValue("AB{$rowActive}", "Batch Code");


$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('F5CAAD');
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray4);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(50);
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Body">

if ($result->num_rows > 0) {
	//echo 'test';
	$no = 1;
	while ($row = mysqli_fetch_array($result)) {

		if ($row['payment_no'] != '') {
			$voucherCode = $row['payment_location'] . '/' . $row['bank_code'] . '/' . $row['pcur_currency_code'];

			if ($row['bank_type'] == 1) {
				$voucherCode .= ' - B';
			} elseif ($row['bank_type'] == 2) {
				$voucherCode .= ' - P';
			} elseif ($row['bank_type'] == 3) {
				$voucherCode .= ' - CAS';
			}

			if ($row['bank_type'] != 3) {
				if ($row['payment_type'] == 1) {
					$voucherCode .= 'RV';
				} else {
					$voucherCode .= 'PV';
				}
			}
		}

		if ($row['payment_type'] == 1) {

			$grand_total = $row['grand_total'] * -1;
		} else {
			$grand_total = $row['grand_total'];
		}

		$grandTotal = ((($row['dpp'] + $row['ppn_amount']) - $row['pph_amount']) - $row['dp']);

		$rowActive++;
		/*
    if($row['payment_id'] == $lastPaymentId) {
                    $counter++;
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "");
	$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "");
	$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "");
	$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "");
	$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "");
	}else{
					
					$sqlCount = "SELECT COUNT(1) AS total_row
					FROM payment p
					LEFT JOIN stockpile_contract sc ON sc.stockpile_contract_id = p.`stockpile_contract_id`
					LEFT JOIN contract c ON c.`contract_id` = sc.`contract_id`
					LEFT JOIN vendor cv ON cv.`vendor_id` = c.`vendor_id`
					LEFT JOIN invoice_detail id ON id.`invoice_id` = p.`invoice_id`
					LEFT JOIN general_vendor igv ON igv.`general_vendor_id` = id.`general_vendor_id`
					LEFT JOIN vendor v ON v.`vendor_id` = p.`vendor_id`
					LEFT JOIN freight f ON f.`freight_id` = p.`freight_id`
					LEFT JOIN labor l ON l.`labor_id` = p.`labor_id`
					LEFT JOIN vendor_handling vh ON vh.`vendor_handling_id` = p.`vendor_handling_id`
					LEFT JOIN sales sl ON sl.`sales_id` = p.`sales_id`
					LEFT JOIN customer cust ON cust.customer_id = sl.customer_id
					LEFT JOIN general_vendor gv ON gv.`general_vendor_id` = p.`general_vendor_id`
					LEFT JOIN payment_cash pc ON pc.payment_id = p.payment_id
					LEFT JOIN general_vendor pcgv ON pcgv.general_vendor_id = pc.general_vendor_id
					WHERE 1=1 AND p.`payment_id` = '{$row['payment_id']}' ORDER BY p.payment_id DESC";
										$resultCount = $myDatabase->query($sqlCount, MYSQLI_STORE_RESULT);
                    $rowCount = $resultCount->fetch_object();
                    $totalRow = $rowCount->total_row;
					$totalRow2 =  $totalRow - 1;
                    $counter = 1;
					$no++;
					if($totalRow > 1){
					$rowMerge = $rowActive + $totalRow2;
					}else{
					$rowMerge = $rowActive;	
					}*/

		//$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:A{$rowMerge}");
		$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $no);

		$objPHPExcel->getActiveSheet()->setCellValueExplicit("B{$rowActive}", $voucherCode . ' # ' . $row['payment_no'], PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $row['p_type']);
		$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "");
		$objPHPExcel->getActiveSheet()->setCellValueExplicit("E{$rowActive}", $row['no_rek'], PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $row['vendor_name']);

		//}
		$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $row['keterangan']);
		/*if($row['payment_id'] == $lastPaymentId) {
                    $counter++;
	$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "");
	$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "");
	$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "");
	}else{
		$counter = 1;*/
		$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $row['bank_name']);
		$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $row['branch']);
		$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $row['beneficiary']);
		//}
		$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $row['quantity']);
		$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $row['price_converted']);
		$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $row['dpp']);
		$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", $row['ppn_amount']);
		$objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", $row['pph_amount']);
		$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", $row['total']);
		/*if($row['payment_id'] == $lastPaymentId) {
                    $counter++;
	$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", "");
	$objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", "");
	$objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", "");
	$objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", "");
	$objPHPExcel->getActiveSheet()->setCellValue("T{$rowActive}", "");
	$objPHPExcel->getActiveSheet()->setCellValue("U{$rowActive}", "");
	$objPHPExcel->getActiveSheet()->setCellValue("V{$rowActive}", "");
	$objPHPExcel->getActiveSheet()->setCellValue("W{$rowActive}", "");
	$objPHPExcel->getActiveSheet()->setCellValue("X{$rowActive}", "");
	}else{
		$counter = 1;*/
		$objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", $row['dp']);
		$objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", $row['paymentType']);
		$objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", $grandTotal);
		$objPHPExcel->getActiveSheet()->setCellValue("T{$rowActive}", $row['stockpile_name']);
		$objPHPExcel->getActiveSheet()->setCellValue("U{$rowActive}", $row['user_name']);
		$objPHPExcel->getActiveSheet()->setCellValue("V{$rowActive}", $row['ppn']);
		$objPHPExcel->getActiveSheet()->setCellValue("W{$rowActive}", $row['pph']);

		// $objPHPExcel->getActiveSheet()->setCellValue("W{$rowActive}", "");
		// $objPHPExcel->getActiveSheet()->setCellValue("X{$rowActive}", "");
		// $objPHPExcel->getActiveSheet()->setCellValue("Y{$rowActive}", "");
		// $objPHPExcel->getActiveSheet()->setCellValue("Z{$rowActive}", $row['entry_date']);
		// $objPHPExcel->getActiveSheet()->setCellValue("AA{$rowActive}", $row['kode3']);
		// $objPHPExcel->getActiveSheet()->setCellValue("AB{$rowActive}", $row['batch_code']);
		//}
		$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}")->applyFromArray($styleArray7);
		$objPHPExcel->getActiveSheet()->getStyle("C{$rowActive}")->applyFromArray($styleArray7);
		$objPHPExcel->getActiveSheet()->getStyle("E{$rowActive}:F{$rowActive}")->applyFromArray($styleArray7);
		$objPHPExcel->getActiveSheet()->getStyle("H{$rowActive}:I{$rowActive}")->applyFromArray($styleArray7);
		$objPHPExcel->getActiveSheet()->getStyle("K{$rowActive}:W{$rowActive}")->applyFromArray($styleArray7);
		// $objPHPExcel->getActiveSheet()->getStyle("Z{$rowActive}:AB{$rowActive}")->applyFromArray($styleArray7);
		$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(100);
		$objPHPExcel->getActiveSheet()->getStyle("S{$rowActive}")->getFont()->setBold(true);
		//$lastPaymentId = $row['payment_id'];
		$AllgrandTotal = $AllgrandTotal + $grand_total;
		$no++;
	}
}
//$rowActive = $rowMerge;
$bodyRowEnd = $rowActive;

if ($bodyRowEnd > $headerRow + 1) {
	$rowActive++;

	$objPHPExcel->getActiveSheet()->mergeCells("P{$rowActive}:R{$rowActive}");
	$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", "TOTAL");
	$objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", $AllgrandTotal);
	// $objPHPExcel->getActiveSheet()->mergeCells("S{$rowActive}:Z{$rowActive}");
	// $objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", "");

	// Set number format for Amount 
	$objPHPExcel->getActiveSheet()->getStyle("S{$rowActive}:S{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	// Set border for table
	$objPHPExcel->getActiveSheet()->getStyle("P{$rowActive}:S{$rowActive}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	// Set Background Color
	$objPHPExcel->getActiveSheet()->getStyle("P{$rowActive}:S{$rowActive}")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
	// Set row TOTAL to bold
	$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->getFont()->setBold(true);

	// $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray8);
	$objPHPExcel->getActiveSheet()->getStyle("P{$rowActive}:S{$rowActive}")->applyFromArray($styleArray7);
	$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(40);
}
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Formating Excel">
// Set column width
for ($temp = ord("A"); $temp <= ord("W"); $temp++) {
	$objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(false);
	$objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setWidth(20);
	//$objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
}
$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setAutoSize(false);
$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(5);
$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setAutoSize(false);
$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setAutoSize(false);
$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension("G")->setAutoSize(false);
$objPHPExcel->getActiveSheet()->getColumnDimension("G")->setWidth(40);

// Set format date in cell
if ($bodyRowEnd > $headerRow) {
	//$objPHPExcel->getActiveSheet()->getColumnDimension("A" . ($headerRow + 1) . ":X{$bodyRowEnd}")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getStyle("E" . ($headerRow + 1) . ":E{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
	//$objPHPExcel->getActiveSheet()->getStyle("G" . ($headerRow + 1) . ":G{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
	//$objPHPExcel->getActiveSheet()->getStyle("Z" . ($headerRow + 1) . ":Z{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
}

//$objPHPExcel->getActiveSheet()->getStyle("E" . ($headerRow + 1) . ":E{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

// Set number format for Amount 
$objPHPExcel->getActiveSheet()->getStyle("K" . ($headerRow + 1) . ":S{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle("T" . ($headerRow + 1) . ":T{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
//$objPHPExcel->getActiveSheet()->getStyle("W" . ($headerRow + 1) . ":Y{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
//$objPHPExcel->getActiveSheet()->getStyle("AA" . ($headerRow + 1) . ":AB{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
//$objPHPExcel->getActiveSheet()->getStyle("AF" . ($headerRow + 1) . ":AH{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

// Set border for table
$objPHPExcel->getActiveSheet()->getStyle("A" . ($headerRow) . ":{$lastColumn}{$bodyRowEnd}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Save Excel and return to browser">
ob_end_clean();
header('Content-Type: application/vnd.ms-excel');
$format = '';
$writerFormat = '';
if ($fileFormat == "Excel") {
	$format = ".xls";
	$writerFormat = "Excel5";
} else if ($fileFormat == "CSV") {
	$format = ".csv";
	$writerFormat = "CSV";
}
$fileName = "ApprovalSheets " . $periodFull . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . $format;
header('Content-Disposition: attachment;filename="' . $fileName . '"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $writerFormat);
$objWriter->save('php://output');
// </editor-fold>
exit();
