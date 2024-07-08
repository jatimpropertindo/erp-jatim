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
$handlingCostId = '';

// If ID is in the parameter
if(isset($_POST['handlingId']) && $_POST['handlingId'] != '') {
    
    $handlingId = $_POST['handlingId'];
    
    ?>

    <script type="text/javascript">
        // unblock when ajax activity stops 
        $(document).ajaxStop($.unblockUI);

        $(document).ready(function(){	//executed after the page has loaded
            
            $('#addhandlingCost').click(function(e){
            
                e.preventDefault();

                $("#modalErrorMsg").hide();
                $('#addhandlingCostModal').modal('show');
    //            alert($('#addNew').attr('href'));
                $('#addhandlingCostModalForm').load('forms/handling-cost-local-sales.php', {handlingId: $('input[id="generalhandlingId"]').val()});
            });
            
            $("#addhandlingCostForm").validate({
                rules: {
                    handlingId: "required",
                    masterGroupId: "required",
					username: "required",
					password: "required",
                },

                messages: {
                    handlingId: "handling is a required field.",
                    masterGroupId: "Group is a required field.",
					username: "Username is a required field.",
					password: "Password is a required field.",
				
                },

                submitHandler: function(form) {
                    $.ajax({
                        url: './data_processing.php',
                        method: 'POST',
                        data: $("#addhandlingCostForm").serialize(),
                        success: function(data){
                            var returnVal = data.split('|');
    //                        alert(data);
                            if(parseInt(returnVal[3])!=0)	//if no errors
                            {
                                //alert(msg);
                                if(returnVal[1] == 'OK') {
                                    //show success message
                                    $('#addhandlingCostModal').modal('hide');
                                    $('#handling-cost-local-sales-data').load('tabs/handling-cost-local-sales-data.php', {handlingId: $('input[id="generalhandlingId"]').val()});
                                    
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
        
            $('#tabContent3').load('contents/handling-cost-local-sales.php', { handlingId: <?php echo $handlingId; ?> }, iAmACallbackFunction3);

            
        });

        function iAmACallbackFunction3() {
            $('#tabContent3').fadeIn();
        }
    </script>
    
    <a href="#addhandlingCostModal" id="addhandlingCost" role="button"><img src="assets/ico/add.png" width="18px" height="18px" style="margin-bottom: 5px;" /> Add handling Cost</a>
    
    <div id="tabContent3">
        
    </div>
    
    <div id="addhandlingCostModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="addhandlingCostModalLabel" aria-hidden="true">
        <form id="addhandlingCostForm" method="post" style="margin: 0px;">
<!--            <input type="hidden" name="handlingCostId" id="handlingCostId" value="--><?php //echo $handlingCostId; ?><!--" />-->
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeAddhandlingCostModal">×</button>
                <h3 id="addhandlingCostModalLabel">Add handling Cost</h3>
            </div>
            <div class="alert fade in alert-error" id="modalErrorMsg" style="display:none;">
                Error Message
            </div>
            <!-- <input type="hidden" name="handlingId" id="handlingId" value="<?php echo $handlingId; ?>" /> -->
            <input type="hidden" name="action" id="action" value="handling_cost_local_sales_data" />
            <div class="modal-body" id="addhandlingCostModalForm">
                
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true" id="closeAddhandlingCostModal">Close</button>
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

