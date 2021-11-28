<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class SYWFonts 
{	
	static $iconfontLoaded = false;
	static $googlefontLoaded = array();
		
	/**
	 * Load the icon font if needed
	 */
	static function loadIconFont()
	{	
		if (self::$iconfontLoaded) {
			return;
		}
		
		JFactory::getDocument()->addStyleSheet(JURI::base(true).'/media/syw/css/fonts-min.css');
						
		self::$iconfontLoaded = true;
	}
	
	/**
	 * Load the Google font if needed
	 */
	static function loadGoogleFont($safefont)
	{
		if (isset(self::$googlefontLoaded[$safefont]) && self::$googlefontLoaded[$safefont]) {
			return;
		}
		
		JFactory::getDocument()->addStyleSheet('http://fonts.googleapis.com/css?family='.$safefont);
		
		self::$googlefontLoaded[$safefont] = true;
	}
	
}
?>
