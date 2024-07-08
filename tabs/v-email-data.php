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

$vendorId = '';

// If ID is in the parameter
if(isset($_POST['vendorId']) && $_POST['vendorId'] != '') {
    
    $vendorId = $_POST['vendorId'];
    
    ?>

    <script type="text/javascript">
        // unblock when ajax activity stops 
        $(document).ajaxStop($.unblockUI);

        $(document).ready(function(){	//executed after the page has loaded
            
            $('#addVEmail').click(function(e){
            
                e.preventDefault();

                $("#modalErrorMsg").hide();
                $('#addVEmailModal').modal('show');
        //            alert($('#addNew').attr('href'));
                $('#addVEmailModalForm').load('forms/v-email-form.php', {vendorId: $('input[id="generalVendorId"]').val()});
            });
            
            $("#addVEmailForm").validate({
                rules: {
                    vEmail: "required",
                    status: "required"
                },

                messages: {
                    vEmail: "Email is required",
                    status: "Status is required"
                },

                submitHandler: function(form) {
                    $.ajax({
                        url: './data_processing.php',
                        method: 'POST',
                        data: $("#addVEmailForm").serialize(),
                        success: function(data){
                            var returnVal = data.split('|');
    //                        alert(data);
                            if(parseInt(returnVal[3])!=0)	//if no errors
                            {
                                //alert(msg);
                                if(returnVal[1] == 'OK') {
                                    //show success message
                                    $('#addVEmailModal').modal('hide');
                                    $('#v-email-data').load('tabs/v-email-data.php', {vendorId: $('input[id="generalVendorId"]').val()});
                                    
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
        
            $('#tabContent3').load('contents/v-email-content.php', { vendorId: <?php echo $vendorId; ?> }, iAmACallbackFunction3);

            
        });

        function iAmACallbackFunction3() {
            $('#tabContent3').fadeIn();
        }
    </script>
    
    <a href="#addVEmailModal" id="addVEmail" role="button"><img src="assets/ico/add.png" width="18px" height="18px" style="margin-bottom: 5px;" /> Add Email</a>
    
    <div id="tabContent3">
        
    </div>
    
    <div id="addVEmailModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="addVEmailModalLabel" aria-hidden="true">
        <form id="addVEmailForm" method="post" style="margin: 0px;">
            <input type="hidden" name="generalVendorId" id="generalVendorId" value="<?php echo $vendorId; ?>" />
            <input type="hidden" name="action" id="action" value="v_email_data" />
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeAddVEmailModal">×</button>
                <h3 id="addVEmailModalLabel">Add Email</h3>
            </div>
            <div class="alert fade in alert-error" id="modalErrorMsg" style="display:none;">
                Error Message
            </div>
            <div class="modal-body" id="addVEmailModalForm">
                
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true" id="closeAddVEmailModal">Close</button>
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

