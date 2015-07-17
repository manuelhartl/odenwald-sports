<?php
echo '<div id="version">
		<a href="html/disclaimer.php" target="_blank">Regeln</a>
		<a href="html/impressum.php" target="_blank">Impressum</a>
		Version: ';
if($config ['beta']){
	echo 'Beta ';
}
echo  $config ['version'];
echo   '</div>';
?>