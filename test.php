<?php
    $so_luong = $_POST[“so_luong”];
    $don_gia = $_POST[“don_gia”];
    if ($so_luong <10)
    
    $thanh_tien = $so_luong * $don_gia;
    elseif ($so_luong >= 10 and $so_luong <=20)
    
    $thanh_tien = ($so_luong * $don_gia) * 0.95;
    
    else
    
    $thanh_tien = ($so_luong * $don_gia) * 0.9;
?>