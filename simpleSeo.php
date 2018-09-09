<?php

use MyCMS\App\Utils\Models\Container;

class SimpleSEO extends Container
{

    public function __construct($container, $language)
    {
        parent::__construct($container);

        $this->container['language'] = $language;

        $this->setUpSettings();
        $this->addAdminPage();
        $this->addAdminMenu();
        $this->applySEO();
    }

    private function setUpSettings()
    {
        $this->container['settings']->addSettingsValue("simpleSEO_keywords", "");
    }

    private function addAdminPage()
    {
        //my_plugin_simpleseo
        $container = $this->container;
        $this->container['plugins']->applyEvent("addAdminPage", "simpleseo", function () use ($container)
        {
            ?>
            <link rel="stylesheet" href="{@MY_PLUGINS_PATH@}/simpleSeo/css/style.css">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="h1PagesTitle">SimpleSEO</h1>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
                <div class="row">
                    <div class="col-lg-12">
                        <p><?php echo $this->container['language']['how_search_engine_view_p']; ?></p>
                    </div>
                    <div class="col-lg-8 col-md-8 col-xs-8">
                        <div class="searchEngineView">
                            <div><a href="#"><h3>{@siteNAME@}</h3></a></div>
                            <div class="websiteLink"> <a href="#">{@siteURL@}</a></div>
                                <div class="websiteDescription">{@siteDESCRIPTION@}</div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-xs-4">
                        <div class="searchEngineView">
                            <a class="btn btn-block" data-featherlight="iframe" href="{@siteURL@}/my-admin/my_plugin/simpleSeoEdit?hiddenMenu=true"><?php echo $this->container['language']['edit_information']; ?></a>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8 col-md-8 col-xs-8">
                        <div class="searchEngineViewResult">
                            <!--<ul>
                                <li>test</li>
                            </ul>-->
                        </div>
                    </div>
                </div>
            </div>
            <?php


            $this->container['plugins']->addEvent("adminFooter", function ()
            {
                ?>
                <script>
                    $().ready(function () {
                       console.log("sad")
                    });
                </script>
                <?php
            });
        });

        $this->container['plugins']->applyEvent("addAdminPage", "simpleSeoEdit", function () use ($container)
        {
            ?>
            <link rel="stylesheet" href="{@MY_PLUGINS_PATH@}/simpleSeo/css/style.css">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="h1PagesTitle">SimpleSEO</h1>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
                <form method="post" action="{@MY_PLUGINS_PATH@}/simpleSeo/saveSettingsAction.php">
                <div class="row">
                    <div class="col-lg-12">
                        <p><?php echo $this->container['language']['edit_information_title']; ?></p>
                    </div>
                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                        <div class="form-group">
                            <label><?php echo $this->container['language']['title']; ?></label>
                            <input type="text" name="settings_site_name" class="form-control" value="{@siteNAME@}">
                        </div>
                        <div class="form-group">
                            <label><?php echo $this->container['language']['description']; ?></label>
                            <input type="text" name="settings_site_description" class="form-control" value="{@siteDescription@}">
                        </div>
                        <div class="form-group">
                            <label><?php echo $this->container['language']['keywords']; ?></label>
                            <input type="text" name="settings_keywords" class="form-control" value="<?php echo $this->container['settings']->getSettingsValue("simpleSEO_keywords"); ?>">
                            <small><?php echo $this->container['language']['separate_with_comma']; ?></small>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                        <input type="submit" id="btnSaveSettingsSEO" class="btn btn-block" value="<?php echo $this->container['language']['save']; ?>">
                    </div>
                </div>
                </form>
            </div>
            <?php


            $this->container['plugins']->addEvent("adminFooter", function ()
            {
                ?>
                <script>
                        $("#btnSaveSettingsSEO").on("click", function () {
                            parent.window.location.reload(true);
                        })
                </script>
                <?php
            });
        });
    }

    private function addAdminMenu()
    {
        $this->container['plugins']->applyEvent('addAdminSubMenu', "admin_settings_simpleseo", "menu_settings", $this->container['language']['menu_simpleseolabel'], "{@siteURL@}/my-admin/my_plugin/simpleseo", ['admin_settings_simpleseo']);
    }

    private function applySEO()
    {
        $this->container['plugins']->addEvent('pageContentAfterParse', function ($pageContent)
        {
            libxml_use_internal_errors(true);
            $page = new DOMDocument();
            $page->loadHTML($pageContent);

            $head = $page->getElementsByTagName('head')->item(0);

            if ($head->hasChildNodes() && count($head->getElementsByTagName('title')) <= 0)
            {
                $titleTag = $page->createElement('title', $this->container['settings']->getSettingsValue("site_name"));
                $head->insertBefore($titleTag, $head->firstChild);
            }

            $descriptionFound = false;
            $keywordsFound = false;
            foreach ($head->getElementsByTagName('meta') as $item)
            {
                if(strtolower($item->attributes[0]->value) == 'description')
                {
                    $descriptionFound = true;
                } else if(strtolower($item->attributes[0]->value) == 'keywords')
                {
                    $keywordsFound = true;
                }
            }

            if(!$descriptionFound)
            {
                $metaTagDescription = $page->createElement('meta');
                $metaTagDescription->setAttribute("name", "description");
                $metaTagDescription->setAttribute("content", $this->container['settings']->getSettingsValue("site_description"));
            }

            if(!$keywordsFound)
            {
                $metaTagKeywords = $page->createElement('meta');
                $metaTagKeywords->setAttribute("name", "keywords");
                $metaTagKeywords->setAttribute("content", $this->container['settings']->getSettingsValue("simpleSEO_keywords"));

                if ($head->hasChildNodes()) {
                    $head->insertBefore($metaTagKeywords, $head->firstChild);
                } else {
                    $head->appendChild($metaTagKeywords);
                }
            }


            return $page->saveHTML();
        });
    }

}

switch (MY_LANGUAGE)
{
    case 'it_IT':
        $language = require_once ("it.php");
        break;
    case 'en_US':
    default:
        $language = require_once ("en.php");
        break;
}

$SimpleSEO = new SimpleSEO($this->container, $language);