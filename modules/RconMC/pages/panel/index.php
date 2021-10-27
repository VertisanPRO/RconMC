<?php
/*
 *  Made by Samerton
 *  https://github.com/NamelessMC/Nameless/tree/v2/
 *  NamelessMC version 2.0.0-pr7
 *
 *  License: MIT
 *
 *  RconMC By xGIGABAITx
 */

$RconMCLanguage = $GLOBALS['RconMCLanguage'];

if ($user->isLoggedIn()) {
	if (!$user->canViewStaffCP()) {

		Redirect::to(URL::build('/'));
		die();
	}
	if (!$user->isAdmLoggedIn()) {

		Redirect::to(URL::build('/panel/auth'));
		die();
	} else {
		if (!$user->hasPermission('admincp.rconmc')) {
			if (!$user->hasPermission('admincp.rcon.veiw')) {
				require_once(ROOT_PATH . '/403.php');
				die();
			} else {
				$smarty->assign(array(
					'USER_PERMISSION' => 1
				));
			}
		} else {
			$smarty->assign(array(
				'USER_PERMISSION' => 2
			));
		}
	}
} else {
	// Not logged in
	Redirect::to(URL::build('/login'));
	die();
}

define('PAGE', 'panel');
define('PARENT_PAGE', 'rcon_mc_items');
define('PANEL_PAGE', 'rcon_mc_items');



require_once(ROOT_PATH . '/core/templates/backend_init.php');
require_once(ROOT_PATH . '/modules/RconMC/classes/Rcon.php');


$smarty->assign(array(
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
));

$template_file = 'RconMC/index.tpl';


