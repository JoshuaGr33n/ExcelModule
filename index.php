<?php
include('class/Excel.class.php');
$cert_num_list = $excel->get_cert_number_list();
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>

<body>


    <div id="wrapper">

        <form id="gen_cert_form" style="margin-bottom:20px">
            <button type="submit" class="btn btn-success" data-toggle="modal" data-target="#myModal">
                Generate New Certificate Number
            </button>
        </form>


        <table>
            <thead>
                <tr>

                    <th>S/N</th>
                    <th>Cert Number</th>
                    <th></th>
                    <th></th>

                </tr>
            </thead>
            <tbody>
                <?php if ($cert_num_list->num_rows > 0) {
                    $n = 1;
                    while ($row = $cert_num_list->fetch_assoc()) {
                ?>
                        <tr>
                            <td data-label="S/N"><?=$n; ?></td>
                            <td data-label="CERT NUMBER"><?= $row['cert_number']; ?></td>
                            <td><?= date('D d M Y H:i:s A', strtotime($row['created_at'])); ?></td>
                            <td>
                                <a href="cert.php?id=<?= $row['id']; ?>" class="btn btn-success btn-sm">View Certificate</a>
                                <span class="delete" data-id="<?= $row['id']; ?>" style="cursor:pointer"><button class="btn btn-danger btn-sm">Delete</button></span>
                            </td>
                        </tr>
                <?php $n++;
                    }
                } ?>
            </tbody>
        </table>
    </div>

</body>

</html>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js"></script>
<script src="js/script.js"></script>