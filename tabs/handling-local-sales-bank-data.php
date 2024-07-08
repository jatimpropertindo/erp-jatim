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

$handlingId = '';

// If ID is in the parameter
if(isset($_POST['handlingId']) && $_POST['handlingId'] != '') {
    
    $handlingId = $_POST['handlingId'];
    
    ?>

    <script type="text/javascript">
        // unblock when ajax activity stops 
        $(document).ajaxStop($.unblockUI);

        $(document).ready(function(){	//executed after the page has loaded
            
            $('#addhandlingBank').click(function(e){
            
                e.preventDefault();

                $("#modalErrorMsg").hide();
                $('#addhandlingBankModal').modal('show');
    //            alert($('#addNew').attr('href'));
                $('#addhandlingBankModalForm').load('forms/handling-local-sales-bank.php', {handlingId: $('input[id="generalhandlingId"]').val()});
            });
            
            $("#addhandlingBankForm").validate({
                rules: {
                    bankName: "required",
					branch: "required",
					accountNo: "required",
					beneficiary: "required"
                },

                messages: {
                    stockpileId: "Bank Name is a required field.",
					branch: "Bracnh is a required field.",
					accountNo: "Account No is a required field.",
					beneficiary: "Beneficiary is a required field."
                },

                submitHandler: function(form) {
                    $.ajax({
                        url: './data_processing.php',
                        method: 'POST',
                        data: $("#addhandlingBankForm").serialize(),
                        success: function(data){
                            var returnVal = data.split('|');
    //                        alert(data);
                            if(parseInt(returnVal[3])!=0)	//if no errors
                            {
                                //alert(msg);
                                if(returnVal[1] == 'OK') {
                                    //show success message
                                    $('#addhandlingBankModal').modal('hide');
                                    $('#handling-local-sales-bank-data').load('tabs/handling-local-sales-bank-data.php', {handlingId: $('input[id="modalhandlingId"]').val()});
                                    
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
        
            $('#tabContent2').load('contents/handling-local-sales-bank.php', { handlingId: <?php echo $handlingId; ?> }, iAmACallbackFunction2);

            
        });

        function iAmACallbackFunction2() {
            $('#tabContent2').fadeIn();
        }
    </script>
    
    <a href="#addhandlingBankModal" id="addhandlingBank" role="button"><img src="assets/ico/add.png" width="18px" height="18px" style="margin-bottom: 5px;" /> Add Bank</a>
    
    <div id="tabContent2">
        
    </div>
    
    <div id="addhandlingBankModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="addhandlingBankModalLabel" aria-hidden="true">
        <form id="addhandlingBankForm" method="post" style="margin: 0px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeAddhandlingBankModal">×</button>
                <h3 id="addhandlingBankModalLabel">Add Bank</h3>
            </div>
            <div class="alert fade in alert-error" id="modalErrorMsg" style="display:none;">
                Error Message
            </div>
            <input type="hidden" name="modalhandlingId" id="modalhandlingId" value="<?php echo $handlingId; ?>" />
            <input type="hidden" name="action" id="action" value="handling_local_sales_bank_data" />
            <div class="modal-body" id="addhandlingBankModalForm">
                
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true" id="closeAddhandlingBankModal">Close</button>
                <button class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>

    <?php
    
} else {

    ?>
    
    <div class="alert fade in alert-error">
        <b>Error:</b><br/>User is not exist!
    </div>

    <?php

}

// Close DB connection
require_once PATH_INCLUDE.DS.'db_close.php';

