<?php

class Cron_Controller extends MVC_Controller
{
	public function index()
	{
		$this->header->allow();

		$type = $this->sanitize->string($this->url->segment(3));
		$token = $this->sanitize->string($this->url->segment(4));

		if($token != system_token)
			response(500, false);

		$this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSystemSettings());
        endif;

        set_system($this->cache->getAll());

        $this->cache->container("system.language.1");

        if($this->cache->empty()):
            $this->cache->setRaw($this->system->getTranslations(1));
        endif;

        set_language($this->cache->getRaw());

		switch($type):
			case "quota":
				$this->cron->resetQuota();
				break;
			case "scheduled":
				$schedules = $this->cron->getScheduled();

				if(!empty($schedules)):
					foreach($schedules as $scheduled):
						$this->cache->container("user.subscription." . $scheduled["hash"]);

				        if($this->cache->empty()):
				            $this->cache->setArray($this->system->checkSubscriptionByUserID($scheduled["uid"]) > 0 ? $this->system->getPackageByUserID($scheduled["uid"]) : $this->system->getDefaultPackage());
				        endif;

				        set_subscription($this->cache->getAll());

						if(time() >= $scheduled["send_date"]):
							$this->cache->container("user.{$scheduled["hash"]}");

			    			if(!$this->cache->has("devices")):
			    				$this->cache->set("devices", $this->system->getDevices($scheduled["uid"]));
			    			endif;

			    			$devices = $this->cache->get("devices");

			    			if(empty($devices))
			    				response(500, false);

			    			if($this->system->checkQuota($scheduled["uid"]) < 1):
								$this->system->create("quota", [
									"uid" => $scheduled["uid"],
									"sent" => 0,
									"received" => 0
								]);
			    			endif;

			    			$groups = explode(",", $scheduled["groups"]);

			    			if(!empty($groups)):
								foreach($groups as $group):
									if($this->system->checkGroup($group) > 0):
										$contacts = $this->system->getContactsByGroup($scheduled["uid"], $group);

										if(!empty($contacts)):
											foreach($contacts as $contact):
												if(!limitation(subscription_send, $this->system->countQuota($scheduled["uid"])["sent"])):
													if($scheduled["did"] == "0"):
									        			foreach($devices as $device):
									                        $this->cache->container("gateway.{$device["did"]}.{$scheduled["hash"]}");

									                        $usort[] = [
									                        	"id" => $device["id"],
									                        	"did" => $device["did"],
									                        	"pending" => count($this->cache->getAll())
									                        ];
									                    endforeach;
														
														usort($usort, function($previous, $next) {
														    return $previous["pending"] > $next["pending"] ? 1 : -1;
														});

														$scheduled["did"] = $usort[0]["did"];
									        		else:
									        			if($this->system->checkDeviceByUnique($scheduled["did"]) < 1)
									        				response(500, false);
									        		endif;

									        		$filtered = [
									        			"uid" => $scheduled["uid"],
									        			"did" => $scheduled["did"],
									        			"sim" => ($scheduled["sim"] < 1 ? 0 : 1),
									        			"phone" => $contact["phone"],
									        			"message" => $this->lex->parse($scheduled["message"], [
									        				"contact" => [
									        					"name" => $contact["name"],
									        					"number" => $contact["phone"]
									        				],
									        				"group" => [
									        					"name" => $contact["group"]
									        				],
									        				"date" => [
									        					"now" => date("F j, Y"),
									        					"time" => date("h:i A") 
									        				]
									        			]),
									        			"status" => 0,
									        			"priority" => 1,
									        			"api" => 0
									        		];

									        		$create = $this->system->create("sent", $filtered);

									        		if($create):
									        			$this->cache->container("gateway.{$filtered["did"]}.{$scheduled["hash"]}");

									        			$this->cache->set($create, [
									        				"api" => (boolean) 0,
									        				"sim" => $filtered["sim"],
									        				"device" => (int) $devices[$scheduled["did"]]["id"],
									        				"phone" => $filtered["phone"],
									        				"message" => $filtered["message"],
									        				"priority" => (boolean) $filtered["priority"],
									        				"timestamp" => time()
									        			]);

									        			$this->system->increment($scheduled["uid"], "sent");
									        		endif;
									        	endif;
											endforeach;
										endif;
									endif;
								endforeach;
							endif;

							$numbers = explode(",", trim($scheduled["numbers"]));

							if(!empty($numbers)):
								foreach($numbers as $number):
									if(!limitation(subscription_send, $this->system->countQuota($scheduled["uid"])["sent"])):
										$rejected = false;

