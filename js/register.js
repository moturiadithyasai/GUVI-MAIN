
function setSuccessMsg(message) {
    let successMessageContainer = $(".success-msg-cont");
    let successMessage = $(".success-msg");
    successMessage.text(message);
    successMessageContainer.show();
    setTimeout(function () {
        successMessageContainer.hide();
    }, 5000);
}

function setErrorMsg(message) {
    let errorMessageContainer = $(".err-msg-cont");
    let errorMessage = $(".err-msg");
    errorMessage.text(message);
    errorMessageContainer.show();
    setTimeout(function () {
        errorMessageContainer.hide();
    }, 5000);
}

$(document).ready(function () {
    $('#register-form').submit(function (e) {
        console.log("Inside the register js");
        e.preventDefault();
        $.ajax({
            url: './php/register.php',
            method: 'POST',
            data: $('#register-form').serialize(),
            success: function (response) {
                let res = JSON.parse(response);
                console.log(res);
                if (res.status === "success") {
                    setSuccessMsg(
                        res.message + " You Will Be Redirected to login page....."
                    );

                    setTimeout(() => {
                        window.location.href = "../login.html";
                    }, 3000);
                }
                else if (res.status === "user_error") {
                    alert("User already existed! Try login again..")
                    setErrorMsg(res.message);
                }
                else {
                    setErrorMsg(res.message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(errorThrown);
            },
        });
        console.log($('#register-form').serialize());
    });
});
