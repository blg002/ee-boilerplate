<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
=====================================================
 File: pi.fourohfour_email.php
-----------------------------------------------------
 Purpose: Send an email when users land on a 404 page
-----------------------------------------------------
 License: Creative Commons Attribution-No Derivative Works 3.0
 Copyright 2010 Encaffeinated, Inc. All Rights Reserved.
 Support thread: http://expressionengine.com/forums/viewthread/100378/
=====================================================
 v1.1 - 10/14/2010 - Bug fixes, added ignore param
 v1.0 - 9/18/2010 - Initial release
=====================================================

*/


$plugin_info = array(
	'pi_name'			=> '404 Email',
	'pi_version'		=> '1.1',
	'pi_author'			=> 'Chad Crowell',
	'pi_author_url'		=> 'http://encaffeinated.com/',
	'pi_description'	=> 'Send an email when users land on a 404 page',
	'pi_usage'			=> Fourohfour_email::usage()
);


class Fourohfour_email {

    var $return_data;

    
    /** ----------------------------------------
    /**  Do it
    /** ----------------------------------------*/

    function Fourohfour_email() {
	
		$this->EE =& get_instance(); 

		$to 		= $this->EE->TMPL->fetch_param('to');
		$ignore		= $this->EE->TMPL->fetch_param('ignore');
		
		if(!$to) {
			return;
		}
		
		$label 			= $this->EE->config->item('site_label');
		$from_name 		= $this->EE->config->item('webmaster_name');
		$from_email 	= $this->EE->config->item('webmaster_email');
		$base 			= $this->EE->config->item('base_url');
		$index 			= $this->EE->config->item('site_index');
		$uri 			= $this->EE->uri->uri_string();
		$url 			= $base.$index.$uri;
		
		//init array
		$ignore_array = array();
		//turn ignore param into array
		if($ignore) {
		    $ignore_array = explode(',',$ignore);
		}
		//add favicon to array
		array_push($ignore_array, 'favicon.ico');
		//clean it up
		array_filter($ignore_array);
		//check 1,2
		//print_r($ignore_array);
		
		//loop 
		foreach($ignore_array as $resource) {
			//see if the ignored resources are in the $url var
			//print_r($url);
			$pos = strripos($url, $resource);
			//print_r($pos);
			if ($pos === false) {
			    //send an email
				$subject 	= "Page Not Found Error on ".$label;
				$body 		= "The following page produced a 404 error on the website:\n\n".$url;

				//load the email and text class
				$this->EE->load->library('email');
				$this->EE->load->helper('text'); 

				//setup the email
				$this->EE->email->wordwrap = true;
				$this->EE->email->mailtype = 'text';	
				$this->EE->email->from($from_email,$from_name);
				$this->EE->email->to($to); 
				$this->EE->email->subject($subject);
				$this->EE->email->message(entities_to_ascii($body));

				//send it
				$this->EE->email->Send();
			}
		}

    }
    /* END */
    
// ----------------------------------------
//  Plugin Usage
// ----------------------------------------

// This function describes how the plugin is used.
//  Make sure and use output buffering

function usage()
{
ob_start(); 
?>

{exp:fourohfour_email to="email@domain.com,another_email@domain.com" ignore="some_file.jpg"}

Parameters:
to : One or more comma separated emails who should receive the notification email.

ignore : One or more comma separated files to ignore. If there are any global resources that aren't loading when the offending page is loaded and the 404 is shown, duplicate emails will be sent.  For instance, while developing this plugin, I constantly got a 404 email for the test page as well as one for favicon.ico, because the browser was looking for it and it didn't yet exist. Favicon.ico is now automatically ignored, but you can also explicitly ignore any other files using this parameter.


Instructions:
Drop this plugin anywhere into your 404 template and it will automatically generate and send an email to the supplied email(s) letting them know that the template was displayed and what the URL was that caused it.

In order to have the correct URL shown in the email, your 404 template must display when the bad URL is loaded. In order to make this happen, use a template setup along these lines:

{exp:channel:entries channel="channel_name" rdf="off"}

	{if no_results}
	{embed="site/404"}
	{/if}

	{embed="includes/_header" title="{title}"}
	...content...
	{embed="includes/_footer" loc="{segment_1}"}
	
{/exp:channel:entries}

In this scenario, the site/404 template that gets loaded should be an entire HTML page including header and footer. Because the 404 template is a full HTML page and is loaded in before any of the content of the intended page template, the user sees the fully rendered 404 error page, while the URL for the intended page is still in the address bar (as opposed to redirecting the user to a www.domain.com/site/404 URL). This way, the plugin can access the URL and email it.

To ensure the emails are formatted correctly, be sure these values in the control panel are filled out:
1. Admin > General Configuration > Name of your site
2. Admin > Email Configuration > Return email address for auto-generated emails
3. Admin > Email Configuration > Webmaster or site name for auto-generated email

<?php
$buffer = ob_get_contents();
	
ob_end_clean(); 

return $buffer;
}
/* END */



}
/* End of file pi.fourohfour_email.php */ 
/* Location: ./system/expressionengine/third_party/plugin_name/pi.fourohfour_email.php */