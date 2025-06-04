<?php

use Thamaraiselvam\MysqlImport\Import;

class Install_Controller extends MVC_Controller
{
	public function index()
	{
		define("install", true);
		
		$this->smarty->display("_install/layout.tpl");
	}

	public function ajax()
	{
		$this->header->allow();

		$request = $this->sanitize->array($_POST);

		if(!isset($request["site_name"], $request["site_desc"], $request["protocol"], $request["dbhost"], $request["dbname"], $request["dbuser"], $request["dbpass"], $request["name"], $request["email"], $request["password"]))
			response(500, "Some fields are not set!");

		if(!$this->sanitize->isEmail($request["email"]))
			response(500, "Invalid email address!");

		if(!$this->sanitize->length($request["name"]))
			response(500, "Name is too short!");

		if(!$this->sanitize->length($request["password"], 5))
			response(500, "Password is too short!");

		$write_req = [
			"uploads/",
			"system/storage/",
			"system/configurations/cc_env.inc",
			"system/configurations/cc_ver.inc",
			"system/controllers/install.php",
			"templates/_install/",
			"install.sql"
		];

		clearstatcache();

		foreach($write_req as $req):
			if(!is_writable($req))
				response(500, "{$req} is not writable! Please chmod to 775!");
		endforeach;

		$dbaddress = explode(":", $request["dbhost"]);

		$dbhost = $dbaddress[0];

		if(count($dbaddress) > 1):
			$dbport = $dbaddress[1];
		else:
			$dbport = 3306;
		endif;

		try {
			@new Import("install.sql", $request["dbuser"], $request["dbpass"], $request["dbname"], $dbhost, $dbport);
		} catch(Exception $e){
			response(500, "Invalid Database Credentials!");
		}

		$filtered = [
			"email" => $this->sanitize->email($request["email"]),
			"password" => password_hash($request["password"], PASSWORD_DEFAULT),
			"name" => $request["name"],
			"language" => 1
		];

		$systoken = hash("sha256", password_hash(uniqid(time(), true), PASSWORD_DEFAULT));

		$env = "dbhost<=>{$dbhost}
dbport<=>{$dbport}
dbname<=>{$request["dbname"]}
dbuser<=>{$request["dbuser"]}
dbpass<=>{$request["dbpass"]}
systoken<=>{$systoken}
installed<=>true";

		$this->file->put("system/configurations/cc_env.inc", $env);

		try {
			$query = "INSERT INTO users (email, password, name, role, suspended, language) VALUES (\"{$filtered["email"]}\", \"{$filtered["password"]}\", \"{$filtered["name"]}\", 1, 0, {$filtered["language"]});
UPDATE settings SET value = \"{$request["site_name"]}\" WHERE name = \"site_name\";
UPDATE settings SET value = \"{$request["site_desc"]}\" WHERE name = \"site_desc\";
UPDATE settings SET value = \"{$request["protocol"]}\" WHERE name = \"protocol\";";

			if($this->file->put("populate.sql", $query)):
				@new Import("populate.sql", $request["dbuser"], $request["dbpass"], $request["dbname"], $dbhost, $dbport);
			else:
				response(500, "Something went wrong while installing zender!");
			endif;
		} catch(Exception $e){
			response(500, "Something went wrong while installing zender!");
		}

		response(200, "Zender has been successfully installed!");
	}
}