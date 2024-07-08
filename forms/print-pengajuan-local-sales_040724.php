<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$allowReturnInvoice = false;

$sql = "SELECT * FROM user_module WHERE user_id = {$_SESSION['userId']}";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

if($result->num_rows > 0) {
    while($row = $result->fetch_object()) {
        if($row->module_id == 49) {
            $allowReturnInvoice = true;
        }
    }
}


$idPP = $myDatabase->real_escape_string($_POST['idpp']);
$totalPrice = '';
$sql = "SELECT pp.invoice_no, pp.invoice_date, pp.tax_invoice, sp.stockpile_name AS payment_location,
CASE WHEN pp.payment_method = '1' THEN 'Payment' 
	WHEN pp.payment_method = '2' THEN 'Down Payment' ELSE NULL END AS Payment, 
CASE WHEN pp.payment_type = 2 THEN 'OUT' ELSE 'IN' END AS tipe, 
CASE WHEN pp.payment_for = 0 THEN 'PKS Kontrak' 
	WHEN pp.payment_for = 1 THEN 'Local Sales' 
	WHEN pp.payment_for = 2 THEN 'Freight Cost' 
	WHEN pp.payment_for = 9 THEN 'Handling Cost' 
	WHEN pp.payment_for = 3 THEN 'Unloading Cost' ELSE NULL END AS payment_For,
pp.periodeFrom, pp.periodeTo, sl.stockpile_name AS stockpile_location, 
CASE WHEN pp.freight_id IS NOT NULL THEN fr.freight_supplier
WHEN pp.labor_id IS NOT NULL THEN lb.labor_name
WHEN pp.vendor_handling_id IS NOT NULL THEN hb.handling_name
ELSE '' END AS vendorName,
CASE WHEN pp.freight_id IS NOT NULL THEN fb.beneficiary
WHEN pp.labor_id IS NOT NULL THEN lbs.beneficiary
WHEN pp.vendor_handling_id IS NOT NULL THEN hbs.beneficiary
ELSE '' END AS beneficiary,
CASE WHEN pp.freight_id IS NOT NULL THEN fb.bank_name
WHEN pp.labor_id IS NOT NULL THEN lbs.bank_name
WHEN pp.vendor_handling_id IS NOT NULL THEN hbs.bank_name
ELSE '' END AS bank_name,
CASE WHEN pp.freight_id IS NOT NULL THEN fb.account_no
WHEN pp.labor_id IS NOT NULL THEN lbs.account_no
WHEN pp.vendor_handling_id IS NOT NULL THEN hbs.account_no
ELSE '' END AS account_no,
CASE WHEN pp.urgent_payment_type = 1 THEN 'URGENT' ELSE 'NORMAL' END AS urgentType, pp.remarks, pp.file          
FROM pengajuan_payment_sales_oa pp
LEFT JOIN stockpile sp ON sp.stockpile_id = pp.stockpile_id
LEFT JOIN stockpile sl ON sl.stockpile_id = pp.stockpile_location
LEFT JOIN freight_local_sales fr ON fr.freight_id = pp.freight_id
LEFT JOIN freight_local_sales_bank fb ON fb.f_bank_id = pp.vendor_bank_id
LEFT JOIN labor_local_sales lb ON lb.labor_id = pp.labor_id
LEFT JOIN labor_local_sales_bank lbs ON lbs.l_bank_id = pp.vendor_bank_id
LEFT JOIN handling_local_sales hb ON hb.handling_id = pp.vendor_handling_id
LEFT JOIN handling_local_sales_bank hbs ON hbs.h_bank_id = pp.vendor_bank_id 
WHERE pp.idPP = {$idPP}";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

