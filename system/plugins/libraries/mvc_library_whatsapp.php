<?php

class MVC_Library_Whatsapp 
{
	public $_guzzle = false;
	public $_file = false;

	public function check()
	{
		try {
			$check = json_decode($this->_guzzle->get(system_wa_server . ":" . system_wa_port, [
				"headers" => [
					"ngrok-skip-browser-warning" => uniqid(),
					"Bypass-Tunnel-Reminder" => uniqid()
				],
				"timeout" => 3,
				"connect_timeout" => 3,
                "allow_redirects" => true,
                "http_errors" => false,
                "verify" => false
            ])->getBody()->getContents());
			
			if($check->status == 200):
				return true;
			else:
				return false;
			endif;
		} catch(Exception $e){
			return false;
		}
	}

	public function create($uid, $hash, $unique = false)
	{
		try {
        	$create = json_decode($this->_guzzle->post(system_wa_server . ":" . system_wa_port . "/accounts/create/" . system_purchase_code, [
        		"headers" => [
					"ngrok-skip-browser-warning" => uniqid(),
					"Bypass-Tunnel-Reminder" => uniqid()
				],
	            "form_params" => [
	            	"system_token" => system_token,
	            	"site_unique" => sha1(site_url),
	            	"site_url" => rtrim(site_url(false, true), "/"),
	            	"unique" => $unique ? $unique : uniqid(time() . $hash),
	            	"uid" => $uid,
	            	"hash" => $hash,
	            	"os" => system_site_name
	            ],
				"timeout" => 5,
				"connect_timeout" => 5,
	            "allow_redirects" => true,
	            "http_errors" => false,
                "verify" => false
	        ])->getBody()->getContents());

	        if($create->status == 200):
	        	return $create->data->qr;
	        else:
        		return false;
        	endif;
        } catch(Exception $e){
        	return false;
        }
	}

	public function update($account)
	{
		try {
			$update = json_decode($this->_guzzle->post(system_wa_server . ":" . system_wa_port . "/accounts/update/" . sha1(site_url) . "/{$account["unique"]}/" . system_purchase_code, [
				"headers" => [
					"ngrok-skip-browser-warning" => uniqid(),
					"Bypass-Tunnel-Reminder" => uniqid()
				],
	            "form_params" => [
	            	"receive_chats" => $account["receive_chats"],
	            	"random_send" => $account["random_send"],
	            	"random_min" => $account["random_min"],
	            	"random_max" => $account["random_max"]
	            ],
				"timeout" => 5,
				"connect_timeout" => 5,
	            "allow_redirects" => true,
	            "http_errors" => false,
                "verify" => false
	        ])->getBody()->getContents());

	    	return $update->status;
		} catch(Exception $e){
			return false;
		}
	}

	public function delete($unique)
	{
		try {
			$delete = json_decode($this->_guzzle->get(system_wa_server . ":" . system_wa_port . "/accounts/delete/" . sha1(site_url) . "/{$unique}/" . system_purchase_code, [
				"headers" => [
					"ngrok-skip-browser-warning" => uniqid(),
					"Bypass-Tunnel-Reminder" => uniqid()
				],
				"timeout" => 5,
				"connect_timeout" => 5,
                "allow_redirects" => true,
                "http_errors" => false,
                "verify" => false
            ])->getBody()->getContents());

            return $delete->status;
		} catch(Exception $e){
			return false;
		}
	}

	public function status($unique)
	{
		try {
			$status = json_decode($this->_guzzle->get(system_wa_server . ":" . system_wa_port . "/accounts/status/" . sha1(site_url) . "/{$unique}/" . system_purchase_code, [
				"headers" => [
					"ngrok-skip-browser-warning" => uniqid(),
					"Bypass-Tunnel-Reminder" => uniqid()
				],
				"timeout" => 5,
				"connect_timeout" => 5,
                "allow_redirects" => true,
                "http_errors" => false,
                "verify" => false
            ])->getBody()->getContents());
			
            return $status->data;
		} catch(Exception $e){
			return false;
		}
	}

	public function send($unique)
	{
		try {
			$send = json_decode($this->_guzzle->get(system_wa_server . ":" . system_wa_port . "/chats/send/" . sha1(site_url) . "/{$unique}/" . system_purchase_code, [
				"headers" => [
					"ngrok-skip-browser-warning" => uniqid(),
					"Bypass-Tunnel-Reminder" => uniqid()
				],
				"timeout" => 5,
				"connect_timeout" => 5,
                "allow_redirects" => true,
                "http_errors" => false,
                "verify" => false
            ])->getBody()->getContents());

            return $send->status;
		} catch(Exception $e){
			return false;
		}
	}

	public function sendPriority($unique, $id, $recipient, $message)
	{
		try {
			$send = json_decode($this->_guzzle->post(system_wa_server . ":" . system_wa_port . "/chats/send/" . sha1(site_url) . "/{$unique}/" . system_purchase_code, [
				"headers" => [
					"ngrok-skip-browser-warning" => uniqid(),
					"Bypass-Tunnel-Reminder" => uniqid()
				],
				"form_params" => [
					"id" => $id,
					"recipient" => $recipient,
					"message" => $message
				],
				"timeout" => 5,
				"connect_timeout" => 5,
                "allow_redirects" => true,
                "http_errors" => false,
                "verify" => false
            ])->getBody()->getContents());

            return $send->status;
		} catch(Exception $e){
			return false;
		}
	}

