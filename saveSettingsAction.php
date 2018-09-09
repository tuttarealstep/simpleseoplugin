<?php
/**
 * Created by PhpStorm.
 * User: thecopyright
 * Date: 08/09/18
 * Time: 11.31
 */


define('MY_CMS_PATH', true);
define("LOADER_LOAD_PAGE", false);
include '../../../../../src/Bootstrap.php';

$app->container['users']->hideIfNotLogged();

if(!$app->container['users']->currentUserHasPermission("manage_options"))
{
    throw new MyCMS\App\Utils\Exceptions\MyCMSException("You do not have permission to access this page!", "Permission denied");
}

if (isset($_POST['settings_site_name']))
{
    if(!empty($_POST['settings_site_name']))
    {
        $app->container['settings']->saveSettings("site_name", htmlentities($_POST['settings_site_name']));
    }
}


if (isset($_POST['settings_site_description']))
{
    if(!empty($_POST['settings_site_description']))
    {
        $app->container['settings']->saveSettings("site_description", htmlentities($_POST['settings_site_description']));
    }
}


if (isset($_POST['settings_keywords']))
{
    if(!empty($_POST['settings_keywords']))
    {
        $app->container['settings']->saveSettings("simpleSEO_keywords", htmlentities($_POST['settings_keywords']));
    }
}