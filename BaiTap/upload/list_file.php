<?php
$directory = "uploads/";
$files = glob($directory . "*");

usort($files, function($a, $b) {
    return filemtime($b) - filemtime($a);
});

echo "<table>";
echo "<tr><th><a href=\"index.php?sort=name\">File Name</a></th><th><a href=\"index.php?sort=date\">Upload Date</a></th><th>File Type</th><th>File Size</th></tr>";

foreach($files as $file) {
    echo "<tr>";
    echo "<td>" . basename($file) . "</td>";
    echo "<td>" . date("Y-m-d H:i:s", filemtime($file)) . "</td>";
    echo "<td>" . pathinfo($file, PATHINFO_EXTENSION) . "</td>";
    echo "<td>" . filesize($file) . " bytes</td>";
    echo "</tr>";
}

echo "</table>";
?>