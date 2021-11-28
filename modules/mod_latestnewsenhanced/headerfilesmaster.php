<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('syw.headerfilescache');

class LNE_CSSFileCache extends SYWHeaderFilesCache
{
	public function __construct($extension, $params = null)
	{
		parent::__construct($extension, $params);
		
		$this->extension = $extension;
		
		$variables = array();

		$suffix = $params->get('suffix');
		$variables[] = 'suffix';
		
		$overall = $params->get('overall_style', 'original');
		$variables[] = 'overall';		
		
		// items width and height
		
		$items_height = trim($params->get('items_h', ''));
		$variables[] = 'items_height';
		$items_width = trim($params->get('items_w', ''));
		$variables[] = 'items_width';
		
		$item_width = trim($params->get('item_w', 100));
		$item_width_unit = $params->get('item_w_u', 'percent');
		
		if ($item_width_unit == 'percent') {
			$item_width_unit = '%';
		}
		
		$variables[] = 'item_width_unit';
		
		if ($item_width_unit == '%') {
			if ($item_width <= 0 || $item_width > 100) {
				$item_width = 100;
			}
		} else {
			if ($item_width < 0) {
				$item_width = 0;
			}
		}
		
		$variables[] = 'item_width';
		
		$margin_in_perc = 0;
		if ($item_width_unit == '%') {
			$news_per_row = (int)(100 / $item_width);
			$left_for_margins = 100 - ($news_per_row * $item_width);
			$margin_in_perc = $left_for_margins / ($news_per_row * 2);
		}
		
		$downgraded_item_width = 100; // %
		$downgraded_item_width_unit = '%';
		
		$percentage_of_item_width = $params->get('perc_item_size', 100); // %
		if ($percentage_of_item_width <= 0 || $percentage_of_item_width > 100) {
			$percentage_of_item_width = 100;
		}
		$variables[] = 'percentage_of_item_width';
		
		if ($item_width_unit == '%') {
		
			$downgraded_news_per_row = (int)(100 / $percentage_of_item_width);
			$downgraded_item_width = $item_width * $percentage_of_item_width / 100;
		
			if ($downgraded_news_per_row > 1) {
				$left_for_margins = 100 - (($news_per_row - 1) * $item_width + $downgraded_news_per_row * $downgraded_item_width);
				$margin_in_perc = $left_for_margins / (($news_per_row + $downgraded_news_per_row - 1) * 2);
			}
		} else { // calculate width in pixels
			$downgraded_item_width_unit = 'px';
			$downgraded_item_width = (int)($item_width * $percentage_of_item_width / 100);
		}
		
		$variables[] = 'margin_in_perc';
		$variables[] = 'downgraded_item_width';
		$variables[] = 'downgraded_item_width_unit';		
		
		// body parameters		
		
		$maintain_height = $params->get('maintain_height', 0);
		$variables[] = 'maintain_height';
		
		$force_title_one_line = $params->get('force_one_line', 0);
		$variables[] = 'force_title_one_line';
		
		$font_ref_body = $params->get('f_r_body', 14);
		$variables[] = 'font_ref_body';
		
		$wrap = $params->get('wrap', 0);
		$variables[] = 'wrap';		
		
		$font_details = $params->get('details_fontsize', 80);
		$variables[] = 'font_details';
		$iconfont_color = trim($params->get('iconscolor', '#000000'));
		$variables[] = 'iconfont_color';
		
		// rating
		
		$star_color = trim($params->get('star_color', '#000000'));
		$variables[] = 'star_color';
		
		// head width and height

		$head_width = $params->get('head_w', 64);
		$head_height = $params->get('head_h', 64);
		
		$head_type = $params->get('head_type', 'none');
		
		// image	
		
		$image = false;			
		if ($head_type == 'image' || $head_type == "imageintro" || $head_type == "imagefull" || $head_type == "author" || $head_type == "allimagesasc" || $head_type == "allimagesdesc") {
			
			$bgcolor = trim($params->get('imagebgcolor', '')) != '' ? trim($params->get('imagebgcolor')) : 'transparent';
			$variables[] = 'bgcolor';
			
			$pic_shadow_width = $params->get('sh_w_pic', 0);
			$variables[] = 'pic_shadow_width';
			$pic_border_width = $params->get('border_w', 0);
			$variables[] = 'pic_border_width';
			$pic_border_color = trim($params->get('border_c_pic', '#FFFFFF'));
			$variables[] = 'pic_border_color';	
			
			$head_width = $head_width - $pic_border_width * 2;
			$head_height = $head_height - $pic_border_width * 2;
			
			$image = true;
		}
		$variables[] = 'image';
		
		// calendar
		
		$calendar = '';
		if ($head_type == 'calendar') {			
			
			$color = trim($params->get('c1', '#3D3D3D'));
			$variables[] = 'color';
			$bgcolor1 = trim($params->get('bgc11', '')) != '' ? trim($params->get('bgc11')) : 'transparent';
			$variables[] = 'bgcolor1';
			$bgcolor2 = trim($params->get('bgc12', '')) != '' ? trim($params->get('bgc12')) : 'transparent';
			$variables[] = 'bgcolor2';
			
			$color_top = trim($params->get('c2', '#494949'));
			$variables[] = 'color_top';
			$bgcolor1_top = trim($params->get('bgc21', '')) != '' ? trim($params->get('bgc21')) : 'transparent';
			$variables[] = 'bgcolor1_top';
			$bgcolor2_top = trim($params->get('bgc22', '')) != '' ? trim($params->get('bgc22')) : 'transparent';
			$variables[] = 'bgcolor2_top';
			
			$color_bottom = trim($params->get('c3', '#494949'));
			$variables[] = 'color_bottom';
			$bgcolor1_bottom = trim($params->get('bgc31', '')) != '' ? trim($params->get('bgc31')) : 'transparent';
			$variables[] = 'bgcolor1_bottom';
			$bgcolor2_bottom = trim($params->get('bgc32', '')) != '' ? trim($params->get('bgc32')) : 'transparent';	
			$variables[] = 'bgcolor2_bottom';
			
			$cal_shadow_width = $params->get('sh_w', 0);
			$variables[] = 'cal_shadow_width';
			$cal_border_width = $params->get('border_w_cal', 0);
			$variables[] = 'cal_border_width';
			$cal_border_radius = $params->get('border_r', 0);
			$variables[] = 'cal_border_radius';
			$cal_border_color = trim($params->get('border_c_cal', '#000000'));
			$variables[] = 'cal_border_color';
			
			$font_ref_cal = $params->get('f_r', 14);
			$variables[] = 'font_ref_cal';
			$font_ratio = 1; // floatval($head_height) / 80; // 1em base for a height of 80px
			$variables[] = 'font_ratio';
		
			$calendar = $params->get('cal_style', 'original');
		}
		$variables[] = 'calendar';
		 
		// head width and height
		
		$variables[] = 'head_width';
		$variables[] = 'head_height';
		
		// animation and pagination
		
		$animation = $params->get('anim', '');
		$pagination = $params->get('pagination', '');
		if (!empty($pagination) && empty($animation)) { // pagination only
			$animation = 'justpagination';
		}
		
		$variables[] = 'animation';
		
		if (!empty($animation)) {
			
			$paginate = false;
			$symbols = false;
			$arrows = false;
			$pages = false;			
			switch ($params->get('pagination')) {
				case 'p': $paginate = true; $pages = true; break;
				case 's': $paginate = true; $pages = true; $symbols = true; break;
				case 'pn': $paginate = true; $arrows = true; break;
				case 'ppn': $paginate = true; $arrows = true; $pages = true; break;
				case 'psn': $paginate = true; $symbols = true; $arrows = true; $pages = true; break;
			}
			$variables[] = 'paginate';
			$variables[] = 'symbols';
			$variables[] = 'arrows';
			$variables[] = 'pages';
			
			$align_pagination = $params->get('pagination_align', 'center');
			$variables[] = 'align_pagination';
			$position_pagination = $params->get('pagination_pos', 'below');
			$variables[] = 'position_pagination';
			$pagination_size = $params->get('pagination_specific_size', 1);
			$variables[] = 'pagination_size';
			$pagination_offset = $params->get('pagination_offset', 0);
			$variables[] = 'pagination_offset';
		}
		 
		// set all necessary parameters
		$this->params = compact($variables);
	}
	
