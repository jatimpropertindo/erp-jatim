<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$whereProperty = '';
$whereProperty2 = '';
$whereProperty3 = '';
//$sumProperty = '';
//$stockpileId = '';
$periodFrom = '';
$periodTo = '';
$pType = '';
$pTypes = '';
$bankId = '';
$bankIds = '';
$lastPaymentId = '';
$pLocation = '';
$pLocations = '';
$paymentNo = '';
$paymentNos = '';
$batchUpload = '-';
$paymentTypes = '';
$fileFormat = 'Excel';
$checkedPaymentIds = [];


//$balanceBefore = 0;
//$boolBalanceBefore = false;
/*
if(isset($_POST['stockpileId']) && $_POST['stockpileId'] != '') {
    $stockpileId = $_POST['stockpileId'];
    $sql = "SELECT stockpile_code FROM stockpile WHERE stockpile_id = {$stockpileId}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    $row = $result->fetch_object();

    $whereProperty .= " AND t.slip_no like '{$row->stockpile_code}%' ";
    $sumProperty .= " AND t.slip_no like '{$row->stockpile_code}%' ";
}*/

function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1, $onchange = "", $boolAllow = false) {
    global $myDatabase;
    
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    echo "<SELECT class='$class' tabindex='$tabindex' $disabled name='" . ($id . $uniq) . "' id='" . ($id . $uniq) . "' $onchange style='width: 13em;'>";
    
    if($empty == 1) {
        echo "<option value=''>-- Please Select --</option>";
    } else if($empty == 2) {
        echo "<option value=''>-- Please Select Stockpile --</option>";
    } else if($empty == 3) {
        echo "<option value=''>-- Please Select Type --</option>";
    } else if($empty == 4) {
        echo "<option value=''>-- Please Select Payment For --</option>";
    } else if($empty == 5) {
        echo "<option value=''>-- Please Select Method --</option>";
    } else if($empty == 6) {
        echo "<option value=''>-- Please Select Buyer --</option>";
    } else if($empty == 7) {
        echo "<option value=''>-- All --</option>";
    }
    
    if($result !== false) {
        while ($combo_row = $result->fetch_object()) {
            if (strtoupper($combo_row->$valuekey) == strtoupper($setvalue))
                $prop = "selected";
            else
                $prop = "";

            echo "<OPTION value=\"" . $combo_row->$valuekey . "\" " . $prop . ">" . $combo_row->$value . "</OPTION>";
        }
    }
    
    if($boolAllow) {
        echo "<option value='INSERT'>-- Insert New --</option>";
    }
    
    echo "</SELECT>";
}

$sql = "SELECT * FROM user_module WHERE user_id = {$_SESSION['userId']} AND module_id = 27";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
if($result->num_rows > 0) {
    while($row = $result->fetch_object()) {
        if($row->module_id == 27) {

			$whereProperty = "";

			if (isset($_POST['pLocation']) && $_POST['pLocation'] != '') {
				$pLocation = $_POST['pLocation'];
				for ($i = 0; $i < sizeof($pLocation); $i++) {
					if($pLocations == '') {
						$pLocations .= "'". $pLocation[$i] ."'";
					} else {
						$pLocations .= ','. "'". $pLocation[$i] ."'";
					}
				}

				$whereProperty3 .= "AND (CASE WHEN p.payment_location = 0 THEN 'HO' ELSE 'Stockpile' END) IN ({$pLocations})";
			}

			if (isset($_POST['pLocations']) && $_POST['pLocations'] != '') {
				$pLocations = $_POST['pLocations'];
				$whereProperty3 .= "AND (CASE WHEN p.payment_location = 0 THEN 'HO' ELSE 'Stockpile' END) IN ({$pLocations})";
			}

        } else {
			$whereProperty = "AND p.entry_by = {$_SESSION['userId']}";
			$whereProperty3 = "";
		}

	}
}

if (isset($_POST['pType']) && $_POST['pType'] != '') {
	$pType = $_POST['pType'];
	for ($i = 0; $i < sizeof($pType); $i++) {
		if($pTypes == '') {
			$pTypes .= $pType[$i];
		} else {
			$pTypes .= ','. $pType[$i];
		}
	}

	$whereProperty2 .= "AND p.payment_type2 IN ({$pTypes})";
}

if (isset($_POST['pTypes']) && $_POST['pTypes'] != '') {
	$pTypes = $_POST['pTypes'];
	$whereProperty2 .= "AND p.payment_type2 IN ({$pTypes})";
}

