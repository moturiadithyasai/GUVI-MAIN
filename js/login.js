
function showSuccessMsg(message) {
  let successMessageContainer = $(".success-msg-cont");
  let successMessage = $(".success-msg");
  successMessage.text(message);
  successMessageContainer.show();
  setTimeout(function () {
    successMessageContainer.hide();
  }, 5000);
}

function showErrorMsg(message) {
  let errorMessageContainer = $(".err-msg-cont");
  let errorMessage = $(".err-msg");
  errorMessage.text(message);
  errorMessageContainer.show();
  setTimeout(function () {
    errorMessageContainer.hide();
  }, 5000);
}

$(document).ready(function () {
  $('#Login-form').submit(function (e) {
    console.log("Inside the Login js");
    e.preventDefault();
    $.ajax({
      url: './php/login.php',
      method: 'POST',
      data: $('#Login-form').serialize(),
      success: function (response) {
        let res = JSON.parse(response);
        if (res.status == "success") {
          console.log(res.session_id);
          localStorage.setItem("redisId", res.session_id);

          if (localStorage.getItem("redisId") != null) {
            showSuccessMsg(res.message)
            window.location.href = "../profile.html";
          }
        } else {
          console.log(res.message);
          showErrorMsg(res.message);
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        console.log(errorThrown);
      },
    });
    console.log($('#Login-form').serialize());
  });
});
