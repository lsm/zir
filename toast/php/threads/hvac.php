<?php

HVAC.php

require "ThreadInstance.php";
class HVAC extends ThreadInstance {
	var $currentTemp;
	var $heaterOn;
	var $acOn;
	var $toggleOn;
	function HVAC () {
		$this->setup();
		$this->currentTemp = 32;
		$this->heaterOn = false;
		$this->acOn = false;
		$this->toggleOn = 70;
	}
	function runHeater() {
		$this->heaterOn = true;
		$this->acOn = false;
	}
	function runAc() {
		$this->heaterOn = false;
		$this->acOn = true;
	}
	function process() {
		switch (true) {
			case ($this->heaterOn):
				return $this->processHeater();
			case ($this->acOn):
				return $this->processAc();
		}
	}
	function processHeater() {
		if ($this->currentTemp < $this->toggleOn) {
			$this->currentTemp++;
		}
	}
	function processAc() {
		if ($this->currentTemp > $this->toggleOn) {
			$this->currentTemp–;
		}
	}
	function apploop($command) {
		$this->process();
		switch ($command) {
			case “”:
				// noop
				return;
			case “start heater”:
				$this->runHeater();
				$this->response (”ok”, NULL);
				return;
			case “start ac”:
				$this->runAc();
				$this->response(”ok”, NULL);
				return;
			case “get current temp”:
				$this->response (”ok”, $this->currentTemp);
				return;
			case “set temp”:
				$temp = intval($this->getLine(true));
				if ($temp) {
					$this->toggleOn = $temp;
					$this->response (”ok”, NULL);
				} else {
					$this->response (”err”, “not a number”);
				}
				return;
			case “quit”:
				exit;
			default:
				$this->response (”err”, “bad request - $command”);
				return;
		}
	}
}
$hvac = new HVAC();
do {
	sleep (1);
	$hvac->apploop($hvac->getCommand());
} while (true);