if (isset($_POST['paymentNo']) && $_POST['paymentNo'] != '') {
	$paymentNo = $_POST['paymentNo'];
	for ($i = 0; $i < sizeof($paymentNo); $i++) {
		if($paymentNos == '') {
			$paymentNos .= "'". $paymentNo[$i] ."'";
		} else {
			$paymentNos .= ','."'". $paymentNo[$i] ."'";
		}
	}

	$whereProperty2 .= "AND p.payment_no IN ({$paymentNos})";
}

if (isset($_POST['paymentNos']) && $_POST['paymentNos'] != '') {
	$paymentNos = $_POST['paymentNos'];
	$whereProperty2 .= "AND p.payment_no IN ({$paymentNos})";
}

if (isset($_POST['bankAccounts']) && $_POST['bankAccounts'] != '') {
    $bankId = $_POST['bankAccounts'];

	for ($i = 0; $i < sizeof($bankId); $i++) {
		if($bankIds == '') {
			$bankIds .= "'". $bankId[$i] ."'";
		} else {
			$bankIds .= ','."'". $bankId[$i] ."'";
		}
	}

    $whereProperty .= " AND b.bank_id IN ({$bankIds}) ";
}

if (isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodFrom = $_POST['periodFrom'];
    $periodTo = $_POST['periodTo'];
    $whereProperty .= " AND DATE_FORMAT(p.payment_date, '%Y-%m-%d') BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
    //$sumProperty .= " AND IF(t.transaction_type = 1, t.unloading_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
    //$boolBalanceBefore = true;
} else if (isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] == '') {
    $periodFrom = $_POST['periodFrom'];
    $whereProperty .= " AND DATE_FORMAT(p.payment_date, '%Y-%m-%d') >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')";
    //$sumProperty .= " AND IF(t.transaction_type = 1, t.unloading_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
    //$boolBalanceBefore = true;
} else if (isset($_POST['periodFrom']) && $_POST['periodFrom'] == '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodTo = $_POST['periodTo'];
    $whereProperty .= " AND DATE_FORMAT(p.payment_date, '%Y-%m-%d') <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";
}

if (isset($_POST['checkedPaymentIds']) && $_POST['checkedPaymentIds'] != '') {
    $checkedPaymentIds = explode(",", $_POST['checkedPaymentIds']);
}

$sql = "SELECT p.payment_id, p.payment_no, p.payment_date, p.entry_date, p.payment_type, 
(SELECT batch_detail.batch_code FROM batch_upload_detail AS batch_detail
 WHERE batch_detail.payment_id = p.payment_id AND batch_detail.STATUS != 2 ORDER BY batch_detail.batch_upload_detail_id DESC LIMIT 1) AS batch_code, 
bud.remarks AS batch_remarks, 
(SELECT batch_detail.batch_upload_detail_id FROM batch_upload_detail AS batch_detail WHERE batch_detail.payment_id = p.payment_id AND batch_detail.STATUS != 2 ORDER BY batch_detail.batch_upload_detail_id DESC LIMIT 1) AS batch_upload_detail_id,
(SELECT batch_header.batch_number FROM batch_upload_detail AS batch_detail 
LEFT JOIN batch_upload_header as batch_header ON batch_header.batch_code = batch_detail.batch_code
WHERE batch_detail.payment_id = p.payment_id AND batch_detail.STATUS != 2 ORDER BY batch_detail.batch_upload_detail_id DESC LIMIT 1) AS batch_number, 
CASE WHEN p.payment_location = 0 THEN 'HOF'
ELSE ps.stockpile_name END AS payment_location,
CASE WHEN p.payment_location = 0 THEN 'HO'
ELSE 'Stockpile' END AS payment_location2,
CASE WHEN p.payment_type = 1 THEN 'IN'
ELSE 'OUT' END AS paymentType,
b.bank_code, b.bank_type, pcur.currency_code AS pcur_currency_code, p.payment_type2,
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
s.stockpile_code,
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
ELSE 'INTERNAL TRANSFER' END AS kode_3,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN 'PKS' 
	WHEN p.invoice_id IS NOT NULL THEN 'INV' 
	WHEN p.payment_cash_id IS NOT NULL THEN 'PC' 
	WHEN p.freight_id IS NOT NULL THEN 'OA' 
	WHEN (p.vendor_handling_id IS NOT NULL AND p.vendor_handling_id != 0) THEN 'HND' 
	WHEN p.labor_id IS NOT NULL THEN 'OB' 
	WHEN p.vendor_id IS NOT NULL THEN 'PKS' 
	WHEN p.sales_id IS NOT NULL THEN 'SLS' 
	WHEN p.general_vendor_id IS NOT NULL THEN '' 
