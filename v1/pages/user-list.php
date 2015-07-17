<?php
/*
 * <form name="tour-list-update" action="" method="post">
 * <input name="action" type="hidden" value="tour-list-canceled" />
 * <?php
 * if (getInVa ( 'showcanceled' ) == 'true') {
 * echo '<input name="showcanceled" type="hidden" value="false" />';
 * echo '<input name="submit-tour-list-update" type="submit" value="Abgesagte Touren verstecken" />';
 * } else {
 * echo '<input name="showcanceled" type="hidden" value="true" />';
 * echo '<input name="submit-tour-list-update" type="submit" value="Abgesagte Touren anzeigen" />';
 * }
 * ?>
 * </form>
 */
?>
<table>
	<tr>
		<th>Nr.</th>
		<th>Benutzer</th>
		<th>Echter Name</th>
		<th>Telefon</th>
		<th>Entfernung zu mir</th>
		<th>Geburtsjahr</th>
		<th>Registration</th>
	</tr>
<?php
require_once 'lib/global.php';
require_once 'lib/users.php';

$reference = new Gps ();
$userextra = getUserExtraById ( $pdo, authUser ()->id );
if (isset ( $userextra->address_lat )) {
	$reference->lat = $userextra->address_lat;
	$reference->long = $userextra->address_long;
}

$stmt = $pdo->prepare ( 'select username,register_date,realname,birthdate,fk_user_id,111195 * ST_Distance(POINT(?,?), address_coord) as dist, mailing, phone ' . //
' from user u' . //
' left join user_extra ue ON (ue.fk_user_id=u.id) ' . //
' WHERE status = "verified"' . //
' ORDER BY lower(u.username) ASC' ); //
ex2er ( $stmt, array (
		$reference->lat,
		$reference->long 
) );
$no = 1;
while ( $row = $stmt->fetch ( PDO::FETCH_ASSOC ) ) {
	unset ( $userextra );
	$user = getUserObject ( $row );
	$hasExtra = ! is_null ( $row ['fk_user_id'] );
	if ($hasExtra) {
		$userextra = getUserExtraObject ( $row );
	}
	echo '<tr class="' .  ($no % 2 == 1 ? 'oddFirst' : 'evenfirst') . '">';
    echo "<td>" . $no . "</td>";
	echo "<td>" . $user->username . "</td>";
	if ($hasExtra) {
		echo "<td>" . $userextra->realname . "</td>";
		echo "<td>" . htmlentities($userextra->phone) . "</td>";
		echo "<td>" . (isset ( $userextra ) ? formatMeters ( $row ['dist'] ) : '') . "</td>";
		echo "<td>" . $userextra->birthdate->format ( 'Y' ) . "</td>";
	} else {
		echo "<td>-</td>";
		echo "<td>-</td>";
		echo "<td>-</td>";
		echo "<td>-</td>";
	}
	echo "<td>" . $user->register_date->format('M Y') . "</td>";
	echo "</tr>";
	$no =$no+1;
}
?>
</table>