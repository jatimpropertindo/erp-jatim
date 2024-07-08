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
    
    $sql = "SELECT * FROM sales_local WHERE sales_con_id = {$salesId}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    if($result !== false && $result->num_rows == 1) {
        $row = $result->fetch_object();
        $sales_con_no = $row->sales_con_no;
        //$stockpile = $row->stockpile_id;
    }
    
    ?>

    <script type="text/javascript">
        // unblock when ajax activity stops 
        $(document).ajaxStop($.unblockUI);

        $(document).ready(function(){	//executed after the page has loaded
            
            $('#addSalesPOlc').click(function(e){
            
                e.preventDefault();

                $("#modalErrorMsg").hide();
                $('#addSalesPOlcModal').modal('show');
    //            alert($('#addNew').attr('href'));
                $('#addSalesPOlcModalForm').load('forms/salesOrder-local-lc.php', {salesId: $('input[id="modalSalesId"]').val()});
            });
            
            $("#addSalesPOlcForm").validate({
                rules: {
                    lcId: "required"
                    //qty: "required"
                },

                messages: {
                    lcId: "This is a required field."
                   // qty: "Quantity is a required field."
                },

                submitHandler: function(form) {
                    $.ajax({
                        url: './data_processing.php',
                        method: 'POST',
                        data: $("#addSalesPOlcForm").serialize(),
                        success: function(data){
                            var returnVal = data.split('|');
    //                        alert(data);
                            if(parseInt(returnVal[3])!=0)	//if no errors
                            {
                                //alert(msg);
                                if(returnVal[1] == 'OK') {
                                    //show success message
                                    $('#addSalesPOlcModal').modal('hide');
                                    $('#salesOrder-local-lc').load('tabs/salesOrder-local-lc.php', {salesId: $('input[id="modalSalesId"]').val()});
                                    
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
        
            $('#tabContentlc').load('contents/salesOrder-local-lc.php', { salesId: <?php echo $salesId; ?> }, iAmACallbackFunction3);

            
        });

        function iAmACallbackFunction3() {
            $('#tabContentlc').fadeIn();
        }
    </script>
    
    <h4>Sales Contract: <?php echo $sales_con_no; ?></h4>
    
    <a href="#addSalesPOlcModal" id="addSalesPOlc" role="button"><img src="assets/ico/add.png" width="18px" height="18px" style="margin-bottom: 5px;" /> Add labor Cost</a>
    
    <div id="tabContentlc">
        
    </div>
    
    <div id="addSalesPOlcModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="addSalesPOlcModalLabel" aria-hidden="true">
        <form id="addSalesPOlcForm" method="post" style="margin: 0px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeaddSalesPOlcModal">×</button>
                <h3 id="addSalesPOlcModalLabel">Add labor Cost</h3>
            </div>
            <div class="alert fade in alert-error" id="modalErrorMsg" style="display:none;">
                Error Message
            </div>
            <input type="hidden" name="modalSalesId" id="modalSalesId" value="<?php echo $salesId; ?>" />
            <input type="hidden" name="action" id="action" value="sales_order_data_lc" />
            <div class="modal-body" id="addSalesPOlcModalForm">
                
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true" id="closeaddSalesPOlcModal">Close</button>
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