ELSE '' END AS kode3,
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
CASE 
	WHEN p.stockpile_contract_id IS NOT NULL THEN (p.amount_converted - p.ppn_amount)
	WHEN p.invoice_id IS NOT NULL and p.currency_id != 1 THEN (SELECT SUM(amount) FROM invoice_detail WHERE invoice_id = p.invoice_id)
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
	WHEN p.currency_id = 2 THEN p.amount
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
{$whereProperty}{$whereProperty2}{$whereProperty3} GROUP BY p.payment_id ORDER BY bud.batch_code IS NULL DESC, bud.batch_code ASC";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

 //echo $sql;

// var_dump('Chekedbox : ' . (count($checkedPaymentIds) != 0 ? implode(",", $checkedPaymentIds) : ''));

?>
<script type="text/javascript">

    $(document).ready(function() {	//executed after the page has loaded
		$.extend($.tablesorter.themes.bootstrap, {
            // these classes are added to the table. To see other table classes available,
            // look here: http://twitter.github.com/bootstrap/base-css.html#tables
            table      : 'table table-bordered',
            header     : 'bootstrap-header', // give the header a gradient background
            footerRow  : '',
            footerCells: '',
            icons      : '', // add "icon-white" to make them white; this icon class is added to the <i> in the header
            sortNone   : 'bootstrap-icon-unsorted',
            sortAsc    : 'icon-chevron-up',
            sortDesc   : 'icon-chevron-down',
            active     : '', // applied when column is sorted
            hover      : '', // use custom css here - bootstrap class may not override it
            filterRow  : '', // filter row class
            even       : '', // odd row zebra striping
            odd        : ''  // even row zebra striping
        });

        // call the tablesorter plugin and apply the uitheme widget
        $("#contentTable").tablesorter({
            // this will apply the bootstrap theme if "uitheme" widget is included
            // the widgetOptions.uitheme is no longer required to be set
            theme : "bootstrap",

            widthFixed: true,

            headerTemplate : '{content} {icon}', // new in v2.7. Needed to add the bootstrap icon!

            // widget code contained in the jquery.tablesorter.widgets.js file
            // use the zebra stripe widget if you plan on hiding any rows (filter widget)
            widgets : [ 'zebra', 'filter', 'uitheme' ],
                    
            headers: { 0: { sorter: false, filter: false } },

            widgetOptions : {
                // using the default zebra striping class name, so it actually isn't included in the theme variable above
                // this is ONLY needed for bootstrap theming if you are using the filter widget, because rows are hidden
                zebra : ["even", "odd"],
                        
                // filter_functions : {
                //     1: true,
                //     6: true,
                //     7: true
                // },
                // reset filters button
//                filter_reset : ".reset"

                // set the uitheme widget to use the bootstrap theme class names
                // this is no longer required, if theme is set
                // ,uitheme : "bootstrap"

            }
        })
        // .tablesorterPager({

        //     // target the pager markup - see the HTML block below
        //     container: $(".pager"),

        //     // target the pager page select dropdown - choose a page
        //     cssGoto  : ".pagenum",

        //     // remove rows from the table to speed up the sort of large tables.
        //     // setting this to false, only hides the non-visible rows; needed if you plan to add/remove rows with the pager enabled.
        //     removeRows: false,
        //     // output string - default is '{page}/{totalPages}';
        //     // possible variables: {page}, {totalPages}, {filteredPages}, {startRow}, {endRow}, {filteredRows} and {totalRows}
        //     output: '{startRow} - {endRow} / {filteredRows} ({totalRows})'

        // });

		checkPayment();

        $('#printApprovalSheet').click(function(e){
            e.preventDefault();

            // $("#transactionContainer").show();
            // https://github.com/jasonday/printThis
            $("#approvalSheet").printThis();
			// $("#transactionContainer").hide();
        });

		$("#approvalSheetsForm").validate({
            submitHandler: function(form) {
				var periodFrom = $('input[id="periodFrom"]').val();
				var periodTo = $('input[id="periodTo"]').val();
				var bankIds = $('input[id="bankIds"]').val();
				var pTypes = $('input[id="pTypes"]').val();
				var paymentNos = $('input[id="paymentNos"]').val();
				var pLocations = $('input[id="pLocations"]').val();
				var paymentIds = $('input[id="paymentIds"]').val();
				var batchUpload = $('input[id="batchUpload"]').val();
				var paymentTypes = $('input[id="paymentTypes"]').val();
				var fileFormat = $('select[id="fileFormat"]').val();	

				if (fileFormat == 'PDF') {
					$.redirect("./reports/paymentToTax-pdf.php", {periodFrom: periodFrom, periodTo: periodTo, bankIds: bankIds, pTypes: pTypes, paymentNos: paymentNos, pLocations: pLocations, paymentIds: paymentIds, batchUpload: batchUpload, paymentTypes: paymentTypes, fileFormat: fileFormat}, "POST", "_blank");
				} else {
					$.redirect("./reports/paymentToTax-xls.php", {periodFrom: periodFrom, periodTo: periodTo, bankIds: bankIds, pTypes: pTypes, paymentNos: paymentNos, pLocations: pLocations, paymentIds: paymentIds, batchUpload: batchUpload, paymentTypes: paymentTypes, fileFormat: fileFormat}, "POST", "_blank");
				}
            }
        });

		$("#batchUploadForm").validate({
            rules: {},
            messages: {},
            submitHandler: function(form) {
				alertify.set({ labels: {
                    ok     : "Yes",
                    cancel : "No"
                } });
                alertify.confirm("Are you sure want to create batch upload?", function(e) {
                    if (e) {
						var periodFrom = $('input[id="periodFrom"]').val();
						var periodTo = $('input[id="periodTo"]').val();
						var bankAccounts = $('input[id="bankIds"]').val();
						var pTypes = $('input[id="pTypes"]').val();
						var paymentNos = $('input[id="paymentNos"]').val();
						var pLocations = $('input[id="pLocations"]').val();
						var paymentIds = $('input[id="paymentIds"]').val();
						var checkedPaymentIds = $('input[id="checkedPaymentIds"]').val();

						$.blockUI({
							message: '<h4>Please wait...</h4>'
						});

						$.redirect("./reports/print_batch_upload.php", {periodFrom: periodFrom, periodTo: periodTo, paymentIds: paymentIds}, "POST", "_blank");

						$.ajax({
							url: './data_processing.php',
							method: 'POST',
							data: $("#batchUploadForm").serialize(),
							success: function(data) {
								
								$("body").css("cursor", "default");
								var returnVal = data.split('|');
								
								if (parseInt(returnVal[4]) != 0)	//if no errors
								{
									alertify.set({ labels: {
										ok     : "OK"
									} });
									
									$('#dataContent').load('reports/paymentToTax.php', {periodFrom: periodFrom, periodTo: periodTo, bankAccounts: bankAccounts, pTypes: pTypes, paymentNos: paymentNos, pLocations: pLocations, checkedPaymentIds: checkedPaymentIds}, iAmACallbackFunction2);
									alertify.alert(returnVal[2]);
								}
							}
						});
					}
                    return false;
                });
            }
        });

		$('#contentTable a').click(function (e) {
            e.preventDefault();
            $("#successMsgAll").hide();
            $("#errorMsgAll").hide();

			var periodFrom = $('input[id="periodFrom"]').val();
			var periodTo = $('input[id="periodTo"]').val();
			var bankAccounts = $('input[id="bankIds"]').val();
			var pTypes = $('input[id="pTypes"]').val();
			var paymentNos = $('input[id="paymentNos"]').val();
			var pLocations = $('input[id="pLocations"]').val();
			var paymentIds = $('input[id="paymentIds"]').val();

            //alert(this.id);
            var linkId = this.id;
            var menu = linkId.split('|');
            if (menu[0] == 'cancelBatchUpload') {
                e.preventDefault();

                $('#cancelModal').modal('show');
                $('#cancelModalForm').load('forms/cancel-batch-upload.php', {batch_upload_detail_id: menu[2], periodFrom: periodFrom, periodTo: periodTo, bankAccounts: bankAccounts, pTypes: pTypes, paymentNos: paymentNos, pLocations: pLocations}, iAmACallbackFunction2);

            }
        });
	});

	function checkPayment() {
		var checkedPayments = document.getElementsByName('checkedPayments[]');
		var selected = "";
		var checkedPaymentValue = [];
		var checkedPaymentIds= "";

		var totalPayment = 0;
		var totalGrandTotal = 0;
		var batchUpload = "";
		var batchUploadSort = "-";
		var paymentTypes = "";

		for (var i = 0; i < checkedPayments.length; i++) {
			
			if (checkedPayments[i].checked) {
				var checkedPaymentsValue = checkedPayments[i].value.split("|");
				var batchArray = batchUpload.split(",");
				var paymentTypeArray = paymentTypes.split(", ");

				if (selected == "") {
					selected = checkedPaymentsValue[0];
				} else {
					selected = selected + "," + checkedPaymentsValue[0];
				}
				totalPayment += 1;
				totalGrandTotal += parseFloat(checkedPaymentsValue[4]);

				if (checkedPaymentsValue[0] != "") {
					if (checkedPaymentIds == "") {
						checkedPaymentIds = checkedPaymentsValue[0];
					} else {
						checkedPaymentIds += "," + checkedPaymentsValue[0];
					}
				}
				
				if (checkedPaymentsValue[16] != "") {
					if (batchUpload == "") {
						batchUpload = checkedPaymentsValue[16];
					} else {
						if (batchArray.includes(checkedPaymentsValue[16]) == false) {
							batchUpload += "," + checkedPaymentsValue[16];
						}
					}
				}

				if (checkedPaymentsValue[17] != "") {
					if (paymentTypes == "") {
						paymentTypes = checkedPaymentsValue[17];
					} else {
						if (paymentTypeArray.includes(checkedPaymentsValue[17]) == false) {
							paymentTypes += ", " + checkedPaymentsValue[17];
						}
					}
				}

				checkedPaymentValue.push({	
					"payment_id" : checkedPaymentsValue[0], 
					"no_rek": checkedPaymentsValue[1], 
					"beneficiary": checkedPaymentsValue[2], 
					"stockpile_name": checkedPaymentsValue[3], 
					"grand_total": checkedPaymentsValue[4], 
					"kode1": checkedPaymentsValue[5], 
					"kode2": checkedPaymentsValue[6], 
					"kode3": checkedPaymentsValue[7], 
					"stockpile_code": checkedPaymentsValue[8], 
					"bank_name": checkedPaymentsValue[9], 
					"email": checkedPaymentsValue[10], 
					"email2": checkedPaymentsValue[11], 
					"email3": checkedPaymentsValue[12], 
					"status": checkedPaymentsValue[13], 
					"branch": checkedPaymentsValue[14], 
					"remarks": checkedPaymentsValue[15]
				});
				var batchUploadSort = batchUpload.length != 0 ? ((batchUpload.split(",")).sort(function(a, b){return a - b})).toString() : '-';
			}
		} 
		
		document.getElementsByName('paymentIds')[0].value = selected;
		document.getElementsByName('paymentIds')[1].value = selected;
		document.getElementById('batchUploadValues').value = JSON.stringify(checkedPaymentValue);
		document.getElementById('totalPayment').value = totalPayment;
		document.getElementById('totalGrandTotal').value = totalGrandTotal;
		document.getElementById('batchUpload').value = batchUploadSort;
		document.getElementById('paymentTypes').value = paymentTypes;
		document.getElementById('checkedPaymentIds').value = checkedPaymentIds;
		$('#totalGrandTotal').number(true, 3);
		// console.log(JSON.stringify(checkedPaymentValue));
	}

	function checkAll(a) {
        var checkedPayments = document.getElementsByName('checkedPayments[]');
		var selected = "";
		var checkedPaymentValue = [];
		var checkedPaymentIds= "";

		var totalPayment = 0;
		var totalGrandTotal = 0;
		var batchUpload = "";
		var batchUploadSort = "-";
		var paymentTypes = "";

        if (a.checked) {
            for (var i = 0; i < checkedPayments.length; i++) {
                if (checkedPayments[i].type == 'checkbox') {
                    checkedPayments[i].checked = true;

					var checkedPaymentsValue = checkedPayments[i].value.split("|");
					var batchArray = batchUpload.split(",");
					var paymentTypeArray = paymentTypes.split(", ");

					if (checkedPayments[i].checked) {
						if (selected == "") {
							selected = checkedPaymentsValue[0];
						} else {
							selected = selected + "," + checkedPaymentsValue[0];
						}
						totalPayment += 1;
						totalGrandTotal += parseFloat(checkedPaymentsValue[4]);

						if (checkedPaymentsValue[0] != "") {
							if (checkedPaymentIds == "") {
								checkedPaymentIds = checkedPaymentsValue[0];
							} else {
								checkedPaymentIds += "," + checkedPaymentsValue[0];
							}
						}
						
						if (checkedPaymentsValue[16] != "") {
							if (batchUpload == "") {
								batchUpload = checkedPaymentsValue[16];
							} else {
								if (batchArray.includes(checkedPaymentsValue[16]) == false) {
									batchUpload += "," + checkedPaymentsValue[16];
								}
							}
						}

						if (checkedPaymentsValue[17] != "") {
							if (paymentTypes == "") {
								paymentTypes = checkedPaymentsValue[17];
							} else {
								if (paymentTypeArray.includes(checkedPaymentsValue[17]) == false) {
									paymentTypes += ", " + checkedPaymentsValue[17];
								}
							}
						}

						checkedPaymentValue.push({	
							"payment_id" : checkedPaymentsValue[0], 
							"no_rek": checkedPaymentsValue[1], 
							"beneficiary": checkedPaymentsValue[2], 
							"stockpile_name": checkedPaymentsValue[3], 
							"grand_total": checkedPaymentsValue[4], 
							"kode1": checkedPaymentsValue[5], 
							"kode2": checkedPaymentsValue[6], 
							"kode3": checkedPaymentsValue[7], 
							"stockpile_code": checkedPaymentsValue[8], 
							"bank_name": checkedPaymentsValue[9], 
							"email": checkedPaymentsValue[10], 
							"email2": checkedPaymentsValue[11], 
							"email3": checkedPaymentsValue[12], 
							"status": checkedPaymentsValue[13], 
							"branch": checkedPaymentsValue[14], 
							"remarks": checkedPaymentsValue[15]
						});
						var batchUploadSort = batchUpload.length != 0 ? ((batchUpload.split(",")).sort(function(a, b){return a - b})).toString() : '-';
					}
                }
            }
        } else {
            for (var i = 0; i < checkedPayments.length; i++) {
                // console.log(i)
                if (checkedPayments[i].type == 'checkbox') {
                    checkedPayments[i].checked = false;
                }
            }
        }

		document.getElementsByName('paymentIds')[0].value = selected;
		document.getElementsByName('paymentIds')[1].value = selected;
		document.getElementById('batchUploadValues').value = JSON.stringify(checkedPaymentValue);
		document.getElementById('totalPayment').value = totalPayment;
		document.getElementById('totalGrandTotal').value = totalGrandTotal;
		document.getElementById('batchUpload').value = batchUploadSort;
		document.getElementById('paymentTypes').value = paymentTypes;
		document.getElementById('checkedPaymentIds').value = checkedPaymentIds;
		$('#totalGrandTotal').number(true, 3);
		// console.log(JSON.stringify(checkedPaymentValue));
    }

	function iAmACallbackFunction2() {
        $("#dataContent").fadeIn("slow");
    }


