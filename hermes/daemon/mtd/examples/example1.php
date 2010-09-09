<?php

error_reporting(E_ALL);

require_once '../include/class.MTDaemon.php';

class MTTest extends MTDaemon {

    public function getNext($slot)
    {
        $this->lock();
        $num = $this->getVar('num');
        if ($num == null) $num = 1;
        if ($num > 100) {
            $this->unlock();
            return null;
        }
        $this->unlock();
        
        $rand = rand(0, 5);
        echo 'Next for slot ' . $slot . ' : ' . $rand . "\n";
        if ($rand == 0) return null;
        else return $rand;
    }

    public function run($next, $slot)
    {
        $rand = rand(3, 10);
        $this->lock();
        $num = $this->getVar('num');
        $this->setVar('num', $this->getVar('num') + 1);
        $this->unlock();
        echo '## Iteration #' . number_format($num) . ' in ' . $rand . 'sec' . "\n";
        
        sleep($rand);
        return 0;
    }

}

$mttest = new MTTest(2);
$mttest->handle();

?>
