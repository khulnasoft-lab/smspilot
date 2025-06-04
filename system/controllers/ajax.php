<?php
/**
 * @controller Ajax
 */

class Ajax_Controller extends MVC_Controller
{
	public function index()
	{	
		$this->header->allow(site_url);

		$type = $this->sanitize->string($this->url->segment(4));
		$request = $this->sanitize->array($_POST);

		$this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSystemSettings());
        endif;

        set_system($this->cache->getAll());

        set_logged($this->session->get("logged"));

        $this->cache->container("system.language." . logged_language);

        if($this->cache->empty()):
            $this->cache->setRaw($this->system->getTranslations(logged_language));
        endif;

        set_language($this->cache->getRaw());

		switch($type):
			case "login":
				if($this->session->has("logged"))
	            	response(302, lang_response_session_true);

				if(!isset($request["email"], $request["password"]))
					response(500, lang_response_invalid);

				if(!$this->sanitize->isEmail($request["email"]))
					response(500, lang_response_invalid_emailpass);

				if(strlen($request["password"]) < 6)
					response(500, lang_response_invalid_emailpass);

				$filtered = [
					"email" => $this->sanitize->email($request["email"]),
					"password" => $request["password"]
				];

				if($this->system->checkEmail($filtered["email"]) > 0):
					$this->cache->container("forgot." . md5($filtered["email"]));

					$raw = $this->system->getPassword($filtered["email"]);

					if($raw["suspended"] > 0)
						response(500, lang_response_suspended);

					if($this->cache->has($filtered["password"])):
						$this->cache->clear();	
						$this->session->set("logged", 
							$this->system->getUser($raw["id"])
						);
						$this->session->delete("language");
						response(301, lang_response_loggedin_success);
					else:
						if(password_verify($filtered["password"], $raw["password"])):
							$this->cache->clear();
							$this->session->set("logged", 
								$this->system->getUser($raw["id"])
							);
							$this->session->delete("language");
							response(301, lang_response_loggedin_success);
						else:
							response(500, lang_response_invalid_emailpass);
						endif;
					endif;
				else:
					response(500, lang_response_invalid_emailpass);
				endif;

				break;
			case "forgot":
				if($this->session->has("logged"))
	            	response(302, lang_response_session_true);

	            if(empty(system_recaptcha_key) || empty(system_recaptcha_secret))
	            	response(500, lang_recaptcha_add_keys);

				if(!isset($request["email"]))
					response(500, lang_response_invalid);

				if(!$this->sanitize->isEmail($request["email"]))
					response(500, lang_response_invalid_email);

				$filtered = [
					"email" => $this->sanitize->email($request["email"]),
				];

			 	try {
	            	$recaptcha = json_decode($this->guzzle->get("https://www.google.com/recaptcha/api/siteverify?secret=" . system_recaptcha_secret . "&response={$request["g-recaptcha-response"]}", [
		                "http_errors" => false
		            ])->getBody()->getContents());
	            } catch(Exception $e){
	            	response(500, lang_response_went_wrong);
	            }

	            if($recaptcha->success):
					if($this->system->checkEmail($filtered["email"]) > 0):
						try {
							$vars = [
								"title" => system_site_name,
								"data" => [
									"subject" => lang_response_retrieval_received,
									"password" => $this->hash->encode(rand(0, 1212), system_token)
								]
							];

							$this->cache->container("forgot." . md5($filtered["email"]));
							$this->cache->set($vars["data"]["password"], $vars["data"]["password"], 600);
							$this->cache->deleteExpired();

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
						    $this->mail->addAddress($filtered["email"]);
						    $this->mail->isHTML(true);  
						    $this->mail->msgHTML($this->smarty->fetch("_mail/forgot.tpl"));

						    $this->mail->send();
						} catch(Exception $e){
							response(500, lang_response_went_wrong);
						}

						response(200, lang_response_retrieval_sent);
					else:
						response(500, lang_response_invalid_email);
					endif;
				else:
	            	response(500, lang_response_solve_captcha);
	            endif;

				break;
			case "register":
				if($this->session->has("logged"))
	            	response(302, lang_response_session_true);

	            if(system_registrations > 1)
	            	response(500, lang_response_register_false);

	            if(empty(system_recaptcha_key) || empty(system_recaptcha_secret))
	            	response(500, lang_recaptcha_add_keys);
	            
	            if(!isset($request["name"], $request["email"], $request["password"], $request["cpassword"], $request["g-recaptcha-response"]))
	            	response(500, lang_response_invalid);

	            if(!$this->sanitize->length($request["name"]))
	            	response(500, lang_response_name_short);

	            if(!$this->sanitize->isEmail($request["email"]))
	            	response(500, lang_response_invalid_email);

	            if(!$this->sanitize->length($request["password"], 5))
	            	response(500, lang_response_password_short);

	            if($request["password"] !== $request["cpassword"])
	            	response(500, lang_response_password_notmatch);

	            try {
	            	$recaptcha = json_decode($this->guzzle->get("https://www.google.com/recaptcha/api/siteverify?secret=" . system_recaptcha_secret . "&response={$request["g-recaptcha-response"]}", [
		                "http_errors" => false
		            ])->getBody()->getContents());
	            } catch(Exception $e){
	            	response(500, lang_response_went_wrong);
	            }

	            if($recaptcha->success):
	            	$filtered = [
	            		"role" => 1,
	            		"name" => $request["name"],
	            		"email" => $this->sanitize->email($request["email"]),
	            		"language" => system_default_lang,
	            		"suspended" => 0,
	            		"password" => password_hash($request["password"], PASSWORD_DEFAULT)
	            	];

	            	if($this->system->checkEmail($filtered["email"]) < 1):
	            		$create = $this->system->create("users", $filtered);
	            		if($create):
	            			$this->session->set("logged", 
	            				$this->system->getUser($create)
	            			);

	            			$this->cache->container("admin.statistics");
							$this->cache->clear();
	            			$this->cache->container("admin.users");
							$this->cache->clear();

	            			response(301, lang_response_register_success);
	            		else:
	            			response(500, lang_response_went_wrong);
	            		endif;
	            	else:
	            		response(500, lang_response_email_unavailable);
	            	endif;
	            else:
	            	response(500, lang_response_solve_captcha);
	            endif;

				break;
			case "logout":
				if(!$this->session->has("logged"))
	            	response(302, lang_response_session_false);

	            if($this->session->destroy())
	            	response(200, lang_response_loggedout_success);
	            else
	            	response(500, lang_response_went_wrong);

				break;
			case "impersonate":
				if(!$this->session->has("logged"))
	            	response(302, lang_response_session_false);

	            if(!permission("manage_users"))
					response(500, lang_response_no_permission);

	            if(!isset($request["id"]))
	            	response(500, lang_response_invalid);

	            if($this->system->checkUser($request["id"]) > 0):
	            	$this->session->set("logged", 
						$this->system->getUser($request["id"])
					);
					$this->session->set("impersonate", 
						$this->system->getUser(logged_id)
					);
					$this->session->delete("language");

					response(200, "Successfully logged in as another user!");
	            else:
	            	response(500, "User was not found!");
	            endif;
	            
				break;
			case "exit":
				if(!$this->session->has("logged"))
	            	response(302, lang_response_session_false);

	            if(!$this->session->has("impersonate")):
		            if(!permission("manage_users"))
						response(500, lang_response_no_permission);
				endif;

	            if(!isset($request["id"]))
	            	response(500, lang_response_invalid);

	            if(!$this->session->has("impersonate"))
	            	response(500, "lang_response_invalid");

	            if($this->system->checkUser($request["id"]) > 0):
	            	$this->session->set("logged", 
						$this->system->getUser($request["id"])
					);
					$this->session->delete("impersonate");
					$this->session->delete("language");

					response(200, "Successfully exited from user account!");
	            else:
	            	response(500, lang_response_went_wrong);
	            endif;

				break;
			case "language":
				$language = $this->sanitize->string($this->url->segment(5));

				if(!$this->sanitize->isInt($language))
					response(500, lang_response_invalid);

				$this->cache->container("system.languages");

		        if($this->cache->empty()):
		            $this->cache->setArray($this->system->getLanguages());
		        endif;

		        if(!array_key_exists($language, $this->cache->getAll()))
		        	response(500, lang_response_invalid);

				if(logged_id):
					if($this->system->update(logged_id, false, "users", [
						"language" => $language
					])):
						$this->session->set("language", $language);
					else:
						response(500, lang_response_went_wrong);
					endif;
				else:
					$this->session->set("language", $language);
				endif;

				response(200, lang_response_lang_changed);

				break;
			case "languages":
				$this->cache->container("system.languages");

		        if($this->cache->empty()):
		            $this->cache->setArray($this->system->getLanguages());
		        endif;

		        $count = 1;

		        foreach($this->cache->getAll() as $language):
		        	if($count < 4):
		        		$languages[] = <<<HTML
<li>
    <a href="#" data-mfb-label="{$language['name']}" class="mfb-component__button--child bg-dark" zender-language="{$language['id']}">
        <i class="mfb-component__child-icon flag-icon flag-icon-{$language['iso']}"></i>
    </a>
</li>
HTML;
					endif;
					
					$count++;
		        endforeach;

		        if(count($this->cache->getAll()) > 3):
		        	$label = lang_all_languages;

		        	$languages[] = <<<HTML
<li>
    <a href="#" data-mfb-label="{$label}" class="mfb-component__button--child bg-dark" zender-toggle="zender.languages">
        <i class="mfb-component__child-icon la la-braille la-lg text-white more-lang"></i>
    </a>
</li>
HTML;
		        endif;
				
		        response(200, "Languages", $languages);

				break;
			case "livechat":
				if(!super_admin)
					response(500, lang_response_no_permission);

				if(!$this->session->has("logged"))
	            	response(302, lang_response_session_false);

	            if(system_livechat < 2):
		            $this->cache->container("system.verify");
		            $this->cache->deleteExpired();

		            if($this->cache->has("username")):
		            	if($this->cache->get("username")):
		            		response(200, false, $this->cache->get("username"));
		            	endif;
				    else:
				    	if($this->sanitize->length(system_purchase_code, 5)):
				            try {
				            	$build = $this->guzzle->post(titansys_api . "/auth/verify?code=" . system_purchase_code, [
						            "allow_redirects" => true,
						            "http_errors" => false
						        ]);

						        $response = json_decode($build->getBody()->getContents());

						        if($response->status == 200):
						        	$this->cache->set("username", $response->data, 86400);
						        	response(200, false, $response->data);
				            	else:
				            		$this->cache->set("username", false, 86400);
				            	endif;
				            } catch(Exception $e){
				            	response(500, lang_response_buildserver_false);
				            }
				        endif;
				    endif;
				endif;			

			    response(500, false);
	            
				break;
			case "forums":
				if(!super_admin)
					response(500, lang_response_no_permission);

				if(!$this->session->has("logged"))
	            	response(302, lang_response_session_false);

	            if(!$this->sanitize->length(system_purchase_code, 5))
	            	response(500, lang_response_pcode_empty);

	            response(200, lang_api_response_forumtrue, titansys_api . "/auth?code=" . system_purchase_code);
	            
				break;
			case "build":
				if(!super_admin)
					response(500, lang_response_no_permission);

				if(!$this->session->has("logged"))
	            	response(302, lang_response_session_false);

	            if(!$this->sanitize->length(system_purchase_code, 5))
	            	response(500, lang_response_pcode_empty);

	            if(!$this->file->exists("uploads/builder/icon.png"))
	            	response(500, lang_response_upload_appicon);

	            if(!$this->file->exists("uploads/builder/logo.png"))
	            	response(500, lang_response_upload_applogo);

	            if(!$this->file->exists("uploads/builder/splash.png"))
	            	response(500, lang_response_upload_appsplash);

	            try {
	            	$build = $this->guzzle->post(titansys_api . "/zender/builder", [
			            "form_params" => [
			            	"token" => system_token,
			            	"code" => system_purchase_code,
			            	"site_url" => str_replace("//", (system_protocol < 2 ? "http://" : "https://"), site_url),
			            	"email" => system_builder_email,
			            	"package_name" => system_package_name,
			            	"app_name" => system_app_name,
			            	"app_desc" => system_app_desc,
			            	"app_color" => system_app_color,
			            	"app_send" => system_app_send,
			            	"app_receive" => system_app_receive
			            ],
			            "allow_redirects" => true,
			            "http_errors" => false
			        ]);

			        $response = json_decode($build->getBody()->getContents());

			        if($response->status == 200):
			        	response(200, lang_api_response_buildsuccess);
			        else:
			        	switch($response->message):
			        		case "invalid_request":
			        			response(500, lang_response_invalid);
			        			break;
			        		case "invalid_code":
			        			response(500, lang_api_response_invalcode);
			        			break;
			        		case "build_running_wait":
			        			response(500, lang_api_response_buildwait);
			        			break;
			        		default:
			        			response(500, lang_response_went_wrong);
			        	endswitch;
	            	endif;
	            } catch(Exception $e){
	            	response(500, lang_response_buildserver_false);
	            }

