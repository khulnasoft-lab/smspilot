<?php

class Page_Controller extends MVC_Controller
{
	public function index()
	{
		if($this->session->has("logged"))
            set_template("dashboard");
        else
            set_template("default");

        $page = ($this->sanitize->string($this->url->segment(2)) ?: "default");

        if(!$this->smarty->templateExists(template . "/pages/page.tpl"))
            $this->header->redirect(site_url);

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

        $this->cache->container("system.pages");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getPages());
        endif;

        $vars = [
            "title" => lang_landing_title_default,
            "page" => "custom",
            "data" => [
                "page" => $this->cache->getAll()
            ]
        ];

        $this->smarty->assign($vars);
        $this->smarty->display(template . "/header.tpl");
        $this->smarty->display(template . "/pages/page.tpl");
        $this->smarty->display(template . "/footer.tpl");
	}
}