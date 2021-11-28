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
 * for Joomla 3+ ONLY
 *
 * field parameters
 * 
 * icons: coma separated transition names (no SYWicon- prefix)
 * icongroups: coma separated transition groups
 * emptyicon: the icon to show as default
 * buttonlabel: the label for the default button
 * help: help field
 * 
 */
class JFormFieldSYWIconPicker extends JFormField 
{		
	public $type = 'SYWIconPicker';
	
	protected $icons;
	protected $icongroups;
	protected $defaulticon;
	protected $buttonlabel;
	protected $help;
	
	static $icongrouplist = array('communications', 'equipment', 'transportation', 'location', 'social', 'agenda', 'finances', 'files', 'systems', 'accessibility', 'media');	
	static $li_icons = '';
		
	static function getIconGroup($icongroup) 
	{
		$icons = array();
		
		switch ($icongroup) {
			case 'communications':
				$icons[] = 'email';
				$icons[] = 'mail_outline';
				$icons[] = 'drafts';
				$icons[] = 'phone-android';
				$icons[] = 'phone-iphone';
				$icons[] = 'phone';
				$icons[] = 'dialpad';
				$icons[] = 'call';
				$icons[] = 'phone_forwarded';
				$icons[] = 'phone_in_talk';
				$icons[] = 'skype';
				$icons[] = 'fax';
				break;
			case 'equipment':
				$icons[] = 'computer';
				$icons[] = 'desktop-windows';
				$icons[] = 'keyboard2';
				$icons[] = 'keyboard-voice';
				$icons[] = 'mouse2';
				$icons[] = 'tablet-android';
				$icons[] = 'tv';
				$icons[] = 'watch';
				$icons[] = 'camera';
				$icons[] = 'camera-alt';
				$icons[] = 'devices_other';
				$icons[] = 'videogame_asset';
				break;	
			case 'transportation':
				$icons[] = 'directions-bike';	
				$icons[] = 'directions-bus';	
				$icons[] = 'directions-car';	
				$icons[] = 'directions-ferry';	
				$icons[] = 'directions-subway';	
				$icons[] = 'directions-train';	
				$icons[] = 'directions-walk';	
				$icons[] = 'flight';	
				$icons[] = 'local-shipping';	
				$icons[] = 'local-taxi';	
				$icons[] = 'traff';	
				$icons[] = 'flight_land';
				$icons[] = 'flight_takeoff';
				$icons[] = 'motorcycle';
				$icons[] = 'airport_shuttle';
				break;
			case 'location':
				$icons[] = 'explore';
				$icons[] = 'my_location';
				$icons[] = 'location_searching';
				$icons[] = 'person_pin_circle';
				$icons[] = 'near_me';
				$icons[] = 'hotel';	
				$icons[] = 'local-attraction';	
				$icons[] = 'local-bar';	
				$icons[] = 'local-cafe';	
				$icons[] = 'local-florist';	
				$icons[] = 'local-hospital';	
				$icons[] = 'local-library';	
				$icons[] = 'local-mall';	
				$icons[] = 'local-parking';	
				$icons[] = 'local-pizza';	
				$icons[] = 'map';	
				$icons[] = 'navigation';	
				$icons[] = 'restaurant-menu';	
				$icons[] = 'store-mall-directory';	
				$icons[] = 'location-city';	
				$icons[] = 'publ';	
				$icons[] = 'school';	
				$icons[] = 'office';	
				$icons[] = 'books';
				$icons[] = 'building';
				$icons[] = 'paw';
				$icons[] = 'spoon';
				$icons[] = 'futbol-o';
				$icons[] = 'weekend';
				$icons[] = 'airline_seat_individual_suite';
				$icons[] = 'airline_seat_recline_extra';
				$icons[] = 'airline_seat_recline_normal';
				$icons[] = 'wc';
				$icons[] = 'beach_access';
				$icons[] = 'business_center';
				$icons[] = 'casino';
				$icons[] = 'fitness_center';
				$icons[] = 'free_breakfast';
				$icons[] = 'golf_course';
				$icons[] = 'pool';
				break;
			case 'social':
				$icons[] = 'group';	
				$icons[] = 'person';
				$icons[] = 'person_outline';
				$icons[] = 'share2';
				$icons[] = 'omega';
				$icons[] = 'blogger';
				$icons[] = 'chat';
				$icons[] = 'comment';
				$icons[] = 'vcard';
				$icons[] = 'thumb-down';
				$icons[] = 'thumb-up';
				$icons[] = 'thumbs-up-down';
				$icons[] = 'flickr';
				$icons[] = 'vimeo';
				$icons[] = 'twitter';
				$icons[] = 'facebook';
				$icons[] = 'googleplus';
				$icons[] = 'pinterest';
				$icons[] = 'tumblr';
				$icons[] = 'linkedin';
				$icons[] = 'dribbble';
				$icons[] = 'stumbleupon';
				$icons[] = 'lastfm';
				$icons[] = 'spotify';
				$icons[] = 'instagram';
				$icons[] = 'circles';
				$icons[] = 'youtube-play';
				$icons[] = 'recent_actors';
				$icons[] = 'contacts';
				$icons[] = 'import_contacts';
				break;
			case 'agenda':
				$icons[] = 'alarm';
				$icons[] = 'event';
				$icons[] = 'view-agenda';
				$icons[] = 'watch_later';
				$icons[] = 'timelapse';	
				$icons[] = 'timer';	
				$icons[] = 'access_time';
				$icons[] = 'event-note';
				$icons[] = 'event_seat';
				$icons[] = 'cake';	
				$icons[] = 'trophy';
				$icons[] = 'gift';
				$icons[] = 'calendar';
				$icons[] = 'birthday-cake';
				$icons[] = 'cake2';
				$icons[] = 'timeline';
				break;
			case 'finances':
				$icons[] = 'account-balance';
				$icons[] = 'account-box';
				$icons[] = 'credit-card';
				$icons[] = 'receipt';	
				$icons[] = 'shopping-cart';	
				$icons[] = 'wallet-giftcard';
				$icons[] = 'wallet-membership';	
				$icons[] = 'attach-money';
				$icons[] = 'paypal';
				$icons[] = 'google-wallet';
				$icons[] = 'cc-visa';
				$icons[] = 'cc-mastercard';
				$icons[] = 'cc-discover';
				$icons[] = 'cc-amex';
				break;
			case 'files':
				$icons[] = 'sd-storage';
				$icons[] = 'storage';
				$icons[] = 'attach-file';
				$icons[] = 'insert-drive-file';
				$icons[] = 'file-download';
				$icons[] = 'file-upload';
				$icons[] = 'folder';
				$icons[] = 'dropbox';
				$icons[] = 'evernote';
				$icons[] = 'picasa';
				break;
			case 'systems':
				$icons[] = 'stack-overflow';
				$icons[] = 'apple';
				$icons[] = 'windows';
				$icons[] = 'android';
				$icons[] = 'linux';
				$icons[] = 'wordpress';
				$icons[] = 'drupal';
				$icons[] = 'joomla';
				break;
			case 'accessibility':
				$icons[] = 'hearing';
				$icons[] = 'accessibility';
				$icons[] = 'accessible';
				$icons[] = 'touch_app';
				break;				
			case 'media':
				$icons[] = 'movie';
				$icons[] = 'movie_filter';
				$icons[] = 'photo_filter';
				$icons[] = 'queue_music';
				$icons[] = 'subtitles';
				$icons[] = 'playlist_play';
				$icons[] = 'music_video';
				$icons[] = 'playlist_add_check';
				$icons[] = 'slow_motion_video';
				break;
// 			case 'icomoon':
// 				$icons[] = 'icon-new';
		}
		
		$iconlist = '';
		foreach ($icons as $icon_item) {
			$iconlist .= '<li style="width: auto; float: left; margin: 2px;" data-SYWicon="'.$icon_item.'">';
			$iconlist .= '<a href="#" class="label hvr-grow" style="padding: 8px; color: #fff; font-size: 1.4em" title="'.$icon_item.'" onclick="return false;"><i class="SYWicon-'.$icon_item.'"></i></a>';
			$iconlist .= '</li>';
		}
		
		return $iconlist;
	}
	
