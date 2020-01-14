<?php
$righe = 7;
$colonne = 9;
$pezzo = 4;

function checkPassword($pwd)
{
  if (!ctype_alnum($pwd) && strlen($pwd)>=3) {
    return true;
  }
  return false;
}

function checkMail($email)
{
  if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
    return true;
  }
  return false;
}


function create_route()
{
  global $righe;
  global $colonne;
  for ($i=0; $i < $righe ; $i++) {
    ?>
    <div class="row">
      <?php for ($j=0; $j < $colonne ; $j++) {
        ?>
        <div class="casella"
        style="
        width: 4vw;
        height: 4vw;
        border-color: black;
        background-color: white;
        border-style: solid;
        border-width: 1px;"
        id = "<?php echo $i*$colonne+$j; ?>"></div><?php
      } ?>
    </div>
    <div class="w-100"></div>
    <?php
  }
}


function remove_pezzo($conn)
{
  $user = $_SESSION['user'];
  $inizio;
  $fine;
  $id;

  if($risposta = @mysqli_query($conn, "SELECT inizio,fine,id FROM pezzi WHERE utente = '$user' and id IN (SELECT MAX(id) FROM pezzi WHERE utente = '$user' FOR UPDATE) FOR UPDATE"))
  {
    $riga = @mysqli_fetch_array($risposta,MYSQLI_NUM);
    $inizio = $riga[0];
    $fine = $riga[1];
    $id = $riga[2];
    @mysqli_free_result($risposta);
  }
  else {
    throw new Exception("Select inizio remove");
  }

  if(!@mysqli_query($conn, "DELETE FROM pezzi WHERE utente = '$user' and id = '$id'"))
  {
    throw new Exception("Select delete");
  }
  $vett = generate_vett($inizio,$fine,$conn);
  manage_cuscinetto($conn,$vett,true);
  unset(($_SESSION['mine'])[$inizio]);
  return_arr("delete ok",$conn);
}

function manage_cuscinetto($conn,$vett,$delete)
{
  //@mysqli_autocommit($conn, FALSE); // turn OFF auto
  //@mysqli_commit($conn); // process ALL queries so far
  //@mysqli_autocommit($conn, TRUE); // turn ON auto
  global $righe;
  global $colonne;
  global $pezzo;
  $l;
  $r;

  $cuscinetto = generate_vett_cuscinetto($vett,$conn);

  if (!$delete) {
    for($x = 0; $x < count($cuscinetto); $x++) {
      $sql= "UPDATE caselle SET num=num+1 WHERE id = $cuscinetto[$x] ;";
      if (!@mysqli_query($conn, $sql)) {
        throw new Exception("Update caselle+");
      }
    }

    for($x = 0; $x < count($cuscinetto); $x++) {
      $sql= "INSERT INTO caselle (id) SELECT * FROM (SELECT $cuscinetto[$x]) AS tmp WHERE NOT EXISTS (SELECT id FROM caselle WHERE id = $cuscinetto[$x]  FOR UPDATE) LIMIT 1; ";
      if (!@mysqli_query($conn, $sql)) {
        throw new Exception("insert caselle");
      }
    }
  }
  else {
    for($x = 0; $x < count($cuscinetto); $x++) {
      $sql= "UPDATE caselle SET num=num-1 WHERE id = $cuscinetto[$x]; ";
      if (!@mysqli_query($conn, $sql)) {
        throw new Exception("Update caselle");
      }
    }
  }
}


function return_arr($str,$conn)
{
  fill_array($conn);
  $vett = array($str,$_SESSION);
  echo json_encode($vett);
}

function check_containment($conn,$vett) {
  global $pezzo;

  $sql="SELECT count(*) FROM caselle WHERE (id = '$vett[0]'";

    for ($i=1; $i < sizeof($vett); $i++)
    {
    $sql .= " or id = '$vett[$i]'";
    }
    $sql .= ") and num > 0 FOR UPDATE";
    if($risposta = @mysqli_query($conn, $sql))
    {
      $result = @mysqli_fetch_array($risposta,MYSQLI_NUM)[0];
      if($result!=0)
      {
        return_arr("pos non disp",$conn);
      }
      else {
        if (isset($_POST['insert'])) {
          manage_cuscinetto($conn,$vett,false);
          insert_pezzo($conn,$vett[0],$vett[$pezzo-1]);
          ($_SESSION['mine'])[$vett[0]] = $vett[$pezzo-1];
        }
        return_arr("pos disp",$conn);
      }

      @mysqli_free_result($risposta);

    }
    else {
      throw new Exception("Select count");
    }
  }

  function insert_pezzo($conn,$inizio,$fine)
  {
    $user = $_SESSION['user'];
    $id;
    $sql = "SELECT COALESCE(MAX(id),0) FROM pezzi where utente = '$user' FOR UPDATE ";
    if($risposta = @mysqli_query($conn, $sql))
    {
      $id = @mysqli_fetch_array($risposta,MYSQLI_NUM)[0];
      $id++;
    }
    else {
      throw new Exception("Select coalesce into");
    }

    $sql="INSERT INTO pezzi (id,utente,inizio,fine) VALUES ($id,'$user','$inizio','$fine');";
    if (!@mysqli_query($conn, $sql)) {
      throw new Exception("Insert into");
    }
  }

  function is_mail_avaiable($conn,$mail)
  {
    if($risposta = @mysqli_query($conn, "SELECT count(*) FROM utenti WHERE utente = '$mail' FOR UPDATE"))
    {
      if(($riga = @mysqli_fetch_array($risposta,MYSQLI_NUM)[0]))
      {
        return false;
      }
      else {
        return true;
      }
      @mysqli_free_result($risposta);
    }
    else {
      throw new Exception("mail avaiable failed");
    }
  }

  function insert_user($conn,$mail,$password)
  {
    $sql="INSERT INTO utenti (utente, password) VALUES ('".$mail."','". $password."');";

    if (@mysqli_query($conn, $sql)) {
      $_SESSION['user'] = $mail;
    } else {
      throw new Exception("insert user");
    }
  }

  function check_credentials($conn,$mail,$password)
  {
    if($risposta = @mysqli_query($conn, "SELECT count(*) FROM utenti WHERE utente = '$mail' and password = '$password' FOR UPDATE"))
    {
      if(($riga = @mysqli_fetch_array($risposta,MYSQLI_NUM)[0]))
      {
        return true;
      }
      else {
        return false;
      }
      @mysqli_free_result($risposta);
    }
    else {
      throw new Exception("check credetntials");
    }
  }

  function fill_array($conn)
  {
    if($risposta = @mysqli_query($conn, "SELECT inizio, fine, utente FROM pezzi FOR UPDATE"))
    {
      $general = array();
      $mine = array();
      for ($i = 0; $i < @mysqli_num_rows($risposta); $i++)
      {
        $riga = @mysqli_fetch_array($risposta,MYSQLI_NUM);
        if (isset($_SESSION['user'])) {
          if ($riga[2] == $_SESSION['user']) {
            $mine[$riga[0]] = $riga[1];
          }
          else {
            $general[$riga[0]] = $riga[1];
          }
        }
        else {
          $general[$riga[0]] = $riga[1];
        }
      }
      $_SESSION['general'] = $general;
      if (isset($_SESSION['user'])) {
        $_SESSION['mine'] = $mine;
      }
      @mysqli_free_result($risposta);
    }
    else {
      throw new Exception("fill_array");
    }
  }
  ?>
