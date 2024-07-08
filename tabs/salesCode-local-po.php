<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$readonlyProperty = '';
$disabledProperty = '';
$whereProperty = '';

$salesId = '';
//$stockpileName = '';

// If ID is in the parameter
if(isset($_POST['salesId']) && $_POST['salesId'] != '') {
    
    $salesId = $_POST['salesId'];
    
    $sql = "SELECT * FROM sales WHERE sales_id = {$salesId}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    if($result !== false && $result->num_rows == 1) {
        $row = $result->fetch_object();
        $salesNo = $row->sales_no;
        $stockpile = $row->stockpile_id;
    }
    
    ?>

    <script type="text/javascript">
        // unblock when ajax activity stops 
        $(document).ajaxStop($.unblockUI);

        $(document).ready(function(){	//executed after the page has loaded
            
            $('#addSalesPO').click(function(e){
            
                e.preventDefault();

                $("#modalErrorMsg").hide();
                $('#addSalesPOModal').modal('show');
    //            alert($('#addNew').attr('href'));
                $('#addSalesPOModalForm').load('forms/salesCode-local-po.php', {salesId: $('input[id="modalSalesId"]').val(),stockpile: $('input[id="modalStockpile"]').val()});
            });
            
            $("#addSalesPOForm").validate({
                rules: {
                    poId: "required",
                    qty: "required"
                },

                messages: {
                    poId: "PO is a required field.",
                    qty: "Quantity is a required field."
                },

                submitHandler: function(form) {
                    $.ajax({
                        url: './data_processing.php',
                        method: 'POST',
                        data: $("#addSalesPOForm").serialize(),
                        success: function(data){
                            var returnVal = data.split('|');
    //                        alert(data);
                            if(parseInt(returnVal[3])!=0)	//if no errors
                            {
                                //alert(msg);
                                if(returnVal[1] == 'OK') {
                                    //show success message
                                    $('#addSalesPOModal').modal('hide');
                                    $('#salesCode-local-po').load('tabs/salesCode-local-po.php', {salesId: $('input[id="modalSalesId"]').val()});
                                    
                                    alertify.set({ labels: {
                                        ok     : "OK"
                                    } });
                                    alertify.alert(returnVal[2]);
                                } else {
                                    //show error message
                                    document.getElementById('modalErrorMsg').innerHTML = returnVal[2];
                                    $("#modalErrorMsg").show();
                                }
                            }
                        }
                    });
                }
            });
        
            $('#tabContent3').load('contents/salesCode-local-po.php', { salesId: <?php echo $salesId; ?> }, iAmACallbackFunction3);

            
        });

        function iAmACallbackFunction3() {
            $('#tabContent3').fadeIn();
        }
    </script>
    
    <h4>Sales No: <?php echo $salesNo; ?></h4>
    
    <a href="#addSalesPOModal" id="addSalesPO" role="button"><img src="assets/ico/add.png" width="18px" height="18px" style="margin-bottom: 5px;" /> Add PO PKS</a>
    
    <div id="tabContent3">
        
    </div>
    
    <div id="addSalesPOModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="addSalesPOModalLabel" aria-hidden="true">
        <form id="addSalesPOForm" method="post" style="margin: 0px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeAddSalesPOModal">×</button>
                <h3 id="addSalesPOModalLabel">Add/Edit PO PKS</h3>
            </div>
            <div class="alert fade in alert-error" id="modalErrorMsg" style="display:none;">
                Error Message
            </div>
            <input type="hidden" name="modalSalesId" id="modalSalesId" value="<?php echo $salesId; ?>" />
            <input type="hidden" name="modalStockpile" id="modalStockpile" value="<?php echo $stockpile; ?>" />
            <input type="hidden" name="action" id="action" value="sales_local_data_po" />
            <div class="modal-body" id="addSalesPOModalForm">
                
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true" id="closeAddSalesPOModal">Close</button>
                <button class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>

    <?php
    
} else {

    ?>
    
    <div class="alert fade in alert-error">
        <b>Error:</b><br/>PO PKS is not exist!
    </div>

    <?php

}

// Close DB connection
require_once PATH_INCLUDE.DS.'db_close.php';

