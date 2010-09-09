<?php
set_time_limit(0);
include "Thread.php";
$tHVAC = Thread::create("HVAC.php");
$r = $tHVAC->tell("start heater");
echo "Start heater: ", $r["status"], "\n";
if ($r["status"] == "err") {
    $tHVAC->close();
    exit;
}
$tHVAC->tell("set temp\n50");
$goingUp = true;
while ($tHVAC->isActive()) {
    echo $tHVAC->getError();
    $r = $tHVAC->tell("get current temp");
    if ($r["status"] == "ok") {
        echo "Current Temperature: ", $r["data"], "\n";
    }
    if ($r["data"] == 50 && $goingUp) {
        $tHVAC->tell("set temp\n35");
        $tHVAC->tell("start ac");
        $goingUp = false;
    } else if ($r["data"] == 35 && !$goingUp) {
        echo "Main Thread donenWaiting for HVAC Thread to end... ";
        $tHVAC->close();
        echo "ok";
        exit;
    }
}