										try {
										    $phone = $this->phone->parse($number);

										    if(!$phone->isValidNumber())
												$rejected = true;

											if(!$phone->getNumberType(Brick\PhoneNumber\PhoneNumberType::MOBILE))
												$rejected = true;

											$request["phone"] = $phone->format(Brick\PhoneNumber\PhoneNumberFormat::E164);
										} catch(Brick\PhoneNumber\PhoneNumberParseException $e) {
											$rejected = true;
										}

										if(!$rejected):
											if($scheduled["did"] == "0"):
							        			foreach($devices as $device):
							                        $this->cache->container("gateway.{$device["did"]}.{$scheduled["hash"]}");

							                        $usort[] = [
							                        	"id" => $device["id"],
							                        	"did" => $device["did"],
							                        	"pending" => count($this->cache->getAll())
							                        ];
							                    endforeach;
												
												usort($usort, function($previous, $next) {
												    return $previous["pending"] > $next["pending"] ? 1 : -1;
												});

												$scheduled["did"] = $usort[0]["did"];
							        		else:
							        			if($this->system->checkDeviceByUnique($scheduled["did"]) < 1)
							        				response(500, false);
							        		endif;

							        		$filtered = [
							        			"uid" => $scheduled["uid"],
							        			"did" => $scheduled["did"],
							        			"sim" => ($scheduled["sim"] < 1 ? 0 : 1),
							        			"phone" => $request["phone"],
							        			"message" => $this->lex->parse($scheduled["message"], [
							        				"contact" => [
							        					"name" => $request["phone"],
							        					"number" => $request["phone"]
							        				],
							        				"group" => [
							        					"name" => "Unknown"
							        				],
							        				"date" => [
							        					"now" => date("F j, Y"),
							        					"time" => date("h:i A") 
							        				]
							        			]),
							        			"status" => 0,
							        			"priority" => 1,
							        			"api" => 0
							        		];

							        		$create = $this->system->create("sent", $filtered);

							        		if($create):
							        			$this->cache->container("gateway.{$filtered["did"]}.{$scheduled["hash"]}");

							        			$this->cache->set($create, [
							        				"api" => (boolean) 0,
							        				"sim" => $filtered["sim"],
							        				"device" => (int) $devices[$scheduled["did"]]["id"],
							        				"phone" => $filtered["phone"],
							        				"message" => $filtered["message"],
							        				"priority" => (boolean) $filtered["priority"],
							        				"timestamp" => time()
							        			]);

							        			$this->system->increment($scheduled["uid"], "sent");
							        		endif;
							        	endif;
							        endif;
								endforeach;
							endif;

							$this->cache->container("user.{$scheduled["hash"]}");
							$this->cache->clear();
							$this->cache->container("messages.{$scheduled["hash"]}");
							$this->cache->clear();

							if($scheduled["repeat"] > 1):
								$this->cron->delete($scheduled["id"], "scheduled");
								$this->cache->container("messages.{$scheduled["hash"]}");
								$this->cache->clear();
							endif;
						endif;
					endforeach;
				endif;

				break;
			case "subscription":
				$subscriptions = $this->cron->getSubscriptions();

				if(!empty($subscriptions)):
					foreach($subscriptions as $subscription):
						if(time() >= $subscription["expire"]):
							$this->cron->delete($subscription["id"], "subscriptions");

							try {
								$vars = [
									"title" => system_site_name,
									"data" => [
										"subject" => lang_mail_subscriptionexpired
									]
								];

								$this->smarty->assign($vars);

								if(system_mail_function > 1):
									$this->mail->isSMTP();
									$this->mail->SMTPAuth = true;
									$this->mail->SMTPSecure = "tls";
									$this->mail->Host = system_smtp_host;
									$this->mail->Port = system_smtp_port; 
									$this->mail->Username = system_smtp_username;
									$this->mail->Password = system_smtp_password;
								endif;

								$this->mail->Subject = $vars["data"]["subject"];
							    $this->mail->setFrom(system_site_mail, $vars["title"]);
							    $this->mail->addAddress($subscription["email"]);
							    $this->mail->isHTML(true);  
							    $this->mail->msgHTML($this->smarty->fetch("_mail/expired.tpl"));

							    $this->mail->send();
							} catch(Exception $e){
								// Ignore exceptions
							}
						endif;
					endforeach;
				endif;

				break;
			default:
				response(500, false);
		endswitch;

		response(200, false);
	}
}