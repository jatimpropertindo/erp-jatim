<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$no_po = $_POST['POId'];
$totalPrice = '';
$general_vendor_name='';
$account_no = '';
$bank_name = '';
$branch= '' ;
$swift_code = '';
$totalpph = '';
$totalppn = '';
$totalall = '';
$beneficiary = '';
$sql = "SELECT
ph.no_po, gv.general_vendor_name, s.stockpile_name, DATE_FORMAT(ph.tanggal, '%d %b %Y') AS tanggal,ph.no_penawaran,
gvb.bank_name,gvb.branch,gvb.account_no,gvb.swift_code , ph.memo, ph.grandtotal,
ph.idpo_hdr, ph.status, ph.totalppn, ph.totalpph, ph.totalall, si.name , u.user_name,gv.general_vendor_address,ph.toc,gvb.beneficiary,
cur.currency_code, CASE WHEN ph.exchangerate > 0 THEN ph.exchangerate ELSE 1 END AS exchangeRate,
CASE WHEN cur.currency_id = 1 THEN 'Rp. '
	WHEN cur.currency_id = 2 THEN '$ '
	WHEN cur.currency_id = 3 THEN 'S$ '
	WHEN cur.currency_id = 4 THEN 'RMB '
	ELSE 'MYR ' END as symbolCurr
FROM po_hdr ph
LEFT JOIN general_vendor gv ON gv.general_vendor_id = ph.general_vendor_id
LEFT JOIN stockpile s ON s.stockpile_id = ph.stockpile_id
LEFT JOIN USER u ON u.user_id = ph.entry_by
LEFT JOIN master_sign si ON si.idmaster_sign = ph.sign_id
LEFT JOIN currency cur ON cur.currency_id = ph.currency_id
LEFT JOIN general_vendor_bank gvb ON gvb.`gv_bank_id`= ph.`bank_id` where ph.no_po='{$no_po}' ORDER BY gvb.`master_bank_id` desc limit 1";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

