<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$batchUploadDetailId = $myDatabase->real_escape_string($_POST['batch_upload_detail_id']);
$periodFrom = $myDatabase->real_escape_string($_POST['periodFrom']);
$periodTo = $myDatabase->real_escape_string($_POST['periodTo']);
$pTypes = $myDatabase->real_escape_string($_POST['periodTo']);
$bankAccounts = $myDatabase->real_escape_string($_POST['bankAccounts']);
$pLocations = $myDatabase->real_escape_string($_POST['pLocations']);
$paymentNos = $myDatabase->real_escape_string($_POST['paymentNos']);
$reason = '';
$imageFile = '';

// <editor-fold defaultstate="collapsed" desc="Functions">

function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1, $onchange = "", $boolAllow = false) {
    global $myDatabase;

    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    echo "<SELECT class='$class' tabindex='$tabindex' $disabled name='" . ($id . $uniq) . "' id='" . ($id . $uniq) . "' $onchange>";

    if($empty == 1) {
        echo "<option value=''>-- Please Select --</option>";
    } else if($empty == 2) {
        echo "<option value=''>-- Please Select Stockpile --</option>";
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
    $(document).ready(function() {	// executed after the page has loaded
        $('#closeCancelButton').click(function (e) {
            var periodFrom = $('input[id="periodFrom"]').val();
			var periodTo = $('input[id="periodTo"]').val();
            e.preventDefault();

            // $('#cancelButton').attr("disabled", true);
            // $('#closeCancelButton').attr("disabled", true);

            $('#cancelModal').modal('hide');
            $('#dataContent').load('reports/paymentToTax.php', {periodFrom: periodFrom, periodTo: periodTo}, iAmACallbackFunction2);
        });

        $('#cancelForm').on('submit', function(e) {

            e.preventDefault();

            $('#cancelButton').attr("disabled", true);
            $('#closeCancelButton').attr("disabled", true);
            //$.blockUI({ message: '<h4>Please wait...</h4>' });

            $(this).ajaxSubmit({
                success:  showResponse //call function after success
            });
        });

    });

    function showResponse(responseText, statusText, xhr, $form)  {
        var periodFrom = $('input[id="periodFrom"]').val();
        var periodTo = $('input[id="periodTo"]').val();
        // for normal html responses, the first argument to the success callback
        // is the XMLHttpRequest object's responseText property

        // if the ajaxSubmit method was passed an Options Object with the dataType
        // property set to 'xml' then the first argument to the success callback
        // is the XMLHttpRequest object's responseXML property

        // if the ajaxSubmit method was passed an Options Object with the dataType
        // property set to 'json' then the first argument to the success callback
        // is the json data object returned by the server

        // alert('status: ' + statusText + '\n\nresponseText: \n' + responseText +
        // '\n\nThe output div should have already been updated with the responseText.');

        var returnVal = responseText.split('|');
        //    alert(returnVal);
        if (parseInt(returnVal[3]) != 0)	// IF No Errors
        {
            //    alert(responseText);
            alertify.set({ labels: {
                ok     : "OK"
            } });
            alertify.alert(returnVal[2]);
            if(returnVal[1] == 'OK') {
                // show success message
                $('#cancelModal').modal('hide');
                $('#dataContent').load('reports/paymentToTax.php', {periodFrom: periodFrom, periodTo: periodTo}, iAmACallbackFunction2);

                // document.getElementById('successMsg').innerHTML = returnVal[2];
                // $("#successMsg").show();

            } 
            else {
                // show error message
                // document.getElementById('modalErrorMsg').innerHTML = returnVal[2];
                // $("#modalErrorMsg").show();
                $('#cancelButton').attr("disabled", false);
                $('#closeCancelButton').attr("disabled", false);
            }
        }

    }

    function iAmACallbackFunction2() {
        $("#dataContent").fadeIn("slow");
    }

</script>

<form id="cancelForm" method="post" enctype="multipart/form-data" action="./data_processing.php">
    <input type="hidden" name="action" id="action" value="cancel_batch_upload" />
    <input type="hidden" name="batch_upload_detail_id" id="batch_upload_detail_id" value="<?php echo $batchUploadDetailId; ?>" />
    <input type="hidden" id="periodFrom" name="periodFrom" value="<?php echo $periodFrom; ?>" />
    <input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>" />


    <div class="row-fluid">
        <label class="control-label" for="imagefile">Image</label>
        <div class="fileupload fileupload-new" data-provides="fileupload">
            <div class="input-append">
                <div class="uneditable-input" style="min-width: 200px;">
                    <i class="icon-file fileupload-exists"></i>
                    <span class="fileupload-preview"></span>
                </div>
                <span class="btn btn-file">
                    <span class="fileupload-new">Select file</span>
                    <span class="fileupload-exists">Change</span>
                    <input type="file" name="imagefile" id="imagefile" />
                </span>
                <a href="#" class="btn fileupload-exists" data-dismiss="fileupload">Remove</a>
            </div>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span12 lightblue">
            <label>Reason<span style="color: red;">*</span></label>
            <textarea class="span10" rows="3" tabindex="7" id="reason" name="reason"><?php echo $reason; ?></textarea>
        </div>
    </div>

    <div class="row-fluid">
        <button class="btn btn-success" id="cancelButton">Submit</button>
        <button class="btn btn-inverse" id="closeCancelButton">Close</button>
    </div>

</form>