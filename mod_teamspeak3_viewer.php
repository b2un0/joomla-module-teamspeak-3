<?php

/**
 * @author     Branko Wilhelm <branko.wilhelm@gmail.com>
 * @link       http://www.z-index.net
 * @copyright  (c) 2014 Branko Wilhelm
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die;

JLoader::register('TeamSpeak3', JPATH_LIBRARIES . '/TeamSpeak3/TeamSpeak3.php');

TeamSpeak3::init();

JLoader::register('ModTeamspeak3ViewerHelper', dirname(__FILE__) . '/helper.php');

$ts3 = ModTeamspeak3ViewerHelper::getData($params);

if (is_string($ts3)) {
    echo $ts3;
    return;
}

require JModuleHelper::getLayoutPath($module->module, $params->get('layout', 'default'));