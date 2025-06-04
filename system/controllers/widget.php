<?php
/**
 * @controller Widget
 */

class Widget_Controller extends MVC_Controller
{
	public function index()
	{
		$this->header->allow(site_url);
        
        $type = $this->sanitize->string($this->url->segment(3));
        set_template($this->sanitize->string($this->url->segment(4)));

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

        $this->cache->container("system.blocks");

        if($this->cache->empty()):
            foreach($this->system->getBlocks() as $key => $value):
                $blocks[$key] = $this->smarty->fetch("string: {$this->sanitize->htmlDecode($value)}");
            endforeach;
            $this->cache->setArray($blocks);
        endif;

        set_blocks($this->cache->getAll());

		/**
		 * @type Modals
		 * @desc Modal template processor
		 */

		if($type == "modal"):

			$tpl = $this->sanitize->string($this->url->segment(5));
			$id = $this->sanitize->string($this->url->segment(6));

			if(Stringy\create($tpl)->contains("smspilot.")):
				if(!in_array($tpl, ["smspilot.languages"])):
					if(!in_array($tpl, ["smspilot.login", "smspilot.forgot", "smspilot.register", "smspilot.api"])):
						if(!$this->session->has("logged")):
		            		response(302, lang_response_session_false);
		            	endif;
					else:
						if($this->session->has("logged")):
		            		response(302, lang_response_session_true);
		            	endif;
					endif;
				endif;

				$tpl = (string) Stringy\create($tpl)->removeLeft("smspilot.");

				if(!$this->smarty->templateExists(template . "/widgets/modals/{$tpl}.tpl")):
		        	response(500, lang_response_invalid);
				endif;
			else:
	            $this->cache->container("system.modals");

	            if(!$this->cache->has($tpl)):
	            	if($this->widget->checkModal($tpl) > 0)
	            		$modal = $this->widget->getModal($tpl);
	            	else
	            		response(500, lang_response_invalid);

	            	$this->cache->set($tpl, $modal);
	            else:
	            	$modal = $this->cache->get($tpl);
	            endif;
			endif;

			switch($tpl):
				case "login":
					$vars = [
						"template" => [
							"title" => lang_modal_login_title
						],
						"handler" => [
							"tpl" => $tpl,
							"type" => "index",
							"position" => "right",
							"require" => "email|" . lang_require_email . "<=>password|" . lang_require_password
						]
					];
					
					break;
				case "forgot":
					$vars = [
						"template" => [
							"title" => lang_modal_forgot_title
						],
						"handler" => [
							"tpl" => $tpl,
							"type" => "index",
							"recaptcha" => empty(system_recaptcha_key) || empty(system_recaptcha_secret) ? false : true,
							"position" => "right",
							"require" => "email|" . lang_require_email
						]
					];
					
					break;
				case "register":
					if(system_registrations > 1)
	            		response(500, lang_response_register_false);

					$vars = [
						"template" => [
							"title" => lang_modal_register_title
						],
						"handler" => [
							"tpl" => $tpl,
							"type" => "index",
							"recaptcha" => empty(system_recaptcha_key) || empty(system_recaptcha_secret) ? false : true,
							"position" => "right",
							"require" => "name|" . lang_require_name . "<=>email|" . lang_require_email . "<=>password|" . lang_require_password . "<=>cpassword|" . lang_require_cpassword
						]
					];
					
					break;
				case "api":
					$vars = [
						"template" => [
							"title" => lang_modal_apiguide_title
						],
						"handler" => [
							"size" => "xl",
							"iframe" => true
						]
					];
					
					break;
				case "languages":
					$this->cache->container("system.languages");

			        if($this->cache->empty()):
			            $this->cache->setArray($this->system->getLanguages());
			        endif;

					$vars = [
						"template" => [
							"title" => lang_widget_alllang_title,
							"data" => [
								"languages" => $this->cache->getAll()
							]
						],
						"handler" => [
							"size" => "md"
						]
					];
					
					break;
				case "user.settings":
					try {
						$user = $this->widget->getUser(logged_id);
					} catch(Exception $e){
						response(500, lang_response_invalid);
					}

					$vars = [
						"template" => [
							"title" => lang_modal_usersettings_title,
							"data" => [
								"user" => $user
							]
						],
						"handler" => [
							"id" => logged_id,
							"tpl" => $tpl,
							"type" => "update",
							"position" => "right",
							"require" => "name|" . lang_require_name . "<=>email|" . lang_require_email
						]
					];
					
					break;
				case "user.subscription":
					$this->cache->container("user." . logged_hash);

	                if(!$this->cache->has("subscription")):
	                    $this->cache->set("subscription", [
	                        "used" => [
	                            "messages" => $this->system->getMessageQuota(logged_id),
	                            "contacts" => $this->system->getTotalContacts(logged_id),
	                            "devices" => $this->system->getTotalDevices(logged_id),
	                            "keys" => $this->system->getTotalKeys(logged_id),
	                            "webhooks" => $this->system->getTotalWebhooks(logged_id)
	                        ],
	                        "package" => ($this->system->getSubscriptionByUserId(logged_id) ?: $this->system->getPackageDefault())
	                    ]);
	                endif;

					$vars = [
						"template" => [
							"title" => lang_modal_subscription_title,
							"data" => [
								"subscription" => $this->cache->get("subscription")
							]
						],
						"handler" => [
							"size" => "md"
						]
					];
					
					break;
				case "providers":
					if(!$this->sanitize->isInt($id))
						response(500, lang_response_invalid);

					foreach(["paypal", "stripe", "mollie"] as $provider):
						if(in_array($provider, explode(",", system_providers)))
							$providers[$provider] = true;
						else
							$providers[$provider] = false;
					endforeach;

					$vars = [
						"template" => [
							"title" => lang_payment_provider,
							"data" => [
								"package" => [
									"id" => $id
								],
								"providers" => $providers
							]
						],
						"handler" => [
							"tpl" => $tpl
						]
					];
					
					break;
				case "payment":
					if(!$this->sanitize->isInt($id))
						response(500, lang_response_invalid);

					$provider = $this->sanitize->string($this->url->segment(7));

					if(!in_array($provider, [
						"paypal",
						"stripe",
						"mollie",
						"voucher"
					]))
						response(500, lang_response_invalid);

					$vars = [
						"template" => [
							"title" => lang_modal_purchase_title,
							"data" => [
								"provider" => $provider
							]
						],
						"handler" => [
							"id" => $id,
							"tpl" => $tpl,
							"size" => "md",
							"type" => "update",
						]
					];

					switch($provider):
						case "stripe":
							$tpl = "$tpl.stripe";
							$vars["handler"]["stripe"] = true;

							break;
						case "voucher":
							$tpl = "$tpl.voucher";
							$vars["handler"]["require"] = "code|" . lang_require_vouchercode;

							break;
						default:
							$vars["handler"]["require"] = "number|" . lang_require_cardnumber . "<=>expiry|" . lang_require_cardexpiry . "<=>name|" . lang_require_cardname . "<=>cvc|" . lang_require_cardcvc;
					endswitch;
					
					break;
				case "packages":
					$vars = [
						"template" => [
							"title" => lang_modal_packages_title,
							"data" => [
								"packages" => $this->widget->getPackages()
							]
						],
						"handler" => [
							"size" => "xl",
							"position" => "right"
						]
					];
					
					break;
				case "view":
					$type = explode("-", $id);

					if(count($type) < 2)
						response(500, lang_response_invalid);

					if(!$this->sanitize->isInt($type[1]))
						response(500, lang_response_invalid);

					switch($type[0]):
						case "tooltips":
							switch($type[1]):
								case 1:
									$content = lang_tooltips_viewfirst;

									break;
								case 2:
									$content = lang_tooltips_viewsecond;

									break;
								case 3:
									$content = lang_tooltips_viewthird;

									break;
								case 4:
									$content = lang_tooltips_viewfourth;

									break;
								default:
									response(500, lang_response_invalid);
							endswitch;

							break;
						case "sent":
							$content = $this->widget->getContent($type[1], "sent", "message");
							break;
						case "received":
							$content = $this->widget->getContent($type[1], "received", "message");
							break;
						case "scheduled":
							$content = $this->widget->getContent($type[1], "scheduled", "message");
							break;
						case "scheduledrecipients":
							$groups = $this->widget->getContent($type[1], "scheduled", "groups");
							$numbers = $this->widget->getContent($type[1], "scheduled", "numbers");

							foreach(explode(",", $groups) as $group):
								$cgroups[] = " Group #{$group}";
							endforeach;

							$content = implode(",", $cgroups) . (empty($numbers) ? false : ", " . $numbers);
							break;
						case "templates":
							$content = $this->widget->getContent($type[1], "templates", "format");
							break;
						case "excel":
							if($type[1] < 2):
								$content = <<<HTML
<strong>Excel SMS Sending Format</strong>
<table class="table table-bordered">
  <tbody>
    <tr>
      <th>639123456788</th>
      <td>1</td>
      <td>1</td>
      <td>Message</td>
    </tr>
    <tr>
      <th>639123456722</th>
      <td>1</td>
      <td>0</td>
      <td>Message2</td>
    </tr>
    <tr>
      <th>639123456711</th>
      <td>2</td>
      <td>0</td>
      <td>Message Testing</td>
    </tr>
  </tbody>
</table>
<strong>Columns:</strong>
1. The E.164 formatted contact number without "+" sign
2. SIM slot number (1 or 2)
3. Priority sending (1 or 0)
4. Message to send
HTML;
							else:
								$content = <<<HTML
<strong>Excel Contact Import Format</strong>
<table class="table table-bordered">
  <tbody>
    <tr>
      <th>639123456788</th>
      <td>Contact Name</td>
      <td>1</td>
    </tr>
    <tr>
      <th>639123456744</th>
      <td>Contact Name2</td>
      <td>3</td>
    </tr>
  </tbody>
</table>
<strong>Columns:</strong>
1. The E.164 formatted contact number without "+" sign
2. The contact name
3. The group ID to assign this contact, you can get group id's in the groups page
HTML;
							endif;
							break;
						default:
							response(500, lang_response_invalid);
					endswitch;

					$vars = [
						"template" => [
							"data" => [
								"content" => $content
							]
						],
						"handler" => [
							"size" => "md"
						]
					];

					break;
				case "sms.quick":
					$vars = [
						"template" => [
							"title" => lang_modal_smsquick_title,
							"data" => [
								"devices" => $this->widget->getDevices(logged_id)
							]
						],
						"handler" => [
							"tpl" => $tpl,
							"type" => "create",
							"position" => "right",
							"table" => "messages.sent",
							"require" => "phone|" . lang_require_phone . "<=>device|" . lang_require_device ."<=>sim|" . lang_require_sim . "<=>priority|" . lang_require_priority . "<=>message|" . lang_require_message
						]
					];
					
					break;
				case "sms.bulk":
					$vars = [
						"template" => [
							"title" => lang_modal_smsbulk_title,
							"data" => [
								"groups" => $this->widget->getGroups(logged_id),
								"devices" => $this->widget->getDevices(logged_id),
								"templates" => $this->widget->getTemplates(logged_id)
							]
						],
						"handler" => [
							"tpl" => $tpl,
							"size" => "lg",
							"type" => "create",
							"table" => "messages.sent",
							"require" => "groups|" . lang_require_groups . "<=>device|" . lang_require_device . "<=>sim|" . lang_require_sim . "<=>priority|" . lang_require_priority . "<=>message|" . lang_require_message
						]
					];
					
					break;
				case "sms.excel":
					$vars = [
						"template" => [
							"title" => lang_widget_smsexcel_title,
							"data" => [
								"devices" => $this->widget->getDevices(logged_id)
							]
						],
						"handler" => [
							"tpl" => $tpl,
							"size" => "md",
							"type" => "create",
							"table" => "messages.sent",
							"require" => "device|" . lang_require_device
						]
					];
					
					break;
				case "sms.scheduled":
					$vars = [
						"template" => [
							"title" => lang_form_scheduled_title,
							"data" => [
								"groups" => $this->widget->getGroups(logged_id),
								"devices" => $this->widget->getDevices(logged_id),
								"templates" => $this->widget->getTemplates(logged_id)
							]
						],
						"handler" => [
							"tpl" => $tpl,
							"size" => "md",
							"type" => "create",
							"position" => "right",
							"table" => "messages.scheduled",
							"require" => "name|" . lang_require_scheduled_name . "<=>groups|" . lang_require_groups . "<=>device|" . lang_require_device . "<=>sim|" . lang_require_sim . "<=>schedule|" . lang_require_scheduled_date . "<=>message|" . lang_require_message
						]
					];
					
					break;
				case "history.sent":
					$vars = [
						"template" => [
							"title" => lang_modal_findsent_title,
							"data" => [
								"devices" => $this->widget->getDevices(logged_id)
							]
						],
						"handler" => [
							"tpl" => $tpl,
							"type" => "history",
							"position" => "right",
							"table" => "messages.sent",
							"require" => "date|" . lang_require_date ."<=>sim|" . lang_require_sim . "<=>priority|" . lang_require_priority . "<=>api|" . lang_require_api . "<=>device|" . lang_require_device
						]
					];
					
					break;
				case "history.received":
					$vars = [
						"template" => [
							"title" => lang_modal_findreceived_title,
							"data" => [
								"devices" => $this->widget->getDevices(logged_id)
							]
						],
						"handler" => [
							"tpl" => $tpl,
							"type" => "history",
							"position" => "right",
							"table" => "messages.received",
							"require" => "date|" . lang_require_date ."<=>device|" . lang_require_device
						]
					];
					
					break;
				case "add.template":
					$vars = [
						"template" => [
							"title" => lang_modal_addtemplate_title,
						],
						"handler" => [
							"tpl" => $tpl,
							"size" => "md",
							"type" => "create",
							"table" => "messages.templates",
							"require" => "name|" . lang_require_templatename . "<=>format|" . lang_require_templateformat
						]
					];
					
					break;
				case "edit.template":
					if(!$this->sanitize->isInt($id))
						response(500, lang_response_invalid);

					try {
						$template_ = $this->widget->getTemplate($id);
					} catch(Exception $e){
						response(500, lang_response_invalid);
					}

					$vars = [
						"template" => [
							"title" => lang_modal_edittemplate_title,
							"data" => [
								"template" => $template_
							]
						],
						"handler" => [
							"id" => $id,
							"tpl" => $tpl,
							"size" => "md",
							"type" => "update",
							"table" => "messages.templates",
							"require" => "name|" . lang_require_templatename . "<=>format|" . lang_require_templateformat
						]
					];
					
					break;
				case "import.contacts":
					$vars = [
						"template" => [
							"title" => lang_widget_importcontacts_title
						],
						"handler" => [
							"tpl" => $tpl,
							"size" => "md",
							"type" => "create",
							"table" => "contacts.saved"
						]
					];
					
					break;
				case "add.contact":
					$vars = [
						"template" => [
							"title" => lang_modal_addcontact_title,
							"data" => [
								"groups" => $this->widget->getGroups(logged_id)
							]
						],
						"handler" => [
							"tpl" => $tpl,
							"type" => "create",
							"position" => "right",
							"table" => "contacts.saved",
							"require" => "name|" . lang_require_contactname . "<=>phone|" . lang_require_phone . "<=>group|" . lang_require_group
						]
					];
					
					break;
				case "edit.contact":
					if(!$this->sanitize->isInt($id))
						response(500, lang_response_invalid);

					try {
						$contact = $this->widget->getContact($id);
					} catch(Exception $e){
						response(500, lang_response_invalid);
					}

					$vars = [
						"template" => [
							"title" => lang_modal_editcontact_title,
							"data" => [
								"contact" => $contact,
								"groups" => $this->widget->getGroups(logged_id)
							]
						],
						"handler" => [
							"id" => $id,
							"tpl" => $tpl,
							"type" => "update",
							"position" => "right",
							"table" => "contacts.saved",
							"require" => "name|" . lang_require_contactname . "<=>phone|" . lang_require_phone . "<=>group|" . lang_require_group
						]
					];
					
					break;
				case "add.group":
					$vars = [
						"template" => [
							"title" => lang_modal_addgroup_title
						],
						"handler" => [
							"tpl" => $tpl,
							"type" => "create",
							"position" => "right",
							"table" => "contacts.groups",
							"require" => "name|" . lang_require_groupname
						]
					];
					
					break;
				case "edit.group":
					if(!$this->sanitize->isInt($id))
						response(500, lang_response_invalid);

					try {
						$group = $this->widget->getGroup($id);
					} catch(Exception $e){
						response(500, lang_response_invalid);
					}

					$vars = [
						"template" => [
							"title" => lang_modal_editgroup_title,
							"data" => [
								"group" => $group
							]
						],
						"handler" => [
							"id" => $id,
							"tpl" => $tpl,
							"type" => "update",
							"position" => "right",
							"table" => "contacts.groups",
							"require" => "name|" . lang_require_groupname
						]
					];
					
					break;
				case "add.device":
					$vars = [
						"template" => [
							"title" => lang_modal_adddevice_title,
							"data" => [
								"hash" => $this->hash->encode(logged_id, system_token)
							]
						],
						"handler" => [
							"tpl" => $tpl,
							"position" => "right"
						]
					];
					
					break;
				case "add.apikey":
					$vars = [
						"template" => [
							"title" => lang_modal_addkey_title,
							"data" => [
								"devices" => $this->widget->getDevices(logged_id)
							]
						],
						"handler" => [
							"tpl" => $tpl,
							"type" => "create",
							"position" => "right",
							"table" => "tools.keys",
							"require" => "name|" . lang_require_apiname . "<=>devices|" . lang_require_devices . "<=>permissions|" . lang_require_permissions
						]
					];
					
					break;
				case "edit.apikey":
					if(!$this->sanitize->isInt($id))
						response(500, lang_response_invalid);

					try {
						$key = $this->widget->getKey($id);
					} catch(Exception $e){
						response(500, lang_response_invalid);
					}

					if($key["devices"] == "0")
						$automatic = true;
					else
						$automatic = false;

					$kdevices = explode(",", $key["devices"]);
					$udevices = $this->widget->getDevices(logged_id);

					if(count($udevices) > 0):
						foreach($udevices as $did => $value):
							if(in_array($did, $kdevices)):
								$devices[$did] = [
									"name" => $value["name"],
									"token" => $value["token"],
									"selected" => true
								];
							else:
								$devices[$did] = [
									"name" => $value["name"],
									"token" => $value["token"],
									"selected" => false
								];
							endif;
						endforeach;
					else:
						$devices = [];
					endif;

					$kpermissions = explode(",", $key["permissions"]);
					$spermissions = [
        				"get_pending" => "get_pending",
        				"get_received" => "get_received",
        				"get_sent" => "get_sent",
        				"send" => "send",
        				"get_contacts" => "get_contacts",
        				"get_groups" => "get_groups",
        				"create_contact" => "create_contact",
        				"create_group" => "create_group",
        				"delete_contact" => "delete_contact",
        				"delete_group" => "delete_group",
        				"get_device" => "get_device",
        				"get_devices" => "get_devices"
        			];

        			foreach($spermissions as $permission => $name):
						if(in_array($permission, $kpermissions)):
							$permissions[$permission] = [
								"name" => $name,
								"token" => strtolower($name),
								"selected" => true
							];
						else:
							$permissions[$permission] = [
								"name" => $name,
								"token" => strtolower($name),
								"selected" => false
							];
						endif;
					endforeach;

					$vars = [
						"template" => [
							"title" => lang_modal_editkey_title,
							"data" => [
								"key" => $key,
								"devices" => $devices,
								"permissions" => $permissions,
								"automatic" => $automatic
							]
						],
						"handler" => [
							"id" => $id,
							"tpl" => $tpl,
							"type" => "update",
							"position" => "right",
							"table" => "tools.keys",
							"require" => "name|" . lang_require_apiname . "<=>devices|" . lang_require_devices . "<=>permissions|" . lang_require_permissions
						]
					];
					
					break;
				case "add.webhook":
					$vars = [
						"template" => [
							"title" => "Add Webhook",
							"data" => [
								"devices" => $this->widget->getDevices(logged_id)
							]
						],
						"handler" => [
							"tpl" => $tpl,
							"type" => "create",
							"position" => "right",
							"table" => "tools.webhooks",
							"require" => "name|" . lang_require_hookname . "<=>url|" . lang_require_hookurl . "<=>devices|" .lang_require_devices
						]
					];
					
					break;
				case "edit.webhook":
					if(!$this->sanitize->isInt($id))
						response(500, lang_response_invalid);

					try {
						$webhook = $this->widget->getWebhook($id);
					} catch(Exception $e){
						response(500, lang_response_invalid);
					}

					if($webhook["devices"] == "0"):
						$automatic = true;
					else:
						$automatic = false;
					endif;

					$wdevices = explode(",", $webhook["devices"]);
					$udevices = $this->widget->getDevices(logged_id);

					if(count($udevices) > 0):
						foreach($udevices as $did => $value):
							if(in_array($did, $wdevices)):
								$devices[$did] = [
									"name" => $value["name"],
									"token" => $value["token"],
									"selected" => true
								];
							else:
								$devices[$did] = [
									"name" => $value["name"],
									"token" => $value["token"],
									"selected" => false
								];
							endif;
						endforeach;
					else:
						$devices = [];
					endif;

					$vars = [
						"template" => [
							"title" => lang_modal_edithook_title,
							"data" => [
								"webhook" => $webhook,
								"devices" => $devices,
								"automatic" => $automatic
							]
						],
						"handler" => [
							"id" => $id,
							"tpl" => $tpl,
							"type" => "update",
							"position" => "right",
							"table" => "tools.webhooks",
							"require" => "name|" . lang_require_hookname . "<=>url|" . lang_require_hookurl . "<=>devices|" .lang_require_devices
						]
					];
					
					break;
				case "add.hook":
					$vars = [
						"template" => [
							"title" => lang_form_hook_addtitle,
							"data" => [
								"devices" => $this->widget->getDevices(logged_id)
							]
						],
						"handler" => [
							"tpl" => $tpl,
							"size" => "md",
							"type" => "create",
							"position" => "right",
							"table" => "tools.actions",
							"require" => "name|" . lang_require_action_name . "<=>devices|" . lang_require_devices . "<=>event|" . lang_require_action_event . "<=>link|" . lang_require_action_link
						]
					];
					
					break;
				case "edit.hook":
					if(!$this->sanitize->isInt($id))
						response(500, lang_response_invalid);

					try {
						$hook = $this->widget->getAction($id);
					} catch(Exception $e){
						response(500, lang_response_invalid);
					}

					if($hook["devices"] == "0"):
						$automatic = true;
					else:
						$automatic = false;
					endif;

					$hdevices = explode(",", $hook["devices"]);
					$udevices = $this->widget->getDevices(logged_id);

					if(count($udevices) > 0):
						foreach($udevices as $did => $value):
							if(in_array($did, $hdevices)):
								$devices[$did] = [
									"name" => $value["name"],
									"token" => $value["token"],
									"selected" => true
								];
							else:
								$devices[$did] = [
									"name" => $value["name"],
									"token" => $value["token"],
									"selected" => false
								];
							endif;
						endforeach;
					else:
						$devices = [];
					endif;

					$vars = [
						"template" => [
							"title" => lang_form_hook_edittitle,
							"data" => [
								"hook" => $hook,
								"devices" => $devices,
								"automatic" => $automatic
							]
						],
						"handler" => [
							"id" => $id,
							"tpl" => $tpl,
							"size" => "md",
							"type" => "update",
							"position" => "right",
							"table" => "tools.actions",
							"require" => "name|" . lang_require_action_name . "<=>devices|" . lang_require_devices . "<=>event|" . lang_require_action_event . "<=>link|" . lang_require_action_link
						]
					];
					
					break;
				case "add.autoreply":
					$vars = [
						"template" => [
							"title" => lang_form_autoreply_addtitle,
							"data" => [
								"devices" => $this->widget->getDevices(logged_id)
							]
						],
						"handler" => [
							"tpl" => $tpl,
							"size" => "md",
							"type" => "create",
							"position" => "right",
							"table" => "tools.actions",
							"require" => "name|" . lang_require_action_name . "<=>devices|" . lang_require_devices . "<=>keywords|" . lang_require_action_keywords . "<=>message|" . lang_require_action_message
						]
					];
					
					break;
				case "edit.autoreply":
					if(!$this->sanitize->isInt($id))
						response(500, lang_response_invalid);

					try {
						$autoreply = $this->widget->getAction($id);
					} catch(Exception $e){
						response(500, lang_response_invalid);
					}

					if($autoreply["devices"] == "0"):
						$automatic = true;
					else:
						$automatic = false;
					endif;

					$adevices = explode(",", $autoreply["devices"]);
					$udevices = $this->widget->getDevices(logged_id);

					if(count($udevices) > 0):
						foreach($udevices as $did => $value):
							if(in_array($did, $adevices)):
								$devices[$did] = [
									"name" => $value["name"],
									"token" => $value["token"],
									"selected" => true
								];
							else:
								$devices[$did] = [
									"name" => $value["name"],
									"token" => $value["token"],
									"selected" => false
								];
							endif;
						endforeach;
					else:
						$devices = [];
					endif;

					$vars = [
						"template" => [
							"title" => lang_form_autoreply_edittitle,
							"data" => [
								"autoreply" => $autoreply,
								"devices" => $devices,
								"automatic" => $automatic
							]
						],
						"handler" => [
							"id" => $id,
							"tpl" => $tpl,
							"size" => "md",
							"type" => "update",
							"position" => "right",
							"table" => "tools.actions",
							"require" => "name|" . lang_require_action_name . "<=>devices|" . lang_require_devices . "<=>keywords|" . lang_require_action_keywords . "<=>message|" . lang_require_action_message
						]
					];
					
					break;
				case "admin.builder":
					if(!super_admin)
						response(500, lang_response_no_permission);

					$vars = [
						"template" => [
							"title" => lang_modal_buildersettings_title,
							"data" => [
								"builder" => $this->widget->getSystemSettings(),
								"assets" => [
									"logo" => $this->file->exists("uploads/builder/logo.png"),
									"icon" => $this->file->exists("uploads/builder/icon.png"),
									"splash" => $this->file->exists("uploads/builder/splash.png")
								]
							]
						],
						"handler" => [
							"id" => 1,
							"tpl" => $tpl,
							"size" => "lg",
							"type" => "update",
							"require" => "package_name|" . lang_require_packagename . "<=>app_name|" . lang_require_appname . "<=>app_color|" . lang_require_appcolor . "<=>app_send|" . lang_require_appsend . "<=>app_receive|" . lang_require_appreceive . "<=>builder_email|" . lang_require_builderemail
						]
					];
					
					break;
				case "admin.settings":
					if(!super_admin)
						response(500, lang_response_no_permission);

					foreach(["paypal", "stripe", "mollie"] as $provider):
						if(in_array($provider, explode(",", system_providers)))
							$providers[$provider] = true;
						else
							$providers[$provider] = false;
					endforeach;

					$vars = [
						"template" => [
							"title" => lang_modal_systemsettings_title,
							"data" => [
								"system" => $this->widget->getSystemSettings(),
								"languages" => $this->widget->getLanguages(),
								"providers" => $providers
							]
						],
						"handler" => [
							"id" => 1,
							"tpl" => $tpl,
							"size" => "xl",
							"type" => "update",
							"position" => "right"
						]
					];
					
					break;
				case "admin.theme":
					if(!super_admin)
						response(500, lang_response_no_permission);
					
					$vars = [
						"template" => [
							"title" => lang_modal_themesettings_title,
							"data" => [
								"system" => $this->widget->getSystemSettings(),
								"assets" => [
									"landing" => $this->file->exists("uploads/theme/landing.png"),
									"dashboard" => $this->file->exists("uploads/theme/dashboard.png"),
									"favicon" => $this->file->exists("uploads/theme/favicon.png")
								]
							]
						],
						"handler" => [
							"id" => 1,
							"tpl" => $tpl,
							"size" => "md",
							"type" => "update"
						]
					];
					
					break;
				case "add.user":
					if(!permission("manage_users"))
						response(500, lang_response_no_permission);
					
					$vars = [
						"template" => [
							"title" => lang_modal_adduser_title,
							"data" => [
								"roles" => $this->widget->getRoles(),
								"languages" => $this->widget->getLanguages()
							]
						],
						"handler" => [
							"tpl" => $tpl,
							"type" => "create",
							"position" => "right",
							"table" => "administration.users",
							"require" => "name|" . lang_require_name . "<=>email|" . lang_require_email . "<=>password|" . lang_require_password
						]
					];
					
					break;
				case "edit.user":
					if(!permission("manage_users"))
						response(500, lang_response_no_permission);
					
					if(!$this->sanitize->isInt($id))
						response(500, lang_response_invalid);

					try {
						$user = $this->widget->getUser($id);
					} catch(Exception $e){
						response(500, lang_response_invalid);
					}

					$vars = [
						"template" => [
							"title" => lang_modal_edituser_title,
							"data" => [
								"user" => $user,
								"roles" => $this->widget->getRoles(),
								"languages" => $this->widget->getLanguages()
							]
						],
						"handler" => [
							"id" => $id,
							"tpl" => $tpl,
							"type" => "update",
							"position" => "right",
							"table" => "administration.users",
							"require" => "name|" . lang_require_name . "<=>email|" . lang_require_email
						]
					];
					
					break;
				case "add.role":
					if(!super_admin)
						response(500, lang_response_no_permission);
					
					$vars = [
						"template" => [
							"title" => lang_widget_importcontacts_title
						],
						"handler" => [
							"tpl" => $tpl,
							"size" => "md",
							"type" => "create",
							"table" => "administration.roles",
							"require" => "name|" . lang_require_addrole_name . "<=>permissions|" . lang_require_addrole_permissions
						]
					];
					
					break;
				case "edit.role":
					if(!super_admin)
						response(500, lang_response_no_permission);

					if(!$this->sanitize->isInt($id))
						response(500, lang_response_invalid);

					try {
						$role = $this->widget->getRole($id);
					} catch(Exception $e){
						response(500, lang_response_invalid);
					}

					$rpermissions = explode(",", $role["permissions"]);
					$spermissions = [
        				"manage_users" => lang_form_roles_manageusers,
        				"manage_packages" => lang_form_roles_managepackages,
        				"manage_vouchers" => lang_form_roles_managevouchers,
        				"manage_subscriptions" => lang_form_roles_managesubscriptions,
        				"manage_transactions" => lang_form_roles_managetransactions,
        				"manage_widgets" => lang_form_roles_managewidgets,
        				"manage_pages" => lang_form_roles_managepages,
        				"manage_languages" => lang_form_roles_managelanguages,
        				"manage_fields" => lang_form_roles_managefields
        			];

        			foreach($spermissions as $permission => $name):
						if(in_array($permission, $rpermissions)):
							$permissions[$permission] = [
								"name" => $name,
								"selected" => true
							];
						else:
							$permissions[$permission] = [
								"name" => $name,
								"selected" => false
							];
						endif;
					endforeach;

					$vars = [
						"template" => [
							"title" => lang_widget_editrole_title,
							"data" => [
								"role" => $role,
								"permissions" => $permissions
							]
						],
						"handler" => [
							"id" => $id,
							"tpl" => $tpl,
							"size" => "md",
							"type" => "update",
							"table" => "administration.roles",
							"require" => "name|" . lang_require_addrole_name . "<=>permissions|" . lang_require_addrole_permissions
						]
					];
					
					break;
				case "add.package":
					if(!permission("manage_packages"))
						response(500, lang_response_no_permission);
					
					$vars = [
						"template" => [
							"title" => lang_modal_addpackage_title
						],
						"handler" => [
							"tpl" => $tpl,
							"size" => "lg",
							"type" => "create",
							"table" => "administration.packages",
							"require" => "name|" . lang_require_packagename . "<=>price|" . lang_require_packageprice . "<=>send|" . lang_require_packagesend . "<=>receive|" . lang_require_packagereceive . "<=>device|" . lang_require_packagedevice . " limit<=>key|" . lang_require_packagekey . "<=>webhook|" . lang_require_packagehook
						]
					];
					
					break;
				case "edit.package":
					if(!permission("manage_packages"))
						response(500, lang_response_no_permission);
					
					if(!$this->sanitize->isInt($id))
						response(500, lang_response_invalid);

					try {
						$package = $this->widget->getPackage($id);
					} catch(Exception $e){
						response(500, lang_response_invalid);
					}

					$vars = [
						"template" => [
							"title" => lang_modal_editpackage_title,
							"data" => [
								"package" => $package
							]
						],
						"handler" => [
							"id" => $id,
							"tpl" => $tpl,
							"size" => "lg",
							"type" => "update",
							"table" => "administration.packages",
							"require" => "name|" . lang_require_packagename . "<=>price|" . lang_require_packageprice . "<=>send|" . lang_require_packagesend . "<=>receive|" . lang_require_packagereceive . "<=>device|" . lang_require_packagedevice . " limit<=>key|" . lang_require_packagekey . "<=>webhook|" . lang_require_packagehook
						]
					];
					
					break;
				case "add.voucher":
					if(!permission("manage_vouchers"))
						response(500, lang_response_no_permission);
					
					$vars = [
						"template" => [
							"title" => lang_form_title_addvoucher,
							"data" => [
								"packages" => $this->widget->getPackages()
							]
						],
						"handler" => [
							"tpl" => $tpl,
							"size" => "md",
							"type" => "create",
							"table" => "administration.vouchers",
							"require" => "name|" . lang_require_voucher_name . "e<=>package|" . lang_require_voucher_package
						]
					];
					
					break;
				case "add.subscription":
					if(!permission("manage_subscriptions"))
						response(500, lang_response_no_permission);
					
					$vars = [
						"template" => [
							"title" => lang_form_title_addsubscription,
							"data" => [
								"users" => $this->widget->getUsers(),
								"packages" => $this->widget->getPackages()
							]
						],
						"handler" => [
							"tpl" => $tpl,
							"size" => "md",
							"type" => "create",
							"table" => "administration.subscriptions",
							"require" => "user|" . lang_require_subscription_user . "<=>package|" . lang_require_subscription_user
						]
					];
					
					break;
				case "add.widget":
					if(!permission("manage_widgets"))
						response(500, lang_response_no_permission);
					
					$vars = [
						"template" => [
							"title" => lang_modal_addwidget_title
						],
						"handler" => [
							"tpl" => $tpl,
							"size" => "lg",
							"type" => "create",
							"table" => "administration.widgets",
							"require" => "name|" . lang_require_widgetname . "<=>size|" . lang_require_widgetsize . "<=>position|" . lang_require_widgetposition . "<=>type|" . lang_require_widgettype
						]
					];
					
					break;
				case "edit.widget":
					if(!permission("manage_widgets"))
						response(500, lang_response_no_permission);
					
					if(!$this->sanitize->isInt($id))
						response(500, lang_response_invalid);

					try {
						$widget = $this->widget->getWidget($id);
					} catch(Exception $e){
						response(500, lang_response_invalid);
					}

					$vars = [
						"template" => [
							"title" => lang_modal_editwidget_title,
							"data" => [
								"widget" => $widget,
								"content" => $widget["content"]
							]
						],
						"handler" => [
							"id" => $id,
							"tpl" => $tpl,
							"size" => "lg",
							"type" => "update",
							"table" => "administration.widgets",
							"require" => "name|" . lang_require_widgetname . "<=>size|" . lang_require_widgetsize . "<=>position|" . lang_require_widgetposition . "<=>type|" . lang_require_widgettype
						]
					];
					
					break;
				case "add.page":
					if(!permission("manage_pages"))
						response(500, lang_response_no_permission);
					
					$vars = [
						"template" => [
							"title" => lang_widget_addpage_title,
							"data" => [
								"roles" => $this->widget->getRoles()
							]
						],
						"handler" => [
							"tpl" => $tpl,
							"size" => "lg",
							"type" => "create",
							"table" => "administration.pages",
							"require" => "name|" . lang_require_pagename . "<=>roles|" . lang_require_pageroles
						]
					];
					
					break;
				case "edit.page":
					if(!permission("manage_pages"))
						response(500, lang_response_no_permission);

					if(!$this->sanitize->isInt($id))
						response(500, lang_response_invalid);

					try {
						$page = $this->widget->getPage($id);
					} catch(Exception $e){
						response(500, lang_response_invalid);
					}

					$proles = explode(",", $page["roles"]);
					$sroles = $this->widget->getRoles();

        			foreach($sroles as $role):
						if(in_array($role["id"], $proles)):
							$roles[$role["id"]] = [
								"name" => $role["name"],
								"selected" => true
							];
						else:
							$roles[$role["id"]] = [
								"name" => $role["name"],
								"selected" => false
							];
						endif;
					endforeach;

					$vars = [
						"template" => [
							"title" => lang_widget_editpage_title,
							"data" => [
								"page" => $page,
								"roles" => $roles
							]
						],
						"handler" => [
							"id" => $id,
							"tpl" => $tpl,
							"size" => "lg",
							"type" => "update",
							"table" => "administration.pages",
							"require" => "name|" . lang_require_pagename . "<=>roles|" . lang_require_pageroles
						]
					];
					
					break;
				case "add.language":
					if(!permission("manage_languages"))
						response(500, lang_response_no_permission);

					try {
						$language = $this->widget->getLanguage(1);
					} catch(Exception $e){
						response(500, lang_response_invalid);
					}
					
					$countries = \CountryCodes::get("alpha2", "country");

					$vars = [
						"template" => [
							"title" => lang_modal_addlanguage_title,
							"data" => [
								"language" => $language,
								"countries" => $countries
							]
						],
						"handler" => [
							"tpl" => $tpl,
							"size" => "lg",
							"type" => "create",
							"table" => "administration.languages",
							"require" => "name|" , lang_require_languagename . "<=>iso|" . lang_require_languageiso . "<=>translations|" . lang_require_languagestr
						]
					];
					
					break;
				case "edit.language":
					if(!permission("manage_languages"))
						response(500, lang_response_no_permission);
					
					if(!$this->sanitize->isInt($id))
						response(500, lang_response_invalid);

					try {
						$language = $this->widget->getLanguage($id);
					} catch(Exception $e){
						response(500, lang_response_invalid);
					}

					$countries = \CountryCodes::get("alpha2", "country");

					$vars = [
						"template" => [
							"title" => lang_modal_editlanguage_title,
							"data" => [
								"language" => $language,
								"countries" => $countries
							]
						],
						"handler" => [
							"id" => $id,
							"tpl" => $tpl,
							"size" => "lg",
							"type" => "update",
							"table" => "administration.languages",
							"require" => "name|" , lang_require_languagename . "<=>iso|" . lang_require_languageiso . "<=>translations|" . lang_require_languagestr
						]
					];
					
					break;
				default:
					if(!isset($modal))
						response(500, lang_response_invalid);

					$tpl = "default";
					$vars = [
						"template" => [
							"title" => $modal["name"],
							"data" => [
								"modal" => $modal,
								"content" => $this->smarty->fetch("string:" . $this->sanitize->htmlDecode($modal["content"]))
							]
						],
						"handler" => [
							"size" => $modal["size"],
							"position" => ($modal["position"] == "center" ? false : $modal["position"])
						]
					];

			endswitch;

	        $this->smarty->assign($vars["template"]);

	    	response(200, "Smspilot Modal", [
	    		"vars" => (isset($vars["handler"]) ? $vars["handler"] : false),
	    		"tpl" => $this->smarty->fetch(template . "/widgets/modals/{$tpl}.tpl")
	    	]);

		endif;

		/**
		 * Tabs
		 */

		if($type == "tab"):

			if(!$this->session->has("logged"))
        		response(302, "Session doesn't exist!");

			$tpl = $this->sanitize->string($this->url->segment(5));

			if(Stringy\create($tpl)->contains("smspilot.")):
				$tpl = (string) Stringy\create($tpl)->removeLeft("smspilot.");
				if(!$this->smarty->templateExists(template . "/widgets/tabs/{$tpl}.tpl"))
		        	response(500, lang_response_invalid);
		    endif;

	        $vars = [
				"handler" => [
					"table" => $tpl
				]
			];

	        $this->smarty->assign($vars);

	    	response(200, "Smspilot Tab", [
	    		"vars" => $vars["handler"],
	    		"tpl" => $this->smarty->fetch(template . "/widgets/tabs/{$tpl}.tpl")
	    	]);

		endif;

		response(500, lang_response_invalid);
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
            response(302, "Session doesn't exist!");
        else
        	set_template("dashboard");

        $type = $this->sanitize->string($this->url->segment(4));
        $user = $this->session->get("logged");

        switch($type):
        	case "dashboard.default":
        		$vars = [
        			"chart" => $type
        		];

        		break;
        	case "admin.earnings":
        		if(!permission("manage_tansactions"))
					response(500, lang_response_no_permission);
					
        		$vars = [
        			"chart" => $type
        		];

        		break;
        	case "admin.messages":
        		if(!is_admin)
					response(500, lang_response_invalid);
					
        		$vars = [
        			"chart" => $type
        		];

        		break;
        	case "admin.users":
        		if(!permission("manage_users"))
					response(500, lang_response_no_permission);
					
        		$vars = [
        			"chart" => $type
        		];

        		break;
        	default:
        		response(500, lang_response_invalid);
        endswitch;

        $this->smarty->assign($vars);
		$this->smarty->display(template . "/widgets/charts/default.tpl");
	}
}