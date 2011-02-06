<?php
/**
 * Anti-Pink Accessory
 *
 * @package			AntiPink
 * @version			1.0.0
 * @author			Jacob Russell <http://jacobrussell.net>
 * @copyright 		Copyright (c) 2010 Jacob Russell <http://jacobrussell.net>
 * @license 		n/a
 * @link			n/a
 * @see				n/a
 */

class anti_pink_acc 
{
	var $name	 		= 'Anti-Pink';
	var $id	 			= 'anti_pink';
	var $version	 	= '1.0.0';
	var $description	= 'EE2 Accessory that replaces the blinding pink of the control panel with a much more reasonable blue.';
	var $sections	 	= array();

	/**
	* Set Sections
	*
	* Set content for the accessory
	*
	* @access	public
	* @return	void
	*/
	function set_sections()
	{
		$EE =& get_instance();

		$theme_folder_url = $EE->config->item('theme_folder_url');

		if (substr($theme_folder_url, -1) != '/')
			$theme_folder_url .= '/';

		$theme_folder_url .= "third_party/anti-pink/";

		$EE->cp->add_to_head('<link rel="stylesheet" type="text/css" href="'.$theme_folder_url.'styles/screen.css" />');
		
		$this->sections[] = '<script type="text/javascript" charset="utf-8">$("#accessoryTabs a.anti_pink").parent().remove();</script>';

	}
}