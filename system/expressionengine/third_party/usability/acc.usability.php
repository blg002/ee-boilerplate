<?php
/**
 * Usability improvements for the CP and Morphine
 *
 * @package			Usability
 * @version			1.0.1
 * @author			Alex Kendrick
 * @copyright 		Copyright (c) 2010 Alex Kendrick
 * @license 		n/a
 * @link			n/a
 * @see				n/a
 */

class usability_acc 
{
	var $name	 		= 'Usability';
	var $id	 			= 'usability';
	var $version	 	= '1.0.0';
	var $description	= 'Higher contrast, monospace text for input and textarea in the CP. Correct cursor type for clickable or draggable elements. Restores field re-order handles for NSM Morphine Theme.';
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

		$theme_folder_url .= "third_party/usability/";

		$EE->cp->add_to_head('<link rel="stylesheet" type="text/css" href="'.$theme_folder_url.'styles/screen.css" />');
		
		$this->sections[] = '<script type="text/javascript" charset="utf-8">$("#accessoryTabs a.usability").parent().remove();</script>';
		

	}
}