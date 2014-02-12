<?php
session_start();
require('auth.php');
require('functions.php');

if(Auth::islog()){

	$liste = 'Mex';
	$pseudo = $_SESSION['Auth']['pseudo'];

// Ajouter une personne
	if(!empty($_POST) && isset($_POST['addName'])){
		$addName = $_POST['addName'];
		$addName = trim($addName);
		$addName = strip_tags($addName);
		$sql = "SELECT prenom FROM friends WHERE prenom = '".$addName."' AND username = '".$pseudo."' AND liste = '".$liste."'";
		try {
		    $req = $connexion->prepare($sql);
		    $req->execute();
		    $countPseudo = $req->rowCount($sql);
		    if($countPseudo > 0){
		    	$error_message_name = 'Vous utilisez déjà ce nom dans cette liste';
		    } else {
		    	$sql2 = "INSERT INTO friends (prenom, created, liste, username) VALUES ('".$addName."','".date("Y-m-d G:i:s")."','".$liste."','".$pseudo."')";
		    	try {
		    		$connexion->exec($sql2);
		    		echo 'Nouveau nom bien ajouté dans la base.';
		    	} catch(PDOException $e) {
		    		echo 'erreur: '.$e->getMessage();
		    	}
		    }
		    $_SESSION['addName'] = $addName;
		} catch(PDOException $e) {
		   echo 'erreur: '.$e->getMessage();
		}
	}




// Ajouter liste
	if(!empty($_POST) && isset($_POST['addListe'])){
		$addListe = $_POST['addListe'];
		$addListe = trim($addListe);
		$addListe = strip_tags($addListe);
		$sql = "SELECT nomDeListe FROM listes WHERE nomDeListe = '".$addListe."' AND createdBy = '".$pseudo."'";
		try {
		    $req = $connexion->prepare($sql);
		    $req->execute();
		    $countListes = $req->rowCount($sql);
		    if($countListes > 0){
		    	$error_message_liste = 'Vous utilisez déjà ce nom de liste';
		    } else {
		    	$sql2 = "INSERT INTO listes (nomDeListe, createdBy, created) VALUES ('".$addListe."','".$pseudo."','".date("Y-m-d G:i:s")."')";
		    	try {
		    		$connexion->exec($sql2);
		    		echo 'Nouvelle liste bien ajoutée dans la base.';
		    	} catch(PDOException $e) {
		    		echo 'erreur: '.$e->getMessage();
		    	}
		    }
		} catch(PDOException $e) {
		   echo 'erreur: '.$e->getMessage();
		}
	}
// Ajouter Montant
	if(!empty($_POST) && isset($_POST['addMontant']) && isset($_SESSION['addName'])){
		$addMontant = $_POST['addMontant'];
		$addMontant = trim($addMontant);
		$addMontant = strip_tags($addMontant);
		$sql3 = "UPDATE friends SET montant = '".$addMontant."' WHERE prenom = '".$_SESSION['addName']."' AND username = '".$pseudo."'";
		try { 
			$connexion->exec($sql3);
			echo 'Le montant a bien été mis à jour';
		} catch(PDOException $e){
			echo 'erreur: '.$e->getMessage();
		}

	} else if(isset($_POST['addMontant']) && !isset($_SESSION['addName'])){
		echo 'addName ne passe pas.';
	}


// Ajouter Date
	if(!empty($_POST) && isset($_POST['datepicker']) && isset($_SESSION['addName'])){
		$addOldDate = $_POST['datepicker'];
		/*$addOldDate = date_format($addDate, 'Y-m-d');*/
		$addDate = date("Y-m-d", strtotime($addOldDate));
		$sql = "UPDATE friends SET dateFin = '".$addDate."' WHERE prenom = '".$_SESSION['addName']."' AND username = '".$pseudo."'";
		try {
			$connexion->exec($sql);
			echo 'Date bien modifiée';
		} catch(PDOException $e){
			echo 'erreur: '.$e->getMessage();
		}
	}


// Ajouter Note
	if(!empty($_POST) && isset($_POST['addNote']) && isset($_SESSION['addName'])){
		$addNote = $_POST['addNote'];
		$addNote = strip_tags($addNote);
		$addNote = addslashes($addNote);
		$sql = "UPDATE friends SET note = '".$addNote."' WHERE prenom = '".$_SESSION['addName']."' AND username = '".$pseudo."'";
		try {
			$req = $connexion->prepare($sql);
			$req->execute();
			echo 'La note/commentaire a bien été mis à jour';
		} catch(PDOException $e){
			echo 'erreur: '.$e->getMessage();
		}
	}


	function deleteName($ligne){
		global $connexion;
		$sql = "DELETE FROM friends WHERE id='".$ligne."'";
		try {
			$connexion->exec($sql);
			echo 'supprimé';	
		} catch(PDOException $e) {
			echo 'erreur: '.$e->getMessage();
		}
	}

	if(isset($_GET['tab']) && $_GET['del'] == true){
		deleteName($_GET['tab']);
	}





} else {
	header('Location:index.php');
}