</script>
<form method="post" id="approvalSheetsForm">
    <!--<input type="hidden" id="stockpileId" name="stockpileId" value="<?php //echo $stockpileId; ?>" />-->
    <input type="hidden" id="periodFrom" name="periodFrom" value="<?php echo $periodFrom; ?>" />
    <input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>" />
	<input type="hidden" id="pTypes" name="pTypes" value="<?php echo $pTypes; ?>" />
	<input type="hidden" id="pLocations" name="pLocations" value="<?php echo $pLocations; ?>" />
	<input type="hidden" id="paymentNos" name="paymentNos" value="<?php echo $paymentNos; ?>" />
	<input type="hidden" id="bankIds" name="bankIds" value="<?php echo $bankIds; ?>" />
	<input type="hidden" id="paymentIds" name="paymentIds" value="<?php echo $paymentIds; ?>" />
	<table>
		<tr>
			<td style="width: 20%;">Total Select Count</td>
			<td style="width: 5%; text-align: center;">:</td>
			<td><input type="text" id="totalPayment" name="totalPayment" value="<?php echo $totalPayment; ?>" readonly style="width: 4em;" /></td>
		</tr>
		<tr>
			<td style="width: 20%;">Grand Total (Rp.)</td>
			<td style="width: 5%; text-align: center;">:</td>
			<td><input type="text" id="totalGrandTotal" name="totalGrandTotal" value="<?php echo $totalGrandTotal; ?>" readonly style="width: 12em;" /></td>
		</tr>
		<tr>
			<td style="width: 20%;">Batch Upload</td>
			<td style="width: 5%; text-align: center;">:</td>
			<td><input type="text" id="batchUpload" name="batchUpload" value="<?php echo $batchUpload; ?>" style="width: 12em;" /></td>
			<td><input type="hidden" id="paymentTypes" name="paymentTypes" value="<?php echo $paymentTypes; ?>" style="width: 12em;" /></td>
		</tr>
		<tr>
			<td style="width: 20%;">File Format</td>
			<td style="width: 5%; text-align: center;">:</td>
			<td class="controls">
                    <?php 
                   createCombo("SELECT 'Excel' AS id, 'Excel' AS format UNION
                                SELECT 'CSV' AS id, 'CSV' AS format UNION
                                SELECT 'PDF' AS id, 'PDF' AS format ", "", "", "fileFormat", "id", "format",
                                "", 2, "select2combobox100", "");
                    ?>
                </td>
		</tr>
	</table>
    <button id="downloadapproval" class="submit btn btn-success" data-action="reports/paymentToTax-xls.php">Download Approval Sheets</button>
	<button class="btn btn-info" id="printApprovalSheet">Print</button>
