<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die ;

jimport('joomla.form.formfield');
jimport('joomla.plugin.helper');

class JFormFieldPhpExtensionTest extends JFormField 
{		
	public $type = 'PhpExtensionTest';

	protected function getLabel() 
	{	
		return '<div style="clear: both;"></div>';
	}

	protected function getInput() 
	{		
		$lang = JFactory::getLanguage();
		$lang->load('lib_syw.sys', JPATH_SITE);
		
		$extension_to_test = trim($this->element['extension']);
		
		$extensions = get_loaded_extensions();
				
		$html = '';
		
		if (!in_array($extension_to_test, $extensions)) {
			$html .= '<div class="alert alert-error">';			
			$html .= '<span>';
			$html .= JText::sprintf('LIB_SYW_PHPEXTENSION_NOTINSTALLED', $extension_to_test);
			$html .= '</span>';
			$html .= '</div>';
				
			return $html;
		} else {
			$html .= '<div class="alert alert-success">';
			$html .= '<span>';
			$html .= JText::sprintf('LIB_SYW_PHPEXTENSION_INSTALLED', $extension_to_test);
			$html .= '</span>';
			$html .= '</div>';
		}
		
		return $html;
	}

}
?>
