var signInForm = $("#sign_in_form");
$(document).ready(function() {
    signInForm.validate({
        errorPlacement: function(error, element) {
            error.insertAfter(element.closest(".form-group"));
        }
    });

    signInForm.find(".toggle-pass-btn").on("click", function() {
        let passInput = $(this).closest(".form-group").find("input");
        if (passInput.attr("type") == "password") {
            passInput.attr("type", 'text');
        } else {
            passInput.attr("type", 'password');
        }
    });
});