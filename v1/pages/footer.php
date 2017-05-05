<?php
echo '<div id="version">
		<a href="html/FAQ.php" target="_blank">FAQ</a>
		<a href="html/disclaimer.php" target="_blank">Regeln</a>
		<a href="html/impressum.php" target="_blank">Impressum</a>
		Version: ';
if (isset ( $config ['beta'] ) && $config ['beta']) {
	echo isset ( $config ['betainformation'] ) && $config ['betainformation'] ? $config ['betainformation'] : 'Beta ';
}
echo $config ['version'];
echo '</div>';
?>