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

class RconMC_Module extends Module
{

    private $_language, $RconMCLanguage;

    public function __construct($language, $pages)
    {
        $this->_language = $language;

        $this->RconMCLanguage = $GLOBALS['RconMCLanguage'];

        $name = 'RconMC';
        $author = '<a href="https://github.com/VertisanPRO" target="_blank" rel="nofollow noopener">VertisanPRO</a>';
        $module_version = '1.3.1';
        $nameless_version = '2.1.0';
        parent::__construct($this, $name, $author, $module_version, $nameless_version);

        $pages->add('RconMC', '/panel/rconmc', 'pages/panel/index.php');
        $pages->add('RconMC', '/panel/rconmc/server', 'pages/panel/rcon.php');
    }

    public function onInstall()
    {
        // Queries
        $queries = new Queries();

        try {
            $data = DB::getInstance()->createTable("rcon_mc", " `id` int(11) NOT NULL AUTO_INCREMENT, `rcon_name` varchar(30) NOT NULL, `rcon_ip` varchar(30) NOT NULL, `rcon_port` int(11) NOT NULL, `rcon_pass` varchar(255) NOT NULL, PRIMARY KEY (`id`)");
        } catch (Exception $e) {
            // Error
        }
    }

    public function onUninstall()
    {
    }

    public function onEnable()
    {

        $queries = new Queries();

        try {

            $group = DB::getInstance()->get('groups', ['id', '=', 2])->results();
            $group = $group[0];

            $group_permissions = json_decode($group->permissions, TRUE);
            $group_permissions['admincp.rconmc'] = 1;

            $group_permissions = json_encode($group_permissions);
            DB::getInstance()->update('groups', 2, ['permissions' => $group_permissions]);
        } catch (Exception $e) {
            // Ошибка
        }
    }

    public function onDisable()
    {
    }

    public function onPageLoad($user, $pages, $cache, $smarty, $navs, $widgets, $template)
    {

        PermissionHandler::registerPermissions('RconMC', [
            'admincp.rconmc' => $this->RconMCLanguage->get('general', 'group_permision')
        ]);
        PermissionHandler::registerPermissions('RconMC', [
            'admincp.rcon.veiw' => $this->RconMCLanguage->get('general', 'server_veiw')
        ]);

        $icon = '<i class="fa fa-terminal" aria-hidden="true"></i>';

        if (defined('BACK_END')) {

            $rcon_list = DB::getInstance()->get('rcon_mc', ['id', '<>', 0])->results();

            foreach ($rcon_list as $value) {
                PermissionHandler::registerPermissions('RconMC', [
                    'admincp.rcon.server.' . $value->id => $this->RconMCLanguage->get('general', 'server_permision') . $value->rcon_name
                ]);
            }



            $rcon_mc_name = $this->RconMCLanguage->get('general', 'rcon_name');

            if ($user->hasPermission('admincp.rconmc') or $user->hasPermission('admincp.rcon.veiw')) {
                $cache->setCache('panel_sidebar');
                if (!$cache->isCached('rcon_mc_order')) {
                    $order = 50;
                    $cache->store('rcon_mc_order', 50);
                } else {
                    $order = $cache->retrieve('rcon_mc_order');
                }

                if (!$cache->isCached('rcon_icon')) {
                    $cache->store('rcon_icon', $icon);
                } else {
                    $icon = $cache->retrieve('rcon_icon');
                }

                $navs[2]->add('rcon_mc_divider', mb_strtoupper($rcon_mc_name, 'UTF-8'), 'divider', 'top', null, $order, '');

                $navs[2]->add('rcon_mc_items', $rcon_mc_name, URL::build('/panel/rconmc'), 'top', null, $order + 0.1, $icon);
            }
        }
    }

    public function getDebugInfo(): array
    {
        return [];
    }
}