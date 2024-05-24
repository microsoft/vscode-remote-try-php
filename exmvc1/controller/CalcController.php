<?php
include_once('../model/CalcModel.php');

if(isset($_POST['btnSubmit']))
{
    $var1 = $_POST['number1'];
    $var2 = $_POST['number2'];
    $method = $_POST['method_v'];
    
    $calc = new CalcModel();
    $calc->a = $var1;
    $calc->b = $var2;
    $result = $calc->method_calc($method);
}

include_once('../view/CalcView.php');
?>
