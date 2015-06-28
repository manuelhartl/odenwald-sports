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
<script type="text/javascript"
	src='http://maps.google.com/maps/api/js?sensor=false&libraries=places'></script>
<script type="text/javascript" src="js/locationpicker.jquery.js"></script>
</head>
<body>
	<div class="container-fluid">
<?php
require_once 'lib/global.php';
require_once 'lib/db_users.php';
require_once 'lib/db_tours.php';
require_once 'lib/users.php';
require_once 'lib/tours.php';
function setPage($page) {
	$_SESSION ['page'] = $page;
}
function getPage() {
	if (! array_key_exists ( 'page', $_SESSION )) {
		$_SESSION ['page'] = 'login';
	}
	return $_SESSION ['page'];
}
$_SESSION ['message'] = '';
function setMessage($msg) {
	$_SESSION ['message'] = $msg;
}

$pdo = db_open ();
$input;
// work on form inputs
if (array_key_exists ( 'action', $_REQUEST )) {
	switch ($_REQUEST ['action']) {
		case 'login' :
			$username = $_POST ['username'];
			$password = $_POST ['password'];
			if (checkAuth ( $pdo, $username, $password )) {
				login ( $pdo, $username );
				setPage ( "home" );
				setMessage ( 'logged in' );
			} else {
				setPage ( "login" );
				$input ['username'] = $username;
				setMessage ( 'login failed' );
			}
			break;
		case 'logout' :
			session_destroy ();
			unset ( $_SESSION );
			setMessage ( 'logged out' );
			setPage ( "login" );
			break;
		case 'register' :
			setPage ( "register" );
			break;
		case 'register-save' :
			setPage ( "register" );
			$username = $_POST ['username'];
			$password = $_POST ['password'];
			$email = $_POST ['email'];
			// validate
			if (! preg_match ( "/^[a-zA-Z][0-9a-zA-Z]*$/", $username )) {
				setMessage ( 'username must only consist of A-Z and 0-9 and start with a character' );
				break;
			}
			if (strlen ( $username ) < 2) {
				setMessage ( 'username must be at least 2 characters' );
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
			// check if email already registered
			if (userExists ( $pdo, $username )) {
				setMessage ( $username . ' already registered' );
				break;
			}
			// check if name is already registered
			if (emailExists ( $pdo, $email )) {
				setMessage ( $email . ' is already registered' );
				break;
			}
			// register
			$hashedPassword = password_hash ( $password, PASSWORD_BCRYPT );
			$token = bin2hex ( openssl_random_pseudo_bytes ( 16 ) );
			$userId = registerUser ( $pdo, $username, $hashedPassword, $email );
			if (! $userId) {
				print_r ( $pdo->errorInfo () );
				setMessage ( 'registering not successful' );
			}
			addActivationToken ( $pdo, $userId, $token );
			sendActivationMail ( $username, $token, $email );
			$input ['username'] = $username;
			$input ['email'] = $email;
			setPage ( "register-save" );
			break;
		default :
			if (! hasAuth ()) {
				setPage ( 'login' );
			} else {
				switch ($_REQUEST ['action']) {
					case 'tour-new' :
						$sports = getSports ( $pdo );
						$input ['sport'] = 1; // MTB is default
						setPage ( 'tour-new' );
						break;
					case 'tour-edit' :
						$tourid = $_POST ['tourid'];
						$tour = getTourById ( $pdo, $tourid );
						$input ['tourid'] = $tourid;
						$input ['sport'] = $tour->sport->sportsubid;
						$input ['meetingpoint'] = $tour->meetingPoint;
						$input ['meetingpoint-lat'] = $tour->meetingPoint_lat;
						$input ['meetingpoint-lon'] = $tour->meetingPoint_long;
						$input ['description'] = $tour->description;
						$input ['startdate'] = $tour->startDateTime;
						$input ['duration'] = $tour->duration;
						setPage ( 'tour-edit' );
						break;
					case 'tour-save' :
						$sports = getSports ( $pdo );
						if (isset ( $_REQUEST ['tourid'] )) {
							$input ['tourid'] = $_REQUEST ['tourid'];
						}
						$input ['meetingpoint'] = $_REQUEST ['meetingpoint'];
						$input ['meetingpoint-lat'] = $_REQUEST ['meetingpoint-lat'];
						$input ['meetingpoint-lon'] = $_REQUEST ['meetingpoint-lon'];
						$input ['description'] = $_REQUEST ['description'];
						$input ['duration'] = $_REQUEST ['duration'];
						if (isset ( $_REQUEST ['sport'] )) {
							$input ['sport'] = $_REQUEST ['sport'];
						}
						if (isset ( $_REQUEST ['startdate'] )) {
							$input ['startdate'] = $_REQUEST ['startdate'];
						}
						if (strlen ( $_REQUEST ['description'] ) < 10) {
							setMessage ( 'Beschreibung zu kurz' );
						} else if (strlen ( $_REQUEST ['meetingpoint'] ) < 5) {
							setMessage ( 'Treffpunkt angeben' );
						} else if (strlen ( $_REQUEST ['duration'] ) < 1) {
							setMessage ( 'Dauer angeben' );
						} else if (isset ( $_POST ['tourid'] )) {
							// edit
							$tour = getTourById ( $pdo, $_POST ['tourid'] );
							if ($tour->guide->id != authUser ()->id) {
								// at the moment only the guide may edit a tour
								die ();
							}
							$tour->description = $_REQUEST ['description'];
							$tour->duration = $_REQUEST ['duration'];
							$tour->meetingPoint = $_REQUEST ['meetingpoint'];
							$tour->meetingPoint_lat = $_REQUEST ['meetingpoint-lat'];
							$tour->meetingPoint_long = $_REQUEST ['meetingpoint-lon'];
							updateTour ( $pdo, $tour );
							setMessage ( 'tour updated' );
							setPage ( 'home' );
						} else {
							// new
							$date = DateTime::createFromFormat('d.m.Y H:i', $_REQUEST ['startdate']);
							if (strlen ( $_REQUEST ['startdate'] ) < 14) {
								setMessage ( 'Datum angeben' );
							} else if ($date < date ( 'Y-m-d H:i:s' )) {
								setMessage ( 'Datum muss in der Zukunft liegen' );
							} else {
								// new
								$tour = new Tour ();
								$tour->guide = authUser ();
								$tour->description = $_REQUEST ['description'];
								$tour->duration = $_REQUEST ['duration'];
								$tour->sport = getSport ( $pdo, $_REQUEST ['sport'] );
								$tour->meetingPoint = $_REQUEST ['meetingpoint'];
								$tour->meetingPoint_lat = $_REQUEST ['meetingpoint-lat'];
								$tour->meetingPoint_long = $_REQUEST ['meetingpoint-lon'];
								$tour->startDateTime = $date;
								if (insertTour ( $pdo, $tour )) {
									mailNewTour ( $pdo, $tour );
									setMessage ( 'tour saved' );
								} else {
									// mail admin?
									setMessage ( 'internal error - mail admin' );
								}
								setPage ( 'home' );
							}
						}
						break;
					case 'tour-join' :
						$tourid = $_POST ['tourid'];
						$tour = getTourById ( $pdo, $tourid );
						if ($tour->canceled) {
							die (); // hack
						}
						tourJoin ( $pdo, authUser ()->id, $tourid );
						setMessage ( 'tour joined' );
						break;
					case 'tour-leave' :
						$tourid = $_POST ['tourid'];
						$tour = getTourById ( $pdo, $tourid );
						if ($tour->canceled) {
							die (); // hack
						}
						tourLeave ( $pdo, authUser ()->id, $tourid );
						setMessage ( 'left tour' );
						break;
					case 'tour-cancel' :
						$tourid = $_POST ['tourid'];
						$tour = getTourById ( $pdo, $tourid );
						if (authUser ()->id != $tour->guide->id) {
							die ();
						}
						tourCancel ( $pdo, $tourid );
						mailCancelTour ( $pdo, $tour );
						setMessage ( 'tour abgesagt' );
						break;
					case 'tour-list' :
					case 'home' :
						setPage ( 'home' );
						break;
					default :
						die ();
				}
			}
	}
}

if (! hasAuth () && getPage () != "login" && getPage () != "register" && getPage () != "register-save") {
	echo "not logged in";
	setPage ( 'login' );
}
echo '<div class="row">';
if (hasAuth ()) {
	echo '<div class="col-md-2">';
	require_once 'pages/navigation.php';
	echo '</div>';
}
echo '<div class="col-md-1">';
echo '<div id="message">' . $_SESSION ['message'] . '</div>';
echo '</div>';
echo '</div>';

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
		break;
	case "tour-edit" :
	case "tour-new" :
		require_once 'pages/tour-new-edit.php';
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