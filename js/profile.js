let data;
function setData() {
    console.log("set the data");
    console.log(data);
    $(".display-details")
        .find("p:eq(0)")
        .html("Email: " + data.email);
    $(".display-details")
        .find("p:eq(1)")
        .html("DOB: " + data.dob);
    $(".display-details")
        .find("p:eq(2)")
        .html("Age:" + data.age);
    $(".display-details")
        .find("p:eq(3)")
        .html("Contact:" + data.contact);
}

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
    $("#loading-message").show();
    //check the session is valid or not
    console.log("Checking the session id... yep!");
    $.ajax({
        type: "POST",
        url: "../php/profile.php",
        data: { action: "valid-session", redisId: localStorage.getItem("redisId") },
        success: function (response) {
            let res = JSON.parse(response);

            if (res.status != "success") {
                showErrorMsg(
                    "Login Again to Continue... You Will Be Redirected...."
                );
                setTimeout(() => {
                    window.location.href = "../login.html";
                }, 3000);
            }
        },
    });

    console.log("If exist get the values yep!!");
   
    $.ajax({
        url: "../php/profile.php",
        type: "POST",
        data: { action: "get-data", redisId: localStorage.getItem("redisId") },
        success: function (response) {
            $("#loading-message").hide();
            let res = JSON.parse(response);
            data = res.data[0];
            setData();
        },

        error: function (xhr, status, error) {
            console.log(error);
        },
    });


    $(".editbtn").click(function () {
        $(".display-details").hide();
        $(".edit-details").show();
        $("#email").val(data.email);
        var dateString = data.dob;
        var dateParts = dateString.split("-");
        var dateObj = new Date(dateParts[2], dateParts[1] - 1, dateParts[0]);
        var formattedDate = dateObj.toISOString().slice(0, 10);
        $("#dob").val(formattedDate);
        $("#age").val(data.age);
        $("#contact").val(data.contact);
    });


    $(".cancelbtn").click(function () {
        $(".edit-details").hide();
        $(".display-details").show();
    });

    $(".savebtn").click(function () {
        var email = $("#email").val();
        console.log(email);
        var dob = $("#dob").val();
        console.log(dob);
        var dobArray = dob.split("-");
        dob = dobArray[2] + "-" + dobArray[1] + "-" + dobArray[0];
        var age = $("#age").val();
        var contact = $("#contact").val();

        console.log(dob, age, contact);

        data = { ...data, dob, age, contact };
        setData();
        $.ajax({
            url: "../php/profile.php",
            type: "POST",
            data: { action: "update-data", email, data },
            success: function (response) {
                $("#loading-message").hide();
                console.log(response);
            },

            error: function (xhr, status, error) {
                console.log(error);
            },
        });

        $(".edit-details").hide();
        $(".display-details").show();
    });

    let loading = true;
    $("#logoutbutton").click(function (e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "../php/profile.php",
            data: { action: "logout", redisId: localStorage.getItem("redisId") },
            success: function (response) {
                let res = JSON.parse(response);

                if (res.status == "success") {
                    showSuccessMsg(res.message + " Redirecting to Home page...");

                    setTimeout(function () {
                        window.location.href = "../index.html";
                    }, 3000);
                }
            },
        });

        localStorage.removeItem("redisId");

    });
});