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
$laborCostId = '';

// If ID is in the parameter
if(isset($_POST['laborId']) && $_POST['laborId'] != '') {
    
    $laborId = $_POST['laborId'];
    
    ?>

    <script type="text/javascript">
        // unblock when ajax activity stops 
        $(document).ajaxStop($.unblockUI);

        $(document).ready(function(){	//executed after the page has loaded
            
            $('#addlaborCost').click(function(e){
            
                e.preventDefault();

                $("#modalErrorMsg").hide();
                $('#addlaborCostModal').modal('show');
    //            alert($('#addNew').attr('href'));
                $('#addlaborCostModalForm').load('forms/labor-cost-local-sales.php', {laborId: $('input[id="generalLaborId"]').val()});
            });
            
            $("#addlaborCostForm").validate({
                rules: {
                    laborId: "required",
                    masterGroupId: "required",
					username: "required",
					password: "required",
                },

                messages: {
                    laborId: "labor is a required field.",
                    masterGroupId: "Group is a required field.",
					username: "Username is a required field.",
					password: "Password is a required field.",
				
                },

                submitHandler: function(form) {
                    $.ajax({
                        url: './data_processing.php',
                        method: 'POST',
                        data: $("#addlaborCostForm").serialize(),
                        success: function(data){
                            var returnVal = data.split('|');
    //                        alert(data);
                            if(parseInt(returnVal[3])!=0)	//if no errors
                            {
                                //alert(msg);
                                if(returnVal[1] == 'OK') {
                                    //show success message
                                    $('#addlaborCostModal').modal('hide');
                                    $('#labor-cost-local-sales-data').load('tabs/labor-cost-local-sales-data.php', {laborId: $('input[id="generalLaborId"]').val()});
                                    
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
        
            $('#tabContent3').load('contents/labor-cost-local-sales.php', { laborId: <?php echo $laborId; ?> }, iAmACallbackFunction3);

            
        });

        function iAmACallbackFunction3() {
            $('#tabContent3').fadeIn();
        }
    </script>
    
    <a href="#addlaborCostModal" id="addlaborCost" role="button"><img src="assets/ico/add.png" width="18px" height="18px" style="margin-bottom: 5px;" /> Add labor Cost</a>
    
    <div id="tabContent3">
        
    </div>
    
    <div id="addlaborCostModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="addlaborCostModalLabel" aria-hidden="true">
        <form id="addlaborCostForm" method="post" style="margin: 0px;">
<!--            <input type="hidden" name="laborCostId" id="laborCostId" value="--><?php //echo $laborCostId; ?><!--" />-->
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeAddlaborCostModal">×</button>
                <h3 id="addlaborCostModalLabel">Add labor Cost</h3>
            </div>
            <div class="alert fade in alert-error" id="modalErrorMsg" style="display:none;">
                Error Message
            </div>
            <!-- <input type="hidden" name="laborId" id="laborId" value="<?php echo $laborId; ?>" /> -->
            <input type="hidden" name="action" id="action" value="labor_cost_local_sales_data" />
            <div class="modal-body" id="addlaborCostModalForm">
                
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true" id="closeAddlaborCostModal">Close</button>
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

