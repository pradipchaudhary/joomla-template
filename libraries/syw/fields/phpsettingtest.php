<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die ;

jimport('joomla.form.formfield');
jimport('joomla.plugin.helper');

class JFormFieldPhpSettingTest extends JFormField 
{		
	public $type = 'PhpSettingTest';

	protected function getLabel() 
	{		
		return '<div style="clear: both;"></div>';
	}

	protected function getInput() 
	{		
		$lang = JFactory::getLanguage();
		$lang->load('lib_syw.sys', JPATH_SITE);
		
		$setting_to_test = trim($this->element['setting']);
						
		$html = '';
		
		if (!ini_get($setting_to_test)) {
			$html .= '<div class="alert alert-error">';			
			$html .= '<span>';
			$html .= JText::sprintf('LIB_SYW_PHPSETTING_DISABLED', $setting_to_test);
			$html .= '</span>';
			$html .= '</div>';
				
			return $html;
		} else {
			$html .= '<div class="alert alert-success">';			
			$html .= '<span>';
			$html .= JText::sprintf('LIB_SYW_PHPSETTING_ENABLED', $setting_to_test);
			$html .= '</span>';
			$html .= '</div>';
		}
		
		return $html;
	}

}
?>
