<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-type: application/json; charset=UTF-8");
include_once("../class/Excel.class.php");

//delete
if (empty($_POST['id'])) {
    header("Location:../index.php");
    exit;
}


$id = 0;
if (isset($_POST['id'])) {
    $id = htmlspecialchars(strip_tags($_POST['id']));
}
if ($id > 0) {

    // Check record exists
    $checkRecord = $excel->get_cert_details($id);

    if (!empty($checkRecord)) {
       
        $excel->delete_certificate($checkRecord['cert_number']);
        echo 1;
        exit;
    } else {
        echo 0;
        exit;
    }
}

echo 0;
exit;
