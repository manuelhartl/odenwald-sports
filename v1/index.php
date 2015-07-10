<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="Tours">
<meta name="author" content="Manuel Hartl">
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<title>Touren</title>
<link href="css/bootstrap.css" rel="stylesheet" type="text/css">
<!-- <link href="css/bootstrap-theme.css" rel="stylesheet" type="text/css"> -->
<link href="css/bootstrap-datetimepicker.css" rel="stylesheet" type="text/css">
<link href="css/jquery.rating.css" rel="stylesheet" type="text/css">
<link href="css/default.css" rel="stylesheet" type="text/css">
<link href="css/ost.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/moment.js"></script>
<script type="text/javascript" src="js/bootstrap.js"></script>
<script type="text/javascript" src="js/bootstrap-datetimepicker.js"></script>
<script type="text/javascript" src='http://maps.google.com/maps/api/js?sensor=false&libraries=places'></script>
<script type="text/javascript" src="js/locationpicker.jquery.js"></script>
<script type="text/javascript" src="js/jquery.rating.pack.js"></script>
</head>
<body>
	<div class="container-fluid">
<?php
require_once __DIR__ . '/lib/global.php';
require_once __DIR__ . '/lib/db_users.php';
require_once __DIR__ . '/lib/db_tours.php';
require_once __DIR__ . '/lib/users.php';
require_once __DIR__ . '/lib/tours.php';
function setPage($page) {
	$_SESSION ['page'] = $page;
}
function getPage() {
	if (! array_key_exists ( 'page', $_SESSION )) {
		$_SESSION ['page'] = 'home';
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
		case 'home' :
			setPage ( 'home' );
			break;
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
			setPage ( "home" );
			break;
		case 'password-reset' :
			setPage ( "password-reset" );
			break;
		case 'password-reset-save' :
			$email = $_POST ['email'];
			$username = $_POST ['username'];
			$input ['username'] = $username;
			$userrow = getUserByName ( $pdo, $username );
			$userid = $userrow ['id'];
			if (! validateEmail ( $email )) {
				setMessage ( 'Gib eine korrekte E-Mail Adresse ein' );
			} else if ($userrow ['email'] != $email) {
				setMessage ( 'Die E-Email Adresse ist falsch' );
			} else {
				$token = bin2hex ( openssl_random_pseudo_bytes ( 16 ) );
				addPasswordresetToken ( $pdo, $userid, $token );
				sendPasswordresetMail ( $username, $token, $email );
				setMessage ( 'Passwort reset link gesendet an: ' . $email );
				setPage ( 'login' );
			}
			break;
		case 'register' :
			setPage ( "register" );
			break;
		case 'register-save' :
			setPage ( "register" );
			$username = $_POST ['username'];
			$password = $_POST ['password'];
			$password2 = $_POST ['password2'];
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
			
			if (! validatePassword ( $password )) {
				setMessage ( 'password must be at least 6 characters' );
				break;
			}
			if ($password != $password2) {
				setMessage ( 'passwords do not match' );
				break;
			}
			if (! validateEmail ( $email )) {
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
					case 'tour-list-canceled' :
						$input ['showcanceled'] = $_REQUEST ['showcanceled'];
						$input ['showold'] = $_REQUEST ['showold'];
						break;
					case 'tour-list-old' :
						$input ['showcanceled'] = $_REQUEST ['showcanceled'];
						$input ['showold'] = $_REQUEST ['showold'];
						break;
					case 'tour-list' :
						break;
					case 'password-change' :
						setPage ( "password-change" );
						break;
					case 'password-change-save' :
						$oldpassword = $_POST ['oldpassword'];
						$newpassword = $_POST ['newpassword'];
						$newpassword2 = $_POST ['newpassword2'];
						if (! validatePassword ( $newpassword )) {
							setMessage ( 'password must be at least 6 characters"' );
						} else if (! checkAuth ( $pdo, authUser ()->username, $oldpassword )) {
							setMessage ( 'Altes Passwort falsch' );
						} else if ($newpassword != $newpassword2) {
							setMessage ( 'Die Eingaben f&uuml;r das neue Passwort stimmen nicht &uuml;berein' );
						} else {
							$hashedPassword = password_hash ( $newpassword, PASSWORD_BCRYPT );
							if (changePassword ( $pdo, authUser ()->id, $hashedPassword )) {
								setMessage ( 'Passwort erfolgreich ge&auml;ndert' );
								setPage ( "home" );
							} else {
								setMessage ( 'Passwort nicht erfolgreich ge&auml;ndert - contact admin' );
							}
						}
						break;
					case 'tour-new' :
						$sports = getSports ( $pdo );
						$input ['sport'] = 1; // MTB is default
						$input ['skill'] = 3; //
						$input ['speed'] = 3; //
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
						$input ['skill'] = $tour->skill;
						$input ['speed'] = $tour->speed;
						$input ['distance'] = $tour->distance / 1000;
						$input ['elevation'] = $tour->elevation;
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
						$input ['skill'] = $_REQUEST ['skill'];
						$input ['speed'] = $_REQUEST ['speed'];
						$input ['elevation'] = $_REQUEST ['elevation'];
						$input ['distance'] = $_REQUEST ['distance'];
						if (isset ( $_REQUEST ['sport'] )) {
							$input ['sport'] = $_REQUEST ['sport'];
						}
						if (isset ( $_REQUEST ['startdate'] )) {
							$input ['startdate'] = $_REQUEST ['startdate'];
						}
						// if (strlen ( $_REQUEST ['description'] ) < 10) {
						// setMessage ( 'Beschreibung zu kurz' );
						// } else
						// TODO: check that startdate is 15mins in future
						if (strlen ( $_REQUEST ['meetingpoint'] ) < 5) {
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
							$tour->skill = $_REQUEST ['skill'];
							$tour->speed = $_REQUEST ['speed'];
							$tour->distance = $_REQUEST ['distance'] * 1000;
							$tour->elevation = $_REQUEST ['elevation'];
							updateTour ( $pdo, $tour );
							mailUpdateTour ( $pdo, $tour );
							setMessage ( 'tour updated' );
							setPage ( 'home' );
						} else {
							// new
							$date = DateTime::createFromFormat ( 'd.m.Y H:i', $_REQUEST ['startdate'] );
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
								$tour->skill = $_REQUEST ['skill'];
								$tour->speed = $_REQUEST ['speed'];
								$tour->distance = $_REQUEST ['distance'] * 1000;
								$tour->elevation = $_REQUEST ['elevation'];
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
						if (! $tour->canceled && ($tour->startDateTime >= new DateTime ())) {
							tourJoin ( $pdo, authUser ()->id, $tourid );
							setMessage ( 'tour joined' );
						}
						break;
					case 'tour-leave' :
						$tourid = $_POST ['tourid'];
						$tour = getTourById ( $pdo, $tourid );
						if (! $tour->canceled && ($tour->startDateTime >= new DateTime ())) {
							tourLeave ( $pdo, authUser ()->id, $tourid );
							setMessage ( 'left tour' );
						}
						break;
					case 'tour-cancel' :
						$tourid = $_POST ['tourid'];
						$tour = getTourById ( $pdo, $tourid );
						if ((authUser ()->id == $tour->guide->id) && ($tour->startDateTime >= new DateTime ())) {
							tourCancel ( $pdo, $tourid );
							mailCancelTour ( $pdo, $tour );
							setMessage ( 'tour abgesagt' );
						}
						break;
					case 'user-list' :
						setPage ( 'user-list' );
						break;
					case 'user-edit' :
						$input ['userid'] = authUser ()->id;
						$userextra = getUserExtraById ( $pdo, $input ['userid'] );
						if ($userextra) {
							$input ['realname'] = $userextra->realname;
							$input ['birthdate'] = $userextra->birtdate;
							$input ['address'] = $userextra->address;
							$input ['mailing'] = $userextra->mailing;
							$input ['phone'] = $userextra->phone;
						} else {
							$input ['realname'] = '';
							$input ['birthdate'] = DateTime::createFromFormat ( 'Y', '0000' );
							$input ['address'] = '';
							$input ['mailing'] = true;
						}
						if (isset ( $userextra->address_lat )) {
							$input ['address-lat'] = $userextra->address_lat;
						}
						if (isset ( $userextra->address_long )) {
							$input ['address-lon'] = $userextra->address_long;
						}
						setPage ( "user-edit" );
						break;
					case 'user-save' :
						if (isset ( $_REQUEST ['userid'] )) {
							// edit
							$userextra = new UserExtra ();
							$userextra->id = authUser ()->id;
							$userextra->birtdate = DateTime::createFromFormat ( 'Y', $_REQUEST ['birthdate'] );
							$userextra->realname = $_REQUEST ['realname'];
							$userextra->address = $_REQUEST ['address'];
							$userextra->address_lat = $_REQUEST ['address-lat'];
							$userextra->address_long = $_REQUEST ['address-lon'];
							if (! userExtraExists ( $pdo, $userextra->id )) {
								addUserExtra ( $pdo, $userextra->id );
							}
							$userextra->mailing = isset ( $_REQUEST ['mailing'] ) ? $_REQUEST ['mailing'] == 'true' : false;
							$userextra->phone = $_REQUEST ['phone'];
							updateUserExtra ( $pdo, $userextra );
							setMessage ( 'Profil aktualisiert' );
							setPage ( 'home' );
						}
						$input ['userid'] = authUser ()->id;
						break;
					default :
						die ();
				}
			}
	}
}

