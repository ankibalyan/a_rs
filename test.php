<?php
$r = rand(1,10);
$tst = fopen("cron_test.txt",'w+');
fwrite($tst,$r);
echo $r;
?>
