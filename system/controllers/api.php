<?php
/**
 * @controller API
 * @desc Handles api related requests
 */

class Api_Controller extends MVC_Controller
{
	/**
     *@api {POST}/send?key=API_KEY Send Message
     *@apiDescription Send an sms to defined phone recipient
     *@apiName Send
     *@apiGroup Messages
     *@apiVersion 1.0.0
     *
     *@apiParam {string} phone Recipient mobile number, must satisfy E164 format
     *@apiParam {string} message Message to be sent to recipient
     *@apiParam {int} device ID of device where you want to send the message, default is automatic (Optional)
     *@apiParam {int} sim Sim slot number for sending message, if the slot is not available, default slot will be used. Default is "1" (Optional)
     *@apiParam {int} priority Send the message as priority (Optional)
     *
     *@apiSuccess (Response Format) {int} status Status code handler
     *<br/> 200 = Success
     *<br/> 500 = Fail
     *@apiSuccess (Response Format) {string} message Status response message
     *@apiSuccess (Response Format) {array} data Additional array of data
     *
     *@apiSuccessExample Success Example
     *{
     *  "status": 200,
     *  "message": "Message has been queued for sending on JFH4-CF",
     *  "data": [
            {
               "name": "Johnny Sins", // recipient name
               "phone": "+6391234567890" // recipient mobile number, E164 formatted
               "slot": 1, // sim slot number
               "device": 2, // id of the device used for sending
               "timestamp": 1234567890123 // creation timestamp
            }
        ]
     *}
     *
     *@apiErrorExample Failed Example
     *{
     *  "status": 400,
     *  "message": "Something went wrong!",
     *  "data": false
     *}
     *
     */

	public function index()
	{
		$this->header->allow();

        $type = $this->sanitize->string($this->url->segment(3));

        $this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSystemSettings());
        endif;

        set_system($this->cache->getAll());

		switch($type):
			case "send":
                $request = $this->sanitize->array($_POST);
                $key = $this->sanitize->string(isset(
                    $_GET["key"]) ? 
                    $_GET["key"] : 
                    response(400, "Invalid Request!")
                );

                if(!$this->sanitize->length($key, 5))
                    response(400, "Invalid Request!");

                $this->cache->container("api.keys");

                if($this->cache->empty()):
                    $this->cache->setArray($this->api->getKeys());
                endif;

                $keys = $this->cache->getAll();

                if(!array_key_exists($key, $keys))
                    response(401, "API key is not valid!");

                $api = $keys[$key];

                $this->cache->container("user.subscription.{$api["hash"]}");

                if($this->cache->empty()):
                    $this->cache->setArray($this->system->checkSubscriptionByUserID($api["uid"]) > 0 ? $this->system->getPackageByUserID($api["uid"]) : $this->system->getDefaultPackage());
                endif;

                set_subscription($this->cache->getAll());

                if(!isset($request["phone"], $request["message"]))
                    response(400, "Invalid Request!");

                try {
                    $number = $this->phone->parse($request["phone"]);

                    if(!$number->isValidNumber())
                        response(400, "Invalid mobile number!");

                    if(!$number->getNumberType(Brick\PhoneNumber\PhoneNumberType::MOBILE))
                        response(400, "Invalid mobile number!");

                    $request["phone"] = $number->format(Brick\PhoneNumber\PhoneNumberFormat::E164);
                } catch(Brick\PhoneNumber\PhoneNumberParseException $e) {
                    response(400, $e->getMessage());
                }

                if(isset($request["sim"])):
                    if(!$this->sanitize->isInt($request["sim"]))
                        response(400, "Invalid Request!");
                endif;

                if(isset($request["priority"])):
                    if(!$this->sanitize->isInt($request["priority"]))
                        response(400, "Invalid Request!");
                endif;

                if(isset($request["device"])):
                    if(!$this->sanitize->isInt($request["device"]))
                        response(400, "Invalid Request!");
                endif;

                if(!$this->sanitize->length($request["message"], 5))
                    response(400, "Message is too short!");

                $this->cache->container("api.devices.{$api["hash"]}");

                if($this->cache->empty()):
                    $this->cache->setArray($this->api->getDevices($api["uid"]));
                endif;