if ($user->hasPermission('admincp.rconmc')) {

	if (!isset($_GET['action'])) {

		if (Input::exists()) {
			$errors = array();
			if (Token::check(Input::get('token'))) {

				$validate = new Validate();
				$validation = $validate->check($_POST, array(
					'server_name' => array(
						'required' => true,
						'min' => 2,
						'max' => 32
					),
					'server_ip' => array(
						'required' => true,
						'min' => 2,
						'max' => 255
					),
					'server_pass' => array(
						'required' => true,
						'min' => 2,
						'max' => 255
					),
					'server_port' => array(
						'required' => true,
						'min' => 2,
						'max' => 10
					)
				));

				if ($validation->passed()) {
					try {

						$queries->create('rcon_mc', array(
							'rcon_name' => htmlspecialchars(Input::get('server_name')),
							'rcon_ip' => htmlspecialchars(Input::get('server_ip')),
							'rcon_port' => htmlspecialchars(Input::get('server_port')),
							'rcon_pass' => htmlspecialchars(Input::get('server_pass')),
						));

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
		}
	} else {
		switch ($_GET['action']) {

			case 'edit':

				if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
					Redirect::to(URL::build('/panel/rconmc'));
					die();
				}
				$edit_server = $queries->getWhere('rcon_mc', array('id', '=', $_GET['id']));
				if (!count($edit_server)) {
					Redirect::to(URL::build('/panel/rconmc'));
					die();
				}

				$edit_server = $edit_server[0];

				if (Input::exists()) {
					$errors = array();
					if (Token::check(Input::get('token'))) {

						$validate = new Validate();
						$validation = $validate->check($_POST, array(
							'server_name' => array(
								'required' => true,
								'min' => 2,
								'max' => 32
							),
							'server_ip' => array(
								'required' => true,
								'min' => 2,
								'max' => 255
							),
							'server_pass' => array(
								'required' => true,
								'min' => 2,
								'max' => 255
							),
							'server_port' => array(
								'required' => true,
								'min' => 2,
								'max' => 10
							)
						));

						if ($validation->passed()) {
							try {

								$queries->update('rcon_mc', $edit_server->id, array(
									'rcon_name' => htmlspecialchars(Input::get('server_name')),
									'rcon_ip' => htmlspecialchars(Input::get('server_ip')),
									'rcon_port' => htmlspecialchars(Input::get('server_port')),
									'rcon_pass' => htmlspecialchars(Input::get('server_pass')),
								));


								Session::flash('staff_rcon', $RconMCLanguage->get('general', 'edit_server_successfully'));
								Redirect::to(URL::build('/panel/rconmc'));
								die();
							} catch (Exception $e) {
								$errors[] = $e->getMessage();
							}
						} else {
							$errors[] = $RconMCLanguage->get('general', 'edit_errors');
						}
					} else {
						$errors[] = $language->get('general', 'invalid_token');
					}
				}

				$smarty->assign(array(
					'EDIT_SERVER' => $RconMCLanguage->get('general', 'server_edit_title'),
					'BACK_LINK' => URL::build('/panel/rconmc'),
					'EDIT_NAME' => Output::getClean($edit_server->rcon_name),
					'EDIT_IP' => Output::getClean($edit_server->rcon_ip),
					'EDIT_PORT' => Output::getClean($edit_server->rcon_port),
					'EDIT_PASS' => Output::getClean($edit_server->rcon_pass)
				));


				$template_file = 'RconMC/edit_rcon_.tpl';

				break;

			case 'delete':
				if (isset($_GET['id']) && is_numeric($_GET['id'])) {
					try {

						$queries->delete('rcon_mc', array('id', '=', $_GET['id']));
					} catch (Exception $e) {
						die($e->getMessage());
					}

					Session::flash('staff_rcon', $RconMCLanguage->get('general', 'deleted_successfully'));
					Redirect::to(URL::build('/panel/rconmc'));
					die();
				}
				break;

			default:
				Redirect::to(URL::build('/panel/rconmc'));
				die();
				break;
		}
	}
}

$server_list = $queries->getWhere('rcon_mc', array('id', '<>', 0));
$server_list_array = array();
if (count($server_list)) {
	foreach ($server_list as $server) {
		if ($user->hasPermission('admincp.rconmc')) {
			$server_list_array[] = array(
				'send_rcon_link' => URL::build('/panel/rconmc/server', 'action=rcon&id=' . Output::getClean($server->id)),
				'edit_link' => URL::build('/panel/rconmc/', 'action=edit&id=' . Output::getClean($server->id)),
				'delete_link' => URL::build('/panel/rconmc/', 'action=delete&id=' . Output::getClean($server->id)),
				'server_name' => Output::getClean($server->rcon_name),
				'rcon_ip' => Output::getClean($server->rcon_ip),
				'rcon_port' => Output::getClean($server->rcon_port),
				'rcon_pass' => Output::getClean($server->rcon_pass)
			);
		} else {
			if ($user->hasPermission('admincp.rcon.server.' . $server->id)) {
				$server_list_array[] = array(
					'send_rcon_link' => URL::build('/panel/rconmc/server', 'action=rcon&id=' . Output::getClean($server->id)),
					'server_name' => Output::getClean($server->rcon_name),
					'rcon_ip' => Output::getClean($server->rcon_ip),
					'rcon_port' => Output::getClean($server->rcon_port),
					'rcon_pass' => Output::getClean($server->rcon_pass)
				);
			} else {
				continue;
			}
		}
	}
	$smarty->assign(array(
		'SERVER_LIST' => $server_list_array
	));
} else {
	$smarty->assign(array(
		'NO_SERVER' => $RconMCLanguage->get('general', 'no_servers')
	));
}



// Load modules + template
Module::loadPage($user, $pages, $cache, $smarty, array($navigation, $cc_nav, $mod_nav), $widgets, $template);
$page_load = microtime(true) - $start;
define('PAGE_LOAD_TIME', str_replace('{x}', round($page_load, 3), $language->get('general', 'page_loaded_in')));
$template->onPageLoad();

if (Session::exists('staff_rcon'))
	$success = Session::flash('staff_rcon');

if (isset($success))
	$smarty->assign(array(
		'SUCCESS' => $success,
		'SUCCESS_TITLE' => $language->get('general', 'success')
	));

if (isset($errors) && count($errors))
	$smarty->assign(array(
		'ERRORS' => $errors,
		'ERRORS_TITLE' => $language->get('general', 'error')
	));

$smarty->assign(array(
	'TOKEN' => Token::get(),
));

require(ROOT_PATH . '/core/templates/panel_navbar.php');

$template->displayTemplate($template_file, $smarty);
