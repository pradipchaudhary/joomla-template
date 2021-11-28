<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// include the syndicate functions only once
require_once (dirname(__FILE__).'/helpers/helper.php');
require_once (dirname(__FILE__).'/helpers/calendarhelper.php');
require_once (dirname(__FILE__).'/headerfilesmaster.php');

jimport('joomla.filesystem.file');
jimport('syw.k2');

jimport('joomla.environment.browser');
$browser = JBrowser::getInstance();
$browser_name = $browser->getBrowser();
$browser_version = $browser->getVersion();
$isMobile = $browser->isMobile();

$show_on_mobile = $params->get('show_on_mobile', 1);

if (($isMobile && $show_on_mobile == 0) || (!$isMobile && $show_on_mobile == 2)) {
	return;
}

$list = null;
	
$class_suffix = $module->id;
$params->set('suffix', $class_suffix);

$datasource = $params->get('datasource', 'articles');
switch ($datasource) {
	case 'articles':
		require_once (dirname(__FILE__).'/helpers/helper_standard.php');
		$list = modLatestNewsEnhancedExtendedHelperStandard::getList($params, $module);
		break;
	case 'k2':
		if (SYWK2::exists()) {
			require_once (dirname(__FILE__).'/helpers/helper_k2.php');
			$list = modLatestNewsEnhancedExtendedHelperK2::getList($params, $module);
		} else {
			return; // wrong selection since K2 is not installed
		}
		break;
}

// consider $list is null, in which case, just do a return
if ($list === null) {
	return;
}

