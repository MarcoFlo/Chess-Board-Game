<?php
if(isset($_COOKIE['s247030']))
{
  header('Location: ' . $_SERVER['HTTP_REFERER']);
  //header('Location: index.php');
  exit;
}
else {
  echo "Cookie non abilitati, abilitali e riprova.";
}
?>
