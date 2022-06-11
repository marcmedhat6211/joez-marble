const signInForm = $("#sign_in_form");
const signUpForm = $("#sign_up_form");
$(document).ready(function() {
    signInForm.validate({
        errorPlacement: function(error, element) {
            error.insertAfter(element.closest(".form-group"));
        }
    });

    $(".account-form-container .toggle-pass-btn").each(function() {
        $(this).on("click", function () {
            console.log("clickeddd");
            let passInput = $(this).closest(".form-group").find("input");
            if (passInput.attr("type") == "password") {
                passInput.attr("type", 'text');
            } else {
                passInput.attr("type", 'password');
            }
        });
    });
});