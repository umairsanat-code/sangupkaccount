function isNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode != 46 && charCode > 31 &&
        (charCode < 48 || charCode > 57))
        return false;

    return true;
}

$('.editComission').on('click', function() {
    $('#editCommissionCard').show()
    $('#commissionCard').hide()
})

$('#commissionForm').on('submit', function(e) {
    if ($('#commission').val() <= 0) {
        toastr.error('Please add commission equal or greater then 1%');
        e.preventDefault()
    }
    if ($('#commission').val() > 100) {
        toastr.error('Please add commission equal or less then 100%');
        e.preventDefault()
    }
    return true

})