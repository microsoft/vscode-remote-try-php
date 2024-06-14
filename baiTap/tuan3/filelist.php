<?php
if($_SESSION["IsLogin"] == false)
    header('Location: login.php');

$files = scandir('uploads/');
$files = array_diff($files, array('.', '..'));

if(isset($_GET['sortby'])) {
    $sortby = $_GET['sortby'];
    if($sortby == 'name') {
        sort($files);
    } elseif($sortby == 'date') {
        usort($files, function($a, $b) {
            return filemtime('uploads/' . $a) < filemtime('uploads/' . $b);
        });
    }
}

foreach($files as $file) {
    echo 'File Name: ' . $file . '<br>';
    echo 'File Type: ' . mime_content_type('uploads/' . $file) . '<br>';
    echo 'Upload Time: ' . date ("F d Y H:i:s.", filemtime('uploads/' . $file)) . '<br>';
    echo 'File Size: ' . filesize('uploads/' . $file) . '<br><br>';
    echo '<form action="delete.php" method="POST">
            <input type="hidden" name="filename" value="' . $file . '">
            <input type="submit" value="Delete">
          </form>';
}

echo '<a href="?sortby=name">Sort by Name</a><br>';
echo '<a href="?sortby=date">Sort by Date</a><br>';
?>
