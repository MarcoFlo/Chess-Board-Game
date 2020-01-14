<?php
ini_set('session.gc_maxlifetime',1800);
ini_set('session.gc_divisor',1);
session_start();
include "myfunction.php";
include "lib_utility.php";
include "vett_function.php";
redirectHttps();

if (isset($_SESSION['user']))
{
  $conn = Connect();
  if (time()-$_SESSION['time']<120)
  {
    $_SESSION['time'] = time();
    @mysqli_autocommit($conn, FALSE);

    try {
      remove_pezzo($conn);
      if (!@mysqli_commit($conn)) {
        throw new Exception("Commit failed");
      }
      @mysqli_close($conn);
    } catch (Exception $e) {
      @mysqli_rollback($conn);
      @mysqli_close($conn);
      echo "Problemi con il database";
      exit;
    }

  }
  else {
    return_arr("not valid session",$conn);
  }
}
else {
  echo "not valid session";
}
?>
