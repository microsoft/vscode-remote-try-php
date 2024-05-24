$(document).ready(function () {
    $('.edit').on('click', function () {
        var id = $(this).val();
        var route = `${window.location.origin}/student/register_department/` + id;
        $.ajax({
            type: 'get',
            url: route,
            success: function (data) {
                $('.department').val(id);
                var courses = data.subject.data;
                var userSubjects = data.user_subject.map(function (userSubject) {
                    return userSubject.id_subject;
                });

                var html = courses.map(function (course) {
                    var checked = userSubjects.includes(course.id.toString()) ? 'checked' : '';
                    var checkboxName = checked ? 'id_subjected[]' : 'id_subject[]'; // Sửa name tùy theo trạng thái checked

                    return "<tr>" +
                        "<td style='padding: 10px; text-align: left; border-bottom: 1px solid #ddd;'><input type='checkbox' name='" + checkboxName + "' value='" + course.id + "' style='margin-right: 5px;' " + checked + "></td>" +
                        "<td style='padding: 10px; text-align: left; border-bottom: 1px solid #ddd;'>" + course.name + "</td>" +
                        "</tr>";
                }).join('');

                $("table.reg").html(function () {
                    return $(this).find("tr").first().prop('outerHTML') + html;
                });

            }
        });
    });

});

