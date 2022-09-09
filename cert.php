<?php
include('class/Excel.class.php');
$cert = $excel->get_cert_details($_GET['id']);
$list = $excel->get_list($cert['cert_number']);
$list2 = $excel->get_list($cert['cert_number']);
$secondary_values = $excel->get_secondary_values($list2);

if (empty($_GET['id'])) :
    header("Location:index.php");
    exit;
endif;

$check_id_exist = $excel->check_id_exist($_GET['id']);
if (empty($check_id_exist)) :
    header("Location:index.php");
    exit;
endif;

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Excel Module</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" type="text/css" href="css/style.css">

    <style>

    </style>
</head>

<body>


    <div id="wrapper">

        <!-- Button to Open the Modal -->
        <button type="button" class="btn btn-success btn-sm " data-toggle="modal" data-target="#myModal">
            Import SpreadSheet
        </button>

        <a href="javascript:void(0);" onclick="printPageArea('print_area')" class="btn btn-secondary btn-sm ">Print</a>
        <a href="index.php" class="btn btn-primary btn-sm ">Back</a>

        <!-- The Modal -->
        <div class="modal fade " id="myModal">
            <div class="modal-dialog">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">Import</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <form method="post" id="import_Form" action="endpoint.php" enctype="multipart/form-data">
                            <p> <select name="policy_no">
                                    <option value="">Select Policy Number</option>
                                    <option value="AI/GIT/0237/2020/LV">AI/GIT/0237/2020/LV</option>
                                </select></p>

                            <p><input type="file" id="import" name="import" accept=".csv, .xlsx, .xls" Required />
                                <span>.csv, .xlsx, .xls files ONLY</span>
                            </p>

                            <input type="hidden" name="cert_number" value="<?= $cert['cert_number']; ?>" />

                            <p><button type="submit" class="btn btn-success" id="submit">Import</button></p>
                        </form>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger close-modal" data-dismiss="modal">Close</button>
                    </div>

                </div>
            </div>
        </div>

        <div id="print_area">

            <div id="head1">
                <div id="head1Left"><img src="img/logo.png" style="width:400px;" /></div>
                <div id="head1Right">
                    <p style="color:red;"><strong>Certificate NO: <?= $cert['cert_number']; ?></strong></p>
                    <p style="color:green"><strong>POLICY NO: <?= $secondary_values["policy_no"]; ?></strong></p>
                    <p style="color:blue"><strong>GIT_PIN: AIC_GIT359644933</strong></p>
                    <p><i style="color: blue;"><strong>Certificate Type:</i> <span style="color:green"><?= $secondary_values["certificate_type"]; ?></strong></p>
                    <p><strong>Date Created: <?= date('D d M Y H:i:s A', strtotime($secondary_values["created_at"])); ?></strong></p>
                </div>
            </div>
            <div id="head2"><img src="img/certificate_of_insurance.png" width="100%" /></div>
            <div id="head3">
                <?php (empty($secondary_values["company"]) ?  $company = "No Company specified" : $company = $secondary_values["company"]); ?>
                This is to Certify That Messers: <span style="color:green"><strong><?= ucwords($company); ?></strong></span> have effected with this Company a
                Goods in <br />Transit Policy under which has been declared the specified and valued as below:
            </div>
            <table>
                <thead>
                    <tr>

                        <th>S/N</th>
                        <th>ITEMS</th>
                        <th class="align_th">SUM INSURED ₦</th>
                        <th>PREMIUM ₦</th>

                    </tr>
                </thead>
                <tbody>
                    <?php if ($list->num_rows > 0) {
                        $n = 1;
                        while ($row = $list->fetch_assoc()) {
                            ($row['amount'] == 0 ?  $amount = "" : $amount = number_format($row['amount'], 2));
                            ($row['premium'] == 0 ?  $premium = "" : $premium = number_format($row['premium'], 2));
                    ?>
                            <tr>

                                <td data-label="S/N"><?= $n; ?></td>
                                <td data-label="ITEMS"><?= $row['items']; ?></td>
                                <td data-label="SUM INSURED" class="align_td"><?= $amount; ?></td>
                                <td data-label="PREMIUM" class="align_td"><?= $premium; ?></td>

                            </tr>

                    <?php $n++;
                        }
                    } ?>
                    <tr>
                        <td data-label="S/N"></td>
                        <td data-label="ITEMS"><strong>Total</strong></td>
                        <td data-label="SUM INSURED" class="align_td"><?= number_format($excel->sum_amounts($list), 2); ?></td>
                        <td data-label="PREMIUM" class="align_td"><?= number_format($excel->sum_premium($list), 2); ?></td>

                    </tr>

                </tbody>
            </table>
            <div id="footer">
                <p><strong>MODE OF CONVEYANCE:</strong> SEA USING BARGE</p>
                <p><strong>LOCATION FROM: </strong><?= $secondary_values["location_from"]; ?></p>
                <p><strong>LOCATION TO: </strong><?= $secondary_values["location_to"]; ?></p>
                <p><strong>TOTAL SUM INSURED:</strong> ₦ <?= number_format($excel->sum_amounts($list), 2); ?></p>
                <p><strong>TOTAL PREMIUM:</strong> ₦ <?= number_format($excel->sum_premium($list), 2); ?></p>
                <p><strong>PERIOD OF VOYAGE:</strong> <?= $secondary_values["period_of_voyage"]; ?> (AS PER POLICY PERIOD)</p>
                <p><strong>CERTIFICATE NO:</strong> <?= $cert['cert_number']; ?></p>
            </div>
        </div>
    </div>
</body>

</html>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="js/script.js"></script>