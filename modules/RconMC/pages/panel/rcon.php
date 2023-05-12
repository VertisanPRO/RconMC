<?php
/*
 *  Made by Samerton
 *  https://github.com/NamelessMC/Nameless/tree/v2/
 *  NamelessMC version 2.1.0
 *
 *  License: MIT
 *
 *  RconMC By VertisanPRO
 */

$RconMCLanguage = $GLOBALS['RconMCLanguage'];

if ($user->isLoggedIn()) {
    if (!$user->canViewStaffCP()) {
        Redirect::to(URL::build('/'));
    }
    if (!$user->isAdmLoggedIn()) {
        Redirect::to(URL::build('/panel/auth'));
    } else {
        if (!$user->hasPermission('admincp.rconmc')) {
            if (!$user->hasPermission('admincp.rcon.server.' . $_GET['id'])) {
                require_once(ROOT_PATH . '/403.php');
            }
        }
    }
} else {
    Redirect::to(URL::build('/login'));
}

const PAGE = 'panel';
const PARENT_PAGE = 'rcon_mc_items';
const PANEL_PAGE = 'rcon_mc_items';

require_once(ROOT_PATH . '/modules/RconMC/classes/Rcon.php');
require_once(ROOT_PATH . '/core/templates/backend_init.php');

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    if (!is_numeric($_GET['id'])) {
        Redirect::to(URL::build('/panel/rconmc'));
    }
    if ($cache->isCached('rcon_cmd_btn')) {
        $cmd_btn = $cache->retrieve('rcon_cmd_btn');
    } else {
        $cmd_btn = [];
    }
    if (isset($_POST['cmd_name'])) {
        try {
            if (Token::check($_POST['token'])) {
                $cmd_btn[$_POST['cmd_name']] = $_POST['command'];
                $cache->store('rcon_cmd_btn', $cmd_btn);
                Redirect::to(URL::build('/panel/rconmc/server', 'action=rcon&id=' . $_GET['id']));
            } else {
                $output['invalid_token'] = $language->get('general', 'invalid_token');
            }
        } catch (Exception $e) {
            // Error
        }
    }
    if (isset($_POST['btn_remove'])) {
        try {
            if (Token::check($_POST['token'])) {
                unset($cmd_btn[$_POST['btn_remove']]);
                $cache->store('rcon_cmd_btn', $cmd_btn);
                Redirect::to(URL::build('/panel/rconmc/server', 'action=rcon&id=' . $_GET['id']));
            } else {
                $output['invalid_token'] = $language->get('general', 'invalid_token');
            }
        } catch (Exception $e) {
            // Error
        }
    }
    $query_server = DB::getInstance()->get('rcon_mc', ['id', '=', $_GET['id']])->results();
    if (!count($query_server)) {
        Redirect::to(URL::build('/panel/rconmc'));
    }
    $query_server = $query_server[0];
    $host = Output::getClean($query_server->rcon_ip);
    $port = Output::getClean($query_server->rcon_port);
    $password = Output::getClean($query_server->rcon_pass);
    $timeout = 3;
    if (isset($_POST['cmd_mc'])) {
        try {
            if (Token::check($_POST['token'])) {
                $response = [];
                $rcon = new Rcon($host, $port, $password, $timeout);
                $command = $_POST['cmd_mc'];
                if (empty($command)) {
                    $response['status'] = 'error';
                    $response['error_label'] = $RconMCLanguage->get('general', 'error_commands_label');
                    $response['error_status'] = $RconMCLanguage->get('general', 'error_commands');
                } else {
                    if ($rcon->connect()) {
                        $rcon->send_command($command);
                        $response['status'] = 'success';
                        $response['command'] = $_POST['cmd_mc'];
                        $response['response'] = $rcon->get_response();
                        $response['success_status'] = $RconMCLanguage->get('general', 'success_send_command');
                    } else {
                        $response['status'] = 'error';
                        $response['error_label'] = $RconMCLanguage->get('general', 'error_commands_label');
                        $response['error_status'] = $RconMCLanguage->get('general', 'error_rcon_connect');
                    }
                }
            }
        } catch (Exception $e) {
            // Error
        }
        echo json_encode($response);
        exit;
    }
} else {
    $response['invalid_token'] = $language->get('general', 'invalid_token');
}

$smarty->assign([
    'CMD_BTN' => $cmd_btn,
    'TOKEN' => Token::get(),
    'BACK_LINK' => URL::build('/panel/rconmc'),
    'SUBMIT' => $language->get('general', 'submit'),
    'YES' => $language->get('general', 'yes'),
    'NO' => $language->get('general', 'no'),
    'BACK' => $language->get('general', 'back'),
    'SEND_TITLE' => $RconMCLanguage->get('general', 'send_title'),
    'TITLE' => $RconMCLanguage->get('general', 'rcon_name'),
    'CONSOLE' => $RconMCLanguage->get('general', 'console'),
    'SERVER_NAME' => Output::getClean($query_server->rcon_name),
    'SERVER_IP' => Output::getClean($query_server->rcon_ip),
    'SERVER_PORT' => Output::getClean($query_server->rcon_port),
    'SERVER_PASS' => Output::getClean($query_server->rcon_pass),
    'NO_RESPONSE' => $RconMCLanguage->get('general', 'no_response'),
    'COMMAND_TITLE' => $RconMCLanguage->get('general', 'command_title'),
    'RESPONSE_TITLE' => $RconMCLanguage->get('general', 'response_title'),
    'RESTART' => $RconMCLanguage->get('general', 'restart'),
    'STOP' => $RconMCLanguage->get('general', 'stop'),
    'TPS' => $RconMCLanguage->get('general', 'tps')
]);

$template_file = 'RconMC/rcon.tpl';

// Load modules + template
Module::loadPage($user, $pages, $cache, $smarty, [$navigation, $cc_nav, $staffcp_nav], $widgets, $template);
$template->onPageLoad();

if (isset($response))
    $smarty->assign([
        'RESPONSE' => $response,
    ]);

require(ROOT_PATH . '/core/templates/panel_navbar.php');

$template->displayTemplate($template_file, $smarty);