	protected function getBuffer()
	{
		// get all necessary parameters
		extract($this->params);
		
// 		if (function_exists('ob_gzhandler')) { // TODO not tested
// 			ob_start('ob_gzhandler');
// 		} else {
			ob_start();
//		}
		
		// set the header
		$this->sendHttpHeaders('css');
		
		include 'styles/style.css.php';
		include 'styles/overall/'.$overall.'/style.css.php';
		if ($calendar) {
			include 'styles/calendar/'.$calendar.'/style.css.php';
		}
		if ($animation) {
			include 'animations/'.$animation.'/style.css.php';
		}
		
		return $this->compress(ob_get_clean());
	}
	
}

class LNE_JSFileCache extends SYWHeaderFilesCache
{
	public function __construct($extension, $params = null)
	{
		parent::__construct($extension, $params);
		
		$this->extension = $extension;
		
		$variables = array();

		$suffix = $params->get('suffix');
		$variables[] = 'suffix';
		
		$item_width = trim($params->get('item_w', 100)); // % : it is always in percentages when the caching occurs
		if ($item_width <= 0 || $item_width > 100) {
			$item_width = 100;
		}
		$variables[] = 'item_width';
		
		$min_width = trim($params->get('min_item_w', 0)); // px : there is always a width when the caching occurs
		$variables[] = 'min_width';
		
		$margin_min_width = 3; // px
		$variables[] = 'margin_min_width';
		
		$margin_error = 1; // px
		$variables[] = 'margin_error';
			
		// set all necessary parameters
		$this->params = compact($variables);
	}
	