if($result !== false && $result->num_rows > 0) {
    $row = $result->fetch_object();
     /*$paymentStatus = $row->payment_status;
	 $invoiceStatus = $row->invoice_status;
	 $mutasi_status = $row->mutasi_status;
	 $edit_date = $row->edit_date;
	 $invoice_date2 = $row->invoice_date2;*/
    // <editor-fold defaultstate="collapsed" desc="Last Transaction & Print Container">
?>

<script type="text/javascript">
    
    $(document).ready(function(){	//executed after the page has loaded
        $('#printInvoiceDetail').click(function(e){
            e.preventDefault();
            
            //$("#transactionContainer").show();
            // https://github.com/jasonday/printThis
            $("#invoiceDetail").printThis();
//            $("#transactionContainer").hide();
        });
     /*$('#returnInvoice').click(function(e){
            $.ajax({
                url: './data_processing.php',
                method: 'POST',
                data: 'action=return_invoice&invoiceId=<?php echo $row->invoice_id; ?>',
                success: function(data) {
                    var returnVal = data.split('|');

                    if (parseInt(returnVal[4]) != 0)	//if no errors
                    {
                        alertify.set({ labels: {
                            ok     : "OK"
                        } });
                        alertify.alert(returnVal[2]);

                        if (returnVal[1] == 'OK') {
                            $('#dataContent').load('forms/print-invoice.php', {invoiceId: <?php echo $row->invoice_id; ?>}, iAmACallbackFunction2);

                        } 
                    }
                }
            });
        });*/
		
		/*$("#returnInvoice").validate({
			rules: {
                returnInvoiceDate: "required"
            },
            messages: {
                returnInvoiceDate: "Return Date is a required field."
            },
			submitHandler: function(form) {
				$('#returnButton').attr("disabled", true);
			alertify.set({ labels: {
                    ok     : "Yes",
                    cancel : "No"
                } });
                alertify.confirm("Are you sure want to RETURN this invoice?", function(form) {
                    if (form) {
            
			$.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#returnInvoice").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);
                            
                            if (returnVal[1] == 'OK') {
                                document.getElementById('invoiceId').value = returnVal[3];
                                
                                $('#dataContent').load('forms/print-invoice.php', { invoiceId: returnVal[3] }, iAmACallbackFunction2);

//                                document.getElementById('successMsg').innerHTML = returnVal[2];
//                                $("#successMsg").show();
                            } 
							$('#returnButton').attr("disabled", false);
                        }
                    }
                });
			}
                    return false;
		});
		}
        });
		
		$('#jurnalInvoice').click(function(e){
            $.ajax({
                url: './data_processing.php',
                method: 'POST',
                data: 'action=jurnal_invoice&invoiceId=<?php echo $row->invoice_id; ?>',
                success: function(data) {
                    var returnVal = data.split('|');

                    if (parseInt(returnVal[4]) != 0)	//if no errors
                    {
                        alertify.set({ labels: {
                            ok     : "OK"
                        } });
                        alertify.alert(returnVal[2]);

                        if (returnVal[1] == 'OK') {
                           // $('#dataContent').load('contents/invoice.php', {}, iAmACallbackFunction2);

                        } 
                    }
                }
            });
        });
    
	$('#jurnalReturn').click(function(e){
            $.ajax({
                url: './data_processing.php',
                method: 'POST',
                data: 'action=jurnal_invoice_return&invoiceId=<?php echo $row->invoice_id; ?>',
                success: function(data) {
                    var returnVal = data.split('|');

                    if (parseInt(returnVal[4]) != 0)	//if no errors
                    {
                        alertify.set({ labels: {
                            ok     : "OK"
                        } });
                        alertify.alert(returnVal[2]);

                        if (returnVal[1] == 'OK') {
                           // $('#dataContent').load('contents/invoice.php', {}, iAmACallbackFunction2);

                        } 
                    }
                }
            });
        });
		
	$('#jurnalAccrue').click(function(e){
            $.ajax({
                url: './data_processing.php',
                method: 'POST',
                data: 'action=jurnal_accrue',
                success: function(data) {
                    var returnVal = data.split('|');

                    if (parseInt(returnVal[4]) != 0)	//if no errors
                    {
                        alertify.set({ labels: {
                            ok     : "OK"
                        } });
                        alertify.alert(returnVal[2]);

                        if (returnVal[1] == 'OK') {
                           // $('#dataContent').load('contents/invoice.php', {}, iAmACallbackFunction2);

                        } 
                    }
                }
            });
        });*/
    });
    
    
    
    
    function back() {
        $.blockUI({ message: '<h4>Please wait...</h4>' }); 
        $('#pageContent').load('views/pengajuan-payment-sales.php', {}, iAmACallbackFunction);
    }
	
	$(function() {
        //https://github.com/eternicode/bootstrap-datepicker
        $('.datepicker').datepicker({
            minViewMode: 0,
            todayHighlight: true,
            autoclose: true,
            startView: 0
        });
    });
</script>


<div id="invoiceDetail">
   
    <table width="100%" style="table-layout:fixed; font-size: 9pt;">
        <tr>
            <td width="24%"><b>Invoice No.</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->invoice_no; ?></td>
            <td width="24%"><b>Stockpile Location</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->stockpile_location; ?></td>
            
        </tr>
        <tr>
            <td width="24%"><b>Invoice Date</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->invoice_date; ?></td>
            <td width="24%"><b>Vendor Name</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->vendorName; ?></td>
        </tr>
        <tr>
            <td width="24%"><b>Tax Invoice No.</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->tax_invoice; ?></td>
            <td width="24%"><b>Bank Account</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->account_no; ?></td>
        </tr>
        <tr>
          
			<td width="24%"><b>Payment Location</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->payment_location; ?></td>
            <td width="24%"><b>Bank Name</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->bank_name; ?></td>
        </tr>
        <tr>
              <td width="24%"><b>Period From</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->periodeFrom; ?></td>
            <td width="24%"><b>Beneficiary</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->beneficiary; ?></td>
        </tr>
		 <tr>
              <td width="24%"><b>Period To</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->periodeTo; ?></td>
            <td width="24%"><b>Documents</b></td>
            <td width="2%">:</td>
            <td width="24%"><a href="<?php echo $row->file ?>" target="_blank">View Documents<img src="assets/ico/file.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a></td>
        </tr>
		 
		
		
		
    </table>
    
    <!--<br/>-->
    <table width="100%" class="table table-bordered table-striped" style="font-size: 9pt;">
        <thead>
            <tr>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td <?php if($row->remarks == '') echo 'style="height: 40px;"'; ?>><?php echo $row->remarks; ?></td>
            </tr>
        </tbody>
    </table>
    <!--<br/>-->
    <?php
	$sql = "SELECT t.*, sc.`shipment_no`, f.`freight_rule`, l.`labor_rule`, h.handling_rule, pps.payment_for,
    CASE WHEN t.freight_cost_id IS NOT NULL THEN (SELECT customer_name FROM customer WHERE customer_id = fc.vendor_id)
    WHEN t.unloading_cost_id IS NOT NULL THEN (SELECT customer_name FROM customer WHERE customer_id = lc.vendor_id)
    WHEN t.handling_cost_id IS NOT NULL THEN (SELECT customer_name FROM customer WHERE customer_id = hc.vendor_id)
    ELSE '' END AS customerName,
    (SELECT tax_value FROM tax WHERE tax_id = f.ppn_tax_id) AS fppn,
    (SELECT tax_value FROM tax WHERE tax_id = f.pph_tax_id) AS fpph,
    (SELECT tax_category FROM tax WHERE tax_id = f.ppn_tax_id) AS fppnCat,
    (SELECT tax_category FROM tax WHERE tax_id = f.pph_tax_id) AS fpphCat,
    (SELECT tax_value FROM tax WHERE tax_id = l.ppn_tax_id) AS lppn,
    (SELECT tax_value FROM tax WHERE tax_id = l.pph_tax_id) AS lpph,
    (SELECT tax_category FROM tax WHERE tax_id = l.ppn_tax_id) AS lppnCat,
    (SELECT tax_category FROM tax WHERE tax_id = l.pph_tax_id) AS lpphCat,
    (SELECT tax_value FROM tax WHERE tax_id = h.ppn_tax_id) AS hppn,
    (SELECT tax_value FROM tax WHERE tax_id = h.pph_tax_id) AS hpph,
    (SELECT tax_category FROM tax WHERE tax_id = h.ppn_tax_id) AS hppnCat,
    (SELECT tax_category FROM tax WHERE tax_id = h.pph_tax_id) AS hpphCat,
    pps.claimPayment, pps.claimPaymentRemarks, pps.total_ppn_amount, pps.total_pph_amount, pps.grand_total,
    COALESCE(hsw.amt_claim) as hsw_amt_claim, COALESCE(ts.amt_claim,0) AS amt_claim
    FROM `transaction` t
    LEFT JOIN shipment sc ON sc.shipment_id = t.shipment_id
    LEFT JOIN pengajuan_sales_oa ps ON ps.`transaction_id` = t.`transaction_id` 
    LEFT JOIN pengajuan_payment_sales_oa pps ON pps.idpp = ps.idpp
    LEFT JOIN freight_cost_local_sales fc ON fc.freight_cost_id = t.freight_cost_id
    LEFT JOIN freight_local_sales f ON f.freight_id = fc.freight_id
    LEFT JOIN labor_cost_local_sales lc ON lc.labor_cost_id = t.unloading_cost_id
    LEFT JOIN labor_local_sales l ON l.labor_id = lc.labor_id
    LEFT JOIN handling_cost_local_sales hc ON hc.handling_cost_id = t.handling_cost_id
    LEFT JOIN handling_local_sales h ON h.handling_id = hc.handling_id
    LEFT JOIN transaction_shrink_weight_ls ts ON t.transaction_id = ts.transaction_id
	LEFT JOIN transaction_additional_shrink_ls hsw ON t.transaction_id = hsw.transaction_id
    WHERE ps.`idPP` = {$idPP}
    ORDER BY t.transaction_date ASC";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

$total = 0;
$dppTotal = 0;
$ppnTotal = 0;
$pphTotal = 0;
$grandTotal = 0;
$totalShrink = 0;
	?>
    <table width="100%" class="table table-bordered table-striped" style="font-size: 9pt;">
        <thead>
            <tr>
                
                <th>Slip No</th>
                <th>Transaction Date</th>
                <th>Shipment Code</th>
                <th>Vendor Name</th>
                <th>Vehicle No</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
         <tr>
        <?php
		if($result !== false && $result->num_rows > 0) {
		 while($row = $result->fetch_object()) {
        
        if($row->payment_for == 2){ // FREIGHT

            $dpp = $row->freight_price * $row->freight_quantity;
			$qty = $row->freight_quantity;
            $price = $row->freight_price;
            $ppn = ($dpp) * ($row->fppn / 100);

            if($row->fpphCat == 1) {
                $dppAmount = ($dpp) / ((100 - $row->fpph) / 100);
                $pph =  $dppAmount - $dpp;
                //$dppShrinkPrice = ($row->qtyClaim * $row->trx_shrink_claim) / ((100 - $row->pph_tax_value) / 100);
                $dppShrinkPrice = ($row->amt_claim) / ((100 - $row->pph_tax_value) / 100);
				$hsw_amt_claim = ($row->hsw_amt_claim) / ((100 - $row->pph_tax_value) / 100);
            }else{
                $dppAmount = $dpp;
                $pph = ($dpp) * ($row->fpph / 100);
                $dppShrinkPrice = $row->amt_claim;
				$hsw_amt_claim = $row->hsw_amt_claim;

            }

        } else if($row->payment_for == 3){ // UNLOADING
            $dpp = $row->unloading_price * $row->quantity;
			$qty = $row->quantity;
            $price = $row->unloading_price;
            $ppn = ($dpp) * ($row->lppn / 100);

            if($row->fpphCat == 1) {
                $dppAmount = ($dpp) / ((100 - $row->lpph) / 100);
                $pph =  $dppAmount - $dpp;
                //$dppShrinkPrice = ($row->qtyClaim * $row->trx_shrink_claim) / ((100 - $row->pph_tax_value) / 100);
            }else{
                $dppAmount = $dpp;
                $pph = ($dpp) * ($row->lpph / 100);

            }

        } else if($row->payment_for == 9){ // UNLOADING
            $dpp = $row->handling_price * $row->quantity;
			$qty = $row->quantity;
            $price = $row->handling_price;
            $ppn = ($dpp) * ($row->hppn / 100);

            if($row->hpphCat == 1) {
                $dppAmount = ($dpp) / ((100 - $row->hpph) / 100);
                $pph =  $dppAmount - $dpp;
                //$dppShrinkPrice = ($row->qtyClaim * $row->trx_shrink_claim) / ((100 - $row->pph_tax_value) / 100);
            }else{
                $dppAmount = $dpp;
                $pph = ($dpp) * ($row->hpph / 100);

            }

        }

            
            $total = $dppAmount + $ppn - $pph;
            $dppTotal =  $dppTotal + $dppAmount;
            $pphTotal = $row->total_ppn_amount;
            $ppnTotal = $row->total_pph_amount;
            $grandTotal = $row->grand_total;
            $totalShrink = $totalShrink + $dppShrinkPrice + $hsw_amt_claim;

            $claimPaymentRemarks = $row->claimPaymentRemarks;
            $claimPayment = $row->claimPayment;
           
        
	 ?>
           
               
                <td><?php echo $row->slip_no;?></td>
                <td><?php echo $row->transaction_date;?></td>
                <td><?php echo $row->shipment_no;?></td>
                <td><?php echo $row->customerName;?></td>
                <td><?php echo $row->vehicle_no;?></td>
                <td style="text-align: right;"><?php echo number_format($qty, 2, ".", ",");?></td>
                <td style="text-align: right;"><?php echo number_format($price, 3, ".", ",");?></td>
                <td style="text-align: right;"><?php echo number_format($dpp, 2, ".", ",");?></td>
                
            
            </tr>
          <?php
		}
}
?>
        </tbody>
        <tfoot>
        <tr>
        <td colspan="7" style="text-align: right;">Total Amount</td>
        <td style="text-align: right;"><?php echo number_format($dppTotal, 2, ".", ",")?></td>
       
        </tr>
        <tr>
        <td colspan="7" style="text-align: right;">Total Shrink</td>
        <td style="text-align: right;"><?php echo number_format($totalShrink, 2, ".", ",")?></td>
       
        </tr>
        <tr>
        <td colspan="7" style="text-align: right;">PPN</td>
        <td style="text-align: right;"><?php echo number_format($ppnTotal, 2, ".", ",")?></td>
       
        </tr>
        <tr>
        <td colspan="7" style="text-align: right;">PPh</td>
        <td style="text-align: right;"><?php echo number_format($pphTotal, 2, ".", ",")?></td>
       
        </tr>
        <tr>
        <td colspan="7" style="text-align: right;">Grand Total</td>
        <td style="text-align: right;"><?php echo number_format($grandTotal, 2, ".", ",")?></td>
       
        </tr>
        </tfoot>
    </table>
    
</div>

<hr>

<div class="row-fluid">
    <div class="span12 lightblue">
        <button class="btn btn-primary" id="printInvoiceDetail">Print</button>
        <button class="btn" type="button" onclick="back()">Back</button>
		  
  </div>
</div>

<?php
    // </editor-fold>
}
?>