                $devices = $this->cache->getAll();

                if($this->system->checkQuota($api["uid"]) < 1):
                    $this->system->create("quota", [
                        "uid" => $api["uid"],
                        "sent" => 0,
                        "received" => 0
                    ]);
                endif;

                if(limitation(subscription_send, $this->system->countQuota($api["uid"])["sent"]))
                    response(400, "Maximum allowed sending for today has been reached!");

                if(!isset($request["device"])):
                    foreach($devices as $device):
                        $this->cache->container("gateway.{$device["did"]}.{$api["hash"]}");

                        $usort[] = [
                            "id" => $device["id"],
                            "did" => $device["did"],
                            "pending" => count($this->cache->getAll())
                        ];
                    endforeach;
                    
                    usort($usort, function($previous, $next) {
                        return $previous["pending"] > $next["pending"] ? 1 : -1;
                    });

                    $did = $usort[0]["did"];
                    $device = $usort[0]["id"];
                else:
                    if(!array_key_exists($request["device"], $devices)):
                        response(400, "Device doesn't exist!");
                    else:
                        $did = $devices[$request["device"]]["did"];
                        $device = $request["device"];
                    endif;
                endif;

                $filtered = [
                    "uid" => $api["uid"],
                    "did" => $did,
                    "sim" => (isset($request["sim"]) ? ($request["sim"] < 1 ? 0 : 1) : 0),
                    "phone" => $request["phone"],
                    "message" => $request["message"],
                    "status" => 0,
                    "priority" => (isset($request["priority"]) ? ($request["priority"] < 1 ? 0 : 1) : 0),
                    "api" => 1
                ];

                $create = $this->api->create("sent", $filtered);

                if($create):
                    $this->cache->container("gateway.{$did}.{$api["hash"]}");

                    $this->cache->set($create, [
                        "api" => (boolean) 1,
                        "sim" => $filtered["sim"],
                        "device" => (int) $device,
                        "phone" => $filtered["phone"],
                        "message" => $filtered["message"],
                        "priority" => (boolean) ($filtered["priority"] < 1 ? 0 : 1),
                        "timestamp" => time()
                    ]);

                    $this->cache->container("messages.{$api["hash"]}");
                    $this->cache->clear();

                    $this->system->increment($api["uid"], "sent");

                    response(200, "Message has been added to queue on {$devices[$device]["name"]}", [
                        "api" => (boolean) 1,
                        "sim" => $filtered["sim"],
                        "device" => (int) $device,
                        "phone" => $filtered["phone"],
                        "message" => $filtered["message"],
                        "priority" => (boolean) $filtered["priority"],
                        "timestamp" => time()
                    ]);
                else:
                    response(400, "Something went wrong!");
                endif;
		
				break;
			default:
                $vars = [
                    "site_url" => (system_protocol < 2 ? str_replace("//", "http://", site_url) : str_replace("//", "https://", site_url))
                ];
                