	protected function getBuffer()
	{
		// get all necessary parameters
		extract($this->params);
	
// 		if (function_exists('ob_gzhandler')) { // not tested
// 			ob_start('ob_gzhandler');
// 		} else {
 			ob_start();
// 		}		
	
		// set the header
		$this->sendHttpHeaders('js');
	
		echo 'jQuery(document).ready(function ($) { ';
		
			echo 'var item = $("#lnee_'.$suffix.' .latestnews-item"); ';			
			echo 'var itemlist = $("#lnee_'.$suffix.' .latestnews-items"); ';
			
			echo 'if (item != null) { ';
				echo 'resize_news(); ';
			echo '} ';
			
			echo '$(window).resize(function() { ';
				echo 'if (item != null) { ';
					echo 'resize_news(); ';
				echo '} ';
			echo '}); ';
			
			echo 'function resize_news() { ';
			
				echo 'var container_width = itemlist.width(); ';
			
				echo 'var news_per_row = 1; ';
			
				echo 'var news_width = Math.floor(container_width * '.$item_width.' / 100); ';
			         
				echo 'if (news_width < '.$min_width.') { ';				    	    
				   echo 'if (container_width < '.$min_width.') { ';
				    	echo 'news_width = container_width; ';
				   echo '} else { ';
				    	echo 'news_width = '.$min_width.'; ';
				   echo '} ';  	
				echo '} ';
			        
				echo 'if ('.$item_width.' <= 50) { ';
					echo 'news_per_row = Math.floor(container_width / news_width); ';  
				        
					echo 'if (news_per_row == 1) { ';
				    	echo 'news_width = container_width; ';
					echo '} else { ';
				    	echo 'news_width = Math.floor(container_width / news_per_row) - ('.$margin_min_width.' * news_per_row); ';
					echo '} '; 
					
				echo '} else { '; // we can never have 2 items on the same row
			        echo 'news_width = container_width; ';
				echo '} ';
		        
				echo 'var left_for_margins = container_width - (news_per_row * news_width); ';
				echo 'var margin_width = Math.floor(left_for_margins / (news_per_row * 2)) - '.$margin_error.'; ';        
		        
				echo 'item.each(function() { ';
		            echo '$(this).width(news_width + "px"); ';
				echo '$(this).css("margin-left", margin_width + "px"); ';
			        echo '$(this).css("margin-right", margin_width + "px"); ';	        
				echo '}); ';
			echo '} ';
			
		echo '}); ';
			
		return ob_get_clean();
	}
	
}
	
