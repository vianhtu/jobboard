<?php
$out = shell_exec( 'git reset --hard origin/master 2>&1;git pull 2>&1' );
echo '<pre>'.$out.'</pre>';
?>
