<?php
/*
session_start();
$id = session_id();
echo "Session Id: ".$id ;
*/

   session_start();
   $id = session_id();
    
   if( isset( $_SESSION['counter'] ) ) {
      $_SESSION['counter'] += 1;
   } else {
      $_SESSION['counter'] = 1;
   }
   $msg = "Number of visits in this session: ".  $_SESSION['counter'];

   echo "Session Id: ".$id . "<br>" ;
 
   echo "$msg";

/*
//Luu trang thai dang nhap
$_SESSION["isLogin"] =true;
//Luu ten dang nhap
$_SESSION["username"] = "";
//Luu loai quyen dang nhap
$_SESSION["authentication_right"]="";


*/

?>
<html>

<p>test session</p>

</html>