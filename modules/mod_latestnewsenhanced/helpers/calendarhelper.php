<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/

// no direct access
defined('_JEXEC') or die;

jimport('joomla.utilities.date');

jimport('syw.fonts');
jimport('syw.utilities');

class modLatestNewsEnhancedExtendedCalendarHelper
{
	static function getCalendarBlockData($params, $date)
	{
		$data = array();

		$weekday_format = $params->get('fmt_w', 'D');
		$month_format = $params->get('fmt_m', 'M');
		$day_format = $params->get('fmt_d', 'd');
		$time_format = $params->get('t_format', 'H:i');

		$position_1 = $params->get('pos_1', 'w');
		$position_2 = $params->get('pos_2', 'd');
		$position_3 = $params->get('pos_3', 'm');
		$position_4 = $params->get('pos_4', 'y');
		$position_5 = $params->get('pos_5', 't');

		$keys = array($position_1, $position_2, $position_3, $position_4, $position_5);

		foreach ($keys as $key) {
			switch ($key) {
				case 'w' :
					$data[] = array('weekday' => JHTML::_('date', $date, $weekday_format)); // 3 letters or full - translate from language .ini file
					break;
				case 'd' :
					$data[] = array('day' => JHTML::_('date', $date, $day_format)); // 01-31 or 1-31
					break;
				case 'm' :
					$data[] = array('month' => JHTML::_('date', $date, $month_format));
					break;
				case 'y' :
					$data[] = array('year' => JHTML::_('date', $date, 'Y'));
					break;
				case 't' :
					$data[] = array('time' => JHTML::_('date', $date, $time_format));
					break;
				case 'e' :
					$data[] = array('empty' => '&nbsp;');
					break;
				default :
					$data[] = array();
			}
		}

		return $data;
	}

	static function getCalendarInlineStyles($params, $suffix)
	{
		$styles = '';

		$font_calendar = $params->get('fontcalendar', '');
		if (!empty($font_calendar)) {
			$font_calendar = str_replace('\'', '"', $font_calendar); // " lost, replaced by '

			$google_font = SYWUtilities::getGoogleFont($font_calendar); // get Google font, if any
			if ($google_font) {
				SYWFonts::loadGoogleFont(SYWUtilities::getSafeGoogleFont($google_font));
			}

			$styles .= '#lnee_'.$suffix.' .calendar {';
			$styles .= 'font-family: '.$font_calendar.' !important;';
			$styles .= '} ';
		}

		$calendar_bg = $params->get('cal_bg', '');
		if ($calendar_bg) {
			$styles .= "#lnee_".$suffix." .newshead .calendar.image {";
			$styles .= "background: transparent url(".JURI::base().$calendar_bg.") top center no-repeat !important;";
			$styles .= "} ";
		}

		return $styles;
	}

}