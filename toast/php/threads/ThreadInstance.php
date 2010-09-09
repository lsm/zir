<?php


ThreadInstance.php

 set_time_limit (0);
 require "ThreadUtility.php";
 class ThreadInstance {
         var $stdin;
	         var $commandbuffer;
		         var $stdout;
			         function setup() {
				                 $this->stdin = fopen ("php://stdin", "r");
						                 $this->stderr = fopen ("php://stderr", "w");
								                 stream_set_blocking ($this->stdin, false);
										                 $this->commandbuffer = (array)NULL;
												                 $this->outbuffer = "";
														         }
															         function getCommand() {
																                 $command = fgets ($this->stdin, 1024);
																		                 $this->commandbuffer[] = $command;

																				                 $command = array_shift ($this->commandbuffer);
																						                 return trim($command);
																								         }
																									         function response ($status, $data) {
																										                 response ($status, $data);
																												         }
																													         function getLine ($wait = false) {
																														                 if ($wait) {
																																                         $buffer = "";
																																			                         while (!strlen($buffer)) {
																																						                                 $buffer .= fgets ($this->stdin, 1024);
																																										                         }
																																													                 } else {
																																															                         $buffer = fgets ($this->stdin, 1024);
																																																		                 }
																																																				                 return trim($buffer);
																																																						         }
																																																							         function debug ($text) {
																																																								                 fwrite ($this->stderr, $text);
																																																										         }
																																																											 }
