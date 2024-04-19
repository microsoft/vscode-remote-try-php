<?php
for ($i = 1; $i <= 200; $i++) {
    if ($i % 2 == 0) {
        echo '<span style="color: red; font-weight: bold;">' . $i . '</span> ';
    } else {
        echo '<span style="color: blue; font-style: italic;">' . $i . '</span> ';
    }
}
?>
