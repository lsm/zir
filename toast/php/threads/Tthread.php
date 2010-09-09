<?php

Thread.php

 require "ThreadUtility.php";
 class Thread {
         var $pref ;
	         var $pipes;
		         var $pid;
			         var $stdout;
				         function Thread() {
					                 $this->pref = 0;
							                 $this->stdout = "";
									                 $this->pipes = (array)NULL;
											         }
												         function Create ($url) {
													                 $t = new Thread;
															                 $descriptor = array (0 => array ("pipe", "r"), 1 => array ("pipe", "w"), 2 => array ("pipe", "w"));
																	                 $t->pref = proc_open ("php -q $url ", $descriptor, $t->pipes);
																			                 stream_set_blocking ($t->pipes[1], 0);
																					                 stream_set_blocking ($t->pipes[2], 0);
																							                 usleep (10);
																									                 return $t;
																											         }
																												         function isActive () {
																													                 $this->stdout .= $this->listen();
																															                 $f = stream_get_meta_data ($this->pipes[1]);
																																	                 return !$f["eof"];
																																			         }
																																				         function close () {
																																					                 $this->tell("quit");
																																							                 $r = proc_close ($this->pref);
																																									                 $this->pref = NULL;
																																											                 return $r;
																																													         }
																																														         function tell ($thought) {
																																															                 fwrite ($this->pipes[0], $thought . "\n");
																																																	                 $response = "";
																																																			                 do {
																																																					                         $response = $this->listen();
																																																								                 } while ($response == "");
																																																										                 return processresponse ($response);
																																																												         }
																																																													         function listen () {
																																																														                 $buffer = $this->stdout;
																																																																                 $this->stdout = "";
																																																																		                 while ($r = fgets ($this->pipes[1], 1024)) {
																																																																				                         $buffer .= $r;
																																																																							                 }
																																																																									                 return $buffer;
																																																																											         }
																																																																												         function getError () {
																																																																													                 $buffer = "";
																																																																															                 while ($r = fgets ($this->pipes[2], 1024)) {
																																																																																	                         $buffer .= $r;
																																																																																				                 }
																																																																																						                 return $buffer;
																																																																																								         }
																																																																																									 }