</form>

<form method="post" id="batchUploadForm">
	<input type="hidden" name="action" id="action" value="batch_upload_data" />
    <input type="hidden" id="periodFrom" name="periodFrom" value="<?php echo $periodFrom; ?>" />
    <input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>" />
	<input type="hidden" id="pTypes" name="pTypes" value="<?php echo $pTypes; ?>" />
	<input type="hidden" id="pLocations" name="pLocations" value="<?php echo $pLocations; ?>" />
	<input type="hidden" id="paymentNos" name="paymentNos" value="<?php echo $paymentNos; ?>" />
	<input type="hidden" id="bankIds" name="bankIds" value="<?php echo $bankIds; ?>" />
	<input type="hidden" id="paymentIds" name="paymentIds" value="<?php echo $paymentIds; ?>" />
	<input type="hidden" id="batchUploadValues" name="batchUploadValues" value="<?php echo $batchUploadValues; ?>" />
	<input type="hidden" id="checkedPaymentIds" name="checkedPaymentIds" value="<?php echo $checkedPaymentIds; ?>" />
	<table>
		<tr>
			<td style="width: 20%;">Batch Number</td>
			<td style="width: 5%; text-align: center;">:</td>
			<td><input type="text" id="batchNumber" name="batchNumber" value="<?php echo $batchNumber; ?>" style="width: 4em;" /></td>
		</tr>
	</table>
	<button id="downloadxls" class="submit btn btn-primary" data-action="reports/print_batch_upload.php">Create Batch Upload</button>