				break;
			default:
				response(500, lang_response_invalid);
		endswitch;
	}

	public function chart()
	{
		$this->header->allow(site_url);

		$this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSystemSettings());
        endif;

        set_system($this->cache->getAll());

        set_logged($this->session->get("logged"));

        $this->cache->container("system.language." . logged_language);

        if($this->cache->empty()):
            $this->cache->setRaw($this->system->getTranslations(logged_language));
        endif;

        set_language($this->cache->getRaw());

        if(!$this->session->has("logged"))
            response(302, lang_response_session_false);

        $type = $this->sanitize->string($this->url->segment(4));

		switch($type):
			case "dashboard.default":
				$this->cache->container("user." . logged_hash);

				$this->cache->deleteExpired();

				if(!$this->cache->has("statistics")):
					$sent = []; $received = [];

					foreach($this->system->getStatsSent(logged_id) as $key => $value):
						$sent[] = [
							(int)("{$key}000"),
							count($value)
						];
					endforeach;

					foreach($this->system->getStatsReceived(logged_id) as $key => $value):
						$received[] = [
							(int)("{$key}000"),
							count($value)
						];
					endforeach;

					$series = [
						"sent" => $sent,
						"received" => $received
					];

					$this->cache->set("statistics", $series, 3600);
				endif;

				$chart = $this->cache->get("statistics");

				$vars = [
					"series" => [
						[
							"name" => strtoupper(lang_dashboard_default_summarysent),
							"data" => $chart["sent"]
						],
						[
							"name" => strtoupper(lang_dashboard_default_summaryreceived),
							"data" => $chart["received"]
						]
					],
					"colors" => [
						"#1ccf13", 
						"#1099da"
					]
				];

				break;
			case "admin.earnings":
				if(!permission("manage_transactions"))
					response(500, lang_response_no_permission);

				$this->cache->container("admin.statistics");

				$this->cache->deleteExpired();

				if(!$this->cache->has("earnings")):
					$series = [];
					
					foreach($this->system->getSystemTransactions() as $key => $value):
						unset($earnings);

						foreach($value as $vkey => $vvalue){
							$earnings[] = [
								(int)("{$vkey}000"),
								array_sum($vvalue)
							];
						}

						$series[] = [
							"name" => strtoupper($key),
							"data" => $earnings
						];
					endforeach;

					$this->cache->set("earnings", $series, 3600);
				endif;

				$vars = [
					"series" => $this->cache->get("earnings")
				];

				break;
			case "admin.messages":
				if(!is_admin)
					response(500, lang_response_no_permission);

				$this->cache->container("admin.statistics");

				$this->cache->deleteExpired();

				if(!$this->cache->has("messages")):
					$sent = []; $received = [];

					foreach($this->system->getSystemSent() as $key => $value):
						$sent[] = [
							(int)("{$key}000"),
							count($value)
						];
					endforeach;

					foreach($this->system->getSystemReceived() as $key => $value):
						$received[] = [
							(int)("{$key}000"),
							count($value)
						];
					endforeach;

					$series = [
						"sent" => $sent,
						"received" => $received
					];

					$this->cache->set("messages", $series, 3600);
				endif;

				$chart = $this->cache->get("messages");

				$vars = [
					"series" => [
						[
							"name" => strtoupper(lang_dashboard_admin_statssenttitle),
							"data" => $chart["sent"]
						],
						[
							"name" => strtoupper(lang_dashboard_admin_statsreceivedtitle),
							"data" => $chart["received"]
						]
					],
					"colors" => [
						"#1ccf13", 
						"#1099da"
					]
				];

				break;
			case "admin.users":
				if(!permission("manage_users"))
					response(500, lang_response_no_permission);

				$this->cache->container("admin.statistics");

				$this->cache->deleteExpired();

				if(!$this->cache->has("users")):
					$users = [];

					foreach($this->system->getSystemUsers() as $key => $value):
						$users[] = [
							(int) ("{$key}000"),
							count($value)
						];
					endforeach;

					$this->cache->set("users", $users, 86400);
				endif;

				$vars = [
					"series" => [
						[
							"name" => strtoupper(lang_dashboard_admin_statsregisteredtitle),
							"data" => $this->cache->get("users")
						]
					],
					"colors" => [
						"#e6821b"
					]
				];

				break;
			default:
				response(500, lang_response_invalid);
		endswitch;

		response(200, "Zender Chart", [
			"vars" => $vars
		]);
	}

	public function autocomplete()
	{
		$this->header->allow(site_url);

		$this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSystemSettings());
        endif;

        set_system($this->cache->getAll());

        set_logged($this->session->get("logged"));

        $this->cache->container("system.language." . logged_language);

        if($this->cache->empty()):
            $this->cache->setRaw($this->system->getTranslations(logged_language));
        endif;

        set_language($this->cache->getRaw());

        if(!$this->session->has("logged"))
            response(302, lang_response_session_false);

        $type = $this->sanitize->string($this->url->segment(4));

		switch($type){
			case "contacts":
				$this->cache->container("autocomplete.{$type}." . logged_hash);

				if($this->cache->empty()):
					$contacts = $this->system->getContacts(logged_id);

					if(!empty($contacts)):
						foreach($contacts as $contact):
							$suggestions[] = [
								"value" => "{$contact["name"]} ({$contact["phone"]})",
								"data" => $contact["phone"]
							];
						endforeach;
					else:
						$suggestions = [];
					endif;

					$this->cache->setArray($suggestions);
				endif;

				response(200, "Suggestions", $this->cache->getAll());

				break;
			default:
				response(500, lang_response_invalid);
		}
	}

	public function history()
	{
		$this->header->allow(site_url);

		$this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSystemSettings());
        endif;

        set_system($this->cache->getAll());

        set_logged($this->session->get("logged"));

        $this->cache->container("system.language." . logged_language);

        if($this->cache->empty()):
            $this->cache->setRaw($this->system->getTranslations(logged_language));
        endif;

        set_language($this->cache->getRaw());

        if(!$this->session->has("logged"))
            response(302, lang_response_session_false);

        $request = $this->sanitize->array($_POST);
        $type = $this->sanitize->string($this->url->segment(4));

		switch($type){
			case "history.sent":
				if(!isset($request["date"], $request["phone"], $request["sim"], $request["priority"], $request["api"], $request["device"]))
					response(500, lang_response_invalid);

				$range = explode("-", $request["date"]);

				if(count($range) < 2)
					response(500, lang_response_invalid_daterange);

				if(count_months($range[0], $range[1]) > 3)
					response(500, lang_response_date_maxrange);

				if(!in_array($request["sim"], ["all", 0, 1]))
					response(500, lang_response_invalid);

				if(!in_array($request["priority"], ["all", 0, 1]))
					response(500, lang_response_invalid);

				if(!in_array($request["api"], ["all", 0, 1]))
					response(500, lang_response_invalid);

				$this->cache->container("user." . logged_hash);

    			if(!$this->cache->has("devices")):
    				$this->cache->set("devices", $this->system->getDevices(logged_id));
    			endif;

    			if($request["device"] != "all"):
	    			if(!array_key_exists($request["device"], $this->cache->get("devices")))
	    				response(500, lang_response_invalid);
	    		endif;

				$filtered = [
					"uid" => logged_id,
					"start" => date("Y-m-d", strtotime($range[0])),
					"end" =>  date("Y-m-d", strtotime($range[1])),
					"phone" => (!empty($request["phone"]) ? $request["phone"] : false),
					"sim" => ($request["sim"] == "all" ? false : ($request["sim"] < 1 ? 0 : 1)),
					"priority" => ($request["priority"] == "all" ? false : ($request["priority"] < 1 ? 0 : 1)),
					"api" => ($request["api"] == "all" ? false : ($request["api"] < 1 ? 0 : 1)),
					"device" => ($request["device"] == "all" ? false : $request["device"])
				];

				if($this->system->checkHistorySent($filtered) > 0):
					$this->cache->container("history." . logged_hash);
					$this->cache->set("sent", $filtered);

					response(200, lang_response_found_senthistory);
				else:
					response(500, lang_response_notfound_senthistory);
				endif;

				break;
			case "history.received":
				if(!isset($request["date"], $request["phone"], $request["device"]))
					response(500, lang_response_invalid);

				$range = explode("-", $request["date"]);

				if(count($range) < 2)
					response(500, lang_response_invalid_daterange);

				if(count_months($range[0], $range[1]) > 3)
					response(500, lang_response_date_maxrange);

				$this->cache->container("user." . logged_hash);

    			if(!$this->cache->has("devices")):
    				$this->cache->set("devices", $this->system->getDevices(logged_id));
    			endif;

    			if($request["device"] != "all"):
	    			if(!array_key_exists($request["device"], $this->cache->get("devices")))
	    				response(500, lang_response_invalid);
	    		endif;

				$filtered = [
					"uid" => logged_id,
					"start" => date("Y-m-d", strtotime($range[0])),
					"end" =>  date("Y-m-d", strtotime($range[1])),
					"phone" => (!empty($request["phone"]) ? $request["phone"] : false),
					"device" => ($request["device"] == "all" ? false : $request["device"])
				];

				if($this->system->checkHistoryReceived($filtered) > 0):
					$this->cache->container("history." . logged_hash);
					$this->cache->set("received", $filtered);

					response(200, lang_response_found_receivehistory);
				else:
					response(500, lang_response_notfound_receivehistory);
				endif;

				break;
			default:
				response(500, lang_response_invalid);
		}
	}

	public function create()
	{
		$this->header->allow(site_url);

		$this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSystemSettings());
        endif;

        set_system($this->cache->getAll());

        set_logged($this->session->get("logged"));

        $this->cache->container("system.language." . logged_language);

        if($this->cache->empty()):
            $this->cache->setRaw($this->system->getTranslations(logged_language));
        endif;

        set_language($this->cache->getRaw());

        $this->cache->container("user.subscription." . logged_hash);

        if($this->cache->empty()):
            $this->cache->setArray($this->system->checkSubscriptionByUserID(logged_id) > 0 ? $this->system->getPackageByUserID(logged_id) : $this->system->getDefaultPackage());
        endif;

        set_subscription($this->cache->getAll());

        if(!$this->session->has("logged"))
            response(302, lang_response_session_false);

        $type = $this->sanitize->string($this->url->segment(4));
        $request = $this->sanitize->array($_POST, 
        	in_array($type, ["add.widget", "add.page"]) ? ["content"] : []
    	);

        switch($type):
        	case "sms.quick":
        		if(!isset($request["phone"], $request["device"], $request["sim"], $request["priority"], $request["message"]))
        			response(500, lang_response_invalid);

        		try {
				    $number = $this->phone->parse($request["phone"]);

				    if(!$number->isValidNumber())
						response(500, lang_response_invalid_number);

					if(!$number->getNumberType(Brick\PhoneNumber\PhoneNumberType::MOBILE))
						response(500, lang_response_invalid_number);

					$request["phone"] = $number->format(Brick\PhoneNumber\PhoneNumberFormat::E164);
				} catch(Brick\PhoneNumber\PhoneNumberParseException $e) {
					response(500, lang_response_invalid_number);
				}

				if(!$this->sanitize->isInt($request["sim"]))
					response(500, lang_response_invalid);

				if(!$this->sanitize->isInt($request["priority"]))
					response(500, lang_response_invalid);

				if(!$this->sanitize->length($request["message"], 5))
					response(500, lang_response_message_short);

				if($this->system->checkQuota(logged_id) < 1):
					$this->system->create("quota", [
						"uid" => logged_id,
						"sent" => 0,
						"received" => 0
					]);
    			endif;

    			if(limitation(subscription_send, $this->system->countQuota(logged_id)["sent"]))
    				response(500, lang_response_limitation_send);

				$this->cache->container("user." . logged_hash);

    			if(!$this->cache->has("devices")):
    				$this->cache->set("devices", $this->system->getDevices(logged_id));
    			endif;

    			$devices = $this->cache->get("devices");

    			if(empty($devices))
    				response(500, lang_response_noregistereddevices);

        		if($request["device"] == "0"):
        			foreach($devices as $device):
                        $this->cache->container("gateway.{$device["did"]}." . logged_hash);

                        $usort[] = [
                        	"did" => $device["did"],
                        	"pending" => count($this->cache->getAll())
                        ];
                    endforeach;
					
					usort($usort, function($previous, $next) {
					    return $previous["pending"] > $next["pending"] ? 1 : -1;
					});

					$request["device"] = $usort[0]["did"];
        		else:
        			if($this->system->checkDeviceByUnique($request["device"]) < 1)
        				response(500, lang_response_invalid);
        		endif;

        		$filtered = [
        			"uid" => logged_id,
        			"did" => $request["device"],
        			"sim" => ($request["sim"] < 1 ? 0 : 1),
        			"phone" => $request["phone"],
        			"message" => $request["message"],
        			"status" => 0,
        			"priority" => ($request["priority"] < 1 ? 0 : 1),
        			"api" => 0
        		];

        		$create = $this->system->create("sent", $filtered);

        		if($create):
        			$this->cache->container("gateway.{$filtered["did"]}." . logged_hash);

        			$this->cache->set($create, [
        				"api" => (boolean) 0,
        				"sim" => $filtered["sim"],
        				"device" => (int) $devices[$request["device"]]["id"],
        				"phone" => $filtered["phone"],
        				"message" => $filtered["message"],
        				"priority" => (boolean) $filtered["priority"],
        				"timestamp" => time()
        			]);

    				$this->cache->container("user." . logged_hash);
					$this->cache->clear();
        			$this->cache->container("messages." . logged_hash);
					$this->cache->clear();

					$this->system->increment(logged_id, "sent");

    				response(200, lang_response_message_queued);
        		else:
        			response(500, lang_response_went_wrong);
        		endif;

        		break;
        	case "sms.bulk":
        		if(!isset($request["groups"], $request["numbers"], $request["device"], $request["sim"], $request["priority"], $request["message"]))
        			response(500, lang_response_invalid);

        		if(!is_array($request["groups"]))
        			response(500, lang_response_invalid);

				if(!$this->sanitize->isInt($request["sim"]))
					response(500, lang_response_invalid);

				if(!$this->sanitize->isInt($request["priority"]))
					response(500, lang_response_invalid);

				if(!$this->sanitize->length($request["message"], 5))
					response(500, lang_response_message_short);

				$this->cache->container("user." . logged_hash);

    			if(!$this->cache->has("devices")):
    				$this->cache->set("devices", $this->system->getDevices(logged_id));
    			endif;

    			$devices = $this->cache->get("devices");

    			if(empty($devices))
    				response(500, lang_response_noregistereddevices);

    			if($this->system->checkQuota(logged_id) < 1):
					$this->system->create("quota", [
						"uid" => logged_id,
						"sent" => 0,
						"received" => 0
					]);
    			endif;

				foreach($request["groups"] as $group):
					if($this->system->checkGroup($group) > 0):
						$contacts = $this->system->getContactsByGroup(logged_id, $group);

						if(!empty($contacts)):
							foreach($contacts as $contact):
								if(!limitation(subscription_send, $this->system->countQuota(logged_id)["sent"])):
									if($request["device"] == "0"):
					        			foreach($devices as $device):
					                        $this->cache->container("gateway.{$device["did"]}." . logged_hash);

					                        $usort[] = [
					                        	"id" => $device["id"],
					                        	"did" => $device["did"],
					                        	"pending" => count($this->cache->getAll())
					                        ];
					                    endforeach;
										
										usort($usort, function($previous, $next) {
										    return $previous["pending"] > $next["pending"] ? 1 : -1;
										});

										$request["device"] = $usort[0]["did"];
					        		else:
					        			if($this->system->checkDeviceByUnique($request["device"]) < 1)
					        				response(500, lang_response_invalid);
					        		endif;

					        		$filtered = [
					        			"uid" => logged_id,
					        			"did" => $request["device"],
					        			"sim" => ($request["sim"] < 1 ? 0 : 1),
					        			"phone" => $contact["phone"],
					        			"message" => $this->lex->parse($request["message"], [
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
					        			"priority" => ($request["priority"] < 1 ? 0 : 1),
					        			"api" => 0
					        		];

					        		$create = $this->system->create("sent", $filtered);

					        		if($create):
					        			$this->cache->container("gateway.{$filtered["did"]}." . logged_hash);

					        			$this->cache->set($create, [
					        				"api" => (boolean) 0,
					        				"sim" => $filtered["sim"],
					        				"device" => (int) $devices[$request["device"]]["id"],
					        				"phone" => $filtered["phone"],
					        				"message" => $filtered["message"],
					        				"priority" => (boolean) $filtered["priority"],
					        				"timestamp" => time()
					        			]);

					        			$this->system->increment(logged_id, "sent");
					        		endif;
					        	endif;
							endforeach;
						endif;
					endif;
				endforeach;

				$numbers = explode("\n", trim($request["numbers"]));

				if(!empty($numbers) && !empty($numbers[0])):
					foreach($numbers as $number):
						if(!limitation(subscription_send, $this->system->countQuota(logged_id)["sent"])):
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
								if($request["device"] == "0"):
				        			foreach($devices as $device):
				                        $this->cache->container("gateway.{$device["did"]}." . logged_hash);

				                        $usort[] = [
				                        	"id" => $device["id"],
				                        	"did" => $device["did"],
				                        	"pending" => count($this->cache->getAll())
				                        ];
				                    endforeach;
									
									usort($usort, function($previous, $next) {
									    return $previous["pending"] > $next["pending"] ? 1 : -1;
									});

									$request["device"] = $usort[0]["did"];
				        		else:
				        			if($this->system->checkDeviceByUnique($request["device"]) < 1)
				        				response(500, lang_response_invalid);
				        		endif;

				        		$filtered = [
				        			"uid" => logged_id,
				        			"did" => $request["device"],
				        			"sim" => ($request["sim"] < 1 ? 0 : 1),
				        			"phone" => $request["phone"],
				        			"message" => $this->lex->parse($request["message"], [
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
				        			"priority" => ($request["priority"] < 1 ? 0 : 1),
				        			"api" => 0
				        		];

				        		$create = $this->system->create("sent", $filtered);

				        		if($create):
				        			$this->cache->container("gateway.{$filtered["did"]}." . logged_hash);

				        			$this->cache->set($create, [
				        				"api" => (boolean) 0,
				        				"sim" => $filtered["sim"],
				        				"device" => (int) $devices[$request["device"]]["id"],
				        				"phone" => $filtered["phone"],
				        				"message" => $filtered["message"],
				        				"priority" => (boolean) $filtered["priority"],
				        				"timestamp" => time()
				        			]);

				        			$this->system->increment(logged_id, "sent");
				        		endif;
				        	endif;
				        endif;
					endforeach;
				endif;

				$this->cache->container("user." . logged_hash);
				$this->cache->clear();
				$this->cache->container("messages." . logged_hash);
				$this->cache->clear();

				response(200, lang_response_message_bulkqueued);

        		break;
        	case "sms.excel":
        		if(!isset($_FILES["excel"], $request["device"]))
        			response(500, lang_response_invalid);

        		try {
        			$this->upload->upload($_FILES["excel"]);
	    			if($this->upload->uploaded):
	    				$this->upload->allowed = [
	    					"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
	    				];
		                $this->upload->file_new_name_body = logged_hash;
		                $this->upload->file_overwrite = true;
		                $this->upload->process("uploads/sheets/");

						if($this->upload->processed)
							$this->upload->clean();
						else
							response(500, lang_response_invalid_excel);
					endif;
        		} catch(Exception $e){
        			response(500, lang_response_invalid_excel);
        		}

        		$this->cache->container("user." . logged_hash);

    			if(!$this->cache->has("devices")):
    				$this->cache->set("devices", $this->system->getDevices(logged_id));
    			endif;

    			$devices = $this->cache->get("devices");

    			if(empty($devices))
    				response(500, lang_response_noregistereddevices);

    			if($this->system->checkQuota(logged_id) < 1):
					$this->system->create("quota", [
						"uid" => logged_id,
						"sent" => 0,
						"received" => 0
					]);
    			endif;

        		$reader = $this->sheet->read("uploads/sheets/" . logged_hash . ".xlsx");

        		foreach($reader->getSheetIterator() as $sheet):
				    if($sheet->getIndex() === 0):
				        foreach($sheet->getRowIterator() as $row):
				        	if(!limitation(subscription_send, $this->system->countQuota(logged_id)["sent"])):
					        	$recipient = [];

					        	foreach($row->getCells() as $cell):
					        		$recipient[] = $cell->getValue();
					        	endforeach;

					        	$recipient[0] = "+{$recipient[0]}";

					        	if(count($recipient) > 2):
					        		$rejected = false;

					        		try {
									    $number = $this->phone->parse($recipient[0]);

									    if(!$number->isValidNumber())
											$rejected = true;

										if(!$number->getNumberType(Brick\PhoneNumber\PhoneNumberType::MOBILE))
											$rejected = true;

										$request["phone"] = $number->format(Brick\PhoneNumber\PhoneNumberFormat::E164);
									} catch(Brick\PhoneNumber\PhoneNumberParseException $e) {
										$rejected = true;
									}

									if(!in_array($recipient[1], [1, 2]))
										$rejected = true;

									if(!in_array($recipient[2], [0, 1]))
										$rejected = true;

									if(!$rejected):
						        		if($request["device"] == "0"):
						        			foreach($devices as $device):
						                        $this->cache->container("gateway.{$device["did"]}." . logged_hash);

						                        $usort[] = [
						                        	"id" => $device["id"],
						                        	"did" => $device["did"],
						                        	"pending" => count($this->cache->getAll())
						                        ];
						                    endforeach;
											
											usort($usort, function($previous, $next) {
											    return $previous["pending"] > $next["pending"] ? 1 : -1;
											});

											$request["device"] = $usort[0]["did"];
						        		else:
						        			if($this->system->checkDeviceByUnique($request["device"]) < 1)
						        				response(500, lang_response_invalid);
						        		endif;
	                                           
						        		$filtered = [
						        			"uid" => logged_id,
						        			"did" => $request["device"],
						        			"sim" => ($recipient[1] < 2 ? 0 : 1),
						        			"phone" => $recipient[0],
						        			"message" => $recipient[3],
						        			"status" => 0,
						        			"priority" => ($recipient[2] < 1 ? 0 : 1),
						        			"api" => 0
						        		];

						        		$create = $this->system->create("sent", $filtered);

						        		if($create):
						        			$this->cache->container("gateway.{$filtered["did"]}." . logged_hash);

						        			$this->cache->set($create, [
						        				"api" => (boolean) 0,
						        				"sim" => $filtered["sim"],
						        				"device" => (int) $devices[$request["device"]]["id"],
						        				"phone" => $filtered["phone"],
						        				"message" => $filtered["message"],
						        				"priority" => (boolean) $filtered["priority"],
						        				"timestamp" => time()
						        			]);

						        			$this->system->increment(logged_id, "sent");
						        		endif;
						        	endif;
					        	endif;

					        	unset($recipient);
					        endif;
				        endforeach;
				    endif;	 
				endforeach;

				$reader->close();

				$this->cache->container("user." . logged_hash);
				$this->cache->clear();
				$this->cache->container("messages." . logged_hash);
				$this->cache->clear();

				response(200, lang_response_message_bulkqueued);

        		break;
        	case "sms.scheduled":
        		if(!isset($request["name"], $request["groups"], $request["numbers"], $request["schedule"], $request["repeat"], $request["device"], $request["sim"], $request["message"]))
        			response(500, lang_response_invalid);

        		if(!is_array($request["groups"]))
        			response(500, lang_response_invalid);

        		try {
        			if(time() > strtotime($request["schedule"]))
        				response(500, "Invalid date selected!");
        		} catch(Exception $e){
        			response(500, "Invalid date selected!");
        		}

        		if(!$this->sanitize->isInt($request["repeat"]))
					response(500, lang_response_invalid);

				if(!$this->sanitize->isInt($request["sim"]))
					response(500, lang_response_invalid);

				if(!$this->sanitize->length($request["message"], 5))
					response(500, lang_response_message_short);

				$this->cache->container("user." . logged_hash);

				if($request["device"] != "0"):
	    			if(!$this->cache->has("devices")):
	    				$this->cache->set("devices", $this->system->getDevices(logged_id));
	    			endif;

	    			$devices = $this->cache->get("devices");

	    			if(empty($devices))
	    				response(500, lang_response_noregistereddevices);

	    			if(!array_key_exists($request["device"], $devices))
	    				response(500, lang_response_invalid);
	    		endif;

    			$numbers = explode("\n", trim($request["numbers"]));

				$filtered = [
					"uid" => logged_id,
					"did" => $request["device"],
					"sim" => ($request["sim"] < 1 ? 0 : 1),
					"groups" => implode(",", $request["groups"]),
					"name" => $request["name"],
					"numbers" => (!empty($numbers) && !empty($numbers[0]) ? implode(",", $numbers) : false),
					"message" => $request["message"],
					"repeat" => ($request["repeat"] < 2 ? 1 : 2),
					"send_date" => strtotime($request["schedule"])
				];

				if($this->system->create("scheduled", $filtered)):
					$this->cache->container("user." . logged_hash);
					$this->cache->clear();
					$this->cache->container("messages." . logged_hash);
					$this->cache->clear();

					response(200, "Message has been scheduled!");
				else:
					response(500, lang_response_went_wrong);
				endif;

				response(200, lang_response_message_bulkqueued);

        		break;
        	case "add.template":
        		if(!isset($request["name"], $request["format"]))
        			response(500, lang_response_invalid);

        		if(!$this->sanitize->length($request["name"]))
					response(500, lang_response_name_short);

				if(!$this->sanitize->length($request["format"], 5))
					response(500, lang_response_format_short);

				$filtered = [
					"uid" => logged_id,
					"name" => $request["name"],
					"format" => $request["format"]
				];

				if($this->system->create("templates", $filtered)):
					$this->cache->container("messages." . logged_hash);
					$this->cache->clear();

					response(200, lang_response_template_added);
				else:
					response(500, lang_response_went_wrong);
				endif;

        		break;
        	case "add.contact":
        		if(!isset($request["name"], $request["phone"], $request["group"]))
        			response(500, lang_response_invalid);

        		if(!$this->sanitize->length($request["name"]))
					response(500, lang_response_name_short);

        		try {
				    $number = $this->phone->parse($request["phone"]);

				    if (!$number->isValidNumber())
						response(500, lang_response_invalid_number);

					if(!$number->getNumberType(Brick\PhoneNumber\PhoneNumberType::MOBILE))
						response(500, lang_response_invalid_number);

					$request["phone"] = $number->format(Brick\PhoneNumber\PhoneNumberFormat::E164);
				} catch(Brick\PhoneNumber\PhoneNumberParseException $e) {
					response(500, lang_response_invalid_number);
				}

				if(!$this->sanitize->isInt($request["group"]))
					response(500, lang_response_invalid);

				if(limitation(subscription_contact, $this->system->countContacts(logged_id)))
    				response(500, lang_response_limitation_contact);

				if($this->system->checkGroup($request["group"]) < 1)
					response(500, lang_response_invalid);

				if($this->system->checkNumber(logged_id, $request["phone"]) > 0)
					response(500, lang_response_number_exist);

				$filtered = [
					"uid" => logged_id,
					"gid" => $request["group"],
					"phone" => $request["phone"],
					"name" => $request["name"]
				];

				if($this->system->create("contacts", $filtered)):
					$this->cache->container("user." . logged_hash);
					$this->cache->clear();
					$this->cache->container("autocomplete.contacts." . logged_hash);
					$this->cache->clear();
					$this->cache->container("contacts." . logged_hash);
					$this->cache->clear();
					$this->cache->container("api.contacts." . logged_hash);
					$this->cache->clear();

					response(200, lang_response_contact_added);
				else:
					response(500, lang_response_went_wrong);
				endif;

        		break;
        	case "import.contacts":
        		if(!isset($_FILES["excel"]))
        			response(500, lang_response_invalid);

        		try {
        			$this->upload->upload($_FILES["excel"]);
	    			if($this->upload->uploaded):
	    				$this->upload->allowed = [
	    					"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
	    				];
		                $this->upload->file_new_name_body = logged_hash;
		                $this->upload->file_overwrite = true;
		                $this->upload->process("uploads/sheets/");

						if($this->upload->processed)
							$this->upload->clean();
						else
							response(500, lang_response_invalid_excel);
					endif;
        		} catch(Exception $e){
        			response(500, lang_response_invalid_excel);
        		}

        		$reader = $this->sheet->read("uploads/sheets/" . logged_hash . ".xlsx");

        		foreach($reader->getSheetIterator() as $sheet):
				    if($sheet->getIndex() === 0):
				        foreach($sheet->getRowIterator() as $row):
				        	if(limitation(subscription_contact, $this->system->countContacts(logged_id))):
				        		$this->cache->container("user." . logged_hash);
								$this->cache->clear();
								$this->cache->container("autocomplete.contacts." . logged_hash);
								$this->cache->clear();
								$this->cache->container("contacts." . logged_hash);
								$this->cache->clear();
								$this->cache->container("api.contacts." . logged_hash);
								$this->cache->clear();
				        		response(500, lang_response_limitation_contact);
				        	endif;

				        	$contact = [];

				        	foreach($row->getCells() as $cell):
				        		$contact[] = $cell->getValue();
				        	endforeach;

				        	$contact[0] = "+{$contact[0]}";

				        	if(count($contact) > 2):
				        		$rejected = false;

				        		try {
								    $number = $this->phone->parse($contact[0]);

								    if(!$number->isValidNumber())
										$rejected = true;

									if(!$number->getNumberType(Brick\PhoneNumber\PhoneNumberType::MOBILE))
										$rejected = true;

									$request["phone"] = $number->format(Brick\PhoneNumber\PhoneNumberFormat::E164);
								} catch(Brick\PhoneNumber\PhoneNumberParseException $e) {
									$rejected = true;
								}

								if(!$rejected):
									if($this->system->checkNumber(logged_id, $contact[0]) < 1):
										if($this->system->checkGroup($contact[2]) > 0):
											$filtered = [
							        			"uid" => logged_id,
							        			"gid" => $contact[2],
							        			"phone" => $contact[0],
							        			"name" => $contact[1]
							        		];

							        		$this->system->create("contacts", $filtered);
					        			endif;
					        		endif;
					        	endif;
				        	endif;

				        	unset($contact);
				        endforeach;
				    endif;	 
				endforeach;

				$reader->close();

				$this->cache->container("user." . logged_hash);
				$this->cache->clear();
				$this->cache->container("autocomplete.contacts." . logged_hash);
				$this->cache->clear();
				$this->cache->container("contacts." . logged_hash);
				$this->cache->clear();
				$this->cache->container("api.contacts." . logged_hash);
				$this->cache->clear();

				response(200, lang_response_contacts_imported);

        		break;
        	case "add.group":
        		if(!isset($request["name"]))
        			response(500, lang_response_invalid);

        		if(!$this->sanitize->length($request["name"]))
					response(500, lang_response_name_short);

				$filtered = [
					"uid" => logged_id,
					"name" => $request["name"]
				];

				if($this->system->create("groups", $filtered)):
					$this->cache->container("contacts." . logged_hash);
					$this->cache->clear();
					$this->cache->container("api.groups." . logged_hash);
					$this->cache->clear();

					response(200, lang_response_group_added);
				else:
					response(500, lang_response_went_wrong);
				endif;

        		break;
        	case "add.apikey":
        		if(!isset($request["name"], $request["devices"], $request["permissions"]))
        			response(500, lang_response_invalid);

        		if(!$this->sanitize->length($request["name"]))
					response(500, lang_response_name_short);

        		if(!is_array($request["devices"]) || !is_array($request["permissions"]))
        			response(500, lang_response_invalid);

        		if(empty($request["permissions"]))
        			response(500, lang_response_permission_min);

        		if(limitation(subscription_key, $this->system->countKeys(logged_id)))
    				response(500, lang_response_limitation_key);

        		foreach($request["devices"] as $key => $value):
        			$request["device_keys"][$value] = $value;
        		endforeach;

        		if(array_key_exists("0", $request["device_keys"])):
        			$request["devices"] = 0;
        		else:
        			$devices = $this->system->getDevicesUnique(logged_id);

        			foreach($request["device_keys"] as $key):
        				if(!array_key_exists($key, $devices))
        					response(500, lang_response_invalid);
        			endforeach;

        			$request["devices"] = implode(",", $request["devices"]);
        		endif;

        		foreach($request["permissions"] as $permission):
        			if(!in_array($permission, [
        				"get_pending",
        				"get_received",
        				"get_sent",
        				"send",
        				"get_contacts",
        				"get_groups",
        				"create_contact",
        				"create_group",
        				"delete_contact",
        				"delete_group",
        				"get_device",
        				"get_devices"
        			])):
        				response(500, lang_response_invalid);
        			endif;
        		endforeach;

				$filtered = [
					"uid" => logged_id,
					"key" => sha1(uniqid(time() . logged_id, true)),
					"name" => $request["name"],
					"devices" => $request["devices"],
					"permissions" => implode(",", $request["permissions"])
				];

				if($this->system->create("keys", $filtered)):
					$this->cache->container("tools." . logged_hash);
					$this->cache->clear();
					$this->cache->container("user." . logged_hash);
					$this->cache->clear();
					$this->cache->container("api.keys");
					$this->cache->clear();

					response(200, lang_response_key_added);
				else:
					response(500, lang_response_went_wrong);
				endif;

        		break;
        	case "add.webhook":
        		if(!isset($request["name"], $request["url"], $request["devices"]))
        			response(500, lang_response_invalid);

        		if(!$this->sanitize->length($request["name"]))
					response(500, lang_response_name_short);

        		if(!$this->sanitize->isUrl($request["url"]))
        			response(500, lang_response_invalid_webhookurl);

        		if(!is_array($request["devices"]))
        			response(500, lang_response_invalid);

        		if(limitation(subscription_webhook, $this->system->countWebhooks(logged_id)))
    				response(500, lang_response_limitation_webhook);

        		foreach($request["devices"] as $key => $value):
        			$request["device_keys"][$value] = $value;
        		endforeach;

        		if(array_key_exists("0", $request["device_keys"])):
        			$request["devices"] = 0;
        		else:
        			$devices = $this->system->getDevicesUnique(logged_id);

        			foreach($request["device_keys"] as $key):
        				if(!array_key_exists($key, $devices))
        					response(500, lang_response_invalid);
        			endforeach;

        			$request["devices"] = implode(",", $request["devices"]);
        		endif;

				$filtered = [
					"uid" => logged_id,
					"secret" => sha1(uniqid(time() . logged_id, true)),
					"name" => $request["name"],
					"url" => $this->sanitize->url($request["url"]),
					"devices" => $request["devices"]
				];

				if($this->system->create("webhooks", $filtered)):
					$this->cache->container("gateway." . logged_hash);
					$this->cache->clear();
					$this->cache->container("tools." . logged_hash);
					$this->cache->clear();
					$this->cache->container("user." . logged_hash);
					$this->cache->clear();

					response(200, lang_response_webhook_added);
				else:
					response(500, lang_response_went_wrong);
				endif;

        		break;
        	case "add.hook":
        		if(!isset($request["name"], $request["devices"], $request["event"], $request["link"]))
        			response(500, lang_response_invalid);

        		if(!$this->sanitize->length($request["name"]))
					response(500, lang_response_name_short);

        		if(!$this->sanitize->isUrl($request["link"]))
        			response(500, lang_response_invalid_linkstructure);

        		if(!$this->sanitize->isInt($request["event"]))
        			response(500, lang_response_invalid);

        		if(!in_array($request["event"], [1, 2]))
        			response(500, lang_response_invalid);

        		if(!is_array($request["devices"]))
        			response(500, lang_response_invalid);

        		foreach($request["devices"] as $key => $value):
        			$request["device_keys"][$value] = $value;
        		endforeach;

        		if(array_key_exists("0", $request["device_keys"])):
        			$request["devices"] = 0;
        		else:
        			$devices = $this->system->getDevicesUnique(logged_id);

        			foreach($request["device_keys"] as $key):
        				if(!array_key_exists($key, $devices))
        					response(500, lang_response_invalid);
        			endforeach;

        			$request["devices"] = implode(",", $request["devices"]);
        		endif;

				$filtered = [
					"uid" => logged_id,
					"type" => 1,
					"name" => $request["name"],
					"event" => $request["event"],
					"devices" => $request["devices"],
					"link" => $request["link"]
				];

				if($this->system->create("actions", $filtered)):
					$this->cache->container("gateway." . logged_hash);
					$this->cache->clear();
					$this->cache->container("tools." . logged_hash);
					$this->cache->clear();

					response(200, lang_response_hook_added);
				else:
					response(500, lang_response_went_wrong);
				endif;

        		break;
        	case "add.autoreply":
        		if(!isset($request["name"], $request["devices"], $request["keywords"], $request["message"]))
        			response(500, lang_response_invalid);

        		if(!$this->sanitize->length($request["name"]))
					response(500, lang_response_name_short);

				if(!$this->sanitize->length($request["keywords"]))
					response(500, lang_response_invalid_keywords);

				if(!$this->sanitize->length($request["message"]))
					response(500, lang_response_message_short);

        		if(!is_array($request["devices"]))
        			response(500, lang_response_invalid);

        		foreach($request["devices"] as $key => $value):
        			$request["device_keys"][$value] = $value;
        		endforeach;

        		if(array_key_exists("0", $request["device_keys"])):
        			$request["devices"] = 0;
        		else:
        			$devices = $this->system->getDevicesUnique(logged_id);

        			foreach($request["device_keys"] as $key):
        				if(!array_key_exists($key, $devices))
        					response(500, lang_response_invalid);
        			endforeach;

        			$request["devices"] = implode(",", $request["devices"]);
        		endif;

				$filtered = [
					"uid" => logged_id,
					"type" => 2,
					"name" => $request["name"],
					"event" => 0,
					"devices" => $request["devices"],
					"keywords" => $request["keywords"],
					"message" => $request["message"]
				];

				if($this->system->create("actions", $filtered)):
					$this->cache->container("gateway." . logged_hash);
					$this->cache->clear();
					$this->cache->container("tools." . logged_hash);
					$this->cache->clear();

					response(200, lang_response_autoreply_added);
				else:
					response(500, lang_response_went_wrong);
				endif;

        		break;
        	case "add.user":
        		if(!permission("manage_users"))
					response(500, lang_response_no_permission);

	            if(!isset($request["name"], $request["email"], $request["password"], $request["role"], $request["language"]))
	            	response(500, lang_response_invalid);

	            if(!$this->sanitize->length($request["name"]))
					response(500, lang_response_name_short);

	            if(!$this->sanitize->isEmail($request["email"]))
	            	response(500, lang_response_invalid_email);

	            if(!$this->sanitize->length($request["password"], 5))
	            	response(500, lang_response_password_short);

	            if(!$this->sanitize->isInt($request["role"]))
	            	response(500, lang_response_invalid);

	            if(!$this->sanitize->isInt($request["language"]))
	            	response(500, lang_response_invalid);

	            if($this->system->checkRole($request["role"]) < 1)
	            	response(500, lang_response_invalid);

	            if($this->system->checkLanguage($request["language"]) < 1)
	            	response(500, lang_response_invalid);

            	$filtered = [
            		"role" => $request["role"],
            		"name" => $request["name"],
            		"language" => $request["language"],
            		"email" => $this->sanitize->email($request["email"]),
            		"suspended" => 0,
            		"password" => password_hash($request["password"], PASSWORD_DEFAULT)
            	];

            	if($this->system->checkEmail($filtered["email"]) < 1):
            		$create = $this->system->create("users", $filtered);
            		if($create):
            			$this->cache->container("admin.statistics");
						$this->cache->clear();
            			$this->cache->container("admin.users");
						$this->cache->clear();

            			response(200, lang_response_user_added);
            		else:
            			response(500, lang_response_went_wrong);
            		endif;
            	else:
            		response(500, lang_response_email_unavailable);
            	endif;

				break;
			case "add.role":
				if(!super_admin)
					response(500, lang_response_no_permission);

        		if(!isset($request["name"], $request["permissions"]))
        			response(500, lang_response_invalid);

        		if(!$this->sanitize->length($request["name"]))
					response(500, lang_response_name_short);

        		if(!is_array($request["permissions"]))
        			response(500, lang_response_invalid);

        		if(empty($request["permissions"]))
        			response(500, lang_response_permission_min);

        		foreach($request["permissions"] as $permission):
        			if(!in_array($permission, [
        				"manage_users",
        				"manage_packages",
        				"manage_vouchers",
        				"manage_subscriptions",
        				"manage_transactions",
        				"manage_widgets",
        				"manage_pages",
        				"manage_languages",
        				"manage_fields"
        			])):
        				response(500, lang_response_invalid);
        			endif;
        		endforeach;

				$filtered = [
					"name" => $request["name"],
					"permissions" => implode(",", $request["permissions"])
				];

				if($this->system->create("roles", $filtered)):
					$this->cache->container("admin.roles");
					$this->cache->clear();

					response(200, lang_role_added);
				else:
					response(500, lang_response_went_wrong);
				endif;

        		break;
			case "add.package":
				if(!permission("manage_packages"))
					response(500, lang_response_no_permission);

	            if(!isset($request["name"], $request["price"], $request["send"], $request["receive"], $request["contact"], $request["device"], $request["key"], $request["webhook"]))
	            	response(500, lang_response_invalid);

	            if(!$this->sanitize->length($request["name"]))
					response(500, lang_response_name_short);

	            if(!$this->sanitize->isInt($request["price"]))
	            	response(500, lang_response_package_priceinvalid);

	            if(!$this->sanitize->isInt($request["send"]))
	            	response(500, lang_response_package_sendinvalid);

	         	if(!$this->sanitize->isInt($request["receive"]))
	            	response(500, lang_response_package_receiveinvalid);

	            if(!$this->sanitize->isInt($request["contact"]))
	            	response(500, lang_response_package_contactinvalid);

	            if(!$this->sanitize->isInt($request["device"]))
	            	response(500, lang_response_package_deviceinvalid);

	            if(!$this->sanitize->isInt($request["key"]))
	            	response(500, lang_response_package_keyinvalid);

	            if(!$this->sanitize->isInt($request["webhook"]))
	            	response(500, lang_response_package_hookinvalid);

            	$filtered = [
            		"name" => $request["name"],
            		"price" => $request["price"],
            		"send_limit" => $request["send"],
            		"receive_limit" => $request["receive"],
            		"contact_limit" => $request["contact"],
            		"device_limit" => $request["device"],
            		"key_limit" => $request["key"],
            		"webhook_limit" => $request["webhook"]
            	];

            	if($this->system->create("packages", $filtered)):
            		$this->cache->container("system.packages");
            		$this->cache->clear();
        			$this->cache->container("admin.packages");
					$this->cache->clear();

        			response(200, lang_response_package_added);
        		else:
        			response(500, lang_response_went_wrong);
        		endif;

				break;
			case "add.voucher":
				if(!permission("manage_vouchers"))
					response(500, lang_response_no_permission);

	            if(!isset($request["name"], $request["package"]))
	            	response(500, lang_response_invalid);

	            if(!$this->sanitize->length($request["name"]))
					response(500, lang_response_name_short);

				if(!$this->sanitize->isInt($request["package"]))
					response(500, lang_response_invalid);

				if($this->system->checkPackage($request["package"]) < 1)
	            	response(500, lang_response_invalid);

            	$filtered = [
            		"code" => md5(uniqid(time(), true)),
            		"name" => $request["name"],
            		"package" => $request["package"]
            	];

            	if($this->system->create("vouchers", $filtered)):
        			$this->cache->container("admin.vouchers");
					$this->cache->clear();

        			response(200, lang_voucher_added);
        		else:
        			response(500, lang_response_went_wrong);
        		endif;

				break;
			case "add.subscription":
				if(!permission("manage_subscriptions"))
					response(500, lang_response_no_permission);

	            if(!isset($request["user"], $request["package"]))
	            	response(500, lang_response_invalid);

	            if(!$this->sanitize->isInt($request["user"]))
					response(500, lang_response_invalid);

				if(!$this->sanitize->isInt($request["package"]))
					response(500, lang_response_invalid);

				if($this->system->checkUser($request["user"]) < 1)
	            	response(500, lang_response_invalid);

    			if($this->system->checkPackage($request["package"]) < 1)
	            	response(500, lang_response_invalid);

            	$filtered = [
            		"uid" => $request["user"],
            		"pid" => $request["package"]
            	];

            	if($this->system->delete($filtered["uid"], false, "subscriptions")):
            		if($this->system->create("subscriptions", $filtered)):
						$this->cache->container("user." . md5($filtered["uid"]));
						$this->cache->clear();
						$this->cache->container("user.subscription." . md5($filtered["uid"]));
						$this->cache->clear();
						$this->cache->container("admin.subscriptions");
						$this->cache->clear();

	        			response(200, "Subscription has been added!");
	        		else:
	        			response(500, lang_response_went_wrong);
	        		endif;
            	else:
            		response(500, lang_response_went_wrong);
            	endif;

				break;
			case "add.widget":
				if(!permission("manage_widgets"))
					response(500, lang_response_no_permission);

	            if(!isset($request["name"], $request["icon"], $request["type"], $request["size"], $request["position"], $request["content"]))
	            	response(500, lang_response_invalid);

	            if(!$this->sanitize->length($request["name"]))
					response(500, lang_response_name_short);

	            if(!$this->sanitize->isInt($request["type"]))
	            	response(500, lang_response_invalid);

	           	if(!in_array($request["type"], [1, 2]))
	           		response(500, lang_response_invalid);

	           	if(!in_array($request["size"], ["sm", "md", "lg", "xl"]))
	           		response(500, lang_response_invalid);

	           	if(!in_array($request["position"], ["center", "left", "right"]))
	           		response(500, lang_response_invalid);

            	$filtered = [
            		"icon" => $request["icon"],
            		"name" => $request["name"],
            		"type" => $request["type"],
            		"size" => $request["size"],
            		"position" => $request["position"],
            		"content" => $this->sanitize->htmlEncode($request["content"])
            	];

            	if($this->system->create("widgets", $filtered)):
        			$this->cache->container("admin.widgets");
					$this->cache->clear();
					$this->cache->container("system.blocks");
					$this->cache->clear();
					$this->cache->container("system.modals");
					$this->cache->clear();

        			response(200, lang_response_widget_added);
        		else:
        			response(500, lang_response_went_wrong);
        		endif;

				break;
			case "add.page":
				if(!permission("manage_pages"))
					response(500, lang_response_no_permission);

	            if(!isset($request["name"], $request["roles"], $request["logged"], $request["content"]))
	            	response(500, lang_response_invalid);

	            if(!$this->sanitize->length($request["name"]))
					response(500, lang_response_name_short);

	            if(!is_array($request["roles"]))
	            	response(500, lang_response_invalid);

	            if(empty($request["roles"]))
	            	response(500, lang_response_invalid);

	            if(!$this->sanitize->isInt($request["logged"]))
					response(500, lang_response_invalid);

				if(!in_array($request["logged"], [1, 2]))
					response(500, lang_response_invalid);

            	$filtered = [
            		"slug" => $this->slug->create($request["name"]),
            		"logged" => $request["logged"],
            		"name" => $request["name"],
            		"roles" => implode(",", $request["roles"]),
            		"content" => $this->sanitize->htmlEncode($request["content"])
            	];

            	if($this->system->create("pages", $filtered)):
        			$this->cache->container("admin.pages");
					$this->cache->clear();

        			response(200, lang_response_page_added);
        		else:
        			response(500, lang_response_went_wrong);
        		endif;

				break;
			case "add.language":
				if(!permission("manage_languages"))
					response(500, lang_response_no_permission);

	            if(!isset($request["name"], $request["iso"], $request["translations"]))
	            	response(500, lang_response_invalid);

	            if(!$this->sanitize->length($request["name"]))
					response(500, lang_response_name_short);

	            if(!array_key_exists($request["iso"], \CountryCodes::get("alpha2", "country")))
	            	response(500, lang_response_invalid);

            	$filtered = [
            		"name" => $request["name"],
            		"iso" => strtoupper($request["iso"]),
            		"translations" => $request["translations"]
            	];

            	if($this->system->create("languages", $filtered)):
            		$this->cache->container("system.languages");
            		$this->cache->clear();
        			$this->cache->container("admin.languages");
					$this->cache->clear();

        			response(301, lang_response_language_added);
        		else:
        			response(500, lang_response_went_wrong);
        		endif;

				break;
        	default:
        		response(500, lang_response_invalid);
        endswitch;
	}

	public function update()
	{
		$this->header->allow(site_url);

		$this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSystemSettings());
        endif;

        set_system($this->cache->getAll());

        set_logged($this->session->get("logged"));

        $this->cache->container("system.language." . logged_language);

        if($this->cache->empty()):
            $this->cache->setRaw($this->system->getTranslations(logged_language));
        endif;

        set_language($this->cache->getRaw());

       	if(!$this->session->has("logged"))
            response(302, lang_response_session_false);

        $type = $this->sanitize->string($this->url->segment(4));
        $request = $this->sanitize->array($_POST, 
        	in_array($type, ["edit.widget", "edit.page"]) ? ["content"] : []
    	);

        if(!isset($request["id"]) || !$this->sanitize->isInt($request["id"]))
        	response(500, lang_response_invalid);

        switch($type):
        	case "user.settings":
        		if(!isset($request["name"], $request["email"], $request["password"], $request["current_email"]))
        			response(500, lang_response_invalid);

        		if($request["id"] != logged_id)
        			response(500, lang_response_invalid);

        		if(!$this->sanitize->length($request["name"]))
        			response(500, lang_response_name_short);

        		if(!$this->sanitize->isEmail($request["email"]))
        			response(500, lang_response_invalid_email);

        		if($request["current_email"] != $request["email"] && $this->system->checkEmail($request["email"]) > 0)
	            	response(500, lang_response_email_unavailable);

        		$filtered = [
        			"name" => $request["name"],
        			"email" => $this->sanitize->email($request["email"])
        		];

        		if(!empty($request["password"])):
        			if(!$this->sanitize->length($request["password"], 5))
        				response(500, lang_response_password_short);
        			else
        				$filtered["password"] = password_hash($request["password"], PASSWORD_DEFAULT);
        		endif;

        		if($this->system->update($request["id"], false, "users", $filtered)):
        			if(isset($_FILES["avatar"])):
	        			$this->upload->upload($_FILES["avatar"]);
	        			if($this->upload->uploaded):
	        				$this->upload->allowed = [
	        					"image/*"
	        				];
			                $this->upload->file_new_name_body = logged_hash;
			                $this->upload->image_convert = "jpg";
							$this->upload->image_resize = true;
							$this->upload->image_x = 150;
							$this->upload->image_ratio_y = true;
			                $this->upload->file_overwrite = true;
			                $this->upload->process("uploads/avatars/");

							if($this->upload->processed)
								$this->upload->clean();
							else
								response(500, lang_response_avatar_invalid);
						endif;
		            endif;

		            $this->session->set("logged", $this->system->getUser(logged_id));
        			$this->cache->container("admin.users");
					$this->cache->clear();

        			response(200, lang_response_profile_updated);
        		else:
        			response(500, lang_response_went_wrong);
        		endif;

        		break;
        	case "payment":
        		if(!isset($request["provider"]))
        			response(500, lang_response_invalid);

        		if($request["provider"] != "voucher"):
	        		if(!in_array($request["provider"], explode(",", system_providers)))
						response(500, lang_response_invalid);
				endif;

        		if($this->system->checkPackage($request["id"]) < 1)
					response(500, lang_response_invalid);

				$package = $this->system->getPackage($request["id"]);

				switch($request["provider"]):
					case "paypal":
						if(!isset($request["number"], $request["expiry"], $request["name"], $request["cvc"]))
		        			response(500, lang_response_invalid);

		        		if($request["id"] < 2)
		        			response(500, lang_response_invalid);

		        		if(!$this->sanitize->length($request["name"]))
							response(500, lang_response_name_short);

		        		if(!in_array($request["provider"], ["paypal", "mollie"]))
		        			response(500, lang_response_invalid);

		        		if(!Freelancehunt\Validators\CreditCard::validCreditCard($request["number"])["valid"])
		        			response(500, lang_response_invalid_cardnumber);

		        		$transaction = [
							"name" => $request["name"],
							"number" => $request["number"],
							"expiry" => explode("/", $request["expiry"]),
							"cvc" => $request["cvc"] 
						];

						if(count($transaction["expiry"]) < 2)
							response(500, lang_response_invalid_cardexpiry);

						$gateway = Omnipay\Omnipay::create("PayPal_Pro");

						$gateway->initialize([
							"username" => system_paypal_username,
							"password" => system_paypal_password,
							"signature" => system_paypal_signature,
							"testMode" => (system_paypal_test < 2 ? true : false)
						]);

						$response = $gateway->purchase([
							"amount" => $package["price"],
							"currency" => strtoupper(system_currency),
							"card" => [
								"name" => $transaction["name"],
								"number" => $transaction["number"],
								"expiryMonth" => $transaction["expiry"][0],
								"expiryYear" => $transaction["expiry"][1],
								"cvv" => $transaction["cvc"]
							]
						])->send();

						if($response->isSuccessful()):
							if($this->system->checkSubscriptionByUserID(logged_id) > 0):
								$filtered = [
									"pid" => $request["id"]
								];

								$subscription = $this->system->getSubscriptionByUserId(logged_id);
								if($this->system->update($subscription["id"], logged_id, "subscriptions", $filtered)):
									if($this->system->create("transactions", [
										"uid" => logged_id,
										"pid" => $filtered["pid"],
										"price" => $package["price"],
										"provider" => $request["provider"]
									])):
										$this->cache->container("admin.statistics");
										$this->cache->clear();
										$this->cache->container("admin.subscriptions");
										$this->cache->clear();
										$this->cache->container("admin.transactions");
										$this->cache->clear();
										$this->cache->container("user." . logged_hash);
										$this->cache->clear();
									endif;
								endif;
							else:
								$filtered = [
									"uid" => logged_id,
									"pid" => $request["id"]
								];

								if($this->system->create("subscriptions", $filtered)):
									if($this->system->create("transactions", [
										"uid" => $filtered["uid"],
										"pid" => $filtered["pid"],
										"price" => $package["price"],
										"provider" => $request["provider"]
									])):
										$this->cache->container("admin.statistics");
										$this->cache->clear();
										$this->cache->container("admin.subscriptions");
										$this->cache->clear();
										$this->cache->container("admin.transactions");
										$this->cache->clear();
										$this->cache->container("user." . logged_hash);
										$this->cache->clear();
									endif;
								endif;
							endif;

							try {
								$vars = [
									"title" => system_site_name,
									"data" => [
										"subject" => lang_response_package_purchasedtitle,
										"package" => $this->system->getPackage($request["id"]),
										"subscription" => $this->system->getSubscriptionByUserId(logged_id)
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
							    $this->mail->addAddress(logged_email);
							    $this->mail->isHTML(true);  
							    $this->mail->msgHTML($this->smarty->fetch("_mail/subscribe.tpl"));

							    $this->mail->send();
							} catch(Exception $e){
								// Ignore exceptions
							}

							response(200, lang_response_package_purchased);
						else:
							response(500, lang_response_card_decline);
						endif;

						break;
					case "stripe":
						if(!isset($request["stripe_token"]))
	        				response(500, lang_response_invalid);

	        			\Stripe\Stripe::setApiKey(system_stripe_secret); 

	        			try {  
					        $customer = \Stripe\Customer::create([ 
					            "email" => logged_email, 
					            "source"  => $request["stripe_token"] 
					        ]); 
					    } catch(Exception $e) {  
					        response(500, lang_payment_proccess_error);
					    }

					    try {  
				            $charge = \Stripe\Charge::create([
				                "customer" => $customer->id, 
				                "amount" => in_array(system_currency, ["jpy"]) ? $package["price"] : $package["price"] * 100, 
				                "currency" => strtoupper(system_currency),
				                "description" => $package["name"] 
				            ]); 
				        } catch(Exception $e) {  
				            response(500, lang_response_card_decline);  
				        }  

				        if($charge->amount_refunded == 0 && empty($charge->failure_code) && $charge->paid == 1 && $charge->captured == 1):
				        	if($this->system->checkSubscriptionByUserID(logged_id) > 0):
								$filtered = [
									"pid" => $request["id"]
								];

								$subscription = $this->system->getSubscriptionByUserId(logged_id);
								if($this->system->update($subscription["id"], logged_id, "subscriptions", $filtered)):
									if($this->system->create("transactions", [
										"uid" => logged_id,
										"pid" => $filtered["pid"],
										"price" => $package["price"],
										"provider" => $request["provider"]
									])):
										$this->cache->container("admin.statistics");
										$this->cache->clear();
										$this->cache->container("admin.subscriptions");
										$this->cache->clear();
										$this->cache->container("admin.transactions");
										$this->cache->clear();
										$this->cache->container("user." . logged_hash);
										$this->cache->clear();
									endif;
								endif;
							else:
								$filtered = [
									"uid" => logged_id,
									"pid" => $request["id"]
								];

								if($this->system->create("subscriptions", $filtered)):
									if($this->system->create("transactions", [
										"uid" => $filtered["uid"],
										"pid" => $filtered["pid"],
										"price" => $package["price"],
										"provider" => $request["provider"]
									])):
										$this->cache->container("admin.statistics");
										$this->cache->clear();
										$this->cache->container("admin.subscriptions");
										$this->cache->clear();
										$this->cache->container("admin.transactions");
										$this->cache->clear();
										$this->cache->container("user." . logged_hash);
										$this->cache->clear();
									endif;
								endif;
							endif;

							try {
								$vars = [
									"title" => system_site_name,
									"data" => [
										"subject" => lang_response_package_purchasedtitle,
										"package" => $this->system->getPackage($request["id"]),
										"subscription" => $this->system->getSubscriptionByUserId(logged_id)
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
							    $this->mail->addAddress(logged_email);
							    $this->mail->isHTML(true);  
							    $this->mail->msgHTML($this->smarty->fetch("_mail/subscribe.tpl"));

							    $this->mail->send();
							} catch(Exception $e){
								// Ignore exceptions
							}

							response(200, lang_response_package_purchased);
				        else:
				        	response(500, lang_payment_failed);
				        endif;

						break;
					case "mollie":
						$mollie = new \Mollie\Api\MollieApiClient();
						$mollie->setApiKey(system_mollie_key);

						$paymentId = uniqid(time());

						$payment = $mollie->payments->create([
						    "amount" => [
						        "currency" => strtoupper(system_currency),
						        "value" => in_array(system_currency, ["jpy"]) ? "{$package["price"]}" : "{$package["price"]}.00"
						    ],
						    "description" => $package["name"],
						    "redirectUrl" => str_replace("//", (system_protocol < 2 ? "http://" : "https://"), site_url) . "/payment/{$paymentId}",
						    "webhookUrl"  => str_replace("//", (system_protocol < 2 ? "http://" : "https://"), site_url) . "/payment/webhook/mollie",
						    "metadata" => [
					            "order_id" => $paymentId,
					        ]
						]);

						$this->cache->container("system.payments");
						$this->cache->set($paymentId, [
							"hash" => logged_hash,
							"email" => logged_email,
							"uid" => logged_id,
							"pid" => $request["id"],
							"price" => $package["price"]
						]);

						response(200, lang_mollie_redirecting_page, $payment->getCheckoutUrl());

						break;
					case "voucher":
						if(!isset($request["code"]))
	        				response(500, lang_response_invalid);

	        			if(!$this->sanitize->length($request["code"]))
	        				response(500, lang_invalid_voucher_code);

	        			if($this->system->checkVoucher($request["code"]) < 1)
	        				response(500, lang_invalid_voucher_code);

	        			$voucher = $this->system->getVoucher($request["code"]);

	        			if($voucher["package"] != $package["id"])
	        				response(500, lang_voucher_code_unmatched);

			        	if($this->system->checkSubscriptionByUserID(logged_id) > 0):
							$filtered = [
								"pid" => $request["id"]
							];

							$subscription = $this->system->getSubscriptionByUserId(logged_id);
							if($this->system->update($subscription["id"], logged_id, "subscriptions", $filtered)):
								if($this->system->create("transactions", [
									"uid" => logged_id,
									"pid" => $filtered["pid"],
									"price" => $package["price"],
									"provider" => $request["provider"]
								])):
									$this->cache->container("admin.statistics");
									$this->cache->clear();
									$this->cache->container("admin.subscriptions");
									$this->cache->clear();
									$this->cache->container("admin.transactions");
									$this->cache->clear();
									$this->cache->container("user." . logged_hash);
									$this->cache->clear();
								endif;
							endif;
						else:
							$filtered = [
								"uid" => logged_id,
								"pid" => $request["id"]
							];

							if($this->system->create("subscriptions", $filtered)):
								if($this->system->create("transactions", [
									"uid" => $filtered["uid"],
									"pid" => $filtered["pid"],
									"price" => $package["price"],
									"provider" => $request["provider"]
								])):
									$this->cache->container("admin.statistics");
									$this->cache->clear();
									$this->cache->container("admin.subscriptions");
									$this->cache->clear();
									$this->cache->container("admin.transactions");
									$this->cache->clear();
									$this->cache->container("user." . logged_hash);
									$this->cache->clear();
								endif;
							endif;
						endif;

						try {
							$vars = [
								"title" => system_site_name,
								"data" => [
									"subject" => lang_response_package_purchasedtitle,
									"package" => $this->system->getPackage($request["id"]),
									"subscription" => $this->system->getSubscriptionByUserId(logged_id)
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
						    $this->mail->addAddress(logged_email);
						    $this->mail->isHTML(true);  
						    $this->mail->msgHTML($this->smarty->fetch("_mail/subscribe.tpl"));

						    $this->mail->send();
						} catch(Exception $e){
							// Ignore exceptions
						}

						$this->system->delete(false, $voucher["id"], "vouchers");

						$this->cache->container("admin.vouchers");
						$this->cache->clear();

						response(200, lang_response_package_purchased);

						break;
					default:
						response(500, lang_response_invalid);
				endswitch;

				break;
        	case "edit.template":
        		if(!isset($request["name"], $request["format"]))
        			response(500, lang_response_invalid);

        		if(!$this->sanitize->length($request["name"]))
					response(500, lang_response_name_short);

				if(!$this->sanitize->length($request["format"], 5))
					response(500, lang_response_format_short);

        		$filtered = [
        			"name" => $request["name"],
        			"format" => $request["format"]
        		];

        		if($this->system->update($request["id"], logged_id, "templates", $filtered)):
        			$this->cache->container("messages." . logged_hash);
					$this->cache->clear();

        			response(200, lang_response_template_updated);
        		else:
        			response(500, lang_response_went_wrong);
        		endif;

        		break;
        	case "edit.contact":
        		if(!isset($request["name"], $request["phone"], $request["group"], $request["current_phone"]))
        			response(500, lang_response_invalid);

        		if(!$this->sanitize->length($request["name"]))
					response(500, lang_response_name_short);

        		if($request["current_phone"] != $request["phone"]):
    				try {
					    $number = $this->phone->parse($request["phone"]);

					    if(!$number->isValidNumber())
							response(500, lang_response_invalid_number);

						if(!$number->getNumberType(Brick\PhoneNumber\PhoneNumberType::MOBILE))
							response(500, lang_response_invalid_number);

						$request["phone"] = $number->format(Brick\PhoneNumber\PhoneNumberFormat::E164);
					} catch(Brick\PhoneNumber\PhoneNumberParseException $e) {
						response(500, lang_response_invalid_number);
					}

        			if($this->system->checkNumber(logged_id, $request["phone"]) > 0)
        				response(500, lang_response_number_exist);
        		endif;

        		if(!$this->sanitize->isInt($request["group"]))
        			response(500, lang_response_invalid);

        		if($this->system->checkGroup($request["group"]) < 1)
					response(500, lang_response_invalid);

        		$filtered = [
        			"gid" => $request["group"],
        			"name" => $request["name"],
        			"phone" => $request["phone"]
        		];

        		if($this->system->update($request["id"], logged_id, "contacts", $filtered)):
        			$this->cache->container("user." . logged_hash);
					$this->cache->clear();
        			$this->cache->container("autocomplete.contacts." . logged_hash);
					$this->cache->clear();
        			$this->cache->container("contacts." . logged_hash);
					$this->cache->clear();

        			response(200, lang_response_contact_updated);
        		else:
        			response(500, lang_response_went_wrong);
        		endif;

        		break;
        	case "edit.group":
        		if(!isset($request["name"]))
        			response(500, lang_response_invalid);

        		if(!$this->sanitize->length($request["name"]))
					response(500, lang_response_name_short);

        		$filtered = [
        			"name" => $request["name"]
        		];

        		if($this->system->update($request["id"], logged_id, "groups", $filtered)):
        			$this->cache->container("contacts." . logged_hash);
					$this->cache->clear();

        			response(200, lang_response_group_updated);
        		else:
        			response(500, lang_response_went_wrong);
        		endif;

        		break;
        	case "edit.apikey":
        		if(!isset($request["name"], $request["devices"], $request["permissions"]))
        			response(500, lang_response_invalid);

        		if(!$this->sanitize->length($request["name"]))
					response(500, lang_response_name_short);

        		if(!is_array($request["devices"]) || !is_array($request["permissions"]))
        			response(500, lang_response_invalid);

        		if(empty($request["permissions"]))
        			response(500, lang_response_permission_min);

        		foreach($request["devices"] as $key => $value):
        			$request["device_keys"][$value] = $value;
        		endforeach;

        		if(array_key_exists("0", $request["device_keys"])):
        			$request["devices"] = 0;
        		else:
        			$devices = $this->system->getDevicesUnique(logged_id);

        			foreach($request["device_keys"] as $key):
        				if(!array_key_exists($key, $devices))
        					response(500, lang_response_invalid);
        			endforeach;

        			$request["devices"] = implode(",", $request["devices"]);
        		endif;

        		foreach($request["permissions"] as $permission):
        			if(!in_array($permission, [
        				"get_pending",
        				"get_received",
        				"get_sent",
        				"send",
        				"get_contacts",
        				"get_groups",
        				"create_contact",
        				"create_group",
        				"delete_contact",
        				"delete_group",
        				"get_device",
        				"get_devices"
        			])):
        				response(500, lang_response_invalid);
        			endif;
        		endforeach;

				$filtered = [
					"name" => $request["name"],
					"devices" => $request["devices"],
					"permissions" => implode(",", $request["permissions"])
				];

				if($this->system->update($request["id"], logged_id, "`keys`", $filtered)):
					$this->cache->container("tools." . logged_hash);
					$this->cache->clear();
					$this->cache->container("user." . logged_hash);
					$this->cache->clear();
					$this->cache->container("api.keys");
					$this->cache->clear();

					response(200, lang_response_key_updated);
				else:
					response(500, lang_response_went_wrong);
				endif;

        		break;
        	case "edit.webhook":
        		if(!isset($request["name"], $request["url"], $request["devices"]))
        			response(500, lang_response_invalid);

        		if(!$this->sanitize->length($request["name"]))
					response(500, lang_response_name_short);

        		if(!$this->sanitize->isUrl($request["url"]))
        			response(500, lang_response_invalid_webhookurl);

        		if(!is_array($request["devices"]))
        			response(500, lang_response_invalid);

        		foreach($request["devices"] as $key => $value):
        			$request["device_keys"][$value] = $value;
        		endforeach;

        		if(array_key_exists("0", $request["device_keys"])):
        			$request["devices"] = 0;
        		else:
        			$devices = $this->system->getDevicesUnique(logged_id);

        			foreach($request["device_keys"] as $key):
        				if(!array_key_exists($key, $devices))
        					response(500, lang_response_invalid);
        			endforeach;

        			$request["devices"] = implode(",", $request["devices"]);
        		endif;

				$filtered = [
					"name" => $request["name"],
					"url" => $this->sanitize->url($request["url"]),
					"devices" => $request["devices"]
				];

				if($this->system->update($request["id"], logged_id, "webhooks", $filtered)):
					$this->cache->container("gateway." . logged_hash);
					$this->cache->clear();
					$this->cache->container("tools." . logged_hash);
					$this->cache->clear();
					$this->cache->container("user." . logged_hash);
					$this->cache->clear();

					response(200, lang_response_webhook_updated);
				else:
					response(500, lang_response_went_wrong);
				endif;

        		break;
        	case "edit.hook":
        		if(!isset($request["name"], $request["devices"], $request["event"], $request["link"]))
        			response(500, lang_response_invalid);

        		if(!$this->sanitize->length($request["name"]))
					response(500, lang_response_name_short);

        		if(!$this->sanitize->isUrl($request["link"]))
        			response(500, lang_response_invalid_linkstructure);

        		if(!$this->sanitize->isInt($request["event"]))
        			response(500, lang_response_invalid);

        		if(!in_array($request["event"], [1, 2]))
        			response(500, lang_response_invalid);

        		if(!is_array($request["devices"]))
        			response(500, lang_response_invalid);

        		foreach($request["devices"] as $key => $value):
        			$request["device_keys"][$value] = $value;
        		endforeach;

        		if(array_key_exists("0", $request["device_keys"])):
        			$request["devices"] = 0;
        		else:
        			$devices = $this->system->getDevicesUnique(logged_id);

        			foreach($request["device_keys"] as $key):
        				if(!array_key_exists($key, $devices))
        					response(500, lang_response_invalid);
        			endforeach;

        			$request["devices"] = implode(",", $request["devices"]);
        		endif;

				$filtered = [
					"name" => $request["name"],
					"event" => $request["event"],
					"link" => $request["link"],
					"devices" => $request["devices"]
				];

				if($this->system->update($request["id"], logged_id, "actions", $filtered)):
					$this->cache->container("gateway." . logged_hash);
					$this->cache->clear();
					$this->cache->container("tools." . logged_hash);
					$this->cache->clear();

					response(200, lang_response_hook_updated);
				else:
					response(500, lang_response_went_wrong);
				endif;

        		break;
        	case "edit.autoreply":
        		if(!isset($request["name"], $request["devices"], $request["keywords"], $request["message"]))
        			response(500, lang_response_invalid);

        		if(!$this->sanitize->length($request["name"]))
					response(500, lang_response_name_short);

				if(!$this->sanitize->length($request["keywords"]))
					response(500, lang_response_invalid_keywords);

				if(!$this->sanitize->length($request["message"]))
					response(500, lang_response_message_short);

        		if(!is_array($request["devices"]))
        			response(500, lang_response_invalid);

        		foreach($request["devices"] as $key => $value):
        			$request["device_keys"][$value] = $value;
        		endforeach;

        		if(array_key_exists("0", $request["device_keys"])):
        			$request["devices"] = 0;
        		else:
        			$devices = $this->system->getDevicesUnique(logged_id);

        			foreach($request["device_keys"] as $key):
        				if(!array_key_exists($key, $devices))
        					response(500, lang_response_invalid);
        			endforeach;

        			$request["devices"] = implode(",", $request["devices"]);
        		endif;

				$filtered = [
					"name" => $request["name"],
					"keywords" => $request["keywords"],
					"message" => $request["message"],
					"devices" => $request["devices"]
				];

				if($this->system->update($request["id"], logged_id, "actions", $filtered)):
					$this->cache->container("gateway." . logged_hash);
					$this->cache->clear();
					$this->cache->container("tools." . logged_hash);
					$this->cache->clear();

					response(200, lang_response_autoreply_updated);
				else:
					response(500, lang_response_went_wrong);
				endif;

        		break;
        	case "edit.user":
        		if(!permission("manage_users"))
					response(500, lang_response_no_permission);

				if($request["id"] < 2):
					if(!super_admin)
						response(500, lang_response_no_permission);
				endif;

	            if(!isset($request["name"], $request["email"], $request["password"], $request["role"], $request["language"], $request["current_email"]))
	            	response(500, lang_response_invalid);

	            if(!$this->sanitize->length($request["name"]))
					response(500, lang_response_name_short);

	            if(!$this->sanitize->isEmail($request["email"]))
	            	response(500, lang_response_invalid_email);

	            if($request["current_email"] != $request["email"] && $this->system->checkEmail($request["email"]) > 0)
	            	response(500, lang_response_email_unavailable);

	            if(!$this->sanitize->isInt($request["role"]))
	            	response(500, lang_response_invalid);

	            if(!$this->sanitize->isInt($request["language"]))
	            	response(500, lang_response_invalid);

	            if($this->system->checkRole($request["role"]) < 1)
	            	response(500, lang_response_invalid);

	            if($this->system->checkLanguage($request["language"]) < 1)
	            	response(500, lang_response_invalid);

            	$filtered = [
            		"role" => $request["role"],
            		"name" => $request["name"],
            		"language" => $request["language"],
            		"email" => $this->sanitize->email($request["email"])
            	];

            	if(!empty($request["password"])):
	            	if(!$this->sanitize->length($request["password"], 5))
	            		response(500, lang_response_password_short);
            		else
            			$filtered["password"] = password_hash($request["password"], PASSWORD_DEFAULT);
		        endif;

        		if($this->system->update($request["id"], false, "users", $filtered)):
        			$this->cache->container("user." . md5($request["id"]));
        			$this->cache->clear();
        			$this->cache->container("admin.statistics");
					$this->cache->clear();
        			$this->cache->container("admin.users");
					$this->cache->clear();

        			response(200, lang_response_user_updated);
        		else:
        			response(500, lang_response_went_wrong);
        		endif;

				break;
			case "edit.role":
				if(!super_admin)
					response(500, lang_response_no_permission);

				if($request["id"] < 2)
					response(500, lang_role_default_update);

        		if(!isset($request["name"], $request["permissions"]))
        			response(500, lang_response_invalid);

        		if(!$this->sanitize->length($request["name"]))
					response(500, lang_response_name_short);

        		if(!is_array($request["permissions"]))
        			response(500, lang_response_invalid);

        		if(empty($request["permissions"]))
        			response(500, lang_response_permission_min);

        		foreach($request["permissions"] as $permission):
        			if(!in_array($permission, [
        				"manage_users",
        				"manage_packages",
        				"manage_vouchers",
        				"manage_subscriptions",
        				"manage_transactions",
        				"manage_widgets",
        				"manage_pages",
        				"manage_languages",
        				"manage_fields"
        			])):
        				response(500, lang_response_invalid);
        			endif;
        		endforeach;

				$filtered = [
					"name" => $request["name"],
					"permissions" => implode(",", $request["permissions"])
				];

				if($this->system->update($request["id"], false, "roles", $filtered)):
					$this->cache->container("admin.roles");
					$this->cache->clear();

					response(200, lang_role_updated);
				else:
					response(500, lang_response_went_wrong);
				endif;

        		break;
			case "edit.package":
				if(!permission("manage_packages"))
					response(500, lang_response_no_permission);

	            if(!isset($request["name"], $request["price"], $request["send"], $request["receive"], $request["contact"], $request["device"], $request["key"], $request["webhook"]))
	            	response(500, lang_response_invalid);

	            if(!$this->sanitize->length($request["name"]))
					response(500, lang_response_name_short);

	            if(!$this->sanitize->isInt($request["price"]))
	            	response(500, lang_response_package_priceinvalid);

	            if($request["id"] < 2 && $request["price"] > 0)
	            	response(500, lang_response_package_premiumfalse);

	            if(!$this->sanitize->isInt($request["send"]))
	            	response(500, lang_response_package_sendinvalid);

	         	if(!$this->sanitize->isInt($request["receive"]))
	            	response(500, lang_response_package_receiveinvalid);

	            if(!$this->sanitize->isInt($request["contact"]))
	            	response(500, lang_response_package_contactinvalid);

	            if(!$this->sanitize->isInt($request["device"]))
	            	response(500, lang_response_package_deviceinvalid);

	            if(!$this->sanitize->isInt($request["key"]))
	            	response(500, lang_response_package_keyinvalid);

	            if(!$this->sanitize->isInt($request["webhook"]))
	            	response(500, lang_response_package_hookinvalid);

            	$filtered = [
            		"name" => $request["name"],
            		"price" => $request["price"],
            		"send_limit" => $request["send"],
            		"receive_limit" => $request["receive"],
            		"contact_limit" => $request["contact"],
            		"device_limit" => $request["device"],
            		"key_limit" => $request["key"],
            		"webhook_limit" => $request["webhook"]
            	];

        		if($this->system->update($request["id"], false, "packages", $filtered)):
        			$this->cache->container("system.packages");
            		$this->cache->clear();
        			$this->cache->container("admin.packages");
					$this->cache->clear();

					foreach($this->system->getUsers() as $user):
    					$this->cache->container("user.{$user["hash"]}");
						$this->cache->clear();
						$this->cache->container("user.subscription.{$user["hash"]}");
						$this->cache->clear();
        			endforeach;

        			response(200, lang_response_package_updated);
        		else:
        			response(500, lang_response_went_wrong);
        		endif;

				break;
			case "edit.widget":
				if(!permission("manage_widgets"))
					response(500, lang_response_no_permission);

	            if(!isset($request["name"], $request["icon"], $request["type"], $request["size"], $request["position"], $request["content"]))
	            	response(500, lang_response_invalid);

	            if(!$this->sanitize->length($request["name"]))
					response(500, lang_response_name_short);

	            if(!$this->sanitize->isInt($request["type"]))
	            	response(500, lang_response_invalid);

	           	if(!in_array($request["type"], [1, 2]))
	           		response(500, lang_response_invalid);

	           	if(!in_array($request["size"], ["sm", "md", "lg", "xl"]))
	           		response(500, lang_response_invalid);
	           	
	           	if(!in_array($request["position"], ["center", "left", "right"]))
	           		response(500, lang_response_invalid);

            	$filtered = [
            		"icon" => $request["icon"],
            		"name" => $request["name"],
            		"type" => $request["type"],
            		"size" => $request["size"],
            		"position" => $request["position"],
            		"content" => $this->sanitize->htmlEncode($request["content"]),
            	];

        		if($this->system->update($request["id"], false, "widgets", $filtered)):
        			$this->cache->container("admin.widgets");
					$this->cache->clear();
					$this->cache->container("system.blocks");
					$this->cache->clear();
					$this->cache->container("system.modals");
					$this->cache->clear();

        			response(200, lang_response_widget_updated);
        		else:
        			response(500, lang_response_went_wrong);
        		endif;

				break;
			case "edit.page":
				if(!permission("manage_pages"))
					response(500, lang_response_no_permission);

	            if(!isset($request["name"], $request["roles"], $request["logged"], $request["content"]))
	            	response(500, lang_response_invalid);

	            if(!$this->sanitize->length($request["name"]))
					response(500, lang_response_name_short);

	            if(!is_array($request["roles"]))
	            	response(500, lang_response_invalid);

	            if(empty($request["roles"]))
	            	response(500, lang_response_invalid);

	            if(!$this->sanitize->isInt($request["logged"]))
					response(500, lang_response_invalid);

				if(!in_array($request["logged"], [1, 2]))
					response(500, lang_response_invalid);

            	$filtered = [
            		"slug" => $this->slug->create($request["name"]),
            		"logged" => $request["logged"],
            		"name" => $request["name"],
            		"roles" => implode(",", $request["roles"]),
            		"content" => $this->sanitize->htmlEncode($request["content"])
            	];

            	if($this->system->update($request["id"], false, "pages", $filtered)):
        			$this->cache->container("admin.pages");
					$this->cache->clear();

        			response(200, lang_response_page_updated);
        		else:
        			response(500, lang_response_went_wrong);
        		endif;

				break;
			case "edit.language":
				if(!permission("manage_languages"))
					response(500, lang_response_no_permission);

	            if(!isset($request["name"], $request["iso"], $request["translations"]))
	            	response(500, lang_response_invalid);

	            if(!$this->sanitize->length($request["name"]))
					response(500, lang_response_name_short);

	            if(!array_key_exists($request["iso"], \CountryCodes::get("alpha2", "country")))
	            	response(500, lang_response_invalid);

            	$filtered = [
            		"name" => $request["name"],
            		"iso" => strtoupper($request["iso"]),
            		"translations" => $request["translations"]
            	];

            	if($this->system->update($request["id"], false, "languages", $filtered)):
            		$this->cache->container("system.language.{$request["id"]}");
            		$this->cache->clear();
            		$this->cache->container("system.languages");
            		$this->cache->clear();
        			$this->cache->container("admin.languages");
					$this->cache->clear();

        			response(301, lang_response_language_updated);
        		else:
        			response(500, lang_response_went_wrong);
        		endif;

				break;
			case "admin.suspend":
				if(!permission("manage_users"))
					response(500, lang_response_no_permission);

				if($request["id"] < 2)
					response(500, lang_response_adminsuspend);

				if($this->system->checkUser($request["id"]) > 0):
					if($this->system->update($request["id"], false, "users", [
						"suspended" => 1
					])):
	        			$this->cache->container("admin.users");
						$this->cache->clear();

	        			response(200, lang_response_successsuspend);
	        		else:
	        			response(500, lang_response_went_wrong);
	        		endif;
				else:

				endif;

				break;
			case "admin.unsuspend":
				if(!permission("manage_users"))
					response(500, lang_response_no_permission);

				if($request["id"] < 2)
					response(500, lang_response_invalid);

				if($this->system->checkUser($request["id"]) > 0):
					if($this->system->update($request["id"], false, "users", [
						"suspended" => 0
					])):
	        			$this->cache->container("admin.users");
						$this->cache->clear();

	        			response(200, lang_response_successunsuspend);
	        		else:
	        			response(500, lang_response_went_wrong);
	        		endif;
				else:

				endif;

				break;
			case "admin.builder":
				if(!super_admin)
					response(500, lang_response_no_permission);

				if(!$this->sanitize->length($request["package_name"], 5))
					response(500, lang_response_builder_packagenameshort);

				if(!$this->sanitize->length($request["app_name"]))
					response(500, lang_response_builder_appnameshort);

				if(!$this->sanitize->isInt($request["app_send"]))
					response(500, lang_response_builder_invalidsend);

				if(!$this->sanitize->isInt($request["app_receive"]))
					response(500, lang_response_builder_invalidreceive);

	            foreach($request as $key => $value):
            		$this->system->settings($key, $value);
	            endforeach;

	            if(isset($_FILES["app_icon"])):
        			$this->upload->upload($_FILES["app_icon"]);
        			if($this->upload->uploaded):
        				$this->upload->allowed = [
        					"image/*"
        				];
		                $this->upload->file_new_name_body = "icon";
		                $this->upload->image_convert = "png";
		                $this->upload->file_overwrite = true;
		                $this->upload->process("uploads/builder/");

						if($this->upload->processed)
							$this->upload->clean();
						else
							response(500, lang_response_builder_appiconfail);
					endif;
	            endif;

	            if(isset($_FILES["app_splash"])):
        			$this->upload->upload($_FILES["app_splash"]);
        			if($this->upload->uploaded):
        				$this->upload->allowed = [
        					"image/*"
        				];
		                $this->upload->file_new_name_body = "splash";
		                $this->upload->image_convert = "png";
		                $this->upload->file_overwrite = true;
		                $this->upload->process("uploads/builder/");

						if($this->upload->processed)
							$this->upload->clean();
						else
							response(500, lang_response_builder_appsplashfail);
					endif;
	            endif;

	            if(isset($_FILES["app_logo"])):
        			$this->upload->upload($_FILES["app_logo"]);
        			if($this->upload->uploaded):
        				$this->upload->allowed = [
        					"image/*"
        				];
		                $this->upload->file_new_name_body = "logo";
		                $this->upload->image_convert = "png";
		                $this->upload->file_overwrite = true;
		                $this->upload->process("uploads/builder/");

						if($this->upload->processed)
							$this->upload->clean();
						else
							response(500, lang_response_builder_applogofail);
					endif;
	            endif;

	            $this->cache->container("system.settings");
	            $this->cache->clear();

    			response(200, lang_response_builder_settingsupdated);

				break;
			case "admin.theme":
				if(!super_admin)
					response(500, lang_response_no_permission);

				if(isset($_FILES["landing_img"])):
        			$this->upload->upload($_FILES["landing_img"]);
        			if($this->upload->uploaded):
        				$this->upload->allowed = [
        					"image/*"
        				];
		                $this->upload->file_new_name_body = "landing";
		                $this->upload->image_convert = "png";
		                $this->upload->file_overwrite = true;
		                $this->upload->process("uploads/theme/");

						if($this->upload->processed)
							$this->upload->clean();
						else
							response(500, lang_response_builder_applogofail);
					endif;
	            endif;

	            if(isset($_FILES["dashboard_img"])):
        			$this->upload->upload($_FILES["dashboard_img"]);
        			if($this->upload->uploaded):
        				$this->upload->allowed = [
        					"image/*"
        				];
		                $this->upload->file_new_name_body = "dashboard";
		                $this->upload->image_convert = "png";
		                $this->upload->file_overwrite = true;
		                $this->upload->process("uploads/theme/");

						if($this->upload->processed)
							$this->upload->clean();
						else
							response(500, lang_response_builder_applogofail);
					endif;
	            endif;

	            if(isset($_FILES["favicon_img"])):
        			$this->upload->upload($_FILES["favicon_img"]);
        			if($this->upload->uploaded):
        				$this->upload->allowed = [
        					"image/*"
        				];
		                $this->upload->file_new_name_body = "favicon";
		                $this->upload->image_convert = "png";
		                $this->upload->image_resize = true;
						$this->upload->image_x = 50;
						$this->upload->image_ratio_y = true;
		                $this->upload->file_overwrite = true;
		                $this->upload->process("uploads/theme/");

						if($this->upload->processed)
							$this->upload->clean();
						else
							response(500, lang_response_builder_applogofail);
					endif;
	            endif;

				try {
	            	$this->scss->setVariables([
					    "theme" => $request["theme_background"],
					    "themeText" => $request["theme_highlight"]
					]);
					
					$this->scss->setFormatter("ScssPhp\ScssPhp\Formatter\Crunched");
					$this->scss->setImportPaths("templates/_scss/");
					$this->file->put("templates/dashboard/assets/css/style.min.css", $this->scss->compile("@import 'dashboard.scss';"));
					$this->file->put("templates/default/assets/css/style.min.css", $this->scss->compile("@import 'default.scss';"));
	            } catch(Exception $e){
	            	response(500, lang_response_went_wrong);
	            }

	            foreach($request as $key => $value):
            		$this->system->settings($key, $value);
	            endforeach;

	            $this->cache->container("system.settings");
	            $this->cache->clear();

    			response(301, lang_response_theme_updated);

				break;
			case "admin.settings":
				if(!super_admin)
					response(500, lang_response_no_permission);

				if(empty($request["providers"]))
					response(500, lang_response_leastprovider);

				if(!is_array($request["providers"]))
					response(500, lang_response_invalid);

				foreach($request["providers"] as $provider):
					if(!in_array($provider, ["paypal", "stripe", "mollie"]))
						response(500, lang_response_invalid);
				endforeach;

				if(!in_array($request["currency"], [
					"usd",
					"eur",
					"gbp",
					"aud",
					"cad",
					"hkd",
					"jpy",
					"rub",
					"sgd"
				]))
					response(500, lang_response_invalid);

				$request["providers"] = implode(",", $request["providers"]);

	            foreach($request as $key => $value):
            		$this->system->settings($key, $value);
	            endforeach;

	            $this->cache->container("system.verify");
	            $this->cache->clear();
	            $this->cache->container("system.settings");
	            $this->cache->clear();

    			response(200, lang_response_system_settingsupdated);

				break;
        	default:
        		response(500, lang_response_invalid);
        endswitch;
	}

	public function delete()
	{
		$this->header->allow(site_url);

		$this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSystemSettings());
        endif;

        set_system($this->cache->getAll());

        set_logged($this->session->get("logged"));

        $this->cache->container("system.language." . logged_language);

        if($this->cache->empty()):
            $this->cache->setRaw($this->system->getTranslations(logged_language));
        endif;

        set_language($this->cache->getRaw());

        if(!$this->session->has("logged"))
            response(302, lang_response_session_false);

        $type = $this->sanitize->string($this->url->segment(4));
        $id = $this->sanitize->string($this->url->segment(5));

        if(!$this->sanitize->isInt($id))
        	response(500, lang_response_invalid);

		switch($type):
			case "sent":
				$message = $this->system->getMessageSent(logged_id, $id);

				if($this->system->delete(logged_id, $id, "sent")):
					$this->cache->container("gateway.{$message["did"]}." . logged_hash);
					if($this->cache->has($message["id"]))
						$this->cache->delete($message["id"]);
					$this->cache->container("user." . logged_hash);
					$this->cache->clear();
					$this->cache->container("messages." . logged_hash);
					$this->cache->clear();

					$vars = [
						"message" => lang_response_deleted_sent,
						"table" => "messages.sent"
					];
				else:
					response(500, lang_response_went_wrong);
				endif;

				break;
			case "received":
				if($this->system->delete(logged_id, $id, "received")):
					$this->cache->container("user." . logged_hash);
					$this->cache->clear();
					$this->cache->container("messages." . logged_hash);
					$this->cache->clear();

					$vars = [
						"message" => lang_response_deleted_received,
						"table" => "messages.received"
					];
				else:
					response(500, lang_response_went_wrong);
				endif;

				break;
			case "scheduled":
				if($this->system->delete(logged_id, $id, "scheduled")):
					$this->cache->container("user." . logged_hash);
					$this->cache->clear();
					$this->cache->container("messages." . logged_hash);
					$this->cache->clear();

					$vars = [
						"message" => lang_response_scheduled_deleted,
						"table" => "messages.scheduled"
					];
				else:
					response(500, lang_response_went_wrong);
				endif;

				break;
			case "templates":
				if($this->system->delete(logged_id, $id, "templates")):
					$this->cache->container("messages." . logged_hash);
					$this->cache->clear();

					$vars = [
						"message" => lang_response_deleted_template,
						"table" => "messages.templates"
					];
				else:
					response(500, lang_response_went_wrong);
				endif;

				break;
			case "contacts":
				if($this->system->delete(logged_id, $id, "contacts")):
					$this->cache->container("user." . logged_hash);
					$this->cache->clear();
					$this->cache->container("autocomplete.contacts" . logged_hash);
					$this->cache->clear();
					$this->cache->container("contacts." . logged_hash);
					$this->cache->clear();
					$this->cache->container("api.contacts." . logged_hash);
					$this->cache->clear();

					$vars = [
						"message" => lang_response_deleted_contact,
						"table" => "contacts.saved"
					];
				else:
					response(500, lang_response_went_wrong);
				endif;

				break;
			case "groups":
				if($this->system->delete(logged_id, $id, "groups")):
					$this->cache->container("contacts." . logged_hash);
					$this->cache->clear();
					$this->cache->container("api.groups." . logged_hash);
					$this->cache->clear();

					$vars = [
						"message" => lang_response_deleted_group,
						"table" => "contacts.groups"
					];
				else:
					response(500, lang_response_went_wrong);
				endif;

				break;
			case "devices":
				if($this->system->delete(logged_id, $id, "devices")):
					$this->cache->container("devices." . logged_hash);
					$this->cache->clear();
					$this->cache->container("user." . logged_hash);
					$this->cache->clear();
					$this->cache->container("gateway." . logged_hash);
					$this->cache->clear();
					$this->cache->container("api.devices." . logged_hash);
					$this->cache->clear();

					$vars = [
						"message" => lang_response_deleted_device,
						"table" => "devices.registered"
					];
				else:
					response(500, lang_response_went_wrong);
				endif;

				break;
			case "keys":
				if($this->system->delete(logged_id, $id, "keys")):
					$this->cache->container("user." . logged_hash);
					$this->cache->clear();
					$this->cache->container("tools." . logged_hash);
					$this->cache->clear();
					$this->cache->container("api.keys");
					$this->cache->clear();

					$vars = [
						"message" => lang_response_deleted_key,
						"table" => "tools.keys"
					];
				else:
					response(500, lang_response_went_wrong);
				endif;

				break;
			case "webhooks":
				if($this->system->delete(logged_id, $id, "webhooks")):
					$this->cache->container("gateway." . logged_hash);
					$this->cache->clear();
					$this->cache->container("user." . logged_hash);
					$this->cache->clear();
					$this->cache->container("tools." . logged_hash);
					$this->cache->clear();

					$vars = [
						"message" => lang_response_deleted_hook,
						"table" => "tools.webhooks"
					];
				else:
					response(500, lang_response_went_wrong);
				endif;

				break;
			case "actions":
				if($this->system->delete(logged_id, $id, "actions")):
					$this->cache->container("gateway." . logged_hash);
					$this->cache->clear();
					$this->cache->container("tools." . logged_hash);
					$this->cache->clear();

					$vars = [
						"message" => lang_response_action_deleted,
						"table" => "tools.actions"
					];
				else:
					response(500, lang_response_went_wrong);
				endif;

				break;
			case "users":
				if(!permission("manage_users"))
					response(500, lang_response_no_permission);

				if($id < 2):
					if(super_admin)
						response(500, lang_response_deleted_defaultuserfalse);
					else
						response(500, lang_response_no_permission);
				else:
					$hash = md5($id);
				endif;

				if($this->system->delete(false, $id, "users")):
					/**
					 * Purge user data
					 */

					$this->cache->container("messages.{$hash}");
					$this->cache->clear();
					$this->cache->container("devices.{$hash}");
					$this->cache->clear();
					$this->cache->container("contacts.{$hash}");
					$this->cache->clear();
					$this->cache->container("dashboard.{$hash}");
					$this->cache->clear();
					$this->cache->container("tools.{$hash}");
					$this->cache->clear();

					/**
					 * Purge admin data
					 */

					$this->cache->container("admin.statistics");
					$this->cache->clear();
					$this->cache->container("admin.users");
					$this->cache->clear();
					$this->cache->container("admin.subscriptions");
					$this->cache->clear();

					$vars = [
						"message" => lang_response_deleted_user,
						"table" => "administration.users"
					];
				else:
					response(500, lang_response_went_wrong);
				endif;

				break;
			case "roles":
				if(!super_admin)
					response(500, lang_response_no_permission);

				if($id < 2)
					response(500, lang_role_default_delete);

				if($this->system->delete(false, $id, "roles")):
					$this->cache->container("admin.roles");
					$this->cache->clear();

					$vars = [
						"message" => lang_role_deleted,
						"table" => "administration.roles"
					];
				else:
					response(500, lang_response_went_wrong);
				endif;

				break;
			case "packages":
				if(!permission("manage_packages"))
					response(500, lang_response_no_permission);

				if($id < 2)
					response(500, lang_response_deleted_defaultpackagefalse);

				if($this->system->delete(false, $id, "packages")):
					$this->cache->container("system.packages");
            		$this->cache->clear();
					$this->cache->container("admin.packages");
					$this->cache->clear();

					$vars = [
						"message" => lang_response_deleted_package,
						"table" => "administration.packages"
					];
				else:
					response(500, lang_response_went_wrong);
				endif;

				break;
			case "vouchers":
				if(!permission("manage_vouchers"))
					response(500, lang_response_no_permission);

				if($this->system->delete(false, $id, "vouchers")):
					$this->cache->container("admin.vouchers");
					$this->cache->clear();

					$vars = [
						"message" => lang_voucher_deleted,
						"table" => "administration.vouchers"
					];
				else:
					response(500, lang_response_went_wrong);
				endif;

				break;
			case "subscriptions":
				if(!permission("manage_subscriptions"))
					response(500, lang_response_no_permission);

				$subscription = $this->system->getSubscription($id);

				if($this->system->delete(false, $id, "subscriptions")):
					$this->cache->container("user." . md5($subscription["uid"]));
					$this->cache->clear();
					$this->cache->container("user.subscription." . md5($subscription["uid"]));
					$this->cache->clear();
					$this->cache->container("admin.subscriptions");
					$this->cache->clear();

					$vars = [
						"message" => lang_response_deleted_subscription,
						"table" => "administration.subscriptions"
					];
				else:
					response(500, lang_response_went_wrong);
				endif;

				break;
			case "widgets":
				if(!permission("manage_widgets"))
					response(500, lang_response_no_permission);

				if($this->system->delete(false, $id, "widgets")):
					$this->cache->container("admin.widgets");
					$this->cache->clear();
					$this->cache->container("system.blocks");
					$this->cache->clear();
					$this->cache->container("system.modals");
					$this->cache->clear();

					$vars = [
						"message" => lang_response_deleted_widget,
						"table" => "administration.widgets"
					];
				else:
					response(500, lang_response_went_wrong);
				endif;

				break;
			case "pages":
				if(!permission("manage_pages"))
					response(500, lang_response_no_permission);

				if($this->system->delete(false, $id, "pages")):
					$this->cache->container("admin.pages");
					$this->cache->clear();

					$vars = [
						"message" => lang_response_page_deleted,
						"table" => "administration.pages"
					];
				else:
					response(500, lang_response_went_wrong);
				endif;

				break;
			case "languages":
				if(!permission("manage_languages"))
					response(500, lang_response_no_permission);

				if($id < 2)
					response(500, lang_response_deleted_defaultlangfalse);
				
				if($this->system->delete(false, $id, "languages")):
					$this->cache->container("system.languages");
					$this->cache->clear();
					$this->cache->container("admin.languages");
					$this->cache->clear();

					$vars = [
						"message" => lang_response_deleted_language,
						"table" => "administration.languages"
					];
				else:
					response(500, lang_response_went_wrong);
				endif;

				break;
			default:
				response(500, lang_response_invalid);
		endswitch;

		response(200, $vars["message"], [
			"vars" => [
				"table" => $vars["table"]
			]
		]);
	}
}