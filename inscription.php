<?php
	
	session_start();

	require('src/log.php');

	if(!isset($_SESSION['connect'])){
		header('location: index.php');
		exit();
	}

	if(!empty($_POST['nom']) && !empty($_POST['prenom']) && !empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['password_two'])){

		require('src/connect.php');

		// les VARIABLES nom, prenom, email, password
		$nom 				= htmlspecialchars($_POST['nom']);
		$prenom 			= htmlspecialchars($_POST['prenom']);
		$email 				= htmlspecialchars($_POST['email']);
		$password 			= htmlspecialchars($_POST['password']);
		//si le premier mot de passe est différent du deuxiéme mot de passe
		$password_two		= htmlspecialchars($_POST['password_two']);

		//NOM
		//si le nom ne correspond pas à la variable (BDD)
		if(!filter_var($nom, FILTER_VALIDATE_TEXT)){

			header('location: inscription.php?error=1&message=Votre NOM est invalide.');
			exit();

		}

		//PRENOM
		//si le prenom ne correspond pas à la variable (BDD)
		if(!filter_var($prenom, FILTER_VALIDATE_TEXT)){

			header('location: inscription.php?error=1&message=Votre PRENOM est invalide.');
			exit();

		}

		//EMAIL
		// ADRESSE EMAIL VALIDE dans le cas l'email ne correspond pas a la sa variable (BDD)
		if(!filter_var($email, FILTER_VALIDATE_EMAIL)){

			header('location: inscription.php?error=1&message=Votre adresse EMAIL est invalide.');
			exit();

		}

		// EMAIL DEJA UTILISEE
		$req = $db->prepare("SELECT count(*) as numberEmail FROM user WHERE email = ?");
		$req->execute(array($email));

		while($email_verification = $req->fetch()){

			if($email_verification['numberEmail'] != 0){

				header('location: inscription.php?error=1&message=Votre adresse email est déjà utilisée par un autre utilisateur.');
				exit();

			}

		}

		// MOT DE PASSE
		// et si oui PASSWORD different du deuxiéme mot de passe
		if($password != $password_two){

			header('location: inscription.php?error=1&message=Vos MOT DE PASSES ne sont pas identiques.');
			exit();

		}

		// HASH
		$secret = sha1($email).time();
		$secret = sha1($secret).time();

		// CHIFFRAGE DU MOT DE PASSE
		$password = "aq1".sha1($password."123")."25";

		// ENVOI
		$req = $db->prepare("INSERT INTO user(email, password, secret) VALUES(?,?,?)");
		$req->execute(array($email, $password, $secret));

		header('location: inscription.php?success=1');
		exit();

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
			<h1>S'inscrire</h1>

			<?php if(isset($_GET['error'])){

				if(isset($_GET['message'])) {

					echo'<div class="alert error">'.htmlspecialchars($_GET['message']).'</div>';

				}

			} else if(isset($_GET['success'])) {

				echo'<div class="alert success">Vous êtes désormais inscrit. <a href="index.php">Connectez-vous</a>.</div>';

			} ?>

			<form method="post" action="inscription.php">
				<input type="email" name="email" placeholder="Votre adresse email" required />
				<input type="text" name="nom" placeholder="nom" required />
				<input type="text" name="prenom" placeholder="prenom" required />
				<input type="password" name="password" placeholder="Mot de passe" required />
				<input type="password" name="password_two" placeholder="Retapez votre mot de passe" required />
				<button type="submit">S'inscrire</button>
			</form>

			<p class="grey">Vous êtes déjà enregistrer ? <a href="index.php">Connectez-vous</a>.</p>
		</div>
	</section>

	<?php include('src/footer.php'); ?>
</body>
</html>