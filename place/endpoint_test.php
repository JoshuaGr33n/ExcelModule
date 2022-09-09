<?php
include('Excel.class.php');

$head = "";
$body = "";
$footer = "";
$secondary_values = "";
$amount = 0;
$premium = 0;

if (empty($_FILES['import']['name'])) {
    header("Location:index.php");
    exit;
}
$file = pathinfo($_FILES['import']['name']);
if ($file['extension'] === "xlsx") {

    $filename = $file['filename'] . '.csv';
    $_FILES['import']['type'] = "text/csv";
} else {

    $filename = $_FILES['import']['name'];
}

echo $filename;

echo $_FILES['import']['type'];


$policy = htmlspecialchars(strip_tags($_POST['policy_no']));
if (empty($policy)) {
    echo '<div class="alert alert-danger" role="alert" style="width:50%; margin:auto;">Select Policy Number</div>';
} else {

    // $info = pathinfo($_FILES['import']['name']);
    // return $info['filename'] . '.' . $new_extension;


    if (is_array($_FILES)) {
        $file_mimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        if (!empty($filename) && in_array($_FILES['import']['type'], $file_mimes)) {
            if (is_uploaded_file($_FILES['import']['tmp_name'])) {
                $csv_file = fopen($_FILES['import']['tmp_name'], 'r');
                $created_at = date("Y-m-d H:i:s");
                $generate_cert_no = 'LNG' . substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 11);
                $certificate_no = substr_replace($generate_cert_no, '-', 10, 0);
                $rows = 1;

                if (!$certificate_no) {
                    header("Location:index.php");
                    exit;
                }

                while (($column = fgetcsv($csv_file)) !== FALSE) {

                    if ($rows++ != 1) {

                        $items = $column[0];
                        $amount = (float) str_replace(',', '', $column[1]);
                        $premium = (float) str_replace(',', '', $column[2]);
                        $policy_no = $policy;
                        $certificate_type = $column[3];
                        $location_from = $column[4];
                        $location_to = $column[5];
                        $total_sum_insured = 0;
                        $total_premium = 0;
                        $company = $column[6];

                        if (!empty($policy_no)) {

                            $result = $excel->import_lists($items, $amount, $premium, $certificate_no, $policy_no, $certificate_type, $location_from, $location_to, $total_sum_insured, $total_premium, $company, $created_at);

                            if ($result) {
                                $body .=
                                    '
                                <tr>
    
                                <td data-label="S/N"></td>
                                <td data-label="ITEMS">' . $items . '</td>
                                <td data-label="SUM INSURED">' . number_format($amount, 2) . '</td>
                                <td data-label="PREMIUM">' . number_format($premium, 2) . '</td>
    
                               </tr>';
                                $list = $excel->get_list();
                                $list2 = $excel->get_list();
                                $secondary_values = $excel->get_secondary_values($list2);
                            }
                        }
                    }
                }
                fclose($csv_file);
            }
        } else {
            echo '<div class="alert alert-danger" role="alert" style="width:50%; margin:auto;">Wrong Format. File must be in .csv format</div>';
            $list = $excel->get_list();
            $list2 = $excel->get_list();
            $secondary_values = $excel->get_secondary_values($list2);
        }
    }

    $head .= '<div id="head1">
 <div id="head1Left"><img src="logo.png" /></div>
  <div id="head1Right">
    <p style="color:red;"><strong>Certificate NO: ' . $secondary_values["certificate_no"] . '</strong></p>
    <p style="color:green"><strong>POLICY NO: ' . $secondary_values["policy_no"] . '</strong></p>
    <p style="color:blue"><strong>GIT_PIN: AIC_GIT359644933</strong></p>
    <p><i style="color: blue;"><strong>Certificate Type:</i> <span style="color:green">' . $secondary_values["certificate_type"] . '</strong></p>
    <p><strong>Date Created: ' . date('D d M Y H:i:s A', strtotime($secondary_values["created_at"])) . '</strong></p>
  </div>
 </div>
 <div id="head2"><img src="certificate_of_insurance.png" width="100%" /></div> 
  <div id="head3">
    This is to Certify That Messers: <span style="color:green"><strong>' . ucwords($secondary_values["company"]) . '</strong></span> have effected with this Company a
    Goods in Transit Policy under which has been declared the specified and valued as below:
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
    <p><strong>PERIOD OF VOYAGE:</strong> ' . date('D d M Y H:i:s A', strtotime($secondary_values["created_at"])) . ' (AS PER POLICY PERIOD)</p>
    <p><strong>CERTIFICATE NO:</strong> ' . $secondary_values["certificate_no"] . '</p>
  </div>';


    echo $head;
    echo $body;
    echo $footer;
}
