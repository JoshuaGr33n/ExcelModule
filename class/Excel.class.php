<?php
require('database.php');
class Excel
{
    public $conn;

    //constructor
    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function gen_cert_number($certificate_no, $created_at)
    {
        $query = "INSERT INTO excelmodule_cert_number SET 
                    cert_number=?,
                    created_at=?";
        $obj = $this->conn->prepare($query);
        $obj->bind_param("ss", $certificate_no, $created_at);
        if ($obj->execute()) {
            return true;
        }
        return false;
    }

    public function get_cert_number_list()
    {
        $get_list_query = "SELECT * FROM excelmodule_cert_number ORDER BY created_at DESC";
        $get_cert_number_list_obj = $this->conn->prepare($get_list_query);

        if ($get_cert_number_list_obj->execute()) {
            return $get_cert_number_list_obj->get_result();
        }
        return array();
    }

    public function last_inserted($cert_number)
    {
        $get_cert_details_query = "SELECT * FROM excelmodule_cert_number WHERE cert_number = '$cert_number'";
        $get_cert_details_obj = $this->conn->prepare($get_cert_details_query);

        if ($get_cert_details_obj->execute()) {
            $data = $get_cert_details_obj->get_result();
            return $data->fetch_assoc();
        }
    }

    public function get_cert_details($id)
    {
        $get_cert_details_query = "SELECT * FROM excelmodule_cert_number WHERE id = '$id'";
        $get_cert_details_obj = $this->conn->prepare($get_cert_details_query);

        if ($get_cert_details_obj->execute()) {
            $data = $get_cert_details_obj->get_result();
            return $data->fetch_assoc();
        }
    }

    public function check_id_exist($id)
    {
        $checkRecord_query = "SELECT * FROM excelmodule_cert_number WHERE id=?";
        $checkRecord_obj = $this->conn->prepare($checkRecord_query);
        $checkRecord_obj->bind_param("i", $id);
        if ($checkRecord_obj->execute()) {
            $data = $checkRecord_obj->get_result();
            return $data->fetch_assoc();
        }
    }

    public function delete_certificate($cert_number)
    {

        $delete_cert_number_query = "DELETE FROM excelmodule_cert_number WHERE cert_number = '$cert_number'";
        $delete_cert_number_obj = $this->conn->prepare($delete_cert_number_query);

        $delete_cert_items_query = "DELETE FROM excelmodule WHERE certificate_no = '$cert_number'";
        $delete_cert_items_obj = $this->conn->prepare($delete_cert_items_query);

        if ($delete_cert_number_obj->execute() && $delete_cert_items_obj->execute()) {
            if ($delete_cert_number_obj->affected_rows > 0 && $delete_cert_items_obj->affected_rows > 0) {

                return true;
            }
            return false;
        }
        return false;
    }

    public function get_list($cert_number)
    {
        $get_list_query = "SELECT * FROM excelmodule WHERE certificate_no = '$cert_number'";
        $get_list_obj = $this->conn->prepare($get_list_query);

        if ($get_list_obj->execute()) {
            return $get_list_obj->get_result();
        }
        return array();
    }

    public function import_lists($items, $amount, $premium, $certificate_no, $policy_no, $certificate_type, $location_from, $location_to, $period_of_voyage, $total_sum_insured, $total_premium, $company, $created_at)
    {
        $temp_query = "INSERT INTO excelmodule SET 
                    items=?,
                    amount=?,premium=?,
                    certificate_no=?,policy_no=?,certificate_type=?,
                    location_from=?,location_to=?, period_of_voyage=?,
                    total_sum_insured=?,total_premium=?,company=?,
                    created_at=?";
        $temp_obj = $this->conn->prepare($temp_query);
        $temp_obj->bind_param("sddssssssddss", $items, $amount, $premium, $certificate_no, $policy_no, $certificate_type, $location_from, $location_to, $period_of_voyage, $total_sum_insured, $total_premium, $company, $created_at);
        if ($temp_obj->execute()) {
            return true;
        }
        return false;
    }

    public function sum_amounts($list)
    {
        $sum = 0;
        foreach ($list as $key => $value) {
            if (isset($value['amount']))
                $sum += $value['amount'];
        }
        return $sum;
    }

    public function sum_premium($list)
    {
        $sum = 0;
        foreach ($list as $key => $value) {
            if (isset($value['premium']))
                $sum += $value['premium'];
        }
        return $sum;
    }

    public function get_secondary_values($list2)
    {
        foreach ($list2 as $key => $value) {
            $query = "SELECT * FROM excelmodule WHERE certificate_no=? AND certificate_type NOT IN('')";
            $obj = $this->conn->prepare($query);
            $obj->bind_param("s", $value['certificate_no']);
            if ($obj->execute()) {
                $data = $obj->get_result();
                return $data->fetch_assoc();
            }
        }
    }
}

function customError($errno, $errstr)
{
    echo '<div class="alert alert-danger" role="alert" style="width:50%; margin:auto;"><b>Error ' . $errno . ':</b>  ' . $errstr . '</div>';
    die();
}
$excel = new Excel();
