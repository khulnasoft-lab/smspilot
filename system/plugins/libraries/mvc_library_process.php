<?php

class MVC_Library_Process 
{
	public $_sanitize = false;
	public $_guzzle = false;
	public $_lex = false;

	public function webhooks($uid, $type, $payload, $webhooks)
	{
		$webhookArray = [];

		if(!empty($webhooks)):
			foreach($webhooks as $webhook):
				$form = [
					"secret" => $webhook["secret"],
					"type" => $type,
					"data" => $payload
				];

				if($this->_sanitize->isUrl($webhook["url"])):
					try {
						$this->_guzzle->post($webhook["url"], [
				            "form_params" => $form,
				            "allow_redirects" => true,
				            "http_errors" => false
				        ]);

				        $webhookArray[] = $webhook["id"];
					} catch(Exception $e){
						// Ignore
					}
				endif;
			endforeach;
		endif;

		return $webhookArray;
	}

	public function actionHooks($uid, $source, $event, $phone, $message, $hooks)
	{
		$actionArray = [];

		if(!empty($hooks)):
			foreach($hooks as $hook):
				if($source == $hook["source"] && $event == $hook["event"]):
					if($this->_sanitize->isUrl($hook["link"])):
						try {
							$this->_guzzle->get($this->_lex->parse($hook["link"], [
		        				"phone" => urlencode($phone),
		        				"message" => urlencode($message),
		        				"date" => [
		        					"now" => urlencode(date("F j, Y")),
		        					"time" => urlencode(date("h:i A"))
		        				]
		        			]), [
					            "allow_redirects" => true,
					            "http_errors" => false
					        ]);

					        $actionArray[] = $hook["id"];
						} catch(Exception $e){
							// Ignore
						}
					endif;
				endif;
			endforeach;
		endif;

		return $actionArray;
	}

	public function actionAutoreplies($uid, $source, $phone, $message, $autoreplies, $subscription = [], $extra = [])
	{
		$actionArray = [];

		if(!empty($autoreplies)):
			foreach($autoreplies as $autoreply):
				if($source == $autoreply["source"]):
					$detected = false;

					if($this->checkMatch($message, $autoreply["keywords"], $autoreply["match"])):
						$detected = true;
					endif;

					if($detected):
						$rejected = false;

						if(isset($extra["account"]) && $extra["account"] == $autoreply["account"]):
							try {
								$msgDecode = json_decode($autoreply["message"], true, JSON_THROW_ON_ERROR);
			
								if(isset($msgDecode["caption"])):
									$msgDecode["caption"] = $this->_lex->parse(footermark($subscription["footermark"], $msgDecode["caption"], system_message_mark), [
										"phone" => $phone,
										"message" => $message,
										"date" => [
											"now" => date("F j, Y"),
											"time" => date("h:i A") 
										]
									]);
								endif;

								if(isset($msgDecode["text"])):
									$msgDecode["text"] = $this->_lex->parse(footermark($subscription["footermark"], $msgDecode["text"], system_message_mark), [
										"phone" => $phone,
										"message" => $message,
										"date" => [
											"now" => date("F j, Y"),
											"time" => date("h:i A") 
										]
									]);
								endif;
							} catch(Exception $e){
								$rejected = true;
							}

							if(!$rejected):
								$actionArray[] = [
									"priority" => $autoreply["priority"],
									"account" => $extra["account"],
									"message" => json_encode($msgDecode)
								];
							endif;
						else:
							if(isset($extra["sim"]) && $extra["sim"] == $autoreply["sim"] && $extra["device"] == $autoreply["device"]):
								$actionArray[] = [
									"priority" => $autoreply["priority"],
									"device" => $extra["device"],
									"message" => $this->_lex->parse(footermark($subscription["footermark"], $autoreply["message"], system_message_mark), [
										"phone" => $phone,
										"message" => $message,
										"date" => [
											"now" => date("F j, Y"),
											"time" => date("h:i A") 
										]
									])
								];
							endif;
						endif;
					endif;
				endif;
			endforeach;
		endif;

		return $actionArray;
	}

	private function checkMatch($string, $trigger, $match) : bool
	{
		switch($match):
			case 1:
				return strcasecmp($string, $trigger) === 0;

				break;
			case 2:
				return strcmp($string, $trigger) === 0;

				break;
			case 3:
				$keywords = array_map("trim", explode(",", trim($trigger)));

				foreach ($keywords as $keyword):
					$escapedKeyword = preg_quote($keyword, '/');
					$pattern = "/\b{$escapedKeyword}\b/iu";

					if (preg_match($pattern, $string)):
						return true;
					endif;
				endforeach;

				break;
			default:
				return $this->isRegularExpressionValid($trigger) ? preg_match($trigger, $string) === 1 : false;
		endswitch;

		return false;
	}

	private function isRegularExpressionValid($pattern) : bool
	{
		set_error_handler(function($severity, $message, $file, $line) {
			throw new Exception($message);
		});
	
		try {
			preg_match($pattern, '');
			restore_error_handler();
			return true;
		} catch (Exception $e) {
			restore_error_handler();
			return false;
		}
	}
}