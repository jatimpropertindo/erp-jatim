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
$periodFrom = '';
$periodTo = '';
$pType = '';
$pTypes = '';
$bankId = '';
$lastPaymentId = '';
$pLocation = '';
$pLocations = '';
$paymentNo = '';
$paymentNos = '';

$sql = "SELECT * FROM user_module WHERE user_id = {$_SESSION['userId']} AND module_id = 27";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
if($result->num_rows > 0) {
    while($row = $result->fetch_object()) {
        if($row->module_id == 27) {

			$whereProperty = "";

			if(isset($_POST['pLocation']) && $_POST['pLocation'] != ''){
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

        }else{
			$whereProperty = "AND p.entry_by = {$_SESSION['userId']}";
			$whereProperty3 = "";
		}

	}
}

if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodFrom = $_POST['periodFrom'];
    $periodTo = $_POST['periodTo'];
    $whereProperty .= " AND DATE_FORMAT(bu_header.batch_date, '%Y-%m-%d') BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
    //$sumProperty .= " AND IF(t.transaction_type = 1, t.unloading_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
    //$boolBalanceBefore = true;
} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] == '') {
    $periodFrom = $_POST['periodFrom'];
    $whereProperty .= " AND DATE_FORMAT(bu_header.batch_date, '%Y-%m-%d') >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')";
    //$sumProperty .= " AND IF(t.transaction_type = 1, t.unloading_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
    //$boolBalanceBefore = true;
} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] == '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodTo = $_POST['periodTo'];
    $whereProperty .= " AND DATE_FORMAT(bu_header.batch_date, '%Y-%m-%d') <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";
}

$sql = "SELECT * FROM batch_upload_header bu_header
WHERE 1=1 {$whereProperty}";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

// echo $sql;

?>
<script type="text/javascript">

    $(document).ready(function() {	//executed after the page has loaded
        $('#printApprovalSheet').click(function(e){
            e.preventDefault();

            // $("#transactionContainer").show();
            // https://github.com/jasonday/printThis
            $("#approvalSheet").printThis();
			// $("#transactionContainer").hide();
        });

        $(document).on('click', 'button.submit', function () {
            var form = $('#downloadBatchUpload');
            var action = $(this).data('action');
            form.attr('action', action);
            form.submit();
        });
	});

	function checkBatchHeader() {
		var checkBatchHeaders = document.getElementsByName('checkedBatchHeaders[]');
		var selected = "";

		for (var i = 0; i < checkBatchHeaders.length; i++) {
			if (checkBatchHeaders[i].checked) {
				if (selected == "") {
					selected = checkBatchHeaders[i].value;
				} else {
					selected = selected + "," + checkBatchHeaders[i].value;
				}
			} else {
                $('#batchUploadDetailContainer').hide();
            }
		} 
		document.getElementById('batchHeaderCodes').value = selected;
        setBatchUploadDetail(selected);
	}

    function checkAll(a) {
        var checkBatchHeaders = document.getElementsByName('checkedBatchHeaders[]');
		var selected = "";

        if (a.checked) {
            for (var i = 0; i < checkBatchHeaders.length; i++) {
                if (checkBatchHeaders[i].type == 'checkbox') {
                    checkBatchHeaders[i].checked = true;
                    if (checkBatchHeaders[i].checked) {
                        if (selected == "") {
                            selected = checkBatchHeaders[i].value;
                        } else {
                            selected = selected + "," + checkBatchHeaders[i].value;
                        }
                    } else {
                        $('#batchUploadDetailContainer').hide();
                    }
                }
            }
        } else {
            for (var i = 0; i < checkBatchHeaders.length; i++) {
                // console.log(i)
                if (checkBatchHeaders[i].type == 'checkbox') {
                    checkBatchHeaders[i].checked = false;
                    $('#batchUploadDetailContainer').hide();
                }
            }
        }

		document.getElementById('batchHeaderCodes').value = selected;
        setBatchUploadDetail(selected);
    }

    function setBatchUploadDetail(checkedSlips) {
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: {
                action: 'getBatchUploadDetail',
                checkedSlips: checkedSlips,
            },
            success: function (data) {
                if (data != '') {
                    $('#batchUploadDetailContainer').show();
                    document.getElementById('batchUploadDetailContainer').innerHTML = data;
                }
            }
        });
    }


</script>

<form id="downloadBatchUpload" method="post" style="margin-top: 10px;">
    <input type="hidden" id="periodFrom" name="periodFrom" value="<?php echo $periodFrom; ?>" />
    <input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>" />
    <input type="hidden" id="batchHeaderCodes" name="batchHeaderCodes" value="<?php echo $batchHeaderCodes; ?>" />
    <button id="downloadxls" class="submit btn btn-success" data-action="reports/print_batch_upload_detail_xls.php">Download XLS</button>
    <button id="downloaddoc" class="submit btn btn-primary" data-action="reports/print_batch_upload_detail_doc.php">Download DOC WA</button>
</form>

<div id = "batchUpload">
    <div class="table-responsive" style="width:100%;overflow:auto; max-height:223px; margin-top: 10px;">
        <table class="table table-bordered table-striped" style="font-size: 8pt;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Batch Date</th>
                    <th>Batch Code</th>
                    <th>Batch Number</th>
                    <th>Total Trx</th>
                    <th>Period Start</th>
                    <th>Period End</th>
                    <th style="text-align: center;"><input type="checkbox" onchange="checkAll(this)" /></th>
                </tr>
            </thead>
        <?php
        if($result->num_rows > 0) {
            $no = 1;
            while($row = mysqli_fetch_array($result)) {
        ?>
            <tbody>
                <tr>
                    <td><?php echo $row['batch_upload_header_id']; ?></td>
                    <td><?php echo $row['batch_date']; ?></td>
                    <td><?php echo $row['batch_code']; ?></td>
                    <td><?php echo $row['batch_number']; ?></td>
                    <td><?php echo number_format($row['total_trx'], 3, '.', ',') ; ?></td>
                    <td><?php echo $row['period_start']; ?></td>
                    <td><?php echo $row['period_end']; ?></td>
                    <td style="text-align: center;"><input type="checkbox" name="checkedBatchHeaders[]" id="fc" value="<?php echo $row['batch_code']; ?>" onclick="checkBatchHeader();" /></td>
                </tr>
            </tbody>
        <?php 
            }
        }
        ?>
        </table>
    </div>

    <div class="row-fluid" id="batchUploadDetailContainer" style="display: none; margin-top: 15px; margin-bottom:15px;">
        Batch Upload Detail
    </div>
</div>
