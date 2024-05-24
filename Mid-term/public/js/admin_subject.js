$(document).ready(function () {
    $('.edit').on('click', function () {
        let id = $(this).val();
        let student = $('.student_id').val();
        let data = {
            'student_id': student
        };
        let route = `${window.location.origin}/api/point/`+ id;
        $.ajax({
            type: 'get',
            url: route,
            data: data,
            success: function (data) {
                $('#name_subject').val(data.name);
                $('#point').val(data.point.point);
                $('#subject_id').val(data.point.subject_id);
            }
        });
    });
    $('#form_update_point').on('submit', function(event) {
        event.preventDefault();
        let formData = $(this).serialize();
        let route = `${window.location.origin}/admin/point`;
        let studentId = $('.student_id').val();
        $.ajax({
            type: 'post',
            url: route,
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Thêm CSRF token vào header
            },
            success: function (data) {
                if(data.status){
                    location.reload();
                }else{
                    $('#point_error').text(data.errors.point || '');
                    toastr.error("Cập nhật điểm không thành công!", "Lỗi", {
                        timeOut: 2000,
                        closeButton: true,
                    });
                }
            },
            error: function(xhr, status, error) {
                toastr.error("Cập nhật điểm không thành công!", "Lỗi", {
                    timeOut: 2000,
                    closeButton: true,
                });
            }
        });

    });
});

function showConfirmationModal(id) {
    const modalWrapper = document.getElementById('modalWrapper');
    currentDeleteId = id;
    const deleteForm = document.getElementById('delete-form');
    deleteForm.action = deleteForm.action.replace('__subject_id', currentDeleteId);
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
