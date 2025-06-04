<?php
/**
 * @controller Default
 */

class Default_Controller extends MVC_Controller
{
    public function index()
    {
        $page = ($this->sanitize->string($this->url->segment(2)) ?: "default");

        if($this->session->has("logged") && $page != "pages")
            $this->header->redirect(site_url("dashboard"));
        else
            set_template("default");

        if(!$this->smarty->templateExists(template . "/pages/{$page}.tpl"))
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

        $this->cache->container("system.packages");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getDefaultPackages());
        endif;

        switch($page):
            case "pages":
                $id = $this->url->segment(3);
                $slug = $this->url->segment(4);

                if(!$this->sanitize->isInt($id))
                    $this->header->redirect(site_url);

                if(empty($slug))
                    $this->header->redirect(site_url);

                $this->cache->container("system.pages");

                if(!$this->cache->has($id)):
                    $this->cache->set($id, $this->system->getPage($id));
                endif;

                $content = $this->cache->get($id);

                if($this->session->has("logged"))
                    $this->header->redirect(site_url . "/dashboard/pages/{$content["id"]}/{$content["slug"]}");

                if($content["logged"] < 2)
                    $this->header->redirect(site_url);

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
            default:
                $vars = [
                    "title" => lang_landing_title_default,
                    "data" => [
                        "packages" => $this->cache->getAll()
                    ]
                ];

        endswitch;

        $vars["page"] = $page;

        $this->smarty->assign($vars);
        $this->smarty->display(template . "/header.tpl");
        $this->smarty->display(template . "/pages/{$page}.tpl");
        $this->smarty->display(template . "/footer.tpl");
    }
}
