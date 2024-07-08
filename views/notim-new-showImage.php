<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

//$transactionId = $_POST['transactionId'];
//$transactionNewId = $_POST['transactionNewId'];
$type = $_POST['type'];

// <editor-fold defaultstate="collapsed" desc="Functions">
$imageType = '';
if(isset($_POST['transactionId'])){
    $sql = "SELECT ti.doc_url, ti.ticket_url, ti.`transaction_id` FROM jatim_inventory.transaction t
        LEFT JOIN jatim_inventory.transaction_img ti ON ti.transaction_id = t.transaction_id
        WHERE t.transaction_id = {$_POST['transactionId']} LIMIT 1";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    // echo $sql;
    if($result !== false && $result->num_rows == 1) {
        $row = $result->fetch_object();
        if ($row->doc_url == '') {
            $photo = $row->pic;
            if($type == 'truck'){
                $photo = $row->pic_truck;
            }
            $imageType = 'Blob';
        } else {
            $photo = $row->ticket_url;
            if($type == 'truck'){
                $photo = $row->doc_url;
            }
            $imageType = 'Url';
        }
    }
    // echo $imageType;
} else if(isset($_POST['transactionNewId'])) {
    $sql = "SELECT ti.doc_url, ti.ticket_url, ti.`transaction_id` FROM jatim_inventory.transaction t
    LEFT JOIN jatim_inventory.transaction_img ti ON ti.transaction_id = t.transaction_id
    WHERE t.transaction_id = {$_POST['transactionNewId']}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    if($result !== false && $result->num_rows == 1) {
        $row = $result->fetch_object();
        if ($row->doc_url == '') {
            $photo = $row->pic;
            if($type == 'truck'){
                $photo = $row->pic_truck;
            }
            $imageType = 'Blob';
        } else {
            $photo = $row->ticket_url;
            if($type == 'truck'){
                $photo = $row->doc_url;
            }
            $imageType = 'Url';
        }
        
        
    }
}

// </editor-fold>
?>

<div id="results" class="text-center">
    <?php if($imageType == 'Blob') { ?>
        <img id="base64image" src="data:image/jpeg;base64,<?php echo base64_encode($photo)?>"/>
        <?php } else if ($imageType == 'Url') { ?>
            <img id="base64image" src="<?php echo $photo ?>"/>
        <?php }else{ ?>
        <h1>Photo Tidak ada!</h1>
    <?php } ?>
</div>




