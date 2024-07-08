<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

// <editor-fold defaultstate="collapsed" desc="Variable for Freight Cost Data">

$whereProperty = '';
$vendorId = $_POST['vendorId'];
$v_ppn_id = '';
$ppn_tax_id = '';
$status = '';


// </editor-fold>

if(isset($_POST['v_ppn_id']) && $_POST['v_ppn_id'] != '') {
    $v_ppn_id = $_POST['v_ppn_id'];
    
    // <editor-fold defaultstate="collapsed" desc="Query for Freight Cost Data">
    
    $sql = "SELECT * FROM vendor_ppn vPPn left join tax tx on tx.tax_id = vPPn.ppn_tax_id WHERE v_ppn_id = {$v_ppn_id}
            ";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $ppn_tax_id = $rowData->ppn_tax_id;
        $status = $rowData->status;
		$taxName = $rowData->tax_name;
		$v_ppn_id = $rowData->v_ppn_id;
        
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
    $(document).ready(function(){
		$("select.select2combobox100").select2({
            width: "100%"
        });
        
        $("select.select2combobox50").select2({
            width: "50%"
        });
        
        $("select.select2combobox75").select2({
            width: "75%"
        });
		
        /*if(document.getElementById('currencyId').value == 1 || document.getElementById('currencyId').value == '') {
            $('#exchangeRateFreight').hide();
        } else {
            $('#exchangeRateFreight').show();
        }
            
        $('#currencyId').change(function() {
            if(document.getElementById('currencyId').value == 1 || document.getElementById('currencyId').value == '') {
                $('#exchangeRateFreight').hide();
            } else {
                $('#exchangeRateFreight').show();
            }
        });*/
		
    });
</script>

<!--<input type="hidden" id="freightCostId" name="freightCostId" value="<?php //echo $freightCostId; ?>">-->
<input type="hidden" id="vendorId" name="vendorId" value="<?php echo $vendorId; ?>">
<input type="hidden" id="v_ppn_id" name="v_ppn_id" value="<?php echo $v_ppn_id; ?>">

<div class="row-fluid">   
    <div class="span6 lightblue">
        <label>PPN <span style="color: red;">*</span></label>
        <?php
		if($v_ppn_id == ''){
        createCombo("SELECT tx.tax_id, tx.tax_name
                FROM tax tx WHERE tx.tax_type = 1 AND tx.tax_id NOT IN (SELECT ppn_tax_id FROM vendor_ppn WHERE vendor_id = {$vendorId})
                ORDER BY tx.tax_id DESC", "", $ppn_tax_id, "taxId", "tax_id", "tax_name", 
                "", 1, "select2combobox100");
		}else{		
        ?>
		<input type="text" readonly id="taxName" name="taxName" value="<?php echo $taxName; ?>">
		<input type="hidden" id="taxId" name="taxId" value="<?php echo $ppn_tax_id; ?>">
		<?php
		}
		?>
    </div>
	<div class="span6 lightblue">
           <label>Status <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT '0' as id, 'Active' as info UNION
                    SELECT '1' as id, 'Inactive' as info;", $status, '', "status", "id", "info", 
                "", 6);
            ?>
        </div>
</div>