</form>

<div id = "approvalSheet">
<li class="active">Rincian Pembayaran PT Jatim Propertindo Jaya</li>
<li class="active">Periode : <?php echo $periodFrom; ?> - <?php echo $periodTo; ?></li>

<table class="table table-bordered table-striped" id="contentTable" style="font-size: 8pt;">

    <thead>
        <tr>
			<th style="text-align: center;"><input type="checkbox" onchange="checkAll(this)" /></th>
            <th>No.</th>
			<th>Batch Code</th>
			<th>Batch Number</th>
			<th>Cancel Batch</th>
			<th>Remarks</th>
            <th>Payment No</th>
			<th>Category</th>
			<th>Payment Type</th>
            <th>Approval</th>
            <th>No Rek/Cek</th>
            <th>Vendor</th>
            <th>Keterangan</th>
            <th>Bank</th>
            <th>Cabang Bank</th>
            <th>Nama Akun Bank</th>
            <th>Qty (MT/KG/HR/DW)</th>
            <th>Harga</th>
            <th>DPP</th>
            <th>PPN</th>
            <th>PPh</th>
            <th>Total Rincian</th>
            <th>DP</th>
			<th>Type</th>
            <th>Grand Total</th>
            <th>Stockpile</th>
			<th>Entry By</th>
            <th>PIC Finance</th>
            <th>PPN</th>
            <th>PPh</th>
            <th>kode1</th>
            <th>kode2</th>
			<th>Entry Date</th>
        </tr>
    </thead>
    <tbody>
        <?php
		if ($result->num_rows > 0) {
			//echo 'test';
            $no = 1;
            while ($row = mysqli_fetch_array($result)) {
				// Kode remark
				$remarks = "";
				$kode3 = "";

				if ($row['batch_remarks'] != '') {
					$remarks = $row['batch_remarks'];
				} else {
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
				}
				$kode3 = str_replace(" ", "", $remarks);
				// End Kode Remark

				if($row['payment_no'] != '') {
                    $voucherCode = $row['payment_location'] .'/'. $row['bank_code'] .'/'. $row['pcur_currency_code'];

                    if($row['bank_type'] == 1) {
                        $voucherCode .= ' - B';
                    } elseif($row['bank_type'] == 2) {
                        $voucherCode .= ' - P';
                    } elseif($row['bank_type'] == 3) {
                        $voucherCode .= ' - CAS';
                    }

                    if($row['bank_type'] != 3) {
                        if($row['payment_type'] == 1) {
                            $voucherCode .= 'RV';
                        } else {
                            $voucherCode .= 'PV';
                        }
                    }
                  }


					if($row['payment_type'] == 1) {

						$grand_total = $row['grand_total'] * -1;
					}else{
						$grand_total = $row['grand_total'];
					}
					
					$grandTotal = $row['dpp'] + $row['ppn_amount'] - $row['pph_amount'] - $row['dp'];
					// echo $row['dp'];
					//$gt[] = $row['total'] ;
				   	//$gTotal = implode(', ', $gt);
				    //$grandTotal = array_sum($gt) - $row['dp'];
				    //$sum += $row['total'];
					//echo $sum;
					//echo array_sum($gt);


			//echo $totals;
		?>
        <tr>

			<?php
              	// if($row['payment_id'] == $lastPaymentId) {
				// $counter++;
			?>


			<?php
			/*	}else{

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
                    $counter = 1;
					$no++;
					//echo  $totalRow;*/

			?>
			<td style="width: 1%; text-align: center;">
				<input type="checkbox" name="checkedPayments[]" id="fc" value="<?php echo $row['payment_id'] . "|" . $row['no_rek'] . "|" . $row['beneficiary'] . "|" . $row['stockpile_name'] . "|" . $grand_total . "|" . $row['kode1'] . "|" . $row['kode2'] . "|" . $kode3 . "|" . $row['stockpile_code'] . "|" . $row['bank_name'] . "|finance@jatimpropertindo.com|" . $row['email2'] . "|" . $row['email3'] . "|1|" . $row['branch'] . "|" . $remarks . "|" . $row['batch_number'] . "|" . $row['p_type']  ?>" onclick="checkPayment();" <?php if(in_array($row['payment_id'], $checkedPaymentIds)) echo "checked='checked'";  ?> />
			</td>
            <td style="text-align: center;"><?php echo $no; ?></td>
			<td ><?php echo $row['batch_code']; ?></td>
			<td ><?php echo $row['batch_number']; ?></td>
			<td style="text-align: center;">
				<?php if ($row['batch_upload_detail_id'] != '') { ?>
				<a href="#" id="cancelBatchUpload|batch_upload|<?php echo $row['batch_upload_detail_id']; ?>" role="button" title="Cancel Batch Upload">
					<img src="assets/ico/gnome-trash.png" width="18px" height="18px" style="margin-bottom: 5px;"/>
				</a>
				<?php } ?>
			</td>
			<td ><?php echo $remarks; ?></td>
            <td ><?php echo $voucherCode; ?> # <?php echo $row['payment_no']; ?></td>
			<td ><?php echo $row['kode_3']; ?></td>
			<td ><?php echo $row['p_type']; ?></td>
            <td ></td>
            <td ><?php echo $row['no_rek']; ?></td>
            <td ><?php echo $row['vendor_name']; ?></td>
				<?php //}	?>
            <td><?php echo $row['keterangan']; ?></td>

			<?php
                /*if($row['payment_id'] == $lastPaymentId) {
                    $counter++;*/
                ?>


			<?php
				/*}else{
					//$totalRow = $rowCount->total_row;
                    $counter = 1;
					//$no++;*/
			?>
            <td ><?php echo $row['bank_name']; ?></td>
            <td ><?php echo $row['branch']; ?></td>
            <td ><?php echo $row['beneficiary']; ?></td>
			<?php //}	?>
            <td style="text-align: right;"><?php echo number_format($row['quantity'], 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row['price_converted'], 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row['dpp'], 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row['ppn_amount'], 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row['pph_amount'], 0, ".", ","); ?></td>
			<td style="text-align: right;"><?php echo number_format($row['total'], 0, ".", ","); ?></td>
			<?php
               /* if($row['payment_id'] == $lastPaymentId) {
                    $counter++;*/
                ?>


			<?php
				/*}else{
					//$totalRow = $rowCount->total_row;
                    $counter = 1;
				//}
					/// $lastPaymentNo = $row['payment_no'];





					//$no++;*/
			?>
			<td  style="text-align: right;"><?php echo number_format($row['dp'], 0, ".", ","); ?></td>
			<td ><?php echo $row['paymentType']; ?></td>
            <td  style="text-align: right;"><?php echo number_format($grandTotal, 0, ".", ","); ?></td>
            <td ><?php echo $row['stockpile_name']; ?></td>
			<td ><?php echo $row['entry_by']; ?></td>
            <td ><?php echo $row['user_name']; ?></td>
            <td ><?php echo $row['ppn']; ?></td>
      			<td ><?php echo $row['pph']; ?></td>
            <td ><?php echo $row['kode1']; ?></td>
            <td ><?php echo $row['kode2']; ?></td>
			
			<td ><?php echo $row['entry_date']; ?></td>
            <?php //}	?>
        </tr>
                <?php
				$no++;
                //$lastPaymentId = $row['payment_id'];
				//$lastPaymentNo2 = $row->payment_no;
				//$dp = $row->dp;
				$AllgrandTotal = $AllgrandTotal + $grand_total;
            }

			//echo $AllgrandTotal;
        } else {
			//echo $sql;
		}
        ?>
		<tr>
		<td colspan="22" style="text-align: right;">TOTAL</td>
		<td ><?php echo number_format($AllgrandTotal, 0, ".", ","); ?></td>
		<td colspan="8"></td>

		</tr>
    </tbody>
</table>
</div>

<div id="cancelModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="cancelModalLabel"
     aria-hidden="true">

    <div class="modal-header">
        <!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeDetailModal">×</button>-->
        <h3 id="cancelModalLabel">Cancel Batch Upload <label id="approveDesc"/></h3>
    </div>
    <div class="alert fade in alert-error" id="modalErrorMsg4" style="display:none;">
        Error Message
    </div>
    <div class="modal-body" id="cancelModalForm" style="max-height: 450px;">
    </div>
    <div class="modal-footer">
        <!--<button class="btn" data-dismiss="modal" aria-hidden="true" id="closeDetailModal">Close</button>-->
    </div>

</div>
