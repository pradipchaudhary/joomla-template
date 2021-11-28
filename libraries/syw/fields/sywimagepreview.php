<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die ;

jimport('joomla.form.formfield');

/**
 *
 * @author Olivier Buisard
 * 
 * field parameters
 * 
 * path: the image path (relative or full)
 * width: the preview max width - defaults to 200
 * height: the preview max width
 * showname: show the file name - defaults to false 
 *
 */
class JFormFieldSYWImagePreview extends JFormField 
{		
	public $type = 'SYWImagePreview';

	protected function getInput() 
	{
		$html = '';
		
		$lang = JFactory::getLanguage();
		$lang->load('lib_syw.sys', JPATH_SITE);
		
		$path = '';
		if (isset($this->element['path'])) {
			$path = trim($this->element['path']);
		}
		
		$width = '200';
		$height = '';
		
		$style = '';
		
		if (isset($this->element['width'])) {
			$width = trim($this->element['width']);
		}
		
		$style .= 'max-width: '.$width.'px;';
		
		if (isset($this->element['height'])) {
			$height = trim($this->element['height']);
		
			if (!empty($height)) {
				$style .= 'max-height: '.$height.'px;';
			}
		}
		
		$html .= '<div class="image_preview" style="'.$style.' overflow: auto; border: 1px solid #ccc; border-radius: 3px; padding: 10px; text-align: center">';
		if (!empty($path)) {
			$html .= '<img src="'.$path.'" style="max-width: 100%">';
			if (isset($this->element['showname']) && $this->element['showname'] == 'true') {
				$parts = explode('/', $path);
				$html .= '<br /><span class="label">'.end($parts).'</span>';
			}
		} else {
			// no preview available
			$html .= '<span>'.JText::_('LIB_SYW_IMAGEPREVIEW_NOPREVIEW').'</span>';
		}
		
		$html .= '</div>';
			
		return $html;
	}

}
?>