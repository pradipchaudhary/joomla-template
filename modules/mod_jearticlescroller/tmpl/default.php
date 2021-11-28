<?php
/**
* @package   mod_jearticlescroller
* @copyright Copyright (C) 2009-2010 Joomlaextensions.co.in All rights reserved.
* @license   http://www.gnu.org/licenses/lgpl.html GNU/LGPL, see LICENSE.php
* Contact to : emailtohardik@gmail.com, joomextensions@gmail.com
**/

// no direct access
defined('_JEXEC') or die('Restricted access');
$document = JFactory::getDocument();
$document->addStyleSheet("modules/mod_jearticlescroller/tmpl/js/skin.css");

$img_width	= $params->get('width', '140');
$img_height	= $params->get('height', '80');

$autoscroller	= $params->get('autoscroller', '1');
?>

<style type="text/css">
.news_img img {
    border: 0 none;
    height: <?php echo $img_height; ?>px;
    width: <?php echo $img_width; ?>px;
	margin: 0px;
}
</style>

<script src="modules/mod_jearticlescroller/tmpl/js/jquery-1.js"></script>
<script src="modules/mod_jearticlescroller/tmpl/js/jquery.js"></script>

<script type="text/javascript">
	/*var $jx = jQuery.noConflict();
	$jx(document).ready(function() {
		$jx('#mycarousel').jcarousel();
	});*/
	
	jQuery(document).ready(function() {
    	jQuery('#mycarousel').jcarousel({
        	
	<?php	if($autoscroller=='1') { ?>
				auto: 2,
        		wrap: 'last',
	<?php 	} else { 	?>
				auto: 0,
				wrap: null,
	<?php 	} 	?>
    	});
	});

	
</script>


<div class="slider">
<div class="slide_bottom">
<ul id="mycarousel" class="jcarousel-skin-tango">
<?php foreach ($list as $item) :  ?>
	<li>
	  <?php if($showimage) { ?>
		<div class="news_img">
			<a href="<?php echo $item->link; ?>">
				<?php if($item->thumb != ""){ ?>
					<?php echo $item->thumb; ?>
				<?php }else{ ?>
					<img src="<?php echo JURI::base(); ?>modules/mod_jearticlescroller/tmpl/images/noimage.png" />
				<?php } ?>
			</a>
		</div>
		<?php } ?>
		<div class="news_text">
		<a href="<?php echo $item->link; ?>">
			<?php echo $item->text; ?></a><br />
			<?php echo $item->introtext; ?>
		</div>
		<div style="clear:both;"></div>
	</li>
<?php endforeach; ?>
</ul>
</div>
</div>
<div style="clear:both;"></div>			