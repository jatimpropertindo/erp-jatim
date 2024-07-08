<?php
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

require_once PATH_EXTENSION . DS . 'DomPDF/autoload.inc.php';

$whereProperty = '';
$whereProperty2 = '';
$periodFrom = $myDatabase->real_escape_string($_POST['periodFrom']);
$periodTo = $myDatabase->real_escape_string($_POST['periodTo']);
$pTypes = isset($_POST['pTypes']) ? $_POST['pTypes'] : '';
$pLocations = isset($_POST['pLocations']) ? $_POST['pLocations'] : '';
$paymentNos = isset($_POST['paymentNos']) ? $_POST['paymentNos'] : '';
$bankIds = isset($_POST['bankIds']) ? $_POST['bankIds'] : '';
$paymentIds = $_POST['paymentIds'];
$batchUpload = $_POST['batchUpload'];
$paymentTypes = $_POST['paymentTypes'];
$fileFormat = $_POST['fileFormat'];
$periodFull = '';

$sql = "SELECT * FROM user_module WHERE user_id = {$_SESSION['userId']} AND module_id = 27";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
if ($result->num_rows > 0) {
	while ($row = $result->fetch_object()) {
		if ($row->module_id == 27) {
			$whereProperty = "";
			if ($pLocations != '') {
				$whereProperty2 .= "AND (CASE WHEN p.payment_location = 0 THEN 'HO' ELSE 'Stockpile' END) IN ({$pLocations})";
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

if ($paymentIds != '') {
	$whereProperty .= "AND p.payment_id IN ({$paymentIds})";
}


if ($periodFrom != '' && $periodTo != '') {
	$whereProperty .= " AND DATE_FORMAT(p.payment_date, '%Y-%m-%d') BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
	$periodFull = $periodFrom . " - " . $periodTo . " ";
} else if ($periodFrom != '' && $periodTo == '') {
	$whereProperty .= " AND DATE_FORMAT(p.payment_date, '%Y-%m-%d') >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";
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
	WHEN p.labor_id IS NOT NULL THEN p.amount_converted
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
-- AND b.bank_type = 2
{$whereProperty}{$whereProperty2} ORDER BY p.payment_id ASC";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
ob_start();
?>
<html>


<body>
	<style type="text/css">
		body {
			font-family: "Arial, sans-serif";
			font-size: 8pt;
			color: #313131;
		}

		#aprroval {
			border-collapse: collapse;
			width: 100%;
			table-layout: fixed;
			overflow-wrap: break-word;
			word-wrap: break-word;
		}

		#aprroval td,
		#aprroval th {
			border: 1px solid #313131;
			padding: 2px;
		}

		#aprroval th {
			padding-top: 12px;
			padding-bottom: 12px;
			text-align: center;
			background-color: #F5CAAD;
		}
	</style>

	<p style="text-align:right;">Print Date: <?= date("d F Y") ?></p>
	<table style="font-weight:bold;">
		<tr>
			<td>Rincian Pembayaran</td>
		</tr>
		<tr>
			<td>Nama Perusahaan</td>
			<td>:</td>
			<td>PT. Jatim Propertindo Jaya</td>
		</tr>
		<tr>
			<td>Batch Upload</td>
			<td>:</td>
			<td><?= $batchUpload ?></td>
		</tr>
		<tr>
			<td>Tanggal Pembayaran</td>
			<td>:</td>
			<td><?= $periodFull ?></td>
		</tr>
		<?php if ($paymentTypes != "") { ?>
			<tr>
				<td>Metode Pembayaran</td>
				<td>:</td>
				<td><?= $paymentTypes ?></td>
			</tr>
		<?php } ?>
		<?php if ($bankIds != "") {
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

			?>
			<tr>
				<td>Sumber Dana</td>
				<td>:</td>
				<td><?= $bankAccountNo ?></td>
			</tr>
		<?php } ?>
	</table>
	<br />
	<table id="aprroval">
		<thead>
			<tr>
				<th style="width: 2%;">No.</th>
				<th style="width: 3.5%;">Payment No</th>
				<th style="width: 3.5%;">Payment Type</th>
				<th style="width: 3.5%;">Approval</th>
				<th style="width: 3.5%;">No Rek/Cek</th>
				<th>Vendor</th>
				<th style="width: 8%;">Keterangan</th>
				<th>Bank</th>
				<th style="width: 3.5%;">Cabang Bank</th>
				<th>Nama Akun Bank</th>
				<th>Qty (MT/KG/HR/DW)</th>
				<th>Harga</th>
				<th>DPP</th>
				<th>PPN</th>
				<th>PPh</th>
				<th>Total Rincian</th>
				<th style="width: 3.5%;">DP</th>
				<th style="width: 2.5%;">Type</th>
				<th style="width: 6.5%;">Grand Total</th>
				<th>Stockpile</th>
				<th style="width: 3.5%;">PIC Finance</th>
				<th style="width: 3%;">PPN</th>
				<th style="width: 3%;">PPh</th>
			</tr>
		</thead>
		<?php
		if($result->num_rows > 0) {
		//echo 'test';
		$no = 1;
		$AllgrandTotal = 0;
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
			?>



			<tr>
				<td style="text-align: center;"><?= $no ?></td>
				<td style="text-align: center;"><?= $voucherCode . " # " . $row["payment_no"] ?></td>
				<td style="text-align: center;"><?= $row["p_type"] ?></td>
				<td></td>
				<td style="text-align: center;"><?= $row["no_rek"] ?></td>
				<td style="text-align: center;"><?= $row["vendor_name"] ?></td>
				<td><?= $row["keterangan"] ?></td>
				<td style="text-align: center;"><?= $row["bank_name"] ?></td>
				<td style="text-align: center;"><?= $row["branch"] ?></td>
				<td style="text-align: center;"><?= $row["beneficiary"] ?></td>
				<td style="text-align: center;"><?= number_format($row["quantity"], 2, ".", ",")  ?></td>
				<td style="text-align: center;"><?= number_format($row["price_converted"], 2, ".", ",")  ?></td>
				<td style="text-align: center;"><?= number_format($row["dpp"], 2, ".", ",")  ?></td>
				<td style="text-align: center;"><?= number_format($row["ppn_amount"], 2, ".", ",")  ?></td>
				<td style="text-align: center;"><?= number_format($row["pph_amount"], 2, ".", ",")  ?></td>
				<td style="text-align: center;"><?= number_format($row["total"], 2, ".", ",")  ?></td>
				<td style="text-align: center;"><?= number_format($row["dp"], 2, ".", ",")  ?></td>
				<td style="text-align: center;"><?= $row["paymentType"] ?></td>
				<td style="text-align: center; font-weight: bold;"><?= number_format($grandTotal, 2, ".", ",")  ?></td>
				<td style="text-align: center;"><?= $row["stockpile_name"] ?></td>
				<td style="text-align: center;"><?= $row["user_name"] ?></td>
				<td style="text-align: center;"><?= $row["ppn"] ?></td>
				<td style="text-align: center;"><?= $row["pph"] ?></td>
			</tr>



		<?php
			$AllgrandTotal = $AllgrandTotal + $grand_total;
			$no++;
		}
		}
		?>
		<tr style="font-weight: bold;">
			<td colspan="15"></td>
			<td colspan="3" style="text-align: center; background-color: #FFFF00;">TOTAL</td>
			<td style="text-align: center; background-color: #FFFF00;"><?= number_format($AllgrandTotal, 2, ".", ",")  ?></td>
			<td colspan="4"></td>
		</tr>
	</table>
	<?php
	$html = ob_get_contents();
	ob_get_clean();
	$dompdf = new Dompdf\Dompdf();
	$dompdf->loadHtml($html);
	$dompdf->setPaper('a3', 'landscape');
	$dompdf->render();
	$dompdf->stream("ApprovalSheets " . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".pdf", array("Attachment" => 1));
	?>
</body>

</html>