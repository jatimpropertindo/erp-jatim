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

// -------- menampilkan data --------- //

$whereProperty = '';
$periodFrom = $myDatabase->real_escape_string($_POST['periodFrom']);
$periodTo = $myDatabase->real_escape_string($_POST['periodTo']);
$paymentIds = $myDatabase->real_escape_string($_POST['paymentIds']);

if ($paymentIds != '') {
	$whereProperty .= " AND p.payment_id IN ({$paymentIds})";
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

$sql = "SELECT 
p.payment_id, 
CONCAT(
  CASE WHEN p.payment_location = 0 THEN 'HOF' ELSE ps.stockpile_name END, 
  b.bank_code, '#', p.payment_no
) AS payment_no, 
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN 'PKS' 
WHEN p.invoice_id IS NOT NULL THEN 'INV' 
WHEN p.payment_cash_id IS NOT NULL THEN 'PC' 
WHEN p.freight_id IS NOT NULL THEN 'OA' 
WHEN (p.vendor_handling_id IS NOT NULL AND p.vendor_handling_id != 0) THEN 'HND' 
WHEN p.labor_id IS NOT NULL THEN 'OB' 
WHEN p.vendor_id IS NOT NULL THEN 'PKS' 
WHEN p.sales_id IS NOT NULL THEN 'SLS' 
WHEN p.general_vendor_id IS NOT NULL THEN '' ELSE '' END AS kode3,
p.payment_date, 
DATE_FORMAT(p.entry_date, '%Y-%m-%d') AS entry_date, 
p.payment_type, 
CASE WHEN p.payment_location = 0 THEN 'HOF' ELSE ps.stockpile_name END AS payment_location, 
CASE WHEN p.payment_location = 0 THEN 'HO' ELSE 'Stockpile' END AS payment_location2, 
CASE WHEN p.payment_type = 1 THEN 'IN' ELSE 'OUT' END AS paymentType, 
b.bank_code, 
b.bank_type, 
pcur.currency_code AS pcur_currency_code, 
p.payment_type2, 
CASE WHEN p.payment_type2 = 1 THEN 'TT' WHEN p.payment_type2 = 2 THEN 'Cek/Giro' WHEN p.payment_type2 = 3 THEN 'Tunai' WHEN p.payment_type2 = 4 THEN 'Bill Payment' WHEN p.payment_type2 = 5 THEN 'Auto Debet' ELSE 'TT' END AS p_type, 
CASE WHEN p.stockpile_contract_id IS NOT NULL 
AND p.payment_date > '2019-10-12' THEN (
  SELECT 
	TRIM(
	  REPLACE(
		REPLACE(
		  REPLACE(account_no, '-', ''), 
		  '.', 
		  ''
		), 
		' ', 
		''
	  )
	) 
  FROM 
	vendor_bank 
  WHERE 
	v_bank_id = p.vendor_bank_id
) WHEN p.vendor_id IS NOT NULL 
AND p.payment_date > '2019-10-12' THEN (
  SELECT 
	TRIM(
	  REPLACE(
		REPLACE(
		  REPLACE(account_no, '-', ''), 
		  '.', 
		  ''
		), 
		' ', 
		''
	  )
	) 
  FROM 
	vendor_bank 
  WHERE 
	v_bank_id = p.vendor_bank_id
) WHEN p.invoice_id IS NOT NULL 
AND p.payment_date > '2019-10-12' THEN (
  SELECT 
	TRIM(
	  REPLACE(
		REPLACE(
		  REPLACE(account_no, '-', ''), 
		  '.', 
		  ''
		), 
		' ', 
		''
	  )
	) 
  FROM 
	general_vendor_bank 
  WHERE 
	gv_bank_id = p.vendor_bank_id
) WHEN p.payment_cash_id IS NOT NULL 
AND p.payment_date > '2019-10-12' THEN (
  SELECT 
	TRIM(
	  REPLACE(
		REPLACE(
		  REPLACE(account_no, '-', ''), 
		  '.', 
		  ''
		), 
		' ', 
		''
	  )
	) 
  FROM 
	general_vendor_bank 
  WHERE 
	gv_bank_id = p.vendor_bank_id
) WHEN p.freight_id IS NOT NULL 
AND p.payment_date > '2019-10-12' THEN (
  SELECT 
	TRIM(
	  REPLACE(
		REPLACE(
		  REPLACE(account_no, '-', ''), 
		  '.', 
		  ''
		), 
		' ', 
		''
	  )
	) 
  FROM 
	freight_bank 
  WHERE 
	f_bank_id = p.vendor_bank_id
) WHEN p.vendor_handling_id IS NOT NULL 
AND p.payment_date > '2019-10-12' THEN (
  SELECT 
	TRIM(
	  REPLACE(
		REPLACE(
		  REPLACE(account_no, '-', ''), 
		  '.', 
		  ''
		), 
		' ', 
		''
	  )
	) 
  FROM 
	vendor_handling_bank 
  WHERE 
	vh_bank_id = p.vendor_bank_id
) WHEN p.labor_id IS NOT NULL 
AND p.payment_date > '2019-10-12' THEN (
  SELECT 
	TRIM(
	  REPLACE(
		REPLACE(
		  REPLACE(account_no, '-', ''), 
		  '.', 
		  ''
		), 
		' ', 
		''
	  )
	) 
  FROM 
	labor_bank 
  WHERE 
	l_bank_id = p.vendor_bank_id
) WHEN p.vendor_id IS NOT NULL 
AND p.payment_date > '2019-10-12' THEN (
  SELECT 
	TRIM(
	  REPLACE(
		REPLACE(
		  REPLACE(account_no, '-', ''), 
		  '.', 
		  ''
		), 
		' ', 
		''
	  )
	) 
  FROM 
	vendor_bank 
  WHERE 
	v_bank_id = p.vendor_bank_id
) WHEN p.general_vendor_id IS NOT NULL THEN TRIM(
  REPLACE(
	REPLACE(
	  REPLACE(gv.account_no, '-', ''), 
	  '.', 
	  ''
	), 
	' ', 
	''
  )
) WHEN p.stockpile_contract_id IS NOT NULL THEN cv.account_no WHEN p.invoice_id IS NOT NULL THEN (
  SELECT 
	TRIM(
	  REPLACE(
		REPLACE(
		  REPLACE(gv.account_no, '-', ''), 
		  '.', 
		  ''
		), 
		' ', 
		''
	  )
	) 
  FROM 
	general_vendor gv 
	LEFT JOIN invoice_detail id ON gv.general_vendor_id = id.general_vendor_id 
  WHERE 
	id.invoice_id = p.invoice_id 
  LIMIT 
	1
) WHEN p.payment_cash_id IS NOT NULL THEN (
  SELECT 
	TRIM(
	  REPLACE(
		REPLACE(
		  REPLACE(gv.account_no, '-', ''), 
		  '.', 
		  ''
		), 
		' ', 
		''
	  )
	) 
  FROM 
	general_vendor gv 
	LEFT JOIN payment_cash pc ON gv.general_vendor_id = pc.general_vendor_id 
  WHERE 
	pc.payment_id = p.payment_id 
  LIMIT 
	1
) WHEN p.vendor_id IS NOT NULL THEN TRIM(
  REPLACE(
	REPLACE(
	  REPLACE(v.account_no, '-', ''), 
	  '.', 
	  ''
	), 
	' ', 
	''
  )
) WHEN p.sales_id IS NOT NULL THEN TRIM(
  REPLACE(
	REPLACE(
	  REPLACE(cust.account_no, '-', ''), 
	  '.', 
	  ''
	), 
	' ', 
	''
  )
) WHEN p.freight_id IS NOT NULL THEN TRIM(
  REPLACE(
	REPLACE(
	  REPLACE(f.account_no, '-', ''), 
	  '.', 
	  ''
	), 
	' ', 
	''
  )
) WHEN p.vendor_handling_id IS NOT NULL THEN TRIM(
  REPLACE(
	REPLACE(
	  REPLACE(vh.account_no, '-', ''), 
	  '.', 
	  ''
	), 
	' ', 
	''
  )
) WHEN p.labor_id IS NOT NULL THEN TRIM(
  REPLACE(
	REPLACE(
	  REPLACE(l.account_no, '-', ''), 
	  '.', 
	  ''
	), 
	' ', 
	''
  )
) WHEN p.invoice_sales_oa_id IS NOT NULL THEN TRIM(
  REPLACE(
	REPLACE(
	  REPLACE(flsb.account_no, '-', ''), 
	  '.', 
	  ''
	), 
	' ', 
	''
  )
) ELSE (
  SELECT 
	TRIM(
	  REPLACE(
		REPLACE(
		  REPLACE(no_rek, '-', ''), 
		  '.', 
		  ''
		), 
		' ', 
		''
	  )
	) 
  FROM 
	vendor_pettycash 
  WHERE 
	account_no = a.account_no
) END AS no_rek, 
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN cv.vendor_name WHEN p.invoice_id IS NOT NULL THEN (
  SELECT 
	gv.general_vendor_name 
  FROM 
	general_vendor gv 
	LEFT JOIN invoice_detail id ON gv.general_vendor_id = id.general_vendor_id 
  WHERE 
	id.invoice_id = p.invoice_id 
  LIMIT 
	1
) WHEN p.payment_cash_id IS NOT NULL THEN (
  SELECT 
	gv.general_vendor_name 
  FROM 
	general_vendor gv 
	LEFT JOIN payment_cash pc ON gv.general_vendor_id = pc.general_vendor_id 
  WHERE 
	pc.payment_id = p.payment_id 
  LIMIT 
	1
) WHEN p.vendor_id IS NOT NULL THEN v.vendor_name WHEN p.sales_id IS NOT NULL THEN cust.customer_name WHEN p.freight_id IS NOT NULL THEN f.freight_supplier WHEN (
  p.vendor_handling_id IS NOT NULL 
  AND p.vendor_handling_id != 0
) THEN vh.vendor_handling_name WHEN p.labor_id IS NOT NULL THEN l.labor_name WHEN p.general_vendor_id IS NOT NULL THEN gv.general_vendor_name WHEN p.invoice_sales_oa_id IS NOT NULL THEN fls.freight_supplier ELSE (
  SELECT 
	vendor_name 
  FROM 
	vendor_pettycash 
  WHERE 
	account_no = a.account_no
) END AS vendor_name, 
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN p.remarks WHEN p.invoice_id IS NOT NULL THEN i.remarks WHEN p.payment_cash_id IS NOT NULL THEN p.remarks WHEN p.vendor_id IS NOT NULL THEN p.remarks WHEN p.sales_id IS NOT NULL THEN p.remarks WHEN p.freight_id IS NOT NULL THEN p.remarks WHEN p.vendor_handling_id IS NOT NULL THEN p.remarks WHEN p.labor_id IS NOT NULL THEN p.remarks WHEN p.general_vendor_id IS NOT NULL THEN p.remarks ELSE p.remarks END AS keterangan, 
CASE WHEN p.stockpile_contract_id IS NOT NULL 
AND p.payment_date > '2019-10-12' THEN (
  SELECT 
	bank_name 
  FROM 
	vendor_bank 
  WHERE 
	v_bank_id = p.vendor_bank_id
) WHEN p.vendor_id IS NOT NULL 
AND p.payment_date > '2019-10-12' THEN (
  SELECT 
	bank_name 
  FROM 
	vendor_bank 
  WHERE 
	v_bank_id = p.vendor_bank_id
) WHEN p.invoice_id IS NOT NULL 
AND p.payment_date > '2019-10-12' THEN (
  SELECT 
	bank_name 
  FROM 
	general_vendor_bank 
  WHERE 
	gv_bank_id = p.vendor_bank_id
) WHEN p.payment_cash_id IS NOT NULL 
AND p.payment_date > '2019-10-12' THEN (
  SELECT 
	bank_name 
  FROM 
	general_vendor_bank 
  WHERE 
	gv_bank_id = p.vendor_bank_id
) WHEN p.freight_id IS NOT NULL 
AND p.payment_date > '2019-10-12' THEN (
  SELECT 
	bank_name 
  FROM 
	freight_bank 
  WHERE 
	f_bank_id = p.vendor_bank_id
) WHEN p.vendor_handling_id IS NOT NULL 
AND p.payment_date > '2019-10-12' THEN (
  SELECT 
	bank_name 
  FROM 
	vendor_handling_bank 
  WHERE 
	vh_bank_id = p.vendor_bank_id
) WHEN p.labor_id IS NOT NULL 
AND p.payment_date > '2019-10-12' THEN (
  SELECT 
	bank_name 
  FROM 
	labor_bank 
  WHERE 
	l_bank_id = p.vendor_bank_id
) WHEN p.stockpile_contract_id IS NOT NULL THEN cv.bank_name WHEN p.invoice_id IS NOT NULL THEN (
  SELECT 
	gv.bank_name 
  FROM 
	general_vendor gv 
	LEFT JOIN invoice_detail id ON gv.general_vendor_id = id.general_vendor_id 
  WHERE 
	id.invoice_id = p.invoice_id 
  LIMIT 
	1
) WHEN p.payment_cash_id IS NOT NULL THEN (
  SELECT 
	gv.bank_name 
  FROM 
	general_vendor gv 
	LEFT JOIN payment_cash pc ON gv.general_vendor_id = pc.general_vendor_id 
  WHERE 
	pc.payment_id = p.payment_id 
  LIMIT 
	1
) WHEN p.vendor_id IS NOT NULL THEN v.bank_name WHEN p.sales_id IS NOT NULL THEN cust.bank_name WHEN p.freight_id IS NOT NULL THEN f.bank_name WHEN p.vendor_handling_id IS NOT NULL THEN vh.bank_name WHEN p.labor_id IS NOT NULL THEN l.bank_name WHEN p.general_vendor_id IS NOT NULL THEN gv.bank_name ELSE (
  SELECT 
	bank 
  FROM 
	vendor_pettycash 
  WHERE 
	account_no = a.account_no
) END AS bank_name, 
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN (
  SELECT 
	kode1 
  FROM 
	master_bank mb 
	LEFT JOIN vendor_bank vb ON mb.master_bank_id = vb.master_bank_id 
  WHERE 
	vb.v_bank_id = p.vendor_bank_id
) WHEN p.invoice_id IS NOT NULL THEN (
  SELECT 
	kode1 
  FROM 
	master_bank mb 
	LEFT JOIN general_vendor_bank gvb ON mb.master_bank_id = gvb.master_bank_id 
  WHERE 
	gvb.gv_bank_id = p.vendor_bank_id
) WHEN p.payment_cash_id IS NOT NULL THEN (
  SELECT 
	kode1 
  FROM 
	master_bank mb 
	LEFT JOIN general_vendor_bank gvb ON mb.master_bank_id = gvb.master_bank_id 
  WHERE 
	gvb.gv_bank_id = p.vendor_bank_id
) WHEN p.freight_id IS NOT NULL THEN (
  SELECT 
	kode1 
  FROM 
	master_bank mb 
	LEFT JOIN freight_bank fb ON fb.master_bank_id = mb.master_bank_id 
  WHERE 
	fb.f_bank_id = p.vendor_bank_id
) WHEN p.vendor_handling_id IS NOT NULL THEN (
  SELECT 
	kode1 
  FROM 
	master_bank mb 
	LEFT JOIN vendor_handling_bank vhb ON vhb.master_bank_id = mb.master_bank_id 
  WHERE 
	vhb.vh_bank_id = p.vendor_bank_id
) WHEN p.labor_id IS NOT NULL THEN (
  SELECT 
	kode1 
  FROM 
	master_bank mb 
	LEFT JOIN labor_bank lb ON lb.master_bank_id = mb.master_bank_id 
  WHERE 
	lb.l_bank_id = p.vendor_bank_id
) WHEN p.vendor_id IS NOT NULL THEN (
  SELECT 
	kode1 
  FROM 
	master_bank mb 
	LEFT JOIN vendor_bank vb ON mb.master_bank_id = vb.master_bank_id 
  WHERE 
	vb.v_bank_id = p.vendor_bank_id
) WHEN p.sales_id IS NOT NULL THEN '' WHEN p.general_vendor_id IS NOT NULL THEN (
  SELECT 
	kode1 
  FROM 
	master_bank mb 
	LEFT JOIN general_vendor_bank gvb ON mb.master_bank_id = gvb.master_bank_id 
  WHERE 
	gvb.gv_bank_id = p.vendor_bank_id
) ELSE (
  SELECT 
	kode1 
  FROM 
	master_bank mb 
	LEFT JOIN vendor_pettycash vpc ON vpc.master_bank_id = mb.master_bank_id 
  WHERE 
	vpc.account_no = a.account_no
) END AS kode1, 
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN (
  SELECT 
	kode2 
  FROM 
	master_bank mb 
	LEFT JOIN vendor_bank vb ON mb.master_bank_id = vb.master_bank_id 
  WHERE 
	vb.v_bank_id = p.vendor_bank_id
) WHEN p.invoice_id IS NOT NULL THEN (
  SELECT 
	kode2 
  FROM 
	master_bank mb 
	LEFT JOIN general_vendor_bank gvb ON mb.master_bank_id = gvb.master_bank_id 
  WHERE 
	gvb.gv_bank_id = p.vendor_bank_id
) WHEN p.payment_cash_id IS NOT NULL THEN (
  SELECT 
	kode2 
  FROM 
	master_bank mb 
	LEFT JOIN general_vendor_bank gvb ON mb.master_bank_id = gvb.master_bank_id 
  WHERE 
	gvb.gv_bank_id = p.vendor_bank_id
) WHEN p.freight_id IS NOT NULL THEN (
  SELECT 
	kode2 
  FROM 
	master_bank mb 
	LEFT JOIN freight_bank fb ON fb.master_bank_id = mb.master_bank_id 
  WHERE 
	fb.f_bank_id = p.vendor_bank_id
) WHEN p.vendor_handling_id IS NOT NULL THEN (
  SELECT 
	kode2 
  FROM 
	master_bank mb 
	LEFT JOIN vendor_handling_bank vhb ON vhb.master_bank_id = mb.master_bank_id 
  WHERE 
	vhb.vh_bank_id = p.vendor_bank_id
) WHEN p.labor_id IS NOT NULL THEN (
  SELECT 
	kode2 
  FROM 
	master_bank mb 
	LEFT JOIN labor_bank lb ON lb.master_bank_id = mb.master_bank_id 
  WHERE 
	lb.l_bank_id = p.vendor_bank_id
) WHEN p.vendor_id IS NOT NULL THEN (
  SELECT 
	kode2 
  FROM 
	master_bank mb 
	LEFT JOIN vendor_bank vb ON mb.master_bank_id = vb.master_bank_id 
  WHERE 
	vb.v_bank_id = p.vendor_bank_id
) WHEN p.sales_id IS NOT NULL THEN '' WHEN p.general_vendor_id IS NOT NULL THEN (
  SELECT 
	kode2 
  FROM 
	master_bank mb 
	LEFT JOIN general_vendor_bank gvb ON mb.master_bank_id = gvb.master_bank_id 
  WHERE 
	gvb.gv_bank_id = p.vendor_bank_id
) ELSE (
  SELECT 
	kode2 
  FROM 
	master_bank mb 
	LEFT JOIN vendor_pettycash vpc ON vpc.master_bank_id = mb.master_bank_id 
  WHERE 
	vpc.account_no = a.account_no
) END AS kode2,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN 'PKS Kontrak' WHEN p.vendor_id IS NOT NULL THEN 'PKS Curah' WHEN p.sales_id IS NOT NULL THEN 'Sales' WHEN p.freight_id IS NOT NULL THEN 'Freight Cost' WHEN p.labor_id IS NOT NULL THEN 'Unloading Cost' WHEN p.invoice_id IS NOT NULL THEN 'Invoice' WHEN p.payment_cash_id IS NOT NULL THEN 'Petty Cash' WHEN p.vendor_handling_id IS NOT NULL THEN 'Handling Cost' WHEN p.shipment_id IS NOT NULL 
OR p.general_vendor_id IS NOT NULL THEN 'Loading/Umum/HO' ELSE 'Internal Transfer/Loading (IN)' END AS payment_for, 
CASE WHEN p.stockpile_contract_id IS NOT NULL 
AND p.payment_date > '2019-10-12' THEN (
  SELECT 
	branch 
  FROM 
	vendor_bank 
  WHERE 
	v_bank_id = p.vendor_bank_id
) WHEN p.vendor_id IS NOT NULL 
AND p.payment_date > '2019-10-12' THEN (
  SELECT 
	branch 
  FROM 
	vendor_bank 
  WHERE 
	v_bank_id = p.vendor_bank_id
) WHEN p.invoice_id IS NOT NULL 
AND p.payment_date > '2019-10-12' THEN (
  SELECT 
	branch 
  FROM 
	general_vendor_bank 
  WHERE 
	gv_bank_id = p.vendor_bank_id
) WHEN p.payment_cash_id IS NOT NULL 
AND p.payment_date > '2019-10-12' THEN (
  SELECT 
	branch 
  FROM 
	general_vendor_bank 
  WHERE 
	gv_bank_id = p.vendor_bank_id
) WHEN p.freight_id IS NOT NULL 
AND p.payment_date > '2019-10-12' THEN (
  SELECT 
	branch 
  FROM 
	freight_bank 
  WHERE 
	f_bank_id = p.vendor_bank_id
) WHEN p.vendor_handling_id IS NOT NULL 
AND p.payment_date > '2019-10-12' THEN (
  SELECT 
	branch 
  FROM 
	vendor_handling_bank 
  WHERE 
	vh_bank_id = p.vendor_bank_id
) WHEN p.labor_id IS NOT NULL 
AND p.payment_date > '2019-10-12' THEN (
  SELECT 
	branch 
  FROM 
	labor_bank 
  WHERE 
	l_bank_id = p.vendor_bank_id
) WHEN p.stockpile_contract_id IS NOT NULL THEN cv.branch WHEN p.invoice_id IS NOT NULL THEN (
  SELECT 
	gv.branch 
  FROM 
	general_vendor gv 
	LEFT JOIN invoice_detail id ON gv.general_vendor_id = id.general_vendor_id 
  WHERE 
	id.invoice_id = p.invoice_id 
  LIMIT 
	1
) WHEN p.payment_cash_id IS NOT NULL THEN (
  SELECT 
	gv.branch 
  FROM 
	general_vendor gv 
	LEFT JOIN payment_cash pc ON gv.general_vendor_id = pc.general_vendor_id 
  WHERE 
	pc.payment_id = p.payment_id 
  LIMIT 
	1
) WHEN p.vendor_id IS NOT NULL THEN v.branch WHEN p.sales_id IS NOT NULL THEN cust.branch WHEN p.freight_id IS NOT NULL THEN f.branch WHEN p.vendor_handling_id IS NOT NULL THEN vh.branch WHEN p.labor_id IS NOT NULL THEN l.branch WHEN p.general_vendor_id IS NOT NULL THEN gv.branch WHEN p.invoice_sales_oa_id IS NOT NULL THEN flsb.branch ELSE (
  SELECT 
	branch 
  FROM 
	vendor_pettycash 
  WHERE 
	account_no = a.account_no
) END AS branch, 
CASE WHEN p.stockpile_contract_id IS NOT NULL 
AND p.payment_date > '2019-10-12' THEN (
  SELECT 
	beneficiary 
  FROM 
	vendor_bank 
  WHERE 
	v_bank_id = p.vendor_bank_id
) WHEN p.vendor_id IS NOT NULL 
AND p.payment_date > '2019-10-12' THEN (
  SELECT 
	beneficiary 
  FROM 
	vendor_bank 
  WHERE 
	v_bank_id = p.vendor_bank_id
) WHEN p.invoice_id IS NOT NULL 
AND p.payment_date > '2019-10-12' THEN (
  SELECT 
	beneficiary 
  FROM 
	general_vendor_bank 
  WHERE 
	gv_bank_id = p.vendor_bank_id
) WHEN p.payment_cash_id IS NOT NULL 
AND p.payment_date > '2019-10-12' THEN (
  SELECT 
	beneficiary 
  FROM 
	general_vendor_bank 
  WHERE 
	gv_bank_id = p.vendor_bank_id
) WHEN p.freight_id IS NOT NULL 
AND p.payment_date > '2019-10-12' THEN (
  SELECT 
	beneficiary 
  FROM 
	freight_bank 
  WHERE 
	f_bank_id = p.vendor_bank_id
) WHEN p.vendor_handling_id IS NOT NULL 
AND p.payment_date > '2019-10-12' THEN (
  SELECT 
	beneficiary 
  FROM 
	vendor_handling_bank 
  WHERE 
	vh_bank_id = p.vendor_bank_id
) WHEN p.labor_id IS NOT NULL 
AND p.payment_date > '2019-10-12' THEN (
  SELECT 
	beneficiary 
  FROM 
	labor_bank 
  WHERE 
	l_bank_id = p.vendor_bank_id
) WHEN p.stockpile_contract_id IS NOT NULL THEN cv.beneficiary WHEN p.invoice_id IS NOT NULL THEN (
  SELECT 
	gv.beneficiary 
  FROM 
	general_vendor gv 
	LEFT JOIN invoice_detail id ON gv.general_vendor_id = id.general_vendor_id 
  WHERE 
	id.invoice_id = p.invoice_id 
  LIMIT 
	1
) WHEN p.payment_cash_id IS NOT NULL THEN (
  SELECT 
	gv.beneficiary 
  FROM 
	general_vendor gv 
	LEFT JOIN payment_cash pc ON gv.general_vendor_id = pc.general_vendor_id 
  WHERE 
	pc.payment_id = p.payment_id 
  LIMIT 
	1
) WHEN p.vendor_id IS NOT NULL THEN v.beneficiary WHEN p.sales_id IS NOT NULL THEN cust.beneficiary WHEN p.freight_id IS NOT NULL THEN f.beneficiary WHEN p.vendor_handling_id IS NOT NULL THEN vh.beneficiary WHEN p.labor_id IS NOT NULL THEN l.beneficiary WHEN p.general_vendor_id IS NOT NULL THEN gv.beneficiary WHEN p.invoice_sales_oa_id IS NOT NULL THEN flsb.beneficiary ELSE (
  SELECT 
	beneficiary 
  FROM 
	vendor_pettycash 
  WHERE 
	account_no = a.account_no
) END AS beneficiary, 
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN c.quantity WHEN p.invoice_id IS NOT NULL 
AND (
  (
	SELECT 
	  account_id 
	FROM 
	  invoice_detail 
	WHERE 
	  invoice_id = p.invoice_id 
	LIMIT 
	  1
  ) = 249 
  OR (
	SELECT 
	  account_id 
	FROM 
	  invoice_detail 
	WHERE 
	  invoice_id = p.invoice_id 
	LIMIT 
	  1
  ) = 167
) THEN COALESCE(
  (
	SELECT 
	  qty 
	FROM 
	  invoice_detail 
	WHERE 
	  invoice_id = p.invoice_id 
	LIMIT 
	  1
  ), 0
) WHEN p.invoice_id IS NOT NULL THEN 0 WHEN p.payment_cash_id IS NOT NULL THEN 0 WHEN p.vendor_id IS NOT NULL THEN (
  SELECT 
	SUM(quantity) 
  FROM 
	`transaction` 
  WHERE 
	payment_id = p.payment_id
) WHEN p.sales_id IS NOT NULL THEN COALESCE(sl.quantity, 0) WHEN p.freight_id IS NOT NULL THEN COALESCE(p.qty, 0) WHEN p.vendor_handling_id IS NOT NULL THEN COALESCE(p.qty, 0) WHEN p.labor_id IS NOT NULL THEN COALESCE(p.qty, 0) WHEN p.general_vendor_id IS NOT NULL THEN COALESCE(p.qty, 0) WHEN p.invoice_sales_oa_id IS NOT NULL THEN (
  SELECT 
	COALESCE(total_qty, 0) AS qty 
  FROM 
	pengajuan_payment_sales_oa 
  WHERE 
	idpp = iso.idpp
) ELSE COALESCE(p.qty, 0) END AS quantity, 
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN c.price_converted WHEN p.invoice_id IS NOT NULL 
AND (
  (
	SELECT 
	  account_id 
	FROM 
	  invoice_detail 
	WHERE 
	  invoice_id = p.invoice_id 
	LIMIT 
	  1
  ) = 249 
  OR (
	SELECT 
	  account_id 
	FROM 
	  invoice_detail 
	WHERE 
	  invoice_id = p.invoice_id 
	LIMIT 
	  1
  ) = 167
) THEN (
  SELECT 
	price 
  FROM 
	invoice_detail 
  WHERE 
	invoice_id = p.invoice_id 
  LIMIT 
	1
) WHEN p.invoice_id IS NOT NULL THEN 0 WHEN p.payment_cash_id IS NOT NULL THEN 0 WHEN p.vendor_id IS NOT NULL THEN p.price WHEN p.sales_id IS NOT NULL THEN sl.price_converted WHEN p.freight_id IS NOT NULL THEN p.price WHEN (
  p.vendor_handling_id IS NOT NULL 
  AND p.vendor_handling_id != 0
) THEN p.price WHEN p.labor_id IS NOT NULL THEN p.price WHEN p.general_vendor_id IS NOT NULL THEN p.price WHEN p.invoice_sales_oa_id IS NOT NULL THEN (
  SELECT 
	price 
  FROM 
	pengajuan_payment_sales_oa 
  WHERE 
	idpp = iso.idpp
) ELSE p.price END AS price_converted, 
CASE WHEN p.currency_id = 2 THEN p.amount WHEN p.stockpile_contract_id IS NOT NULL THEN (
  p.amount_converted - p.ppn_amount
) WHEN p.invoice_id IS NOT NULL THEN (
  SELECT 
	SUM(amount_converted) 
  FROM 
	invoice_detail 
  WHERE 
	invoice_id = p.invoice_id
) WHEN p.payment_cash_id IS NOT NULL THEN (
  SELECT 
	SUM(amount_converted) 
  FROM 
	payment_cash 
  WHERE 
	payment_id = p.payment_id
) WHEN p.vendor_id IS NOT NULL 
AND p.ppn_amount_converted > 0 THEN p.amount_converted - p.ppn_amount_converted WHEN p.vendor_id IS NOT NULL THEN p.amount_converted WHEN p.sales_id IS NOT NULL THEN p.amount_converted WHEN p.freight_id IS NOT NULL 
AND p.payment_method = 2 THEN (p.amount_converted) WHEN p.freight_id IS NOT NULL 
AND p.ppn_amount_converted > 0 THEN (
  p.original_amount_converted + p.pph_amount_converted
) - p.ppn_amount_converted WHEN p.freight_id IS NOT NULL THEN (
  p.original_amount_converted + p.pph_amount_converted
) WHEN p.vendor_handling_id IS NOT NULL 
AND p.payment_method = 2 THEN p.amount_converted WHEN p.vendor_handling_id IS NOT NULL THEN (
  p.amount_converted + p.pph_amount_converted
) WHEN p.labor_id IS NOT NULL THEN (
  p.amount_converted + p.pph_amount_converted
) WHEN p.general_vendor_id IS NOT NULL THEN p.amount_converted ELSE p.amount_converted END AS dpp, 
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN p.ppn_amount WHEN p.invoice_id IS NOT NULL THEN (
  SELECT 
	SUM(ppn_converted) 
  FROM 
	invoice_detail 
  WHERE 
	invoice_id = p.invoice_id
) WHEN p.payment_cash_id IS NOT NULL THEN (
  SELECT 
	SUM(ppn_converted) 
  FROM 
	payment_cash 
  WHERE 
	payment_id = p.payment_id
) WHEN p.vendor_id IS NOT NULL THEN p.ppn_amount WHEN p.sales_id IS NOT NULL THEN p.ppn_amount WHEN p.freight_id IS NOT NULL THEN p.ppn_amount WHEN (
  p.vendor_handling_id IS NOT NULL 
  AND p.vendor_handling_id != 0
) THEN p.ppn_amount WHEN p.labor_id IS NOT NULL THEN p.ppn_amount WHEN p.general_vendor_id IS NOT NULL THEN p.ppn_amount WHEN p.invoice_sales_oa_id IS NOT NULL THEN p.ppn_amount ELSE p.ppn_amount END AS ppn_amount, 
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN p.pph_amount WHEN p.invoice_id IS NOT NULL THEN (
  SELECT 
	SUM(pph_converted) 
  FROM 
	invoice_detail 
  WHERE 
	invoice_id = p.invoice_id
) WHEN p.payment_cash_id IS NOT NULL THEN (
  SELECT 
	SUM(pph_converted) 
  FROM 
	payment_cash 
  WHERE 
	payment_id = p.payment_id
) WHEN p.vendor_id IS NOT NULL THEN p.pph_amount WHEN p.sales_id IS NOT NULL THEN p.pph_amount WHEN p.freight_id IS NOT NULL THEN p.pph_amount WHEN (
  p.vendor_handling_id IS NOT NULL 
  AND p.vendor_handling_id != 0
) THEN p.pph_amount WHEN p.labor_id IS NOT NULL THEN p.pph_amount WHEN p.general_vendor_id IS NOT NULL THEN p.pph_amount WHEN p.invoice_sales_oa_id IS NOT NULL THEN p.pph_amount ELSE p.pph_amount END AS pph_amount, 
CASE WHEN p.currency_id = 2 THEN p.amount WHEN p.stockpile_contract_id IS NOT NULL THEN p.amount_converted WHEN p.invoice_id IS NOT NULL THEN (
  (
	(
	  SELECT 
		SUM(amount_converted) 
	  FROM 
		invoice_detail 
	  WHERE 
		invoice_id = p.invoice_id
	) + (
	  SELECT 
		SUM(ppn_converted) 
	  FROM 
		invoice_detail 
	  WHERE 
		invoice_id = p.invoice_id
	)
  ) - (
	SELECT 
	  SUM(pph_converted) 
	FROM 
	  invoice_detail 
	WHERE 
	  invoice_id = p.invoice_id
  )
) WHEN p.payment_cash_id IS NOT NULL THEN (
  (
	(
	  SELECT 
		SUM(amount_converted) 
	  FROM 
		payment_cash 
	  WHERE 
		payment_id = p.payment_id
	) + (
	  SELECT 
		SUM(ppn_converted) 
	  FROM 
		payment_cash 
	  WHERE 
		payment_id = p.payment_id
	)
  ) - (
	SELECT 
	  SUM(pph_converted) 
	FROM 
	  payment_cash 
	WHERE 
	  payment_id = p.payment_id
  )
) WHEN p.vendor_id IS NOT NULL THEN p.amount_converted WHEN p.sales_id IS NOT NULL THEN (
  p.amount_converted + p.ppn_amount
) WHEN p.freight_id IS NOT NULL 
AND p.payment_method = 2 THEN (
  (
	p.amount_converted + p.ppn_amount
  ) - p.pph_amount
) WHEN p.freight_id IS NOT NULL THEN p.amount_converted WHEN (
  p.vendor_handling_id IS NOT NULL 
  AND p.vendor_handling_id != 0
) 
AND p.payment_method = 2 THEN (
  (
	p.amount_converted + p.ppn_amount
  ) - p.pph_amount
) WHEN (
  p.vendor_handling_id IS NOT NULL 
  AND p.vendor_handling_id != 0
) THEN p.amount_converted WHEN p.labor_id IS NOT NULL THEN p.amount_converted WHEN p.general_vendor_id IS NOT NULL THEN (
  (
	p.amount_converted + p.ppn_amount
  ) - p.pph_amount
) ELSE p.amount_converted END AS total, 
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN (
  p.amount_converted - p.original_amount
) WHEN p.invoice_id IS NOT NULL THEN (
  SELECT 
	(
	  COALESCE(
		SUM(idp.amount_payment), 
		0
	  ) + COALESCE(
		SUM(
		  CASE WHEN iddp.`ppn` != 0 THEN idp.amount_payment * (ppn.`tax_value` / 100) ELSE 0 END
		), 
		0
	  )
	) - COALESCE(
	  SUM(
		CASE WHEN iddp.pph != 0 THEN idp.amount_payment * (pph.`tax_value` / 100) ELSE 0 END
	  ), 
	  0
	) 
  FROM 
	invoice_detail id 
	LEFT JOIN invoice_dp idp ON idp.invoice_detail_id = id.invoice_detail_id 
	LEFT JOIN invoice_detail iddp ON iddp.invoice_detail_id = idp.invoice_detail_dp 
	LEFT JOIN tax ppn ON ppn.`tax_id` = iddp.`ppnID` 
	LEFT JOIN tax pph ON pph.`tax_id` = iddp.`pphID` 
  WHERE 
	id.invoice_id = p.invoice_id 
	AND idp.status = 0
) WHEN p.payment_cash_id IS NOT NULL THEN (
  SELECT 
	COALESCE(
	  GROUP_CONCAT(
		(
		  SELECT 
			ROUND(
			  SUM(tamount), 
			  2
			) 
		  FROM 
			payment_cash 
		  WHERE 
			payment_cash_dp = pc.payment_cash_id
		)
	  ), 
	  0
	) 
  FROM 
	payment_cash pc 
  WHERE 
	pc.payment_id = p.payment_id
) WHEN p.freight_id IS NOT NULL 
AND p.ppn_amount_converted > 0 THEN (
  p.original_amount_converted - p.amount_journal
) WHEN p.freight_id IS NOT NULL THEN (
  p.amount_converted - p.amount_journal
) WHEN p.vendor_handling_id IS NOT NULL THEN (
  (
	(
	  p.amount_converted + p.ppn_amount
	) - p.pph_amount
  ) - p.original_amount
) WHEN p.vendor_id IS NOT NULL THEN (
  p.amount_converted - p.original_amount
) ELSE 0 END AS dp, 
CASE WHEN p.currency_id = 2 THEN p.amount
    WHEN p.stockpile_contract_id IS NOT NULL THEN (p.amount_converted - (p.amount_converted - p.original_amount))
	WHEN p.invoice_id IS NOT NULL THEN ((SELECT SUM((amount_converted + ppn_converted) - pph_converted) FROM invoice_detail WHERE invoice_id = p.invoice_id) - (SELECT (COALESCE(SUM(idp.amount_payment),0) + COALESCE(SUM(CASE WHEN id.`ppn` != 0 THEN idp.amount_payment * (ppn.`tax_value`/100) ELSE 0 END),0)) -
COALESCE(SUM(CASE WHEN id.pph != 0 THEN idp.amount_payment * (pph.`tax_value`/100) ELSE 0 END),0) FROM invoice_detail id
	LEFT JOIN invoice_dp idp ON idp.invoice_detail_id = id.invoice_detail_id
	LEFT JOIN invoice_detail iddp ON iddp.invoice_detail_id = idp.invoice_detail_dp
	LEFT JOIN tax ppn ON ppn.`tax_id` = id.`ppnID`
	LEFT JOIN tax pph ON pph.`tax_id` = id.`pphID`
	WHERE id.invoice_id = p.invoice_id))
	WHEN p.payment_cash_id IS NOT NULL THEN ((SELECT SUM((amount_converted + ppn_converted) - pph_converted) FROM payment_cash WHERE payment_id = p.payment_id) - (SELECT COALESCE(GROUP_CONCAT((SELECT ROUND(SUM(tamount),2) FROM payment_cash WHERE payment_cash_dp = pc.payment_cash_id)),0) FROM payment_cash pc WHERE pc.payment_id = p.payment_id))
	WHEN p.vendor_id IS NOT NULL THEN p.original_amount
	WHEN p.sales_id IS NOT NULL THEN (p.amount_converted + p.ppn_amount)
	WHEN p.freight_id IS NOT NULL AND p.payment_method = 2 THEN ((p.amount_converted + p.ppn_amount) - p.pph_amount) - (p.amount_converted - p.original_amount)
	WHEN p.freight_id IS NOT NULL THEN (p.amount_converted - (p.amount_converted - p.original_amount))
	WHEN p.general_vendor_id IS NOT NULL THEN (((p.amount_converted + p.ppn_amount) - p.pph_amount) - (p.amount_converted - p.original_amount))
	WHEN p.vendor_handling_id IS NOT NULL AND p.payment_method = 2 THEN (p.amount_converted - p.pph_amount)
	WHEN p.vendor_handling_id IS NOT NULL THEN p.amount_converted
	WHEN p.labor_id IS NOT NULL THEN p.amount_converted
ELSE p.amount_converted END AS grand_total,
  p.payment_type, 
s.stockpile_name, 
u.user_name, 
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN cvppn.tax_name WHEN p.invoice_id IS NOT NULL THEN (
  SELECT 
	tx.tax_name 
  FROM 
	tax tx 
	LEFT JOIN general_vendor gv ON gv.ppn_tax_id = tx.tax_id 
	LEFT JOIN invoice_detail id ON gv.general_vendor_id = id.general_vendor_id 
  WHERE 
	id.invoice_id = p.invoice_id 
  LIMIT 
	1
) WHEN p.payment_cash_id IS NOT NULL THEN (
  SELECT 
	tx.tax_name 
  FROM 
	tax tx 
	LEFT JOIN general_vendor gv ON gv.ppn_tax_id = tx.tax_id 
	LEFT JOIN payment_cash pc ON gv.general_vendor_id = pc.general_vendor_id 
  WHERE 
	pc.payment_id = p.payment_id 
  LIMIT 
	1
) WHEN p.vendor_id IS NOT NULL THEN vppn.tax_name WHEN p.sales_id IS NOT NULL THEN custppn.tax_name WHEN p.freight_id IS NOT NULL THEN fppn.tax_name WHEN (
  p.vendor_handling_id IS NOT NULL 
  AND p.vendor_handling_id != 0
) THEN vhppn.tax_name WHEN p.labor_id IS NOT NULL THEN lppn.tax_name WHEN p.general_vendor_id IS NOT NULL THEN gvppn.tax_name WHEN p.invoice_sales_oa_id IS NOT NULL THEN txppns.tax_name ELSE '' END AS ppn, 
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN cvpph.tax_name WHEN p.invoice_id IS NOT NULL THEN (
  SELECT 
	tx.tax_name 
  FROM 
	tax tx 
	LEFT JOIN general_vendor gv ON gv.pph_tax_id = tx.tax_id 
	LEFT JOIN invoice_detail id ON gv.general_vendor_id = id.general_vendor_id 
  WHERE 
	id.invoice_id = p.invoice_id 
  LIMIT 
	1
) WHEN p.payment_cash_id IS NOT NULL THEN (
  SELECT 
	tx.tax_name 
  FROM 
	tax tx 
	LEFT JOIN general_vendor gv ON gv.pph_tax_id = tx.tax_id 
	LEFT JOIN payment_cash pc ON gv.general_vendor_id = pc.general_vendor_id 
  WHERE 
	pc.payment_id = p.payment_id 
  LIMIT 
	1
) WHEN p.vendor_id IS NOT NULL THEN vpph.tax_name WHEN p.sales_id IS NOT NULL THEN custpph.tax_name WHEN p.freight_id IS NOT NULL THEN fpph.tax_name WHEN (
  p.vendor_handling_id IS NOT NULL 
  AND p.vendor_handling_id != 0
) THEN vhpph.tax_name WHEN p.labor_id IS NOT NULL THEN lpph.tax_name WHEN p.general_vendor_id IS NOT NULL THEN gvpph.tax_name WHEN p.invoice_sales_oa_id IS NOT NULL THEN txpphs.tax_name ELSE '' END AS pph,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN p.remarks WHEN p.invoice_id IS NOT NULL THEN i.remarks WHEN p.payment_cash_id IS NOT NULL THEN p.remarks WHEN p.vendor_id IS NOT NULL THEN p.remarks WHEN p.sales_id IS NOT NULL THEN p.remarks WHEN p.freight_id IS NOT NULL THEN p.remarks WHEN (
  p.vendor_handling_id IS NOT NULL 
  AND p.vendor_handling_id != 0
) THEN p.remarks WHEN p.labor_id IS NOT NULL THEN p.remarks WHEN p.general_vendor_id IS NOT NULL THEN p.remarks ELSE p.remarks END AS keterangan, 
s.stockpile_code, 
p.remarks2, 
p.payment_date, 
DATE_FORMAT(p.entry_date, '%Y-%m-%d') AS entry_date, 
CASE WHEN p.payment_location = 0 THEN 'HOF' ELSE ps.stockpile_name END AS payment_location, 
CASE WHEN p.payment_location = 0 THEN 'HO' ELSE 'Stockpile' END AS payment_location2, 
CASE WHEN p.payment_type = 1 THEN 'IN' ELSE 'OUT' END AS paymentType, 
CONCAT(
  DATE_FORMAT(p.`period_from`, '%d%b'), 
  '-', 
  DATE_FORMAT(p.`period_to`, '%d%b%y')
) AS periode, 
c.`contract_type`, 
(
  SELECT 
	ac.account_no 
  FROM 
	account ac 
	LEFT JOIN invoice_detail id ON id.account_id = ac.account_id 
  WHERE 
	id.invoice_id = i.invoice_id 
  GROUP BY 
	i.`invoice_id` 
  LIMIT 
	1
) AS accountInvoice, 
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN cv.vendor_code WHEN p.invoice_id IS NOT NULL THEN (
  SELECT 
	gv.general_vendor_name 
  FROM 
	general_vendor gv 
	LEFT JOIN invoice_detail id ON gv.general_vendor_id = id.general_vendor_id 
  WHERE 
	id.invoice_id = p.invoice_id 
  LIMIT 
	1
) WHEN p.payment_cash_id IS NOT NULL THEN (
  SELECT 
	gv.general_vendor_name 
  FROM 
	general_vendor gv 
	LEFT JOIN payment_cash pc ON gv.general_vendor_id = pc.general_vendor_id 
  WHERE 
	pc.payment_id = p.payment_id 
  LIMIT 
	1
) WHEN p.vendor_id IS NOT NULL THEN v.vendor_code WHEN p.sales_id IS NOT NULL THEN cust.customer_name WHEN p.freight_id IS NOT NULL THEN f.freight_code WHEN (
  p.vendor_handling_id IS NOT NULL 
  AND p.vendor_handling_id != 0
) THEN vh.vendor_handling_code WHEN p.labor_id IS NOT NULL THEN l.labor_name WHEN p.general_vendor_id IS NOT NULL THEN gv.general_vendor_name ELSE (
  SELECT 
	vendor_name 
  FROM 
	vendor_pettycash 
  WHERE 
	account_no = a.account_no
) END AS vendor_code, 
(
  SELECT 
	termin 
  FROM 
	invoice_detail 
  WHERE 
	invoice_id = i.invoice_id 
  GROUP BY 
	i.`invoice_id` 
  LIMIT 
	1
) AS termin, 
CASE WHEN p.invoice_id IS NOT NULL THEN (p.gv_email) WHEN p.invoice_id IS NULL THEN (
  SELECT 
	sto.stockpile_email 
  FROM 
	stockpile sto 
  WHERE 
	sto.stockpile_id = p.stockpile_location 
  LIMIT 
	1
) ELSE '' END AS email2, 
p.gv_email2 AS email3, 
CASE WHEN p.payment_cash_id IS NOT NULL THEN DATE_FORMAT(p.invoice_date, '%d%b%y') WHEN p.invoice_id IS NOT NULL THEN DATE_FORMAT(i.invoice_date, '%d%b%y') ELSE '' END AS invoice_date 
FROM 
payment p 
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
LEFT JOIN invoice_sales_oa iso ON iso.payment_id = p.payment_id 
LEFT JOIN freight_local_sales fls ON fls.freight_id = iso.freightId 
LEFT JOIN tax txpphs ON txpphs.tax_id = fls.pph_tax_id 
LEFT JOIN tax txppns ON txppns.tax_id = fls.ppn_tax_id 
LEFT JOIN freight_local_sales_bank flsb ON flsb.freight_id = iso.freightId 
WHERE 
1 = 1 
AND p.payment_status = 0 
{$whereProperty}
-- AND p.payment_id NOT IN (SELECT payment_id FROM batch_upload_detail WHERE STATUS != 2)
";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

$fileName = "Batch Upload ". $periodFull . str_replace(" ", "-", $_SESSION['userName']) . ".xls";
$onSheet = 0;
$lastColumn = "R";

// <editor-fold defaultstate="collapsed" desc="Create Excel and Define Header">
$objPHPExcel = new PHPExcel();
PHPExcel_Cell::setValueBinder(new PHPExcel_Cell_AdvancedValueBinder());

$objPHPExcel->setActiveSheetIndex($onSheet);
$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(100);
$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
$objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
$objPHPExcel->getDefaultStyle()->getFont()->setSize(11);

$rowActive = 1;

$data = array();
while($rowData = mysqli_fetch_array($result)) {
    $data[] = $rowData;
}

//xlsWriteLabel($y,$x,$sql);

$no = 0;
$grandTotal = 0;
foreach ($data as $row) {
	$no++;
	$grandTotal += $row['grand_total'];
}

$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "P");
$objPHPExcel->getActiveSheet()->setCellValueExplicit("B{$rowActive}", date("Ymd"), PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("C{$rowActive}", "1020006401522", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("D{$rowActive}", $no, PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("E{$rowActive}", $grandTotal, PHPExcel_Cell_DataType::TYPE_STRING);

$objPHPExcel->getActiveSheet()->getStyle("E{$rowActive}")->getNumberFormat()->setFormatCode();

foreach ($data as $row) {
	$email = "finance@jatimpropertindo.com;";
	if ($row['email2'] != '' && $row['email2'] != '-' && $row['email2'] != 'undefined') {
		$email .= $row['email2'].";";
	}
	// if ($row['email3'] != '') {
	// 	$email .= $row['email3'].";";
	// }

	// Kode remark
	$remarks = "";

	if ($row['kode3'] == "PKS") {
		if ($row['contract_type'] == "P") {
			$remarks .= $row['stockpile_code'] . " KTRK " . $row['vendor_code'] . " " . str_replace(".", "", (number_format($row['quantity'] / 1000, 0, ",", "."))) . "MT";

		} else {
			$remarks .= $row['stockpile_code'] . " CRH " . $row['vendor_code'] . " " . str_replace(".", "", (number_format($row['quantity'] / 1000, 0, ",", "."))) . "MT";
		}
	}

	if ($row['kode3'] == "OA") {
		$remarks .= $row['stockpile_code'] . " OA " . $row['periode'];
	}

	if ($row['kode3'] == "HND") {
		$remarks .= $row['stockpile_code'] . " HND " . $row['periode'];
	}

	if ($row['kode3'] == "OB") {
		$remarks .= $row['stockpile_code'] . " OB " . $row['periode'];
	}

	if ($row['kode3'] == "INV") {
		$str = preg_replace('#[aeiou/\(|\)/]+#i', "", strtoupper($row['keterangan']));
		$strArr = explode(" ", $str);

		if (in_array("PPH", $strArr)) {
			$str = strtoupper($row['keterangan']);
			$strArr = explode(" ", $str);
			$remarks .= $row['stockpile_code'] . " PPh" . $strArr[in_array("PPH", $strArr) + 1] . $strArr[in_array("PPH", $strArr) + 2];

		} else if ($row['accountInvoice'] == "510403") {
			$remarks .= $row['stockpile_code'] . " KSOP";

		} else if ($row['accountInvoice'] == "510601") {
			$remarks .= $row['stockpile_code'] . " SVRYINTRTK";

		} else if ($row['accountInvoice'] == "510602") {
			$remarks .= $row['stockpile_code'] . " SVRYSCI";

		} else if ($row['accountInvoice'] == "510604") {
			$remarks .= $row['stockpile_code'] . " SVRYSCF";

		} else if ($row['accountInvoice'] == "510605") {
			$remarks .= $row['stockpile_code'] . " SVRYCRS";

		} else if ($row['accountInvoice'] == "510606") {
			$remarks .= $row['stockpile_code'] . " SVRYSGS";

		} else if ($row['accountInvoice'] == "510606") {
			$remarks .= $row['stockpile_code'] . " SVRYSGS";

		} else if ($row['accountInvoice'] == "510900") {
			$remarks .= $row['stockpile_code'] . " PHYTO";

		} else if ($row['accountInvoice'] == "700080") {
			$remarks .= $row['stockpile_code'] . " ADM BNK";

		} else if ($row['accountInvoice'] == "510800") {
			$remarks .= $row['stockpile_code'] . " FUMI";

		} else if ($row['accountInvoice'] == "510700") {
			$remarks .= $row['stockpile_code'] . " BGRENT";

		} else if ($row['accountInvoice'] == "510502") {
			$remarks .= $row['stockpile_code'] . " mobdemob";

		} else if ($row['accountInvoice'] == "510501") {
			$remarks .= $row['stockpile_code'] . " SW ALT";

		} else if ($row['accountInvoice'] == "510405") {
			$remarks .= $row['stockpile_code'] . " EMKL";

		} else if ($row['accountInvoice'] == "520401") {
			$remarks .= $row['stockpile_code'] . " SW ALT";

		} else if ($row['accountInvoice'] == "520900") {
			$remarks .= $row['stockpile_code'] . " OKSAKT";

		} else if ($row['accountInvoice'] == "520900") {
			$remarks .= $row['stockpile_code'] . " BPJS";

		} else if ($row['accountInvoice'] == "150200") {
			$remarks .= $row['stockpile_code'] . " Adv prdn";

		} else if ($row['accountInvoice'] == "270100") {
			$remarks .= $row['stockpile_code'] . " ANGSRN";

		} else if ($row['accountInvoice'] == "700014") {
			$remarks .= $row['stockpile_code'] . " TRM UANG";

		} else if ($row['accountInvoice'] == "510401") {
			$remarks .= $row['stockpile_code'] . " BY UPR JS";

		} else if ($row['accountInvoice'] == "510302") {
			$remarks .= $row['stockpile_code'] . " BEA KLR LDNG";

		} else if ($row['accountInvoice'] == "510301") {
			$remarks .= $row['stockpile_code'] . " PNGTN SWIT";

		} else {
			$remarks .= $row['stockpile_code'] . " " . $strArr[0] . " " . $strArr[1];
		}

		if (strlen($remarks) > 11) {
			$remarks = substr($remarks, 0, 11);
		}

		if ($row['invoice_date'] != '') {
			$remarks .= " " . $row['invoice_date'];
		}
	}

	if ($row['kode3'] == "PC") {
		$str = preg_replace('#[aeiou/\(|\)/]+#i', "", strtoupper($row['keterangan']));
		$strArr = explode(" ", $str);

		if (in_array("BBM", $strArr)) {
			$remarks .= $row['stockpile_code'] . " BBM";

		} else if (in_array("ADVANCE", $strArr)) {
			$remarks .= $row['stockpile_code'] . " ADV";

		} else if (in_array("SETTLEMENT", $strArr)) {
			$remarks .= $row['stockpile_code'] . " STTLMNT";

		} else if (in_array("BY.", $strArr)) {
			$remarks .= $row['stockpile_code'] . " BY. ADM BNK";

		} else if (in_array("MAKAN", $strArr)) {
			$remarks .= $row['stockpile_code'] . " UANG MKN";

		} else if (in_array("BURUH", $strArr)) {
			$remarks .= $row['stockpile_code'] . " UPH BRH";

		} else if (in_array("INTERNET", $strArr)) {
			$remarks .= $row['stockpile_code'] . " INTRNT";

		} else if (in_array("TIKET", $strArr)) {
			$remarks .= $row['stockpile_code'] . " TKT";

		} else if (in_array("BIAYA", $strArr)) {
			$remarks .= $row['stockpile_code'] . " BY " . $strArr[in_array("BIAYA", $strArr) + 1];

		} else if (in_array("ATAS", $strArr)) {
			$remarks .= $row['stockpile_code'] . " PMBYRN ATS " . $strArr[in_array("ATAS", $strArr) + 1];

		} else if (in_array("PEMBELIAN", $strArr)) {
			$remarks .= $row['stockpile_code'] . " PMBLN " . $strArr[in_array("PEMBELIAN", $strArr) + 1];

		} else {
			if (in_array("UNTUK", $strArr) == 1) {
				$remarks .= $row['stockpile_code'] . " " . $strArr[0] . " " . $strArr[2];

			} else if (count($strArr) > 1) {
				$remarks .= $row['stockpile_code'] . " " . $strArr[0] . " " . $strArr[1];
	
			} else {
				$remarks .= $row['stockpile_code'] . " " . $strArr[0];

			}
		}

		if (in_array("DAN", $strArr)) {
			$remarks .= " & " . $strArr[in_array("DAN", $strArr) + 1];
		}

		if (strlen($remarks) > 11) {
			$remarks = substr($remarks, 0, 11);
		}

		if ($row['invoice_date'] != '') {
			$remarks .= " " . $row['invoice_date'];
		}
	}

	if ($row['kode3'] == "SLS") {
		$str = preg_replace('#[aeiou/\(|\)/]+#i', "", strtoupper($row['keterangan']));
		$strArr = explode(" ", $str);

		if (stripos($str,"TERIMA UANG") !== false) {
			$remarks .= $row['stockpile_code'] . " TRM UANG";

		} else {
			$remarks .= $row['stockpile_code'] . " " . $strArr[0];

		}

		if (strlen($remarks) > 11) {
			$remarks = substr($remarks, 0, 11);
		}

		if ($row['invoice_date'] != '') {
			$remarks .= " " . $row['invoice_date'];
		}
	}

	if ($row['kode3'] == "") {
		$str = preg_replace('#[aeiou/\(|\)/]+#i', "", strtoupper($row['keterangan']));
		$strArr = explode(" ", $str);

		$remarks .= $row['stockpile_code'] . " " . $strArr[0] . " " . $strArr[1];

		if (strlen($remarks) > 11) {
			$remarks = substr($remarks, 0, 11);
		}

		if ($row['invoice_date'] != '') {
			$remarks .= " " . $row['invoice_date'];
		}
	}

	$kode3 = strtoupper(str_replace(" ", "", $remarks));
	// End Kode Remark

	$rowActive++;
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("A{$rowActive}", $row['no_rek'], PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $row['benificiary']);
	$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $row['stockpile_name']);
	$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "");
	$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "");
	$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "IDR");

	if ($row['payment_type'] == 1) {
		$grand_total = $row['grand_total'] * -1;
	} else {
		$grand_total = $row['grand_total'];
	}

    $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $grand_total);
	$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $kode3);
	$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "");
	$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $row['kode1']);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("K{$rowActive}", $row['kode2'], PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $row['bank_name']);
	$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "");
	$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", "");
	$objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", "");
	$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", $row['branch']);
	$objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", "Y");
	$objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", $email);
	$no++;

	$objPHPExcel->getActiveSheet()->getStyle("G{$rowActive}")->getNumberFormat()->setFormatCode();
}

$bodyRowEnd = $rowActive;

// Set column width
for ($temp = ord("A"); $temp <= ord("R"); $temp++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
}

// <editor-fold defaultstate="collapsed" desc="Save Excel and return to browser">
ob_end_clean();
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="' . $fileName . '"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
// </editor-fold>
exit();

?>
