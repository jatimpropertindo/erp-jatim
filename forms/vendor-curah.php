<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$boolShow = false;
$vendorCurahId = '';

if(isset($_POST['vendorCurahId']) && $_POST['vendorCurahId'] != '') {
    $vendorCurahId = $_POST['vendorCurahId'];
    $boolShow = true;
}

?>

<script type="text/javascript">
    $(document).ajaxStop($.unblockUI);

    $(document).ready(function(){	//executed after the page has loaded
        loadConfig();

        $('#myTab a').click(function (e) {
            e.preventDefault();
            //alert(this);
            $("#successMsg").hide();
            $("#errorMsg").hide();

            $(this).tab('show');

            loadConfig();
        });



    });

    function loadConfig() {
        var url = $('#myTab .active a').attr('href');
        //alert(url)
        var menu = url.split('#');

        $.blockUI({ message: '<h4>Please wait...</h4>' });
        //alert(menu[1]);
        $('#' + menu[1]).load('tabs/' + menu[1] + '.php', {vendorCurahId: $('input[id="generalVendorCurahId"]').val()});
    }

    function back() {
        $.blockUI({ message: '<h4>Please wait...</h4>' });
        $('#pageContent').load('views/vendor-curah.php', {}, iAmACallbackFunction);
    }

</script>

<ul class="nav nav-tabs" id="myTab">
    <li class="active"><a href="#vendor-curah-data" data-toggle="tab">Vendor Curah Details</a></li>
    <?php
    if($boolShow) {
    ?>
	<li style="display: none;"><a href="#vendor-curah-email-data" data-toggle="tab">Email</a></li>
	 <?php
    }
    ?>
</ul>

<div class="tab-content">
    <input type="hidden" id="generalVendorCurahId" value="<?php echo $vendorCurahId; ?>" />
    <div class="alert">
        <b>Info:</b> <span style="color: red;">*</span> is required field.
    </div>
    <div class="alert fade in alert-success" id="successMsg" style="display:none;">
        Success Message
    </div>
    <div class="alert fade in alert-error" id="errorMsg" style="display:none;">
        Error Message
    </div>

    <div class="tab-pane active" id="vendor-curah-data">

    </div>

    <?php
    if($boolShow) {
    ?>
	<div class="tab-pane" id="vendor-curah-email-data">
        
    </div>
    <?php
    }
    ?>

</div>