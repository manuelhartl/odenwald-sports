<?php
require_once 'lib/global.php';
require_once 'lib/db_users.php';
require_once 'lib/users.php';
function setPage($page) {
	$_SESSION ['page'] = $page;
}
function getPage() {
	if (! array_key_exists ( 'page', $_SESSION )) {
		$_SESSION ['page'] = 'login';
	}
	return $_SESSION ['page'];
}

$pdo = db_open ();

// work on form inputs
if (array_key_exists ( 'action', $_POST )) {
	switch ($_POST ['action']) {
		case 'login' :
			$userName = $_POST ['username'];
			$password = $_POST ['password'];
			if (checkAuth ( $pdo, $userName, $password )) {
				login ( $pdo, $userName );
				setPage ( "home" );
				echo "logged in";
			} else {
				setPage ( "login" );
				echo "login failed";
			}
			break;
		case 'logout' :
			session_destroy ();
			echo "logged out";
			setPage ( "login" );
			break;
		case 'register' :
			setPage ( "register" );
			break;
		case 'register-save' :
			setPage ( "register" );
			$userName = $_POST ['username'];
			$password = $_POST ['password'];
			$email = $_POST ['email'];
			// validate
			if (strlen ( $userName ) < 3) {
				echo "username must be at least 3 characters";
				break;
			}
			if (strlen ( $password ) < 6) {
				echo "password must be at least 6 characters";
				break;
			}
			if (! filter_var ( $email, FILTER_VALIDATE_EMAIL )) {
				echo "email not valid";
				break;
			}
			// register
			$hashedPassword = password_hash ( $password, PASSWORD_BCRYPT );
			$token = bin2hex ( openssl_random_pseudo_bytes ( 16 ) );
			$userId = registerUser ( $pdo, $userName, $hashedPassword, $email );
			if (! $userId) {
				print_r ( $pdo->errorInfo () );
				echo "registering not successful";
			}
			addActivationToken ( $pdo, $userId, $token );
			sendActivationMail ( $userName, $token, $email );
			setPage ( "register-save" );
			break;
		case 'tour-join' :
			break;
		case 'tour-leave' :
			break;
		default :
			die ();
	}
}

if (! hasAuth () && getPage () != "login" && getPage () != "register" && getPage () != "register-save") {
	echo "not logged in";
	die ();
}
switch (getPage ()) {
	default :
	case "login" :
		require_once 'pages/login.php';
		break;
	case "register" :
		require_once 'pages/register.php';
		break;
	case "register-save" :
		setPage ( 'login' );
		$email = $_POST ['email'];
		require_once 'pages/register-save.php';
		break;
	case "home" :
		require_once 'pages/tours.php';
		include 'pages/logout.php';
		break;
}
/*
 * echo "<pre>";
 * print_r ( $_SESSION );
 * print_r ( $_GET );
 * print_r ( $_POST );
 * echo "</pre>";
 */

?>
