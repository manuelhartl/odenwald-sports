<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="Tours">
<meta name="author" content="Manuel Hartl">
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<title>Tourportal sport2gether</title>
<link href="css/bootstrap.css" rel="stylesheet" type="text/css">
<!-- <link href="css/bootstrap-theme.css" rel="stylesheet" type="text/css"> -->
<link href="css/bootstrap-datetimepicker.css" rel="stylesheet" type="text/css">
<link href="css/jquery.rating.css" rel="stylesheet" type="text/css">
<link href="css/s2t.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/moment.js"></script>
<script type="text/javascript" src="js/bootstrap.js"></script>
<script type="text/javascript" src="js/bootstrap-datetimepicker.js"></script>
<script type="text/javascript" src='http://maps.google.com/maps/api/js?sensor=false&libraries=places'></script>
<script type="text/javascript" src="js/locationpicker.jquery.js"></script>
<script type="text/javascript" src="js/jquery.rating.pack.js"></script>
</head>
<body>
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
	$_SESSION ['message'] = ($msg);
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
				setMessage ( $username . ', du bist jetzt angemeldet' );
			} else {
				setPage ( "login" );
				$input ['username'] = $username;
				setMessage ( $username . ', leider hat das Anmelden nicht geklappt' );
			}
			break;
		case 'logout' :
			session_destroy ();
			unset ( $_SESSION );
			setMessage ( "Auf Wiedersehen" );
			setPage ( "home" );
			break;
		case 'password-reset' :
			setPage ( "Ihr Passwort wurde zur&uuml;ckgesetzt" );
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
			if (! hasAuth ()) {
				setPage ( "register" );
			} else {
				setPage ( "home" );
			}
			break;
		case 'register-save' :
			setPage ( "register" );
			$username = $_POST ['username'];
			$password = $_POST ['password'];
			$password2 = $_POST ['password2'];
			$email = $_POST ['email'];
			$acceptrules = isset ( $_REQUEST ['acceptrules'] ) ? $_REQUEST ['acceptrules'] == 'true' : false;
			
			$input ['username'] = $username;
			$input ['email'] = $email;
			$input ['acceptrules'] = $acceptrules;
			// validate
			if (! preg_match ( "/^[0-9a-zA-Z]*$/", $username )) {
				setMessage ( 'Der Benutzername darf nur A-Z und 0-9 enthalten' );
				break;
			}
			if (strlen ( $username ) < 2) {
				setMessage ( 'Der Benutzername mu&szlig; mindestens aus 2 Zeichen bestehen' );
				break;
			}
			if (! validatePassword ( $password )) {
				setMessage ( 'Das Passwort mu&szlig; mindestens 6 Zeichen lang sein' );
				break;
			}
			if ($password != $password2) {
				setMessage ( 'Die Passw&ouml;rter stimmen nicht &uuml;berein' );
				break;
			}
			if (! validateEmail ( $email )) {
				setMessage ( 'Die E-Mail Adresse ist nicht g&uuml;ltig' );
				break;
			}
			// check if email already registered
			if (userExists ( $pdo, $username )) {
				setMessage ( $username . ' ist schon registriert' );
				break;
			}
			// check if name is already registered
			if (emailExists ( $pdo, $email )) {
				setMessage ( $email . ' is schon registriert' );
				break;
			}
			if (! $acceptrules) {
				setMessage ( 'Bitte die Regeln der Seite akzeptieren' );
				break;
			}
			
			// register
			$hashedPassword = password_hash ( $password, PASSWORD_BCRYPT );
			$token = bin2hex ( openssl_random_pseudo_bytes ( 16 ) );
			$userId = registerUser ( $pdo, $username, $hashedPassword, $email );
			if (! $userId) {
				setMessage ( 'Registrierung nicht erfolgreich' );
				break;
			}
			addActivationToken ( $pdo, $userId, $token );
			sendActivationMail ( $username, $token, $email );
			$input ['username'] = $username;
			$input ['email'] = $email;
			setPage ( "home" );
			setMessage ( 'Aktivierungsmail wurde versendet' );
			break;
		default :
			if (! hasAuth ()) {
				setPage ( 'login' );
			} else {
				switch ($_REQUEST ['action']) {
					case 'mail-user' :
						setPage ( 'mail' );
						$toid = $_REQUEST ['toid'];
						$touser = getUserById ( $pdo, $toid );
						$input ['to'] = $touser ['username'];
						break;
					case 'mail-send' :
						$to = $_REQUEST ['to'];
						$subject = $_REQUEST ['subject'];
						$body = $_REQUEST ['body'];
						$input ['to'] = $_REQUEST ['to'];
						$input ['subject'] = $_REQUEST ['subject'];
						$input ['body'] = $_REQUEST ['body'];
						if (empty ( $subject )) {
							setMessage ( 'Bitte einen Betreff eingeben' );
						} else if (empty ( $body )) {
							setMessage ( 'Bitte eine Nachricht eingeben' );
						} else {
							$touser = getUserByName ( $pdo, $to );
							sendmail ( $touser ['email'], 'Nachricht von ' . authUser ()->username . ' mit Betreff: ' . $subject, $body );
							setPage ( 'home' );
							setMessage ( 'Mail an ' . $touser ['username'] . ' gesendet' );
						}
						break;
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
						$input ['meetingpoint_desc'] = $tour->meetingPoint_desc;
						$input ['meetingpoint-lat'] = $tour->meetingPoint_lat;
						$input ['meetingpoint-lon'] = $tour->meetingPoint_long;
						$input ['description'] = $tour->description;
						$input ['startdate'] = $tour->startDateTime->format ( 'd.m.Y H:i' );
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
						$input ['meetingpoint_desc'] = $_REQUEST ['meetingpoint_desc'];
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
						$date = DateTime::createFromFormat ( 'd.m.Y H:i', $_REQUEST ['startdate'] );
						if (strlen ( $_REQUEST ['meetingpoint'] ) < 5) {
							setMessage ( 'Treffpunkt angeben' );
						} else if (strlen ( $_REQUEST ['duration'] ) < 1) {
							setMessage ( 'Dauer angeben' );
						} else if ($date < date ( 'Y-m-d H:i:s' )) {
							setMessage ( 'Datum muss in der Zukunft liegen' );
						} else if (isset ( $_POST ['tourid'] )) {
							// edit
							$tour = getTourById ( $pdo, $_POST ['tourid'] );
							if ($tour->guide->id != authUser ()->id) {
								// at the moment only the guide may edit a tour
								die ();
							}
							$tour->startDateTime = $date;
							$tour->description = $_REQUEST ['description'];
							$tour->duration = $_REQUEST ['duration'];
							$tour->meetingPoint = $_REQUEST ['meetingpoint'];
							$tour->meetingPoint_desc = $_REQUEST ['meetingpoint_desc'];
							$tour->meetingPoint_lat = $_REQUEST ['meetingpoint-lat'];
							$tour->meetingPoint_long = $_REQUEST ['meetingpoint-lon'];
							$tour->skill = $_REQUEST ['skill'];
							$tour->speed = $_REQUEST ['speed'];
							$tour->distance = $_REQUEST ['distance'] * 1000;
							$tour->elevation = $_REQUEST ['elevation'];
							updateTour ( $pdo, $tour );
							mailUpdateTour ( $pdo, $tour );
							setMessage ( 'Die Tour wurde erfolgreich ge&auml;ndert' );
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
								$tour->meetingPoint_desc = $_REQUEST ['meetingpoint_desc'];
								$tour->meetingPoint_lat = $_REQUEST ['meetingpoint-lat'];
								$tour->meetingPoint_long = $_REQUEST ['meetingpoint-lon'];
								$tour->startDateTime = $date;
								$tour->skill = $_REQUEST ['skill'];
								$tour->speed = $_REQUEST ['speed'];
								$tour->distance = $_REQUEST ['distance'] * 1000;
								$tour->elevation = $_REQUEST ['elevation'];
								if (insertTour ( $pdo, $tour )) {
									mailNewTour ( $pdo, $tour );
									setMessage ( 'Die Tour wurde erfolgreich gespeichert' );
								} else {
									// mail admin?
									setMessage ( 'Interner Fehler' );
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
							setMessage ( 'Danke f&uuml;r das Anmelden an der Tour' );
						}
						break;
					case 'tour-leave' :
						$tourid = $_POST ['tourid'];
						$tour = getTourById ( $pdo, $tourid );
						if (! $tour->canceled && ($tour->startDateTime >= new DateTime ())) {
							tourLeave ( $pdo, authUser ()->id, $tourid );
							setMessage ( 'Schade, das n&auml;chste mal klappt es wieder mit dem Mitfahren' );
						}
						break;
					case 'tour-cancel' :
						$tourid = $_POST ['tourid'];
						$tour = getTourById ( $pdo, $tourid );
						if ((authUser ()->id == $tour->guide->id) && ($tour->startDateTime >= new DateTime ())) {
							tourCancel ( $pdo, $tourid );
							mailCancelTour ( $pdo, $tour );
							setMessage ( 'Schade, das Du die Tour abgesagt werden musstest' );
						}
						break;
					case 'tour-view' :
						$tourid = $_REQUEST ['tourid'];
						$tour = getTourById ( $pdo, $tourid );
						setPage ( 'tour-view' );
						break;
					case 'user-list' :
						setPage ( 'user-list' );
						break;
					case 'user-edit' :
					case 'user-view' :
						$input ['userid'] = ($_REQUEST ['action'] == 'user-edit') ? authUser ()->id : $_REQUEST ['userid'];
						$user = getUserObject ( getUserById ( $pdo, $input ['userid'] ) );
						$userextra = getUserExtraById ( $pdo, $input ['userid'] );
						$input ['username'] = $user->username;
						if ($userextra) {
							$input ['realname'] = $userextra->realname;
							$input ['birthdate'] = $userextra->birtdate;
							$input ['address'] = $userextra->address;
							$input ['mailing'] = $userextra->mailing;
							$input ['phone'] = $userextra->phone;
							$input ['address-lat'] = $userextra->address_lat;
							$input ['address-lon'] = $userextra->address_long;
						} else {
							$input ['realname'] = '';
							$input ['birthdate'] = '';
							$input ['address'] = '';
							// default value true
							$input ['mailing'] = true;
							$input ['phone'] = '';
							$input ['address-lat'] = false;
							$input ['address-lon'] = false;
						}
						
						setPage ( $_REQUEST ['action'] );
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

if (isset ( $config ['beta'] ) && $config ['beta']) {
	echo '<div id="beta">';
	echo 'Beta Sport2gether Beta sport2gether Beta sport2gether Beta sport2gether Beta sport2gether Beta sport2gether Beta sport2gether Beta sport2gether ';
	echo $config ['version'];
	echo '</div>';
}

echo '<div class="total">';
echo '<div id="navigation">';
require_once 'pages/navigation.php';
echo '</div> <!--End div navigation -->';

echo '<div class="content" id="main">';
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
	case 'mail' :
		require_once 'pages/mail.php';
		break;
	case 'tour-edit' :
	case 'tour-new' :
		require_once 'pages/tour-new-edit.php';
		break;
	case 'tour-view' :
		require_once 'pages/tour-view.php';
		break;
	case 'user-list' :
		require_once 'pages/user-list.php';
		break;
	case 'user-edit' :
		require_once 'pages/user-edit.php';
		break;
	case 'user-view' :
		require_once 'pages/user-view.php';
		break;
}
echo '</div> <!--End div content -->';
echo '<div id="footer">';
require_once 'pages/footer.php';
echo '</div> <!--End div footer -->';
echo '</div> <!--End div total -->';
// print_r ( session_get_cookie_params () );
/*
 * echo "<pre>";
 * print_r ( $_SESSION );
 * print_r ( $_REQUEST );
 * echo "</pre>";
 */
?>

</body>
</html>