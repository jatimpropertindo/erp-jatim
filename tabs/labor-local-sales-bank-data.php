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

$laborId = '';

// If ID is in the parameter
if(isset($_POST['laborId']) && $_POST['laborId'] != '') {
    
    $laborId = $_POST['laborId'];
    
    ?>

    <script type="text/javascript">
        // unblock when ajax activity stops 
        $(document).ajaxStop($.unblockUI);

        $(document).ready(function(){	//executed after the page has loaded
            
            $('#addlaborBank').click(function(e){
            
                e.preventDefault();

                $("#modalErrorMsg").hide();
                $('#addlaborBankModal').modal('show');
    //            alert($('#addNew').attr('href'));
                $('#addlaborBankModalForm').load('forms/labor-local-sales-bank.php', {laborId: $('input[id="generalLaborId"]').val()});
            });
            
            $("#addlaborBankForm").validate({
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
                        data: $("#addlaborBankForm").serialize(),
                        success: function(data){
                            var returnVal = data.split('|');
    //                        alert(data);
                            if(parseInt(returnVal[3])!=0)	//if no errors
                            {
                                //alert(msg);
                                if(returnVal[1] == 'OK') {
                                    //show success message
                                    $('#addlaborBankModal').modal('hide');
                                    $('#labor-local-sales-bank-data').load('tabs/labor-local-sales-bank-data.php', {laborId: $('input[id="modallaborId"]').val()});
                                    
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
        
            $('#tabContent2').load('contents/labor-local-sales-bank.php', { laborId: <?php echo $laborId; ?> }, iAmACallbackFunction2);

            
        });

        function iAmACallbackFunction2() {
            $('#tabContent2').fadeIn();
        }
    </script>
    
    <a href="#addlaborBankModal" id="addlaborBank" role="button"><img src="assets/ico/add.png" width="18px" height="18px" style="margin-bottom: 5px;" /> Add Bank</a>
    
    <div id="tabContent2">
        
    </div>
    
    <div id="addlaborBankModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="addlaborBankModalLabel" aria-hidden="true">
        <form id="addlaborBankForm" method="post" style="margin: 0px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeAddlaborBankModal">×</button>
                <h3 id="addlaborBankModalLabel">Add Bank</h3>
            </div>
            <div class="alert fade in alert-error" id="modalErrorMsg" style="display:none;">
                Error Message
            </div>
            <input type="hidden" name="modallaborId" id="modallaborId" value="<?php echo $laborId; ?>" />
            <input type="hidden" name="action" id="action" value="labor_local_sales_bank_data" />
            <div class="modal-body" id="addlaborBankModalForm">
                
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true" id="closeAddlaborBankModal">Close</button>
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

