<?php

function fact ($n) {
if ($n < 2) return $n;
return fact($n - 1) + fact ($n - 2);
}

include ("Thread.php");
$t2 = Thread::create("t2.php");
$t3 = Thread::create("t3.php");
$t4 = Thread::create("t4.php");
$t5 = Thread::create("t5.php");
while ($t2->isActive() || $t3->isActive() || $t4->isActive() || $t5->isActive()) {
echo $t2->listen();
echo $t3->listen();
echo $t4->listen();
echo $t5->listen();
}
$t2->close();
$t3->close();
$t4->close();
$t5->close();
echo "Main thread done\n";

$tHVAC = Thread::create("HVAC.php");
$tHVAC->tell("start");
do {
$tHVAC->tell("get current temperature");
$temp = $tHVAC->listen();
if ($temp > 73) {
$tHVAC->tell("start air conditioning");
} else if ($temp < 65) {
$tHVAC->tell(”start heater”);
}
} while (/* application loop */);