if (! hasAuth () && //
(getPage () != "login") && //
(getPage () != "register") && //
(getPage () != "register-save") && //
(getPage () != "password-reset") && //
(getPage () != "password-reset-save")) //
{
	setPage ( 'home' );
}
echo '<div class="row">';
echo '<div class="col-xs-12" id="navigation">';
require_once 'pages/navigation.php';
echo '</div>';
echo '</div>';

echo '<div class="row">';
echo '<div class="col-xs-12" id="main">';
switch (getPage ()) {
	case 'login' :
		require_once 'pages/login.php';
		break;
	case 'password-change' :
		require_once 'pages/password-change.php';
		break;
	case 'password-reset' :
		require_once 'pages/password-reset.php';
		break;
	case 'register' :
		require_once 'pages/register.php';
		break;
	case 'register-save' :
		setPage ( 'login' );
		$email = $_POST ['email'];
		require_once 'pages/register-save.php';
		break;
	default :
	case 'home' :
		require_once 'pages/tour-list.php';
		break;
	case 'tour-edit' :
	case 'tour-new' :
		require_once 'pages/tour-new-edit.php';
		break;
	case 'user-list' :
		require_once 'pages/user-list.php';
		break;
	case 'user-edit' :
		require_once 'pages/user-edit.php';
		break;
}
echo '</div>';
echo '</div>';
echo '<div id="version"><a href="html/disclaimer.php" target="_blank">Regeln</a> <a href="html/impressum.php" target="_blank">Impressum</a> Version: ' . $config ['version'] . '</div>';
echo '</div>';

/*
 * echo "<pre>";
 * print_r ( $_SESSION );
 * print_r ( $_REQUEST );
 * echo "</pre>";
 */
?>
</body>
</html>