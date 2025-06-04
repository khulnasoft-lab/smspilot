<?php

class Payment_Controller extends MVC_Controller
{
	public function index()
	{
		if(!$this->session->has("logged"))
            $this->header->redirect(site_url);
        else
            set_template("dashboard");

        $order = $this->sanitize->string($this->url->segment(3));

        if(!$this->smarty->templateExists(template . "/pages/payment.tpl"))
            $this->header->redirect(site_url("dashboard"));

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

        $this->cache->container("system.payments");

        if(!$this->cache->has($order))
        	$this->header->redirect(site_url("dashboard"));

        $this->smarty->assign("order", $order);
        $this->smarty->display(template . "/pages/payment.tpl");
	}

	public function webhook()
	{	
		$request = $this->sanitize->array($_POST);
		$provider = $this->sanitize->string($this->url->segment(4));
		
		$this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSystemSettings());
        endif;

        set_system($this->cache->getAll());

        $this->cache->container("system.language." . system_default_lang);

        if($this->cache->empty()):
            $this->cache->setRaw($this->system->getTranslations(system_default_lang));
        endif;

        set_language($this->cache->getRaw());

        $this->cache->container("system.payments");

		switch($provider):
			case "mollie":
				if(!isset($request["id"]))
					response(500, "Invalid Request!");

				$mollie = new \Mollie\Api\MollieApiClient();
				$mollie->setApiKey(system_mollie_key);

			 	$payment = $mollie->payments->get($request["id"]);

				if(!$this->cache->has($payment->metadata->order_id))
					response(500, "Order doesn't exist!");
				else
					$order = $this->cache->get($payment->metadata->order_id);

				if ($payment->isPaid() && !$payment->hasRefunds() && !$payment->hasChargebacks()):
			    	if($this->system->checkSubscriptionByUserID($order["uid"]) > 0):
						$filtered = [
							"pid" => $order["pid"]
						];

						$subscription = $this->system->getSubscriptionByUserId($order["uid"]);
						if($this->system->update($subscription["id"], $order["uid"], "subscriptions", $filtered)):
							if($this->system->create("transactions", [
								"uid" => $order["uid"],
								"pid" => $order["pid"],
								"price" => $order["price"],
								"provider" => "mollie"
							])):
								$this->cache->container("admin.statistics");
								$this->cache->clear();
								$this->cache->container("admin.subscriptions");
								$this->cache->clear();
								$this->cache->container("admin.transactions");
								$this->cache->clear();
								$this->cache->container("user." . $order["hash"]);
								$this->cache->clear();
							endif;
						endif;
					else:
						$filtered = [
							"uid" => $order["uid"],
							"pid" => $order["pid"]
						];

						if($this->system->create("subscriptions", $filtered)):
							if($this->system->create("transactions", [
								"uid" => $filtered["uid"],
								"pid" => $filtered["pid"],
								"price" => $order["price"],
								"provider" => "mollie"
							])):
								$this->cache->container("admin.statistics");
								$this->cache->clear();
								$this->cache->container("admin.subscriptions");
								$this->cache->clear();
								$this->cache->container("admin.transactions");
								$this->cache->clear();
								$this->cache->container("user." . $order["hash"]);
								$this->cache->clear();
							endif;
						endif;
					endif;

					try {
						$vars = [
							"title" => system_site_name,
							"data" => [
								"subject" => lang_response_package_purchasedtitle,
								"package" => $this->system->getPackage($order["pid"]),
								"subscription" => $this->system->getSubscriptionByUserId($order["uid"])
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
					    $this->mail->addAddress($order["email"]);
					    $this->mail->isHTML(true);  
					    $this->mail->msgHTML($this->smarty->fetch("_mail/subscribe.tpl"));

					    $this->mail->send();
					} catch(Exception $e){
						// Ignore exceptions
					}
			    else:
			    	try {
						$vars = [
							"title" => system_site_name,
							"data" => [
								"subject" => lang_payment_transact_failed,
								"package" => $this->system->getPackage($order["pid"]),
								"order" => $payment->metadata->order_id
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
					    $this->mail->addAddress($order["email"]);
					    $this->mail->isHTML(true);  
					    $this->mail->msgHTML($this->smarty->fetch("_mail/failed.tpl"));

					    $this->mail->send();
					} catch(Exception $e){
						// Ignore exceptions
					}
			    endif;

				break;
			default:
				response(500, "Invalid Request!");
		endswitch;
	}
}