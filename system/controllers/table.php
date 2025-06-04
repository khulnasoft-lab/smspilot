<?php

class Table_Controller extends MVC_Controller
{
	public function index()
	{
		$this->header->allow();

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

		$table = $this->sanitize->string($this->url->segment(3));

		switch($table){
			case "messages.sent":
				$this->cache->container("history." . logged_hash);

				if($this->cache->has("sent")):
					$messages = $this->table->getSent(logged_id, $this->cache->get("sent"));
					$this->cache->clear();
				else:
					$this->cache->container("messages." . logged_hash);

					if(!$this->cache->has($table)):
						$this->cache->set($table, $this->table->getSent(logged_id));
					endif;

					$messages = $this->cache->get($table);
				endif;

				$vars = [
					"data" => $messages,
					"columns" => [
						[
							"title" => "Sorting",
							"data" => "sorting",
							"visible" => false,
							"searchable" => false
						],
						[
							"title" => lang_dashboard_messages_tablesentrecipient,
							"data" => "phone"
						],
						[
							"title" => lang_dashboard_messages_tablesentmessage,
							"data" => "message",
							"width" => "20%"
						],
						[
							"title" => lang_dashboard_messages_tablesentdevice,
							"data" => "device",
							"width" => "15%"
						],
						[
							"title" => lang_dashboard_messages_tablesentcreated,
							"data" => [
								"_" => "sorting",
								"filter" => "sorting",
								"display" => "create_date"
							],
							"width" => "20%"
						],
						[
							"title" => lang_dashboard_messages_tablesentdetails,
							"data" => "details",
							"searchable" => false,
							"width" => "25%",
							"searchable" => false,
							"sortable" => false
						],
						[
							"title" => lang_dashboard_messages_tablesentoptions,
							"data" => "options",
							"searchable" => false,
							"sortable" => false
						]
					]
				];

				break;
			case "messages.received":
				$this->cache->container("history." . logged_hash);

				if($this->cache->has("received")):
					$messages = $this->table->getReceived(logged_id, $this->cache->get("received"));
					$this->cache->clear();
				else:
					$this->cache->container("messages." . logged_hash);

					if(!$this->cache->has($table)):
						$this->cache->set($table, $this->table->getReceived(logged_id));
					endif;

					$messages = $this->cache->get($table);
				endif;

				$vars = [
					"data" => $messages,
					"columns" => [
						[
							"title" => "Sorting",
							"data" => "sorting",
							"visible" => false,
							"searchable" => false
						],
						[
							"title" => lang_dashboard_messages_tablereceivedsender,
							"data" => "phone"
						],
						[
							"title" => lang_dashboard_messages_tablereceivedmessage,
							"data" => "message"
						],
						[
							"title" => lang_dashboard_messages_tablereceiveddevice,
							"data" => "device"
						],
						[
							"title" => lang_dashboard_messages_tablereceivedreceived,
							"data" => [
								"_" => "sorting",
								"filter" => "sorting",
								"display" => "receive_date"
							],
							"width" => "20%"
						],
						[
							"title" => lang_dashboard_messages_tablereceivedoptions,
							"data" => "options",
							"searchable" => false,
							"sortable" => false
						]
					]
				];

				break;
			case "messages.scheduled":
				$this->cache->container("messages." . logged_hash);

				if(!$this->cache->has($table)):
					$this->cache->set($table, $this->table->getScheduled(logged_id));
				endif;

				$messages = $this->cache->get($table);

				$vars = [
					"data" => $messages,
					"columns" => [
						[
							"title" => "Sorting",
							"data" => "sorting",
							"visible" => false,
							"searchable" => false
						],
						[
							"title" => lang_table_scheduled_name,
							"data" => "name"
						],
						[
							"title" => lang_table_scheduled_recipients,
							"data" => "recipients",
							"width" => "20%"
						],
						[
							"title" => lang_dashboard_messages_tablesentmessage,
							"data" => "message",
							"width" => "20%"
						],
						[
							"title" => lang_dashboard_messages_tablesentdevice,
							"data" => "device",
							"width" => "15%"
						],
						[
							"title" => lang_table_scheduled_repeat,
							"data" => "repeat",
							"width" => "15%"
						],
						[
							"title" => lang_table_scheduled_schedule,
							"data" => [
								"_" => "sorting",
								"filter" => "sorting",
								"display" => "send_date"
							],
							"width" => "20%"
						],
						[
							"title" => lang_dashboard_messages_tablesentoptions,
							"data" => "options",
							"searchable" => false,
							"sortable" => false
						]
					]
				];

				break;
			case "messages.templates":
				$this->cache->container("messages." . logged_hash);

				if(!$this->cache->has($table)):
					$this->cache->set($table, $this->table->getTemplates(logged_id));
				endif;

				$vars = [
					"data" => $this->cache->get($table),
					"columns" => [
						[
							"title" => "ID",
							"data" => "id",
							"visible" => false,
							"searchable" => false
						],
						[
							"title" => lang_dashboard_messages_tabletemplatesname,
							"data" => "name",
						],
						[
							"title" => lang_dashboard_messages_tabletemplatesformat,
							"data" => "format",
							"width" => "35%"
						],
						[
							"title" => lang_dashboard_messages_tabletemplatesoptions,
							"data" => "options",
							"searchable" => false,
							"sortable" => false
						]
					]
				];

				break;
			case "contacts.saved":
				$this->cache->container("contacts." . logged_hash);

				if(!$this->cache->has($table)):
					$this->cache->set($table, $this->table->getContacts(logged_id));
				endif;

				$vars = [
					"data" => $this->cache->get($table),
					"columns" => [
						[
							"title" => "ID",
							"data" => "id",
							"visible" => false,
							"searchable" => false
						],
						[
							"title" => lang_dashboard_contacts_tablesavedname,
							"data" => "name"
						],
						[
							"title" => lang_dashboard_contacts_tablesavednumber,
							"data" => "phone"
						],
						[
							"title" => lang_dashboard_contacts_tablesavedgroup,
							"data" => "group"
						],
						[
							"title" => lang_dashboard_contacts_tablesavedoptions,
							"data" => "options",
							"searchable" => false,
							"sortable" => false
						]
					]
				];

				break;
			case "contacts.groups":
				$this->cache->container("contacts." . logged_hash);

				if(!$this->cache->has($table)):
					$this->cache->set($table, $this->table->getGroups(logged_id));
				endif;

				$vars = [
					"data" => $this->cache->get($table),
					"columns" => [
						[
							"title" => "ID",
							"data" => "id",
							"visible" => false,
							"searchable" => false
						],
						[
							"title" => "ID",
							"data" => "id"
						],
						[
							"title" => lang_dashboard_contacts_tablegroupscontacts,
							"data" => "total",
							"width" => "20%"
						],
						[
							"title" => lang_dashboard_contacts_tablegroupsname,
							"data" => "name"
						],
						[
							"title" => lang_dashboard_contacts_tablegroupsoptions,
							"data" => "options",
							"searchable" => false,
							"sortable" => false
						]
					]
				];

				break;
			case "devices.registered":
				$this->cache->container("devices." . logged_hash);

				if(!$this->cache->has($table)):
					$this->cache->set($table, $this->table->getDevices(logged_id));
				endif;

				$vars = [
					"data" => $this->cache->get($table),
					"columns" => [
						[
							"title" => "ID",
							"data" => "id",
							"visible" => false,
							"searchable" => false
						],					
						[
							"title" => lang_dashboard_devices_tableregisteredmodel,
							"data" => "name"
						],
						[
							"title" => lang_dashboard_devices_tableregisteredbrand,
							"data" => "manufacturer"
						],
						[
							"title" => lang_dashboard_devices_tableregisteredversion,
							"data" => "version"
						],
						[
							"title" => lang_dashboard_devices_tableregisteredadded,
							"data" => [
								"_" => "sorting",
								"filter" => "sorting",
								"display" => "create_date"
							]
						],
						[
							"title" => lang_dashboard_devices_tableregisteredoptions,
							"data" => "options",
							"searchable" => false,
							"sortable" => false
						]
					]
				];

				break;
			case "tools.keys":
				$this->cache->container("tools." . logged_hash);

				if(!$this->cache->has($table)):
					$this->cache->set($table, $this->table->getKeys(logged_id));
				endif;

				$vars = [
					"data" => $this->cache->get($table),
					"columns" => [
						[
							"title" => "ID",
							"data" => "id",
							"visible" => false,
							"searchable" => false
						],	
						[
							"title" => lang_dashboard_tools_tablekeysname,
							"data" => "name"
						],	
						[
							"title" => lang_dashboard_tools_tablekeysdevices,
							"data" => "devices"
						],
						[
							"title" => lang_dashboard_tools_tablekeyspermissions,
							"data" => "permissions"
						],
						[
							"title" => lang_dashboard_tools_tablekeyscreated,
							"data" => [
								"_" => "sorting",
								"filter" => "sorting",
								"display" => "create_date"
							]
						],
						[
							"title" => lang_dashboard_tools_tablekeysoptions,
							"data" => "options",
							"searchable" => false,
							"sortable" => false
						]
					]
				];

				break;
			case "tools.webhooks":
				$this->cache->container("tools." . logged_hash);

				if(!$this->cache->has($table)):
					$this->cache->set($table, $this->table->getWebhooks(logged_id));
				endif;

				$vars = [
					"data" => $this->cache->get($table),
					"columns" => [
						[
							"title" => "ID",
							"data" => "id",
							"visible" => false,
							"searchable" => false
						],					
						[
							"title" => lang_dashboard_tools_tablehooksname,
							"data" => "name",
							"width" => "20%"
						],
						[
							"title" => lang_dashboard_tools_tablehooksurl,
							"data" => "url"
						],
						[
							"title" => lang_dashboard_tools_tablehooksdevices,
							"data" => "devices"
						],
						[
							"title" => lang_dashboard_tools_tablehookscreated,
							"data" => [
								"_" => "sorting",
								"filter" => "sorting",
								"display" => "create_date"
							]
						],
						[
							"title" => lang_dashboard_tools_tablehooksoptions,
							"data" => "options",
							"searchable" => false,
							"sortable" => false
						]
					]
				];

				break;
			case "tools.actions":
				$this->cache->container("tools." . logged_hash);

				if(!$this->cache->has($table)):
					$this->cache->set($table, $this->table->getActions(logged_id));
				endif;

				$vars = [
					"data" => $this->cache->get($table),
					"columns" => [
						[
							"title" => "ID",
							"data" => "id",
							"visible" => false,
							"searchable" => false
						],					
						[
							"title" => lang_table_action_name,
							"data" => "name",
							"width" => "20%"
						],
						[
							"title" => lang_table_action_type,
							"data" => "type"
						],
						[
							"title" => lang_table_action_event,
							"data" => "event"
						],
						[
							"title" => lang_table_action_devices,
							"data" => "devices"
						],
						[
							"title" => lang_dashboard_tools_tablehookscreated,
							"data" => [
								"_" => "sorting",
								"filter" => "sorting",
								"display" => "create_date"
							]
						],
						[
							"title" => lang_dashboard_tools_tablehooksoptions,
							"data" => "options",
							"searchable" => false,
							"sortable" => false
						]
					]
				];

				break;
			case "administration.users":
				if(!permission("manage_users"))
					response(500, lang_response_no_permission);

				$this->cache->container("admin.users");

				if($this->cache->empty()):
					$this->cache->setArray($this->table->getUsers());
				endif;

				$vars = [
					"data" => $this->cache->getAll(),
					"columns" => [
						[
							"title" => "ID",
							"data" => "id",
							"visible" => false,
							"searchable" => false
						],					
						[
							"title" => lang_dashboard_admin_tableusersname,
							"data" => "name",
							"width" => "20%"
						],
						[
							"title" => lang_dashboard_admin_tableusersemail,
							"data" => "email"
						],
						[
							"title" => lang_dashboard_admin_tableuserslanguage,
							"data" => "language"
						],
						[
							"title" => lang_dashboard_admin_tableusersjoin,
							"data" => [
								"_" => "sorting",
								"filter" => "sorting",
								"display" => "create_date"
							]
						],
						[
							"title" => lang_dashboard_admin_tableusersoptions,
							"data" => "options",
							"searchable" => false,
							"sortable" => false
						]
					]
				];

				break;
			case "administration.roles":
				if(!super_admin)
					response(500, lang_response_no_permission);

				$this->cache->container("admin.roles");

				if($this->cache->empty()):
					$this->cache->setArray($this->table->getRoles());
				endif;

				$vars = [
					"data" => $this->cache->getAll(),
					"columns" => [
						[
							"title" => "ID",
							"data" => "id",
							"visible" => false,
							"searchable" => false
						],					
						[
							"title" => lang_table_role_name,
							"data" => "name",
							"width" => "20%"
						],
						[
							"title" => lang_table_role_permissions,
							"data" => "permissions"
						],
						[
							"title" => lang_dashboard_admin_tableusersoptions,
							"data" => "options",
							"searchable" => false,
							"sortable" => false
						]
					]
				];

				break;
			case "administration.packages":
				if(!permission("manage_packages"))
					response(500, lang_response_no_permission);

				$this->cache->container("admin.packages");

				if($this->cache->empty()):
					$this->cache->setArray($this->table->getPackages());
				endif;

				$vars = [
					"data" => $this->cache->getAll(),
					"columns" => [
						[
							"title" => "ID",
							"data" => "id",
							"visible" => false,
							"searchable" => false
						],					
						[
							"title" => lang_dashboard_admin_tablepackagesname,
							"data" => "name",
							"width" => "20%"
						],
						[
							"title" => lang_dashboard_admin_tablepackagesprice,
							"data" => "price"
						],
						[
							"title" => lang_dashboard_admin_tablepackagessend,
							"data" => "send"
						],
						[
							"title" => lang_dashboard_admin_tablepackagesreceive,
							"data" => "receive"
						],
						[
							"title" => "Contacts",
							"data" => "contacts"
						],
						[
							"title" => lang_dashboard_admin_tablepackagesdevices,
							"data" => "devices"
						],
						[
							"title" => lang_dashboard_admin_tablepackageskeys,
							"data" => "keys"
						],
						[
							"title" => lang_dashboard_admin_tablepackageshooks,
							"data" => "webhooks"
						],
						[
							"title" => lang_dashboard_admin_tablepackagesoptions,
							"data" => "options",
							"searchable" => false,
							"sortable" => false
						]
					]
				];

				break;
			case "administration.vouchers":
				if(!permission("manage_vouchers"))
					response(500, lang_response_no_permission);

				$this->cache->container("admin.vouchers");

				if($this->cache->empty()):
					$this->cache->setArray($this->table->getVouchers());
				endif;

				$vars = [
					"data" => $this->cache->getAll(),
					"columns" => [
						[
							"title" => "ID",
							"data" => "id",
							"visible" => false,
							"searchable" => false
						],					
						[
							"title" => lang_table_voucher_name,
							"data" => "name",
							"width" => "20%"
						],
						[
							"title" => lang_table_voucher_package,
							"data" => "package"
						],
						[
							"title" => lang_table_voucher_created,
							"data" => [
								"_" => "create_sorting",
								"filter" => "create_sorting",
								"display" => "create_date"
							]
						],
						[
							"title" => lang_dashboard_admin_tableusersoptions,
							"data" => "options",
							"searchable" => false,
							"sortable" => false
						]
					]
				];

				break;
			case "administration.subscriptions":
				if(!permission("manage_subscriptions"))
					response(500, lang_response_no_permission);

				$this->cache->container("admin.subscriptions");

				if($this->cache->empty()):
					$this->cache->setArray($this->table->getSubscriptions());
				endif;

				$vars = [
					"data" => $this->cache->getAll(),
					"columns" => [
						[
							"title" => "ID",
							"data" => "id",
							"visible" => false,
							"searchable" => false
						],					
						[
							"title" => lang_dashboard_admin_tablesubscriptionsuser,
							"data" => "name",
							"width" => "20%"
						],
						[
							"title" => lang_dashboard_admin_tablesubscriptionspackage,
							"data" => "package"
						],
						[
							"title" => lang_dashboard_admin_tablesubscriptionsprice,
							"data" => "price"
						],
						[
							"title" => lang_dashboard_admin_tablesubscriptionsstart,
							"data" => [
								"_" => "start_sorting",
								"filter" => "start_sorting",
								"display" => "start_date"
							]
						],
						[
							"title" => lang_dashboard_admin_tablesubscriptionsexpire,
							"data" => [
								"_" => "expire_sorting",
								"filter" => "expire_sorting",
								"display" => "expire_date"
							]
						],
						[
							"title" => lang_dashboard_admin_tablesubscriptionsoptions,
							"data" => "options",
							"searchable" => false,
							"sortable" => false
						]
					]
				];

				break;
			case "administration.transactions":
				if(!permission("manage_transactions"))
					response(500, lang_response_no_permission);

				$this->cache->container("admin.transactions");

				if($this->cache->empty()):
					$this->cache->setArray($this->table->getTransactions());
				endif;

				$vars = [
					"data" => $this->cache->getAll(),
					"columns" => [
						[
							"title" => "ID",
							"data" => "id",
							"visible" => false,
							"searchable" => false
						],					
						[
							"title" => lang_dashboard_admin_tabletransactionscustomer,
							"data" => "name",
							"width" => "20%"
						],
						[
							"title" => lang_dashboard_admin_tabletransactionspackage,
							"data" => "package"
						],
						[
							"title" => lang_dashboard_admin_tabletransactionsamount,
							"data" => "price"
						],
						[
							"title" => lang_dashboard_admin_tabletransactionsprovider,
							"data" => "provider"
						],
						[
							"title" => lang_dashboard_admin_tabletransactionsdate,
							"data" => [
								"_" => "sorting",
								"filter" => "sorting",
								"display" => "create_date"
							]
						]
					]
				];

				break;
			case "administration.widgets":
				if(!permission("manage_widgets"))
					response(500, lang_response_no_permission);

				$this->cache->container("admin.widgets");

				if($this->cache->empty()):
					$this->cache->setArray($this->table->getWidgets());
				endif;

				$vars = [
					"data" => $this->cache->getAll(),
					"columns" => [
						[
							"title" => "ID",
							"data" => "id",
							"visible" => false,
							"searchable" => false
						],					
						[
							"title" => lang_dashboard_admin_tablewidgetsname,
							"data" => "name",
							"width" => "20%"
						],
						[
							"title" => lang_dashboard_admin_tablewidgetstype,
							"data" => "type"
						],
						[
							"title" => lang_dashboard_admin_tablewidgetssize,
							"data" => "size"
						],
						[
							"title" => lang_dashboard_admin_tablewidgetsposition,
							"data" => "position"
						],
						[
							"title" => lang_dashboard_admin_tablewidgetscreated,
							"data" => [
								"_" => "sorting",
								"filter" => "sorting",
								"display" => "create_date"
							]
						],
						[
							"title" => lang_dashboard_admin_tablewidgetsoptions,
							"data" => "options",
							"searchable" => false,
							"sortable" => false
						]
					]
				];

				break;
			case "administration.pages":
				if(!permission("manage_pages"))
					response(500, lang_response_no_permission);

				$this->cache->container("admin.pages");

				if($this->cache->empty()):
					$this->cache->setArray($this->table->getPages());
				endif;

				$vars = [
					"data" => $this->cache->getAll(),
					"columns" => [
						[
							"title" => "ID",
							"data" => "id",
							"visible" => false,
							"searchable" => false
						],					
						[
							"title" => lang_table_page_name,
							"data" => "name",
							"width" => "20%"
						],
						[
							"title" => lang_table_page_require,
							"data" => "logged"
						],
						[
							"title" => lang_table_page_roles,
							"data" => "roles"
						],
						[
							"title" => lang_table_page_created,
							"data" => [
								"_" => "sorting",
								"filter" => "sorting",
								"display" => "create_date"
							]
						],
						[
							"title" => "Options",
							"data" => "options",
							"searchable" => false,
							"sortable" => false
						]
					]
				];

				break;
			case "administration.languages":
				if(!permission("manage_languages"))
					response(500, lang_response_no_permission);

				$this->cache->container("admin.languages");

				if($this->cache->empty()):
					$this->cache->setArray($this->table->getLanguages());
				endif;

				$vars = [
					"data" => $this->cache->getAll(),
					"columns" => [
						[
							"title" => "ID",
							"data" => "id",
							"visible" => false,
							"searchable" => false
						],					
						[
							"title" => lang_dashboard_admin_tablelanguagesiso,
							"data" => "iso",
							"width" => "15%"
						],
						[
							"title" => lang_dashboard_admin_tablelanguagesname,
							"data" => "name"
						],
						[
							"title" => lang_dashboard_admin_tablelanguagessize,
							"data" => "size"
						],
						[
							"title" => lang_dashboard_admin_tablelanguagescreated,
							"data" => [
								"_" => "sorting",
								"filter" => "sorting",
								"display" => "create_date"
							]
						],
						[
							"title" => lang_dashboard_admin_tablelanguagesoptions,
							"data" => "options",
							"searchable" => false,
							"sortable" => false
						]
					]
				];

				break;
			default:
				response(500, lang_response_invalid);
		}

		response(200, "DataTable", [
			"data" => $vars["data"],
			"search" => [
				"text" => lang_table_search_text,
				"placeholder" => lang_table_search_placeholder
			],
			"columns" => $vars["columns"]
		]);
	}
}