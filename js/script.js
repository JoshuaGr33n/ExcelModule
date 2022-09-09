
// ##################### Generate Cert Number #######
$(document).ready(function () {
  $("#gen_cert_form").on('submit', function (ev) {
    ev.preventDefault();
    $.ajax({
      url: "endpoints/gen-cert-endpoint.php",
      type: "POST",
      data: new FormData($("#gen_cert_form")[0]),
      dataType: 'json',
      processData: false,
      contentType: false,
      success: function (data) {
        if (data.status === 1) {
          document.getElementById('gen_cert_form').reset();

          var resp = '<tr>' +
            '<td data-label="S/N"> </td>' +
            '<td data-label="CERT NUMBER">' + data.cert_no + '</td>' +
            '<td>' + data.created_at + '</td>' +
            '<td><a href = "cert.php?id='+ data.id +'" class="btn btn-success btn-sm" > View Certificate</a>'+
              ' <span class="delete" data-id="'+ data.id +'" style="cursor:pointer"><button class="btn btn-danger btn-sm">Delete</button></span></td>' +
          '</tr>';
          $("tbody").prepend(resp);
          setTimeout(function () {
            window.location.reload(1);
          }, 3000);
          sendSuccessResponse('Success', data.message);


        } else {
          sendErrorResponse('Error', data.message);
          $.alert({
            title: 'Error!',
            content: data.message,
            type: 'red',
            typeAnimated: true,
          });
        }
      },
      error: function (errData) { },
      complete: function () {
      }
    });
  });
});



// ##################### Delete Certificate #######

$(document).ready(function () {

  // Delete 
  $('.delete').click(function () {
    var el = this;

    // Delete id
    var deleteid = $(this).data('id');

    $.confirm({
      title: 'WARNING!',
      content: 'Deleteing certificate number erases all certificate items within. Are you sure you want to do this?',
      buttons: {
        Yes: {
          text: 'Yes',
          btnClass: 'btn-danger',
          action: function () {
            // AJAX Request
            $.ajax({
              url: 'endpoints/delete-cert-endpoint.php',
              type: 'POST',
              data: {
                id: deleteid
              },
              success: function (response) {

                if (response == 1) {
                  // Remove row from Table
                  $(el).closest('tr').css('background', 'red');
                  $(el).closest('tr').fadeOut(800, function () {
                    $(this).remove();
                  });
                } else {
                  alert('Invalid Selection.');
                }

              }

            });
            setInterval('location.reload()', 1000);
          }
        },
        cancel: function () {

        }
      }
    });

  });

});





// ##################### Import Script #######
$(document).ready(function () {

  $('#import_Form').on('submit', function (event) {
    event.preventDefault();
    $.ajax({
      url: "endpoints/import-endpoint.php",
      method: "POST",
      data: new FormData(this),
      contentType: false,
      processData: false,
      success: function (data) {
        $(".close").trigger("click");
        $('#wrapper').html(data);
        $('#import').val('');
        // setInterval('location.reload()', 3000);

      }
    });
  });
});


// ##################### Print #######
function printPageArea(areaID) {
  var printContent = document.getElementById(areaID);
  var WinPrint = window.open('', '', 'width=900,height=650');
  WinPrint.document.write(printContent.innerHTML);
  WinPrint.document.close();
  WinPrint.focus();
  WinPrint.print();
  WinPrint.close();
}



