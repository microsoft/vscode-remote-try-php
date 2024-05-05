<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1, h2 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
            cursor: pointer;
        }
        th:hover {
            background-color: #ddd;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .upload-btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .upload-btn:hover {
            background-color: #45a049;
        }
    
        .choose-btn {
            background-color: #008CBA;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .choose-btn:hover {
            background-color: #0077a3;
        }
        .delete-btn {
            background-color: #f44336;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        .delete-btn:hover {
            background-color: #cc0000;
        }
    </style>
</head>
<body>
    <h1>File Upload</h1>
    <?php
    // Kiểm tra xem có thông báo thành công hay lỗi từ upload.php không và hiển thị nó
    if (isset($_GET['error'])) {
        echo "<p style='color: red; text-align: center;'>".$_GET['error']."</p>";
    } elseif (isset($_GET['success'])) {
        echo "<p style='color: green; text-align: center;'>".$_GET['success']."</p>";
    }
    ?>
    <form action="../Controller/upload.php" method="post" enctype="multipart/form-data">
        <label for="fileToUpload" class="choose-btn">Choose File</label>
        <input type="file" name="fileToUpload" id="fileToUpload" class="file-input" style="display: none;" onchange="displayFileName()">
        <span id="selectedFileName"></span>
        <input type="submit" value="Upload File" name="submit" class="upload-btn">
    </form>
    <br><br>
    <table id="fileTable">
        <thead>
            <tr>
                <th onclick="sortTable(0)">File Name</th>
                <th onclick="sortTable(1)">File Type</th>
                <th onclick="sortTable(2)">Upload Date</th>
                <th onclick="sortTable(3)">File Size</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php include 'showFiles.php'; ?>
        </tbody>
    </table>

    <script>
        function sortTable(n) {
            var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
            table = document.getElementById("fileTable");
            switching = true;
            dir = "asc";
            while (switching) {
                switching = false;
                rows = table.rows;
                for (i = 1; i < (rows.length - 1); i++) {
                    shouldSwitch = false;
                    x = rows[i].getElementsByTagName("TD")[n];
                    y = rows[i + 1].getElementsByTagName("TD")[n];
                    if (dir == "asc") {
                        if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                            shouldSwitch= true;
                            break;
                        }
                    } else if (dir == "desc") {
                        if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                            shouldSwitch = true;
                            break;
                        }
                    }
                }
                if (shouldSwitch) {
                    rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                    switching = true;
                    switchcount ++;
                } else {
                    if (switchcount == 0 && dir == "asc") {
                        dir = "desc";
                        switching = true;
                    }
                }
            }
        }
        function displayFileName() {
            var fileInput = document.getElementById('fileToUpload');
            var selectedFileName = document.getElementById('selectedFileName');
            selectedFileName.innerText = fileInput.files[0].name;
        }
    </script>
</body>
</html>
