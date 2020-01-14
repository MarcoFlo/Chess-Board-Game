<?php
ini_set('session.gc_maxlifetime',1800);
ini_set('session.gc_divisor',1);
session_start();
include "myfunction.php";
include "lib_utility.php";
redirectHttps();

if (isset($_POST["mail"]) && isset($_POST["password"]) && $_POST["mail"] != "" && $_POST["password"] != "")
{
  $conn = Connect();
  $mail = validate($conn,$_POST["mail"]);
  if (checkMail($mail) && checkPassword($_POST["password"])) {
    @mysqli_autocommit($conn, FALSE);
    $password = md5($_POST["password"]);
    try {
      if (isset($_POST["new"])) {
        if(is_mail_avaiable($conn,$mail))
        {
          insert_user($conn,$mail,$password);
          $_SESSION['time'] = time();
          echo "accesso effettuato";
        }
        else {
          echo "Mail non disponibile";
        }
      }
      else
      {
        if (check_credentials($conn,$mail,$password))
        {
          $_SESSION['time'] = time();
          $_SESSION['user'] = $mail;
          echo "accesso effettuato";
        }
        else {
          echo "Le credenziali sono sbagliate, riprova!";
        }
      }
      if (!@mysqli_commit($conn)) {
        throw new Exception("Commit failed");
      }
      @mysqli_close($conn);
    }
    catch (Exception $e) {
      @mysqli_rollback($conn);
      @mysqli_close($conn);
      echo "Problemi con il database";
      exit;
    }
  }
  else {
    echo "Le credenziali inserite non rispettano i criteri indicati";
  }
}
else {
  echo "Inserisci tutti i campi.";
}
?>
