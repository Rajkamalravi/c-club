$('document').ready(function() { 
    $("#donate_form").validate({
    rules: {
        donation_amount:"required",
    },
    messages: {
        donation_amount:"A minimum of $1 is required",
    }, 
    });
});