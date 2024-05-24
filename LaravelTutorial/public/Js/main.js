$(document).ready(function () {
    $('#btn-login').click(function (e) { 
        e.preventDefault();
        
        const username = $('#username').val();
        const password = $('#password').val();

        // jqajax
        $.ajax({
            type: "POST",
            url: "/api/login",
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            },
            data: {
                username: username,
                password: password
            },
            success: function (response) {
                if (response.status) {
                    window.location.href = '/';
                }
                alert(response.message);
            }
        });
    });

    $('#btn-register').click(function (e) { 
        e.preventDefault();
        
        const username = $('#username').val();
        const password = $('#password').val();
        const confirm_password = $('#confirm-password').val();

        if (password != confirm_password) {
            alert('Nhập lại mật khẩu không chính xác');
            return;
        }

        $.ajax({
            type: "POST",
            url: "/api/register",
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            },
            data: {
                username: username,
                password: password,
            },
            success: function (response) {
                if (response.status) {
                    window.location.href = '/login';
                }
                alert(response.message);
            }
        });
    });
});