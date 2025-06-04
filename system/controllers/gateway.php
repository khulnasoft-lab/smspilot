<?php
/**
 * @controller Gateway
 */

class Gateway_Controller extends MVC_Controller
{
	public function index()
	{
		$this->header->allow();

		$request = $this->sanitize->array($_GET);

		if(!isset($request["hash"], $request["device_unique"]))
			response(500, false);

		$decode = $this->hash->decode($request["hash"], system_token);

		if(!$decode)
			response(500, false);
		else
			$hash = md5($decode);

		if($this->gateway->checkSuspension($hash) > 0)
			response(403, false);

		$this->cache->container("gateway.{$hash}");

		if(!$this->cache->has("devices")):
			if($this->gateway->checkUserKey($hash) > 0):
				$this->cache->set("devices", $this->gateway->getDevices($hash));
			endif;
		endif;

		if (!array_key_exists($request["device_unique"], $this->cache->get("devices")))
            response(500, false);

		$this->cache->container("gateway.{$request["device_unique"]}.{$hash}");

		if(!$this->cache->exist()):
			$this->cache->setArray($this->gateway->getPending($hash, $request["device_unique"]));
		endif;

		$messages = $this->cache->getAll();

		ksort($messages);

		if(count($messages) > 0):
			$pending["automatic"] = [];
			$pending["priority"] = [];

			foreach($messages as $key => $value){
				if($value["priority"]):
					$pending["priority"][] = [
						"id" => $key,
						"sim" => $value["sim"],
						"phone" => $value["phone"],
						"message" => $value["message"]
					];
				else:
					$pending["automatic"][] = [
						"id" => $key,
						"sim" => $value["sim"],
						"phone" => $value["phone"],
						"message" => $value["message"]
					];
				endif;
			}
		else:
			$pending = [
				"automatic" => [],
				"priority" => []
			];
		endif;

		response(200, false, $pending);
	}

	public function mark()
	{
		$this->header->allow();

		$request = $this->sanitize->array($_GET);

		if(!isset($request["id"], $request["status"], $request["hash"], $request["device_unique"]))
			response(500, false);

		if(!$this->sanitize->isInt($request["id"]))
			response(500, false);

		if(!$this->sanitize->isInt($request["status"]))
			response(500, false);

		if(!in_array($request["status"], [1, 2]))
			response(500, false);

		$decode = $this->hash->decode($request["hash"], system_token);

		if(!$decode)
			response(500, false);
		else
			$hash = md5($decode);

		if($this->gateway->checkSuspension($hash) > 0)
			response(403, false);

		$this->cache->container("gateway.{$hash}");

		if(!$this->cache->has("devices")):
			if($this->gateway->checkUserKey($hash) > 0):
				$this->cache->set("devices", $this->gateway->getDevices($hash));
			endif;
		endif;

		if (!array_key_exists($request["device_unique"], $this->cache->get("devices")))
            response(500, false);

        if(!$this->cache->has("actions")):
        	$this->cache->set("actions", $this->gateway->getActions($hash));
        endif;

        $actions = $this->cache->get("actions");

		if($this->gateway->mark($request["id"], $request["status"])):
			$this->cache->container("user.{$hash}");
			$this->cache->clear();
			$this->cache->container("messages.{$hash}");
			$this->cache->clear();
		endif;

		$this->cache->container("gateway.{$request["device_unique"]}.{$hash}");

		if($this->cache->has($request["id"])):
			$message = $this->cache->get($request["id"]);

			if($this->cache->delete($request["id"])):
		        if(!empty($actions)):
		    		foreach($actions as $action):
		    			if($action["type"] == 1 && $action["event"] == 1):
		        			if(!$action["devices"][0] || in_array($request["device_unique"], $action["devices"])):
				        		try {
									$this->guzzle->get($this->lex->parse($action["link"], [
				        				"recipient" => [
				        					"number" => urlencode($message["phone"]),
				        					"message" => urlencode($message["message"])
				        				],
				        				"date" => [
				        					"time" => $message["timestamp"]
				        				]
				        			]), [
					                	"http_errors" => false
						            ]);
								} catch(Exception $e){
									// Ignore Exceptions
								}
							endif;
						endif;
					endforeach;
				endif;

				response(200, false);
			else:
				response(500, false);
			endif;
		else:
			response(500, false);
		endif;
	}

