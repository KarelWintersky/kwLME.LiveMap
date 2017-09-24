<?php
/**
 * User: Arris
 * Date: 24.09.2017, time: 14:52
 */

define('__ROOT__', __DIR__);

require_once (__ROOT__ . '/engine/__required.php');
require_once (__ROOT__ . '/engine/units/unit.MapsListRender.php');

$main_template = new Template('index.html', '$/templates');

// is logged
$auth = LMEConfig::get_auth();

$is_logged = $auth->isLogged();

if ($is_logged) {
    $userinfo = $auth->getCurrentSessionInfo();
    $main_template->set('is_logged', $is_logged);
    $main_template->set('is_logged_user', $userinfo['email']);
    $main_template->set('is_logged_user_ip', $userinfo['ip']);
}

// maps
$all_maps = new MapsListRender('');
$all_maps->run('');
$content_maps = $all_maps->content();
$main_template->set('content_maps', $content_maps);


// finish
$content = $main_template->render();
echo $content;