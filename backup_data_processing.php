<?php
elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'sales_local_data') {
    // <editor-fold defaultstate="collapsed" desc="sales_data">

    $return_value = '';
    $boolNew = false;
    $boolExists = true;
    $boolShipment = true;
    $boolUpdateShipment = false;
    
    $addMessage = "";
    // <editor-fold defaultstate="collapsed" desc="VARIABLE POST DATA">
    $salesId = $myDatabase->real_escape_string($_POST['salesId']);
    $accountId = $myDatabase->real_escape_string($_POST['accountId']);
    $salesNo = $myDatabase->real_escape_string($_POST['salesNo']);
    $shipmentNo = $myDatabase->real_escape_string($_POST['shipmentNo']);
    $salesDate = $myDatabase->real_escape_string($_POST['salesDate']);
    $shipmentDate = $myDatabase->real_escape_string($_POST['shipmentDate']);
    $salesType = $myDatabase->real_escape_string($_POST['salesType']);
    $customerId = $myDatabase->real_escape_string($_POST['customerId']);
    //$customerName = $myDatabase->real_escape_string($_POST['customerName']);
    //$customerAddress = $myDatabase->real_escape_string($_POST['customerAddress']);
    //$npwp = $myDatabase->real_escape_string($_POST['npwp']);
    //$ppn = $myDatabase->real_escape_string($_POST['ppn']);
    //$pph = $myDatabase->real_escape_string($_POST['pph']);
    $stockpileId = $myDatabase->real_escape_string($_POST['stockpileId']);
    $destination = $myDatabase->real_escape_string($_POST['destination']);
    $notes = $myDatabase->real_escape_string($_POST['notes']);
    $currencyId = $myDatabase->real_escape_string($_POST['currencyId']);
    $exchangeRate = str_replace(",", "", $myDatabase->real_escape_string($_POST['exchangeRate']));
    $price = str_replace(",", "", $myDatabase->real_escape_string($_POST['price']));
    $quantity = str_replace(",", "", $myDatabase->real_escape_string($_POST['quantity']));
    $totalShipment = str_replace(",", "", $myDatabase->real_escape_string($_POST['totalShipment']));
    $oldTotalShipment = str_replace(",", "", $myDatabase->real_escape_string($_POST['oldTotalShipment']));
    $bkp_jkp = $myDatabase->real_escape_string($_POST['bkp_jkp']);
    $peb_fp_no = $myDatabase->real_escape_string($_POST['peb_fp_no']);
    $pebDate = $myDatabase->real_escape_string($_POST['pebDate']);
	$vendorLangsir = 0;
    $stockpileLangsir = 0;
	$stockpileContractId = 0;
	
	echo $totalShipment;
	echo $oldTotalShipment;
    //$stockpileContractId = $myDatabase->real_escape_string($_POST['stockpileContractId']);
    //$vendorLangsir = $myDatabase->real_escapzle_string($_POST['vendorLangsir']);
    //$stockpileLangsir = $myDatabase->real_escape_string($_POST['stockpileLangsir']);
    // </editor-fold>
   

    if ($salesId == '') {
        $boolNew = true;
    } else {
        /*if ($totalShipment != $oldTotalShipment) {
            if ($totalShipment < $oldTotalShipment) {
                $boolShipment = false;
            }
        }*/
    }

		if ($salesDate != '' && $salesNo != '' && $salesType != '' && $customerId != '' && $currencyId != '' && $price != '' && $quantity != '' && $totalShipment != '' && $stockpileId != '' && $boolShipment) {

        if ($boolNew) {
            $sql = "SELECT * FROM `sales` WHERE company_id = {$_SESSION['companyId']} AND sales_no = '{$salesNo}'";
            $resultSales = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            if ($resultSales->num_rows > 0) {
                $boolExists = false;
            }
        }

        if ($boolExists) {
            if ($exchangeRate == '') {
                $exchangeRate = 1;
            }

           

            $priceConverted = $price * $exchangeRate;

            //closingDate
            $newSalesDate = implode("-", array_reverse(explode("/", $salesDate)));
            $checkClosingDate = explode('-', closingDate($newSalesDate, 'Sales - Sales Agreement'));
            $boolClosing = $checkClosingDate[0];
            $closingDate = $checkClosingDate[1];

            if (!$boolClosing) {
                $return_value = $closingDate;
                echo $return_value;
            } else {
                if ($boolNew) {
                    $sql = "INSERT INTO `sales` (sales_no, sales_date, sales_type, customer_id, "
                        . "stockpile_id, account_id, destination, notes, currency_id, exchange_rate, price, price_converted, quantity, "
                        . "total_shipment, shipment_date, bkp_jkp, peb_fp_no, peb_fp_date, company_id, entry_by, entry_date, stockpileContractId,vendorLangsir,stockpileLangsir,localSales) VALUES ("
                        . "'{$salesNo}', STR_TO_DATE('{$salesDate}', '%d/%m/%Y'), {$salesType}, "
                        . "{$customerId}, {$stockpileId}, {$accountId}, '{$destination}', '{$notes}', {$currencyId}, {$exchangeRate}, {$price}, "
                        . "{$priceConverted}, {$quantity}, {$totalShipment}, STR_TO_DATE('{$shipmentDate}', '%m/%Y'), '{$bkp_jkp}', '{$peb_fp_no}', STR_TO_DATE('{$pebDate}', '%d/%m/%Y'), {$_SESSION['companyId']}, {$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'), {$stockpileContractId},{$vendorLangsir},{$stockpileLangsir},1)";
                } else {
					
					$sql = "DELETE FROM shipment WHERE sales_id = {$salesId} AND shipment_date IS NULL AND shipment_status = 0";
					$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
					
					$sql = "SELECT COUNT(*) AS total_shipment FROM shipment WHERE sales_id = {$salesId}";
						$resultStockpile = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
						if ($resultStockpile->num_rows == 1) {
							$rowStockpile = $resultStockpile->fetch_object();
							$total_shipment = $rowStockpile->total_shipment;
						}
						if($total_shipment > $totalShipment){
							$totalShipment2 = $total_shipment;
						}else{
							$totalShipment2 = $totalShipment;
						}
						
                    $sql = "UPDATE `sales` sl LEFT JOIN shipment sh ON sl.sales_id = sh.sales_id SET "
                        . "sh.shipment_no = '{$shipmentNo}', "
                        . "sl.sales_no = '{$salesNo}', "
                        . "sl.sales_date = STR_TO_DATE('{$salesDate}', '%d/%m/%Y'), "
                        . "sl.sales_type = {$salesType}, "
                        . "sl.customer_id = {$customerId}, "
                        . "sl.stockpile_id = {$stockpileId}, "
                        . "sl.account_id = {$accountId}, "
                        . "sl.destination = '{$destination}', "
                        . "sl.notes = '{$notes}', "
                        . "sl.currency_id = {$currencyId}, "
                        . "sl.exchange_rate = {$exchangeRate}, "
                        . "sl.price = {$price}, "
                        . "sl.price_converted = {$priceConverted}, "
                        . "sl.quantity = {$quantity}, "
                        . "sl.total_shipment = {$totalShipment}, "
                        //. "sl.stockpileContractId = {$stockpileContractId}, "
                        //. "sl.vendorLangsir = {$vendorLangsir}, "
                        //. "sl.stockpileLangsir = {$stockpileLangsir}, "
                        . "sl.shipment_date = STR_TO_DATE('{$shipmentDate}', '%m/%Y'), "
                        . "sl.bkp_jkp = '{$bkp_jkp}', "
                        . "sl.peb_fp_no = '{$peb_fp_no}', "
                        . "sl.peb_fp_date = STR_TO_DATE('{$pebDate}', '%d/%m/%Y') "
                        . "WHERE sl.sales_id = {$salesId}";
                }
                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
				echo $sql;
                if ($result !== false) {
                    if ($boolNew) {
                        $salesId = $myDatabase->insert_id;
                        $j = 1;
                        $boolUpdateShipment = true;
                    } else {
                        if ($totalShipment > $total_shipment) {
                            $j = $total_shipment + 1;
                            $boolUpdateShipment = true;
                        } else {
                            $addMessage = "";
							//$boolUpdateShipment = true;
                        }

//                    $sql = "DELETE FROM `shipment` WHERE sales_id = {$salesId}";
//                    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                    }

                    if ($boolUpdateShipment) {
                        for ($i = $j; $i <= $totalShipment; $i++) {
                            $shipmentCode = $salesNo . '-' . $i;

                            $sql = "INSERT INTO `shipment` (shipment_code, shipment_no, sales_id, entry_by, entry_date) VALUES ("
                                . "'{$shipmentCode}', '{$shipmentNo}', {$salesId}, {$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
                            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                        }
                    }

                    $return_value = '|OK|Sales Agreement has successfully inserted/updated.' . $addMessage . '|' . $salesId . '|';
                } else {
                    $return_value = '|FAIL|Insert/update sales agreement failed.||';
                    echo $sql;
                }
            }
        } else {
            $return_value = '|FAIL|Sales agreement already exists.||';
        }
    } else {
        if (!$boolShipment) {
            $return_value = '|FAIL|Total shipment cant be less than before.||';
        } else {
            $return_value = '|FAIL|Please fill the required fields.||';
        }
    }

    echo $return_value;
    // </editor-fold>
}elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'transaction_data') {

    // <editor-fold defaultstate="collapsed" desc="transaction_data">

    $return_value = '';
    $boolQuantity = true;

    // <editor-fold defaultstate="collapsed" desc="POST variables">
    $stockpileId = $myDatabase->real_escape_string($_POST['stockpileId']);
    $stockpileContractId = $myDatabase->real_escape_string($_POST['stockpileContractId']);
    $contractPksDetailId = $myDatabase->real_escape_string($_POST['contractPksDetailId']);
    $salesId = $myDatabase->real_escape_string($_POST['salesId']);
    $shipmentId = $myDatabase->real_escape_string($_POST['shipmentId']);
    $transactionDate = $myDatabase->real_escape_string($_POST['transactionDate']);
    $loadingDate = $myDatabase->real_escape_string($_POST['loadingDate']);
    $transactionDate2 = $myDatabase->real_escape_string($_POST['transactionDate2']);
    $vehicleNo = $myDatabase->real_escape_string($_POST['vehicleNo']);
    $vehicleNo2 = $myDatabase->real_escape_string($_POST['vehicleNo2']);
    $unloadingCostId = $myDatabase->real_escape_string($_POST['unloadingCostId']);
    $unloadingDate = $myDatabase->real_escape_string($_POST['unloadingDate']);
    $freightCostId = $myDatabase->real_escape_string($_POST['freightCostId']);
    $handlingCostId = $myDatabase->real_escape_string($_POST['handlingCostId']);
    $permitNo = $myDatabase->real_escape_string($_POST['permitNo']);
    $transactionType = $myDatabase->real_escape_string($_POST['transactionType']);
    $sendWeightRule = str_replace(",", "", $myDatabase->real_escape_string($_POST['sendWeight']));
    $sendWeight2 = str_replace(",", "", $myDatabase->real_escape_string($_POST['sendWeight2']));
    $blWeight = str_replace(",", "", $myDatabase->real_escape_string($_POST['blWeight']));
    $brutoWeight = str_replace(",", "", $myDatabase->real_escape_string($_POST['brutoWeight']));
    $tarraWeight = str_replace(",", "", $myDatabase->real_escape_string($_POST['tarraWeight']));
    $nettoWeight = str_replace(",", "", $myDatabase->real_escape_string($_POST['nettoWeight']));
    $notes = $myDatabase->real_escape_string($_POST['notes']);
    $notes2 = $myDatabase->real_escape_string($_POST['notes2']);
    $driver = $myDatabase->real_escape_string($_POST['driver']);
    $block = $myDatabase->real_escape_string($_POST['block']);
    $tempRSB = $myDatabase->real_escape_string($_POST['rsb']);
    $tempGGL = $myDatabase->real_escape_string($_POST['ggl']);
    $tempRG = $myDatabase->real_escape_string($_POST['rg']);
    $tempUncertified = $myDatabase->real_escape_string($_POST['un']);

    $vendorId = $myDatabase->real_escape_string($_POST['vendorId']);
    $supplierId = $myDatabase->real_escape_string($_POST['supplierId']);
    $laborId = $myDatabase->real_escape_string($_POST['laborId']);
    $isTaxable = $myDatabase->real_escape_string($_POST['isTaxable']);
    $pph = $myDatabase->real_escape_string($_POST['pph']);
    $ppn = $myDatabase->real_escape_string($_POST['ppn']);
    $stockpileCode = '';
    $unitPrice = 0;
    $balanceQuantity = 0;
    $contractId = 0;
    $custTaxId = 'NULL';
    $curahTaxId = 'NULL';
    $ucTaxId = 0;
    $fcTaxId = 0;
    $t_date = str_replace('/', '-', $unloadingDate);
    $t_date2 = str_replace('/', '-', $transactionDate2);
    $currentYear2 = date('y', strtotime($t_date));
    $currentYear3 = date('y', strtotime($t_date2));
	
    $slipUpload = $myDatabase->real_escape_string($_POST['idSuratTugas']);
	
    $persenPecahSlip = $myDatabase->real_escape_string($_POST['persenPecahSlip']);
    $tempTransactionId = $myDatabase->real_escape_string($_POST['tempTransactionId']);
	//== Start Add by Eva
    $newAmountClaim = $myDatabase->real_escape_string($_POST['newAmountClaim']);
	$qtyAddShrink = $myDatabase->real_escape_string($_POST['qtyAddShrink']);
	$priceAddShrink = $myDatabase->real_escape_string($_POST['priceAddShrink']);
    //== End Add by Eva
	// update by idris
	$photoDocument = $_POST['photoDocument'];
    // Base64 Decode
    $img = $photoDocument;
    $img = str_replace('data:image/jpeg;base64,', '', $img);
    $img = str_replace(' ', '+', $img);
    $file = base64_decode($img);
    $photoDocument = addslashes($file);
    //photoTicket
    $photoTicket = $_POST['photoTicket'];
    $img = $photoTicket;
    $img = str_replace('data:image/jpeg;base64,', '', $img);
    $img = str_replace(' ', '+', $img);
    $file = base64_decode($img);
    $photoTicket = addslashes($file);
	
	$freightCostSalesId = $myDatabase->real_escape_string($_POST['freightCostSalesId']);
	$vehicleNoSales = $myDatabase->real_escape_string($_POST['vehicleNoSales']);
	$sim = $myDatabase->real_escape_string($_POST['sim']);
	$driverSales = $myDatabase->real_escape_string($_POST['driverSales']);
	$doSales = $myDatabase->real_escape_string($_POST['doSales']);
	
	
    // </editor-fold>


    $allowInsert = false;
    $allowInsert2 = false;
    $date = new DateTime();
    $todayDate = $date->format('Y-m-d');
    $date1 = new DateTime($todayDate);

    $dateTrans = str_replace("/", "-", $myDatabase->real_escape_string($_POST['unloadingDate']));
    $dt = date('Y-m-d', strtotime($dateTrans));
    $date2 = new DateTime($dt);
    $a = $date1->format('Y-m-d');
    $b = $date2->format('Y-m-d');
    $diff = date_diff(date_create($a), date_create($b));
    $interval = $diff->days;

    $dateTrans2 = str_replace("/", "-", $myDatabase->real_escape_string($_POST['transactionDate2']));
    $dt2 = date('Y-m-d', strtotime($dateTrans2));
    $date22 = new DateTime($dt2);
    $a2 = $date1->format('Y-m-d');
    $b2 = $date22->format('Y-m-d');
    $diff2 = date_diff(date_create($a2), date_create($b2));
    $interval2 = $diff2->days;

    if ($interval < 5) {
        $allowInsert = true;
    }

    if ($interval2 < 5) {
        $allowInsert2 = true;
    }

    $sql = "SELECT * FROM user_module WHERE user_id = {$_SESSION['userId']} and module_id = 30";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            if ($row->module_id == 30) {
                $allowInsert = true;
                $allowInsert2 = true;
            }
        }
    }

    if ($isTaxable == '') {
        $isTaxable = 0;
    }

    if ($ppn == '') {
        $ppn = 0;
    }

    if ($pph == '') {
        $pph = 0;
    }

    if ($contractPksDetailId == '') {
        $contractPksDetailId = 0;
    }


    if ($transactionType == 1) {
        if ($allowInsert) {
            // <editor-fold defaultstate="collapsed" desc="IN">
            if ($stockpileId != '' && $stockpileContractId != '' && $loadingDate != '' && $vehicleNo != '' && $unloadingCostId != '' &&
                $unloadingDate != '' && $freightCostId != '' && $handlingCostId != '' && $transactionType != '' && $sendWeightRule >= 0 &&
                $brutoWeight >= 0 && $tarraWeight >= 0 && $nettoWeight >= 0 && $driver != '' && $vendorId != '' && $laborId != '') {

                if ($supplierId == '') {
                    $supplierId = "NULL";
                }
                if ($slipUpload == '') {
                    $slipUpload = 0;
                }
                // check balance contract & get unit price
                $sql = "SELECT s.stockpile_code, con.price_converted, sc.contract_id, con.contract_type, con.qty_rule, con.quantity_rule,
			DATE_FORMAT(con.entry_date,'%Y-%m-%d') AS contractDate,
                        ((SELECT COALESCE(SUM(quantity), 0) FROM stockpile_contract WHERE contract_id = sc.contract_id
                        ) - (SELECT COALESCE(SUM(adjustment), 0) FROM contract_adjustment WHERE contract_id = sc.contract_id
                        )) - (
                            SELECT CASE WHEN c.contract_type = 'C' THEN COALESCE(SUM(t.quantity), 0)
                ELSE COALESCE(SUM(t.send_weight), 0) END 
                FROM TRANSACTION t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.`stockpile_contract_id`
                LEFT JOIN contract c ON c.contract_id = sc.contract_id
                WHERE sc.contract_id =  con.contract_id
                        ) AS balance, s.freight_weight_rule, s.curah_weight_rule, v.pph_tax_id
                    FROM stockpile_contract sc
                    INNER JOIN stockpile s
                        ON s.stockpile_id = sc.stockpile_id
                    INNER JOIN contract con
                        ON con.contract_id = sc.contract_id
                    INNER JOIN vendor v
                        ON v.vendor_id = con.vendor_id
                    WHERE stockpile_contract_id = {$stockpileContractId}";
                $resultStockpile = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                if ($resultStockpile !== false && $resultStockpile->num_rows == 1) {
                    $rowStockpile = $resultStockpile->fetch_object();
                    $stockpileCode = $rowStockpile->stockpile_code;
                    $contractId = $rowStockpile->contract_id;
                    $unitPrice = $rowStockpile->price_converted;
                    $contractType = $rowStockpile->contract_type;
                    $freightWeightRule = $rowStockpile->freight_weight_rule;
                    $curahWeightRule = $rowStockpile->curah_weight_rule;
					$quantityRule = $rowStockpile->quantity_rule;
                    $contractDate = $rowStockpile->contractDate;
                    if ($contractType == 'P') {
                        $balanceQuantity = $rowStockpile->balance;
                    } elseif ($contractType == 'C') {
                        $balanceQuantity = $rowStockpile->balance;
                        if ($rowStockpile->pph_tax_id != 0 && $rowStockpile->pph_tax_id != '') {
                            $curahTaxId = $rowStockpile->pph_tax_id;
                        }
                    }
                    $qty_rule = $rowStockpile->qty_rule;
                    if ($qty_rule == 0) {
                        $sendWeight = $sendWeightRule;
                    } else if ($qty_rule == 1) {
                        $sendWeight = $nettoWeight;
                    } else {
                        if ($sendWeightRule < $nettoWeight) {
                            $sendWeight = $sendWeightRule;
                        } elseif ($nettoWeight < $sendWeightRule) {
                            $sendWeight = $nettoWeight;
                        } else {
                            $sendWeight = $sendWeightRule;
                        }

                    }
                }

                // get netto weight
                //$nettoWeight = $brutoWeight - $tarraWeight;

                // get freight weight
                $freightRule = '';
                $freightQuantity = 0;
                $sqlfc = "SELECT fc.freight_id, fc.price_converted, f.pph_tax_id, f.freight_rule
                            FROM `freight_cost` fc 
                            INNER JOIN freight f
                                ON f.freight_id = fc.freight_id
                            WHERE fc.freight_cost_id = {$freightCostId}";
                $resultFreight = $myDatabase->query($sqlfc, MYSQLI_STORE_RESULT);
                if ($resultFreight !== false && $resultFreight->num_rows == 1) {
                    $rowFreight = $resultFreight->fetch_object();
                    $freightRule = $rowFreight->freight_rule;


                }

                if ($freightRule == 0 && $nettoWeight < $sendWeight) {
                    $freightQuantity = $nettoWeight;
                } else if ($freightRule == 0 && $nettoWeight > $sendWeight) {
                    $freightQuantity = $sendWeight;
                } else if ($freightRule == 2) {
                    $freightQuantity = $nettoWeight;
                } else if ($freightRule == 1) {
                    $freightQuantity = $sendWeight;
                } else if ($nettoWeight == $sendWeight) {
                    $freightQuantity = $sendWeight;
                } else {
                    $freightQuantity = $sendWeight;
                }

                /*if($freightRule == 1) {
				$freightQuantity = $sendWeight;
            }else if($freightRule == 0) {
                if($nettoWeight < $sendWeight) {
                    $freightQuantity = $nettoWeight;
                } else if($nettoWeight > $sendWeight) {
                    $freightQuantity = $sendWeight;
                } else {
                    $freightQuantity = $sendWeight;
                }
            }*/ /*elseif($freightWeightRule == 1) {
                $freightQuantity = $sendWeight;
            } elseif($freightWeightRule == 2) {
                $freightQuantity = $nettoWeight;
            } else {
                $freightQuantity = $sendWeight;
            }*/

                // get shrink condition
//            $quantity = $nettoWeight;
//            $sql = "SELECT * FROM `condition` WHERE category_id = 1 AND contract_id = {$contractId}";
//            $resultCondition = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
//            if($resultCondition !== false && $resultCondition->num_rows == 1) {
//                $rowCondition = $resultCondition->fetch_object();
//                $quantity = ${$rowCondition->rule};
//            }
                if ($contractDate <= $b) {
                    if ($sendWeight <= $balanceQuantity || ($contractType == 'C' && $balanceQuantity >= 0)) {
                        // get shrink
                        $shrink = 0;
                        if ($contractType == 'P') {
                            if ($nettoWeight < $sendWeight) {
                                $quantity = $nettoWeight;
                                $shrink = $sendWeight - $nettoWeight;
                            } elseif ($nettoWeight > $sendWeight) {
                                $quantity = $sendWeight;
                                $shrink = 0;
                            } else {
                                $quantity = $sendWeight;
                            }
                        } elseif ($contractType == 'C') {
                            if ($quantityRule == 0) {
                                if ($nettoWeight < $sendWeight) {
                                    $quantity = $nettoWeight;
                                    $shrink = $sendWeight - $nettoWeight;
                                } elseif ($nettoWeight > $sendWeight) {
                                    $quantity = $sendWeight;
                                    $shrink = 0;
                                } else {
                                    $quantity = $sendWeight;
                                }
                            } elseif ($quantityRule == 1) {
								$quantity = $sendWeight;
                                if ($nettoWeight > $sendWeight) {
                                    $shrink = 0;
                                } elseif ($nettoWeight < $sendWeight){
									$shrink = $sendWeight - $nettoWeight;
								}
                            } elseif ($quantityRule == 2) {
                                $quantity = $nettoWeight;
                                $shrink = $sendWeight - $nettoWeight;
                            } else {
                                $quantity = $sendWeight;
                            }
                        }

                        // get next slip no
                        // $checkSlipNo = $stockpileCode /*.'-'. $currentYear*/;

                        $checkSlipNo = $stockpileCode . '-' . $currentYear2;

//                $sql = "SELECT LPAD(COUNT(1) + 1, 10, '0') AS next_id FROM transaction WHERE slip_no LIKE '{$checkSlipNo}%' ";
//                $resultSlip = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                        $sql = "SELECT LPAD(RIGHT(slip_no, 10) + 1, 10, '0') AS next_id FROM transaction WHERE company_id = {$_SESSION['companyId']} AND slip_no LIKE '{$checkSlipNo}%' ORDER BY transaction_id DESC LIMIT 1";
                        $resultSlip = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                        if ($resultSlip->num_rows == 0) {
                            $sql = "SELECT LPAD(1, 10, '0') AS next_id FROM dual";
                            $resultSlip = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                        }
                        $rowSlipNo = $resultSlip->fetch_object();
                        $nextSlipNo = $rowSlipNo->next_id;
                        $slipNo = $checkSlipNo . '-' . $nextSlipNo;

                        // get freight cost
                        if ($freightCostId == 'NONE') {
                            $freightCostId = 'NULL';
                            $freightPrice = 0;
                            //$freightQuantity2111 = 0;
                        } else {
                            $sqlF = "SELECT fc.freight_id, fc.price_converted, f.pph_tax_id, f.freight_rule
                            FROM `freight_cost` fc 
                            INNER JOIN freight f
                                ON f.freight_id = fc.freight_id
                            WHERE fc.freight_cost_id = {$freightCostId}";
                            $resultF = $myDatabase->query($sqlF, MYSQLI_STORE_RESULT);
                            if ($resultF !== false && $resultF->num_rows == 1) {
                                $rowF = $resultF->fetch_object();
                                $freightPrice = $rowF->price_converted;
                                if ($rowF->freight_rule == 1) {
                                    //$freightQuantity2111 = $sendWeight;
                                } else {
                                    //$freightQuantity2111 = $freightQuantity;
                                }
                                if ($rowF->pph_tax_id != 0 && $rowF->pph_tax_id != '') {
                                    $fcTaxId = $rowF->pph_tax_id;
                                } else {
                                    $fcTaxId = 0;
                                }
                            }
                        }

                        // get handling cost
                        if ($handlingCostId == 'NONE') {
                            $handlingCostId = 'NULL';
                            $handlingQuantity = 0;
                            $handlingPrice = 0;
                        } else {
                            $sql = "SELECT vhc.vendor_handling_id, vhc.price_converted, vh.pph_tax_id, vh.vendor_handling_rule
                            FROM `vendor_handling_cost` vhc 
                            INNER JOIN vendor_handling vh
                                ON vh.vendor_handling_id = vhc.vendor_handling_id
                            WHERE vhc.handling_cost_id = {$handlingCostId}";
                            $resultHandling = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                            if ($resultHandling !== false && $resultHandling->num_rows == 1) {
                                $rowHandling = $resultHandling->fetch_object();
                                $handlingPrice = $rowHandling->price_converted;
                                if ($rowHandling->vendor_handling_rule == 1) {
                                    $handlingQuantity = $sendWeight;
                                } else {
                                    $handlingQuantity = $nettoWeight;
                                }

                            }
                        }

                        // get unloading cost
                        if ($unloadingCostId == 'NONE') {
                            $unloadingCostId = 'NULL';
                            $unloadingPrice = 0;
                        } else {
                            $sql = "SELECT uc.price_converted 
                            FROM `unloading_cost` uc 
                            WHERE uc.unloading_cost_id = {$unloadingCostId}";
							//echo " YE " . $sql;
                            $resultUnloading = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                            if ($resultUnloading !== false && $resultUnloading->num_rows == 1) {
                                $rowUnloading = $resultUnloading->fetch_object();

                                $unloadingPrice2 = $rowUnloading->price_converted;


                                $sql2 = "SELECT l.laborRules FROM `labor` l WHERE l.labor_id = {$laborId}";
                                $resultLabor2 = $myDatabase->query($sql2, MYSQLI_STORE_RESULT);
                                if ($resultLabor2 !== false && $resultLabor2->num_rows == 1) {
                                    $rowLabor2 = $resultLabor2->fetch_object();
                                    if ($rowLabor2->laborRules == 1) {
                                        $unloadingPrice = $unloadingPrice2;
                                    } elseif ($rowLabor2->laborRules == 2) {

                                        if ($nettoWeight < $sendWeight) {
                                            $unloadingPrice = $unloadingPrice2 * $nettoWeight;
                                        } elseif ($nettoWeight > $sendWeight) {
                                            $unloadingPrice = $unloadingPrice2 * $sendWeight;
                                        } else {
                                            $unloadingPrice = $unloadingPrice2 * $nettoWeight;
                                        }

                                    } elseif ($rowLabor2->laborRules == 3) {
                                        $unloadingPrice = $unloadingPrice2 * $nettoWeight;
                                    } elseif ($rowLabor2->laborRules == 4) {
                                        $unloadingPrice = $unloadingPrice2 * $sendWeight;
                                    } else {
                                        $unloadingPrice = $unloadingPrice2;
                                    }
                                } else {
                                    $unloadingPrice = $unloadingPrice2;
                                }
                            }
                        }
						
						$boolLabor = true;
						if($unloadingPrice > 0 && $laborId == 'NONE') {
							$boolLabor = false;
						}


                        if ($laborId == 'NONE') {
                            $laborId = 'NULL';
                        } else {
                            $sql = "SELECT l.pph_tax_id
                            FROM `labor` l
                            WHERE l.labor_id = {$laborId}";
                            $resultLabor = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                            if ($resultLabor !== false && $resultLabor->num_rows == 1) {
                                $rowLabor = $resultLabor->fetch_object();
                                if ($rowLabor->pph_tax_id != 0 && $rowLabor->pph_tax_id != '') {
                                    $ucTaxId = $rowLabor->pph_tax_id;
                                } else {
                                    $ucTaxId = 0;
                                }
                            }
                        }

                        // get inventory value
                        $inventoryValue = ($freightQuantity * $freightPrice) + $unloadingPrice + ($quantity * $unitPrice);

                        //check double input
                        $sqlTiket2 = "SELECT pecah_slip FROM transaction_timbangan WHERE transaction_id = '{$slipUpload}'";
                        $resultTiket2 = $myDatabase->query($sqlTiket2, MYSQLI_STORE_RESULT);
                        if ($resultTiket2 !== false && $resultTiket2->num_rows == 1) {
                            $rowTiket2 = $resultTiket2->fetch_object();
                            $pecah_slip = $rowTiket2->pecah_slip;
                            //$boolTiket = false;


                        }

                        $boolTiket = true;
                        if ($slipUpload != '' && $slipUpload != 'NULL' && $slipUpload != 0 && $pecah_slip == 0) {

                            $sqlTiket = "SELECT COUNT(*) as tiket,COALESCE(SUM(persen_pecah_slip),0) AS persen FROM transaction WHERE notim_status = 0 AND t_timbangan = '{$slipUpload}'";
                            $resultTiket = $myDatabase->query($sqlTiket, MYSQLI_STORE_RESULT);
                            if ($resultTiket !== false && $resultTiket->num_rows == 1) {
                                $rowTiket = $resultTiket->fetch_object();


                                if ($rowTiket->tiket >= 1) {
                                    $boolTiket = false;

                                }
                            }
                        }

                        if ($pecah_slip == 1) {
                            $sqlTiket1 = "SELECT COALESCE(SUM(persen_pecah_slip),0) AS persen FROM transaction WHERE notim_status = 0 AND t_timbangan = '{$slipUpload}'";
                            $resultTiket1 = $myDatabase->query($sqlTiket1, MYSQLI_STORE_RESULT);
                            if ($resultTiket1 !== false && $resultTiket1->num_rows == 1) {
                                $rowTiket1 = $resultTiket1->fetch_object();

                                if ($rowTiket1->persen > 0) {
                                    $persenPecahSlip = 1 - $rowTiket1->persen;
                                }

                            }
                        } else {
                            $persenPecahSlip = 0;
                        }

                        //Check Closing Date
                        $newTransactionDate = implode("-", array_reverse(explode("/", $unloadingDate)));
                        $checkClosingDate = explode('-', closingDate($newTransactionDate, 'Nota Timbang - Input'));
                        $boolClosing = $checkClosingDate[0];
                        $closingDate = $checkClosingDate[1];


                        $sqlChckSlip = "SELECT slip_no FROM TRANSACTION WHERE slip_no LIKE '{$checkSlipNo}%' ORDER BY transaction_id DESC LIMIT 1";
                        $resultChckSlip = $myDatabase->query($sqlChckSlip, MYSQLI_STORE_RESULT);
                        if ($resultChckSlip !== false && $resultChckSlip->num_rows == 1) {
                            $rowChckSlip = $resultChckSlip->fetch_object();

                            $lastChckSlip = $rowChckSlip->slip_no;

                        }
                        if ($lastChckSlip != $slipNo) {
							
							if($boolLabor){
                            if ($boolTiket) {
                                // insert into transaction
                                if ($boolClosing) {
                                    $sql = "INSERT INTO `transaction` (photo_document,photo_ticket,slip_no, t_timbangan, stockpile_contract_id, transaction_date, loading_date, vehicle_no, labor_id, unloading_cost_id, "
                                        . "unloading_date, freight_cost_id, handling_cost_id, permit_no, transaction_type, vendor_id, send_weight, bruto_weight, tarra_weight, "
                                        . "netto_weight, notes, driver, freight_quantity, handling_quantity, quantity, shrink, freight_price, handling_price, unloading_price, unit_price, "
                                        . "inventory_value, block, curah_tax_id, uc_tax_id, fc_tax_id, entry_by, entry_date, contract_pks_detail_id, persen_pecah_slip, rsb, ggl, rsb_ggl, uncertified) "
                                        . "VALUES ('{$photoDocument}','{$photoTicket}','{$slipNo}', '{$slipUpload}', {$stockpileContractId}, STR_TO_DATE('{$unloadingDate}', '%d/%m/%Y'), STR_TO_DATE('{$loadingDate}', '%d/%m/%Y'), "
                                        . "'{$vehicleNo}', {$laborId}, {$unloadingCostId}, STR_TO_DATE('{$unloadingDate}', '%d/%m/%Y'), {$freightCostId}, {$handlingCostId}, '{$permitNo}', "
                                        . "{$transactionType}, {$supplierId}, {$sendWeight}, {$brutoWeight}, {$tarraWeight}, {$nettoWeight}, '{$notes}', "
                                        . "'{$driver}', {$freightQuantity}, {$handlingQuantity}, {$quantity}, {$shrink}, {$freightPrice}, {$handlingPrice}, {$unloadingPrice}, {$unitPrice}, {$inventoryValue}, '{$block}', "
                                        . "{$curahTaxId}, {$ucTaxId}, {$fcTaxId}, {$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'), '{$contractPksDetailId}','{$persenPecahSlip}', {$tempRSB}, {$tempGGL}, {$tempRG}, {$tempUncertified})";
                                    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

                                    if ($result !== false) {
                                        $return_value = '|OK|Transaction has inserted successfully.|';

                                        $transactionId = $myDatabase->insert_id;

                                        // if curah update contract and stockpile_contract quantity
                                    if ($contractType == 'C' && ($contractPksDetailId != '' || $contractPksDetailId != 'NONE')) {
                                        //echo 'TESSSSSTTTTTTTTTTTTTTTTTT';
                                        //echo $contractId;
                                        //echo $contractPksDetailId;
                                        $sql1 = "SELECT contract_pks_detail_id FROM contract_pks_detail WHERE contract_id = {$contractId} AND vendor_curah_id = {$contractPksDetailId}";
                                       $result1 = $myDatabase->query($sql1, MYSQLI_STORE_RESULT);
                                       if ($result1 !== false) {
                                           $row1 = $result1->fetch_object();
                                           $contract_pks_detail_id = $row1->contract_pks_detail_id;
                                           echo $sql1;

                                           if($contract_pks_detail_id == ''){

                                            $sql = "INSERT INTO contract_pks_detail (contract_id,vendor_curah_id,entry_by,entry_date) VALUES ({$contractId},{$contractPksDetailId}, {$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
                                            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                                            $contract_pks_detail_id2 = $myDatabase->insert_id;
                                            echo $sql;

                                            $sql1 = "UPDATE `transaction` SET contract_pks_detail_id = {$contract_pks_detail_id2} WHERE transaction_id = '{$transactionId}'";
                                            $result1 = $myDatabase->query($sql1, MYSQLI_STORE_RESULT);
                                            echo $sql1;

                                           }else{

                                            $sql = "UPDATE `transaction` SET contract_pks_detail_id = {$contract_pks_detail_id} WHERE transaction_id = '{$transactionId}'";
                                            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                                            echo $sql;

                                           }

                                       }


                                        }

                                        $sql1 = "SELECT tt.send_weight,COALESCE(SUM(t.send_weight),0) AS totalSend, tt.`pecah_slip`
										 FROM transaction_timbangan tt 
										 LEFT JOIN TRANSACTION t ON t.t_timbangan = tt.transaction_id 
										 WHERE tt.transaction_id = '{$slipUpload}'";
                                        $result1 = $myDatabase->query($sql1, MYSQLI_STORE_RESULT);
                                        if ($result1 !== false && $result1->num_rows == 1) {
                                            $row1 = $result1->fetch_object();
                                            $totalSend = $row1->totalSend;
                                            if ($row1->totalSend == $row1->send_weight && $row1->pecah_slip == 1) {

                                                $sql = "UPDATE transaction_timbangan SET notim_status = 1 WHERE transaction_id = '{$slipUpload}'";
                                                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                                            } else if ($row1->pecah_slip == 0) {
                                                $sql = "UPDATE transaction_timbangan SET notim_status = 1 WHERE transaction_id = '{$slipUpload}'";
                                                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                                            }

                                        }


                                        $sql = "CALL sp_shrink_weight({$transactionId})";
                                        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
										
										//== Start Add by Eva
                                        $sql_shrink = "SELECT * FROM transaction_shrink_weight 
                                                        WHERE transaction_id = '{$transactionId}'";
                                        $result_shrink = $myDatabase->query($sql_shrink, MYSQLI_STORE_RESULT);
                                        
                                        if($result_shrink !== false && $result_shrink->num_rows == 1){
                                            $rowData = $result_shrink->fetch_object();
                                            $shrink_id = $rowData->shrink_id;
                                            $transaction_id = $rowData->transaction_id;
                                            $stockpile_name = $rowData->stockpile_name;
                                            $send_weight = $rowData->send_weight;
                                            $netto_weight = $rowData->netto_weight;
                                            $susut = $rowData->susut;
                                            $shrink_tolerance_kg = $rowData->shrink_tolerance_kg;
                                            $persen = $rowData->persen;
                                            $weight_persen = $rowData->weight_persen;
                                            $amt_claim = $rowData->amt_claim;
                                            $trx_shrink_tolerance_kg = $rowData->trx_shrink_tolerance_kg;
                                            $trx_shrink_tolerance_persen = $rowData->trx_shrink_tolerance_persen;
                                            $trx_shrink_claim = $rowData->trx_shrink_claim;
											
											
										}else{
											$shrink_id = 0;
                                            $transaction_id = $transactionId;
                                            $stockpile_name = '-';
                                            $send_weight = $sendWeight;
                                            $netto_weight = $nettoWeight;
                                            $susut = $shrink;
                                            $shrink_tolerance_kg = 0;
                                            $persen = 0;
                                            $weight_persen = 0;
                                            $amt_claim = $newAmountClaim;
                                            $trx_shrink_tolerance_kg = 0;
                                            $trx_shrink_tolerance_persen = 0;
                                            $trx_shrink_claim = 0;
										}
                                            //if($newAmountClaim > 0 && $newAmountClaim !=''){
											if($newAmountClaim !='' || $newAmountClaim != null){
												$sqlslb = "INSERT INTO `transaction_additional_shrink` (shrink_id,
																transaction_id, stockpile_name, send_weight,
                                                                netto_weight, susut, shrink_tolerance_kg, persen, weight_persen, price_add_shrink, qty_add_shrink, amt_claim, 
                                                                trx_shrink_tolerance_kg, trx_shrink_tolerance_persen, trx_shrink_claim, update_by, 
                                                                update_date) VALUES ("
                                                                . "'{$shrink_id}','{$transaction_id}','{$stockpile_name}','{$send_weight}',
                                                                '{$netto_weight}','{$susut}','{$shrink_tolerance_kg}','{$persen}','{$weight_persen}','{$priceAddShrink}','{$qtyAddShrink}',
                                                                '{$newAmountClaim}','{$trx_shrink_tolerance_kg}','{$trx_shrink_tolerance_persen}',
                                                                '{$trx_shrink_claim}','{$_SESSION['userId']}', STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
                                                $resultslb = $myDatabase->query($sqlslb, MYSQLI_STORE_RESULT);
												
                                                //$sqlhistory = "INSERT INTO `history_shrink_weight` (shrink_id, transaction_id, //stockpile_name, send_weight,
                                                //                netto_weight, susut, shrink_tolerance_kg, persen, weight_persen, amt_claim, 
                                                //                trx_shrink_tolerance_kg, trx_shrink_tolerance_persen, trx_shrink_claim, update_by, 
                                                //                update_date) VALUES ("
                                                 //               . "'{$shrink_id}','{$transaction_id}','{$stockpile_name}','{$send_weight}',
                                                //                '{$netto_weight}','{$susut}','{$shrink_tolerance_kg}','{$persen}','{$weight_persen}',
                                                //                '{$amt_claim}','{$trx_shrink_tolerance_kg}','{$trx_shrink_tolerance_persen}',
                                                //                '{$trx_shrink_claim}','{$_SESSION['userId']}', STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
                                                //$resulthistory = $myDatabase->query($sqlhistory, MYSQLI_STORE_RESULT);

                                                //if ($resulthistory !== false) {
                                                //    $sql = "UPDATE `transaction_shrink_weight` SET "
                                                //    . "amt_claim = '{$newAmountClaim}'"
                                                //   . "WHERE transaction_id = {$transactionId}";
                                                //}
                                                //$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                                            }
                                        //}
                                        //== End Add by Eva
										
                                        if ($qty_rule !== 0) {
                                            $sql = "INSERT INTO contract_netto (transaction_id, send_weight) VALUES ({$transactionId},{$sendWeightRule})";
                                            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                                        }

                                        insertGeneralLedger($myDatabase, 'NOTA TIMBANG', "NULL", "NULL", "NULL", $transactionId);
										
										insertReportGL($myDatabase, 'NOTA TIMBANG', "NULL", "NULL", "NULL", $transactionId);

                                        unset($_SESSION['transaction']);
                                    } else {
                                        $return_value = '|FAIL|Failed insert transaction.| ' . $sql . '';
                                    }
                                } else {
                                    echo $closingDate;
                                    die();
                                } //tutup else closingDate
                            } else {
                                $return_value = '|FAIL|Tiket timbang sudah diinput.|';
                            }
							}else{
							$return_value = '|FAIL|Unloading Org tidak boleh NONE.|';
					
							}

                        } else {
                            $return_value = '|FAIL|No Slip Sudah Ada.|';
                        }

                    } else {
                        $return_value = '|FAIL|The quantity exceed the balance of the contract.|';
                    }

                } else {
                    $return_value = '|FAIL|Tanggal transaksi harus melebihi tanggal kontrak.|' . $contractDate . '|' . $unloadingDate . '|';
                }
            } else {
                $return_value = '|FAIL|Please fill the required fields.|' . $sql . '';
            }

        } else {
            $return_value = '|FAIL|Tanggal transaksi sudah lebih dari 5 hari.|';
        }
        // </editor-fold>
    } elseif ($transactionType == 2 && $_POST['_method'] == 'INSERT_PREVIEW') {
        // <editor-fold defaultstate="collapsed" desc="OUT">
        $rsb = $myDatabase->real_escape_string($_POST['rsb1']);
        $ggl = $myDatabase->real_escape_string($_POST['ggl1']);
        $rsb_ggl = $myDatabase->real_escape_string($_POST['rsb_ggl']);
        $uncertified1 = $myDatabase->real_escape_string($_POST['uncertified']);

        $msgError_R = $myDatabase->real_escape_string($_POST['qtyRSB_error1']);
        $msgError_G = $myDatabase->real_escape_string($_POST['qtyGGL_error1']);
        $msgError_RG = $myDatabase->real_escape_string($_POST['qtyRG_error1']);
        $msgError_UN = $myDatabase->real_escape_string($_POST['qtyUN_error1']);

        $whereProperty = '';
        $boolean1 = true;

        $qtyRSB = str_replace(",", "", $myDatabase->real_escape_string($_POST['qty_rsb']));
        $qtyGGL = str_replace(",", "", $myDatabase->real_escape_string($_POST['qty_ggl']));
        $qty_RG = str_replace(",", "", $myDatabase->real_escape_string($_POST['qty_RG']));
        $qty_uncertified = str_replace(",", "", $myDatabase->real_escape_string($_POST['qty_uncertified']));
		
		$transactionDate =  $myDatabase->real_escape_string($_POST['transactionDate2']);
        $newtransactionDate2 = implode("-", array_reverse(explode("/", $transactionDate)));
		
		
		
        if($freightCostSalesId != '' && $freightCostSalesId != 'NONE' && $freightCostSalesId != 0){

            $sqlsf = "SELECT a.`freight_rule`, a.`pph_tax_id`, b.price_converted FROM freight_local_sales a
            LEFT JOIN freight_cost_local_sales b ON a.`freight_id` = b.`freight_id`
            WHERE b.`freight_cost_id` = {$freightCostSalesId}";
            $resultSalesf = $myDatabase->query($sqlsf, MYSQLI_STORE_RESULT);
            if ($resultSalesf !== false && $resultSalesf->num_rows == 1) {
                $rowSalesf = $resultSalesf->fetch_object();

                $fcSalesTaxId = $rowSalesf->pph_tax_id;
                $freightSalesPrice = $rowSalesf->price_converted;

                if($rowSalesf->freight_rule == 2){
                        if($sendWeight2 < $blWeight){
                            $freightSalesQty = $sendWeight2;
                        }else if ($sendWeight2 > $blWeight){
                            $freightSalesQty = $blWeight;
                        }else{
                            $freightSalesQty = $blWeight;
                        }
    
                    }else if($rowSalesf->freight_rule == 0){
                        $freightSalesQty = $sendWeight2;
                    }else{
                        $freightSalesQty = $blWeight;
                    }
            }

        }else{
		$freightSalesQty = 0;
		$fcSalesTaxId = 0;
		$freightSalesPrice = 0;
		$freightCostSalesId = 0;
		}


        //JIKA yg dipilih hanya 1 jeni Sertifikat
        if($rsb == 1 && $ggl == 0 && $rsb_ggl == 0 && $uncertified1 == 0){  //RSB
            $whereProperty = "AND t.rsb = 1";
        } else if($ggl == 1 && $rsb == 0 && $rsb_ggl == 0 && $uncertified1 == 0 ){ //GGL
            $whereProperty = "AND t.ggl = 1";
        }else if($rsb_ggl == 1 && $ggl == 0 && $rsb == 0 && $uncertified1 == 0){ //rsb + ggl
            $whereProperty = "AND t.rsb_ggl = 1";
        }else if($uncertified1 == 1 && $ggl == 0 && $rsb_ggl == 0 && $rsb == 0){  //uncertified
            $whereProperty = "AND t.uncertified = 1";
        }
        
        //JIKA yg dipilih hanya 2 jeni Sertifikat
        else if($rsb == 1 && $ggl == 1  && $rsb_ggl == 0  && $uncertified1 == 0){ //rsb, ggl
            $whereProperty = "AND (t.rsb = 1 OR t.ggl = 1)";
        }else if($rsb == 1 && $rsb_ggl == 1  && $ggl == 0  && $uncertified1 == 0){ //rsb. (rsb+ggl)
            $whereProperty = "AND (t.rsb = 1 OR t.rsb_ggl = 1 )";
        }else if($rsb == 1 && $uncertified1 == 1 && $ggl == 0  && $rsb_ggl == 0){  //rsb, uncertified
            $whereProperty = "AND (t.rsb = 1 OR t.uncertified = 1)";
        }else if($ggl == 1 && $rsb_ggl == 1 && $rsb == 0  && $uncertified1 == 0){ // ggl, (rsb+ggl)
            $whereProperty = "AND (t.ggl = 1 OR t.rsb_ggl = 1)";
        }else if($ggl == 1 && $uncertified1 == 1 && $rsb == 0  && $rsb_ggl == 0){ // ggl, uncertified
            $whereProperty = "AND (t.ggl = 1 OR t.uncertified = 1)";
        }else if($rsb_ggl == 1 && $uncertified1 == 1 && $rsb == 0  && $ggl == 0){  // (rsb+ggl), uncertified
            $whereProperty = "AND (t.rsb_ggl = 1 OR t.uncertified = 1)";
        }
        
        //JIKA yg dipilih hanya 3 jeni Sertifikat
        else if($rsb == 1 && $ggl == 1 && $rsb_ggl == 1 && $uncertified1 == 0){  //rsb, ggl, (rsb+ggl)
            $whereProperty = "AND (t.rsb = 1 OR t.ggl = 1 OR t.rsb_ggl = 1)";
        }else if($rsb == 1 && $ggl == 1 &&  $uncertified1 == 1 && $rsb_ggl == 0){ //rsb, ggl, uncertified 
            $whereProperty = "AND (t.rsb = 1 OR t.ggl = 1 OR t.uncertified = 1)";
        }else if($rsb == 1 && $rsb_ggl == 1 && $uncertified1 == 1 && $ggl == 0){  //rsb, (rsb+ggl), uncertified
            $whereProperty = "AND (t.rsb = 1 OR t.rsb_ggl = 1 OR t.uncertified = 1)";
        }else if($ggl == 1 && $rsb_ggl == 1 && $uncertified1 == 1 && $rsb == 0){ //ggl, (rsb+ggl), uncerti
            $whereProperty = "AND ( t.ggl = 1 OR t.rsb_ggl = 1 OR t.uncertified = 1)";
        }
        
        //JIKA yg dipilih hanya semua jeni Sertifikat
        else if($rsb == 1 && $ggl == 1 && $rsb_ggl == 1 && $uncertified1 == 1){  //all
            $whereProperty = "AND (t.rsb = 1 OR t.ggl = 1 OR t.rsb_ggl = 1 OR t.uncertified = 1)";
        }


        //VALIDASI JIKA ADA NOTIM YG BELUM DI APPROVE DI PREVIEW
        $sqlValidasi = "SELECT sl.sales_id, sl.`sales_no`, sh.`shipment_code`, sl.stockpile_id FROM temp_transaction tt
            LEFT JOIN SHIPMENT sh ON sh.shipment_id = tt.`shipment_id`
            LEFT JOIN sales sl ON sl.`sales_id` = sh.`sales_id`
            WHERE tt.`status` = 0 AND sl.`stockpile_id` = {$stockpileId} AND tt.status <> 2";
        $resultValidasi = $myDatabase->query($sqlValidasi, MYSQLI_STORE_RESULT);
        $validasiCount = $resultValidasi->num_rows;

	//VALIDATION QTY INPUTAN < QTY AVAILABLE => UNCERTIFIED ------------------------------------------------------------------------
        $sqlSP = "SELECT stockpile_code FROM stockpile  WHERE stockpile_id = {$stockpileId}";
        $resultSP = $myDatabase->query($sqlSP, MYSQLI_STORE_RESULT);
        $rowSP = $resultSP->fetch_object();
        $stockpileCode = $rowSP->stockpile_code;

        $sql = "SELECT ROUND(SUM( CASE WHEN t.transaction_type = 1 AND (t.uncertified = 1 OR t.uncertified = 0 OR t.uncertified IS NULL) THEN t.quantity 
                WHEN t.transaction_type = 2 AND (t.uncertified = 1 OR t.uncertified = 0 OR t.uncertified IS NULL) THEN -1 * t.quantity ELSE 0 END) -
                SUM(CASE WHEN t.transaction_type = 2  AND (t.uncertified = 1 OR t.uncertified = 0 OR t.uncertified IS NULL) THEN t.shrink ELSE 0 END))
                AS qty_availableUN,
                ROUND(SUM(CASE WHEN t.transaction_type = 2 THEN t.shrink ELSE 0 END),2 ) AS shrink
            FROM `transaction` t
            LEFT JOIN (
                    SELECT d.transaction_id, d.quantity 
                        FROM delivery d 
                        LEFT JOIN TRANSACTION t ON t.transaction_id = d.`transaction_id` 
                        WHERE t.`delivery_status` = 2 
            )
            d ON d.transaction_id = t.`transaction_id`
            WHERE SUBSTRING(t.slip_no,1,3) = '{$stockpileCode}' AND t.transaction_date <=  '{$newtransactionDate2}' 
            ORDER BY t.transaction_date ASC";
            // echo $sql;
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            if($result !== false && $result->num_rows == 1) {
                $row = $result->fetch_object();	
                $beginingUN = $row->qty_availableUN;
            }
        //END VALIDATION UNCERTIFIED---------------------------------------------------------------------------------------------------

        if($validasiCount == 0){
             if($beginingUN >= $qty_uncertified ){
                if ($allowInsert2) {
                    if ($stockpileId != '' && $salesId != '' && $shipmentId != '' && $transactionDate2 != '' && $vehicleNo2 != '' && $sendWeight2 != '' && $blWeight != '') {
                             // check stockpile detail & transaction balance
                        $sql = "SELECT s.stockpile_code, s.stockpile_name, 
                        (
                            SELECT COALESCE(SUM(t.quantity), 0) FROM `transaction` t
                            INNER JOIN stockpile_contract sc ON sc.stockpile_contract_id = t.stockpile_contract_id
                            WHERE sc.stockpile_id = s.stockpile_id
                            AND t.transaction_type = 1 {$whereProperty} 
                        ) AS available_balance,
                        (
                            SELECT COALESCE(SUM(quantity), 0) FROM `transaction` 
                            WHERE shipment_id IN (SELECT shipment_id FROM `shipment` WHERE sales_id = {$salesId})
                            AND transaction_type = 2 
                        ) AS delivered_balance
                    FROM stockpile s
                    WHERE s.stockpile_id = {$stockpileId}";
                // echo $sql;
                    //die();
                    $resultStockpile = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                    if ($resultStockpile !== false && $resultStockpile->num_rows == 1) {
                        $rowStockpile = $resultStockpile->fetch_object();
                        $stockpileCode = $rowStockpile->stockpile_code;
                        $stockpileName = $rowStockpile->stockpile_name;
                        $availableBalance = $rowStockpile->available_balance;
                        $deliveredBalance = $rowStockpile->delivered_balance;
                    }

                        // get sales detail
                        $sql = "SELECT sl.quantity, sl.price_converted, sl.currency_id, sl.exchange_rate, sl.price, cust.pph_tax_id,
                        sl.stockpileContractId, sl.sales_type,sl.stockpileLangsir, sl.vendorLangsir, sl.sales_no, DATE_FORMAT(sales_date,'%Y-%m-%d') AS salesDate
                            FROM sales sl 
                            INNER JOIN customer cust
                                ON cust.customer_id = sl.customer_id
                            WHERE sl.sales_id = {$salesId}";
                        $resultSales = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                        if ($resultSales !== false && $resultSales->num_rows == 1) {
                            $rowSales = $resultSales->fetch_object();
                            $salesBalance = $rowSales->quantity;
                            $salesUnitPrice = $rowSales->price_converted;
                            $salesCurrencyId = $rowSales->currency_id;
                            $salesExchangeRate = $rowSales->exchange_rate;
                            $salesOriginalUnitPrice = $rowSales->price;
                            $custTaxId = $rowSales->pph_tax_id;
                            $stockpileContractIdShipment = $rowSales->stockpileContractId;
                            $salesType = $rowSales->sales_type;
                            $vendorLangsir = $rowSales->vendorLangsir;
                            $salesNo = $rowSales->sales_no;
                            $stockpileLangsir = $rowSales->stockpileLangsir;
                            $salesDate = $rowSales->salesDate;
                        }

                        // get next slip no

                        $checkSlipNo = $stockpileCode . '-' . $currentYear3;

                        //$checkSlipNo = $stockpileCode /*.'-'. $currentYear*/;
        //            $sql = "SELECT LPAD(COUNT(1) + 1, 10, '0') AS next_id FROM transaction WHERE slip_no LIKE '{$checkSlipNo}%' ";
        //            $resultSlip = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                        $sql = "SELECT LPAD(RIGHT(slip_no, 10) + 1, 10, '0') AS next_id FROM transaction WHERE company_id = {$_SESSION['companyId']} AND slip_no LIKE '{$checkSlipNo}%' ORDER BY transaction_id DESC LIMIT 1";
                        $resultSlip = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                        if ($resultSlip->num_rows == 0) {
                            $sql = "SELECT LPAD(1, 10, '0') AS next_id FROM dual";
                            $resultSlip = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                        }
                        $rowSlipNo = $resultSlip->fetch_object();
                        $nextSlipNo = $rowSlipNo->next_id;
                        $slipNo = $checkSlipNo . '-' . $nextSlipNo;

                        if ($salesDate <= $b2) {
        //            if($sendWeight2 <= $availableBalance && $blWeight <= ($salesBalance - $deliveredBalance)) {
                            if ($sendWeight2 <= $availableBalance) {
                                $shrink = $sendWeight2 - $blWeight;
                                //closingDate
                                $newTransactionDate = implode("-", array_reverse(explode("/", $transactionDate2)));
                                $stockpileCD = $stockpileId;
                                $checkClosingDate = explode('-', closingDate($newTransactionDate, 'Nota Timbang - Input'));
                                $boolClosing = $checkClosingDate[0];
                                $closingDate = $checkClosingDate[1];

                                // insert into transaction

                                if ($boolClosing) {
                                    $sql = "INSERT INTO `temp_transaction` (slip_no, shipment_id, transaction_date,unloading_date, vehicle_no, transaction_type, "
                                    . "send_weight, notes, quantity, shrink, cust_tax_id, entry_by, entry_date, ggl, rsb, rsb_ggl, uncertified, freight_cost_id, freight_price, freight_quantity,fc_tax_id,permit_no,driver,sim) "
                                    . "VALUES ('{$slipNo}', {$shipmentId}, STR_TO_DATE('{$transactionDate2}', '%d/%m/%Y'), STR_TO_DATE('{$transactionDate2}', '%d/%m/%Y'), '{$vehicleNo2}', "
                                    . "{$transactionType}, {$sendWeight2}, '{$notes2}', {$blWeight}, {$shrink}, {$custTaxId}, {$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'), {$ggl}, {$rsb}, {$rsb_ggl}, {$uncertified1},{$freightCostSalesId},{$freightSalesPrice},{$freightSalesQty},{$fcSalesTaxId},'{$doSales}','{$driverSales}','{$sim}')";
                                    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                                    echo $sql;
                                    if ($result !== false) {
                                        $tempTransactionId = $myDatabase->insert_id;

                                        $return_value = '|OK|Transaction has inserted successfully.|';

                                        // insert into tempdelivery

                                        if ($stockpileContractIdShipment == 0 || $stockpileContractIdShipment == 'NULL' || $stockpileContractIdShipment == '') {

                                            //SELECT semua data yg berhubungan dengan sertifikat yg di pilih saat input notim OUT
                                            $sqlTest = "SELECT t.* 
                                            FROM transaction t 
                                            WHERE SUBSTR(t.slip_no,1,3) IN (SELECT stockpile_code FROM stockpile WHERE stockpile_id = {$stockpileId}) 
                                                AND t.transaction_type = 1 AND t.delivery_status <> 1 {$whereProperty} AND transaction_date <= '{$newTransactionDate}'
                                            ORDER BY t.unloading_date ASC, t.slip_no ASC";
                                            $resultDelivery = $myDatabase->query($sqlTest, MYSQLI_STORE_RESULT);
                                           echo $sqlTest;
                                            if ($resultDelivery !== false && $resultDelivery->num_rows > 0) {
                                                $balanceLeftUN = $qty_uncertified;
                                                $balanceLeftR = $qtyRSB; //2jt
                                                $balanceLeftG = $qtyGGL; 
                                                $balanceLeftRG = $qty_RG; //1jt
                                                $tempNo = 1;
                                                $totalInventoryValue = 0;
                                                while ($rowDelivery = $resultDelivery->fetch_object()) {
                                                    $syncStatus = 0;
                                                    $tempQtyR = 0;
                                                    $tempQtyG = 0;
                                                    $tempQtyRG = 0;
                                                    $tempQtyUN = 0;
                                                    $quantityTaken = 0;
                                                    $percentTaken = 0;
                                                    $deliveryStatus = 0;
                                                   
                                                    if ($rowDelivery->sync_status == 1) {
                                                        $syncStatus = 2;
                                                    }
                                                     
    
                                                    if ($rowDelivery->delivery_status == 0) { //JIKA NOTIM BELUM DI PAKE
                                                        if($rowDelivery->rsb == 1 && $balanceLeftR > 0) { //RSB
                                                            if ($balanceLeftR >= $rowDelivery->quantity) {
                                                                $balanceLeftR = $balanceLeftR - $rowDelivery->quantity;
                                                                $quantityTaken = $rowDelivery->quantity;
                                                                $percentTaken = 1;
                                                                $deliveryStatus = 1;
                                                                $tempQtyR = $quantityTaken;
                                                                
                                                            } elseif ($balanceLeftR < $rowDelivery->quantity) {
                                                                $quantityTaken = $balanceLeftR;
                                                                $tempQtyR = $quantityTaken;
                                                                $percentTaken = ($quantityTaken / $rowDelivery->quantity) * 1;
                                                                $balanceLeftR = 0;
                                                                $deliveryStatus = 2;
                                                            }
                                                        }else if($rowDelivery->ggl == 1 && $balanceLeftG > 0) { //GLL
                                                            if ($balanceLeftG >= $rowDelivery->quantity) {
                                                                $balanceLeftG = $balanceLeftG - $rowDelivery->quantity;
                                                                $quantityTaken = $rowDelivery->quantity;
                                                                $percentTaken = 1;
                                                                $deliveryStatus = 1;
                                                                $tempQtyG = $quantityTaken;
                                                            } elseif ($balanceLeftG < $rowDelivery->quantity) {
                                                                $quantityTaken = $balanceLeftG;
                                                                $percentTaken = ($quantityTaken / $rowDelivery->quantity) * 1;
                                                                $balanceLeftG = 0;
                                                                $deliveryStatus = 2;
                                                                $tempQtyG = $quantityTaken;
                                                            }
                                                        }else if($rowDelivery->rsb_ggl == 1 && $balanceLeftRG > 0) { //GLL-RSB
                                                            if ($balanceLeftRG >= $rowDelivery->quantity) {
                                                                $balanceLeftRG = $balanceLeftRG - $rowDelivery->quantity;
                                                                $quantityTaken = $rowDelivery->quantity;
                                                                $percentTaken = 1;
                                                                $deliveryStatus = 1;
                                                                $tempQtyRG = $quantityTaken;
                                                            } elseif ($balanceLeftRG < $rowDelivery->quantity) {
                                                                $quantityTaken = $balanceLeftRG;
                                                                $percentTaken = ($quantityTaken / $rowDelivery->quantity) * 1;
                                                                $balanceLeftRG = 0;
                                                                $deliveryStatus = 2;
                                                                $tempQtyRG = $quantityTaken;
                                                            }
                                                        } else if($rowDelivery->uncertified == 1 && $balanceLeftUN > 0) { //un-certified 
                                                            if ($balanceLeftUN >= $rowDelivery->quantity) { //272.080
                                                                $balanceLeftUN = $balanceLeftUN - $rowDelivery->quantity;
                                                                $quantityTaken = $rowDelivery->quantity;
                                                                $percentTaken = 1;
                                                                $deliveryStatus = 1;
                                                                $tempQtyUN = $quantityTaken;
                                                            } elseif ($balanceLeftUN < $rowDelivery->quantity) {  // 10 < 7820
                                                                $quantityTaken = $balanceLeftUN;
                                                                $percentTaken = ($quantityTaken / $rowDelivery->quantity) * 1;
                                                                $balanceLeftUN = 0;
                                                                $deliveryStatus = 2;
                                                                $tempQtyUN = $quantityTaken;
                                                            }
                                                        }  
                                                    } elseif ($rowDelivery->delivery_status == 2) { //JIKA NOTIM SUDAH DI PAKE TAPI ADA SISA
                                                        $totalTaken = 0;
                                                        $sql = "SELECT SUM(quantity) AS total_taken FROM delivery WHERE transaction_id = {$rowDelivery->transaction_id}";
                                                        $resultTaken = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                                                        if ($resultTaken !== false && $resultTaken->num_rows == 1) {
                                                            $rowTaken = $resultTaken->fetch_object();
                                                            $totalTaken = $rowTaken->total_taken;
                                                        }
                                                        if($rowDelivery->rsb == 1 && $balanceLeftR > 0){ //RSB
                                                            if ($balanceLeftR >= ($rowDelivery->quantity - $totalTaken)) {
                                                                $balanceLeftR = $balanceLeftR - ($rowDelivery->quantity - $totalTaken);
                                                                $quantityTaken = ($rowDelivery->quantity - $totalTaken);
                                                                $percentTaken = ($quantityTaken / $rowDelivery->quantity) * 1;
                                                                $deliveryStatus = 1;
                                                                $tempQtyR = $quantityTaken;
                                                               // echo " QTY TAKEN " . $quantityTaken;
                                                            } elseif ($balanceLeftR < ($rowDelivery->quantity - $totalTaken)) {
                                                                $quantityTaken = $balanceLeftR;
                                                                $percentTaken = ($quantityTaken / $rowDelivery->quantity) * 1;
                                                                $balanceLeftR = 0;
                                                                $deliveryStatus = 2;
                                                                $tempQtyR = $quantityTaken;
                                                            }
                                                        } else if($rowDelivery->ggl == 1 && $balanceLeftG > 0){ //GGL
                                                            if ($balanceLeftG >= ($rowDelivery->quantity - $totalTaken)) {
                                                                $balanceLeftG = $balanceLeftG - ($rowDelivery->quantity - $totalTaken);
                                                                $quantityTaken = ($rowDelivery->quantity - $totalTaken);
                                                                $percentTaken = ($quantityTaken / $rowDelivery->quantity) * 1;
                                                                $deliveryStatus = 1;
                                                                $tempQtyG = $quantityTaken;
                                                            } elseif ($balanceLeftG < ($rowDelivery->quantity - $totalTaken)) {
                                                                $quantityTaken = $balanceLeftG;
                                                                $percentTaken = ($quantityTaken / $rowDelivery->quantity) * 1;
                                                                $balanceLeftG = 0;
                                                                $deliveryStatus = 2;
                                                                $tempQtyG = $quantityTaken;
                                                            }
                                                        }else if($rowDelivery->rsb_ggl == 1 && $balanceLeftRG > 0){ //GGL-RSB
                                                            if ($balanceLeftRG >= ($rowDelivery->quantity - $totalTaken)) {
                                                                $balanceLeftRG = $balanceLeftRG - ($rowDelivery->quantity - $totalTaken);
                                                                $quantityTaken = ($rowDelivery->quantity - $totalTaken);
                                                                $percentTaken = ($quantityTaken / $rowDelivery->quantity) * 1;
                                                                $deliveryStatus = 1;
                                                                $tempQtyRG = $quantityTaken;
                                                            } elseif ($balanceLeftRG < ($rowDelivery->quantity - $totalTaken)) {
                                                                $quantityTaken = $balanceLeftRG;
                                                                $percentTaken = ($quantityTaken / $rowDelivery->quantity) * 1;
                                                                $balanceLeftRG = 0;
                                                                $deliveryStatus = 2;
                                                                $tempQtyRG = $quantityTaken;
                                                            }
                                                        }else if($rowDelivery->uncertified == 1 && $balanceLeftUN > 0){ //un-certified
                                                            if ($balanceLeftUN >= ($rowDelivery->quantity - $totalTaken)) {
                                                                $balanceLeftUN = $balanceLeftUN - ($rowDelivery->quantity - $totalTaken);
                                                                $quantityTaken = ($rowDelivery->quantity - $totalTaken);
                                                                $percentTaken = ($quantityTaken / $rowDelivery->quantity) * 1;
                                                                $deliveryStatus = 1;
                                                                $tempQtyUN = $quantityTaken;
                                                            } elseif ($balanceLeftUN < ($rowDelivery->quantity - $totalTaken)) {
                                                                $quantityTaken = $balanceLeftUN;
                                                                $percentTaken = ($quantityTaken / $rowDelivery->quantity) * 1;
                                                                $balanceLeftUN = 0;
                                                                $deliveryStatus = 2;
                                                                $tempQtyUN = $quantityTaken;
                                                            }
                                                        } 
                                                    }
    
                                                    $deliveryValue = $quantityTaken * $salesUnitPrice;
                                                    $inventoryValue = $percentTaken * $rowDelivery->inventory_value;
                                                    $totalInventoryValue = $totalInventoryValue + $inventoryValue;
                                                    $percentTaken = $percentTaken * 100;
                                                    if($quantityTaken <> 0){
                                                        $sqlTest3 = "INSERT INTO `temp_delivery` (temp_transaction_id, shipment_id, transaction_id, delivery_date, percent_taken, quantity, inventory_value, 
                                                                            delivery_value, entry_by, entry_date, qty_rsb, qty_ggl, qty_rsb_ggl, qty_uncertified, delivery_status, sync_status) VALUES ("
                                                                . "{$tempTransactionId}, {$shipmentId}, {$rowDelivery->transaction_id}, STR_TO_DATE('{$transactionDate2}', '%d/%m/%Y'), {$percentTaken}, "
                                                                . "{$quantityTaken}, {$inventoryValue}, {$deliveryValue}, {$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'), {$tempQtyR}, {$tempQtyG}, {$tempQtyRG}, {$tempQtyUN}, {$deliveryStatus}, {$syncStatus})";
                                                        $result = $myDatabase->query($sqlTest3, MYSQLI_STORE_RESULT);
                                                  }
                                               //     echo "INSERT DELIVERY" . $tempNo . " => " . $balanceLeftUN . " | " . $quantityTaken .  " <> ";
                                                    // $tempNo++;
                                                } 
                                            }
                                        } else {
                                            $sql = "SELECT t.* 
                                                FROM transaction t 
                                                WHERE SUBSTR(t.slip_no,1,3) IN (SELECT stockpile_code FROM stockpile WHERE stockpile_id = {$stockpileId}) 
                                                        AND t.transaction_type = 1 AND t.delivery_status <> 1 {$whereProperty}
                                                ORDER BY t.unloading_date ASC, t.slip_no ASC";
                                            $resultDelivery = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
											echo $sql;
                                            $totalInventoryValue = 0;
                                            if ($resultDelivery !== false && $resultDelivery->num_rows > 0) {
                                                $balanceLeftUN = $qty_uncertified;
                                                $balanceLeftR = $qtyRSB;
                                                $balanceLeftG = $qtyGGL;
                                                $balanceLeftRG = $qty_RG;
                                                while ($rowDelivery = $resultDelivery->fetch_object()) {
                                                    $syncStatus = 0;
                                                    $tempQtyR = 0;
                                                    $tempQtyG = 0;
                                                    $tempQtyRG = 0;
                                                    $tempQtyUN = 0;
                                                    $quantityTaken = 0;
                                                    $percentTaken = 0;
                                                    $deliveryStatus = 0;
                                                    if ($rowDelivery->sync_status == 1) {
                                                        $syncStatus = 2;
                                                    }
    
                                                    if ($rowDelivery->delivery_status == 0) {
                                                        if($rowDelivery->rsb == 1 && $balanceLeft > 0) { //RSB
                                                            if ($balanceLeftR >= $rowDelivery->quantity) {
                                                                $balanceLeftR = $balanceLeftR - $rowDelivery->quantity;
                                                                $quantityTaken = $rowDelivery->quantity;
                                                                $percentTaken = 1;
                                                                $deliveryStatus = 1;
                                                                $tempQtyR = $quantityTaken;
                                                            } elseif ($balanceLeftR < $rowDelivery->quantity) {
                                                                $quantityTaken = $balanceLeftR;
                                                                $percentTaken = ($quantityTaken / $rowDelivery->quantity) * 1;
                                                                $balanceLeftR = 0;
                                                                $deliveryStatus = 2;
                                                                $tempQtyR = $quantityTaken;
                                                            }
                                                        }else if($rowDelivery->ggl == 1 && $balanceLeftG > 0) { //GGL
                                                            if ($balanceLeftG >= $rowDelivery->quantity) {
                                                                $balanceLeftG = $balanceLeftG - $rowDelivery->quantity;
                                                                $quantityTaken = $rowDelivery->quantity;
                                                                $percentTaken = 1;
                                                                $deliveryStatus = 1;
                                                                $tempQtyG = $quantityTaken;
                                                            } elseif ($balanceLeftG < $rowDelivery->quantity) {
                                                                $quantityTaken = $balanceLeftG;
                                                                $percentTaken = ($quantityTaken / $rowDelivery->quantity) * 1;
                                                                $balanceLeftG = 0;
                                                                $deliveryStatus = 2;
                                                                $tempQtyG = $quantityTaken;
                                                            }
                                                        }else if($rowDelivery->rsb_ggl == 1 && $balanceLeftRG > 0) { //GGL-RSB
                                                            if ($balanceLeftRG >= $rowDelivery->quantity) {
                                                                $balanceLeftRG = $balanceLeftRG - $rowDelivery->quantity;
                                                                $quantityTaken = $rowDelivery->quantity;
                                                                $percentTaken = 1;
                                                                $deliveryStatus = 1;
                                                                $tempQtyRG = $quantityTaken;
                                                            } elseif ($balanceLeftRG < $rowDelivery->quantity) {
                                                                $quantityTaken = $balanceLeftRG;
                                                                $percentTaken = ($quantityTaken / $rowDelivery->quantity) * 1;
                                                                $balanceLeftRG = 0;
                                                                $deliveryStatus = 2;
                                                                $tempQtyRG = $quantityTaken;
                                                            }
                                                        }else if($rowDelivery->uncertified == 0 && $balanceLeftUN > 0) { //un-certified
                                                            if ($balanceLeftUN >= $rowDelivery->quantity) {
                                                                $balanceLeftUN = $balanceLeftUN - $rowDelivery->quantity;
                                                                $quantityTaken = $rowDelivery->quantity;
                                                                $percentTaken = 1;
                                                                $deliveryStatus = 1;
                                                                $tempQtyUN = $quantityTaken;
                                                            } elseif ($balanceLeftUN < $rowDelivery->quantity) {
                                                                $quantityTaken = $balanceLeftUN;
                                                                $percentTaken = ($quantityTaken / $rowDelivery->quantity) * 1;
                                                                $balanceLeftUN = 0;
                                                                $deliveryStatus = 2;
                                                                $tempQtyUN = $quantityTaken;
                                                            }
                                                        }
                                                    } elseif ($rowDelivery->delivery_status == 2) {
                                                        $totalTaken = 0;
                                                        $sql = "SELECT SUM(quantity) AS total_taken FROM delivery WHERE transaction_id = {$rowDelivery->transaction_id}";
                                                        $resultTaken = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                                                        if ($resultTaken !== false && $resultTaken->num_rows == 1) {
                                                            $rowTaken = $resultTaken->fetch_object();
                                                            $totalTaken = $rowTaken->total_taken;
                                                        }
                                                        if($rowDelivery->rsb == 1 && $balanceLeft > 0){ //RSB
                                                            if ($balanceLeftR >= ($rowDelivery->quantity - $totalTaken)) {
                                                                $balanceLeftR = $balanceLeftR - ($rowDelivery->quantity - $totalTaken);
                                                                $quantityTaken = ($rowDelivery->quantity - $totalTaken);
                                                                $percentTaken = ($quantityTaken / $rowDelivery->quantity) * 1;
                                                                $deliveryStatus = 1;
                                                                $tempQtyR = $quantityTaken;
                                                            } elseif ($balanceLeftR < ($rowDelivery->quantity - $totalTaken)) {
                                                                $quantityTaken = $balanceLeftR;
                                                                $percentTaken = ($quantityTaken / $rowDelivery->quantity) * 1;
                                                                $balanceLeftR = 0;
                                                                $deliveryStatus = 2;
                                                                $tempQtyR = $quantityTaken;
                                                            }
                                                        }else if($rowDelivery->ggl == 1 &&  $balanceLeftG > 0){ //GGL
                                                            if ($balanceLeftG >= ($rowDelivery->quantity - $totalTaken)) {
                                                                $balanceLeftG = $balanceLeftG - ($rowDelivery->quantity - $totalTaken);
                                                                $quantityTaken = ($rowDelivery->quantity - $totalTaken);
                                                                $percentTaken = ($quantityTaken / $rowDelivery->quantity) * 1;
                                                                $deliveryStatus = 1;
                                                                $tempQtyG = $quantityTaken;
                                                            } elseif ($balanceLeftG < ($rowDelivery->quantity - $totalTaken)) {
                                                                $quantityTaken = $balanceLeftG;
                                                                $percentTaken = ($quantityTaken / $rowDelivery->quantity) * 1;
                                                                $balanceLeftG = 0;
                                                                $deliveryStatus = 2;
                                                                $tempQtyG = $quantityTaken;
                                                            }
                                                        }else if($rowDelivery->rsb_ggl == 1 && $balanceLeftRG > 0 ){ //GGL-RSB
                                                            if ($balanceLeftRG >= ($rowDelivery->quantity - $totalTaken)) {
                                                                $balanceLeftRG = $balanceLeftRG - ($rowDelivery->quantity - $totalTaken);
                                                                $quantityTaken = ($rowDelivery->quantity - $totalTaken);
                                                                $percentTaken = ($quantityTaken / $rowDelivery->quantity) * 1;
                                                                $deliveryStatus = 1;
                                                                $tempQtyRG = $quantityTaken;
                                                            } elseif ($balanceLeftRG < ($rowDelivery->quantity - $totalTaken)) {
                                                                $quantityTaken = $balanceLeftRG;
                                                                $percentTaken = ($quantityTaken / $rowDelivery->quantity) * 1;
                                                                $balanceLeftRG = 0;
                                                                $deliveryStatus = 2;
                                                                $tempQtyRG = $quantityTaken;
                                                            }
                                                        }else if($rowDelivery->uncertified == 1 && $balanceLeftUN > 0){ //un-certified
                                                            if ($balanceLeftUN >= ($rowDelivery->quantity - $totalTaken)) {
                                                                $balanceLeftUN = $balanceLeftUN - ($rowDelivery->quantity - $totalTaken);
                                                                $quantityTaken = ($rowDelivery->quantity - $totalTaken);
                                                                $percentTaken = ($quantityTaken / $rowDelivery->quantity) * 1;
                                                                $deliveryStatus = 1;
                                                                $tempQtyUN = $quantityTaken;
                                                            } elseif ($balanceLeftUN < ($rowDelivery->quantity - $totalTaken)) {
                                                                $quantityTaken = $balanceLeftUN;
                                                                $percentTaken = ($quantityTaken / $rowDelivery->quantity) * 1;
                                                                $balanceLeftUN = 0;
                                                                $deliveryStatus = 2;
                                                                $tempQtyUN = $quantityTaken;
                                                            }
                                                        }
                                                    }
    
                                                    $deliveryValue = $quantityTaken * $salesUnitPrice;
                                                    $inventoryValue = $percentTaken * $rowDelivery->inventory_value;
                                                    $totalInventoryValue = $totalInventoryValue + $inventoryValue;
                                                    $percentTaken = $percentTaken * 100;
    
                                                   if($quantityTaken <> 0){
                                                        $sqlTest3 = "INSERT INTO `temp_delivery` (temp_transaction_id, shipment_id, transaction_id, delivery_date, percent_taken, quantity, inventory_value, 
                                                                        delivery_value, entry_by, entry_date, qty_rsb, qty_ggl, qty_rsb_ggl, qty_uncertified, delivery_status, sync_status) VALUES ("
                                                                    . "{$tempTransactionId}, {$shipmentId}, {$rowDelivery->transaction_id}, STR_TO_DATE('{$transactionDate2}', '%d/%m/%Y'), {$percentTaken}, "
                                                                    . "{$quantityTaken}, {$inventoryValue}, {$deliveryValue}, {$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'), {$tempQtyR}, {$tempQtyG}, {$tempQtyRG}, {$tempQtyUN}, {$deliveryStatus}, {$syncStatus})";
                                                        $result = $myDatabase->query($sqlTest3, MYSQLI_STORE_RESULT);
                                                    
                                                  }
                                                }
                                            }
                                        }

                                        //$updateInventoryValue = ($blWeight/$sendWeight2) * $totalInventoryValue;
                                        $sql = "UPDATE temp_transaction SET inventory_value = {$totalInventoryValue}, unit_price = ({$totalInventoryValue}/send_weight) WHERE temp_transaction_id = {$tempTransactionId}";
                                        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
      
                                        // update shipment
                                        $cogsAmount = $totalInventoryValue;
                                        if ($salesCurrencyId != 1) {
                                            $cogsAmount = $totalInventoryValue / $salesExchangeRate;
                                        }
                                        $invoiceAmount = $blWeight * $salesOriginalUnitPrice;
                                        $sql = "UPDATE shipment SET shipment_date = STR_TO_DATE('{$transactionDate2}', '%d/%m/%Y'), "
                                            . "cogs_amount = {$cogsAmount}, invoice_amount = {$invoiceAmount}, quantity = {$blWeight}, shipment_status = 1 "
                                            . "WHERE shipment_id = {$shipmentId}";
                                     //   echo " dua " . $sql;
                                        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
                                        // update sales
                                        $boolUpdateSales = false;
                                        if ($blWeight == ($salesBalance - $deliveredBalance)) {
                                            $salesStatus = 1;
                                            $boolUpdateSales = true;
                                        } elseif ($blWeight < ($salesBalance - $deliveredBalance)) {
                                            $salesStatus = 2;
                                            $boolUpdateSales = true;
                                        }

                                        if ($boolUpdateSales) {
                                            $sql = "UPDATE sales SET sales_status = {$salesStatus} WHERE sales_id = {$salesId}";
                                            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
                                            $sql = "UPDATE sales SET used_status = 1 WHERE sales_id = {$salesId}";
                                            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                                        }

                               
                                        // insertGeneralLedger($myDatabase, 'NOTA TIMBANG', "NULL", "NULL", "NULL", $transactionId);

                                        // insertReportGL($myDatabase, 'NOTA TIMBANG', "NULL", "NULL", "NULL", $transactionId);

                                        $return_value = '|OK|Transaction has inserted successfully.|';
                                        unset($_SESSION['transaction']);
                                    } else {
                                        $return_value = '|FAIL|Failed insert transaction.|';
										echo $sql;
                                    }
                                } else {
                                    echo $closingDate;
                                    die();
                                } //tutup else closing_date
                            } else {
                                if ($sendWeight2 > $availableBalance) {
                                    $return_value = '|FAIL|Available quantity in ' . $stockpileName . ' is ' . number_format($availableBalance, 4, '.', ',') . ' Kg.|';
                                } elseif ($sendWeight2 > ($salesBalance - $deliveredBalance)) {
                                    $return_value = '|FAIL|Sales agreement balance is ' . ($salesBalance - $deliveredBalance) . ' Kg.|';
                                }
                            }

                        } else {
                            $return_value = '|FAIL|Tanggal transaksi harus melebihi tanggal sales.|' . $salesDate . '|' . $transactionDate2 . '|';
                        }

                    } else {
                        $return_value = '|FAIL|Please fill the required fields.|';
                    }

                } else {
                    $return_value = '|FAIL|Tanggal transaksi sudah lebih dari 5 hari.|';
                }// </editor-fold>
            }else{
                $return_value = '|FAIL|Nilai Inputan RSB/GGL/RSB+GGL/Uncertified Salah.|';
            }
        }else{
            $return_value = '|FAIL|Notim OUT sebelumnya belum di Approve.|';
        }
    }elseif ($transactionType == 2 && $_POST['_method'] == 'INSERT') {

        $tempTransactionId = $myDatabase->real_escape_string($_POST['tempTransactionId']);

     

        $sql = "SELECT SUBSTRING(tt.slip_no, 1, 3) AS spCode, tt.* FROM temp_transaction tt where temp_transaction_id = {$tempTransactionId}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        if ($result !== false && $result->num_rows > 0) {
            $rowData = $result->fetch_object();
            $spCode = $rowData->spCode;
            $rsb_ggl = $rowData->rsb_ggl;
            $rsb = $rowData->rsb;
            $ggl = $rowData->ggl;
            $uncertified = $rowData->uncertified;
            if($rowData->unit_price == ''){
                $unit_price = 'NULL';
            }else{
                $unit_price = $rowData->unit_price;
            }
			
			if($rowData->inventory_value == ''){
                $inventory_value = 0;
            }else{
                $inventory_value = $rowData->unit_price;
            }

            $checkSlipNo = $spCode . '-' . $currentYear3;
            $sql = "SELECT LPAD(RIGHT(slip_no, 10) + 1, 10, '0') AS next_id FROM transaction WHERE company_id = {$_SESSION['companyId']} AND slip_no LIKE '{$checkSlipNo}%' ORDER BY transaction_id DESC LIMIT 1";
            $resultSlip = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            // echo "YY".$sql;
            if ($resultSlip->num_rows == 0) {
                $sql = "SELECT LPAD(1, 10, '0') AS next_id FROM dual";
                $resultSlip = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            }
            $rowSlipNo = $resultSlip->fetch_object();
            $nextSlipNo = $rowSlipNo->next_id;
            $slipNo = $checkSlipNo . '-' . $nextSlipNo;

            $transactionDate2 = $rowData->transaction_date;
            $freightCostSalesId = $rowData->freight_cost_id;
			
			if($freightCostSalesId != '' && $freightCostSalesId != 'NONE' && $freightCostSalesId != 0){

            $sqlsf = "SELECT a.`freight_rule`, a.`pph_tax_id`, b.price_converted FROM freight_local_sales a
            LEFT JOIN freight_cost_local_sales b ON a.`freight_id` = b.`freight_id`
            WHERE b.`freight_cost_id` = {$freightCostSalesId}";
            $resultSalesf = $myDatabase->query($sqlsf, MYSQLI_STORE_RESULT);
            if ($resultSalesf !== false && $resultSalesf->num_rows == 1) {
                $rowSalesf = $resultSalesf->fetch_object();

                $fcSalesTaxId = $rowSalesf->pph_tax_id;
                $freightSalesPrice = $rowSalesf->price_converted;

                if($rowSalesf->freight_rule == 2){
                        if($sendWeight2 < $blWeight){
                            $freightSalesQty = $sendWeight2;
                        }else if ($sendWeight2 > $blWeight){
                            $freightSalesQty = $blWeight;
                        }else{
                            $freightSalesQty = $blWeight;
                        }
    
                    }else if($rowSalesf->freight_rule == 0){
                        $freightSalesQty = $sendWeight2;
                    }else{
                        $freightSalesQty = $blWeight;
                    }
            }

        }else{
		$freightSalesQty = 0;
		$fcSalesTaxId = 0;
		$freightSalesPrice = 0;
		$freightCostSalesId = 'NULL';
		}		

            $sqlT = "INSERT INTO `transaction` (slip_no, shipment_id, transaction_date,unloading_date, vehicle_no, transaction_type, "
                    . "send_weight, notes, quantity, shrink, cust_tax_id, entry_by, inventory_value, entry_date, rsb, ggl, rsb_ggl, uncertified, freight_cost_id, freight_price, freight_quantity,fc_tax_id,permit_no,driver,sim,unit_price) "
                    . "VALUES ('{$slipNo}', {$rowData->shipment_id}, '{$rowData->transaction_date}', '{$rowData->transaction_date}', '{$rowData->vehicle_no}', "
                    . "{$rowData->transaction_type}, {$rowData->send_weight}, '{$rowData->notes}', {$rowData->quantity}, {$rowData->shrink}, {$rowData->cust_tax_id}, {$_SESSION['userId']},{$inventory_value} ,STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'), "
                    . "{$rsb}, {$ggl}, {$rsb_ggl}, {$uncertified},{$freightCostSalesId},{$freightSalesPrice},{$freightSalesQty},{$fcSalesTaxId},'{$doSales}','{$driverSales}','{$sim}','{$unit_price}')";
                $resultT = $myDatabase->query($sqlT, MYSQLI_STORE_RESULT);
                 echo ' TRANSACTION-1 ' . $sqlT;
                
                if ($resultT !== false) {
                    $transactionId = $myDatabase->insert_id;
                
                    $return_value = '|OK|Transaction has inserted successfully.|';

                    $sql = "UPDATE temp_transaction SET transaction_id = {$transactionId}, slip_no = '{$slipNo}', status = 1 WHERE temp_transaction_id = {$tempTransactionId}";
                    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

                    $sqlTD = "SELECT * FROM temp_delivery WHERE temp_transaction_id = {$tempTransactionId}";
                    $resultTD = $myDatabase->query($sqlTD, MYSQLI_STORE_RESULT);
                    if ($resultTD !== false && $resultTD->num_rows > 0) {
                        while ($rowTD = $resultTD->fetch_object()) {
                            $deliveryId1 = $rowTD->temp_delivery_id;

                            $sql = "UPDATE transaction SET delivery_status = {$rowTD->delivery_status}, sync_status = {$rowTD->sync_status} WHERE transaction_id = {$rowTD->transaction_id}";
                            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

                            $sqlDelivery = "INSERT INTO `delivery` (shipment_id, transaction_id, delivery_date, percent_taken, quantity, inventory_value, delivery_value, entry_by, entry_date, "
                                    . "qty_rsb, qty_ggl, qty_rsb_ggl, qty_uncertified) VALUES ("
                                    . "{$rowTD->shipment_id}, {$rowTD->transaction_id}, '{$transactionDate2}', {$rowTD->percent_taken}, "
                                    . "{$rowTD->quantity}, {$rowTD->inventory_value}, {$rowTD->delivery_value}, {$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'), {$rowTD->qty_rsb}, "
                                    . "{$rowTD->qty_ggl}, {$rowTD->qty_rsb_ggl}, {$rowTD->qty_uncertified})";
                            $resultDelivery = $myDatabase->query($sqlDelivery, MYSQLI_STORE_RESULT);
                             echo ' DELIVERY-1 ' . $sqlDelivery;

                            if ($resultDelivery !== false) {
                                $deliveryId_copy = $myDatabase->insert_id;

                                $sql_td = "UPDATE temp_delivery SET delivery_id = {$deliveryId_copy}, status = 1 WHERE temp_delivery_id = {$deliveryId1}";
                                $result = $myDatabase->query($sql_td, MYSQLI_STORE_RESULT);
                            }
                        }
                    }
					
					$sql = "SELECT sl.quantity, sl.price_converted, sl.currency_id, sl.exchange_rate, sl.price, cust.pph_tax_id,
                        sl.stockpileContractId, sl.sales_type,sl.stockpileLangsir, sl.vendorLangsir, sl.sales_no, DATE_FORMAT(sales_date,'%Y-%m-%d') AS salesDate
                            FROM sales sl 
                            INNER JOIN customer cust
                                ON cust.customer_id = sl.customer_id
                            WHERE sl.sales_id = {$salesId}";
                        $resultSales = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                        if ($resultSales !== false && $resultSales->num_rows == 1) {
                            $rowSales = $resultSales->fetch_object();
                            $salesBalance = $rowSales->quantity;
                            $salesUnitPrice = $rowSales->price_converted;
                            $salesCurrencyId = $rowSales->currency_id;
                            $salesExchangeRate = $rowSales->exchange_rate;
                            $salesOriginalUnitPrice = $rowSales->price;
                            $custTaxId = $rowSales->pph_tax_id;
                            $stockpileContractIdShipment = $rowSales->stockpileContractId;
                            $salesType = $rowSales->sales_type;
                            $vendorLangsir = $rowSales->vendorLangsir;
                            $salesNo = $rowSales->sales_no;
                            $stockpileLangsir = $rowSales->stockpileLangsir;
                            $salesDate = $rowSales->salesDate;
                        }
						
						if ($salesType == 3) {

                                    $sql2 = "SELECT d.*,
            CASE WHEN t.transaction_type = 1 THEN s.stockpile_name ELSE s2.stockpile_name END AS stockpile_name,  
            DATE_FORMAT(t.unloading_date, '%d %b %Y') AS transaction_date2,
            t.slip_no, 
            CASE WHEN con.contract_type = 'P' THEN 'PKS' ELSE 'Curah' END AS contract_type2,
            con.po_no, 
            CONCAT(f.freight_code, '-', v2.vendor_code) AS freight_code,
            v3.vendor_name AS supplier,
            v1.vendor_name, 
            sh.shipment_code,
            t.send_weight, t.netto_weight, d.quantity,
			CASE WHEN t.mutasi_id IS NOT NULL AND t.mutasi_id <> 0 THEN t.unit_cost
			WHEN t.adjustmentAudit_id IS NOT NULL AND t.adjustmentAudit_id <> 0 THEN t.unit_price
			ELSE con.price_converted END AS price_converted,
            CASE WHEN t.mutasi_id IS NOT NULL AND t.mutasi_id <> 0 THEN d.quantity * t.unit_cost
			WHEN t.adjustmentAudit_id IS NOT NULL AND t.adjustmentAudit_id <> 0 THEN d.quantity * t.unit_price
			ELSE d.quantity * con.price_converted END AS cogs_amount,
            t.freight_quantity, t.freight_price, 
			CASE WHEN t.delivery_status = 2 THEN (d.percent_taken / 100) * (t.quantity * t.freight_price)
			ELSE (d.percent_taken / 100) * (t.freight_quantity * t.freight_price) END AS freight_total,
			CASE WHEN t.delivery_status = 2 AND t.freight_cost_id IS NOT NULL THEN (d.quantity/t.freight_quantity) * COALESCE((SELECT amt_claim FROM transaction_shrink_weight WHERE transaction_id = d.transaction_id),0)
	    WHEN t.freight_cost_id IS NOT NULL THEN (d.percent_taken / 100) * COALESCE((SELECT amt_claim FROM transaction_shrink_weight WHERE transaction_id = d.transaction_id),0) ELSE 0 END AS freight_shrink,
            t.unloading_price, (d.percent_taken / 100) * t.unloading_price AS unloading_total,
			vhc.price AS vh_price, t.handling_quantity,
			CASE WHEN t.delivery_status = 2 THEN (d.percent_taken / 100) * (t.handling_quantity * vhc.price)
			ELSE (d.percent_taken / 100) * (t.handling_quantity * vhc.price) END AS handling_total,
			vh1.pph_tax_id AS vh_pph_tax_id, vh1.pph AS vh_pph, vhtx.tax_category AS vh_pph_tax_category,
            f.ppn_tax_id AS fc_ppn_tax_id, f.ppn AS fc_ppn, fctxppn.tax_category AS fc_ppn_tax_category,
            t.fc_tax_id AS fc_pph_tax_id, fctxpph.tax_value AS fc_pph, fctxpph.tax_category AS fc_pph_tax_category,
            l.ppn_tax_id AS uc_ppn_tax_id, l.ppn AS uc_ppn, uctxppn.tax_category AS uc_ppn_tax_category,
            l.pph_tax_id AS uc_pph_tax_id, l.pph AS uc_pph, uctxpph.tax_category AS uc_pph_tax_category,
			l.labor_id,t.freight_cost_id, f.freight_id,
			(SELECT slip_no FROM TRANSACTION WHERE notim_status != 1 AND slip_retur IS NULL AND shipment_id = (SELECT shipment_id FROM shipment WHERE sales_id = sl.sales_id ORDER BY shipment_id ASC LIMIT 1)LIMIT 1) AS slipOut,
			(SELECT transaction_date FROM TRANSACTION WHERE notim_status != 1 AND slip_retur IS NULL AND shipment_id = (SELECT shipment_id FROM shipment WHERE sales_id = sl.sales_id ORDER BY shipment_id ASC LIMIT 1)LIMIT 1) AS transactionDate,
			(SELECT SUBSTRING(slip_no,1,3) FROM TRANSACTION WHERE notim_status != 1 AND slip_retur IS NULL AND shipment_id = (SELECT shipment_id FROM shipment WHERE sales_id = sl.sales_id ORDER BY shipment_id ASC LIMIT 1)LIMIT 1) AS slipOutCode,
			(SELECT vehicle_no FROM TRANSACTION WHERE notim_status != 1 AND slip_retur IS NULL AND shipment_id = (SELECT shipment_id FROM shipment WHERE sales_id = sl.sales_id ORDER BY shipment_id ASC LIMIT 1)LIMIT 1) AS vessel_name, sh.shipment_no
                     FROM delivery d
        LEFT JOIN `transaction` t
        	ON t.transaction_id = d.transaction_id
        LEFT JOIN stockpile_contract sc
            ON sc.stockpile_contract_id = t.stockpile_contract_id
        LEFT JOIN stockpile s
            ON s.stockpile_id = sc.stockpile_id
        LEFT JOIN contract con
            ON con.contract_id = sc.contract_id
        LEFT JOIN vendor v1
            ON v1.vendor_id = con.vendor_id
        LEFT JOIN unloading_cost uc
            ON uc.unloading_cost_id = t.unloading_cost_id
        LEFT JOIN vehicle vh
            ON vh.vehicle_id = uc.vehicle_id
        LEFT JOIN freight_cost fc
            ON fc.freight_cost_id = t.freight_cost_id
        LEFT JOIN freight f
            ON f.freight_id = fc.freight_id
        LEFT JOIN vendor v2
            ON v2.vendor_id = fc.vendor_id
        LEFT JOIN vendor v3
            ON v3.vendor_id = t.vendor_id
        LEFT JOIN shipment sh
            ON sh.shipment_id = d.shipment_id
        LEFT JOIN sales sl
            ON sl.sales_id = sh.sales_id
        LEFT JOIN stockpile s2
            ON s2.stockpile_id = sl.stockpile_id
        LEFT JOIN customer cust
            ON cust.customer_id = sl.customer_id
        LEFT JOIN tax fctxpph
	        ON fctxpph.tax_id = t.fc_tax_id
        LEFT JOIN tax fctxppn
	        ON fctxppn.tax_id = f.ppn_tax_id
	    LEFT JOIN labor l
            ON l.labor_id = t.labor_id
	    LEFT JOIN tax uctxpph
	        ON uctxpph.tax_id = l.pph_tax_id
        LEFT JOIN tax uctxppn
	        ON uctxppn.tax_id = l.ppn_tax_id	
		lEFT JOIN vendor_handling_cost vhc
			ON vhc.handling_cost_id = t.handling_cost_id
		LEFT JOIN vendor_handling vh1
			ON vh1.vendor_handling_id = vhc.vendor_handling_id
		LEFT JOIN tax vhtx
			ON vh1.pph_tax_id = vhtx.tax_id
						WHERE 1=1
						AND d.shipment_id = {$shipmentId}";
                                    $result2 = $myDatabase->query($sql2, MYSQLI_STORE_RESULT);
                                    if ($result2->num_rows > 0) {

                                        while ($row2 = $result2->fetch_object()) {
                                            $value = '';
                                            $no1 = 1;

                                            $slipOut = $row2->slipOut;
                                            $slipOutCode = $row2->slipOutCode;

                                            if ($row2->slip_no >= 'SAM-0000000001' && $row2->slip_no <= 'SAM-0000001925') {
                                                $fc_pph2 = 4;
                                            } else {
                                                $fc_pph2 = $row2->fc_pph;
                                            }

                                            if ($row2->slip_no >= 'MAR-0000000001' && $row2->slip_no <= 'MAR-0000007138') {
                                                $fc_pph2 = 4;
                                            } else {
                                                $fc_pph2 = $row2->fc_pph;
                                            }

                                            if ($row2->vh_pph_tax_category == 1 && $row2->vh_pph_tax_id != '') {
                                                $pphvh2 = ($row2->handling_total / ((100 - $row2->vh_pph) / 100)) - $row2->handling_total;

                                            } elseif ($row2->vh_pph_tax_category == 0 && $row2->vh_pph_tax_id != '') {
                                                $pphvh2 = 0;
                                                //$pphfc =  $row->freight_total - ($row->freight_total * ((100 - $fc_pph) / 100));
                                            } else {
                                                $pphvh2 = 0;
                                            }

                                            $handlingTotal2 = $row2->handling_total - $pphvh2;

                                            if ($row2->fc_pph_tax_category == 1 && $row2->fc_pph_tax_id != '') {
                                                $pphfc2 = ($row2->freight_total / ((100 - $fc_pph2) / 100)) - $row2->freight_total;
                                                $pphfcShrink2 = ($row2->freight_shrink / ((100 - $fc_pph2) / 100)) - $row2->freight_shrink;

                                            } elseif ($row2->fc_pph_tax_category == 0 && $row2->fc_pph_tax_id != '') {
                                                $pphfc2 = 0;
                                                $pphfcShrink2 = 0;
                                                //$pphfc =  $row->freight_total - ($row->freight_total * ((100 - $fc_pph) / 100));
                                            } else {
                                                $pphfc2 = 0;
                                                $pphfcShrink2 = 0;
                                            }
                                            /*
									 if($row->fc_ppn_tax_id != ''){
										 $ppnfc = ($row->freight_total * ((100 + $row->fc_ppn) / 100)) - $row->freight_total;
									 }else{
										 $ppnfc = 0;
									 }*/

                                            $freightTotal2 = ($row2->freight_total + $ppnfc2 + $pphfc2) - ($row2->freight_shrink + $pphfcShrink2);


                                            if ($row2->uc_pph_tax_category == 1 && $row2->uc_pph_tax_id != '') {
                                                $pphuc2 = ($row2->unloading_total / ((100 - $row2->uc_pph) / 100)) - $row2->unloading_total;

                                            } elseif ($row2->uc_pph_tax_category == 0 && $row2->uc_pph_tax_id != '') {
                                                $pphuc2 = 0;
                                                //$pphuc =  $row->unloading_total - ($row->unloading_total * ((100 - $row->uc_pph) / 100));
                                            } else {
                                                $pphuc2 = 0;
                                            }


                                            $unloadingTotal2 = $row2->unloading_total + $ppnuc2 + $pphuc2;

                                            $totalCogs2 = $row2->cogs_amount + $freightTotal2 + $unloadingTotal2 + $handlingTotal2;

                                            $quantity_total = $row2->quantity;
                                            $total_quantity = $quantity_total + $total_quantity;

                                            $pks_total = $row2->cogs_amount;
                                            $total_pks = $pks_total + $total_pks;

                                            $fc_total = $freightTotal2;
                                            $total_fc = $fc_total + $total_fc;

                                            $vh_total = $handlingTotal2;
                                            $total_vh = $vh_total + $total_vh;

                                            $uc_total = $unloadingTotal2;
                                            $total_uc = $uc_total + $total_uc;

                                            $cogs_total = $totalCogs2;
                                            $total_cogs = $cogs_total + $total_cogs;

                                            $unitPrice = $total_cogs / $total_quantity;

                                            $no1++;

                                        }
                                    }


                                    $sql = "SELECT * FROM vendor WHERE vendor_id = {$vendorLangsir}";
                                    $resultVendor = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                                    if ($resultVendor !== false && $resultVendor->num_rows == 1) {
                                        $rowVendor = $resultVendor->fetch_object();
                                        $vendorCode = $rowVendor->vendor_code;
                                    }

                                    $checkPoNo = 'P-' . $vendorCode . '-' . $currentYearMonth;
                                    $sql = "SELECT po_no FROM contract WHERE company_id = {$_SESSION['companyId']} AND po_no LIKE '{$checkPoNo}%' ORDER BY contract_id DESC LIMIT 1";
                                    $resultPo = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                                    if ($resultPo->num_rows == 1) {
                                        $rowPo = $resultPo->fetch_object();
                                        $splitPoNo = explode('-', $rowPo->po_no);
                                        $lastExplode = count($splitPoNo) - 1;
                                        $nextPoNo = ((float)$splitPoNo[$lastExplode]) + 1;
                                        $poNo = $checkPoNo . '-' . $nextPoNo;
                                    } else {
                                        $poNo = $checkPoNo . '-1';
                                    }

                                    $unitPriceConverted = $unitPrice * $salesExchangeRate;

                                    $sqlKontrak = "INSERT INTO contract (contract_type, po_no, contract_no, vendor_id, currency_id, exchange_rate, price, price_converted, quantity, payment_status, notes, entry_by, entry_date,langsir,langsir_shipment_id) VALUES ('P','{$poNo}','{$salesNo}',{$vendorLangsir},{$salesCurrencyId},{$salesExchangeRate},{$unitPrice},{$unitPriceConverted}, {$total_quantity}, 1, '-',{$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'),1,{$shipmentId})";
                                    $result = $myDatabase->query($sqlKontrak, MYSQLI_STORE_RESULT);
                                    if ($result !== false) {
                                        $contractId = $myDatabase->insert_id;

                                        $sql = "INSERT INTO stockpile_contract (stockpile_id, contract_id, quantity, entry_by, entry_date) VALUES ("
                                            . "{$stockpileLangsir}, {$contractId}, {$total_quantity}, {$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
                                        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                                        //insertGeneralLedger($myDatabase, 'CONTRACT', "NULL", "NULL", $contractId);
                                        //insertReportGL($myDatabase, 'CONTRACT', "NULL", "NULL", $contractId);
                                    }

                                }
                    
                   insertGeneralLedger($myDatabase, 'NOTA TIMBANG', "NULL", "NULL", "NULL", $transactionId);

                    insertReportGL($myDatabase, 'NOTA TIMBANG', "NULL", "NULL", "NULL", $transactionId);
                }else{
                    $return_value = '|FAIL|Failed insert transaction.| '. $sqlTest3;
                }
        }

    }else if ($_POST['_method'] == 'CANCEL') {
        $cancelRemarks = $myDatabase->real_escape_string($_POST['reject_remarks']);

        $sql_tt = "UPDATE temp_transaction SET status = 2, cancel_remarks = '{$cancelRemarks}' WHERE temp_transaction_id = {$tempTransactionId}";
        $result = $myDatabase->query($sql_tt, MYSQLI_STORE_RESULT);

     

        if ($result != false) {

                $sql = "UPDATE temp_delivery SET status = 2 WHERE temp_transaction_id = {$tempTransactionId}";
                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

                $sqlSh = "SELECT sales_id From shipment where shipment_id = {$shipmentId}";
                $resultSh = $myDatabase->query($sqlSh, MYSQLI_STORE_RESULT);
                $rowSh = $resultSh->fetch_object();
                $salesId = $rowSh->sales_id;

                $sql_shipment = "UPDATE shipment SET shipment_status = 0,
                                    shipment_date = NULL, cogs_amount = 0, invoice_amount = 0, quantity = 0 WHERE shipment_id = {$shipmentId}";
                $result = $myDatabase->query($sql_shipment, MYSQLI_STORE_RESULT);

                $sql_tt = "UPDATE sales SET sales_status = 0, used_status = 0 WHERE sales_id = {$salesId}";
                $result = $myDatabase->query($sql_tt, MYSQLI_STORE_RESULT);

                $return_value = '|OK|Reject  has successfully.|';
        } else {
            $return_value = '|FAIL|Reject  FAIL!|.'.$sql_tt;

        }
    
    }

    echo $return_value;
    // </editor-fold>
}
?>