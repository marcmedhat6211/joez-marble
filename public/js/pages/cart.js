$(document).ready(function() {
    // Cart incrementor
    const qtyInput = $("input[name=qty]");
    $(".incrementor-style-1 button.plus").on("click", function() {
        let newVal = increment($(this).closest(".incrementor-style-1").find(qtyInput));
        $(this).closest(".incrementor-style-1").find(qtyInput).val(newVal);
        $(this).closest(".incrementor-style-1").find("button.minus").prop("disabled", false);
    });

    $(".incrementor-style-1 button.minus").on("click", function() {
        let newVal = decrement($(this).closest(".incrementor-style-1").find(qtyInput));
        if (newVal) {
            $(this).closest(".incrementor-style-1").find(qtyInput).val(newVal);
        }
        if (newVal == 1) {
            $(this).prop("disabled", true);
        }
    });
});

function increment(element) {
    return parseInt(element.val()) + 1;
}

function decrement(element) {
    let number = parseInt(element.val());
    if (number > 0) {
        return number - 1;
    }

    return false;
}