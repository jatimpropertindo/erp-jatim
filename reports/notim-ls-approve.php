<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';


$soId = '';


if(isset($_POST['soId']) && $_POST['soId'] != ''){
	$soId = $_POST['soId'];
}


$sql = "SELECT a.*, c.`salesCode`, e.`user_name`, c.`sales_no` AS sales_contract, d.so_no 
FROM temp_transaction a
LEFT JOIN shipment b ON b.`shipment_id` = a.`shipment_id`
LEFT JOIN sales c ON b.`sales_id` = c.`sales_id`
LEFT JOIN sales_order d ON d.`so_id` = c.`so_id`
LEFT JOIN `user` e ON e.`user_id` = a.`entry_by`
WHERE a.`status` = 0 AND c.`so_id` = {$soId} ORDER BY a.slip_no ASC";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

//echo $sql;

?>
<script type="text/javascript">

    $(document).ready(function(){	//executed after the page has loaded
        $("#approveNotimForm").validate({

    submitHandler: function(form) {
    $.blockUI({ message: '<h4>Please wait...</h4>' }); 
    $.ajax({
        url: './data_processing.php',
        method: 'POST',
        data: $("#approveNotimForm").serialize(),
        success: function(data) {
            var returnVal = data.split('|');

            if (parseInt(returnVal[3]) != 0)	//if no errors
            {
                alertify.set({ labels: {
                    ok     : "OK"
                } });
                alertify.alert(returnVal[2]);
                
                if (returnVal[1] == 'OK') {
                    $('#dataContent').load('reports/notim-ls-approve.php', {soId: $('input[id="soId"]').val()},iAmACallbackFunction2);
                } 
            }
        }
    });
}
});

$('#canceled').click(function (e) {
    e.preventDefault();

    //var checkboxes = document.getElementsByName('checks');
    //var checkboxes = $('input[name="checks"]').val();

    var notim = [];
		/*Initializing array with Checkbox checked values*/
        $("input[name='checks[]']:checked").each(function(){
            notim.push(this.value);
        });
        

    $.ajax({
        url: './data_processing.php',
        method: 'POST',
        //data: $("#approveNotimForm").serialize(),
        data: {
                action: 'transaction_data',
                _method: 'CANCEL_2',
                checkboxes: notim
                //tempTransactionId: document.getElementsByName('checks[]'),
                //shipmentId: document.getElementById('shipmentId').value,
                // reject_remarks: document.getElementById('reject_remarks').value
            },
        success: function(data) {
            var returnVal = data.split('|');

            if (parseInt(returnVal[3]) != 0)	//if no errors
            {
                alertify.set({ labels: {
                    ok     : "OK"
                } });
                alertify.alert(returnVal[2]);
                
                if (returnVal[1] == 'OK') {
                    $('#dataContent').load('reports/notim-ls-approve.php', {soId: $('input[id="soId"]').val()},iAmACallbackFunction2);
                } 
            }
        }
    });

});

});

function toggle(source) {
        checkboxes = document.getElementsByName('checks[]');
        for(var i=0, n=checkboxes.length;i<n;i++) {
          checkboxes[i].checked = source.checked;
        }
    }


</script>

<?php 
$sql = "SELECT a.*
FROM sales_order a
WHERE a.`so_id` = {$soId}";
$result2 = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
if ($result2 !== false && $result2->num_rows == 1) {
    $row = $result2->fetch_object();
    
    $qty = $row->qty;
}

?>

<form method="post" id="approveNotimForm">


<li class="active">Qty Sales Order : <?php echo number_format($qty, 0, ".", ","); ?> Kg</li>

<table class="table table-bordered table-striped" style="font-size: 8pt;">

    <thead>
        <tr>
            <td> <div style="text-align: center">
                <input type="checkbox" onClick="toggle(this)" />
                </div>
            </td>
			
			
            <td>Slip No</td>
			<td>Sales Contract</td>
			<td>Sales Order</td>
            <td>Sales Code</td>
            <td>Transaction Date</td>
            <td>Vehicle No</td>
            <td>Driver </td>
            <td>Inventory Weight</td>
            <td>Buyer Weight</td>
            <td>PKS Weight</td>
            <td>Entry By</td>
            <td>Entry Date</td>
            
        </tr>
    </thead>
    <tbody>
        <?php
          if($result->num_rows > 0) {
			//echo 'test';
            $no = 1;
            //while( $row = mysqli_fetch_array($result)){
			while ($row = mysqli_fetch_array($result)) {
			//echo 'test';
		?>
        <tr>

			
            <td >
            <input type="checkbox" name="checks[]" value="<?php echo $row['temp_transaction_id']?>" />  
            </td>
			
            <td ><?php echo $row['slip_no']; ?></td>
			<td ><?php echo $row['sales_contract']; ?></td>
			<td ><?php echo $row['so_no']; ?></td>
            <td ><?php echo $row['salesCode']; ?></td>
            <td ><?php echo $row['transaction_date']; ?></td>
            <td><?php echo $row['vehicle_no']; ?></td>
            <td ><?php echo $row['driver']; ?></td>
            <td style="text-align: right;"><?php echo number_format($row['send_weight'], 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row['quantity'], 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row['pks_weight'], 0, ".", ","); ?></td>
            <td ><?php echo $row['user_name']; ?></td>
            <td ><?php echo $row['entry_date']; ?></td>
			
        </tr>
                <?php
				$no++;
                
				$swTotal = $swTotal + $row['send_weight'];
                $blTotal = $blTotal + $row['quantity'];
                $pksTotal = $pksTotal + $row['pks_weight'];
            }

			//echo $grandTotal;
        }else{
			//echo $sql;
		}
        ?>
		<tr>
		<td colspan="8" style="text-align: right;">TOTAL</td>
		<td style="text-align: right;"><?php echo number_format($swTotal, 0, ".", ","); ?></td>
        <td style="text-align: right;"><?php echo number_format($blTotal, 0, ".", ","); ?></td>
        <td style="text-align: right;"><?php echo number_format($pksTotal, 0, ".", ","); ?></td>
		<td colspan="2"></td>

		</tr>
    </tbody>
</table>
<div class="row-fluid">
    <input type="hidden" name="action" id="action" value="transaction_data" />
    <input type="hidden" name="soId" id="soId" value="<?php echo $soId; ?>" />
    <input type="hidden" name="transactionType" id="transactionType" value="2" />
    <input type="hidden" name="_method" value="INSERT_2">
</div>
<div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn btn-primary" <?php echo $disableProperty; ?>>Submit</button>
			<button class="btn btn-danger"  <?php echo $disableProperty; ?> id="canceled" >Cancel</button>
        </div>
    </div>
</form>