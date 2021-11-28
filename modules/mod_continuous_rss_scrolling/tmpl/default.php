<?php
/**
 * Continuous rss scrolling
 *
 * @package 	Continuous rss scrolling
 * @subpackage 	Continuous rss scrolling
 * @version   	4.7
 * @author    	Gopi Ramasamy
 * @copyright 	Copyright (C) 2010 - 2017 www.gopiplus.com, LLC
 * @license   	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * http://www.gopiplus.com/extensions/2011/06/continuous-rss-scrolling-joomla-module/
 */

// no direct access
defined('_JEXEC') or die;

$crs_record_height = $args['crs_record_height'];
$crs_display_count = $args['crs_display_count'];
$crs_display_width = $args['crs_display_width'];

if(!is_numeric($crs_record_height)){$crs_record_height = 40;} 
if(!is_numeric($crs_display_count)){$crs_display_count = 5;} 
if(!is_numeric($crs_display_width)){$crs_display_width = 200;}

$crs_count = 0;
$crs_html = "";
$crs_x = "";
foreach ( $items as $items ) 
{
	$crs_post_title = $items->title;
	$crs_post_link = $items->link;
	
	$dis_height = $crs_record_height."px";
	$crs_html = $crs_html . "<div class='crs_div' style='height:$dis_height;padding:2px 0px 2px 0px;'>"; 
	$crs_html = $crs_html . "<a target='_blank' href='$crs_post_link'>$crs_post_title</a>";
	$crs_html = $crs_html . "</div>";
	
	$crs_post_title = trim($crs_post_title);
	$crs_x = $crs_x . "crs_array[$crs_count] = '<div class=\'crs_div\' style=\'height:$dis_height;padding:2px 0px 2px 0px;\'><a target=\'_blank\' href=\'$crs_post_link\'>$crs_post_title</a></div>'; ";	
	$crs_count++;
}

$crs_record_height = $crs_record_height + 4;
if($crs_count >= $crs_display_count)
{
	$crs_count = $crs_display_count;
	$crs_height = ($crs_record_height * $crs_display_count);
}
else
{
	$crs_count = $crs_count;
	$crs_height = ($crs_count*$crs_record_height);
}
$crs_height1 = $crs_record_height."px";

?>
<div style="padding-top:8px;padding-bottom:8px;">
  <div style="text-align:left;vertical-align:middle;text-decoration: none;overflow: hidden; position: relative; margin-left: 1px; height: <?php echo $crs_height1; ?>;" id="crs_Holder"><?php echo @$crs_html; ?></div>
</div>
<script type="text/javascript">
var crs_array	= new Array();
var crs_obj	= '';
var crs_scrollPos 	= '';
var crs_numScrolls	= '';
var crs_heightOfElm = '<?php echo $crs_record_height; ?>';
var crs_numberOfElm = '<?php echo $crs_count; ?>';
var crs_scrollOn 	= 'true';
function crs_createscroll() 
{
    <?php echo $crs_x; ?>
    crs_obj	= document.getElementById('crs_Holder');
    crs_obj.style.height = (crs_numberOfElm * crs_heightOfElm) + 'px';
    crs_content();
}
</script>
<script type="text/javascript">
crs_createscroll();
</script>