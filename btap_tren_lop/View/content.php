<?php
    session_start();
    if ($_SESSION["IsLogin"] == false)
        header("Location: ../View/login.html");
?>
<?php
   $id = session_id();
    
   if( isset( $_SESSION['counter'] ) ) {
      $_SESSION['counter'] += 1;
   } else {
      $_SESSION['counter'] = 1;
   }
   $msg = "Number of visits in this session: ".  $_SESSION['counter'];

   echo "Session Id: ".$id . "<br><br>"; ;
 
    $ten = session_id();
    echo 'Hello '.$ten;
   



?>
<?php
echo ("<br/><br/>");
echo "$msg";
echo ("<br/><br/>");
echo ("<a href='../Controller/logout.php'>Logout</a>");

?>