if($result !== false && $result->num_rows > 0) {
    	$row = $result->fetch_object();
     	$general_vendor_name = $row->general_vendor_name;
		$account_no = $row->account_no;
		$bank_name = $row->bank_name;
		$branch= $row->branch;
		$swift_code = $row->swift_code;
		$prepareby = $row->user_name;
		$checkby = $row->name;
		$toc=$row->toc;
		$status=$row->status;
		$no_po=$row->no_po;
		$beneficiary=$row->beneficiary;
		$currency=$row->currency_code;
		$exchangeRate =$row->exchangeRate;
		$symbolCurr = $row->symbolCurr;
?>

<script type="text/javascript">

    $(document).ready(function(){	//executed after the page has loaded
        $('#printPODetail').click(function(e){
            e.preventDefault();

            //$("#transactionContainer").show();
            // https://github.com/jasonday/printThis
            $("#PODetail").printThis();
//            $("#transactionContainer").hide();
        });
		/*
     $('#returnInvoice').click(function(e){
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
		*/
		$('#reject').click(function(e){
            $.ajax({
                url: './data_processing.php',
                method: 'POST',
                data: 'action=PO_reject&poId=<?php echo $row->idpo_hdr; ?>',
                success: function(data) {
                    var returnVal = data.split('|');

                    if (parseInt(returnVal[4]) != 0)	//if no errors
                    {
                        alertify.set({ labels: {
                            ok     : "OK"
                        } });
                        alertify.alert(returnVal[2]);

                        if (returnVal[1] == 'OK') {
                           // $('#dataContent').load('contents/po.php', {}, iAmACallbackFunction2);

                        }
                    }

                }
            });
        });

    });




    function back() {
        $.blockUI({ message: '<h4>Please wait...</h4>' });
        $('#pageContent').load('views/PO.php', {}, iAmACallbackFunction);
    }
</script>


<div id="PODetail">

   <table width="100%" class="table table-bordered table-striped">
	   <tr>

	   <td width = "100%" height="51"><b><font = size ="5"> PT.JATIM PROPERTINDO JAYA</font></b>
		</td>
     </tr>
	   <tr>
	    <td width = "100%"><b><font = size ="4"><div align = "center">Purchase Order # <?php echo $no_po;?></div></font></b>
		</td>
     </tr>
	   <tr>
	    <td width = "100%"><b><font = size ="4"><div align = "center">
			<?php
			if($status==3){
				echo "REJECTED";
				//echo $status;
			}
			elseif($status==4){
				echo "CANCELLED";
				//echo $status;
			}?></div></font></b>
		</td>
     </tr>
  </table>
  </br>
    <table width="100%" style="table-layout:fixed; font-size: 9pt;">
		<tr>
            <td width="10%"><b>Vendor Name</b></td>
            <td width="2%">:</td>
            <td width="38%"><?php echo $row->general_vendor_name; ?></td>
            <td width="10%"><b>Ref No.</b></td>
            <td width="2%">:</td>
            <td width="38%"><?php echo $row->no_penawaran; ?></td>
        </tr>
		<tr>
            <td width="10%"></td>
            <td width="2%"></td>
            <td width="38%"></td>
            <td width="10%"><b>Currency</b></td>
            <td width="2%">:</td>
            <td width="38%"><?php echo $currency; ?></td>
        </tr>
		<tr>
            <td width="10%"><b></b></td>
            <td width="2%"></td>
            <td width="38%"></td>
            <td width="10%"><b>Exchange Rate</b></td>
            <td width="2%">:</td>
            <td width="38%"> <?php echo 'Rp. '. number_format($exchangeRate, 2, ".", ",");?></td>
        </tr>
        <tr>
            <td width="10%"><b>Address</b></td>
            <td width="2%">:</td>
            <td width="38%" rowspan="3"><?php echo $row->general_vendor_address; ?></td>
            <td width="10%"><b>PO Date</b></td>
            <td width="2%">:</td>
            <td width="38%"><?php echo $row->tanggal; ?></td>
        </tr>
		



    </table>
    </br>
    <!--<br/>-->
    <table width="100%" class="table table-bordered table-striped" style="font-size: 9pt;">
        <thead>
            <tr>
                <th align="left">Remarks</th>
          </tr>
        </thead>
        <tbody>
            <tr>
                <td <?php if($row->memo == '') echo 'style="height: 40px;"'; ?>><?php echo $row->memo; ?></td>
            </tr>
        </tbody>
    </table>
    <!--<br/>-->
    <?php
	$sql = "select CONCAT(FORMAT(qty,2),' ',u.uom_type) AS qty, harga, keterangan, amount, i.item_name,
			(case when pd.pphstatus = 1 then pd.pph else 0 end) as pph,
			(case when pd.ppnstatus = 1 then pd.ppn else 0 end) as ppn,
    		(pd.amount+(case when pd.ppnstatus = 1 then pd.ppn else 0 end)-(case when pd.pphstatus = 1 then pd.pph else 0 end)) as grandtotal,
            u.uom_type,s.`stockpile_name`, sh.`shipment_no`,
			CASE WHEN cur.currency_id = 1 THEN 'Rp. '
				WHEN cur.currency_id = 2 THEN '$ '
				WHEN cur.currency_id = 3 THEN 'S$ '
				WHEN cur.currency_id = 4 THEN 'RMB '
				ELSE 'MYR ' END as symbolCurr
			from po_detail pd
			LEFT JOIN po_hdr ph ON ph.no_po = pd.no_po
			left join master_item i on i.idmaster_item = pd.item_id
            left join uom u on u.idUOM = i.uom_id
			LEFT JOIN stockpile s ON s.`stockpile_id` = pd.`stockpile_id`
			LEFT JOIN currency cur ON cur.currency_id = ph.currency_id
            LEFT JOIN shipment sh ON sh.`shipment_id` = pd.`shipment_id`
			WHERE pd.no_po = '{$no_po}' ORDER BY idpo_detail ASC";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);



	?>
    <table width="100%" class="table table-bordered table-striped" style="font-size: 8pt;">
        <thead>
            <tr>
                <th>Shipment Code</th>
				<th>Stockpile</th>
				<th>Qty</th>
                <th>Price</th>
                <th>Description</th>
                <th>Amount</th>
				<th>VAT</th>
				<th>WHT</th>
				<th>Total Amount</th>

            </tr>
        </thead>
        <tbody>
         <tr>
        <?php
		if($result !== false && $result->num_rows > 0) {
		 while($row = $result->fetch_object()) {

		$amount = $row->amount;
		$totalPrice = $totalPrice + $amount;
		$tpph = $row->pph;
		$tppn = $row->ppn;
		$tgtotal = $row->grandtotal;
		$totalpph = $totalpph + $tpph;
		$totalppn = $totalppn + $tppn;
		$totalall = $totalall + $tgtotal;
		$symbolCurr = $row->symbolCurr;


	 ?>
				<td><?php echo $row->shipment_no; ?></td>
                <td><?php echo $row->stockpile_name; ?></td>
                <td><?php echo $row->qty; ?></td>
                <td><?php echo $symbolCurr.' '.number_format($row->harga, 2, ".", ",");?></td>
                <td><?php echo $row->item_name;?></td>
                <td style="text-align: right;"><?php echo $symbolCurr.' '.number_format($row->amount, 2, ".", ",");?></td>
                <td style="text-align: right;"><?php echo $symbolCurr.' '.number_format($row->ppn, 2, ".", ",");?></td>
			 	<td style="text-align: right;"><?php echo $symbolCurr.' '.number_format($row->pph, 2, ".", ",");?></td>
			 	<td style="text-align: right;"><?php echo $symbolCurr.' '.number_format($row->grandtotal, 2, ".", ",");?></td>

          </tr>
          <?php
		}
}
?>
        </tbody>
        <tfoot>

			<tr>
        <td colspan="5" style="text-align: right;"> Grand Total</td>
        <td colspan="1" style="text-align: right;"><?php echo $symbolCurr.' '.number_format($totalPrice, 2, ".", ",");?></td>
       <td colspan="1" style="text-align: right;"><?php echo $symbolCurr.' '.number_format($totalppn, 2, ".", ",");?></td>
		<td colspan="1" style="text-align: right;"><?php echo $symbolCurr.' '.number_format($totalpph, 2, ".", ",");?></td>
		<td colspan="1" style="text-align: right;"><?php echo $symbolCurr.' '.number_format($totalall, 2, ".", ",");?></td>
        </tr>

			<tr>


        </tr>
        </tfoot>
    </table>
	<table width="100%" class="table table-bordered table-striped" style="font-size: 9pt;">
        <thead>
            <tr>
                <th align="left">Terms & Conditions</th>
          </tr>
        </thead>
        <tbody>
            <tr>
              <td <?php if($toc == '') echo 'style="height: 40px;"'; ?>><?php echo nl2br(htmlspecialchars($toc));?></td>
            </tr>
        </tbody>
    </table>
    <table width="100%" >
      <tbody>
        <tr>
          <td width="50%"><table width="100%" style="font-size: 9pt;">
            <tbody>
              <tr>
                <td width="20%">Transfer to</td>
                <td width="80%">: <?php echo $beneficiary ?></td>
              </tr>
              <tr>
                <td width="20%">Bank</td>
                <td width="80%">: <?php echo $bank_name ?></td>
              </tr>
              <tr>
                <td width="20%">Branch</td>
                <td width="80%">: <?php echo $branch ?></td>
              </tr>
              <tr>
                <td width="20%">Account No</td>
                <td width="80%">: <?php echo $account_no ?></td>
              </tr>
              <tr>
                <td width="20%">Swift Code</td>
                <td width="80%">: <?php echo $swift_code ?></td>
              </tr>
            </tbody>
          </table></td>
          <td width="42%">
		  <table width="100%" class="table table-bordered table-striped" style="font-size: 7pt;">
            <tbody>
              <tr>
                <td>Prepare by,</td>
                <td>Approved by,</td>
                <td>Acknowledge by,</td>
              </tr>
              <tr>
                <td height="78">&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td height="23"><?php echo $prepareby ?></td>
                <td><?php echo $checkby ?></td>
                <td></td>
              </tr>
            </tbody>
          </table></td>

        </tr>
      </tbody>
  </table>
</div>

<hr>

<div class="row-fluid">
    <div class="span12 lightblue">
        <button class="btn btn-primary" id="printPODetail">Print</button>
		 <button class="btn" type="button" onclick="back()">Back</button>
        <?php if($_SESSION['userId'] == 19) {
?>
<button class="btn btn-warning" id="reject">Reject</button>
<?php
}
?>
  </div>
</div>

<?php
    // </editor-fold>
}
?>
