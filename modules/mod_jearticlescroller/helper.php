<?php
/**
* @package   mod_jearticlescroller
* @copyright Copyright (C) 2009-2010 Joomlaextensions.co.in All rights reserved.
* @license   http://www.gnu.org/licenses/lgpl.html GNU/LGPL, see LICENSE.php
* Contact to : emailtohardik@gmail.com, joomextensions@gmail.com
**/

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once (JPATH_SITE.'/'.'components'.'/'.'com_content'.'/'.'helpers'.'/'.'route.php');

class modJeArticleScrollerHelper
{
	function getList(&$params)
	{
		//global $mainframe;
		
		$db			=& JFactory::getDBO();
		$user		=& JFactory::getUser();
		$userId		= (int) $user->get('id');

		$count		= (int) $params->get('count', 5);
		$catid		= trim( $params->get('catid') );
		$show_front	= $params->get('show_front', 1);
		
		$aid		= $user->get('aid', 0);
		
		$contentConfig 	= &JComponentHelper::getParams( 'com_content' );
		$access			= !$contentConfig->get('show_noauth');

		$nullDate	= $db->getNullDate();

		$date =& JFactory::getDate();
		$jdate = new JDate;
		$now = $jdate->toSql();

		$where		= 'a.state = 1'
			. ' AND ( a.publish_up = '.$db->Quote($nullDate).' OR a.publish_up <= '.$db->Quote($now).' )'
			. ' AND ( a.publish_down = '.$db->Quote($nullDate).' OR a.publish_down >= '.$db->Quote($now).' )'
			;
		
		// User Filter
		switch ($params->get( 'user_id' ))
		{
			case 'by_me':
				$where .= ' AND (created_by = ' . (int) $userId . ' OR modified_by = ' . (int) $userId . ')';
				break;
			case 'not_me':
				$where .= ' AND (created_by <> ' . (int) $userId . ' AND modified_by <> ' . (int) $userId . ')';
				break;
		}
		// Ordering
		switch ($params->get( 'ordering' ))
		{
			case 'm_dsc':
				$ordering		= 'a.modified DESC, a.created DESC';
				break;
			case 'c_dsc':
			default:
				$ordering		= 'a.created DESC';
				break;
		}

		if ($catid)
		{
			$ids = explode( ',', $catid );
			JArrayHelper::toInteger( $ids );
			$catCondition = ' AND (cc.id=' . implode( ' OR cc.id=', $ids ) . ')';
		}
		
		// Content Items only
		$query = 'SELECT a.*, ' .
			' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug,'.
			' CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug'.
			' FROM #__content AS a' .
			($show_front == '0' ? ' LEFT JOIN #__content_frontpage AS f ON f.content_id = a.id' : '') .
			' INNER JOIN #__categories AS cc ON cc.id = a.catid' .
			' WHERE '. $where .
			($access ? ' AND a.access >= ' .(int) $aid. ' AND cc.access >= ' .(int) $aid : '').
			($catid ? $catCondition : '').
			($show_front == '0' ? ' AND f.content_id IS NULL ' : '').
			' AND cc.published = 1' .
			' ORDER BY '. $ordering;
			
		$db->setQuery($query, 0, $count);
		$rows = $db->loadObjectList();
		
		$i		= 0;
		$lists	= array();
		foreach ( $rows as $row )
		{
			$introtext = $row->introtext;
			preg_match_all('/<img[^>]*>/',$introtext,$images);
			$introtext = preg_replace( '/<img[^>]*>/', '', $introtext );
		
		// ++++++++++++++++++++++++++++++ Modify Code ++++++++++++++++++++++++++++++++++++ //
			if( isset($images[0][0]) ) {
				if($images[0][0] != ""){
					$images[0][0] = preg_replace('/width=\"(.*?)\"/', '', $images[0][0]);
					$images[0][0] = preg_replace('/width=\'(.*?)\'/', '', $images[0][0]);
					$images[0][0] = preg_replace('/height=\"(.*?)\"/', '', $images[0][0]);
					$images[0][0] = preg_replace('/height=\'(.*?)\'/', '', $images[0][0]);
				} 
			} else {
				$images[0][0] = '';
			}
		// ++++++++++++++++++++++++++++++ EOF Modify Code ++++++++++++++++++++++++++++++++ //
		
			$lists[$i]->thumb = $images[0][0];
			
			$lists[$i]->introtext = modJeArticleScrollerHelper::truncString(modJeArticleScrollerHelper::cleanHtml($row->introtext),$params->get('number_intro', 40));
			if($row->access >= $aid)
			{
				$lists[$i]->link = JRoute::_(ContentHelperRoute::getArticleRoute($row->slug, $row->catslug, $row->sectionid));
			} else {
				$lists[$i]->link = JRoute::_('index.php?option=com_users&view=login');
			}
			$lists[$i]->text = htmlspecialchars( $row->title );
			$i++;
		}
		
		return $lists;
		
	}
	
	function truncString($str = "", $len = 150, $more = 'true') {
			if ($str == "") return $str;
			if (is_array($str)) return $str;
			$str = trim($str);
			// if it's les than the size given, then return it
			if (strlen($str) <= $len) return $str;
			// else get that size of text
			$str = substr($str, 0, $len);
			// backtrack to the end of a word
			if ($str != "") {
			  // check to see if there are any spaces left
			  if (!substr_count($str , " ")) {
				if ($more == 'true') $str .= "...";
				return $str;
			  }
			  // backtrack
			  while(strlen($str) && ($str[strlen($str)-1] != " ")) {
				$str = substr($str, 0, -1);
			  }
			  $str = substr($str, 0, -1);
			  if ($more == 'true') $str .= "...";
			  if ($more != 'true' and $more != 'false') $str .= $more;
			}
			return $str;
  }
  
  function cleanHtml($clean_it) {
	$clean_it = preg_replace('/\r/', ' ', $clean_it);
	$clean_it = preg_replace('/\t/', ' ', $clean_it);
	$clean_it = preg_replace('/\n/', ' ', $clean_it);
	
	$clean_it= nl2br($clean_it);
// update breaks with a space for text displays in all listings with descriptions
	while (strstr($clean_it, '<br>')) $clean_it = str_replace('<br>', ' ', $clean_it);
	while (strstr($clean_it, '<br />')) $clean_it = str_replace('<br />', ' ', $clean_it);
	while (strstr($clean_it, '<br/>')) $clean_it = str_replace('<br/>', ' ', $clean_it);
	while (strstr($clean_it, '<p>')) $clean_it = str_replace('<p>', ' ', $clean_it);
	while (strstr($clean_it, '</p>')) $clean_it = str_replace('</p>', ' ', $clean_it);
// temporary fix more for reviews than anything else
	while (strstr($clean_it, '<span class="smallText">')) $clean_it = str_replace('<span class="smallText">', ' ', $clean_it);
	while (strstr($clean_it, '</span>')) $clean_it = str_replace('</span>', ' ', $clean_it);
	while (strstr($clean_it, '  ')) $clean_it = str_replace('  ', ' ', $clean_it);
// remove other html code to prevent problems on display of text
	$clean_it = strip_tags($clean_it);
	return $clean_it;
  }
}
