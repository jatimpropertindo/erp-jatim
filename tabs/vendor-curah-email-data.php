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

$vendorCurahId = '';

// If ID is in the parameter
if(isset($_POST['vendorCurahId']) && $_POST['vendorCurahId'] != '') {
    
    $vendorCurahId = $_POST['vendorCurahId'];
    ?>

    <script type="text/javascript">
        // unblock when ajax activity stops 
        $(document).ajaxStop($.unblockUI);

        $(document).ready(function(){	//executed after the page has loaded
            
            $('#addVendorCurahEmail').click(function(e){
            
                e.preventDefault();

                $("#modalErrorMsg").hide();
                $('#addVendorCurahEmailModal').modal('show');
                //            alert($('#addNew').attr('href'));
                $('#addVendorCurahEmailModalForm').load('forms/vendor-curah-email-form.php', {generalVendorCurahId: $('input[id="generalVendorCurahId"]').val()});
            });
            
            $("#addVendorCurahEmailForm").validate({
                rules: {
                    vcEmail: "required",
                    status: "required"
                },

                messages: {
                    vcEmail: "Email is required",
                    status: "Status is required"
                },

                submitHandler: function(form) {
                    $.ajax({
                        url: './data_processing.php',
                        method: 'POST',
                        data: $("#addVendorCurahEmailForm").serialize(),
                        success: function(data){
                            var returnVal = data.split('|');
                        //    alert(data);
                            if(parseInt(returnVal[3])!=0)	//if no errors
                            {
                                //alert(msg);
                                if(returnVal[1] == 'OK') {
                                    //show success message
                                    $('#addVendorCurahEmailModal').modal('hide');
                                    $('#vendor-curah-email-data').load('tabs/vendor-curah-email-data.php', {vendorCurahId: $('input[id="generalVendorCurahId"]').val()});
                                    
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
        
            $('#tabContent3a').load('contents/vendor-curah-email-content.php', { generalVendorCurahId: <?php echo $vendorCurahId; ?> }, iAmACallbackFunction3);

            
        });

        function iAmACallbackFunction3() {
            $('#tabContent3a').fadeIn();
        }
    </script>
    
    <a href="#addVendorCurahEmailModal" id="addVendorCurahEmail" role="button"><img src="assets/ico/add.png" width="18px" height="18px" style="margin-bottom: 5px;" /> Add Email</a>
    
    <div id="tabContent3a">
        
    </div>
    
    <div id="addVendorCurahEmailModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="addVendorCurahEmailModalLabel" aria-hidden="true">
        <form id="addVendorCurahEmailForm" method="post" style="margin: 0px;">
            <input type="hidden" name="generalVendorCurahId" id="generalVendorCurahId" value="<?php echo $vendorCurahId; ?>" />
            <input type="hidden" name="action" id="action" value="vc_email_data" />
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeAddVendorCurahEmailModalx">×</button>
                <h3 id="addVendorCurahEmailModalLabel">Add Email</h3>
            </div>
            <div class="alert fade in alert-error" id="modalErrorMsg" style="display:none;">
                Error Message
            </div>
            <div class="modal-body" id="addVendorCurahEmailModalForm">
                
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true" id="closeAddVendorCurahEmailModal">Close</button>
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

