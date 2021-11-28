<?php 
/**
* @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/

// No direct access to this file
defined('_JEXEC') or die;

// Explicitly declare the type of content
header("Content-type: text/css; charset=UTF-8");
?>
	
#lnee_<?php echo $suffix; ?> ul.latestnews-items li.active {
	opacity: 0.5;				
	-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=50)"; /* IE8 */
}

#lnee_<?php echo $suffix; ?> ul.latestnews-items li.full {	
	<?php if ($item_width_unit == '%' && $item_width > 50) : ?>
		margin-right: <?php echo ((100 - $item_width) / 2); ?>%;
		margin-left: <?php echo ((100 - $item_width) / 2); ?>%;
	<?php endif; ?>
}

#lnee_<?php echo $suffix; ?>.horizontal ul.latestnews-items li.full {	
	float: left;
}

#lnee_<?php echo $suffix; ?> ul.latestnews-items li.downgraded {
	border-top: 1px solid #CCCCCC;
	padding-top: 5px;
	margin-top: 5px;

	width: <?php echo $downgraded_item_width; ?><?php echo $downgraded_item_width_unit; ?>;
}

	#lnee_<?php echo $suffix; ?> .innernews {
		padding: 2px;
	}
		
		#lnee_<?php echo $suffix; ?> .head_left .newshead {
			float: left;
			margin: 0 8px 0 0;
		}
		
		#lnee_<?php echo $suffix; ?> .head_right .newshead {
			float: right;
			margin: 0 0 0 8px;
		}
		
		<?php if ($image) : ?>
		
			#lnee_<?php echo $suffix; ?> .newshead.picturetype {
				position: relative;
				max-width: 100%;
			}
			
			<?php if ($pic_border_width > 0) : ?>
				#lnee_<?php echo $suffix; ?> .newshead .picture,
				#lnee_<?php echo $suffix; ?> .newshead .nopicture {				
					border: <?php echo $pic_border_width ?>px solid <?php echo $pic_border_color ?>;						
					-webkit-box-sizing: border-box;
					-moz-box-sizing: border-box;
					box-sizing: border-box;				
				}
			<?php endif; ?>
			
			#lnee_<?php echo $suffix; ?> .newshead .picture img {
				display: inherit;
			}

		<?php endif; ?>

		#lnee_<?php echo $suffix; ?> .newsinfooverhead {
			display: none;
		}
			
		<?php if (!$wrap) : ?>					
			#lnee_<?php echo $suffix; ?> .newsinfo {
				overflow: hidden;
			}
			
			#lnee_<?php echo $suffix; ?> .head_left .newsinfo.noimagespace {
				margin-left: 0 !important;
			}
			
			#lnee_<?php echo $suffix; ?> .head_right .newsinfo.noimagespace {
				margin-right: 0 !important;
			}			
		<?php endif; ?>
		
			#lnee_<?php echo $suffix; ?> .newstitle {
				font-weight: bold;
			}		
			
<?php if ($image) : ?>	

	#lnee_<?php echo $suffix; ?> .shadow.simple .picturetype {
		padding: <?php echo (intval($pic_shadow_width) + 2) ?>px;
		box-sizing: border-box; /* should use padding-box */
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
	}
	
	#lnee_<?php echo $suffix; ?> .shadow.simple .picture,
	#lnee_<?php echo $suffix; ?> .shadow.simple .nopicture {
		box-shadow: 0 0 <?php echo $pic_shadow_width; ?>px rgba(0, 0, 0, 0.8);
		-moz-box-shadow: 0 0 <?php echo $pic_shadow_width; ?>px rgba(0, 0, 0, 0.8);
		-webkit-box-shadow: 0 0 <?php echo $pic_shadow_width; ?>px rgba(0, 0, 0, 0.8);
		/* IE 7 AND 8 DO NOT SUPPORT BLUR PROPERTY OF SHADOWS */
	}
	
<?php endif; ?>
