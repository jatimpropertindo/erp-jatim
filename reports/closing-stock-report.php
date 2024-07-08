<?php

error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));

// PATH

require_once '../assets/include/path_variable.php';



// Session

require_once PATH_INCLUDE.DS.'session_variable.php';



// Initiate DB connection

require_once PATH_INCLUDE.DS.'db_init.php';



$periodFrom = $_POST['periodFrom'];
$periodTo = $_POST['periodTo'];

$sql = "Call SP_notimReportSummary('{$periodFrom}','{$periodTo}')";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

//$stockpileName = 'All';
/*
if(isset($_POST['stockpileId']) && $_POST['stockpileId'] != '') {

    $stockpileCode = $_POST['stockpileId'];
	
	
	$sql = "SELECT stockpile_name FROM stockpile WHERE stockpile_code = '{$stockpileCode}'";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    if ($result !== false && $result->num_rows == 1) {
        $row = $result->fetch_object();
        $stockpileName = $row->stockpile_name;
    }

}*/


?>
<script type="text/javascript">
 $(document).ready(function () {
	  var wto;
        $('#downloadxls').submit(function (e) {
            clearTimeout(wto);
            wto = setTimeout(function () {
                $.blockUI({ message: '<h4>Please wait...</h4>' }); 
                $('#dataContent').load('reports/closing-stock-report.php', {
                    periodFrom: document.getElementById('periodFrom').value,
                    periodTo: document.getElementById('periodTo').value,
                    //stockpileId: document.getElementById('stockpileId').value,
                }, iAmACallbackFunction2);
            }, 1000);
        });

    });
</script>

<div class="row" style="background-color: #f5f5f5; 
            margin-bottom: 5px; padding-top: 15px; 
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            border-radius: 4px;">
    <div class="offset3 span3">
       
        <form class="form-horizontal" id="downloadxls" method="post" action="reports/closing-stock-report-xls.php" >
         <input type="hidden" id="periodFrom" name="periodFrom" value="<?php echo $periodFrom; ?>" />
    	 <input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>" />
        <!-- <input type="hidden" id="stockpileId" name="stockpileId" value="<?php //echo $stockpileCode; ?>" />-->

           
            <!--<div class="control-group">
               <label class="control-label" for="stockpile_name">Stockpile</label>
                <div class="controls">
                 <input type="text" readonly id="stockpile_name" name="stockpile_name" value="<?php //echo $stockpileName; ?>" />
                </div>
            </div>-->
    
            <div class="control-group">
                <label class="control-label" for="module_name2">Period</label>
                <div class="controls">
                  <input type="text" readonly id="module_name2" name="module_name2" value="<?php echo $periodFrom .' - '. $periodTo; ?>" />
                </div>
				</div>
			
            <div class="control-group">
               
                <div class="controls">
                    <button class="btn btn-success">Download XLS</button>
                   
                </div>
            </div>
            <div class="control-group">
                <div class="controls">
                    
                </div>
            </div>
			
			<table class="table table-bordered table-striped" id="contentTable" style="font-size: 8pt;">
				<thead>
				<tr>
					<th>STOCKPILE</th>
					<th>GGL</th>
					<th>ISCC</th>
					<th>GGL + ISCC</th>
					<th>NON CERTIFIED</th>
					<th>TOTAL</th>
				</tr>
				</thead>
				<tbody>
				 <?php
				    while ($rowContent = $result->fetch_object()) {
						$stockpile_name = $rowContent->stockpile_name;
						$ggl = $rowContent->ggl;
						$iscc = $rowContent->rsb;
						$ggliscc = $rowContent->rsb_ggl;
						$uncertified = $rowContent->uncertified;
						$total = $rowContent->total;
						
						
						?>
						<tr>
						<td style="text-align: left"><?php echo $stockpile_name; ?></td>
						<td style="text-align: right"><?php echo number_format($ggl, 2, ".", ","); ?></td>
						<td style="text-align: right"><?php echo number_format($iscc, 2, ".", ","); ?></td>
						<td style="text-align: right"><?php echo number_format($ggliscc, 2, ".", ","); ?></td>
						<td style="text-align: right"><?php echo number_format($uncertified, 2, ".", ","); ?></td>
						<td style="text-align: right"><?php echo number_format($total, 2, ".", ","); ?></td>
                        </tr>
					<?php	
					}
				 ?>
				</tbody>
            </table>
        </form>
    </div>
</div>