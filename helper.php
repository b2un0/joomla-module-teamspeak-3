<?php

/**
 * @author     Branko Wilhelm <branko.wilhelm@gmail.com>
 * @link       http://www.z-index.net
 * @copyright  (c) 2014 Branko Wilhelm
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @see        http://addons.teamspeak.com/directory/addon/integration/TeamSpeak-3-PHP-Framework.html
 */

defined('_JEXEC') or die;

JLoader::register('TeamSpeak3', JPATH_LIBRARIES . '/TeamSpeak3/TeamSpeak3.php');

TeamSpeak3::init();

class ModTeamspeak3ViewerHelper
{
    public static function getData(JRegistry &$params, stdClass &$module)
    {
        if (!$params->get('server_host') || !$params->get('server_port') || !$params->get('query_port') || !$params->get('query_login') || !$params->get('query_password')) {
            return JText::_('MOD_TEAMSPEAK3_BASIC_CONFIGURATION_MISSING');
        }

        $cache = JFactory::getCache('teamspeak3', 'output');
        $cache->setCaching(1);
        $cache->setLifeTime($params->get('cache_time', 5));

        $query = array();
        $query['server_port'] = $params->get('server_port');
        $query['timeout'] = $params->get('connection_timeout', 10);
        if ($params->get('no_query_clients', 1)) {
            $query['no_query_clients'] = 1;
        }

        $query = http_build_query($query);

        $url = 'serverquery://' . $params->get('query_login') . ':' . $params->get('query_password') . '@' . $params->get('server_host') . ':' . $params->get('query_port') . '/?' . $query;

        $key = md5($url);

        if (!$data = $cache->get($key)) {
            try {
                $ts3 = TeamSpeak3::factory($url);
            } catch (TeamSpeak3_Exception $e) {
                return $e->getMessage() . ' (' . $e->getCode() . ')';
            }

            $html = new TeamSpeak3_Viewer_Html_Joomla($params, $module);

            $data = new stdClass;
            $data->infos = $ts3->getInfo(true, true);

            if ($params->get('channel_id')) {
                try {
                    $channel = $ts3->channelGetById($params->get('channel_id'));
                } catch (TeamSpeak3_Exception $e) {
                    return $e->getMessage() . ' (' . $e->getCode() . ')';
                }

                $data->viewer = $channel->getViewer($html);
            } else {
                $data->viewer = $ts3->getViewer($html);
            }

            $cache->store($data, $key);
        }

        return $data;
    }

    public static function infoString($str, $type)
    {
        $str = (string)$str;

        switch ($type) {
            case 'virtualserver_created':
                return JHtml::_('date', $str, JText::_('DATE_FORMAT_LC2'));
                break;

            case 'virtualserver_flag_password':
                return ($str == 1) ? JText::_('JYES') : JText::_('JNO');
                break;

            default:
                return $str;
                break;
        }
    }
}

class TeamSpeak3_Viewer_Html_Joomla extends TeamSpeak3_Viewer_Html
{
    protected $pattern = '<table id="%0" class="%1" summary="%2"><tr class="%3"><td class="%4">%5</td><td class="%6" title="%7">%8 <span>%9</span></td><td class="%10">%11%12</td></tr></table>';

    protected $params;

    protected $module;

    protected $linkPrefix;

    public function __construct(JRegistry &$params, stdClass &$module)
    {
        $this->params = $params;

        $this->module = $module;

        $this->linkPrefix = 'ts3server://' . $this->params->get('server_host') . '?port=' . $this->params->get('server_port');

        if ($this->params->get('server_password')) {
            $this->linkPrefix .= '&amp;=password=' . $this->params->get('server_password');
        }

        $images = JUri::base(true) . '/media/mod_teamspeak3/images';

        parent::__construct($images . '/', $images . '/flags/', 'data:image');
    }

    public function fetchObject(TeamSpeak3_Node_Abstract $node, array $siblings = array())
    {
        $obj = parent::fetchObject($node, $siblings);

        if ($this->HideCurChannel()) {
            return '';
        }

        if ($this->HideCurCLient()) {
            return '';
        }

        if ($this->params->get('module_title') && $this->currObj instanceof TeamSpeak3_Node_Server) {
            $this->module->title = $obj;
            return '';
        }

        return $obj;
    }

    private function HideCurChannel()
    {
        if ($this->currObj instanceof TeamSpeak3_Node_Channel) {
            if ($this->params->get('channel_hide')) {
                $channel_hide = explode(',', $this->params->get('channel_hide'));
                JArrayHelper::toInteger($channel_hide);

                return in_array($this->currObj->getId(), $channel_hide);
            }
        }
    }

    private function HideCurCLient()
    {
        if ($this->currObj instanceof TeamSpeak3_Node_Client) {
            if ($this->params->get('client_hide')) {
                $client_hide = explode(',', $this->params->get('client_hide'));
                JArrayHelper::toInteger($client_hide);

                return in_array($this->currObj->getId(), $client_hide);
            }
        }
    }

    protected function getCorpusName()
    {
        $name = parent::getCorpusName();

        if ($this->params->get('join_links')) {
            if ($this->currObj instanceof TeamSpeak3_Node_Channel && !$this->currObj->isSpacer()) {
                $name = JHtml::_('link', $this->linkPrefix . '&amp;channel=' . rawurlencode((string)$this->currObj->getPathway()), $name);
            }

            if ($this->currObj instanceof TeamSpeak3_Node_Server) {
                $name = JHtml::_('link', $this->linkPrefix, $name);
            }
        }

        return $name;
    }
}