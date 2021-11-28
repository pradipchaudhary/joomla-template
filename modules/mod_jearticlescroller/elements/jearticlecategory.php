<?php
/**
* @package   mod_jearticlescroller
* @copyright Copyright (C) 2009-2010 Joomlaextensions.co.in All rights reserved.
* @license   http://www.gnu.org/licenses/lgpl.html GNU/LGPL, see LICENSE.php
* Contact to : emailtohardik@gmail.com, joomextensions@gmail.com
**/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class JFormFieldJeArticlecategory extends JFormFieldList
{
	protected $type 		= 'JeArticlecategory';
	public $_cat_list	= NULL;
	
	function getInput()
	{
		$db =& JFactory::getDBO();

		$query = "SELECT a.id AS value, a.title AS text FROM #__categories AS a WHERE a.parent_id >0 AND extension = 'com_content' AND a.published IN (0,1) ORDER BY a.lft";
		$db->setQuery( $query );
		$options = $db->loadObjectList();
		
		@$sel_pcat[0]->text	= JText::_('Select Category');	
		@$sel_pcat[0]->value	= ' ';
		$options	= @array_merge($sel_pcat,$options);

		return JHTML::_('select.genericlist',  $options, $this->name, 'class="inputbox"', 'value', 'text', $this->value );
	}
	
	
}
