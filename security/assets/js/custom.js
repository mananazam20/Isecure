// document.addEventListener('DOMContentLoaded', function() {
//     var checkboxAttempt = document.getElementById('checkbox-attempt');
//     var dataToToggle = document.getElementById('data-to-toggle');
//     var changeLoginUrl = document.getElementById('change-login-url');
//     var redirectUrl = document.getElementById('redirect_url');

//     checkboxAttempt.addEventListener('click', function() {
//         if (!this.checked) {
//             dataToToggle.style.display = "none";
//         } else {
//             dataToToggle.style.display = "block";
//         }
//     });

//     changeLoginUrl.addEventListener('click', function() {
//         if (!this.checked) {
//             redirectUrl.style.display = "none";
//         } else {
//             redirectUrl.style.display = "block";
//         }
//     });
// });


jQuery(document).ready(function($) {

   
    $('#checkbox-attempt').click(function() {    
        if (!$('#checkbox-attempt').prop('checked')) {
            $('#data-to-toggle').hide();
        } else {
            $('#data-to-toggle').show();
        }
    });

    if ($('#checkbox-attempt').attr('checked') !== undefined) {
        console.log("yes");
        $('#data-to-toggle').show();
    } else {
        console.log("no");
        $('#data-to-toggle').hide();
    }

    $('#change-login-url').click(function() {
        if (!$('#change-login-url').prop('checked')) {
            $('#redirect_url').hide();
        } else {
            $('#redirect_url').show();
        }
    });

    if ($('#change-login-url').attr('checked') !== undefined) {
        console.log("yes");
        $('#redirect_url').show();
    } else {
        console.log("no");
        $('#redirect_url').hide();
    }
});

