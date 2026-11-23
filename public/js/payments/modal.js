var paymentsUrl = baseUrl + '/payments/'
$('body').on('click', '.transferPayment', function() {
    $('#paymentModal #submit').hide()
    var userId = $(this).attr('data-id')
    console.log(userId);
    $.ajax({
        type: 'get',
        dataType: 'json',
        url: paymentsUrl + userId + '/verify',
        success: function(data) {
            var payments = data.data
            var user = payments.user
            var payment = parseInt(payments.clearedAmount)

            if (isNaN(payment)) {
                toastr.error('This user amount is already transferred!')
            } else {
                $('#sendPayment').attr('action', paymentsUrl + userId + '/send')
                 $('#paymentModal #submit').attr('data-id', userId)
                $('#paymentModal #submit').show()
                $.LoadingOverlay('show')
                $('#paymentModal').modal('show')
                $.LoadingOverlay('hide')
            }
        },
        error: function() {
            $.LoadingOverlay('hide')
        }

    })
})
$('body').on('click', '#submit', function() {
    $('#paymentModal #submit').attr('disable', "on")
    $("#sendPayment").submit();

})


$('#paymentModal #amount').on('change', function() {
    var enteredAmount = parseInt($(this).val())
    var userId = $(this).attr('data-id')
    $.ajax({
        type: 'get',
        dataType: 'json',
        url: paymentsUrl + userId + '/verify',
        success: function(data) {
            var sum = data.data
            var payment = parseInt(sum.amount)
            var user = sum.user

            $('#paymentModal #amount').attr({ 'max': payment })
            if (enteredAmount > payment) {
                $('#paymentModal .load-payments').text(' Earned Amount is  ' + payment + '$ !')
                $('#paymentModal #submit').hide()
            } else {
                $('#paymentModal .load-payments').text('')
                $('#paymentModal #submit').show()
            }
        },
        error: function() {
            $.LoadingOverlay('hide')
        }

    })
})

function ucfirst(str, force) {
    str = force ? str.toLowerCase() : str;
    return str.replace(/(\b)([a-zA-Z])/,
        function(firstLetter) {
            return firstLetter.toUpperCase();
        });
}
