<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die ;

jimport('joomla.form.formfield');
jimport('joomla.filesystem.folder');

class JFormFieldDemotest extends JFormField 
{		
	public $type = 'Demotest';

	protected function getLabel() 
	{		
		return '<div style="clear: both;"></div>';		
	}
	
	protected function getInput() 
	{		
		$lang = JFactory::getLanguage();
		$lang->load('lib_syw.sys', JPATH_SITE);
		
		$html = '';
		
		$folder = trim($this->element['demofolder']);
					
		$folder = JPATH_ROOT.$folder;
		if (JFolder::exists($folder)) {
			$html .= '<div class="alert alert-warning">';			
			$html .= '<span style="text-transform: uppercase;">';
			$html .= JText::_('LIB_SYW_DEMOTEST_THISISADEMO');
			$html .= '</span>';
			$html .= '</div>';
		} 
		
		return $html;
	}

}
?>