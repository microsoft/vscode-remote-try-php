<?php
            $files = scandir("../upload/");
            foreach ($files as $file) {
                if ($file != '.' && $file != '..') {
                    $file_path = "../upload/" . $file;
                    if (file_exists($file_path)) {
                        $file_info = pathinfo($file_path);
                        $file_size = filesize($file_path);
                        $file_date = date("Y-m-d H:i:s", filemtime($file_path));
                        echo "<tr>";
                        echo "<td>".$file_info['basename']."</td>";
                        echo "<td>".$file_info['extension']."</td>";
                        echo "<td>".$file_date."</td>";
                        echo "<td>".$file_size." bytes</td>";
                        echo "</tr>";
                    } else {
                        // Xử lý tệp tin không tồn tại
                        echo "<tr><td colspan='4'>File $file không tồn tại</td></tr>";
                    }
                }
            }
        ?>