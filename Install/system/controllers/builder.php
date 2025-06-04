<?php

class Builder_Controller extends MVC_Controller
{
	public function index()
	{
		$this->header->allow();

		$request = $this->sanitize->array($_GET);
		$type = $this->sanitize->string($this->url->segment(3));

		if(!isset($request["token"]))
			response(500, "Invalid Request!");
		
		if(system_token != $request["token"])
			response(500, "Invalid Request!");

		$this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSystemSettings());
        endif;

        set_system($this->cache->getAll());

		switch($type):
			case "download":
    			try {
					$this->guzzle->get(titansys_builder . "/releases/" . md5(system_purchase_code) . ".apk", [
						"sink" => "uploads/builder/gateway.apk",
		                "allow_redirects" => true,
						"http_errors" => false
					]);
				} catch(Exception $e){
					response(500, "Failed downloading apk file!");
				}

	            response(200, "Apk has been downloaded!");

				break;
			default:
				response(500, "Invalid Request!");
		endswitch;
	}
}