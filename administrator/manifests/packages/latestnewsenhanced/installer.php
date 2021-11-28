<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

/**
 * Script file for the packaged Latest News Enhanced module
 */
class pkg_latestnewsenhancedInstallerScript
{	
	static $version = '3.0.4';
	static $minimum_needed_library_version = '1.3.6';
	static $download_link = 'http://www.simplifyyourweb.com/downloads/syw-extension-library';
	static $changelog_link = 'http://www.simplifyyourweb.com/free-products/latest-news-enhanced/file/162-latest-news-enhanced';
	static $transifex_link = 'https://www.transifex.com/opentranslators/latest-news-enhanced';
	
	/**
	 * Called before an install/update method
	 *
	 * @return  boolean  True on success
	 */
	public function preflight($type, $parent) 
	{	
		// check if syw library is present			
		
		if (!JFolder::exists(JPATH_ROOT.'/libraries/syw')) {
			
			if (!$this->installOrUpdatePackage($parent, 'lib_syw')) {
				$message = JText::_('SYWLIBRARY_INSTALLFAILED').'<br /><a href="'.self::$download_link.'" target="_blank">'.JText::_('SYWLIBRARY_DOWNLOAD').'</a>';
				JFactory::getApplication()->enqueueMessage($message, 'error');
				return false;
			}
			
			JFactory::getApplication()->enqueueMessage(JText::sprintf('SYWLIBRARY_INSTALLED', self::$minimum_needed_library_version), 'message');
			
		} else {
			jimport('syw.version');
			
			if (SYWVersion::isCompatible(self::$minimum_needed_library_version)) {
									
				JFactory::getApplication()->enqueueMessage(JText::_('SYWLIBRARY_COMPATIBLE'), 'message');
				
			} else {
				
				if (!$this->installOrUpdatePackage($parent, 'lib_syw')) {
					$message = JText::_('SYWLIBRARY_UPDATEFAILED').'<br />'.JText::_('SYWLIBRARY_UPDATE');
					JFactory::getApplication()->enqueueMessage($message, 'error');
					return false;
				}
				
				JFactory::getApplication()->enqueueMessage(JText::sprintf('SYWLIBRARY_UPDATED', self::$minimum_needed_library_version), 'message');
			}
		}
		
		return true;
	}
	
	/**
	 * Called after an install/update method
	 *
	 * @return  boolean  True on success
	 */
	public function postflight($type, $parent) 
	{
		echo '<p style="margin: 10px 0 20px 0">';
		echo '<img src="../modules/mod_latestnewsenhanced/images/logo.png" />';
		echo '<br /><br /><span class="label">'.JText::sprintf('PKG_LATESTNEWSENHANCED_VERSION', self::$version).'</span>';
		echo '<br /><br />Olivier Buisard @ <a href="http://www.simplifyyourweb.com" target="_blank">Simplify Your Web</a>';
		echo '</p>';	
		
 		// language test
 			
 		$available_languages = array('de-DE', 'en-GB', 'es-ES', 'fi-FI', 'fr-FR', 'it-IT', 'pt-BR', 'ru-RU', 'sl-SI', 'tr-TR');
 		$current_language = JFactory::getLanguage()->getTag();
 		if (!in_array($current_language, $available_languages)) {
 			JFactory::getApplication()->enqueueMessage(JText::sprintf('PKG_LATESTNEWSENHANCED_INFO_LANGUAGETRANSLATE', JFactory::getLanguage()->getName(), self::$transifex_link), 'notice');
 		}
		
		if ($type == 'update') {
			
			// update warning
			
			JFactory::getApplication()->enqueueMessage(JText::sprintf('PKG_LATESTNEWSENHANCED_WARNING_RELEASENOTES', self::$changelog_link), 'warning');
			
			// delete unnecessary files
			
			$files = array(
				'/modules/mod_latestnewsenhanced/animationmaster.js.php',
				'/modules/mod_latestnewsenhanced/stylemaster.css.php',
				'/modules/mod_latestnewsenhanced/stylemaster.js.php'
			);
			
			foreach ($files as $file) {
				if (JFile::exists(JPATH_ROOT.$file) && !JFile::delete(JPATH_ROOT.$file)) {
					JFactory::getApplication()->enqueueMessage(JText::sprintf('PKG_LATESTNEWSENHANCED_ERROR_DELETINGFILEFOLDER', $file), 'warning');
				}
			}
			
			// remove old cached headers which may interfere with fixes, updates or new additions
						
			$filenames_to_delete = array();			
			
			$filenames = glob(JPATH_CACHE.'/mod_latestnewsenhanced/style_*.{css,js}', GLOB_BRACE);
			if ($filenames != false) {
				$filenames_to_delete = array_merge($filenames_to_delete, $filenames);
			}
				
			$filenames = glob(JPATH_CACHE.'/mod_latestnewsenhanced/animation_*.js');
			if ($filenames != false) {
				$filenames_to_delete = array_merge($filenames_to_delete, $filenames);
			}
			
			// from previous versions
			
			$filenames = glob(JPATH_ROOT.'/modules/mod_latestnewsenhanced/stylemaster_*.{css,js}', GLOB_BRACE);
			if ($filenames != false) {
				$filenames_to_delete = array_merge($filenames_to_delete, $filenames);
			}
			
			$filenames = glob(JPATH_ROOT.'/modules/mod_latestnewsenhanced/animationmaster_*.js');
			if ($filenames != false) {
				$filenames_to_delete = array_merge($filenames_to_delete, $filenames);
			}			
			
			foreach ($filenames_to_delete as $filename) {
				if (JFile::exists($filename) && !JFile::delete($filename)) {
					JFactory::getApplication()->enqueueMessage(JText::sprintf('PKG_LATESTNEWSENHANCED_ERROR_DELETINGFILEFOLDER', $filename), 'warning');
				}
			}	
			
			// overrides warning
			
			$defaultemplate = $this->getDefaultTemplate();
			
			if ($defaultemplate) {
				$overrides_path = JPATH_ROOT.'/templates/'.$defaultemplate.'/html/';
			
				if (JFolder::exists($overrides_path.'mod_latestnewsenhanced')) {
					JFactory::getApplication()->enqueueMessage(JText::_('PKG_LATESTNEWSENHANCED_WARNING_OVERRIDES'), 'warning');
				}
			}
		}

 		// remove the old module update site for when it was not packaged
 		
 		$this->removeUpdateSite('module', 'mod_latestnewsenhanced');
		
		return true;
	}	
	
