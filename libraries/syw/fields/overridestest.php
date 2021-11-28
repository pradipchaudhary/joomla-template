<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die ;

jimport('joomla.filesystem.folder');

class JFormFieldOverridesTest extends JFormField 
{		
	public $type = 'OverridesTest';
	
	protected $extension;
	protected $parent_extension;

	protected function getLabel() 
	{		
		return '<div style="clear: both;"></div>';
	}

	protected function getInput() 
	{		
		$lang = JFactory::getLanguage();
		$lang->load('lib_syw.sys', JPATH_SITE);
		
		//$defaultemplate = JFactory::getApplication('site')->getTemplate(); // does not work because we can only get one instance of JApplication and it already is admin
		
		$db = JFactory::getDBO();
		$query = "SELECT template FROM #__template_styles WHERE client_id = 0 AND home = 1";
		$db->setQuery($query);
		$defaultemplate = $db->loadResult();
		
		$overrides_path = JPATH_ROOT.'/templates/'.$defaultemplate.'/html/';
		
		$html = '';
		
		$html .= '<div class="alert alert-info">';
		
		$html .= '<span>';
		if (JFolder::exists($overrides_path.$this->extension)) {
			$html .= JText::_('LIB_SYW_FILESOVERRIDEN');
			$files = JFolder::files($overrides_path.$this->extension);
			$html .= '<br />'.implode(', ', $files);
		} else {
			$html .= JText::_('LIB_SYW_NOOVERRIDES');
		}
		$html .= '</span><br /><br />';
		$html .= '<span>';
		if (JFolder::exists($overrides_path.'layouts/'.$this->extension)) {
			$html .= JText::_('LIB_SYW_LAYOUTSOVERRIDEN');
			$files = JFolder::files($overrides_path.'layouts/'.$this->extension);
			$html .= '<br />'.implode(', ', $files);
		} else if (JFolder::exists($overrides_path.'layouts/'.$this->parent_extension)) {
			$html .= JText::_('LIB_SYW_LAYOUTSOVERRIDENINCOMPONENT');
			$files = JFolder::files($overrides_path.'layouts/'.$this->parent_extension);
			$html .= '<br />'.implode(', ', $files);
		} else {
			$html .= JText::_('LIB_SYW_NOLAYOUTOVERRIDES');
		}
		$html .= '</span>';
		
		$html .= '</div>';
		
		return $html;
	}
	
	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		$return = parent::setup($element, $value, $group);

		if ($return) {
			$this->extension = isset($this->element['extension']) ? $this->element['extension'] : null;
			$this->parent_extension = isset($this->element['parentextension']) ? $this->element['parentextension'] : null;
		}

		return $return;
	}

}
?>
