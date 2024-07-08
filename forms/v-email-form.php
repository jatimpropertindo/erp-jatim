<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$vEmailId = '';
$vEmail = '';
$status = '';


if(isset($_POST['vEmailId']) && $_POST['vEmailId'] != '') {
    $vEmailId = $_POST['vEmailId'];

    // <editor-fold defaultstate="collapsed" desc="Query for Freight Cost Data">

    $sql = "SELECT * FROM vendor_email WHERE v_email_id = {$vEmailId}";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
		$vEmailId = $rowData->v_email_id;
        $vEmail = $rowData->email;
        $status = $rowData->status;
    }

    // </editor-fold>
}

    // <editor-fold defaultstate="collapsed" desc="Functions">

function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1) {
    global $myDatabase;

    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    echo "<SELECT class='$class' tabindex='$tabindex' $disabled name='" . ($id . $uniq) . "' id='" . ($id . $uniq) . "'>";

    if($empty == 1) {
        echo "<option value=''>-- Please Select --</option>";
    } else if($empty == 2) {
        echo "<option value=''>-- Please Select if Applicable --</option>";
    }

    while ($combo_row = $result->fetch_object()) {
        if (strtoupper($combo_row->$valuekey) == strtoupper($setvalue))
            $prop = "selected";
        else
            $prop = "";

        echo "<OPTION value=\"" . $combo_row->$valuekey . "\" " . $prop . ">" . $combo_row->$value . "</OPTION>";
    }

    if($empty == 2) {
        echo "<option value='OTHER'>Others</option>";
    }

    echo "</SELECT>";
}

    // </editor-fold>

?>


<script type="text/javascript">
    $(document).ajaxStop($.unblockUI);

    $(document).ready(function(){

        $(".select2combobox100").select2({
            width: "100%"
        });

        $(".select2combobox50").select2({
            width: "50%"
        });

        $(".select2combobox75").select2({
            width: "75%"
        });

    });

</script>

<input type="hidden" id="vEmailId" name="vEmailId" value="<?php echo $vEmailId; ?>">

<div class="row-fluid">
    <div class="span6 lightblue">
        <label>Email</label>
        <input type="text" class="span12" tabindex="10" id="vEmail" name="vEmail" value="<?php echo $vEmail; ?>">
    </div>
</div>
<div class="row-fluid">
    <div class="span6 lightblue">
        <label>Status <span style="color: red;">*</span></label>
        <?php
            createCombo("SELECT '0' as id, 'Active' as info UNION
                    SELECT '1' as id, 'Inactive' as info;", $status, '', "status", "id", "info", 
                "", 6);
        ?>
    </div>
</div>

