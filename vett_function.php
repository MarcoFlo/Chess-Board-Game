<?php
function is_inverted($inizio,$fine,$conn)
{
  global $pezzo;
  global $colonne;

  if ($fine == ($inizio - $colonne*($pezzo-1)) || $fine == $inizio-($pezzo-1))
  {
    return $fine;
  }
  else if ($fine == ($inizio + $colonne*($pezzo-1)) || $fine == $inizio+($pezzo-1))
  {
    return $inizio;
  }
  else {
    return_arr("not valid",$conn);
    exit;
  }
}

function is_vert($inizio,$fine,$conn)
{
  global $pezzo;
  global $colonne;
  if($fine == "null")
  {
    return;
  }

  if ($fine == ($inizio - $colonne*($pezzo-1)) || $fine == ($inizio + $colonne*($pezzo-1)))
  {
    return TRUE;
  }
  else if($fine == $inizio+($pezzo-1) || $fine == $inizio-($pezzo-1))
  {
    return FALSE;
  }
  else {
    return_arr("not valid",$conn);
    exit;
  }
}

function generate_vett($inizio,$fine,$conn)
{
  global $pezzo;
  global $colonne;
  $vert = TRUE;

  if($fine == "null")
  {
    $vett = array($inizio);
    return $vett;
  }

  $vert = is_vert($inizio,$fine,$conn);
  $vett[0]=is_inverted($inizio,$fine,$conn);

  if ($vert) {
    for ($i = 1; $i < $pezzo; $i++) {
      $vett[$i] = $vett[0]+$colonne*$i;
    }
  }
  else {
    for ($i = 1; $i < $pezzo; $i++) {
      $vett[$i] = $vett[0]+$i;
    }
  }
  return $vett;
}



function generate_vett_cuscinetto($vett,$conn)
{
  global $pezzo;
  global $colonne;
  global $righe;
  $result = [];

  for ($i=0; $i < ($pezzo-1); $i++)
  {
    array_push($result,($vett[$i]));
  }

  if (is_vert($vett[0],$vett[$pezzo-1],$conn))
  {
    //nord
    if ($vett[0] >= $colonne)
    {

      for ($i=($vett[0]-$colonne); $i <= ($vett[0]-$colonne); $i++) {
        array_push($result,$i);
      }
    }
    //sud
    if ($vett[($pezzo-1)] < ($colonne*$righe-$colonne))
    {
      for ($i=($vett[($pezzo-1)]+$colonne); $i <= ($vett[($pezzo-1)]+$colonne); $i++) {
        array_push($result,$i);
      }
    }

    //ovest
    if (($vett[0] % $colonne) != 0)
    {
      for ($i=($vett[0]-$colonne-1+$colonne); $i < ($vett[($pezzo-1)]+$colonne-1); $i+=$colonne) {
        array_push($result,$i);
      }
    }

    //est
    if (($vett[0] % $colonne) != ($colonne-1)) {

      for ($i=($vett[0]-$colonne+1+$colonne); $i < ($vett[($pezzo-1)]+$colonne+1); $i+=$colonne) {
        array_push($result,$i);
      }
    }

    //angoli
    //no
    if ($vett[0] >= $colonne && ($vett[0] % $colonne) != 0)
    array_push($result,($vett[0]-$colonne-1));

    //ne
    if ($vett[0] >= $colonne && ($vett[0] % $colonne) != ($colonne-1))
    array_push($result,($vett[0]-$colonne+1));

    //so
    if ($vett[($pezzo-1)] < ($colonne*$righe-$colonne) && ($vett[0] % $colonne) != 0)
    array_push($result,($vett[($pezzo-1)]+$colonne-1));

    //se
    if ($vett[($pezzo-1)] < ($colonne*$righe-$colonne) && ($vett[0] % $colonne) != ($colonne-1))
    array_push($result,($vett[($pezzo-1)]+$colonne+1));
  }
  else {

    //nord
    if ($vett[0] >= $colonne)
    {
      for ($i=($vett[0]-$colonne); $i <= ($vett[($pezzo-1)]-$colonne); $i++) {
        array_push($result,$i);
      }
    }
    //sud
    if ($vett[0] < ($colonne*$righe-$colonne))
    {
      for ($i=($vett[0]+$colonne); $i <= ($vett[($pezzo-1)]+$colonne); $i++) {
        array_push($result,$i);
      }
    }

    //ovest
    if (($vett[0] % $colonne) != 0)
    {
      for ($i=($vett[0]-$colonne-1+$colonne); $i < ($vett[0]+$colonne-1); $i+=$colonne) {
        array_push($result,$i);
      }
    }

    //est
    if (($vett[($pezzo-1)] % $colonne) != ($colonne-1)) {

      for ($i=($vett[($pezzo-1)]-$colonne+1+$colonne); $i < ($vett[($pezzo-1)]+$colonne+1); $i+=$colonne) {
        array_push($result,$i);
      }
    }

    //angoli
    //no
    if ($vett[0] >= $colonne && ($vett[0] % $colonne) != 0)
    array_push($result,($vett[0]-$colonne-1));

    //ne
    if ($vett[0] >= $colonne && ($vett[($pezzo-1)] % $colonne) != ($colonne-1))
    array_push($result,($vett[($pezzo-1)]-$colonne+1));

    //so
    if ($vett[0] < ($colonne*$righe-$colonne) && ($vett[0] % $colonne) != 0)
    array_push($result,($vett[0]+$colonne-1));

    //se
    if ($vett[($pezzo-1)] < ($colonne*$righe-$colonne) && ($vett[($pezzo-1)] % $colonne) != ($colonne-1))
    array_push($result,($vett[($pezzo-1)]+$colonne+1));
  }
  array_push($result,$vett[($pezzo-1)]);
  return $result;
}
?>