?>
<!DOCTYPE html>
<html lang="fr" class="no-js">
<head>
   	<title>PHP | Membres</title>
  	<meta name="viewport" content="width=device-width, initial-scale=1.0">
   	<meta name="author" content="Alexis Bertin" />
   	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
   	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
   	<!-- link href="styles.css" rel="stylesheet" -->
   	<link rel="stylesheet" href="assets/css/jquery-ui.css">
   	<script type="text/javascript" src="assets/js/jquery.js"></script>
   	<script type="text/javascript" src="assets/js/jquery-ui.js"></script>
   	<script>
		$(function() {
			$( "#datepicker" ).datepicker();
			$('#ui-datepicker-div').appendTo('.calendar');
		});
  	</script>

</head>
<body>
	<div class="root">
		<div class="container">
			<div class="fullscreen page0">
				<div class="page-insider">
					<h3>Qui ?</h3>
			    	<form method="POST" action="private.php">
			       		<label for="addName">Ajouter une personne</label>
			    		<input type="text" name="addName" placeholder="nom de la personne" value="<?php if(isset($_POST['addName'])){ echo $_POST['addName']; } ?>" required />
			    		<input type="submit" value="Ajouter" />
			 			<div class="error"><?php if(isset($error_message_name)){ echo $error_message_name;} ?></div>
			    	</form>
				</div>
			</div>
			<div class="fullscreen page1">
				<div class="page-insider">
					<h3>Montant</h3>
		    		<form method="POST" action="private.php">
		    			<label for="addMontant">Combien ?</label>
		    			<input type="text" name="addMontant" placeholder="combien ça coute" value="<?php if(isset($_POST['addMontant'])){ echo $_POST['addMontant']; } ?>" required />
		    			<input type="submit" value="Ajouter" />
		    			<div class="error"><?php if(isset($error_message_montant)){ echo $error_message_montant; } ?></div>
		    		</form>
				</div>
			</div>
			<div class="fullscreen page2">
				<div class="page-insider">
					<h3>Calendrier</h3>
					<form action="private.php" method="POST">
						<div class="calendar"></div>
						<input type="text" id="datepicker" name="datepicker" value="<?php if(isset($_POST['datepicker'])){ echo $_POST['datepicker']; } ?>" />
						<input type="submit" value="INSERT DATE">
					</form>
				</div>
			</div>
			<div class="fullscreen page3">
				<div class="page-insider">
					<h3>Note</h3>
					<form action="private.php" method="POST">
						<label for="addNote">Note</label>
						<textarea name="addNote"></textarea>
						<input type="submit" value="Ajouter" />
					</form>
				</div>
			</div>
			<div class="fullscreen page4">
				<div class="page-insider">
					
				</div>
			</div>
			<div class="fullscreen page5">
				<div class="page-insider">
					
				</div>
			</div>
			<div class="fullscreen page6">
				<div class="page-insider">
					
				</div>
			</div>
		</div>
	</div>


	<div class="container">
	    <header>
	        <h1>Espace Privé</h1>
	    </header>

	    <div class="content">
	    <section>
	    	
	    </section>

	    <section>
	    	<a href="#onMeDoit">On me doit</a>
	    	<a href="#jeDois">Je dois</a>
	    	<a href="#depenses">Dépenses de groupe</a>
	    </section>

	    <section id="jeDois">
	    	<h2>Je dois</h2>

	    </section>
	    <section id="depenses">
	    	<h2>Dépenses de groupe</h2>

	    </section>

	    	<form method="POST" action="private.php">
		    	<select>
		    	<?php
		    		$sql = "SELECT nomDeListe FROM listes WHERE createdBy = '".$pseudo."'";
		    		$req = $connexion->prepare($sql);
		    		$req->execute();
		    		$tableau = $req->fetchAll();
		    		$count = $req->rowCount();

		    		for($i = 1; $i <= $count; $i++){
		    			echo '<option value="'.$tableau[$i-1]['nomDeListe'].'">'.$tableau[$i-1]['nomDeListe'].'</option>';
		    		}
		    	?>
		    	</select>
		    	<input type="submit" value="Envoyer" />
		    </form>

	    	
	       	
	    
	    	<ul>
	    	<?php
	    		$sql2 = "SELECT prenom, montant, liste, id FROM friends WHERE username = '".$pseudo."'";

	    		$req2 = $connexion->prepare($sql2);
	    		$req2->execute();
	    		$tableau = $req2->fetchAll();
	    		$count = $req2->rowCount();

	    		for ($i = 1; $i <= $count; $i++) {
	    		    echo '<li>';
	    		    for($x = 0; $x <= 2; $x++){
	    		    	echo '<span class="case">'.$tableau[$i-1][$x].'</span>';
	    		    }
	    		    echo '<a class="del" style="margin-left: 10px;" href="?tab='.$tableau[$i-1]['id'].'&del=true">X</a>';
	    		    echo '</li>';
	    		}

	    	?>
	    	</ul>
	        <br />
	        <a href="logout.php">Se déconnecter</a>
	    </div>
	</div>
</body>
</html> 