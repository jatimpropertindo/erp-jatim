<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

$purchasingDetailId = $_POST['purchasingDetailId'];

// <editor-fold defaultstate="collapsed" desc="Functions">
$sql = "SELECT CONCAT(v.`vendor_code`, ' - ', v.vendor_name) AS vendor_name,
            case when p.contract_type = 1 then 'PKS-Contract'
            when p.contract_type = 2 then 'PKS-SPB'
            when p.contract_type = 3 then 'PKHOA' end as contract_type2,
            s.`stockpile_name`,p.*,DATE_FORMAT(p.entry_date, '%d %b %Y %h:%m:%s') AS entry_date,
            DATE_FORMAT(c.entry_date, '%d %b %Y' ) AS input_date,
            CASE WHEN p.ppn = 1 THEN 'INCLUDE' ELSE 'EXCLUDE' END AS ppn,
            CASE WHEN p.freight = 1 THEN 'INCLUDE' ELSE 'EXCLUDE' END freight,
            DATE_FORMAT(p.admin_input, '%d %b %Y %h:%m:%s') AS admin_input,
            p.purchasing_id as id,
            p.price as harga,
            p.reject_note,
            pp.contract_no,
            CASE WHEN pd.purchasing_detail_id IS NOT NULL THEN pd.quantity_payment
                    ELSE p.quantity  END AS quantity_termin, p.isTermin, pd.termin
            FROM purchasing p
            LEFT JOIN stockpile s ON s.`stockpile_id`=p.`stockpile_id`
            LEFT JOIN vendor v ON v.`vendor_id`=p.`vendor_id`
            LEFT JOIN contract c on c.contract_id = p.contract_id
            LEFT JOIN purchasing_detail pd ON pd.purchasing_id = p.purchasing_id
            LEFT JOIN po_pks pp on pp.purchasing_id = p.purchasing_id
        WHERE pd.purchasing_detail_id = {$purchasingDetailId}";
$resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);




?>
<input type="hidden" name="modalPurchasingDetailId" id="modalPurchasingDetailId" value="<?php echo $purchasingDetailId; ?>" />
<input type="hidden" name="action" id="action" value="approve_contract_termin" />
<div class="row-fluid">
    <div class="span12 lightblue">
        <table class="table table-bordered table-striped" id="contentTable" style="font-size: 9pt;">
            <thead>
                <tr>


                    <th>Number</th>
                    <th>Stockpile</th>
                    <th>Contract Type</th>
                    <th>Vendor Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Termin</th>
                    <th>Quantity Termin</th>
                    <th>PPN</th>
                    <th>Freight</th>
                    <th>Entry Date</th>


                </tr>
            </thead>
            <tbody>
                <?php
                if ($resultData !== false && $resultData->num_rows > 0) {
                    while ($rowData = $resultData->fetch_object()) {

                        $upload_file = $rowData->import2 != '' ? $rowData->import2 : $rowData->upload_file;
                        $contractNo = $rowData->contract_no;
                        ?>
                        <tr>
                            <td style="text-align: center"><?php echo $rowData->id; ?></td>
                            <td><?php echo $rowData->stockpile_name; ?></td>
                            <td><?php echo $rowData->contract_type2; ?></td>
                            <td><?php echo $rowData->vendor_name; ?></td>
                            <td>
                                <div style="text-align: right;"><?php echo number_format($rowData->harga, 2, ".", ","); ?></div>
                            </td>
                            <td>
                                <div style="text-align: right;"><?php echo number_format($rowData->quantity, 2, ".", ","); ?></div>
                            </td>
                            <td>Termin - <?php echo $rowData->termin; ?></td>
                            <td>
                                <div style="text-align: right;"><?php echo number_format($rowData->quantity_termin, 2, ".", ","); ?></div>
                            </td>
                            <td><?php echo $rowData->ppn; ?></td>
                            <td><?php echo $rowData->freight; ?></td>
                            <td><?php echo $rowData->entry_date; ?></td>
                        </tr>
                    <?php

                        }
                    } else {
                        ?>
                    <tr>
                        <td colspan="7">
                            No data to be shown.
                        </td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="span12 lightblue">
        <iframe src="<?php echo $upload_file; ?>" style="width:95%; height:250px;"></iframe>
    </div>
</div>