	public function received()
	{
		$this->header->allow();

		$request = $this->sanitize->array($_POST);

		if(!isset($request["hash"], $request["payload"], $request["device_unique"]))
			response(500, false);

		if(!is_array($request["payload"]))
			response(500, false);

		$decode = $this->hash->decode($request["hash"], system_token);

		if(!$decode)
			response(500, false);
		else
			$hash = md5($decode);

		if($this->gateway->checkSuspension($hash) > 0)
			response(403, false);

		$this->cache->container("user.subscription.{$hash}");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->checkSubscriptionByUserID($decode) > 0 ? $this->system->getPackageByUserID($decode) : $this->system->getDefaultPackage());
        endif;

        set_subscription($this->cache->getAll());

		$this->cache->container("gateway.{$hash}");

		if(!$this->cache->has("devices")):
			if($this->gateway->checkUserKey($hash) > 0):
				$this->cache->set("devices", $this->gateway->getDevices($hash));
			endif;
		endif;

		if (!array_key_exists($request["device_unique"], $this->cache->get("devices")))
            response(500, false);

        if(!$this->cache->has("contacts")):
        	$this->cache->set("contacts", $this->gateway->getContacts($hash));	
        endif;

        $contacts = $this->cache->get("contacts");

        if(!$this->cache->has("webhooks")):
        	$this->cache->set("webhooks", $this->gateway->getWebhooks($hash));	
        endif;

        $webhooks = $this->cache->get("webhooks");

        if(!$this->cache->has("actions")):
        	$this->cache->set("actions", $this->gateway->getActions($hash));	
        endif;

        $actions = $this->cache->get("actions");

        if(!empty($request["payload"])):
        	$uid = $this->gateway->getUserID($hash);

        	if($this->system->checkQuota($uid) < 1):
				$this->system->create("quota", [
					"uid" => $uid,
					"sent" => 0,
					"received" => 0
				]);
			endif;

	        foreach($request["payload"] as $message):
	        	$rejected = false;

	        	try {
				    $number = $this->phone->parse($message["address"]);

				    if(!$number->isValidNumber())
						$rejected = true;

					if(!$number->getNumberType(Brick\PhoneNumber\PhoneNumberType::MOBILE))
						$rejected = true;

					$message["address"] = $number->format(Brick\PhoneNumber\PhoneNumberFormat::E164);
				} catch(Brick\PhoneNumber\PhoneNumberParseException $e) {
					$rejected = true;
				}

				if(!$rejected):
		        	if($this->gateway->checkReceived($message["id"], $uid, $request["device_unique"]) < 1):
		        		if(!limitation(subscription_receive, $this->system->countQuota($decode)["received"])):
			        		$filtered = [
			        			"rid" => $message["id"],
			        			"uid" => $uid,
			        			"did" => $request["device_unique"],
			        			"phone" => $message["address"],
			        			"message" => $message["body"],
			        			"receive_date" => date("Y-m-d H:i:s", $message["date"] / 1000)
			        		];

			        		if(!empty($webhooks)):
				        		foreach($webhooks as $webhook):
				        			if(!$webhook["devices"][0] || in_array($request["device_unique"], $webhooks["devices"])):
						        		try {
											$this->guzzle->post($webhook["url"], [
								                "form_params" => [
								                	"secret" => $webhook["secret"],
								                	"name" => isset($contacts[$message["address"]]) ? $contacts[$message["address"]]["name"] : (boolean) false,
								                	"phone" => $filtered["phone"],
								                	"message" => $filtered["message"],
								                	"receive_date" => date("m/d/Y g:i A", strtotime($filtered["receive_date"]))
								                ],
								                "http_errors" => false
								            ]);
										} catch(Exception $e){
											// Ignore Exceptions
										}
									endif;
								endforeach;
							endif;

