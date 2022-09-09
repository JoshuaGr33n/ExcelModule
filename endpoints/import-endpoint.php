<?php
require('../library/php-excel-reader/excel_reader2.php');
require('../library/SpreadsheetReader.php');
include('../class/Excel.class.php');

$head = "";
$body = "";
$footer = "";
$secondary_values = "";
$amount = 0;
$premium = 0;
$folder = "excel_files/";

if (empty($_FILES['import']['name'])) :
    header("Location:../index.php");
    exit;
endif;
if (empty($_POST['cert_number'])) :
    header("Location:../index.php");
    exit;
endif;

$filename = $_FILES['import']['name'];
$policy = htmlspecialchars(strip_tags($_POST['policy_no']));
$cert_number = htmlspecialchars(strip_tags($_POST['cert_number']));

if (empty($policy)) :
    echo '<div class="alert alert-danger" role="alert" style="width:50%; margin:auto;">Select Policy Number</div>';
else :
    if (is_array($_FILES)) {
        $csv_file_mimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv');
        $excel_file_mimes = array('application/vnd.ms-excel', 'text/xls', 'text/xlsx', 'application/vnd.oasis.opendocument.spreadsheet', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $created_at = date("Y-m-d H:i:s");
        $certificate_no = $cert_number;
        $rows = 1;

        if (!$certificate_no) {
            header("Location:../index.php");
            exit;
        }

        if (!empty($filename) && in_array($_FILES['import']['type'], $csv_file_mimes)) {
            if (is_uploaded_file($_FILES['import']['tmp_name'])) {
                $csv_file = fopen($_FILES['import']['tmp_name'], 'r');

                while (($column = fgetcsv($csv_file)) !== FALSE) {

                    if ($rows++ != 1) {

                        if (empty($column[7])) {
                            $column[7] = '';
                        }

                        $items = $column[0];
                        $amount = (float) str_replace(',', '', $column[1]);
                        $premium = (float) str_replace(',', '', $column[2]);
                        $policy_no = $policy;
                        (isset($column[3]) ?  $certificate_type = $column[3] : '');
                        (isset($column[4]) ?  $location_from = $column[4] : '');
                        (isset($column[5]) ?  $location_to = $column[5] : '');
                        $total_sum_insured = 0;
                        $total_premium = 0;
                        (isset($column[6]) ?  $period_of_voyage = $column[6] : '');
                        (isset($column[7]) ?  $company = $column[7] : '');


                        if (!empty($policy_no)) {
                            $result = $excel->import_lists($items, $amount, $premium, $certificate_no, $policy_no, $certificate_type, $location_from, $location_to, $period_of_voyage, $total_sum_insured, $total_premium, $company, $created_at);
                            if ($result) {
                                $body .=
                                    '<tr>
                                    <td data-label="S/N"></td>
                                    <td data-label="ITEMS">' . $items . '</td>
                                    <td data-label="SUM INSURED">' . number_format($amount, 2) . '</td>
                                    <td data-label="PREMIUM">' . number_format($premium, 2) . '</td>
                                </tr>';
                                $list = $excel->get_list($cert_number);
                                $list2 = $excel->get_list($cert_number);
                                $secondary_values = $excel->get_secondary_values($list2);
                            }
                        }
                    }
                }
                fclose($csv_file);
            }
        } elseif (!empty($filename) && in_array($_FILES['import']['type'], $excel_file_mimes)) {
            $excel_file = $folder . basename($filename);

            set_error_handler("customError", E_USER_WARNING);
            if (!file_exists($folder)) {
                trigger_error($folder.' directory does not exist or has been deleted', E_USER_WARNING);
            }

            move_uploaded_file($_FILES['import']['tmp_name'], $excel_file);

            $Reader = new SpreadsheetReader($excel_file);
            $totalSheet = count($Reader->sheets());


            foreach ($Reader as $column) {
                if ($rows++ != 1) {

                    if (empty($column[7])) {
                        $column[7] = '';
                    }

                    $items = $column[0];
                    $amount = (float) str_replace(',', '', $column[1]);
                    $premium = (float) str_replace(',', '', $column[2]);
                    $policy_no = $policy;
                    (isset($column[3]) ?  $certificate_type = $column[3] : '');
                    (isset($column[4]) ?  $location_from = $column[4] : '');
                    (isset($column[5]) ?  $location_to = $column[5] : '');
                    $total_sum_insured = 0;
                    $total_premium = 0;
                    (isset($column[6]) ?  $period_of_voyage = $column[6] : '');
                    (isset($column[7]) ?  $company = $column[7] : '');
                    

                    

                    if (!empty($policy_no)) {
                        $result = $excel->import_lists($items, $amount, $premium, $certificate_no, $policy_no, $certificate_type, $location_from, $location_to, $period_of_voyage, $total_sum_insured, $total_premium, $company, $created_at);
                        if ($result) {
                            $body .=
                                '<tr>
                                <td data-label="S/N"></td>
                                <td data-label="ITEMS">' . $items . '</td>
                                <td data-label="SUM INSURED">' . number_format($amount, 2) . '</td>
                                <td data-label="PREMIUM">' . number_format($premium, 2) . '</td>
                            </tr>';
                            $list = $excel->get_list($cert_number);
                            $list2 = $excel->get_list($cert_number);
                            $secondary_values = $excel->get_secondary_values($list2);
                            if (file_exists($excel_file)) :
                                unlink($excel_file);
                            endif;
                        }
                    }
                }
            }
        } else {
            echo '<div class="alert alert-danger" role="alert" style="width:50%; margin:auto;">Wrong Format. File must be in .csv, .xlsx, .xls format</div>';
            $list = $excel->get_list($cert_number);
            $list2 = $excel->get_list($cert_number);
            $secondary_values = $excel->get_secondary_values($list2);
        }
    }
    (empty($secondary_values["company"]) ?  $company_name = "No Company specified" : $company_name = $secondary_values["company"]);
    $head .= '<div id="head1">
 <div id="head1Left"><img src="img/logo.png" style="width:400px;"/></div>
  <div id="head1Right">
    <p style="color:red;"><strong>Certificate NO: ' . $cert_number . '</strong></p>
    <p style="color:green"><strong>POLICY NO: ' . $secondary_values["policy_no"] . '</strong></p>
    <p style="color:blue"><strong>GIT_PIN: AIC_GIT359644933</strong></p>
    <p><i style="color: blue;"><strong>Certificate Type:</i> <span style="color:green">' . $secondary_values["certificate_type"] . '</strong></p>
    <p><strong>Date Created: ' . date('D d M Y H:i:s A', strtotime($secondary_values["created_at"])) . '</strong></p>
  </div>
 </div>
 <div id="head2"><img src="img/certificate_of_insurance.png" width="100%" /></div> 
  <div id="head3">
    
    This is to Certify That Messers: <span style="color:green"><strong>' . ucwords($company_name) . '</strong></span> have effected with this Company a
    Goods in <br/>Transit Policy under which has been declared the specified and valued as below:
  </div>
 <table>
   <thead>
    <tr>

        <th>S/N</th>
        <th>ITEMS</th>
        <th>SUM INSURED ₦</th>
        <th>PREMIUM ₦</th>

    </tr>
   </thead>
   <tbody>';

    $footer .= '
        <tr>
           <td data-label="S/N"></td>
           <td data-label="ITEMS"><strong>Total</strong></td>
           <td data-label="SUM INSURED">' . number_format($excel->sum_amounts($list), 2) . '</td>
           <td data-label="PREMIUM">' . number_format($excel->sum_premium($list), 2) . '</td>
        </tr>

    </tbody>
  </table>
  <div id="footer">
    <p><strong>MODE OF CONVEYANCE:</strong> SEA USING BARGE</p>
    <p><strong>LOCATION FROM: </strong> ' . $secondary_values["location_from"] . '</p>
    <p><strong>LOCATION TO: </strong> ' . $secondary_values["location_to"] . '</p>
    <p><strong>TOTAL SUM INSURED:</strong> ₦ ' . number_format($excel->sum_amounts($list), 2) . '</p>
    <p><strong>TOTAL PREMIUM:</strong> ₦ ' . number_format($excel->sum_premium($list), 2) . '</p>
    <p><strong>PERIOD OF VOYAGE:</strong> ' . $secondary_values["period_of_voyage"] . ' (AS PER POLICY PERIOD)</p>
    <p><strong>CERTIFICATE NO:</strong> ' . $cert_number . '</p>
  </div>';


    echo $head;
    echo $body;
    echo $footer;
endif;
