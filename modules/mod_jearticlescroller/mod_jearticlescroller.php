<?php
/**
* @package   mod_jearticlescroller
* @copyright Copyright (C) 2009-2010 Joomlaextensions.co.in All rights reserved.
* @license   http://www.gnu.org/licenses/lgpl.html GNU/LGPL, see LICENSE.php
* Contact to : emailtohardik@gmail.com, joomextensions@gmail.com
**/

// no direct access
defined('_JEXEC') or die('Restricted access');

// Include the syndicate functions only once
require_once (dirname(__FILE__).'/'.'helper.php');

$number = $params->get('number', 5);
$width = $params->get('width', 100);
$height = $params->get('height',60);
$showimage = intval($params->get('show_image',"1"));
$loadjquery = intval($params->get('show_jquery',"1"));

$list = modJeArticleScrollerHelper::getList($params);
require(JModuleHelper::getLayoutPath('mod_jearticlescroller'));