							if(!empty($actions)):
				        		foreach($actions as $action):
				        			if($action["type"] == 1 && $action["event"] == 2):
					        			if(!$action["devices"][0] || in_array($request["device_unique"], $action["devices"])):
							        		try {
												$this->guzzle->get($this->lex->parse($action["link"], [
							        				"recipient" => [
							        					"number" => urlencode($filtered["phone"]),
							        					"message" => urlencode($filtered["message"])
							        				],
							        				"date" => [
							        					"time" => strtotime($filtered["receive_date"])
							        				]
							        			]), [
								                	"http_errors" => false
									            ]);
											} catch(Exception $e){
												// Ignore Exceptions
											}
										endif;
									endif;

									if($action["type"] == 2):
					        			if(!$action["devices"][0] || in_array($request["device_unique"], $action["devices"])):
					        				if(Stringy\create($filtered["message"])->containsAny($action["keywords"], false)):
					        					if($this->system->checkQuota($decode) < 1):
													$this->system->create("quota", [
														"uid" => $decode,
														"sent" => 0,
														"received" => 0
													]);
								    			endif;

					        					$this->cache->container("user.{$hash}");

								    			if(!$this->cache->has("devices")):
								    				$this->cache->set("devices", $this->system->getDevices($decode));
								    			endif;

								    			$devices = $this->cache->get("devices");

								    			if(!empty($devices)):
							    					$reply = [
									        			"uid" => $decode,
									        			"did" => $request["device_unique"],
									        			"sim" => 0,
									        			"phone" => $filtered["phone"],
									        			"message" => $this->lex->parse($action["message"], [
									        				"recipient" => [
									        					"number" => $filtered["phone"],
									        					"message" => $filtered["message"]
									        				],
									        				"date" => [
									        					"time" => strtotime($filtered["receive_date"])
									        				]
									        			]),
									        			"status" => 0,
									        			"priority" => 1,
									        			"api" => 0
									        		];

									        		$create = $this->system->create("sent", $reply);

									        		if($create):
									        			$this->cache->container("gateway.{$reply["did"]}.{$hash}");

									        			$this->cache->set($create, [
									        				"api" => (boolean) 0,
									        				"sim" => $reply["sim"],
									        				"device" => (int) $devices[$request["device_unique"]]["id"],
									        				"phone" => $reply["phone"],
									        				"message" => $reply["message"],
									        				"priority" => (boolean) $reply["priority"],
									        				"timestamp" => time()
									        			]);

									    				$this->cache->container("user.{$hash}");
														$this->cache->clear();
									        			$this->cache->container("messages.{$hash}");
														$this->cache->clear();

														$this->system->increment($decode, "sent");
									        		endif;
								    			endif;
					        				endif;
										endif;
									endif;
								endforeach;
							endif;

							$this->gateway->received($filtered);
							$this->system->increment($uid, "received");
						endif;
		        	endif;
		        endif;
	        endforeach;

	        $this->cache->container("user.{$hash}");
	        $this->cache->clear();
	        $this->cache->container("messages.{$hash}");
	        $this->cache->clear();
	    endif;

