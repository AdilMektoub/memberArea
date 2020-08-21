<?php

	session_start();

	require('src/log.php');

//C'est pour VERIFIER la variable email et password 
	if(!empty($_POST['nom']) && !empty($_POST['prenom']) && !empty($_POST['email']) && !empty($_POST['password'])){

		require('src/connect.php');

		// VARIABLES 
		$nom 			= htmlspecialchars($_POST['nom']);
		$prenom 		= htmlspecialchars($_POST['prenom']);
		$email 			= htmlspecialchars($_POST['email']);
		$password		= htmlspecialchars($_POST['password']);

		// ADRESSE EMAIL SYNTAXE si il existe pas "!" le mail
		if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {

			header('location: index.php?error=1&message=Votre adresse email est invalide.');
			exit();

		}

		// CHIFFRAGE DU MOT DE PASSE avec sha fonction de hachage pour hacher le mot de passe
		$password = "aq1".sha1($password."123")."25";

		// EMAIL DEJA UTILISE
		$req = $db->prepare("SELECT count(*) as numberEmail FROM user WHERE email = ?");
		$req->execute(array($email));

		while($email_verification = $req->fetch()){
			if($email_verification['numberEmail'] != 1){
				header('location: index.php?error=1&message=Impossible de vous authentifier correctement.');
				exit();
			}
		}

		// CONNEXION à la BDD
		$req = $db->prepare("SELECT * FROM user WHERE email = ?");
		$req->execute(array($email));

		while($user = $req->fetch()){

			if($password == $user['password']){

				$_SESSION['connect'] = 1;
				$_SESSION['email']   = $user['email'];

				if(isset($_POST['auto'])){
					setcookie('auth', $user['secret'], time() + 364*24*3600, '/', null, false, true);
				}

				header('location: index.php?success=1');
				exit();

			}
			else {

				header('location: index.php?error=1&Impossible de vous authentifier correctement.');
				exit();

			}

		}

	}

?>


<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Formulaire de contact Adil</title>
	<link rel="stylesheet" type="text/css" href="design/default.css">
	<link rel="icon" type="image/pngn" href="img/favicon.png">
</head>
<body>

	<?php include('src/header.php'); ?>
	
	<section>
		<div id="login-body">

				<?php if(isset($_SESSION['connect'])) { ?>

					<h1>Bonjour !</h1>
					<?php
					if(isset($_GET['success'])){
						echo'<div class="alert success">Vous êtes maintenant connecté.</div>';
					} ?>
					<p>Qu'allez-vous regarder aujourd'hui ?</p>
					<small><a href="logout.php">Déconnexion</a></small>

				<?php } else { ?>
					<h1>Formulaire de Contact</h1>

					<?php if(isset($_GET['error'])) {

						if(isset($_GET['message'])) {
							echo'<div class="alert error">'.htmlspecialchars($_GET['message']).'</div>';
						}

					} ?>
					<!-- je ne comprend pas le css ne s'applique pas sur mon NOM et PRENOM alors que mon HTML est lié a mon "default.css"  -->
					<!-- j'ai remplacé id par placeholder car  -->
					<form method="post" action="index.php">
						<input type="email" name="email" placeholder="Votre adresse email" required />
						<input type="text" name="nom" placeholder="nom" required />
						<input type="text" name="prenom" placeholder="prenom" required />
						<input type="password" name="password" placeholder="Mot de passe" required />
						<button type="submit">Envoyer</button>
						<label id="option"><input type="checkbox" name="auto" checked />Se souvenir de moi</label>
					</form>
				

					<p class="grey">Première visite chez Adil ? <a href="inscription.php">Inscrivez-vous</a>.</p>
				<?php } ?>
		</div>
	</section>

	<?php include('src/footer.php'); ?>
</body>
</html>