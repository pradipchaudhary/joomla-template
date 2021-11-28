<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

require_once (JPATH_SITE.'/components/com_content/helpers/route.php');
require_once (dirname(__FILE__).'/helper.php');

jimport('joomla.filesystem.file');

jimport('syw.tags');
jimport('syw.cache');
jimport('syw.text');

class modLatestNewsEnhancedExtendedHelperStandard
{		
	/**
	 * 
	 * @param unknown $params
	 * @param unknown $module
	 * @param unknown $items
	 * @throws Exception
	 * @return array of categories (id, description, article count)
	 */
	static function getCategoryList($params, $items)
	{
		$categories = array();
		
		// get all categories and how many articles are in them
		foreach ($items as $item) {
			if (array_key_exists($item->catid, $categories)) {
				$categories[$item->catid]++;
			} else {
				$categories[$item->catid] = 1;
			}
		}
		
		if ($params->get('show_cat_description', 0)) { // need description
			
			$categories_string = implode(',', array_keys($categories));		
			
			$db = JFactory::getDbo();
			
			$query = $db->getQuery(true);
			
			$query->select($db->quoteName('id'));
			$query->select($db->quoteName('description'));
			$query->from($db->quoteName('#__categories'));
			$query->where($db->quoteName('id').' IN ('.$categories_string.')');
			
			$db->setQuery($query);
			
			try {
				$categories_list = $db->loadObjectList('id');
			} catch (RuntimeException $e) {
				$app->enqueueMessage(JText::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
				return null;
			}
		
			foreach ($categories_list as $category) {
				$category->count = $categories[$category->id];	
			}
		} else {
			$categories_list = array();
			
			foreach ($categories as $key => $value) {
				$categories_list[$key] = (Object) array('id' => $key, 'count' => $value);
			}
		}
		
		return $categories_list;
	}
	
	static function getList($params, $module)
	{
		$db = JFactory::getDbo();		
		$app = JFactory::getApplication();
		
		$jinput = $app->input;		
		$option = $jinput->get('option');
		$view = $jinput->get('view');
		
		if (!$params->get('show_on_item_page', 1)) {
			if ($option === 'com_content' && $view === 'article') {
				return null;
			}
		}
		
		$query = $db->getQuery(true);
		
		$item_on_page_id = '';
		$item_on_page_tagids = array();
		$item_on_page_keys = array();
		
		$related = $params->get('related', 0); // 0: no, 1: keywords, 2: tags articles only, 3: tags any content
				
		if ($related == 1) { // related by keyword
			
			if ($option === 'com_content' && $view === 'article') {
				$temp = $jinput->getString('id');
				$temp = explode(':', $temp);
				$item_on_page_id = $temp[0];
			}
			
			if ($item_on_page_id) {
				
				$query->select($db->quoteName('metakey'));
				$query->from($db->quoteName('#__content'));
				$query->where($db->quoteName('id').' = '.$item_on_page_id);
				
				$db->setQuery($query);
				
				try {
					$result = $db->loadResult();
				} catch (RuntimeException $e) {
					$app->enqueueMessage(JText::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
					return null;
				}
			
				$result = trim($result);
				if (empty($result)) {
					return array(); // won't find a related article if no key is present
				}
				
				$keys = explode(',', $result);
				
				// assemble any non-blank word(s)
				foreach ($keys as $key) {
					$key = trim($key);
					if ($key) {
						$item_on_page_keys[] = $key;
					}
				}
					
				if (empty($item_on_page_keys)) {
					return array();
				}
				
				$query->clear();
			} else {
				return null; // no result (was not on article page)
			}
		
		} else if ($related == 2 || $related == 3) { // related by tag
			
			$get_the_tags = false;
			if ($related == 2 && $option === 'com_content' && $view === 'article') {
				$get_the_tags = true;
			} else if ($related == 3) { // no restriction on the type of content
				$get_the_tags = true;
			
				if ($option === 'com_trombinoscopeextended' && $view === 'contact') { // because tags are recorded with com_contact
					$option = 'com_contact';
				}
			}
			
			if ($get_the_tags) {
				$temp = $jinput->getString('id');
				$temp = explode(':', $temp);
				$item_on_page_id = $temp[0];
			
				if ($item_on_page_id) {
					$helper_tags = new JHelperTags;
					$tag_objects = $helper_tags->getItemTags($option.'.'.$view, $item_on_page_id); // array of tag objects
					foreach ($tag_objects as $tag_object) {
						$item_on_page_tagids[] = $tag_object->tag_id;
					}
				}
			
				if (empty($item_on_page_tagids)) {
					return array(); // no result because no tag found for the object on the page
				}
			} else {
				return null; // no result (was not on article page)
			}
		}
		
		// START OF DATABASE QUERY
		
		$fulltext_query = 'a.fulltext, ';		
		
		$subquery1 = ' CASE WHEN ';
		$subquery1 .= $query->charLength('a.alias');
		$subquery1 .= ' THEN ';
		$a_id = $query->castAsChar('a.id');
		$subquery1 .= $query->concatenate(array($a_id, 'a.alias'), ':');
		$subquery1 .= ' ELSE ';
		$subquery1 .= $a_id.' END AS slug';
		
		$subquery2 = ' CASE WHEN ';
		$subquery2 .= $query->charLength('c.alias');
		$subquery2 .= ' THEN ';
		$c_id = $query->castAsChar('c.id');
		$subquery2 .= $query->concatenate(array($c_id, 'c.alias'), ':');
		$subquery2 .= ' ELSE ';
		$subquery2 .= $c_id.' END AS cat_slug';
		
		$query->select('DISTINCT a.id, a.title, a.alias, a.introtext, '.$fulltext_query.
				
			'CASE WHEN a.fulltext IS NULL OR a.fulltext = \'\' THEN 0 ELSE 1 END AS fulltexthascontent, '.
			
			'a.checked_out, a.checked_out_time, '.
			'a.catid, a.created, a.created_by, a.created_by_alias, '.
			// Use created if modified is 0
			'CASE WHEN a.modified = '.$db->quote($db->getNullDate()).' THEN a.created ELSE a.modified END as modified, '.
			'a.modified_by, uam.name as modified_by_name,'.
			// Use created if publish_up is 0
			'CASE WHEN a.publish_up = '.$db->quote($db->getNullDate()).' THEN a.created ELSE a.publish_up END as publish_up, '.
			'a.publish_down, a.images, a.urls, a.attribs, a.metadata, a.metakey, a.metadesc, a.access, a.hits, a.featured, a.language');
		
		$published = 1; // 'state = 1' only published for now
		
		// Process an Archived Article layout
		if ($published == 2) {
			// If badcats is not null, this means that the article is inside an archived category
			// In this case, the state is set to 2 to indicate Archived (even if the article state is Published)
			$query->select('CASE WHEN badcats.id is null THEN a.state ELSE 2 END AS state');
		} else {
			// Process non-archived layout
			// If badcats is not null, this means that the article is inside an unpublished category
			// In this case, the state is set to 0 to indicate Unpublished (even if the article state is Published)
			$query->select('CASE WHEN badcats.id is not null THEN 0 ELSE a.state END AS state');
		}
		
		$query->select($subquery1);
		$query->select($subquery2);
		
		$query->from('#__content AS a');		
		
		// join over the categories
		$query->select('c.title AS category_title, c.path AS category_route, c.access AS category_access, c.alias AS category_alias');
		$query->join('LEFT', '#__categories AS c ON c.id = a.catid');
		
		// join over the users for the author and modified_by names
		switch ($params->get('show_a', 'alias')) {
			case 'full': $query->select("ua.name AS author"); break;
			case 'user': $query->select("ua.username AS author"); break;
			default: $query->select("CASE WHEN a.created_by_alias > ' ' THEN a.created_by_alias ELSE ua.name END AS author");
		}
		
		$query->select("ua.email AS author_email");

		$query->join('LEFT', '#__users AS ua ON ua.id = a.created_by');
		
		$query->join('LEFT', '#__users AS uam ON uam.id = a.modified_by');
		
		// join over the categories to get parent category titles
		$query->select('parent.title as parent_title, parent.id as parent_id, parent.path as parent_route, parent.alias as parent_alias');
		$query->join('LEFT', '#__categories as parent ON parent.id = c.parent_id');
		
		// join on voting table
		$query->select('ROUND(v.rating_sum / v.rating_count, 1) AS rating, v.rating_count as rating_count');
		$query->join('LEFT', '#__content_rating AS v ON a.id = v.content_id');
		
		// join to check for category published state in parent categories up the tree
		$query->select('c.published, CASE WHEN badcats.id is null THEN c.published ELSE 0 END AS parents_published');
		
		$subquery = 'SELECT cat.id as id FROM #__categories AS cat JOIN #__categories AS parent ';
		$subquery .= 'ON cat.lft BETWEEN parent.lft AND parent.rgt ';
		$subquery .= 'WHERE parent.extension = ' . $db->quote('com_content');
				
		if ($published == 2) {
			// Find any up-path categories that are archived
			// If any up-path categories are archived, include all children in archived layout
			$subquery .= ' AND parent.published = 2 GROUP BY cat.id ';
			// Set effective state to archived if up-path category is archived
			$publishedWhere = 'CASE WHEN badcats.id is null THEN a.state ELSE 2 END';
		} else {
			// Find any up-path categories that are not published
			// If all categories are published, badcats.id will be null, and we just use the article state
			$subquery .= ' AND parent.published != 1 GROUP BY cat.id ';
			// Select state to unpublished if up-path category is unpublished
			$publishedWhere = 'CASE WHEN badcats.id is null THEN a.state ELSE 0 END';
		}
		
		$query->join('LEFT OUTER', '(' . $subquery . ') AS badcats ON badcats.id = c.id');
		
		if (is_numeric($published)) {
			// Use article state if badcats.id is null, otherwise, force 0 for unpublished
			$query->where($publishedWhere . ' = ' . (int) $published);
		} elseif (is_array($published)) {
			JArrayHelper::toInteger($published);
			$published = implode(',', $published);
			// Use article state if badcats.id is null, otherwise, force 0 for unpublished
			$query->where($publishedWhere . ' IN ('.$published.')');
		}
		
		//$query->where('a.state = 1'); // needed ? NO
		//$query->where('c.published = 1');
		
		// access filter
			
		$user = JFactory::getUser();
		$groups	= implode(',', $user->getAuthorisedViewLevels());
				
		//$access = !JComponentHelper::getParams('com_content')->get('show_noauth'); // for links only
		$authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
		
		$show_unauthorized_items = false; // do not allow to show unauthorized items in the free version
		
		//if ($access) {
		if (!$show_unauthorized_items) { // show authorized items only
			$query->where('a.access IN ('.$groups.')');
			$query->where('c.access IN ('.$groups.')');
		}
		
		// filter by start and end dates
		
		$nullDate = $db->quote($db->getNullDate());
		$nowDate = $db->quote(JFactory::getDate()->toSql());		
		
		$postdate = $params->get('post_d', 'published');
		
		if ($postdate != 'fin_pen' && $postdate != 'pending') {
			$query->where('(a.publish_up = '.$nullDate.' OR a.publish_up <= '.$nowDate.')');
		}
		if ($postdate == 'pending') {
			$query->where('a.publish_up > ' . $nowDate);
		}
		$query->where('(a.publish_down = '.$nullDate.' OR a.publish_down >= '.$nowDate.')');
					
		// filter by date range
		
		switch ($postdate)
		{
			case 'created' : $dateField = 'a.created'; $query->select($db->quoteName('a.created', 'date')); break;
			case 'modified' : $dateField = 'a.modified'; $query->select($db->quoteName('a.modified', 'date')); break;
			case 'finished' : case 'fin_pen' : case 'pending' : $dateField = 'a.publish_down'; $query->select($db->quoteName('a.publish_down', 'date')); break;
			default: $dateField = 'a.publish_up'; $query->select($db->quoteName('a.publish_up', 'date'));
		}
		
		switch ($params->get('use_range', 0))
		{
			case 1: // relative
				$range_from = $params->get('range_from', 'now'); // now, day, week, month, year
				$spread_from = $params->get('spread_from', 1);
				$range_to = $params->get('range_to', 'week');
				$spread_to = $params->get('spread_to', 1);
		
				if ($range_from == 'now') {
					$query->where($dateField.' <= '.$nowDate);
				} else {
					if ($dateField != 'a.publish_down') {
						$query->where($dateField.' <= DATE_SUB('.$nowDate.', INTERVAL '.$spread_from.' '.$range_from.')');
					} else {
						$query->where($dateField.' <= DATE_ADD('.$nowDate.', INTERVAL '.$spread_from.' '.$range_from.')');
					}
				}
				if ($range_to == 'now') {
					$query->where($dateField.' >= '.$nowDate);
				} else {
					if ($dateField != 'a.publish_down') {
						$query->where($dateField.' >= DATE_SUB('.$nowDate.', INTERVAL '.$spread_to.' '.$range_to.')');
					} else {
						$query->where($dateField.' >= DATE_ADD('.$nowDate.', INTERVAL '.$spread_to.' '.$range_to.')');
					}
				}
			break;
					
			case 2: // range
				$startDateRange = $db->quote($params->get('start_date_range', $db->getNullDate()));
				$endDateRange = $db->quote($params->get('end_date_range', $db->getNullDate()));
		
				$query->where('('.$dateField.' >= '.$startDateRange.' AND '.$dateField.' <= '.$endDateRange.')');
			break;
		}
					
		// category filter
		
		$categories_array = $params->get('catid', array());
	
		$array_of_category_values = array_count_values($categories_array);
		if (isset($array_of_category_values['all']) && $array_of_category_values['all'] > 0) { // 'all' was selected therefore no filtering
			// take everything, so no category selection
		} else {
			if (isset($array_of_category_values['auto']) && $array_of_category_values['auto'] > 0) { // 'auto' was selected
			
				$categories_array = array();
				
				if ($option === 'com_content') {
					switch($view) 
					{
						case 'categories':
							$categories_array[] = $jinput->getInt('id'); // id is the top-level category (can be 0 if 'root' has been selected)
						break;
						case 'category':
							$categories_array[] = $jinput->getInt('id');
						break;					
						case 'article':
							//if ($params->get('show_on_item_page', 1)) { // useless since test before
							
								$article_id = $jinput->getInt('id');
								$catid = $jinput->getInt('catid');
							
								if (!$catid) {
									// Get an instance of the generic article model
									$article = JModelLegacy::getInstance('Article', 'ContentModel', array('ignore_request' => true));
							
									$article->setState('params', $app->getParams());
									$article->setState('filter.published', 1);
									$article->setState('article.id', (int) $article_id);
									$item = $article->getItem();
									$categories_array[] = $item->catid;
								} else {
									$categories_array[] = $catid;
								}
							//}
						break;
					}
				}
			
				if (empty($categories_array)) {
					return null; // no result if not in the category page
				}
			}
			
			if (!empty($categories_array)) {
			
				// sub-category inclusion
				$get_sub_categories = $params->get('includesubcategories', 'no');
				if ($get_sub_categories != 'no') {
					
					$levels = $params->get('levelsubcategories', 1);
						
					if (!$show_unauthorized_items) {
						$categories_object = JCategories::getInstance('Content');
					} else {
						$categories_object = JCategories::getInstance('Content', array('access' => false));
					}
					foreach ($categories_array as $category) {
						$category_object = $categories_object->get($category); // if category unpublished, unset
						if (isset($category_object) && $category_object->hasChildren()) {
								
							$sub_categories_array = $category_object->getChildren(true); // get all levels recursively
							foreach ($sub_categories_array as $subcategory_object) {
								$condition = ($get_sub_categories == 'all' || ($subcategory_object->level - $category_object->level) <= $levels);
								if ($condition) {
									$categories_array[] = $subcategory_object->id;
								}
							}
						}
					}
					$categories_array = array_unique($categories_array);
				}
			
				$categories = implode(',', $categories_array);
				
				$test_type = $params->get('cat_inex', 1) ? 'IN' : 'NOT IN';
				
				$query->where('a.catid '.$test_type.' ('.$categories.')');
			}			
		}
		
		// metakeys filter
		
		$metakeys = array();
		$keys = explode(',', $params->get('keys', ''));
		
		// assemble any non-blank word(s)
		foreach ($keys as $key) {
			$key = trim($key);
			if ($key) {
				$metakeys[] = $key;
			}
		}
		
		if (!empty($item_on_page_keys)) {
			if (!empty($metakeys)) { // if none of the tags we filter are in the content item on the page, return nothing
				
				$keys_in_common = array_intersect($item_on_page_keys, $metakeys);
				if (empty($keys_in_common)) {
					return array();
				}
				
				$metakeys = $keys_in_common;
				
			} else {
				$metakeys = $item_on_page_keys;
			}
		}		
		
		if (!empty($metakeys)) {
			$concat_string = $query->concatenate(array('","', ' REPLACE(a.metakey, ", ", ",")', ' ","')); // remove single space after commas in keywords
			$query->where('('.$concat_string.' LIKE "%'.implode('%" OR '.$concat_string.' LIKE "%', $metakeys).'%")');
		}	
		
		// tags filter
		
		$tags = $params->get('tags', array());
		
		if (!empty($tags)) {
		
			// if all selected, get all available tags
			$array_of_tag_values = array_count_values($tags);
			if (isset($array_of_tag_values['all']) && $array_of_tag_values['all'] > 0) { // 'all' was selected
				$tags = array();
				$tag_objects = SYWTags::getTags('com_content.article');
				if ($tag_objects !== false) {
					foreach ($tag_objects as $tag_object) {
						$tags[] = $tag_object->id;
					}
				}
				
				if (empty($tags) && $params->get('tags_inex', 1)) { // won't return any article if no article has been associated to any tag (when include tags only)
					return array();
				}
			} else if ($params->get('include_tag_children', 1)) { // get tag children
				
				$tagTreeArray = array();			
				$helper_tags = new JHelperTags;
				
				foreach ($tags as $tag) {
					$helper_tags->getTagTreeArray($tag, $tagTreeArray);
				}
			
				$tags = array_unique(array_merge($tags, $tagTreeArray));
			}
		}
			
		if (!empty($item_on_page_tagids)) {
			if (!empty($tags)) { // if none of the tags we filter are in the content item on the page, return nothing
		
				// take the tags common to the item on the page and the module selected tags
				$tags_in_common = array_intersect($item_on_page_tagids, $tags);
				if (empty($tags_in_common)) {
					return array();
				}
			
				if ($params->get('tags_match', 'any') == 'all') {
					if (count($tags_in_common) != count($tags)) {
						return array();
					}
				}
			
				$tags = $tags_in_common;
		
			} else {
				$tags = $item_on_page_tagids;
			}
			
			// Note: does not work if 'exclude' tags, which is normal
		}
			
		if (!empty($tags)) {
		
			$tags_to_match = implode(',', $tags);
			
			$query->select('COUNT(t.id) AS tags_count');
			$query->join('INNER', $db->quoteName('#__contentitem_tag_map', 'm').' ON '.$db->quoteName('m.content_item_id').' = '.$db->quoteName('a.id').' AND '.$db->quoteName('m.type_alias').' = '.$db->quote('com_content.article'));
			$query->join('INNER', $db->quoteName('#__tags', 't') . ' ON '.$db->quoteName('m.tag_id').' = '.$db->quoteName('t.id'));
			
			$test_type = $params->get('tags_inex', 1) ? 'IN' : 'NOT IN';			
			$query->where($db->quoteName('t.id').' '.$test_type.' ('.$tags_to_match.')');
			
			$query->where($db->quoteName('t.access').' IN ('.$groups.')');
			$query->where($db->quoteName('t.published').' = 1');
			
			if (!$params->get('tags_inex', 1)) { // EXCLUDE TAGS
				$query->select('tags_per_items.tag_count_per_item');
					
				// subquery gets all the tags for all items
				$subquery = 'SELECT mm.content_item_id AS content_id, COUNT(tt.id) AS tag_count_per_item FROM #__contentitem_tag_map AS mm INNER JOIN #__tags AS tt ON mm.tag_id = tt.id WHERE tt.access IN ('.$groups.') AND tt.published = 1 AND mm.type_alias = \'com_content.article\' GROUP BY content_id';
				$query->join('INNER', '(' . $subquery . ') AS tags_per_items ON tags_per_items.content_id = a.id');
				
				//if ($params->get('tags_match', 'any') == 'all') {
					// TODO incomplete: if an item has one of the tags and that is the only tag, it won't show (COUNT(t.id) is never 0)
					//$query->having('COUNT('.$db->quoteName('t.id').') + '.count($tags).' <> tags_per_items.tag_count_per_item');
				//} else {
					// we keep items that have the same amount of tags before and after removals
					$query->having('COUNT('.$db->quoteName('t.id').') = tags_per_items.tag_count_per_item');
				//}
			} else { // INCLUDE TAGS
				if ($params->get('tags_match', 'any') == 'all') {
					$query->having('COUNT('.$db->quoteName('t.id').') = '.count($tags));
				}
			}
			
			$query->group($db->quoteName('a.id'));
		}
	
		// user filter
		
		$include = $params->get('author_inex', 1);
		$authors_array = $params->get('created_by', array());
		
		// old parameter - backward compatibility
		$old_authors = $params->get('user_id', '');
		if ($old_authors) {
			switch ($old_authors)
			{
				case 'by_me': $include = true; $authors_array[] = 'auto'; break;
				case 'not_me': $include = false; $authors_array[] = 'auto'; break;
				case 'all': default: $authors_array[] = 'all';
			}
		}
		
		$array_of_authors_values = array_count_values($authors_array);
		if (isset($array_of_authors_values['all']) && $array_of_authors_values['all'] > 0) { // 'all' was selected
			// take all authors
		} else if (isset($array_of_authors_values['auto']) && $array_of_authors_values['auto'] > 0) { // 'auto' was selected
			$test_type = $include ? '=' : '<>';
			$query->where('a.created_by ' .$test_type.' '.(int) $user->get('id'));
		} else {
			$authors = implode(',', $authors_array);
			if ($authors) {
				$test_type = $include ? 'IN' : 'NOT IN';
				$query->where('a.created_by '.$test_type.' ('.$authors.')');
			}
		}
	
		// language filter
		
		if ($app->getLanguageFilter()) {
			$query->where('a.language IN ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')');
		}
	
		$ordering = '';
	
		// author order
		
		switch ($params->get('author_order', '')) 
		{
			case 'selec_asc': $ordering .= "author ASC,"; break;
			case 'selec_dsc': $ordering .= "author DESC,"; break;
		}
	
		// featured switch
		
		$featured = false;
		$featured_only = false;
		switch ($params->get('show_f', 3))
		{
			case '1': // only
				$featured = true;
				$featured_only = true;
				$query->where('a.featured = 1');
				if ($params->get('order') == 'o_asc' || $params->get('order') == 'o_dsc') {
					$query->join('LEFT', '#__content_frontpage AS fp ON fp.content_id = a.id');
				}
				break;
			case '0': // hide
				$query->where('a.featured = 0');
				break;
			case '2': // first the featured ones
				$featured = true;
				if ($params->get('order') == 'o_asc' || $params->get('order') == 'o_dsc') {
					$query->join('LEFT', '#__content_frontpage AS fp ON fp.content_id = a.id');
				}
				$ordering .= 'a.featured DESC,';
				break;
			default: // no discrimination between featured/unfeatured items
				$featured = true;
				if ($params->get('order') == 'o_asc' || $params->get('order') == 'o_dsc') {
					$query->join('LEFT', '#__content_frontpage AS fp ON fp.content_id = a.id');
				}
				break;
		}

		// category order
		
		if (!$featured_only) {
			switch ($params->get('cat_order', '')) 
			{
				case 'o_asc': $ordering .= "c.lft ASC,"; break;
				case 'o_dsc': $ordering .= "c.lft DESC,"; break;
			}
		}
	
		// general ordering
	
		switch ($params->get('order'))
		{
			case 'o_asc': if ($featured) { $ordering .= 'CASE WHEN (a.featured = 1) THEN fp.ordering ELSE a.ordering END ASC'; } else { $ordering .= 'a.ordering ASC'; } break;
			case 'o_dsc': if ($featured) { $ordering .= 'CASE WHEN (a.featured = 1) THEN fp.ordering ELSE a.ordering END DESC'; } else { $ordering .= 'a.ordering DESC'; } break;
			case 'p_asc': $ordering .= 'a.publish_up ASC'; break;
			case 'p_dsc': $ordering .= 'a.publish_up DESC'; break;
			case 'f_asc': $ordering .= 'CASE WHEN (a.publish_down = '.$db->quote($db->getNullDate()).') THEN a.publish_up ELSE a.publish_down END ASC'; break;
			case 'f_dsc': $ordering .= 'CASE WHEN (a.publish_down = '.$db->quote($db->getNullDate()).') THEN a.publish_up ELSE a.publish_down END DESC'; break;
			case 'm_asc': $ordering .= 'a.modified ASC, a.created ASC'; break;
			case 'm_dsc': $ordering .= 'a.modified DESC, a.created DESC'; break;
			case 'c_asc': $ordering .= 'a.created ASC'; break;
			case 'c_dsc': $ordering .= 'a.created DESC'; break;
			case 'mc_asc': $ordering .= 'CASE WHEN (a.modified = '.$db->quote($db->getNullDate()).') THEN a.created ELSE a.modified END ASC'; break;
			case 'mc_dsc': $ordering .= 'CASE WHEN (a.modified = '.$db->quote($db->getNullDate()).') THEN a.created ELSE a.modified END DESC'; break;
			case 'random': $ordering .= 'rand()'; break;
			case 'hit': $ordering .= 'a.hits DESC'; break;
			case 'title_asc': $ordering .= 'a.title ASC'; break;
			case 'title_dsc': $ordering .= 'a.title DESC'; break;
			default: $ordering .= 'a.publish_up DESC'; break;
		}
	
		$query->order($ordering);
	
		// include only
	
		$articles_to_include = trim($params->get('in', ''));
		if (!empty($articles_to_include)) {			
			$query->where('a.id IN ('.$articles_to_include.')');
		}
	
		// exclude
	
		$articles_to_exclude = array_filter(explode(",", trim($params->get('ex', ''))));
		
		$item_on_page_id = '';
		if ($params->get('ex_current_item', 0) && $option === 'com_content' && $view === 'article') {
			$temp = $jinput->getString('id');
			$temp = explode(':', $temp);
			$item_on_page_id = $temp[0];
		}
		
		if ($item_on_page_id) { // do not show the current article in the list
			$articles_to_exclude[] = $item_on_page_id;
		}		
		
		if (!empty($articles_to_exclude)) {			
			$query->where('a.id NOT IN ('.implode(",", $articles_to_exclude).')');
		}
	
		// launch query
		
		$count = trim($params->get('count', ''));
		$startat = $params->get('startat', 1);
		if ($startat < 1) {
			$startat = 1;
		}
		
		if (!empty($count) && $params->get('count_for', 'articles') == 'articles') {
			$db->setQuery($query, $startat - 1, intval($count));
		} else {
			$db->setQuery($query);
		}	
		
		try {
			$items = $db->loadObjectList();
		} catch (RuntimeException $e) {
			$app->enqueueMessage(JText::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
			return null;
		}
		
		// END OF DATABASE QUERY
		
		if (empty($items)) {
			return array();
		}
		
		// parameters for all
	
		$head_type = $params->get('head_type', 'none');
		
		$show_image = false;
		if ($head_type == "image") {
			$show_image = true;
			
			$crop_picture = $params->get('crop_pic', 0);
			$maintain_height = $params->get('maintain_height', 0);
			$head_width = $params->get('head_w', 64);
			$head_height = $params->get('head_h', 64);
			$border_width = $params->get('border_w', 0);
			
			$head_width = $head_width - $border_width * 2;
			$head_height = $head_height - $border_width * 2;
			
			$quality_jpg = $params->get('quality_jpg', 100);
			$quality_png = $params->get('quality_png', 0);
			$filter = $params->get('filter', 'none');
			
			if ($quality_jpg > 100) {
				$quality_jpg = 100;
			}
			if ($quality_jpg < 0) {
				$quality_jpg = 0;
			}
			
			if ($quality_png > 9) {
				$quality_png = 9;
			}
			if ($quality_png < 0) {
				$quality_png = 0;
			}
			
			$image_qualities = array('jpg' => $quality_jpg, 'png' => $quality_png);
			
			$clear_cache = $params->get('clear_cache', 0);
			
			$subdirectory = 'thumbnails/lnee';
			if ($params->get('thumb_path', 'images') == 'cache') {
				$subdirectory = 'mod_latestnewsenhanced';
			}
			$tmp_path = SYWCache::getTmpPath($params->get('thumb_path', 'images'), $subdirectory);
			
			$default_picture = trim($params->get('default_pic', ''));
			
			if ($clear_cache) {
				modLatestNewsEnhancedExtendedHelper::clearThumbnails($module->id, $tmp_path);
			}
		}
				
		$text_type = $params->get('text', 'intro');
		$letter_count = trim($params->get('l_count'));
		$keep_tags = $params->get('keep_tags');
		$strip_tags = $params->get('strip_tags', 1);
		$always_show_readmore = $params->get('readmore_always_show', true);
		$trigger_OnContentPrepare = $params->get('trigger_events', false);
		$force_one_line = $params->get('force_one_line', false);
		$title_letter_count = trim($params->get('letter_count_title', ''));
		$show_date = $params->get('show_d', 'date');	
		$link_to = $params->get('link_to', 'article');
		
		// ITEM DATA MODIFICATIONS AND ADDITIONS
		
		// date
		
		$items_with_no_date = array();
		
		foreach ($items as $key => &$item) {
		
			// check if date is null
			if ($item->date == $db->getNullDate()) {
				$item->date = null;
				$items_with_no_date[] = $item;
				unset($items[$key]);
			}
		}
		
		$when_no_date = $params->get('when_no_date', 0);
		if ($when_no_date == 1) {
			$items = array_merge($items_with_no_date, $items);
		} else if ($when_no_date == 2) {
			$items = array_merge($items, $items_with_no_date);
		}
	
		// restrict articles per author or category
		
		$count_for = $params->get('count_for', 'articles');
		if (!empty($count) && ($count_for == 'catid' || $count_for == 'author')) {
		
			$grouped = array();
			$pass = array();
			
			foreach ($items as $key => &$item) {
					
				if (!isset($grouped[$item->$count_for])) {
					$grouped[$item->$count_for] = array();
					$pass[$item->$count_for] = array();
				}
				
				if (count($pass[$item->$count_for]) < ($startat - 1)) {
					$pass[$item->$count_for][$key] = $item;
				} elseif (count($grouped[$item->$count_for]) < intval($count)) {
					$grouped[$item->$count_for][$key] = $item;
				}
					
				unset($items[$key]);
			}
			
			$items = array();
			foreach ($grouped as $group) {
				$items = array_merge($items, $group);
			}
		}
	
		$helper_tags = new JHelperTags;
		
		foreach ($items as &$item) {
	
			// category link
			
			if (!$show_unauthorized_items || in_array($item->category_access, $authorised)) {
				$item->catlink = JRoute::_(ContentHelperRoute::getCategoryRoute($item->cat_slug, $item->language));
				$item->category_authorized = true;
			} else {
				$catlink = new JUri(JRoute::_('index.php?option=com_users&view=login', false));
				
				$category = JCategories::getInstance('Content', array('access' => false))->get($item->catid);				
				$catlink->setVar('return', base64_encode(ContentHelperRoute::getCategoryRoute($category, $item->language)));				
				
				$item->catlink = $catlink;
				$item->category_authorized = false;
			}
			
			// item link
			
			$item->linktarget = '';
			$item->isinternal = true;
	
			if ($item->category_authorized && (!$show_unauthorized_items || in_array($item->access, $authorised))) {
			
				if ($link_to == 'modal') {
					$item->linktarget = 3;
				}
			
				// We know that user has the privilege to view the article						
				$item->link = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->cat_slug, $item->language));
				$item->authorized = true;
			} else {	
				
				// cannot open in modal window in this case - too many cases where it might fail bacause the login form	opens first
				
				$link = new JUri(JRoute::_('index.php?option=com_users&view=login', false));
				
				//$link->setVar('return', base64_encode(JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->cat_slug, $item->language), false)));
				// returns /MyWork_3_4/index.php/latest-news/13-ipsum/9-phosfluorescently-engage-worldwide-methodologies-with-web-enabled-technology-5
				// does not work
				
				if ($item->category_authorized) {
					$link->setVar('return', base64_encode(ContentHelperRoute::getArticleRoute($item->slug, $item->cat_slug, $item->language)));
				} else {
					$link->setVar('return', base64_encode(self::getUnauthorizedArticleRoute($item->slug, $item->cat_slug, $item->language)));
				}
				// works
				// returns 'index.php/latest-news/13-ipsum/9-phosfluorescently-engage-worldwide-methodologies-with-web-enabled-technology-5';
				
				$item->link = $link;
				$item->authorized = false;
			}
			
			$item->linktitle = $item->title;
				
			// title
			
			if (!$force_one_line) {
				if (strlen($title_letter_count) > 0) {
					$item->title = SYWText::getText($item->title, 'txt', (int)$title_letter_count);
				}
			}
			
			// rating (to avoid call to rating plugin, use $item->vote)
			
			if (isset($item->rating)) {
				$item->vote = $item->rating; // to avoid calls to rating plugin
				$item->vote_count = $item->rating_count;
				unset($item->rating);
				unset($item->rating_count);
			} else {
				$item->vote = '';
				$item->vote_count = 0;
			}
			
			// tags			
			
			$tags_array = $helper_tags->getItemTags('com_content.article', $item->id); // array of tag objects
			if (count($tags_array) > 0) {
				$item->tags = $tags_array;
			}
				
			// thumbnail image creation
			
			$item->imagetag = '';
			$item->error = array();
			
			if ($show_image) {
				
				$thumbnails_exist = false;
				$filename = '';
				
				if (!$clear_cache) {
					$thumbnails_exist_tmp = modLatestNewsEnhancedExtendedHelper::thumbnailExists($module->id, $item->id, $tmp_path);
					if ($thumbnails_exist_tmp != false) {
						$filename = $thumbnails_exist_tmp;
						$thumbnails_exist = true;
					}
				}
				
				if (!$thumbnails_exist) {
					// thumbnail(s) do not exist
						
					$imagesrc = '';
				
					if ($head_type == "image") {
					
						if (isset($item->fulltext))	{
							$imagesrc = modLatestNewsEnhancedExtendedHelper::getImageSrcFromContent($item->introtext, $item->fulltext);
						} else {
							$imagesrc = modLatestNewsEnhancedExtendedHelper::getImageSrcFromContent($item->introtext);
						}
					}	
					
					// last resort, use default image if it exists
					$used_default_image = false;
					if (empty($imagesrc)) {
						if (!empty($default_picture)) {
							$imagesrc = $default_picture;
							$used_default_image = true;
						}
					}
					
					if (!empty($imagesrc)) { // found an image
						$result_array = modLatestNewsEnhancedExtendedHelper::getImageFromSrc($module->id, $item->id, $imagesrc, $tmp_path, $head_width, $head_height, $crop_picture, $image_qualities, $filter);
					
						if (!empty($result_array[0])) {
							$filename = $result_array[0];
						}
						
						if (!empty($result_array[1])) {
							// if error for the file found, try and use the default image instead
							if (!$used_default_image && !empty($default_picture)) { // if the default image was the one chosen, no use to retry
								$result_array = modLatestNewsEnhancedExtendedHelper::getImageFromSrc($module->id, $item->id, $default_picture, $tmp_path, $head_width, $head_height, $crop_picture, $image_qualities, $filter);
							
								if (!empty($result_array[0])) {
									$filename = $result_array[0];
								}
									
								if (!empty($result_array[1])) {
									$item->error[] = $result_array[1];
								}
							} else {
								$item->error[] = $result_array[1];
							}
						}
					}
						
					if (!empty($filename) && empty($item->error)) {
						$thumbnails_exist = true;
					}
				}
			
				if (!empty($filename)) {
				
					$extra_styling = '';
				
					if ($thumbnails_exist) {
						// thumbnails have been created
				
						if (!$crop_picture && $maintain_height) {
								
							$imagesize = @getimagesize($filename); // @ to avoid warnings
							$imageheight = $imagesize[1];
								
							$top = intval(($head_height - $imageheight) / 2); // to center the image, when no cropping
							$extra_styling = ' style="position: relative; top: '.$top.'px"';
						}
				
						$filename = JURI::base(true).'/'.$filename;
					}
						
					$item->imagetag = '<img alt="'.$item->title.'" src="'.$filename.'"'.$extra_styling.' />';
				}
			}
				
			// ago
				
			if ($show_date == 'ago' || $show_date == 'agomhd' || $show_date == 'agohm') {
				
				if ($item->date != $db->getNullDate()) {
					$details = modLatestNewsEnhancedExtendedHelper::date_to_counter($item->date, ($postdate == 'finished' || $postdate == 'fin_pen' || $postdate == 'pending') ? true : false);
				
					$item->nbr_seconds  = intval($details['secs']);
					$item->nbr_minutes  = intval($details['mins']);
					$item->nbr_hours = intval($details['hours']);
					$item->nbr_days = intval($details['days']);
					$item->nbr_months = intval($details['months']);
					$item->nbr_years = intval($details['years']);
				}
			}
			
			// text
			
			$item->text = '';
			
			$number_of_letters = -1;
			if ($letter_count != '') {
				$number_of_letters = (int)($letter_count);
			}
			
			$beacon = '';
			if (!$always_show_readmore) {
				$beacon = '^';				
			}
			
			switch ($text_type)
			{
				case 'intrometa':
					if (trim($item->introtext) != '') {
						$use_intro = true;
					} else {
						$use_intro = false;
					}
					break;
				case 'metaintro':
					if (trim($item->metadesc) != '') {
						$use_intro = false;
					} else {
						$use_intro = true;
					}
					break;
				case 'meta': $use_intro = false; break;
				default: case 'intro': $use_intro = true;
			}
			
			if ($use_intro) { // use intro text
				$item->text = $item->introtext;
				if ($trigger_OnContentPrepare) { // will trigger events from plugins
					$app->triggerEvent('onContentPrepare', array('com_content.article', &$item, &$params, 0));
				} // TODO add ,false to allow plugin syntax to be kept in full text (since v1.3.0 of lib_syw)
				$item->text = SYWText::getText($item->text.$beacon, 'html', $number_of_letters, $strip_tags, trim($keep_tags));
			} else { // use meta text
				$item->text = SYWText::getText($item->metadesc.$beacon, 'txt', $number_of_letters, false, '');
			}
			
			// the text won't be cropped if the ^ character is still present after processing (hopefully no ^ at the end of the text)
			$item->cropped = true;
			if (!$always_show_readmore) {
				$text_length = strlen($item->text);
				$item->text = rtrim($item->text, "^");
				if (strlen($item->text) < $text_length && !$item->fulltexthascontent) {
					$item->cropped = false;
				}	
			}	
		}
	
		return $items;
	}
		
	// rewrite of article route in the case where the category is not authorized
	// Joomla should have handled the category node like in the category route code
	
	protected static function getUnauthorizedArticleRoute($id, $catid = 0, $language = 0)
	{
		$needles = array(
				'article'  => array((int) $id)
		);
	
		// Create the link
		$link = 'index.php?option=com_content&view=article&id=' . $id;
	
		if ((int) $catid > 1)
		{
			$categories = JCategories::getInstance('Content', array('access' => false)); // important!
			$category   = $categories->get((int) $catid);
	
			if ($category)
			{
				$needles['category']   = array_reverse($category->getPath());
				$needles['categories'] = $needles['category'];
				$link .= '&catid=' . $catid;
			}
		}
	
		if ($language && $language != "*" && JLanguageMultilang::isEnabled())
		{
			$link .= '&lang=' . $language;
			$needles['language'] = $language;
		}
	
		if ($item = self::_findItem($needles))
		{
			$link .= '&Itemid=' . $item;
		}
	
		return $link;
	}
	
	protected static $lookup = array();
	
	protected static function _findItem($needles = null)
	{
		$app      = JFactory::getApplication();
		$menus    = $app->getMenu('site');
		$language = isset($needles['language']) ? $needles['language'] : '*';
	
		// Prepare the reverse lookup array.
		if (!isset(self::$lookup[$language]))
		{
			self::$lookup[$language] = array();
	
			$component  = JComponentHelper::getComponent('com_content');
	
			$attributes = array('component_id');
			$values     = array($component->id);
	
			if ($language != '*')
			{
				$attributes[] = 'language';
				$values[]     = array($needles['language'], '*');
			}
	
			$items = $menus->getItems($attributes, $values);
	
			foreach ($items as $item)
			{
				if (isset($item->query) && isset($item->query['view']))
				{
					$view = $item->query['view'];
	
					if (!isset(self::$lookup[$language][$view]))
					{
						self::$lookup[$language][$view] = array();
					}
	
					if (isset($item->query['id']))
					{
						/**
						 * Here it will become a bit tricky
						 * language != * can override existing entries
						 * language == * cannot override existing entries
						 */
						if (!isset(self::$lookup[$language][$view][$item->query['id']]) || $item->language != '*')
						{
							self::$lookup[$language][$view][$item->query['id']] = $item->id;
						}
					}
				}
			}
		}
	
		if ($needles)
		{
			foreach ($needles as $view => $ids)
			{
				if (isset(self::$lookup[$language][$view]))
				{
					foreach ($ids as $id)
					{
						if (isset(self::$lookup[$language][$view][(int) $id]))
						{
							return self::$lookup[$language][$view][(int) $id];
						}
					}
				}
			}
		}
	
		// Check if the active menuitem matches the requested language
		$active = $menus->getActive();
	
		if ($active
				&& $active->component == 'com_content'
				&& ($language == '*' || in_array($active->language, array('*', $language)) || !JLanguageMultilang::isEnabled()))
		{
			return $active->id;
		}
	
		// If not found, return language specific home link
		$default = $menus->getDefault($language);
	
		return !empty($default->id) ? $default->id : null;
	}
		
}
?>