        response(200, false);
	}

	public function language()
	{
		$this->header->allow();

		$request = $this->sanitize->array($_GET);

		if(!isset($request["hash"]))
			response(500, "Invalid Request!");

		$decode = $this->hash->decode($request["hash"], system_token);

		if(!$decode)
			response(500, false);
		else
			$hash = md5($decode);

		$language = $this->gateway->getUserLanguage($hash);

		$this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSystemSettings());
        endif;

        set_system($this->cache->getAll());

        $this->cache->container("system.language." . $language);

        if($this->cache->empty()):
            $this->cache->setRaw($this->system->getTranslations($language));
        endif;

        set_language($this->cache->getRaw());

        response(200, false, [
        	"status_gateway_running" => lang_app_status_gateway_running,
			"status_gateway_touch" => lang_app_device_registered,
			"device_registered" => lang_app_device_registered,
			"device_unregistered" => lang_app_device_unregistered,
			"terminal_gateway_ready" => lang_app_terminal_gateway_ready,
			"terminal_gateway_register" => lang_app_terminal_gateway_register,
			"terminal_gateway_hash" => lang_app_terminal_gateway_hash,
			"terminal_gateway_registered" => lang_app_terminal_gateway_registered,
			"terminal_gateway_device" => lang_app_terminal_gateway_device,
			"terminal_gateway_connecterror" => lang_app_terminal_gateway_connecterror,
			"terminal_gateway_started" => lang_app_terminal_gateway_started,
			"terminal_gateway_stopped" => lang_app_terminal_gateway_stopped,
			"terminal_gateway_unregistered" => lang_app_terminal_gateway_unregistered,
			"terminal_uid_failed" => lang_app_terminal_uid_failed,
			"terminal_feature_error" => lang_app_terminal_feature_error,
			"terminal_connection_restored" => lang_app_terminal_connection_restored,
			"terminal_gateway_errorstop" => lang_app_terminal_gateway_errorstop,
			"terminal_gateway_cantconnect" => lang_app_terminal_gateway_cantconnect,
			"terminal_sms_sent" => lang_app_terminal_sms_sent,
			"terminal_message_failed" => lang_app_terminal_message_failed,
			"terminal_device_error" => lang_app_terminal_device_error,
			"dialog_wait" => lang_app_dialog_wait,
			"dialog_exit" => lang_app_dialog_exit,
			"dialog_exit_desc" => lang_app_dialog_exit_desc,
			"camera_qrcode_inside" => lang_app_camera_qrcode_inside,
			"ui_status" => lang_app_ui_status,
			"ui_exit" => lang_app_ui_exit
        ]);
	}

	public function register()
	{
		$this->header->allow();

		$request = $this->sanitize->array($_POST);

		if(!isset($request["hash"], $request["device_unique"], $request["device_model"], $request["device_version"], $request["device_manufacturer"]))
			response(500, false);

		$decode = $this->hash->decode($request["hash"], system_token);

		if(!$decode)
			response(500, false);
		else
			$hash = md5($decode);

		if($this->gateway->checkSuspension($hash) > 0)
			response(403, false);

		$this->cache->container("user.subscription.{$hash}");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->checkSubscriptionByUserID($decode) > 0 ? $this->system->getPackageByUserID($decode) : $this->system->getDefaultPackage());
        endif;

        set_subscription($this->cache->getAll());

		if($this->gateway->checkDevice($request["device_unique"], $hash) > 0):
			response(200, false, [
				"hash" => $request["hash"],
				"device_unique" => $request["device_unique"]
			]);
		else:
			if(limitation(subscription_device, $this->system->countDevices($decode)))
				response(401, false);
		endif;

		if($this->gateway->checkUserKey($hash) < 1)
			response(401, false);

		$uid = $this->gateway->getUserID($hash);

		$filtered = [
			"uid" => $uid,
			"did" => $request["device_unique"],
			"name" => $request["device_model"],
			"version" => $request["device_version"],
			"manufacturer" => ucfirst($request["device_manufacturer"])
		];

		if($this->gateway->register($filtered)):
			$this->cache->container("user.{$hash}");
			$this->cache->clear();
			$this->cache->container("gateway.{$hash}");
        	$this->cache->clear();
			$this->cache->container("devices.{$hash}");
			$this->cache->clear();
			$this->cache->container("api.devices.{$hash}");
			$this->cache->clear();

			response(200, false, [
				"hash" => $request["hash"],
				"device_unique" => $filtered["did"]
			]);
		else:
			response(500, false);
		endif;
	}
}