	static function getIcons() 
	{		
		if (empty(self::$li_icons)) {
			
			foreach (self::$icongrouplist as $icongrouplist_item) {
				self::$li_icons .= self::getIconGroup($icongrouplist_item);
				self::$li_icons .= '<li class="divider" style="clear: both; width: auto;"></li>';
			}
			
			$icons[] = 'assignment';
			$icons[] = 'book';
			$icons[] = 'bookmark';
			$icons[] = 'bug-report';
			$icons[] = 'description';
			$icons[] = 'favorite';
			$icons[] = 'help';
			$icons[] = 'home';
			$icons[] = 'info2';
			$icons[] = 'label';
			$icons[] = 'language';
			$icons[] = 'picture-in-picture';
			$icons[] = 'query-builder';
			$icons[] = 'settings';
			$icons[] = 'stars';
			$icons[] = 'theaters';
			$icons[] = 'extension';
			$icons[] = 'dashboard';
			$icons[] = 'format-quote';
			$icons[] = 'verified-user';
			$icons[] = 'view-carousel';
			$icons[] = 'visibility';
			$icons[] = 'work';
			$icons[] = 'equalizer';
			$icons[] = 'games';
			$icons[] = 'radio';
			$icons[] = 'videocam';
			$icons[] = 'location-on';
			$icons[] = 'messenger';
			$icons[] = 'voicemail';
			$icons[] = 'add-circle';
			$icons[] = 'content-paste';
			$icons[] = 'create';
			$icons[] = 'flag';
			$icons[] = 'forward';
			$icons[] = 'remove-circle';			
			$icons[] = 'save';
			$icons[] = 'dvr';
			$icons[] = 'gps-fixed';
			$icons[] = 'now-wallpaper';
			$icons[] = 'now-widgets';
			$icons[] = 'insert-emoticon';
			$icons[] = 'insert-photo';
			$icons[] = 'cloud';			
			$icons[] = 'security';
			$icons[] = 'color-lens';
			$icons[] = 'filter-frames';	
			$icons[] = 'healing';	
			$icons[] = 'style';	
			$icons[] = 'wb-sunny';
			$icons[] = 'apps';	
			$icons[] = 'check-box';	
			$icons[] = 'radio-button-on';	
			$icons[] = 'star';	
			$icons[] = 'star-half';	
			$icons[] = 'star-outline';	
			$icons[] = 'pushpin';
			$icons[] = 'quotes-right';	
			$icons[] = 'power-cord';
			$icons[] = 'directions';
			$icons[] = 'tag';
			$icons[] = 'leaf';
			$icons[] = 'newspaper';
			$icons[] = 'lifebuoy';
			$icons[] = 'briefcase';
			$icons[] = 'hourglass';
			$icons[] = 'gauge';
			$icons[] = 'language2';
			$icons[] = 'network';
			$icons[] = 'key';
			$icons[] = 'suitcase';
			$icons[] = 'light-bulb';
			$icons[] = 'box';
			$icons[] = 'ticket';
			$icons[] = 'rss';
			$icons[] = 'pie';
			$icons[] = 'lock';
			$icons[] = 'info';
			$icons[] = 'docs';
			$icons[] = 'tag2';
			$icons[] = 'tags';
			$icons[] = 'chain';
			$icons[] = 'sitemap';
			$icons[] = 'new-releases';
			$icons[] = 'droplets';
			$icons[] = 'fiber_new';
			$icons[] = 'art_track';
			$icons[] = 'web_asset';
			$icons[] = 'highlight';
			$icons[] = 'developer_board';
			$icons[] = 'filter_vintage';
			$icons[] = 'power';
			$icons[] = 'build';
			$icons[] = 'fingerprint';
			$icons[] = 'pets';
			$icons[] = 'rowing';
			$icons[] = 'pan_tool';
			$icons[] = 'ac_unit';
			$icons[] = 'child_care';
			$icons[] = 'child_friendly';
			$icons[] = 'hot_tub';
			$icons[] = 'kitchen';
			$icons[] = 'room_service';
			$icons[] = 'smoke_free';
			$icons[] = 'smoking_rooms';
			$icons[] = 'spa';
			$icons[] = 'goat';
			$icons[] = 'update';
			$icons[] = 'launch';
			
			foreach ($icons as $icon_item) {
				self::$li_icons .= '<li style="width: auto; float: left; margin: 2px;" data-SYWicon="'.$icon_item.'">';
				self::$li_icons .= '<a href="#" class="label hvr-grow" style="padding: 8px; color: #fff; font-size: 1.4em" title="'.$icon_item.'" onclick="return false;"><i class="SYWicon-'.$icon_item.'"></i></a>';
				self::$li_icons .= '</li>';
			}
		}
		
		return self::$li_icons;
	}
	
