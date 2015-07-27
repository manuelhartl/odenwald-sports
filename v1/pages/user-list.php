<?php
require_once 'lib/global.php';
require_once 'lib/users.php';

$reference = new Gps ();
$hasAdress = false;
$userextra = getUserExtraById ( $pdo, authUser ()->id );
if (isset ( $userextra->address_lat )) {
	$hasAdress = true;
	$reference->lat = $userextra->address_lat;
	$reference->long = $userextra->address_long;
}
?>
<table>
	<tr>
		<th>#</th>
		<th>Benutzer</th>
		<th>Mail</th>
		<th>Echter Name</th>
		<th>Telefon</th>
<?php
if ($hasAdress) {
	echo ("		<th>Entfernung zu " . authUser ()->username . "</th>");
}
?>		
		<th>Geburtsjahr</th>
		<th>Registration</th>
	</tr>
<?php

// dump actual user
$user = getUserObject ( getUserByName ( $pdo, authUser ()->username ) );
showUser ( "", $user, $userextra, null, $hasAdress, $hasAdress, true );
// space
echo ("	<tr>");
echo ("		<td>&nbsp;</td>");
echo ("	</tr>");

$stmt = $pdo->prepare ( 'select username,id,register_date,realname,birthdate,fk_user_id,111195 * ST_Distance(POINT(?,?), address_coord) as dist, address, mailing, phone ' . //
' from user u' . //
' left join user_extra ue ON (ue.fk_user_id=u.id) ' . //
' WHERE status = "verified"' . //
' AND u.id != ?' . //
' ORDER BY lower(u.username) ASC' ); //
ex2er ( $stmt, array (
		$reference->lat,
		$reference->long,
		authUser ()->id 
) );

//

$no = 1;
$showAdress = $hasAdress; // only if actual user has an adress
while ( $row = $stmt->fetch ( PDO::FETCH_ASSOC ) ) {
	unset ( $userextra );
	$userextra = false;
	$hasAdress = false;
	$user = getUserObject ( $row );
	$hasExtra = ! is_null ( $row ['fk_user_id'] );
	if ($hasExtra) {
		$userextra = getUserExtraObject ( $row );
		if (isset ( $row ['dist'] )) {
			$hasAdress = true;
		}
	}
	showUser ( $no, $user, $userextra, $row ['dist'], $hasAdress, $showAdress, false );
	$no = $no + 1;
}
function showUser($no, $user, $userextra, $distance, $hasAdress, $showAdress, $isActualUser) {
	echo '<tr class="' . ($no % 2 == 1 ? 'oddFirst' : 'evenfirst') . '">';
	echo '<td>' . $no . '</td>';
	echo '<td>' . createUserProfilLink ( $user,  createUserInfo ( $user, $userextra ) ) . '</td>';
	echo '<td><a style = "display: block;" href="?action=mail-user&toid=' . $user->id . '"><span class="flex">Mail</span></a></td>';
	
	if (isset ( $userextra )) {
		echo "		<td>" . (isset ( $userextra->realname ) ? $userextra->realname : "") . "</td>";
		echo "		<td>" . (isset ( $userextra->phone ) ? htmlentities ( $userextra->phone ) : "") . "</td>";
		if ($showAdress) {
			if ($hasAdress) {
				if ($isActualUser) {
					echo "		<td>" . (isset ( $userextra->address ) ? ($userextra->address) : "") . "</td>";
				} else {
					echo "		<td>" . (isset ( $distance ) ? formatMeters ( $distance ) : '') . "</td>";
				}
			} else {
				echo "		<td></td>";
			}
		}
		echo "		<td>" . (isset ( $userextra->birtdate ) ? $userextra->birtdate->format ( 'Y' ) : "") . "</td>";
	} else {
		echo "		<td>-</td>";
		echo "		<td>-</td>";
		if ($showAdress) {
			echo "		<td>-</td>";
		}
		echo "		<td>-</td>";
	}
	echo "<td>" . (isset ( $user->register_date ) ? $user->register_date->format ( 'd M Y' ) : "") . "</td>";
	echo "	</tr>";
}
?>
</table>