	private function getDefaultTemplate()
	{
		$db = JFactory::getDBO();
		
		$query = $db->getQuery(true);
		
		$query->select('template');
		$query->from('#__template_styles');
		$query->where($db->quoteName('client_id').'= 0');
		$query->where($db->quoteName('home').'= 1');
		
		$db->setQuery($query);

		$defaultemplate = '';
		
		try {
			$defaultemplate = $db->loadResult();
		} catch (RuntimeException $e) {
			if ($db->getErrorNum()) {
				JFactory::getApplication()->enqueueMessage($db->getErrorMsg(), 'warning');
			} else {
				JFactory::getApplication()->enqueueMessage(JText::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
			}
		}
		
		return $defaultemplate;
	}	
	
	private function removeUpdateSite($type, $element, $folder = '')
	{
		$db = JFactory::getDBO();
	
		$query = $db->getQuery(true);
			
		$query->select('extension_id');
		$query->from('#__extensions');
		$query->where($db->quoteName('type').'='.$db->quote($type));
		$query->where($db->quoteName('element').'='.$db->quote($element));
		if ($folder) {
			$query->where($db->quoteName('folder').'='.$db->quote($folder));
		}
	
		$db->setQuery($query);
	
		$extension_id = '';
		try {
			$extension_id = $db->loadResult();
		} catch (RuntimeException $e) {
			if ($db->getErrorNum()) {
				JFactory::getApplication()->enqueueMessage($db->getErrorMsg(), 'warning');
			} else {
				JFactory::getApplication()->enqueueMessage(JText::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
			}
			return false;
		}
			
		if ($extension_id) {
	
			$query->clear();
	
			$query->select('update_site_id');
			$query->from('#__update_sites_extensions');
			$query->where($db->quoteName('extension_id').'='.$db->quote($extension_id));
	
			$db->setQuery($query);
	
			$updatesite_id = '';
			try {
				$updatesite_id = $db->loadResult();
			} catch (RuntimeException $e) {
				if ($db->getErrorNum()) {
					JFactory::getApplication()->enqueueMessage($db->getErrorMsg(), 'warning');
				} else {
					JFactory::getApplication()->enqueueMessage(JText::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
				}
				return false;
			}
	
			if ($updatesite_id) {
	
				$query->clear();
					
				$query->delete($db->quoteName('#__update_sites'));
				$query->where($db->quoteName('update_site_id').' = '.$db->quote($updatesite_id));
	
				$db->setQuery($query);
	
				try {
					$db->execute();
				} catch (RuntimeException $e) {
					if ($db->getErrorNum()) {
						JFactory::getApplication()->enqueueMessage($db->getErrorMsg(), 'warning');
					} else {
						JFactory::getApplication()->enqueueMessage(JText::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
					}
					return false;
				}
			}
		}
	
		return true;
	}
	
	private function installOrUpdatePackage($parent, $package_name, $installation_type = 'install')
	{
		// Get the path to the package
	
		$sourcePath = $parent->getParent()->getPath('source');
		$sourcePackage = $sourcePath . '/packages/'.$package_name.'.zip';
	
		// Extract and install the package
	
		$package = JInstallerHelper::unpack($sourcePackage);
		$tmpInstaller = new JInstaller;
	
		try {
			if ($installation_type == 'install') {
				$installResult = $tmpInstaller->install($package['dir']);
			} else {
				$installResult = $tmpInstaller->update($package['dir']);
			}
		} catch (\Exception $e) {
			return false;
		}
	
		return true;
	}
	
	/**
	 * Called on installation
	 *
	 * @return  boolean  True on success
	 */
	public function install($parent) {}
	
	/**
	 * Called on update
	 *
	 * @return  boolean  True on success
	 */
	public function update($parent) {}
	
	/**
	 * Called on uninstallation
	 */
	public function uninstall($parent) {}
	
}
?>