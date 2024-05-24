<!DOCTYPE html>
<html>
<head>
    <title>Subject Info</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #ddd;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Danh sách môn chưa đăng ký</h1>
    <table>
        <tr>
            <th>ID môn học</th>
            <th>Tên môn học</th>
        </tr>
        @foreach($infoSubject['data'] as $key => $subject)
            <tr>
                <td>{{ $subject->id }}</td>
                <td>{{ $subject->name }}</td>
            </tr>
        @endforeach
    </table>
</body>
</html>
