<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

// <editor-fold defaultstate="collapsed" desc="Functions">
$paymentLocation = false;
$sql = "SELECT * FROM user_module WHERE user_id = {$_SESSION['userId']} AND module_id = 27";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
if($result->num_rows > 0) {
    while($row = $result->fetch_object()) {
        if($row->module_id == 27) {
            
			$paymentLocation = true;
		}
		
	}
}
function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1, $onchange = "", $boolAllow = false) {
    global $myDatabase;
    
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    echo "<SELECT class='$class' tabindex='$tabindex' $disabled name='" . ($id . $uniq) . "' id='" . ($id . $uniq) . "' $onchange>";
    
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

// </editor-fold>

?>

<script type="text/javascript">
    $(document).ready(function(){
    $(".select2combobox100").select2({
        width: "250%"
    });
    
    $(".select2combobox50").select2({
        width: "50%"
    });
    
    $(".select2combobox75").select2({
        width: "75%"
    });
    
    $('#searchForm').submit(function(e){
        e.preventDefault();
//            alert('tes');
        $.blockUI({ message: '<h4>Please wait...</h4>' }); 
        $('#dataContent').load('reports/paymentToTax.php', {
            // stockpileId: $('select[id="searchStockpileId"]').val(), 
            periodFrom: $('input[id="searchPeriodFrom"]').val(),
            periodTo: $('input[id="searchPeriodTo"]').val(),
            pType: $('select[id="pType"]').val(),
            pLocation: $('select[id="pLocation"]').val(),
            paymentNo: $('select[id="paymentNo"]').val(),
            bankAccounts: $('select[id="bankAccounts"]').val(),
        }, iAmACallbackFunction2);
        });
    });
	
	$('#searchPeriodFrom').change(function() {
        // resetPaymentNo('');
        if(document.getElementById('searchPeriodFrom').value != '') {
            
            setPaymentNo($('input[id="searchPeriodFrom"]').val(),$('input[id="searchPeriodTo"]').val());
            
        }
    });
		
    $('#searchPeriodTo').change(function() {
        // resetPaymentNo('');
        if(document.getElementById('searchPeriodTo').value != '') {
            
            setPaymentNo($('input[id="searchPeriodFrom"]').val(),$('input[id="searchPeriodTo"]').val());
            
        }
    });
		
    // function resetPaymentNo(text) {
	// 	document.getElementById('paymentNo').options.length = 0;
    //     var x = document.createElement('option');
    //     x.value = '';
    //     x.text = '-- Please Select' + text + '--';
    //     document.getElementById('paymentNo').options.add(x);
    // }
	

    function setPaymentNo(searchPeriodFrom,searchPeriodTo) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getPaymentNo',
                    searchPeriodFrom: searchPeriodFrom,
					searchPeriodTo: searchPeriodTo,
            },
            success: function(data){
                var returnVal = data.split('~');
                if(parseInt(returnVal[0])!=0)	//if no errors
                {
                    //alert(returnVal[1].indexOf("{}"));
                    if(returnVal[1] == '') {
                        returnValLength = 0;
                    } else if(returnVal[1].indexOf("{}") == -1) {
                        isResult = returnVal[1].split('{}');
                        returnValLength = 1;
                    } else {
                        isResult = returnVal[1].split('{}');
                        returnValLength = isResult.length;
                    }

                    //alert(isResult);
				 
					

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('paymentNo').options.add(x);
                    }
				
                    
                }
            }
        });
    }
    
   $(function() {
        //https://github.com/eternicode/bootstrap-datepicker
        $('.datepicker').datepicker({
            minViewMode: 0,
            todayHighlight: true,
            //autoclose: true,
			orientation: "bottom auto",
            startView: 0
        });
    });
</script>

<div class="row" style="background-color: #f5f5f5; 
            margin-bottom: 5px; padding-top: 15px; 
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            border-radius: 4px;">
    <div class="offset3 span3">
        <form class="form-horizontal" id="searchForm" method="post">
          <!--  <div class="control-group">
                <label class="control-label" for="searchStockpileId">Stockpile</label>
                <div class="controls">
                    <?php 
                    /*createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                                FROM user_stockpile us
                                INNER JOIN stockpile s
                                    ON s.stockpile_id = us.stockpile_id
                                WHERE us.user_id = {$_SESSION['userId']}
                                ORDER BY s.stockpile_code ASC, s.stockpile_name ASC", "", "", "searchStockpileId", "stockpile_id", "stockpile_full", 
                                "", 1, "", 7);*/
                    ?>
                </div>
            </div> -->
            <div class="control-group">
                <label class="control-label" for="searchPeriodFrom">Payment Date From</label>
                <div class="controls">
                    <input type="text" placeholder="DD/MM/YYYY" tabindex="2" id="searchPeriodFrom" name="searchPeriodFrom" data-date-format="dd/mm/yyyy" class="datepicker" >
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="searchPeriodTo">Payment Date To</label>
                <div class="controls">
                    <input type="text" placeholder="DD/MM/YYYY" tabindex="3" id="searchPeriodTo" name="searchPeriodTo" data-date-format="dd/mm/yyyy" class="datepicker" >
                </div>
            </div>
			<div class="control-group">
                <label class="control-label" for="paymentNo">Payment No</label>
                <div class="controls">
                    <?php
						createCombo("", "", "", "paymentNo", "payment_no", "payment_no",
                    "", 7, "select2combobox100", "", "multiple");
					?>
                </div>
            </div>
			<div class="control-group">
                <label class="control-label" for="pType">Payment Type</label>
                <div class="controls">
                    <?php 
                   createCombo("SELECT 1 AS id, 'TT' AS info UNION
                                SELECT 2 AS id, 'Cek/Giro' AS info UNION
								SELECT 3 AS id, 'Tunai' AS info UNION
								SELECT 4 AS id, 'Bill Payment' AS info UNION
								SELECT 5 AS id, 'Auto Debet' as info ", "", "", "pType", "id", "info",
                                "", 7, "select2combobox100", "", "multiple");
                    ?>
                </div>
            </div>
			<?php if($paymentLocation){?>
			<div class="control-group">
                <label class="control-label" for="pLocation">Payment Location</label>
                <div class="controls">
                    <?php 
                   createCombo("SELECT 'HO' AS id, 'HO' AS info UNION
                                SELECT 'Stockpile' AS id, 'Stockpile' AS info ", "", "", "pLocation", "id", "info",
                                "", 7, "select2combobox100", "", "multiple");
                    ?>
                </div>
            </div>
			<?php }?>
            <div class="control-group">
                <label class="control-label" for="pType">Bank Accounts</label>
                <div class="controls">
                    <?php 
                    createCombo("SELECT b.bank_account_no, b.bank_id
                    FROM bank b
                    ORDER BY b.bank_name ASC", "", "", "bankAccounts", "bank_id", "bank_account_no",
                     "", 7, "select2combobox100", "", "multiple");
                    ?>
                </div>
            </div>
            <div class="control-group">
                <div class="controls">
                    <button type="submit" class="btn" id="preview">Preview</button>
                    <!--<button class="btn btn-success" id="generate">Generate XLS</button>-->
                </div>
            </div>
        </form>
    </div>
</div>