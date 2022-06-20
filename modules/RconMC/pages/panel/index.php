<?php
/*
 *  Made by Samerton
 *  https://github.com/NamelessMC/Nameless/tree/v2/
 *  NamelessMC version 2.0.0-pr7
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
            if (!$user->hasPermission('admincp.rcon.veiw')) {
                require_once(ROOT_PATH . '/403.php');
            } else {
                $smarty->assign([
                    'USER_PERMISSION' => 1
                ]);
            }
        } else {
            $smarty->assign([
                'USER_PERMISSION' => 2
            ]);
        }
    }
} else {
    // Not logged in
    Redirect::to(URL::build('/login'));
}

const PAGE = 'panel';
const PARENT_PAGE = 'rcon_mc_items';
const PANEL_PAGE = 'rcon_mc_items';

require_once(ROOT_PATH . '/core/templates/backend_init.php');
require_once(ROOT_PATH . '/modules/RconMC/classes/Rcon.php');


$smarty->assign([
    'SUBMIT' => $language->get('general', 'submit'),
    'YES' => $language->get('general', 'yes'),
    'NO' => $language->get('general', 'no'),
    'BACK' => $language->get('general', 'back'),
    'ARE_YOU_SURE' => $language->get('general', 'are_you_sure'),
    'CONFIRM_DELETE' => $language->get('general', 'confirm_delete'),
    'TITLE' => $RconMCLanguage->get('general', 'rcon_name'),
    'ADD_NEW_SERVER' => $RconMCLanguage->get('general', 'add_new_server'),
    'SERVER_NAME' => $RconMCLanguage->get('general', 'server_name'),
    'SERVER_IP' => $RconMCLanguage->get('general', 'server_ip'),
    'SERVER_PORT' => $RconMCLanguage->get('general', 'server_port'),
    'SERVER_PASS' => $RconMCLanguage->get('general', 'server_pass'),
]);

$template_file = 'RconMC/index.tpl';


if ($user->hasPermission('admincp.rconmc')) {

    if (!isset($_GET['action'])) {

        if (Input::exists()) {
            $errors = [];
            try {
                if (Token::check(Input::get('token'))) {

                    $validation = Validate::check($_POST, [
                        'server_name' => [
                            'required' => true,
                            'min' => 2,
                            'max' => 32
                        ],
                        'server_ip' => [
                            'required' => true,
                            'min' => 2,
                            'max' => 255
                        ],
                        'server_pass' => [
                            'required' => true,
                            'min' => 2,
                            'max' => 255
                        ],
                        'server_port' => [
                            'required' => true,
                            'min' => 2,
                            'max' => 10
                        ]
                    ]);

                    if ($validation->passed()) {
                        try {

                            DB::getInstance()->insert('rcon_mc', [
                                'rcon_name' => htmlspecialchars(Input::get('server_name')),
                                'rcon_ip' => htmlspecialchars(Input::get('server_ip')),
                                'rcon_port' => htmlspecialchars(Input::get('server_port')),
                                'rcon_pass' => htmlspecialchars(Input::get('server_pass')),
                            ]);

                            Session::flash('staff_rcon', $RconMCLanguage->get('general', 'new_server_successfully'));
                        } catch (Exception $e) {
                            $errors[] = $e->getMessage();
                        }
                    } else {
                        $errors[] = $RconMCLanguage->get('general', 'new_server_errors');
                    }
                } else {
                    $errors[] = $language->get('general', 'invalid_token');
                }
            } catch (Exception $e) {
                // Error
            }
        }
    } else {
        switch ($_GET['action']) {

            case 'edit':

                if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
                    Redirect::to(URL::build('/panel/rconmc'));
                }
                $edit_server = DB::getInstance()->get('rcon_mc', ['id', '=', $_GET['id']])->results();
                if (!count($edit_server)) {
                    Redirect::to(URL::build('/panel/rconmc'));
                }

                $edit_server = $edit_server[0];

                if (Input::exists()) {
                    $errors = [];
                    try {
                        if (Token::check(Input::get('token'))) {

                            $validation = Validate::check($_POST, [
                                'server_name' => [
                                    'required' => true,
                                    'min' => 2,
                                    'max' => 32
                                ],
                                'server_ip' => [
                                    'required' => true,
                                    'min' => 2,
                                    'max' => 255
                                ],
                                'server_pass' => [
                                    'required' => true,
                                    'min' => 2,
                                    'max' => 255
                                ],
                                'server_port' => [
                                    'required' => true,
                                    'min' => 2,
                                    'max' => 10
                                ]
                            ]);

                            if ($validation->passed()) {
                                try {

                                    DB::getInstance()->update('rcon_mc', $edit_server->id, [
                                        'rcon_name' => htmlspecialchars(Input::get('server_name')),
                                        'rcon_ip' => htmlspecialchars(Input::get('server_ip')),
                                        'rcon_port' => htmlspecialchars(Input::get('server_port')),
                                        'rcon_pass' => htmlspecialchars(Input::get('server_pass')),
                                    ]);


                                    Session::flash('staff_rcon', $RconMCLanguage->get('general', 'edit_server_successfully'));
                                    Redirect::to(URL::build('/panel/rconmc'));
                                } catch (Exception $e) {
                                    $errors[] = $e->getMessage();
                                }
                            } else {
                                $errors[] = $RconMCLanguage->get('general', 'edit_errors');
                            }
                        } else {
                            $errors[] = $language->get('general', 'invalid_token');
                        }
                    } catch (Exception $e) {
                        // Error
                    }
                }

                $smarty->assign([
                    'EDIT_SERVER' => $RconMCLanguage->get('general', 'server_edit_title'),
                    'BACK_LINK' => URL::build('/panel/rconmc'),
                    'EDIT_NAME' => Output::getClean($edit_server->rcon_name),
                    'EDIT_IP' => Output::getClean($edit_server->rcon_ip),
                    'EDIT_PORT' => Output::getClean($edit_server->rcon_port),
                    'EDIT_PASS' => Output::getClean($edit_server->rcon_pass)
                ]);


                $template_file = 'RconMC/edit_rcon_.tpl';

                break;

            case 'delete':
                if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                    try {

                        DB::getInstance()->delete('rcon_mc', ['id', '=', $_GET['id']]);
                    } catch (Exception $e) {
                        die($e->getMessage());
                    }

                    Session::flash('staff_rcon', $RconMCLanguage->get('general', 'deleted_successfully'));
                    Redirect::to(URL::build('/panel/rconmc'));
                }
                break;

            default:
                Redirect::to(URL::build('/panel/rconmc'));
        }
    }
}

$server_list = DB::getInstance()->get('rcon_mc', ['id', '<>', 0])->results();
$server_list_array = [];
if (count($server_list)) {
    foreach ($server_list as $server) {
        if ($user->hasPermission('admincp.rconmc')) {
            $server_list_array[] = [
                'send_rcon_link' => URL::build('/panel/rconmc/server', 'action=rcon&id=' . Output::getClean($server->id)),
                'edit_link' => URL::build('/panel/rconmc/', 'action=edit&id=' . Output::getClean($server->id)),
                'delete_link' => URL::build('/panel/rconmc/', 'action=delete&id=' . Output::getClean($server->id)),
                'server_name' => Output::getClean($server->rcon_name),
                'rcon_ip' => Output::getClean($server->rcon_ip),
                'rcon_port' => Output::getClean($server->rcon_port),
                'rcon_pass' => Output::getClean($server->rcon_pass)
            ];
        } else {
            if ($user->hasPermission('admincp.rcon.server.' . $server->id)) {
                $server_list_array[] = [
                    'send_rcon_link' => URL::build('/panel/rconmc/server', 'action=rcon&id=' . Output::getClean($server->id)),
                    'server_name' => Output::getClean($server->rcon_name),
                    'rcon_ip' => Output::getClean($server->rcon_ip),
                    'rcon_port' => Output::getClean($server->rcon_port),
                    'rcon_pass' => Output::getClean($server->rcon_pass)
                ];
            }
        }
    }
    $smarty->assign([
        'SERVER_LIST' => $server_list_array
    ]);
} else {
    $smarty->assign([
        'NO_SERVER' => $RconMCLanguage->get('general', 'no_servers')
    ]);
}



// Load modules + template
Module::loadPage($user, $pages, $cache, $smarty, [$navigation, $cc_nav, $staffcp_nav], $widgets, $template);
$template->onPageLoad();

if (Session::exists('staff_rcon'))
    $success = Session::flash('staff_rcon');

if (isset($success))
    $smarty->assign([
        'SUCCESS' => $success,
        'SUCCESS_TITLE' => $language->get('general', 'success')
    ]);

if (isset($errors) && count($errors))
    $smarty->assign([
        'ERRORS' => $errors,
        'ERRORS_TITLE' => $language->get('general', 'error')
    ]);

$smarty->assign([
    'TOKEN' => Token::get(),
]);

require(ROOT_PATH . '/core/templates/panel_navbar.php');

$template->displayTemplate($template_file, $smarty);