	protected function getInput() 
	{		
		$doc = JFactory::getDocument();	
		
		$lang = JFactory::getLanguage();
		$lang->load('lib_syw.sys', JPATH_SITE);	

		JHtml::_('stylesheet', 'syw/fonts-min.css', false, true);		
		JHtml::_('stylesheet', 'syw/2d-transitions-min.css', false, true);
		
		//JHtml::_('stylesheet', 'jui/icomoon.css', false, true);
		
		$script = 'jQuery(document).ready(function () {';	

		// after load, select the saved value
		$script .= '    if (jQuery(\'#' . $this->id . '\').val() != "") {';
		$script .= '         jQuery("#'.$this->id.'_select li a").each(function() {';
		$script .= '              if (jQuery(this).parent().attr(\'data-SYWicon\') == jQuery(\'#' . $this->id . '\').val()) {';
		$script .= '                  jQuery(this).addClass("label-success");';
		$script .= '              }';
		$script .= '         });';
		$script .= '    }';
		
		$script .= '    jQuery("#'.$this->id.'_select li").click(function() {';
		// de-select the previous value
		$script .= '         jQuery("#'.$this->id.'_select li a").each(function() {';
		$script .= '              jQuery(this).removeClass("label-success");';
		$script .= '         });';
		//
		$script .= '         jQuery(\'#' . $this->id . '\').val(jQuery(this).attr(\'data-SYWicon\'));';
		$script .= '         jQuery(\'#' . $this->id . '_icon\').attr(\'class\', \'SYWicon-\' + jQuery(this).attr(\'data-SYWicon\'));';
		$script .= '         jQuery("#'.$this->id.'_default").removeClass("btn-primary");';
		$script .= '         jQuery(this).children(":first").addClass("label-success");';
		$script .= '    });';
		$script .= '    jQuery("#'.$this->id.'_default").click(function() {';
		$script .= '         jQuery(\'#' . $this->id . '\').val(\'\');';
		$script .= '         jQuery(\'#' . $this->id . '_icon\').attr(\'class\', \'\');';
		if (empty($this->default)) {
			$script .= '          jQuery(\'#' . $this->id . '_icon\').attr(\'class\', \'SYWicon-'.$this->emptyicon.'\');';
		} else {
			$script .= '          jQuery(\'#' . $this->id . '_icon\').attr(\'class\', \'SYWicon-'.$this->default.'\');';
		}
		$script .= '         jQuery("#'.$this->id.'_default").addClass("btn-primary");';
		$script .= '         jQuery("#'.$this->id.'_select li a").removeClass("label-success");';
		$script .= '    });';
		$script .= '});';
		
		$doc->addScriptDeclaration($script);
					
		$html = '';
			
		$html .= '<div class="input-prepend input-append">';	
		
 		if (!empty($this->value)) {
 			$html .= '    <div class="add-on"><i id="'.$this->id.'_icon" class="SYWicon-'.$this->value.'"></i></div>';
 		} else {
			if (empty($this->default)) {
				$html .= '    <div class="add-on"><i id="'.$this->id.'_icon" class="SYWicon-'.$this->emptyicon.'"></i></div>';
			} else {
				$html .= '    <div class="add-on"><i id="'.$this->id.'_icon" class="SYWicon-'.$this->default.'"></i></div>';
			}
 		}	

		$html .= '    <input type="hidden" name="'.$this->name.'" id="'.$this->id.'"'.' value="'.htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8').'" />';
		$html .= '    <div class="btn-group" style="display:inline-block;vertical-align:middle">';
		$html .= '        <button id="'.$this->id.'_caret" style="border-radius:0;margin-left:-1px;min-width:auto" class="btn dropdown-toggle hasTooltip" data-toggle="dropdown" title="' . JText::_('LIB_SYW_ICONPICKER_SELECTICON') . '">';
		$html .= '            <span class="caret" style="margin-bottom:auto"></span>';
		$html .= '        </button>';
		$html .= '        <ul id="'.$this->id.'_select" class="dropdown-menu" style="min-width: 250px; max-height: 110px; overflow: auto">';
						
		if (isset($this->icons)) {			
			$icons = explode(",", $this->icons);
			foreach ($icons as $icon_item) {
				$html .= '<li style="width: auto; float: left; margin: 2px;" data-SYWicon="'.$icon_item.'"><a href="#" class="label hvr-grow" style="padding: 8px; color: #fff; font-size: 1.4em" title="'.$icon_item.'" onclick="return false;"><i class="SYWicon-'.$icon_item.'"></i></a></li>';
			}
		} else if (isset($this->icongroups)) {
			$icongroups = explode(",", $this->icongroups);
			$i = 0;
			foreach ($icongroups as $icongroup_item) {
				$html .= self::getIconGroup($icongroup_item);
				if ($i < count($icongroups) - 1) {
					$html .= '<li class="divider" style="clear: both; width: auto;"></li>';
				}
				$i++;
			}
		} else {
			$html .= self::getIcons(); // TODO use jQuery append (why?)
		}		
		
		$html .= '        </ul>';
		$html .= '    </div>';
		
		$default_class_extra = '';
		if (empty($this->value) || (!empty($this->default) && $this->default == $this->value)) {
			$default_class_extra = ' btn-primary';
		}
		$html .= '    <a id="'.$this->id.'_default" class="btn'.$default_class_extra.' hasTooltip" title="'.htmlspecialchars($this->buttonlabel, ENT_COMPAT, 'UTF-8').'" href="#" onclick="return false;">'.$this->buttonlabel.'</a>';
				
		$html .= '</div>';
		
		if (isset($this->help)) {
			$html .= '<span class="help-block">'.JText::_($this->help).'</span>';
		}
		
		return $html;
	}
	
	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		$return = parent::setup($element, $value, $group);

		if ($return) {
			$this->icons = isset($this->element['icons']) ? $this->element['icons'] : null;
			$this->icongroups = isset($this->element['icongroups']) ? $this->element['icongroups'] : null;
			$this->emptyicon = isset($this->element['emptyicon']) ? $this->element['emptyicon'] : 'crop-free';
			$this->buttonlabel = isset($this->element['buttonlabel']) ? JText::_($this->element['buttonlabel']) : JText::_('JDEFAULT');
			$this->help = isset($this->element['help']) ? $this->element['help'] : null;
		}

		return $return;
	}

}
?>
