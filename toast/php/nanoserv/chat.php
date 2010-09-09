#!/opt/local/bin/php
<?php

require "nanoserv/nanoserv.php";
require "nanoserv/handlers/NS_Line_Input_Connection_Handler.php";

class simple_chat_server extends NS_Line_Input_Connection_Handler {

    function on_Accept() {

            $this->nick = $this->socket->Get_Peer_Name();

	            $this->Write("Hello ".$this->nick.", welcome to simple_chat_server.\n");

		            $this->Pubmsg("* {$this->nick} has joined");

			        }

				    function on_Read_Line($s) {

				            if ($s{0} == "/") {
					            
						                switch (strtoupper(substr(strtok($s, " "), 1))) {

								                case "NICK":
										                $newnick = trim(strtok(""));
												                $msg = "* {$this->nick} is now known as {$newnick}";
														                $this->nick = $newnick;
																                break;

																		                case "ME":
																				                $msg = "* {$this->nick} ".trim(strtok(""));
																						                break;

																								            }

																									            } else {

																										                if ($ts = trim($s)) $msg = "<{$this->nick}> {$ts}";

																												        }

																													        if (isset($msg)) $this->Pubmsg($msg);

																														    }

																														        public function on_Disconnect() {

																															        $this->Pubmsg("* {$this->nick} has quit");    

																																    }

																																        protected function Pubmsg($s) {

																																	        $msg = $s . "\n";

																																		        foreach (Nanoserv::Get_Connections() as $c) $c->Write($msg);

																																			    }

																																			    }

																																			    Nanoserv::New_Listener("tcp://0.0.0.0:1999", "simple_chat_server")->Activate();
																																			    Nanoserv::Run();

																																			    ?>
