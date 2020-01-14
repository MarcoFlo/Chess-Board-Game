<?php
include "lib_utility.php";
include "myfunction.php";
redirectHttps();
ini_set('session.gc_maxlifetime',1800);
ini_set('session.gc_divisor',1);
session_start();


if(!isset($_COOKIE['s247030']))
{
	setcookie('s247030', 'cookie', time()+3600);
	header('Location: check.php');
	exit;
}

$diff = 0;
if (isset($_SESSION['time'])) {
	$diff = time()-$_SESSION['time'];
}
if ($diff > 120 || isset($_GET['delete']))
{

	$_SESSION = array();
	if (ini_get("session.use_cookies")) {
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 42000,
		$params["path"], $params["domain"],
		$params["secure"], $params["httponly"]);
	}
	session_destroy();
	if (isset($_GET['msg'])) {
		header('Location: index.php?msg=redo');
		unset($_GET['msg']);
	}
	else {
		header('Location: index.php');
	}
	exit;
}
else {
	$_SESSION['time'] = time();
}

if (isset($_SESSION['user']))
{
	$loggedin = TRUE;
}
else $loggedin = FALSE;


$conn = Connect();
try {
	fill_array($conn);
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
?>

<!DOCTYPE html>
<html lang="it">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="content-type" content="text/html">
	<title>MarcoFLorian</title>



	<link rel="stylesheet" href="bootstrap.min.css">
	<script src="jquery.min.js"></script>
	<script src="popper.min.js"></script>
	<script src="bootstrap.min.js"></script>


	<link href="dashboard.css" rel="stylesheet">


	<script type="text/javascript">
	function ajaxRequest() {
		try { // Non IE Browser?
			var request = new XMLHttpRequest();
		} catch(e1){ // No
			try { // IE 6+?
				request = new ActiveXObject("Msxml2.XMLHTTP");
			} catch(e2){ // No
				try { // IE 5?
					request = new ActiveXObject("Microsoft.XMLHTTP")
				} catch(e3){ // No AJAX Support
					request = false;
				}
			}
		}
		return request;
	}

</script>
</head>
<body class="p-0">
	<div class="modal fade" id="modal-registrazione">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Modulo di registrazione/login</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close" id="buttonClose-sum">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col alert alert-warning alert-dismissible fade show" style="display: none;" role="alert" id="login_error">
							<p id="login_error_text"></p>
							<button type="button" class="close" onclick="$('#login_error').hide()" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
					</div>
					<label for="sign_in_email" class="sr-only">Email address</label>
					<input type="email" id="sign_in_email" class="form-control mb-2" placeholder="Email address">
					<label for="sign_in_password" class="sr-only p-1">Password</label>
					<input type="password" id="sign_in_password" class="form-control mt-2" placeholder="Password" autocomplete="off">
					<small id="passwordHelpBlock" class="form-text text-muted">
					</small>
					<div class="form-check m-1">
						<input type="checkbox" class="form-check-input" id="check_nuovo_utente">
						<label class="form-check-label" for="check_nuovo_utente">Registrami</label>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-lg btn-primary btn-block" id="sign_in">Sign in</button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal registrazione -->

	<nav class="navbar navbar-expand navbar-dark bg-dark sticky-top justify-content-between">
		<div class="justify-content-left">
			<span class="navbar-text">Gioco</span>
		</div>
	</nav> <!--fine navbar-top-->

	<div class="mysidenav bg-light">
		<div class="sidebar-sticky">
			<ul class="nav flex-column">
				<li class="nav-item">
					<button type="button" class="btn btn-link" id="registrazione">Accedi o Registrati</button>
				</li>
				<li class="nav-item">
					<?php if ($loggedin) {
						?>
						<button type="button" class="btn btn-link text-secondary" id="esci">Esci</button>
						<?php
					} ?>
				</li>
			</ul>
		</div>
	</div>	<!--fine sidebar -->

	<div class="main p-0 mr-0">
		<div class="container-fluid">
			<div class="row">
				<div class="col alert alert-primary alert-dismissible fade show" role="alert" id="main_msg">
					<p id="main_msg_text">
						<noscript>
							Senza javascript abilitato il sito non funzionerà correttamente!
							<br>
						</noscript>
						<?php if (!$loggedin)
						{
							echo "Accedi per posizionare i tuoi pezzi";
						}
						else {
							echo "Benvenuto ". $_SESSION['user'];
						}
						?>
					</p>
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
			</div>
			<div class="row justify-content-center my-3" <?php if (!$loggedin) {
				?>
				data-toggle="tooltip" data-placement="bottom" title="Posiziona pezzi in orizzontale o in verticale con un quadratino di confine"
				<?php
			} ?>>
			<?php create_route(); ?>
		</div>
		<div class="row m-1 my-5">
			<?php if ($loggedin == TRUE) {
				?>
				<div class="col m-1">
					<button class="btn btn-primary btn-lg btn-block" type="button"  id="invia" data-toggle="tooltip" data-placement="bottom" title="Invia la mossa al server">Invia mossa</button>
				</div>
				<div class="col m-1">
					<button class="btn btn-lg btn-block" type="button" id="cancella" data-toggle="tooltip" data-placement="bottom" title="Rimuove l'ultimo pezzo posizionato">Cancella</button>
				</div>
				<div class="col m-1">
					<button class="btn btn-lg btn-block" type="button" id="remove_pezzo" data-toggle="tooltip" data-placement="bottom" title="Rimuove l'ultimo pezzo inviato">Rimuovi mossa</button>
				</div>
				<?php
			}
			?>
		</div>
	</div>
</div> <!--container-fluid-->
</div> <!--main-->

<script><!--
function validatePassword(password)
{
	var re = /[^a-zA-Z0-9]/;
	return (re.test(password) && password.length>=3);
}

function validateEmail(email)
{
	var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	return re.test(email);
}

function dismiss_info()
{
	$('#main_msg').hide();
}

function colora_caselle(colore,inizio,fine) {

	var colonne = "<?php echo $colonne;  ?>";
	var pezzo = "<?php echo $pezzo;  ?>";
	var vert = true;
	var n = 0;
	var vett = new Array();
	var flag = false;

	if (fine == null) {
		vett[0] = inizio;
		flag= true;
	}
	else if (parseInt(fine) == parseInt(parseInt(inizio) - colonne*(pezzo-1)) )
	{
		vert = true;
		n= -1;
	}
	else if (parseInt(fine) == parseInt(parseInt(inizio) + colonne*(pezzo-1)) )
	{
		vert = true;
		n=1;
	}
	else if(parseInt(fine) == parseInt(parseInt(inizio)+(pezzo-1)))
	{
		vert = false;
		n=1;
	}
	else if(parseInt(fine) == parseInt(parseInt(inizio)-(pezzo-1)))
	{
		vert = false;
		n= -1;
	}

	if (!flag) {
		if (n==1) {
			vett[0]=inizio;
		}
		else {
			vett[0]=fine;
		}
		if (vert) {
			for (var i = 1; i < pezzo; i++) {
				vett[i] = parseInt(vett[0])+parseInt(colonne*i);
			}
		}
		else {
			for (var i = 1; i < pezzo; i++) {
				vett[i] = parseInt(vett[0])+parseInt(i);
			}
		}
	}
	for (var i = 0; i < pezzo; i++)
	{
		$("#"+(parseInt(vett[i]))).css("background-color",colore);
	}
}

function colora_array(arr)
{
	for (var i = 0; i < <?php echo $colonne; ?>*<?php echo $righe; ?>; i++)
	{
		$("#"+(parseInt(i))).css("background-color","white");
	}

	if (typeof arr === 'undefined') {
		var container = [];
		var supp = <?php echo json_encode($_SESSION['general']); ?>;
		container['general'] = supp;
		<?php
		if (isset($_SESSION['user']))
		{
			?>
			supp = <?php echo json_encode($_SESSION['mine']); ?>;
			container['mine'] = supp;
			<?php
		}
		?>
		arr = container;
	}
	<?php
	if (isset($_SESSION['user']))
	{
		?>
		for (var i in arr['mine'])
		{
			colora_caselle("green", i ,(arr['mine'])[i]);
		}
		<?php
	}
	?>
	for (var i in arr['general'])
	{
		colora_caselle("black", i ,(arr['general'])[i]);
	}
}

colora_array();

<?php if(isset($_GET['msg']))
{ ?>
	document.getElementById("main_msg_text").innerHTML = "La tua sessione è scaduta, dovresti rifare il login";
	document.getElementById("main_msg").className = "col alert alert-warning alert-dismissible fade show";
	<?php
}
unset($_GET['msg']);
if ($loggedin)
{
	?>
	$("#registrazione").prop('disabled', true);
	<?php
}
?>
var inizio=null;
var fine=null;

$(document).ready(function(){
	//setTimeout(dismiss_info, 5000);
	$('#check_nuovo_utente').change(function() {
		if($(this).is(':checked'))
		{
			$('#passwordHelpBlock').html("La tua password deve contenere almeno tre caratteri, di cui almeno uno non alfanumerico");
		}
		else {
			$('#passwordHelpBlock').html("");
		}
	});

	$("#registrazione").click(function(){
		$("#modal-registrazione").modal({
			keyboard: true, backdrop: "static", show: true
		});
	});


	$('#sign_in').click(function () {
		if(validateEmail($('#sign_in_email').val()) && validatePassword($('#sign_in_password').val()))
		{
			req = ajaxRequest();
			req.onreadystatechange=
			function() {
				if (req.readyState==4 && (req.status==200 || req.status==0))
				{
					if(req.responseText == "accesso effettuato")
					{
						$('#modal-registrazione').modal('hide');
						window.location.replace("index.php");
					}
					else {
						$('#login_error').show();
						document.getElementById("login_error_text").innerHTML = req.responseText;
					}
				}
			}
			if($('#check_nuovo_utente').is(':checked'))
			{
				req.open("POST","myreg.php",true);
				req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				req.send("mail="+$('#sign_in_email').val()+"&password="+$('#sign_in_password').val()+"&new=true");
			}
			else {
				req.open("POST","myreg.php",true);
				req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				req.send("mail="+$('#sign_in_email').val()+"&password="+$('#sign_in_password').val());
			}

		}
		else {
			$('#login_error').show();
			document.getElementById("login_error_text").innerHTML = "Le credenziali inserite non rispettano i criteri indicati";
		}
	});

	$('#invia').click(function () {
		if (inizio != null && fine != null) {
			req = ajaxRequest();
			req.onreadystatechange=
			function() {
				if (req.readyState==4 && (req.status==200 || req.status==0))
				{
					if (req.responseText != "Problemi con il database") {
						var arr = $.parseJSON(req.responseText);
						if (arr[0] != "not valid session") {
							if (arr[0] != "pos non disp") {
								document.getElementById("main_msg").className= "col alert alert-success alert-dismissible fade show";
								document.getElementById("main_msg_text").innerHTML = "Pezzo posizionato correttamente";
							}
							else {
								document.getElementById("main_msg").className = "col alert alert-warning alert-dismissible fade show";
								document.getElementById("main_msg_text").innerHTML = "Pozione non più disponibile";
							}
							colora_array(arr[1]);
							$('#main_msg').show();
							//setTimeout(dismiss_info, 5000);
							inizio=null;
							fine=null;
						}
						else {
							window.location.replace("index.php?delete=true&msg=redo");
						}
					}
					else {
						document.getElementById("main_msg").className = "col alert alert-danger alert-dismissible fade show";
						document.getElementById("main_msg_text").innerHTML = "Ci sono problemi con il database si prega di riprovare in seguito";
						$('#main_msg').show();
					}
				}
			}
			req.open("POST","mymossa.php",true);
			req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			req.send("inizio="+inizio+"&fine="+fine+"&insert=true");
		}
	});

	$('#cancella').click(function () {
		colora_caselle("white",inizio,fine);
		inizio=null;
		fine=null;
	});


	$('#remove_pezzo').click(function () {
		req = ajaxRequest();
		req.onreadystatechange=
		function() {
			if (req.readyState==4 && (req.status==200 || req.status==0))
			{
				if (req.responseText != "Problemi con il database") {
					var arr = $.parseJSON(req.responseText);
					if (arr[0] != "not valid session") {
						if (arr[0] != "not valid") {
							colora_array(arr[1]);
							colora_caselle("grey",inizio,fine);
							document.getElementById("main_msg").className= "col alert alert-success alert-dismissible fade show";
							document.getElementById("main_msg_text").innerHTML = "Pezzo rimosso correttamente";
							$('#main_msg').show();
							//setTimeout(dismiss_info, 5000);
						}
					}
					else {
						window.location.replace("index.php?delete=true&msg=redo");
					}
				}
				else {
					document.getElementById("main_msg").className = "col alert alert-danger alert-dismissible fade show";
					document.getElementById("main_msg_text").innerHTML = "Ci sono problemi con il database si prega di riprovare in seguito";
					$('#main_msg').show();
				}
			}
		}
		req.open("GET","myremove.php",true);
		req.send();
	});

	<?php if($loggedin)
	{
		?>
		$('.casella').click(function () {
			if (inizio==null || fine ==null) {
				var id = this.id;
				req = ajaxRequest();
				req.onreadystatechange=
				function() {
					if (req.readyState==4 && (req.status==200 || req.status==0))
					{
						if (req.responseText != "Problemi con il database") {
							var arr = $.parseJSON(req.responseText);
							if(arr[0] != "not valid session")
							{
								if(arr[0] == "pos disp")
								{
									colora_array(arr[1]);
									colora_caselle("grey",inizio,fine);
									$('#main_msg').hide();
								}
								else if(arr[0] == "pos non disp"){
									colora_array(arr[1]);
									if(fine==null)
									{
										inizio = null;
									}	else {
										fine=null;
										colora_caselle("grey",inizio,fine);
									}
									document.getElementById("main_msg").className= "col alert alert-warning alert-dismissible fade show";
									document.getElementById("main_msg_text").innerHTML = "Posizione non disponibile";
									$('#main_msg').show();
								}
								else {
									fine=null;
								}
							}
							else {
								window.location.replace("index.php?delete=true&msg=redo");
							}
						}
						else {
							document.getElementById("main_msg").className = "col alert alert-danger alert-dismissible fade show";
							document.getElementById("main_msg_text").innerHTML = "Ci sono problemi con il database si prega di riprovare in seguito";
							$('#main_msg').show();
						}
					}
				}
				if (inizio!=null) {
					fine=id;
					req.open("POST","mymossa.php",true);
					req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
					req.send("inizio="+inizio+"&fine="+fine);
				}
				else {
					inizio = id;
					req.open("POST","mymossa.php",true);
					req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
					req.send("inizio="+inizio+"&fine="+fine);
				}

			}
		});
		<?php
	}
	?>

	$('#esci').click(function () {
		window.location.replace("index.php?delete=true");
	});

});



//--></script>
</body>
</html>
