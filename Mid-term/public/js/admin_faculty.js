$(document).ready(function () {
    $('.edit').on('click', function () {
        let id = $(this).val();
        let route = `${window.location.origin}/api/faculty/update/` + id;
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
                if (data.faculty != null) {
                    $('#name_department_edit').val(data.faculty.name);
                    $('#id_edit').val(data.faculty.id);
                    $('#description_edit').val(data.faculty.description);
                }
            }
        });
    });

    $('.delete').on('click', function () {
        let id = $(this).val();
        // $('.delete_confirm').attr('href', '/admin/faculties/' + id);
    });
    $('.unreg').on('click', function () {
        var id = $(this).val();
        var route = `${window.location.origin}/api/un-register-subjects/` + id;
        console.log(route);
        $.ajax({
            type: 'get',
            url: route,
            success: function (response) {
                var rows = '';
                $.each(response.data, function(index, subject) {
                    rows += '<tr>';
                    rows += '<td style="background-color: #f2f2f2; padding: 10px; text-align: left; border-bottom: 1px solid #ddd;"> '+ subject.id + '</td>';
                    rows += '<td style="background-color: #f2f2f2; padding: 10px; text-align: left; border-bottom: 1px solid #ddd;"> '+ subject.name + ' </td>';
                    rows += '</tr>';
                });
                $("#unreg").html(rows);
            },
            error: function (xhr, status, error) {
                console.log('Error fetching registered subjects:', error);
            }
        });
    });

});


function showConfirmationModal(id) {
    const modalWrapper = document.getElementById('modalWrapper');
    currentDeleteId = id;
    const deleteForm = document.getElementById('delete-form');
    deleteForm.action = deleteForm.action.replace('__faculty_id', currentDeleteId);
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