				$this->smarty->display("_apidoc/layout.tpl", $vars);
		endswitch;
	}

	/**
     *@api {GET}/get/pending?key=API_KEY Get Pending
     *@apiDescription Get the list of pending messages for sending
     *@apiName Get Pending
     *@apiGroup Messages
     *@apiVersion 1.0.0
     *
     *@apiParam {int} device ID of the specific device you want to get pending messages (Optional)
     *
     *@apiSuccess (Response Format) {int} status Status code handler
     *<br/> 200 = Success
     *<br/> 500 = Fail
     *@apiSuccess (Response Format) {string} message Status response message
     *@apiSuccess (Response Format) {array} data Additional array of data
     *
     *@apiSuccessExample Success Example
     *{
     *  "status": 200,
     *  "message": "Messages waiting to be sent",
     *  "data": [
            {
               "api": true,
               "sim": 0,
               "phone": "+639123456789",
               "device": 1, // id of device used for sending
               "message": "This is the message",
               "priority": false,
               "timestamp": "1234567899999" // timestamp of creation
            }
        ]
     *} 
     *
     *@apiErrorExample Failed Example
     *{
     *  "status": 404,
     *  "message": "Device doesn't exist!",
     *  "data": false
     *}
     *
     */

    /**
     *@api {GET}/get/sent?key=API_KEY Get Sent
     *@apiDescription Get the list of sent messages on your account
     *@apiName Get Sent
     *@apiGroup Messages
     *@apiVersion 1.0.0
     *
     *@apiParam {int} limit Number of results to return, default is 10 (Optional)
     *@apiParam {int} page Pagination number to navigate result sets (Optional)
     *@apiParam {int} device Get messages only from specific device (Optional)
     *@apiParam {int} api Only get sent messages by API (Optional)
     *<br> 1 = Yes
     *<br> 0 = No (Default)
     *@apiParam {int} priority Only get prioritized sent messages (Optional)
     *<br> 1 = Yes
     *<br> 0 = No (Default)
     *
     *@apiSuccess (Response Format) {int} status Status code handler
     *<br/> 200 = Success
     *<br/> 500 = Fail
     *@apiSuccess (Response Format) {string} message Status response message
     *@apiSuccess (Response Format) {array} data Additional array of data
     *
     *@apiSuccessExample Success Example
     *{
     *  "status": 200,
     *  "message": "List of sent messages",
     *  "data": [
            {
               "sim": 1, // sim slot
               "api": true,
               "device": 1, // id of device used for sending, 0 if device was deleted
               "phone": "+639123456789",
               "message": "This is the message",
               "priority": false,
               "timestamp": 1234567891234 // timestamp of creation
            },
            {
               "sim": 2, // sim slot
               "api": false,
               "device": 34, // id of device used for sending, 0 if device was deleted
               "phone": "+639123456789",
               "message": "This is another message",
               "priority": true,
               "timestamp": 1234567899999 // timestamp of creation
            }
        ]
     *}
     *
     *@apiErrorExample Failed Example
     *{
     *  "status": 404,
     *  "message": "Device doesn't exist!",
     *  "data": false
     *}
     *
     */

    /**
     *@api {GET}/get/received?key=API_KEY Get Received
     *@apiDescription Get the list of received messages on your account
     *@apiName Get Received
     *@apiGroup Messages
     *@apiVersion 1.0.0
     *
     *@apiParam {int} limit Number of results to return, default is 10 (Optional)
     *@apiParam {int} page Pagination number to navigate result sets (Optional)
     *@apiParam {int} device Get received messages from specific device (Optional)
     *
     *@apiSuccess (Response Format) {int} status Status code handler
     *<br/> 200 = Success
     *<br/> 500 = Fail
     *@apiSuccess (Response Format) {string} message Status response message
     *@apiSuccess (Response Format) {array} data Additional array of data
     *
     *@apiSuccessExample Success Example
     *{
     *  "status": 200,
     *  "message": "List of sent messages",
     *  "data": [
            {
               "device": 1, // id of device origin, 0 if device was deleted
               "phone": "+639123456789",
               "message": "This is the message",
               "timestamp": 1234567891234 // timestamp of receive
            },
            {
               "device": 1, // id of device origin, 0 if device was deleted
               "phone": "+639123456789",
               "message": "This is another message",
               "timestamp": 1234567899999 // timestamp of receive
            }
        ]
     *}
     *
     *@apiErrorExample Failed Example
     *{
     *  "status": 404,
     *  "message": "Device doesn't exist!",
     *  "data": false
     *}
     *
     */

    /**
     *@api {GET}/get/devices?key=API_KEY Get Devices
     *@apiDescription Get the list of registered devices on your account
     *@apiName Get Devices
     *@apiGroup Devices
     *@apiVersion 1.0.0
     *
     *@apiSuccess (Response Format) {int} status Status code handler
     *<br/> 200 = Success
     *<br/> 500 = Fail
     *@apiSuccess (Response Format) {string} message Status response message
     *@apiSuccess (Response Format) {array} data Additional array of data
     *
     *@apiSuccessExample Success Example
     *{
     *  "status": 200,
     *  "message": "List of registered devices",
     *  "data": [
            {
               "id": 1,
               "name": "OPPO-F11",
               "version": 9,
               "version_name": "Android Pie",
               "manufacturer": "Oppo",
               "timestamp": 1234567891234 // registration timestamp
            },
            {
               "id": 24,
               "name": "SMJF1-SH",
               "version": 10,
               "version_name": "Android 10",
               "manufacturer": "Samsung",
               "timestamp": 1234567899999 // registration timestamp
            }
        ]
     *}
     *
     *@apiErrorExample Failed Example
     *{
     *  "status": 400,
     *  "message": "Something went wrong!",
     *  "data": false
     *}
     *
     */

    /**
     *@api {GET}/get/device?key=API_KEY Get Device
     *@apiDescription Get details about a registered device on your account
     *@apiName Get Device
     *@apiGroup Devices
     *@apiVersion 1.0.0
     *
     *@apiParam {int} id ID of the device
     *
     *@apiSuccess (Response Format) {int} status Status code handler
     *<br/> 200 = Success
     *<br/> 500 = Fail
     *@apiSuccess (Response Format) {string} message Status response message
     *@apiSuccess (Response Format) {array} data Additional array of data
     *
     *@apiSuccessExample Success Example
     *{
     *  "status": 200,
     *  "message": "Device information for SMFH-07",
     *  "data": {
           "name": "OPPO-F11",
           "version": 9,
           "version_name": "Android Pie",
           "manufacturer": "Oppo",
           "messages": {
                "sent": 88, // total sent messages
                "received": 62 // total received messages
            },
           "timestamp": 1234567891234 // registration timestamp
        }
     *}
     *
     *@apiErrorExample Failed Example
     *{
     *  "status": 400,
     *  "message": "Device doesn't exist!",
     *  "data": false
     *}
     *
     */

	/**
     *@api {GET}/get/contacts?key=API_KEY Get Contacts
     *@apiDescription Get the list of your saved contacts
     *@apiName Get Contacts
     *@apiGroup Address Book
     *@apiVersion 1.0.0
     *
     *@apiSuccess (Response Format) {int} status Status code handler
     *<br/> 200 = Success
     *<br/> 500 = Fail
     *@apiSuccess (Response Format) {string} message Status response message
     *@apiSuccess (Response Format) {array} data Additional array of data
     *
     *@apiSuccessExample Success Example
     *{
     *  "status": 200,
     *  "message": "List of saved contacts",
     *  "data": [
            {
               "gid": 1, // group id
               "group": "Friends", 
               "phone":"+639123456789",
               "name":"Martino Salesi"
            },
            {
               "gid": 5, // group id
               "group": "Default", 
               "phone":"+639123455678",
               "name":"Danny Flask"
            }
        ]
     *}
     *
     *@apiErrorExample Failed Example
     *{
     *  "status": 400,
     *  "message": "Something went wrong!",
     *  "data": false
     *}
     *
     */

    /**
     *@api {GET}/get/groups?key=API_KEY Get Groups
     *@apiDescription Get the list of your cantact groups
     *@apiName Get Groups
     *@apiGroup Address Book
     *@apiVersion 1.0.0
     *
     *@apiSuccess (Response Format) {int} status Status code handler
     *<br/> 200 = Success
     *<br/> 500 = Fail
     *@apiSuccess (Response Format) {string} message Status response message
     *@apiSuccess (Response Format) {array} data Additional array of data
     *
     *@apiSuccessExample Success Example
     *{
     *  "status": 200,
     *  "message": "List of contact groups",
     *  "data": [
            {
               "id": 1,
               "name":"Friends"
            },
            {
               "id": 5,
               "name":"Default"
            }
        ]
     *}
     *
     *@apiErrorExample Failed Example
     *{
     *  "status": 400,
     *  "message": "Something went wrong!",
     *  "data": false
     *}
     *
     */

	public function get()
	{
		$this->header->allow();

        $request = $this->sanitize->array($_GET);
		$type = $this->sanitize->string($this->url->segment(4));
        $key = $this->sanitize->string(isset(
            $request["key"]) ? 
            $request["key"] : 
            response(400, "Invalid Request!")
        );

        if(empty($key))
            response(400, "Invalid Request!");

        $this->cache->container("api.keys");

        if($this->cache->empty()):
            $this->cache->setArray($this->api->getKeys());
        endif;

        $keys = $this->cache->getAll();

        if(!array_key_exists($key, $keys))
            response(401, "API key is not valid!");

        $api = $keys[$key];

		switch($type):
            case "pending":
                if(!in_array("get_{$type}", $api["permissions"]))
                    response(403, "Permission \"get_{$type}\" not granted!");

                $this->cache->container("api.devices.{$api["hash"]}");

                if($this->cache->empty()):
                    $this->cache->setArray($this->api->getDevices($api["uid"]));
                endif;

                $devices = $this->cache->getAll();
                
                if(isset($request["device"])):
                    if(!$this->sanitize->isInt($request["device"]))
                        response(400, "Invalid Request!");

                    if(!array_key_exists($request["device"], $devices))
                        response(404, "Device doesn't exist!");

                    $this->cache->container("gateway.{$devices[$request["device"]]["did"]}." . md5($api["uid"]));

                    response(200, "Messages waiting to be sent on {$devices[$request["device"]]["name"]}", $this->cache->getAll());
                else:
                    foreach($devices as $device):
                        if($api["uid"] == $device["uid"]):
                            $this->cache->container("gateway.{$device["did"]}." . md5($api["uid"]));
                            $containers[] = $this->cache->getAll();
                        endif;
                    endforeach;
                    
                    response(200, "Messages waiting to be sent", (isset($containers) ? array_merge(...$containers) : []));
                endif;

                break;
            case "sent":
                if(!in_array("get_{$type}", $api["permissions"]))
                    response(403, "Permission \"get_{$type}\" not granted!");

                if(isset($request["limit"])):
                    if(!$this->sanitize->isInt($request["limit"]))
                        response(400, "Invalid Request!");
                endif;

                if(isset($request["page"])):
                    if(!$this->sanitize->isInt($request["page"]))
                        response(400, "Invalid Request!");
                endif;

                if(isset($request["api"])):
                    if(!$this->sanitize->isInt($request["api"]))
                        response(400, "Invalid Request!");
                endif;

                if(isset($request["priority"])):
                    if(!$this->sanitize->isInt($request["priority"]))
                        response(400, "Invalid Request!");
                endif;

                if(isset($request["device"])):
                    if(!$this->sanitize->isInt($request["device"]))
                        response(400, "Invalid Request!");

                    $this->cache->container("api.devices.{$api["hash"]}");

                    if($this->cache->empty()):
                        $this->cache->setArray($this->api->getDevices($api["uid"]));
                    endif;

                    $devices = $this->cache->getAll();

                    if(!array_key_exists($request["device"], $devices))
                        response(404, "Device doesn't exist!");
                endif;   

                $messages = $this->api->getSent(
                    $api["uid"],
                    (isset($request["limit"]) ? $request["limit"] : 10), 
                    (isset($request["page"]) ? $request["page"] : false),
                    (isset($devices) ? $devices[$request["device"]]["did"] : false), 
                    (isset($request["api"]) ? $request["api"] : false), 
                    (isset($request["priority"]) ? $request["priority"] : false)
                );

                response(200, "List of sent messages" . (isset($devices) ? " on {$devices[$request["device"]]["name"]}" : false), $messages);

                break;
            case "received":
                if(!in_array("get_{$type}", $api["permissions"]))
                    response(403, "Permission \"get_{$type}\" not granted!");

                if(isset($request["limit"])):
                    if(!$this->sanitize->isInt($request["limit"]))
                        response(400, "Invalid Request!");
                endif;

                if(isset($request["page"])):
                    if(!$this->sanitize->isInt($request["page"]))
                        response(400, "Invalid Request!");
                endif;

                if(isset($request["device"])):
                    if(!$this->sanitize->isInt($request["device"]))
                        response(400, "Invalid Request!");

                    $this->cache->container("api.devices.{$api["hash"]}");

                    if($this->cache->empty()):
                        $this->cache->setArray($this->api->getDevices($api["uid"]));
                    endif;

                    $devices = $this->cache->getAll();

                    if(!array_key_exists($request["device"], $devices))
                        response(404, "Device doesn't exist!");
                endif;   

                $messages = $this->api->getReceived(
                    $api["uid"],
                    (isset($request["limit"]) ? $request["limit"] : 10), 
                    (isset($request["page"]) ? $request["page"] : false),
                    (isset($devices) ? $devices[$request["device"]]["did"] : false)
                );

                response(200, "List of received messages" . (isset($devices) ? " from {$devices[$request["device"]]["name"]}" : false), $messages);

                break;
            case "devices":
                if(!in_array("get_{$type}", $api["permissions"]))
                    response(403, "Permission \"get_{$type}\" not granted!");

                $this->cache->container("api.devices.{$api["hash"]}");

                if($this->cache->empty()):
                    $this->cache->setArray($this->api->getDevices($api["uid"]));
                endif;

                $devices = [];

                foreach($this->cache->getAll() as $device):
                    if($api["uid"] == $device["uid"]):
                        $devices[] = [
                            "id" => (int) $device["id"],
                            "name" => $device["name"],
                            "version" => (int) $device["version"],
                            "version_name" => $device["version_name"],
                            "manufacturer" => $device["manufacturer"],
                            "timestamp" => (int) $device["timestamp"]
                        ];
                    endif;
                endforeach;

                response(200, "List of registered devices", $devices);

                break;
            case "device":
                if(!in_array("get_{$type}", $api["permissions"]))
                    response(403, "Permission \"get_{$type}\" not granted!");

                if(!$this->sanitize->isInt($request["id"]))
                    response(500, "Invalid Request");

                $this->cache->container("api.devices.{$api["hash"]}");

                if($this->cache->empty()):
                    $this->cache->setArray($this->api->getDevices($api["uid"]));
                endif;

                $devices = $this->cache->getAll();

                if(!array_key_exists($request["id"], $devices))
                    response(404, "Device doesn't exist!");

                if($api["devices"][0] != 0):
                    if(!in_array($devices[$request["id"]]["did"], $api["devices"]))
                        response(403, "API key has no access to this device!");
                endif;

                response(200, "Device information for {$devices[$request["id"]]["name"]}", [
                    "name" => $devices[$request["id"]]["name"],
                    "version" => (float) $devices[$request["id"]]["version"],
                    "version_name" => $devices[$request["id"]]["version_name"],
                    "manufacturer" => $devices[$request["id"]]["manufacturer"],
                    "messages" => [
                        "sent" => $this->api->getDeviceSentTotal($api["uid"], $devices[$request["id"]]["did"]),
                        "received" => $this->api->getDeviceReceivedTotal($api["uid"], $devices[$request["id"]]["did"])
                    ],
                    "timestamp" => $devices[$request["id"]]["timestamp"]
                ]);

                break;
            case "contacts":
                if(!in_array("get_{$type}", $api["permissions"]))
                    response(403, "Permission \"get_{$type}\" not granted!");

                $this->cache->container("api.contacts.{$api["hash"]}");

                if($this->cache->empty()):
                    $this->cache->setArray($this->api->getContacts($api["uid"]));
                endif;

                response(200, "List of saved contacts", $this->cache->getAll());

                break;
            case "groups":
                if(!in_array("get_{$type}", $api["permissions"]))
                    response(403, "Permission \"get_{$type}\" not granted!");

                $this->cache->container("api.groups.{$api["hash"]}");

                if($this->cache->empty()):
                    $this->cache->setArray($this->api->getGroups($api["uid"]));
                endif;

                response(200, "List of contact groups", $this->cache->getAll());

                break;
			default:
				response(400, "Invalid Request!");
		endswitch;
	}

	/**
     *@api {POST}/create/contact?key=API_KEY Create Contact
     *@apiDescription Create and save a new contact to your account
     *@apiName Create Contact
     *@apiGroup Address Book
     *@apiVersion 1.0.0
     *
     *@apiParam {string} phone Contact mobile number, it must satisfy "E164" format
     *@apiParam {string} name Contact name
     *@apiParam {int} group ID of contact group where you want to save this contact
     *
     *@apiSuccess (Response Format) {int} status Status code handler
     *<br/> 200 = Success
     *<br/> 500 = Fail
     *@apiSuccess (Response Format) {string} message Status response message
     *@apiSuccess (Response Format) {string} data Additional array of data
     *
     *@apiSuccessExample Success Example
     *{
     *  "status": 200,
     *  "message": "Contact saved sauccessfully!",
     *  "data": false
     *}
     *
     *@apiErrorExample Failed Example
     *{
     *  "status": 400,
     *  "message": "Something went wrong!",
     *  "data": false
     *}
     *
     */

  	/**
     *@api {POST}/create/group?key=API_KEY Create Group
     *@apiDescription Create and save a new contact group to your account
     *@apiName Create Group
     *@apiGroup Address Book
     *@apiVersion 1.0.0
     *
     *@apiParam {string} name Name of contact group
     *
     *@apiSuccess (Response Format) {int} status Status code handler
     *<br/> 200 = Success
     *<br/> 500 = Fail
     *@apiSuccess (Response Format) {string} message Status response message
     *@apiSuccess (Response Format) {string} data Additional array of data
     *
     *@apiSuccessExample Success Example
     *{
     *  "status": 200,
     *  "message": "Contact group saved successfully!",
     *  "data": false
     *}
     *
     *@apiErrorExample Failed Example
     *{
     *  "status": 400,
     *  "message": "Something went wrong!",
     *  "data": false
     *}
     *
     */

	public function create()
	{
		$this->header->allow();

		$request = $this->sanitize->array($_POST);
        $type = $this->sanitize->string($this->url->segment(4));
        $key = $this->sanitize->string(isset(
            $_GET["key"]) ? 
            $_GET["key"] : 
            response(400, "Invalid Request!")
        );

        if(empty($key))
            response(400, "Invalid Request!");

        $this->cache->container("api.keys");

        if($this->cache->empty()):
            $this->cache->setArray($this->api->getKeys());
        endif;

        $keys = $this->cache->getAll();

        if(!array_key_exists($key, $keys))
            response(401, "API key is not valid!");

        $api = $keys[$key];

        $this->cache->container("user.subscription.{$api["hash"]}");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->checkSubscriptionByUserID($api["uid"]) > 0 ? $this->system->getPackageByUserID($api["uid"]) : $this->system->getDefaultPackage());
        endif;

        set_subscription($this->cache->getAll());

		switch($type):
            case "contact":
                if(!in_array("create_{$type}", $api["permissions"]))
                    response(403, "Permission \"create_{$type}\" not granted!");

                if(!isset($request["phone"], $request["name"], $request["group"]))
                    response(400, "Invalid Request");

                if(empty($request["name"]))
                    response(400, "Contact name cannot be empty!");

                if(limitation(subscription_contact, $this->system->countContacts($api["uid"])))
                    response(400, "Maximum allowed contacts has been reached!");

                try {
                    $number = $this->phone->parse($request["phone"]);
                } catch(Brick\PhoneNumber\PhoneNumberParseException $e) {
                    response(400, $e->getMessage());
                }

                if (!$number->isValidNumber())
                    response(400, "Invalid mobile number!");

                if(!$number->getNumberType(Brick\PhoneNumber\PhoneNumberType::MOBILE))
                    response(400, "Invalid mobile number!");

                $request["phone"] = $number->format(Brick\PhoneNumber\PhoneNumberFormat::E164);

                if(!$this->sanitize->isInt($request["group"]))
                    response(400, "Invalid Request!");

                $this->cache->container("api.groups.{$api["hash"]}");

                if($this->cache->empty()):
                    $this->cache->setArray($this->api->getGroups($api["uid"]));
                endif;

                if(!array_key_exists($request["group"], $this->cache->getAll()))
                    response(400, "Invalid Request!");

                $this->cache->container("api.contacts.{$api["hash"]}");

                if($this->cache->empty()):
                    $this->cache->setArray($this->api->getContacts($api["uid"]));
                endif;

                if(array_key_exists($request["phone"], $this->cache->getAll()))
                    response(400, "Contact number already exist!");

                $filtered = [
                    "uid" => $api["uid"],
                    "gid" => $request["group"],
                    "phone" => $request["phone"],
                    "name" => $request["name"]
                ];

                if($this->api->create("contacts", $filtered)):
                    $this->cache->container("api.contacts.{$api["hash"]}");
                    $this->cache->clear();

                    response(200, "Contact saved successfully!");
                else:
                    response(400, "Something went wrong!");
                endif;

                break;
            case "group":
                if(!in_array("create_{$type}", $api["permissions"]))
                    response(403, "Permission \"create_{$type}\" not granted!");

                if(!isset($request["name"]))
                    response(400, "Invalid Request!");

                if(empty($request["name"]))
                    response(400, "Group name cannot be empty!");

                $filtered = [
                    "uid" => $api["uid"],
                    "name" => $request["name"]
                ];

                if($this->api->create("groups", $filtered)):
                    $this->cache->container("api.groups.{$api["hash"]}");
                    $this->cache->clear();

                    response(200, "Contact group saved sauccessfully!");
                else:
                    response(400, "Something went wrong!");
                endif;

                break;
			default:
				response(400, "Invalid Request!");
		endswitch;
	}

	/**
     *@api {GET}/delete/contact?key=API_KEY Delete Contact
     *@apiDescription Delete saved contact number from your account
     *@apiName Delete Contact
     *@apiGroup Address Book
     *@apiVersion 1.0.0
     *
     *@apiParam {int} id ID of contact number
     *
     *@apiSuccess (Response Format) {int} status Status code handler
     *<br/> 200 = Success
     *<br/> 500 = Fail
     *@apiSuccess (Response Format) {string} message Status response message
     *@apiSuccess (Response Format) {array} data Additional array of data
     *
     *@apiSuccessExample Success Example
     *{
     *  "status": 200,
     *  "message": "Contact number deleted successfully!",
     *  "data": false
     *}
     *
     *@apiErrorExample Failed Example
     *{
     *  "status": 400,
     *  "message": "Something went wrong!",
     *  "data": false
     *}
     *
     */

	/**
     *@api {GET}/delete/group?key=API_KEY Delete Group
     *@apiDescription Delete contact group from your account
     *@apiName Delete Group
     *@apiGroup Address Book
     *@apiVersion 1.0.0
     *
     *@apiParam {int} id ID of contact group
     *
     *@apiSuccess (Response Format) {int} status Status code handler
     *<br/> 200 = Success
     *<br/> 500 = Fail
     *@apiSuccess (Response Format) {string} message Status response message
     *@apiSuccess (Response Format) {array} data Additional array of data
     *
     *@apiSuccessExample Success Example
     *{
     *  "status": 200,
     *  "message": "Contact group deleted successfully!",
     *  "data": false
     *}
     *
     *@apiErrorExample Failed Example
     *{
     *  "status": 400,
     *  "message": "Something went wrong!",
     *  "data": false
     *}
     *
     */

	public function delete()
	{
		$this->header->allow();

		$request = $this->sanitize->array($_GET);
        $type = $this->sanitize->string($this->url->segment(4));
        $key = $this->sanitize->string(isset(
            $request["key"]) ? 
            $request["key"] : 
            response(400, "Invalid Request!")
        );

        if(empty($key))
            response(400, "Invalid Request!");

        $this->cache->container("api.keys");

        if($this->cache->empty()):
            $this->cache->setArray($this->api->getKeys());
        endif;

        $keys = $this->cache->getAll();

        if(!array_key_exists($key, $keys))
            response(401, "API key is not valid!");

        $api = $keys[$key];

		switch($type):
            case "contact":
                if(!in_array("delete_{$type}", $api["permissions"]))
                    response(403, "Permission \"delete_{$type}\" not granted!");

                if(!isset($request["id"]))
                    response(400, "Invalid Request!");

                if(!$this->sanitize->isInt($request["id"]))
                    response(400, "Invalid Request!");

                if($this->api->delete($api["uid"], $request["id"], "contacts")):
                    $this->cache->container("api.contacts.{$api["hash"]}");
                    $this->cache->clear();

                    response(200, "Contact number deleted successfully!");
                else:
                    response(400, "Something went wrong!");
                endif;

                break;
            case "group":
                if(!in_array("delete_{$type}", $api["permissions"]))
                    response(403, "Permission \"delete_{$type}\" not granted!");

                if(!isset($request["id"]))
                    response(400, "Invalid Request!");

                if(!$this->sanitize->isInt($request["id"]))
                    response(400, "Invalid Request!");

                if($this->api->delete($api["uid"], $request["id"], "groups")):
                    $this->cache->container("api.groups.{$api["hash"]}");
                    $this->cache->clear();

                    response(200, "Contact group deleted successfully!");
                else:
                    response(400, "Something went wrong!");
                endif;

                break;
			default:
				response(400, "Invalid Request!");
		endswitch;
	}
}