class LNE_JSAnimationFileCache extends SYWHeaderFilesCache
{
	public function __construct($extension, $params = null)
	{
		parent::__construct($extension, $params);

		$this->extension = $extension;

		$variables = array();

		$suffix = $params->get('suffix');
		$variables[] = 'suffix';
		
		$module = '#lnee_'.$suffix;
		$variables[] = 'module';
		
		$jQuery_var = 'jQuery';
		$variables[] = 'jQuery_var';
		
		$warning_items = '';
		$variables[] = 'warning_items';
		
		$warnings = false;
		$variables[] = 'warnings';

		$animation = $params->get('anim', '');
		$pagination = $params->get('pagination', '');
		if (!empty($pagination) && empty($animation)) { // pagination only
			$animation = 'justpagination';
		}
		$variables[] = 'animation';
		
		// general parameters
		
		$horizontal = ($params->get('align', 'v') == 'h') ? true : false;
		$variables[] = 'horizontal';
		
		$item_width = trim($params->get('item_w', 100));
		$item_width_unit = $params->get('item_w_u', 'percent');
		
		if ($item_width_unit == 'percent') {
			$item_width_unit = '%';
		}
		
		$variables[] = 'item_width_unit';
		
		if ($item_width_unit == '%') {
			if ($item_width <= 0 || $item_width > 100) {
				$item_width = 100;
			}
		} else {
			if ($item_width < 0) {
				$item_width = 0;
			}
		}
		
		$variables[] = 'item_width';
		
		$margin_in_perc = 0;
		if ($item_width_unit == '%' && $item_width > 0 && $item_width < 100) {
			$news_per_row = (int)(100 / $item_width);
			$left_for_margins = 100 - ($news_per_row * $item_width);
			$margin_in_perc = $left_for_margins / ($news_per_row * 2);
		}
		$variables[] = 'margin_in_perc';
		
		$items_height = trim($params->get('items_h', ''));
		$variables[] = 'items_height';
		$items_width = trim($params->get('items_w', ''));
		$variables[] = 'items_width';
		
		// animation parameters
				
		$direction = 'left';
		if (!$horizontal) {
			$direction = 'up';
		}
		switch ($params->get('dir', 't')) {
			case 'l' :
				$direction = 'left';
				if (!$horizontal) {
					$direction = 'up';
				}
				break;
			case 'r' :
				$direction = 'right';
				if (!$horizontal) {
					$direction = 'down';
				}
				break;
			case 't' :
				$direction = 'up';
				if ($horizontal) {
					$direction = 'left';
				}
				break;
			case 'b' :
				$direction = 'down';
				if ($horizontal) {
					$direction = 'right';
				}
				break;				
		}
		$variables[] = 'direction';
		
		$auto = $params->get('auto', 1);
		$variables[] = 'auto';
		
		$speed = $params->get('speed', 1000);
		$variables[] = 'speed';
		
		$interval = $params->get('interval', 3000);
		$variables[] = 'interval';
		
		$visibleatonce = $params->get('visible_items', 1);
		$variables[] = 'visibleatonce';	
		
		$moveatonce = ($params->get('move', 'all') === 'all') ? $visibleatonce : '1';
		$variables[] = 'moveatonce';
		
		$num_links = $params->get('num_links', 5);
		$variables[] = 'num_links';		
		
		$prev_type = $params->get('prev_type', '');
		$prev_label = ($prev_type == 'prev') ? JText::_('JPREV') : ($prev_type == 'label' ? trim($params->get('label_prev', '')) : '');
		$variables[] = 'prev_label';
		
		$next_type = $params->get('next_type', '');
		$next_label = ($next_type == 'next') ? JText::_('JNEXT') : ($next_type == 'label' ? trim($params->get('label_next', '')) : '');
		$variables[] = 'next_label';

		$arrows = false;
		$pages = false;
		switch ($params->get('pagination')) {
			case 'p': case 's': $pages = true; $arrows = false; break;
			case 'pn': $pages = false; $arrows = true; break;
			case 'ppn': case 'psn': $arrows = true; $pages = true; break;				
		}
		$variables[] = 'arrows';
		$variables[] = 'pages';
		
		$position = $params->get('pagination_pos', 'below');
		$variables[] = 'position';
			
		// set all necessary parameters
		$this->params = compact($variables);
	}

	protected function getBuffer()
	{
		// get all necessary parameters
		extract($this->params);

// 		if (function_exists('ob_gzhandler')) { // not tested
// 			ob_start('ob_gzhandler');
// 		} else {
 			ob_start();
// 		}

		// set the header
		$this->sendHttpHeaders('js');

		if (!empty($animation)) {
			include 'animations/'.$animation.'/'.$animation.'.js.php';
		}
			
		return $this->compress(ob_get_clean(), false);
	}
	
}