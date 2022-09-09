<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-type: application/json; charset=UTF-8");

include('../class/Excel.class.php');

if ($_SERVER['REQUEST_METHOD'] === "POST") {

    $created_at = date("Y-m-d H:i:s");
    $generate_cert_no = 'LNG' . substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 11);
    $certificate_no = substr_replace($generate_cert_no, '-', 10, 0);

    $result = $excel->gen_cert_number($certificate_no, $created_at);
    if ($result) {
        $new_cert = $excel->last_inserted($certificate_no);
        http_response_code(200);
        echo json_encode(array("status" => 1, "message" => "Certificate Number Added", "cert_no" => $certificate_no, "created_at" => date('D d M Y H:i:s A', strtotime($created_at)),  "id" => $new_cert['id'] ));
    } else {
        http_response_code(200);
        echo json_encode(array("status" => 0, "message" => "Certificate not Generated"));
    }
} else {
    http_response_code(503);
    echo json_encode(array("status" => 503, "message" => "Access Denied"));
}
