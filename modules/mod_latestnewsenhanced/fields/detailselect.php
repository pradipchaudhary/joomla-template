<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
*/

// no direct access
defined( '_JEXEC' ) or die;

jimport('syw.k2');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

JFormHelper::loadFieldClass('list');

class JFormFieldDetailSelect extends JFormFieldList
{
	public $type = 'DetailSelect';

	static $core_fields = null;	
	static $k2_fields = null;
	
	static function getCoreFields()
	{
		if (!isset(self::$core_fields)) {
			JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');
			self::$core_fields = FieldsHelper::getFields('com_content.article');
		}
		
		return self::$core_fields;
	}
	
	static function getK2Fields()
	{
		if (!isset(self::$k2_fields)) {
			
			$db = JFactory::getDBO();
				
			$query = $db->getQuery(true);
			
			$query->select('fields.id');
			$query->select('fields.type');
			$query->select('fields.name');
			$query->select('groups.name AS group_name');
			$query->from('#__k2_extra_fields AS fields');
			$query->where($db->quoteName('published').'= 1');
			$query->order($db->quoteName('ordering'));
				
			$query->innerJoin('#__k2_extra_fields_groups AS groups ON groups.id = fields.group');
				
			$db->setQuery($query);
				
			$fields = array();
			try {
				$fields = $db->loadObjectList();
			} catch (RuntimeException $e) {
				if ($db->getErrorNum()) {
					JFactory::getApplication()->enqueueMessage($db->getErrorMsg(), 'warning');
				} else {
					JFactory::getApplication()->enqueueMessage(JText::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
				}
			}
			
			self::$k2_fields = $fields;			
		}
		
		return self::$k2_fields;
	}

	protected function getInput() {
			
		$options = array();
		
		$options[] = JHTML::_('select.option', 'hits', JText::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_HITS'), 'value', 'text', $disable = false);
		$options[] = JHTML::_('select.option', 'rating', JText::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_RATING'), 'value', 'text', $disable = false);
		$options[] = JHTML::_('select.option', 'author', JText::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_AUTHOR'), 'value', 'text', $disable = false);
		$options[] = JHTML::_('select.option', 'date', JText::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_DATE'), 'value', 'text', $disable = false);
		$options[] = JHTML::_('select.option', 'time', JText::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_TIME'), 'value', 'text', $disable = false);
		$options[] = JHTML::_('select.option', 'category', JText::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_CATEGORY'), 'value', 'text', $disable = false);
		$options[] = JHTML::_('select.option', 'linkedcategory', JText::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_LINKEDCATEGORY'), 'value', 'text', $disable = false);
		$options[] = JHTML::_('select.option', 'tags', JText::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_TAGS'), 'value', 'text', $disable = true);
		$options[] = JHTML::_('select.option', 'selectedtags', JText::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_SELECTEDTAGS'), 'value', 'text', $disable = true);
		$options[] = JHTML::_('select.option', 'linkedtags', JText::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_LINKEDTAGS'), 'value', 'text', $disable = true);
		$options[] = JHTML::_('select.option', 'linkedselectedtags', JText::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_LINKEDSELECTEDTAGS'), 'value', 'text', $disable = true);
		$options[] = JHTML::_('select.option', 'keywords', JText::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_KEYWORDS'), 'value', 'text', $disable = false);
		$options[] = JHTML::_('select.option', 'linka', JText::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_LINKA'), 'value', 'text', $disable = true);
		$options[] = JHTML::_('select.option', 'linkb', JText::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_LINKB'), 'value', 'text', $disable = true);
		$options[] = JHTML::_('select.option', 'linkc', JText::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_LINKC'), 'value', 'text', $disable = true);
		$options[] = JHTML::_('select.option', 'links', JText::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_LINKS'), 'value', 'text', $disable = true);
		$options[] = JHTML::_('select.option', 'linksnl', JText::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_LINKSNEWLINE'), 'value', 'text', $disable = true);
		$options[] = JHTML::_('select.option', 'readmore', JText::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_READMORE'), 'value', 'text', $disable = false);
		$options[] = JHTML::_('select.option', 'share', JText::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_SHAREICONS'), 'value', 'text', $disable = true);

		if (JFile::exists(JPATH_ROOT . '/components/com_jcomments/jcomments.php')) {
			$options[] = JHTML::_('select.option', 'jcommentscount', JText::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_JCOMMENTSCOUNT'), 'value', 'text', $disable = true);
			$options[] = JHTML::_('select.option', 'linkedjcommentscount', JText::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_LINKEDJCOMMENTSCOUNT'), 'value', 'text', $disable = true);
		}
		
		if (SYWK2::exists()) {
			//$options[] = JHTML::_('select.option', 'k2_user', JText::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_K2USER'), 'value', 'text', $disable = false);
			$options[] = JHTML::_('select.option', 'k2commentscount', JText::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_K2COMMENTSCOUNT'), 'value', 'text', $disable = true);
			$options[] = JHTML::_('select.option', 'linkedk2commentscount', JText::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_LINKEDK2COMMENTSCOUNT'), 'value', 'text', $disable = true);
		
			// get K2 extra fields
			
			$fields = self::getK2Fields();
				
			// supported field types
			$allowed_types = array('textfield', 'textarea', 'select', 'multipleSelect', 'radio', 'link', /*'labels',*/ 'date');
			
			$fields_count = 0;
			foreach ($fields as $field) {
				if (in_array($field->type, $allowed_types)) {
						
					if ($fields_count == 0) {
						$options[] = JHtml::_('select.optgroup', JText::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_K2EXTRAFIELDS'));
					}
						
					$options[] = JHTML::_('select.option', 'k2field:'.$field->type.':'.$field->id, 'K2: '.$field->group_name.': '.$field->name, 'value', 'text', $disable = true);
						
					$fields_count++;
				}
			}
				
			if ($fields_count > 0) {
				$options[] = JHtml::_('select.optgroup', JText::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_K2EXTRAFIELDS'));
			}
		}
		
		// get Joomla! fields
		// test the fields folder first to avoid message warning that the component is missing
		if (JFolder::exists(JPATH_ADMINISTRATOR . '/components/com_fields') && JComponentHelper::isEnabled('com_fields') && JComponentHelper::getParams('com_content')->get('custom_fields_enable', '1')) {
		
			$fields = self::getCoreFields();
				
			// supported field types
			$allowed_types = array('calendar', 'checkboxes', 'email', 'integer', 'list', 'radio', 'tel', 'text', 'textarea', 'url');
				
			// organize the fields according to their group
		
			$fieldsPerGroup = array(
					0 => array()
			);
		
			$groupTitles = array(
					0 => JText::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_NOGROUPFIELD')
			);
		
			$fields_exist = false;
			foreach ($fields as $field) {
					
				if (!in_array($field->type, $allowed_types)) {
					continue;
				}
					
				if (!array_key_exists($field->group_id, $fieldsPerGroup)) {
					$fieldsPerGroup[$field->group_id] = array();
					$groupTitles[$field->group_id] = $field->group_title;
				}
					
				$fieldsPerGroup[$field->group_id][] = $field;
				$fields_exist = true;
			}
		
			// loop trough the groups
				
			if ($fields_exist) {
				$options[] = JHtml::_('select.optgroup', JText::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_JOOMLAFIELDS'));
					
				foreach ($fieldsPerGroup as $group_id => $groupFields) {
						
					if (!$groupFields) {
						continue;
					}
						
					foreach ($groupFields as $field) {
						$options[] = JHTML::_('select.option', 'jfield:'.$field->type.':'.$field->id, $groupTitles[$group_id].': '.$field->title, 'value', 'text', $disable = true);
					}
				}
					
				$options[] = JHtml::_('select.optgroup', JText::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_JOOMLAFIELDS'));
			}
		}
		
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);
		
		$attributes = 'class="inputbox"';

		return JHTML::_('select.genericlist', $options, $this->name, $attributes, 'value', 'text', $this->value, $this->id);
	}
}
?>