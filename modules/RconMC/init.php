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

$INFO_MODULE = array(
    'name' => 'RconMC',
    'author' => '<a href="https://github.com/GIGABAIT-Official" target="_blank" rel="nofollow noopener">VertisanPRO</a>',
    'module_ver' => '1.3.0',
    'nml_ver' => '2.0.0-pr13',
);

$RconMCLanguage = new Language(ROOT_PATH . '/modules/' . $INFO_MODULE['name'] . '/language', LANGUAGE);

$GLOBALS['RconMCLanguage'] = $RconMCLanguage;

require_once(ROOT_PATH . '/modules/' . $INFO_MODULE['name'] . '/module.php');

$module = new RconMC_Module($language, $pages, $INFO_MODULE);
