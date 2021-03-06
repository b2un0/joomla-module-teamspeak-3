<?php

/**
 * @author     Branko Wilhelm <branko.wilhelm@gmail.com>
 * @link       http://www.z-index.net
 * @copyright  (c) 2014 Branko Wilhelm
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die;

JLoader::register('ModTeamspeak3Helper', __DIR__ . '/helper.php');

$params->set('layout', $params->get('layout', 'viewer'));

$data = ModTeamspeak3Helper::getData($params, $module);

if (is_string($data)) {
    echo $data;
    return;
}

require JModuleHelper::getLayoutPath($module->module, $params->get('layout', 'viewer'));