if (empty($list)) { // $list can be an empty array
	$nodata_message = trim($params->get('nodatamessage', ''));	
	if (!empty($nodata_message)) {
		require JModuleHelper::getLayoutPath('mod_latestnewsenhanced', $params->get('layout', 'default'));
	} else {
		return;
	}
} else {

	jimport('syw.utilities');
	jimport('syw.libraries');
	jimport('syw.stylesheets');
	jimport('syw.image');
	jimport('syw.fonts');
	jimport('syw.cache');
	
	SYWFonts::loadIconFont();
	
	// parameters
	
	$urlPath = JURI::base().'modules/mod_latestnewsenhanced/';
	$doc = JFactory::getDocument();
	$app = JFactory::getApplication();
	
	$show_errors = $params->get('show_errors', 0);
	$remove_whitespaces = $params->get('remove_whitespaces', 0);
	
	$items_align = $params->get('align', 'v'); 
	
	$items_height = trim($params->get('items_h', ''));
	$items_width = trim($params->get('items_w', ''));
	$item_width = trim($params->get('item_w', 100));
	$item_width_unit = $params->get('item_w_u', 'percent');
	if ($item_width_unit == 'percent') {
		$item_width_unit = '%';
	}
	$min_item_width = $params->get('min_item_w', '');
	
	if ($item_width_unit == '%') {
		if ($item_width <= 0 || $item_width > 100) {
			$item_width = 100;
		}
	} else {
		if ($item_width < 0) {
			$item_width = 0;
		}
	}
	
	$text_align = $params->get('text_align', 'r');
	$title_before_head = $params->get('title_before_head', false);
	$title_html_tag = $params->get('title_tag', '4');
	
	$link_label = trim($params->get('link', ''));
	$unauthorized_link_label = '';
	
	$follow = $params->get('follow', true);
	
	$popup_width = $params->get('popup_x', 600);
	$popup_height = $params->get('popup_y', 500);
	
	$show_title = true;
	if (trim($params->get('letter_count_title', '')) == '0') {
		$show_title = false;
	}
	$force_title_one_line = $params->get('force_one_line', false);
	
	// categories
	
	$show_category = ($params->get('show_cat', 0) == 0) ? false : true;
	$link_category = ($params->get('show_cat', 0) == 1) ? true : false;
		
	$pos_category = $params->get('pos_cat', 'last');
	
	$cat_link_text = trim($params->get('cat_link', ''));
	$unauthorized_cat_link_text = '';
	$consolidate_category = $params->get('consol_cat', 1);
	$show_category_description = $params->get('show_cat_description', 0);
	$show_article_count = $params->get('show_article_count', 0);
	
	// read all
	
	$readall_link = $params->get('readall_link', '');
	$readall_link_label = '';
	$readall_isExternal = false;
	if (!empty($readall_link)) {
		if ($readall_link == 'extern') {
			$external_url = trim($params->get('readall_external_url', ''));
			if ($external_url) {
				$readall_link = $external_url;
				$readall_isExternal = true;
				$readall_link_label = trim($params->get('readall_link_lbl', '')) == '' ? $external_url : trim($params->get('readall_link_lbl'));
			} else {
				$readall_link = '';
			}
		} else {
			$menu = $app->getMenu();
			$menuitem = $menu->getItem($readall_link);
			
			switch ($menuitem->type)
			{
				case 'separator':
				case 'heading':
					$readall_link = '';
					break;		
				case 'url':
					if ((strpos($menuitem->link, 'index.php?') === 0) && (strpos($menuitem->link, 'Itemid=') === false)) {
						// if this is an internal Joomla link, ensure the Itemid is set
						$readall_link = $menuitem->link.'&Itemid='.$menuitem->id;
					} else {
						$readall_link = $menuitem->link;
					}
					break;		
				case 'alias': // if this is an alias use the item id stored in the parameters to make the link
					$readall_link = 'index.php?Itemid='.$menuitem->params->get('aliasoptions');
					break;		
				default:
					$readall_link = $menuitem->link.'&Itemid='.$menuitem->id;
					break;
			}
		
			$readall_link_label = trim($params->get('readall_link_lbl', '')) == '' ? $menuitem->title : trim($params->get('readall_link_lbl'));
		}
	}
	
	$pos_readall = $params->get('readall_pos', 'last');	
	
	// head
	
	$head_type = $params->get('head_type', 'none');
	$head_width = $params->get('head_w', 64);
	$head_height = $params->get('head_h', 64);
	$maintain_height = $params->get('maintain_height', 0);
	
	$show_image = false;
	$show_calendar = false;
	if ($head_type == "image") {
		$show_image = true;
	} else if ($head_type == "calendar") {
		$show_calendar = true;
	}
	
	$show_link = false;
	$show_link_label = false;
	switch ($params->get('what_to_link', '')) {
		case 'title' :
			$show_link = true;
			break;
		case 'label' :
			$show_link_label = true;
			break;
		case 'both' :		
			$show_link = true;
			$show_link_label = true;
			break;
		default :
			break;
	}
	
	$info_block_placement = $params->get('ad_place', 1);
	$append_link = $params->get('append_link', 0);
	$overall_style = $params->get('overall_style', 'original');
	$keep_space = $params->get('keep_image_space', 1);
	$alignment = ($items_align == 'v') ? 'vertical' : 'horizontal';
	$clear_css_cache = $params->get('clear_css_cache', true);
	
	// read-more skinning
	
	$extrareadmorestyle = '';
	$extrareadmoreclass = '';
	$extrareadmorelinkclass = '';	

	$read_more_classes = trim($params->get('readmore_classes', ''));
	
	if ($read_more_classes) {
		$extrareadmorelinkclass = ' '.$read_more_classes;
	}
	
	$read_more_style = $params->get('readmore_style', '');
	
	if ($read_more_style == 'bootstrap') {
	
		$read_more_type = $params->get('readmore_type', 'btn-default');
		$extrareadmorelinkclass .= ' btn '.$read_more_type;
		
		$read_more_size = $params->get('readmore_size', '');
		if ($read_more_size == 'small') {
			$extrareadmorelinkclass .= ' btn-small btn-sm'; // for Bootstrap 2.3 and 3.3
		} else if ($read_more_size == 'mini') {
			$extrareadmorelinkclass .= ' btn-mini btn-xs'; // for Bootstrap 2.3 and 3.3
		}
	}
		
	$read_more_align = $params->get('readmore_align', '');
	if ($read_more_align == 'btn-block') { // for backward compatibility
		$read_more_align = 'justify';
	}
	
	switch ($read_more_align) {
		case 'left': 
			$extrareadmoreclass .= ' linkleft'; 
			$extrareadmorestyle = 'text-align:left'; 
			break;
		case 'right': 
			$extrareadmoreclass .= ' linkright'; 
			$extrareadmorestyle = 'text-align:right'; 
			break;
		case 'center': 
			$extrareadmoreclass .= ' linkcenter'; 
			$extrareadmorestyle = 'text-align:center'; 
			break;
		case 'justify': 
			$extrareadmoreclass .= ' linkjustify';
			if ($read_more_style == 'bootstrap') { 
				$extrareadmorelinkclass .= ' btn-block';
			} else {
				$extrareadmorestyle = 'text-align:center';
			}
	}
	
	if (!empty($extrareadmorestyle)) {
		$extrareadmorestyle = ' style="'.$extrareadmorestyle.'"';
	}
	
	// end read-more skinning
	
	// category skinning
	
	if ($show_category) {
		
		$extracategorystyle = '';
		$extracategoryclass = ''; // $link_category ? '' : ' nolink';
		$extracategorylinkclass = '';	
		$extracategorynolinkclass = '';

		$cat_read_more_classes = trim($params->get('cat_readmore_classes', ''));
		
		if ($cat_read_more_classes) {
			if ($link_category) {
				$extracategorylinkclass = ' '.$cat_read_more_classes;
			} else {
				$extracategorynolinkclass = $cat_read_more_classes;
			}
		}
		
		$cat_read_more_style = $params->get('cat_readmore_style', '');
		
		if ($cat_read_more_style == 'bootstrap' && $link_category) {	
	
			$cat_read_more_type = $params->get('cat_readmore_type', 'btn-default');
			$extracategorylinkclass .= ' btn '.$cat_read_more_type;
		
			$cat_read_more_size = $params->get('cat_readmore_size', '');
			if ($cat_read_more_size == 'small') {
				$extracategorylinkclass .= ' btn-small btn-sm'; // for Bootstrap 2.3 and 3.3
			} else if ($cat_read_more_size == 'mini') {
				$extracategorylinkclass .= ' btn-mini btn-xs'; // for Bootstrap 2.3 and 3.3
			}
		} else {
			$extracategoryclass = ' nostyle';
		}
		
		switch ($params->get('cat_readmore_align', '')) {
			case 'left': 
				$extracategoryclass .= ' linkleft';
				if ($pos_category != 'picture') {
					$extracategorystyle = 'text-align:left';
				}
				break;
			case 'right': 
				$extracategoryclass .= ' linkright';
				if ($pos_category != 'picture') {
					$extracategorystyle = 'text-align:right';
				}
				break;
			case 'center': 
				$extracategoryclass .= ' linkcenter';
				$extracategorystyle = 'text-align:center'; 
				break;
			case 'justify': 
				$extracategoryclass .= ' linkjustify';
				if ($cat_read_more_style == 'bootstrap') {
					$extracategorylinkclass .= ' btn-block';
				} else {
					$extracategorystyle = 'text-align:center';
				}				 
		}
		
		if (!empty($extracategorystyle)) {
			$extracategorystyle = ' style="'.$extracategorystyle.'"';
		}
		
		if (!empty($extracategorynolinkclass)) {
			$extracategorynolinkclass = ' class="'.$extracategorynolinkclass.'"';
		}
	}
	
	// end category skinning
	
	// readall skinning
	
	if (!empty($readall_link)) {
	
		$extrareadallstyle = '';
		$extrareadallclass = '';
		$extrareadalllinkclass = '';	

		$readall_classes = trim($params->get('readall_classes', ''));
		
		if ($readall_classes) {
			$extrareadalllinkclass = ' '.$readall_classes;
		}
		
		$readall_style = $params->get('readall_style', '');
		
		if ($readall_style == 'bootstrap') {
		
			$readall_type = $params->get('readall_type', 'btn-default');
			$extrareadalllinkclass .= ' btn '.$readall_type;
			
			$readall_size = $params->get('readall_size', '');
			if ($readall_size == 'small') {
				$extrareadalllinkclass .= ' btn-small btn-sm'; // for Bootstrap 2.3 and 3.3
			} else if ($readall_size == 'mini') {
				$extrareadalllinkclass .= ' btn-mini btn-xs'; // for Bootstrap 2.3 and 3.3
			}
		}
		
		switch ($params->get('readall_align', '')) {
			case 'left': 
				$extrareadallclass .= ' linkleft'; 
				$extrareadallstyle = 'text-align:left'; 
				break;
			case 'right': 
				$extrareadallclass .= ' linkright'; 
				$extrareadallstyle = 'text-align:right'; 
				break;
			case 'center': 
				$extrareadallclass .= ' linkcenter'; 
				$extrareadallstyle = 'text-align:center'; 
				break;
			case 'justify': 
				$extrareadallclass .= ' linkjustify';
				if ($readall_style == 'bootstrap') {
					$extrareadalllinkclass .= ' btn-block'; 
				} else {				
					$extrareadallstyle = 'text-align:center';
				}
		}
		
		if (!empty($extrareadallstyle)) {
			$extrareadallstyle = ' style="'.$extrareadallstyle.'"';
		}
	}
	
	// end readall skinning
	
	// start downgrading styles
	
	$leading_items_count = 0;
	$percentage_of_item_size = 100;	
	$remove_head = false;
	$remove_text = false;
	$remove_details = false;
	
	// end downgrading styles
	
	// parameters image
	
	if ($show_image) {
		
		$border_width_pic = $params->get('border_w', 0);
		$shadow_width_pic = $params->get('sh_w_pic', 0);
		
		$head_width = $head_width - $border_width_pic * 2;
		$head_height = $head_height - $border_width_pic * 2;
		
		$hover_effect = $params->get('hover_effect', 'none');
		if (strval($hover_effect) == '0') { // for backward compatibility
			$hover_effect = 'none';
		} else if (strval($hover_effect) == '1') {
			$hover_effect = 'shrink';
		}
		if ($hover_effect != 'none') {
			$hover_effect = 'hvr-'.$hover_effect;
		}
	}
	
	// parameters calendar

	$extracalendarclass = 'noimage';
	if ($show_calendar) {
		if ($params->get('cal_bg', '')) {	
			$extracalendarclass = 'image';
		}
		
		// for backward compatibility, in case there are overrides, avoids crashes
		$date_params_keys = array('', 'update');
		$date_params_values = array('', 'update' => 'Please update override');
		$weekday_format = $month_format = $day_format = $time_format = '';
	}
	
	// animation / pagination
	
	$animation = '';
	$pagination = $params->get('pagination', '');
	
	if (!empty($pagination)) { // pagination only
		$animation = 'justpagination';
	}	
	
	if ($animation) {
	
		JHtml::_('jquery.framework');
			
		modLatestNewsEnhancedExtendedHelper::loadLibrary($animation);
		
		$pagination_position_type = $params->get('pagination_pos', 'below');		
		
		$pagination_position = '';
		$pagination_position_top = 'top';
		$pagination_position_bottom = 'bottom';	
		
		if (!empty($pagination)) {
			
			if ($pagination_position_type == 'around') {
				if ($items_align == 'v') {
					$pagination_position_top = 'up';
					$pagination_position_bottom = 'down';
				} else {
					$pagination_position_top = 'left';
					$pagination_position_bottom = 'right';
				}		
			}
		}	
		
		$prev_type = $params->get('prev_type', '');
		$label_prev = $prev_type == 'prev' ? JText::_('JPREV') : ($prev_type == 'label' ? trim($params->get('label_prev', '')) : '');
		
		$next_type = $params->get('next_type', '');
		$label_next = $next_type == 'next' ? JText::_('JNEXT') : ($next_type == 'label' ? trim($params->get('label_next', '')) : '');
				
		$prev_next = true;
		if ($pagination == 'p' || $pagination == 's') {
			$prev_next = false;
		}
		
		$extra_pagination_classes = $params->get('pagination_style', '');
		$pagination_size = $params->get('pagination_size', '');
		if (!empty($extra_pagination_classes)) {
			if ($pagination_size == 'small') {
				$extra_pagination_classes .= ' pagination-small pagination-sm'; // for Bootstrap 2.3 and 3.3
			} else if ($pagination_size == 'mini') {
				$extra_pagination_classes .= ' pagination-mini pagination-sm'; // for Bootstrap 2.3 and 3.3 (no mini)
			} 
		}
	
		$cache_anim_js = new LNE_JSAnimationFileCache('mod_latestnewsenhanced', $params);
		$result = $cache_anim_js->cache('animation_'.$module->id.'.js', $clear_css_cache);
		
		if ($result) {
			$doc->addScript(JURI::base(true).'/cache/mod_latestnewsenhanced/animation_'.$module->id.'.js');
		} 
		
	} else {
		// remove animation.js if it exists
		if (JFile::exists(JPATH_CACHE.'/mod_latestnewsenhanced/animation_'.$module->id.'.js')) {
			JFile::delete(JPATH_CACHE.'/mod_latestnewsenhanced/animation_'.$module->id.'.js');
		}
	}
	
	if (empty($animation) || $animation == 'justpagination') {
	
		// add items responsiveness	when not in an animation other than pagination
	
		if ($item_width_unit == '%' && !empty($min_item_width)) {
		
			JHtml::_('jquery.framework');
		
			$cache_js = new LNE_JSFileCache('mod_latestnewsenhanced', $params);
			$result = $cache_js->cache('style_'.$module->id.'.js', $clear_css_cache);
			
			if ($result) {
				$doc->addScript(JURI::base(true).'/cache/mod_latestnewsenhanced/style_'.$module->id.'.js');
			} 
		}
	} else {
		// remove style.js if it exists
		if (JFile::exists(JPATH_CACHE.'/mod_latestnewsenhanced/style_'.$module->id.'.js')) {
			JFile::delete(JPATH_CACHE.'/mod_latestnewsenhanced/style_'.$module->id.'.js');
		}
	}	
	
	if (JFile::exists(JPATH_ROOT.'/modules/mod_latestnewsenhanced/styles/substitute_styles.css') || JFile::exists(JPATH_ROOT.'/modules/mod_latestnewsenhanced/styles/substitute_styles-min.css')) {
		modLatestNewsEnhancedExtendedHelper::loadUserStylesheet(true);
		
		// remove style.css if it exists
		if (JFile::exists(JPATH_CACHE.'/mod_latestnewsenhanced/style_'.$module->id.'.css')) {
			JFile::delete(JPATH_CACHE.'/mod_latestnewsenhanced/style_'.$module->id.'.css');
		}
	} else {
	
		// extra styles
		
		$extra_styles = trim($params->get('style_overrides', ''));
		if (!empty($extra_styles)) {
			$extra_styles .= ' ';
		}		

		if ($show_calendar) {
			$extra_styles .= modLatestNewsEnhancedExtendedCalendarHelper::getCalendarInlineStyles($params, $class_suffix);
		}
		
		// font details
		$font_details = $params->get('details_font', '');
		if (!empty($font_details)) {
			$font_details = str_replace('\'', '"', $font_details); // " lost, replaced by '
		
			$google_font = SYWUtilities::getGoogleFont($font_details); // get Google font, if any
			if ($google_font) {
				SYWFonts::loadGoogleFont(SYWUtilities::getSafeGoogleFont($google_font));
			}
		
			$extra_styles .= '#lnee_'.$class_suffix.' .newsextra {';
			$extra_styles .= 'font-family: '.$font_details;
			$extra_styles .= '} ';
		}
		
		if (!empty($extra_styles)) {
			$extra_styles = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $extra_styles); // minify the CSS code
		}
		
		$cache_css = new LNE_CSSFileCache('mod_latestnewsenhanced', $params);
		$cache_css->addDeclaration($extra_styles);
		$result = $cache_css->cache('style_'.$module->id.'.css', $clear_css_cache);
		
		if ($result) {
			$doc->addStyleSheet(JURI::base(true).'/cache/mod_latestnewsenhanced/style_'.$module->id.'.css');
		} 
				
		modLatestNewsEnhancedExtendedHelper::loadCommonStylesheet();		
		
		if (JFile::exists(JPATH_ROOT.'/modules/mod_latestnewsenhanced/styles/common_user_styles.css') || JFile::exists(JPATH_ROOT.'/modules/mod_latestnewsenhanced/styles/common_user_styles-min.css')) {
			modLatestNewsEnhancedExtendedHelper::loadUserStylesheet();
		}		
	}
	
	if ($show_image && $hover_effect != 'none') {
		SYWStylesheets::load2DTransitions();
	}
	
	// call the layout	
	require JModuleHelper::getLayoutPath('mod_latestnewsenhanced', $params->get('layout', 'default'));
}
?>