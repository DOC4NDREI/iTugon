<?php
    session_start();
    if(isset($_SESSION['U_unique_id'])){
        
                session_unset();
                session_destroy();
                header("location: ../login.php");
    }else{  
        header("location: ../login.php");
    }
?>