<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die ;

jimport('joomla.form.formfield');
jimport('joomla.plugin.helper');

class JFormFieldGdtest extends JFormField 
{		
	public $type = 'Gdtest';

	protected function getLabel() 
	{		
		return '<div style="clear: both;"></div>';
	}

	protected function getInput() 
	{		
		$lang = JFactory::getLanguage();
		$lang->load('lib_syw.sys', JPATH_SITE);
		
		$show_gif = (trim($this->element['showgif']) === "false") ? false : true;
							
		$extensions = get_loaded_extensions();
		
		$html = '';
		
		if( !in_array( 'gd', $extensions ) ) {			
			$html .= '<div class="alert alert-error">';			
			$html .= '<span>';
			$html .= JText::_('LIB_SYW_GDTEST_NOTLOADED');
			$html .= '</span>';
			$html .= '</div>';
			
			return $html;
		} else {
			$html .= '<div class="alert alert-success">';			
			$html .= '<span>';
			$html .= JText::_('LIB_SYW_GDTEST_LOADED').' (v'.GD_VERSION.')';
			$html .= '</span>';
			$html .= '</div>';
		}
		
		// Add the script to the document head.
		$doc = JFactory::getDocument();	

		$type = strtolower($this->type);
		
		ob_start();
			require_once dirname(__FILE__).'/'.$type.'/tmpl/default.php';
			$html .= ob_get_contents();
		ob_end_clean();
		
		return $html;
	}

}
?>
