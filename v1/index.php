<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="Tours">
<meta name="author" content="Manuel Hartl">
<title>Touren</title>
<link href="css/bootstrap.css" rel="stylesheet" type="text/css">
<!-- <link href="css/bootstrap-theme.css" rel="stylesheet" type="text/css"> -->
<link href="css/bootstrap-datetimepicker.css" rel="stylesheet"
	type="text/css">
<link href="css/default.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/moment.js"></script>
<script type="text/javascript" src="js/bootstrap.js"></script>
<script type="text/javascript" src="js/bootstrap-datetimepicker.js"></script>
</head>
<body>
	<div class="container">
<?php
require_once 'lib/global.php';
require_once 'lib/db_users.php';
require_once 'lib/db_tours.php';
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
function setMessage($message) {
	echo '<div id="message">' . $message . '</div>';
}

$pdo = db_open ();
$input;
// work on form inputs
if (array_key_exists ( 'action', $_REQUEST )) {
	switch ($_REQUEST ['action']) {
		case 'login' :
			$userName = strtolower ( $_POST ['username'] );
			$password = $_POST ['password'];
			if (checkAuth ( $pdo, $userName, $password )) {
				login ( $pdo, $userName );
				setPage ( "home" );
				setMessage ( 'logged in' );
			} else {
				setPage ( "login" );
				$input ['username'] = $userName;
				setMessage ( 'login failed' );
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
			$userName = strtolower ( $_POST ['username'] );
			$password = $_POST ['password'];
			$email = $_POST ['email'];
			// validate
			if (strlen ( $userName ) < 3) {
				setMessage ( 'username must be at least 3 characters' );
				break;
			}
			if (strlen ( $password ) < 6) {
				setMessage ( 'password must be at least 6 characters"' );
				break;
			}
			if (! filter_var ( $email, FILTER_VALIDATE_EMAIL )) {
				setMessage ( 'email not valid' );
				break;
			}
			// register
			$hashedPassword = password_hash ( $password, PASSWORD_BCRYPT );
			$token = bin2hex ( openssl_random_pseudo_bytes ( 16 ) );
			$userId = registerUser ( $pdo, $userName, $hashedPassword, $email );
			if (! $userId) {
				print_r ( $pdo->errorInfo () );
				setMessage ( 'registering not successful' );
			}
			addActivationToken ( $pdo, $userId, $token );
			sendActivationMail ( $userName, $token, $email );
			setPage ( "register-save" );
			break;
		case 'tour-new' :
			setPage ( 'tour-new' );
			break;
		case 'tour-save' :
			echo "tour saved";
			$tour = new Tour ();
			$tour->guide = authUser ();
			$tour->description = $_REQUEST ['description'];
			$tour->duration = $_REQUEST ['duration'];
			$tour->meetingPoint = $_REQUEST ['meetingpoint'];
			$tour->startDateTime = $_REQUEST ['startdate'];
			insertTour ( $pdo, $tour );
			setPage ( 'home' );
			break;
		case 'tour-join' :
			$tourid = $_POST ['tourid'];
			tourJoin ( $pdo, authUser ()->id, $tourid );
			break;
		case 'tour-leave' :
			$tourid = $_POST ['tourid'];
			tourLeave ( $pdo, authUser ()->id, $tourid );
			break;
		case 'home' :
			setPage ( 'home' );
			break;
		default :
			die ();
	}
}

if (! hasAuth () && getPage () != "login" && getPage () != "register" && getPage () != "register-save") {
	echo "not logged in";
	die ();
}
require_once 'pages/navigation.php';
echo '<div id="main">';
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
		require_once 'pages/tour-list.php';
		require_once 'pages/logout.php';
		break;
	case "tour-new" :
		require_once 'pages/tour-new.php';
		require_once 'pages/logout.php';
		break;
}
echo '</div>';
/*
 * echo "<pre>";
 * print_r ( $_SESSION );
 * print_r ( $_GET );
 * print_r ( $_POST );
 * echo "</pre>";
 */
?>
</div>
</body>
</html>