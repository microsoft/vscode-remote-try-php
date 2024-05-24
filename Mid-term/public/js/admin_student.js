$(document).ready(function () {
    $('.username').on('blur', function () {
        let username = $(this).val();
        let email = username + '@gmail.com';
        $('.email').val(email);
    });
    $('#username_edit').on('blur', function () {
        let username = $(this).val();
        let email = username + '@gmail.com';
        $('#email_edit').val(email);
    });
    $('.delete').on('click', function () {
        let id = $(this).val();
        $('.delete_confirm').attr('href', '/admin/students/delete/' + id);
    });
    $('.edit').on('click', function () {
        let id = $(this).val();
        let route = `${window.location.origin}/api/student/update/`+id;
        let modalIdValue = $('#modal-data').data('modal-id');
        if (id == modalIdValue) {
            $('.point-error').show();
        } else {
            $('.point-error').hide();
        }
        $.ajax({
            type: 'get',
            url: route,
            success: function (data) {
                if(data.user !=null){
                    $('#userName_edit').val(data.user.user.username);
                    $('#id_edit').val(data.user.id);
                    $('#fullname_edit').val(data.user.full_name);
                    $('#email_edit').val(data.user.user.email);
                    $('#date_of_birth_edit').val(data.user.date_of_birth);
                    let avatarUrl = data.user.user.avartar;
                    if(avatarUrl){
                        $('#avatar').attr('src', '/images/'+ avatarUrl);
                    }else{
                        $('#avatar').attr('src', '../logo_user.png');
                    }
                    $('.js-example-basic-single').val(data.user.id_faculty).trigger('change');
                }
            }
        });
    });
});


function showConfirmationModal(id) {
    const modalWrapper = document.getElementById('modalWrapper');
    currentDeleteId = id;
    const deleteForm = document.getElementById('delete-form');
    deleteForm.action = deleteForm.action.replace('__student_id', currentDeleteId);
    const modal = document.getElementById('modal');
    modalWrapper.classList.add('show');
    modal.classList.add('show');
}

function hideConfirmationModal() {
    const modalWrapper = document.getElementById('modalWrapper');
    const modal = document.getElementById('modal');
    modalWrapper.classList.remove('show');
    modal.classList.remove('show');
}
