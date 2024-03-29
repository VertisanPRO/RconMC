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

$RconMCLanguage = new Language(ROOT_PATH . '/modules/' . 'RconMC' . '/language', LANGUAGE);
$GLOBALS['RconMCLanguage'] = $RconMCLanguage;

require_once(ROOT_PATH . '/modules/' . 'RconMC' . '/module.php');

$module = new RconMC_Module($language, $pages);