	public function download($unique, $file, $id)
	{
		try {
			$fileName = explode(".", $file);
			$fileExtension = end($fileName);
			$uploadPath = "uploads/whatsapp/received/{$unique}/{$id}.{$fileExtension}";

			$this->_file->mkdir("uploads/whatsapp/received/{$unique}");

			$download = $this->_guzzle->get(system_wa_server . ":" . system_wa_port . "/files/download/" . sha1(site_url) . "/{$unique}/" . system_purchase_code . "/{$file}", [
				"headers" => [
					"ngrok-skip-browser-warning" => uniqid(),
					"Bypass-Tunnel-Reminder" => uniqid()
				],
				"sink" => $uploadPath,
				"allow_redirects" => true,
				"http_errors" => false,
                "verify" => false
            ]);

			if(filesize($uploadPath) <= 1024)
				$this->_file->delete($uploadPath);

            return true;
		} catch(Exception $e){
			return false;
		}
	}

	public function delete_campaign($unique, $hash, $cid)
	{	
		try {
			$delete = json_decode($this->_guzzle->get(system_wa_server . ":" . system_wa_port . "/chats/campaign/remove/" . sha1(site_url) . "/{$unique}/{$hash}/{$cid}/" . system_purchase_code, [
				"headers" => [
					"ngrok-skip-browser-warning" => uniqid(),
					"Bypass-Tunnel-Reminder" => uniqid()
				],
				"timeout" => 15,
				"connect_timeout" => 15,
                "allow_redirects" => true,
                "http_errors" => false,
                "verify" => false
            ])->getBody()->getContents());

            return $delete->status;
		} catch(Exception $e){
			return false;
		}
	}

	public function delete_chat($unique, $hash, $cid, $id)
	{
		try {
			$delete = json_decode($this->_guzzle->get(system_wa_server . ":" . system_wa_port . "/chats/delete/" . sha1(site_url) . "/{$unique}/{$hash}/{$cid}/{$id}/" . system_purchase_code, [
				"headers" => [
					"ngrok-skip-browser-warning" => uniqid(),
					"Bypass-Tunnel-Reminder" => uniqid()
				],
				"timeout" => 5,
				"connect_timeout" => 5,
                "allow_redirects" => true,
                "http_errors" => false,
                "verify" => false
            ])->getBody()->getContents());

            return $delete->status;
		} catch(Exception $e){
			return false;
		}
	}

	public function start_campaign($unique, $hash, $cid)
	{
		try {
			$start = json_decode($this->_guzzle->get(system_wa_server . ":" . system_wa_port . "/chats/campaign/start/" . sha1(site_url) . "/{$unique}/{$hash}/{$cid}/" . system_purchase_code, [
				"headers" => [
					"ngrok-skip-browser-warning" => uniqid(),
					"Bypass-Tunnel-Reminder" => uniqid()
				],
				"timeout" => 5,
				"connect_timeout" => 5,
                "allow_redirects" => true,
                "http_errors" => false,
                "verify" => false
            ])->getBody()->getContents());

            return $start->status;
		} catch(Exception $e){
			return false;
		}
	}

	public function stop_campaign($unique, $hash, $cid)
	{
		try {
			$stop = json_decode($this->_guzzle->get(system_wa_server . ":" . system_wa_port . "/chats/campaign/stop/" . sha1(site_url) . "/{$unique}/{$hash}/{$cid}/" . system_purchase_code, [
				"headers" => [
					"ngrok-skip-browser-warning" => uniqid(),
					"Bypass-Tunnel-Reminder" => uniqid()
				],
				"timeout" => 5,
				"connect_timeout" => 5,
                "allow_redirects" => true,
                "http_errors" => false,
                "verify" => false
            ])->getBody()->getContents());

            return $stop->status;
		} catch(Exception $e){
			return false;
		}
	}

	public function get_groups($unique)
	{
		try {
			$groups = json_decode($this->_guzzle->get(system_wa_server . ":" . system_wa_port . "/contacts/groups/" . sha1(site_url) . "/{$unique}/" . system_purchase_code, [
				"headers" => [
					"ngrok-skip-browser-warning" => uniqid(),
					"Bypass-Tunnel-Reminder" => uniqid()
				],
				"timeout" => 15,
				"connect_timeout" => 15,
                "allow_redirects" => true,
                "http_errors" => false,
                "verify" => false
            ])->getBody()->getContents(), true);

			if($groups["status"] == 200):
				return $groups["data"];
			else:
				return false;
			endif;
		} catch(Exception $e){
			return false;
		}
	}

	public function validate($unique, $address)
	{
		try {
			$validate = json_decode($this->_guzzle->get(system_wa_server . ":" . system_wa_port . "/contacts/validate/" . sha1(site_url) . "/{$unique}/{$address}/" . system_purchase_code, [
				"headers" => [
					"ngrok-skip-browser-warning" => uniqid(),
					"Bypass-Tunnel-Reminder" => uniqid()
				],
				"timeout" => 15,
				"connect_timeout" => 15,
                "allow_redirects" => true,
                "http_errors" => false,
                "verify" => false
            ])->getBody()->getContents(), true);

			if($validate["status"] == 200): 
				$phone = explode("@", $validate["data"]["jid"]);
				return [
					"jid" => $validate["data"]["jid"],
					"phone" => "+{$phone[0]}"
				];
			else:
				return false;
			endif;
		} catch(Exception $e){
			return false;
		}
	}
}