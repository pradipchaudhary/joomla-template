<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die ;

jimport('joomla.form.formfield');

class JFormFieldExtensionLink extends JFormField 
{		
	public $type = 'ExtensionLink';

	protected function getLabel() 
	{		
		$html = '';
		
		$lang = JFactory::getLanguage();
		$lang->load('lib_syw.sys', JPATH_SITE);
		
		JHtml::_('stylesheet', 'syw/fonts-min.css', false, true);
		
		$type = $this->element['linktype'];
		
		$html .= '<div style="clear: both;">';		
		
		$icon = '';
		$title = '';
		switch ($type) {
			case 'forum': $icon="SYWicon-chat"; $title = 'LIB_SYW_EXTENSIONLINK_FORUM_LABEL'; break;
			case 'demo': $icon="SYWicon-visibility"; $title = 'LIB_SYW_EXTENSIONLINK_DEMO_LABEL'; break;
			case 'review': $icon="SYWicon-thumb-up"; $title = 'LIB_SYW_EXTENSIONLINK_REVIEW_LABEL'; break;
			case 'donate': $icon="SYWicon-paypal"; $title = 'LIB_SYW_EXTENSIONLINK_DONATE_LABEL'; break;
			case 'upgrade': $icon="SYWicon-wallet-membership"; $title = 'LIB_SYW_EXTENSIONLINK_UPGRADE_LABEL'; break;
			case 'buy': $icon="SYWicon-paypal"; $title = 'LIB_SYW_EXTENSIONLINK_BUY_LABEL'; break;
			case 'doc': $icon="SYWicon-local-library"; $title = 'LIB_SYW_EXTENSIONLINK_DOC_LABEL'; break;
			case 'onlinedoc': $icon="SYWicon-local-library"; $title = 'LIB_SYW_EXTENSIONLINK_ONLINEDOC_LABEL'; break;
			case 'acknowledgement': $icon="SYWicon-thumb-up"; $title = 'LIB_SYW_EXTENSIONLINK_ACKNOWLEDGEMENT_LABEL'; break;
			case 'license': $icon="SYWicon-receipt"; $title = 'LIB_SYW_EXTENSIONLINK_LICENSE_LABEL'; break;
			case 'report': $icon="SYWicon-bug-report"; $title = 'LIB_SYW_EXTENSIONLINK_BUGREPORT_LABEL'; break;
			case 'support': $icon="SYWicon-lifebuoy"; $title = 'LIB_SYW_EXTENSIONLINK_SUPPORT_LABEL'; break;
			case 'translate': $icon="SYWicon-translate"; $title = 'LIB_SYW_EXTENSIONLINK_TRANSLATE_LABEL'; break;
		}
		
		$html .= '<span class="label label-info">';
		$html .= '<i class="'.$icon.'" style="font-size: 1.4em; padding-right: 5px; vertical-align: middle"></i>';
		$html .= '<span style="vertical-align: middle">'.JText::_($title).'</span>';
		$html .= '</span>';
		
		$html .= '</div>';		
		
		return $html;
	}

	protected function getInput() 
	{		
		$lang = JFactory::getLanguage();
		$lang->load('lib_syw.sys', JPATH_SITE);
		
		$type = $this->element['linktype'];
		$link = $this->element['link'];
		$specific_desc = $this->element['description'];
		
		$desc = '';
		switch ($type) {
			case 'forum': $desc = 'LIB_SYW_EXTENSIONLINK_FORUM_DESC'; break;
			case 'demo': $desc = 'LIB_SYW_EXTENSIONLINK_DEMO_DESC'; break;
			case 'review': $desc = 'LIB_SYW_EXTENSIONLINK_REVIEW_DESC'; break;
			case 'donate': $desc = 'LIB_SYW_EXTENSIONLINK_DONATE_DESC'; break;
			case 'upgrade': $desc = 'LIB_SYW_EXTENSIONLINK_UPGRADE_DESC'; break;
			case 'buy': $desc = 'LIB_SYW_EXTENSIONLINK_BUY_DESC'; break;
			case 'doc': $desc = 'LIB_SYW_EXTENSIONLINK_DOC_DESC'; break;
			case 'onlinedoc': $desc = 'LIB_SYW_EXTENSIONLINK_ONLINEDOC_DESC'; break;
			case 'acknowledgement': $desc = 'LIB_SYW_EXTENSIONLINK_ACKNOWLEDGEMENT_DESC'; break;
			case 'license': $desc = 'LIB_SYW_EXTENSIONLINK_LICENSE_DESC'; break;
			case 'report': $desc = 'LIB_SYW_EXTENSIONLINK_BUGREPORT_DESC'; break;
			case 'support': $desc = 'LIB_SYW_EXTENSIONLINK_SUPPORT_DESC'; break;
			case 'translate': $desc = 'LIB_SYW_EXTENSIONLINK_TRANSLATE_DESC'; break;
		}
		
		$html = '<div style="padding-top: 5px; overflow: inherit">';		
			
		if (isset($specific_desc)) {
			if (isset($link)) {
				$html .= JText::sprintf($specific_desc, $link);
			} else {
				$html .= JText::_($specific_desc);
			}
		} else {
			if (isset($link)) {
				$html .= JText::sprintf($desc, $link);
			} else {
				$html .= JText::_($desc);
			}
		}		
		
		$html .= '</div>';

		return $html;
	}

}
?>
