<?php
function Connect() {
  $i=0;
  while ($i < 5) {
    $conn = @mysqli_connect("localhost", "s247030", "esiestmo", "s247030");
    if ($conn && @mysqli_set_charset($conn, "utf8"))
    {
      return $conn;
    }
    $i++;
  }
  echo "Problemi con il database";
  @mysqli_close($conn);
  exit;
}

function redirectHttps()
{
  if ($_SERVER['HTTPS'] != 'on') {
    $redirect = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: '.$redirect);
    exit();
  }
}

function validate($conn,$string)
{
  $string = @strip_tags($string);
  $string = @htmlspecialchars($string);
  $string = @mysqli_real_escape_string($conn,$string);
  return $string;
}
?>
