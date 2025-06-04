<?php
/**
 * @controller Dashboard
 */

class Dashboard_Controller extends MVC_Controller
{
    public function index()
    {
        if(!$this->session->has("logged"))
            $this->header->redirect(site_url);
        else
            set_template("dashboard");

        $page = ($this->sanitize->string($this->url->segment(3)) ?: "default");
        
        if($page != "pages"):
            if(!$this->sanitize->isValidToken($_GET))
                $this->header->redirect(($page == "default" ? site_url("dashboard") : site_url("dashboard/{$page}")));
        endif;

        if(!$this->smarty->templateExists(template . "/pages/{$page}.tpl"))
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

        if($this->system->checkQuota(logged_id) < 1):
            $this->system->create("quota", [
                "uid" => logged_id,
                "sent" => 0,
                "received" => 0
            ]);
        endif;

        switch($page){
            case "pages":
                $id = $this->url->segment(4);
                $slug = $this->url->segment(5);

                if(!$this->sanitize->isInt($id))
                    $this->header->redirect(site_url);

                if(empty($slug))
                    $this->header->redirect(site_url);

                $this->cache->container("system.pages");

                if(!$this->cache->has($id)):
                    $this->cache->set($id, $this->systemm->getPage($id));
                endif;

                $content = $this->cache->get($id);

                if($content["slug"] != $slug)
                    $this->header->redirect(site_url);

                $vars = [
                    "title" => $content["name"],
                    "data" => [
                        "page" => $content,
                        "content" => $this->sanitize->htmlDecode($content["content"])
                    ]
                ];

                break;
            case "messages":
                $vars = [
                    "title" => lang_dashboard_title_messages
                ];

                break;
            case "contacts":
                $vars = [
                    "title" => lang_dashboard_title_contacts
                ];

                break;
            case "devices":  
                $vars = [
                    "title" => lang_dashboard_title_devices
                ];

                break;
            case "tools": 
                $vars = [
                    "title" => lang_dashboard_title_tools
                ];

                break;
            case "administration": 
                if(!is_admin)
                    $this->header->redirect(site_url("dashboard"));

                $vars = [
                    "title" => lang_dashboard_title_admin,
                    "data" => [
                        "gateway" => $this->file->exists("uploads/builder/gateway.apk")
                    ]
                ];

                break;
            default:
                $this->cache->container("user." . logged_hash);

                if(!$this->cache->has("total")):
                    $this->cache->set("total", [
                        "sent" => number_format($this->system->getTotalSent(logged_id)),
                        "received" => number_format($this->system->getTotalReceived(logged_id))
                    ]);
                endif;

                if(!$this->cache->has("recent")):
                    $this->cache->set("recent", [
                        "sent" => $this->system->getRecentSent(logged_id, 5),
                        "received" => $this->system->getRecentReceived(logged_id, 5)
                    ]);
                endif;

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
                    "title" => lang_dashboard_title_default,
                    "data" => [
                        "total" => $this->cache->get("total"),
                        "recent" => $this->cache->get("recent"),
                        "subscription" => $this->cache->get("subscription")
                    ]
                ];
        }

        $vars["page"] = $page;

        $this->smarty->assign($vars);
        $this->smarty->display(template . "/header.tpl");
        $this->smarty->display(template . "/pages/{$page}.tpl");
        $this->smarty->display(template . "/footer.tpl");
    }
}