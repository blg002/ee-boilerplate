<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Solspace - Solspace Update Advisor
*
* @package		Solspace:Solspace Update Advisor
* @author		Solspace DevTeam
* @copyright	Copyright (c) 2008-2011, Solspace, Inc.
* @link			http://solspace.com/docs/addon/c/Solspace_update_advisor/
* @version		1.0.0
* @filesource 	./system/expressionengine/third_party/solspace_update_advisor/
* 
*/

/**
* Rogue Members - Solspace Update Advisor
*
* Main file, handles all i/o for the accoessory and displays outdated installed add-ons and upgrade info
*
* @package 		Solspace:Solspace Update Advisor
* @author		Solspace DevTeam
* @filesource 	./system/expressionengine/third_party/solspace_update_advisor/acc.solspace_update_advisor.php
*/

class Solspace_update_advisor_acc 
{

	public 	$name 			= 'Solspace Update Advisor';
	public 	$id 			= 'solspace_update_advisor';
	public 	$version 		= '1.0.0';
	public 	$description 	= 'Checks for updates for your currently installed Solspace addons.';
	public 	$sections 		= array();
	public 	$addon_json_url = "http://www.solspace.com/software/software_advisor/";
	public 	$has_update  	= array();
	private	$db_cache		= array(
		'local_addons'		=> FALSE,
		'solspace_addons'	=> FALSE,
		'bridge_version'	=> FALSE
	);


	// --------------------------------------------------------------------
	
	/**
	 * Constructor
	 *
	 * @access	public
	 */
	
	public function __construct()
	{
		$this->EE =& get_instance();
		
		if ( ! function_exists('json_decode'))
		{
			$this->EE->load->library('Services_json');
		}
		
		//--------------------------------------------  
		//	set everything with proper names from lang file
		//--------------------------------------------
		
		$this->EE->lang->loadfile('solspace_update_advisor');
		
		$this->name 		= $this->EE->lang->line('accessory_name');
		$this->description 	= $this->EE->lang->line('accessory_description');
	}
	//END __construct


	// --------------------------------------------------------------------

	/**
	 * returns sections for visual output
	 *
	 * @access	public
	 */

	public function set_sections()
	{
		$this->get_db_data();
		
		//--------------------------------------------  
		//	Resources
		//--------------------------------------------
		
		$lang_resources = $this->EE->lang->line('resources');
		
		$vars = array(
			'lang_online_documentation' => $this->EE->lang->line('online_documentation'),
			'lang_tech_support_forums' 	=> $this->EE->lang->line('tech_support_forums'), 
			'lang_solspace_downloads'	=> $this->EE->lang->line('solspace_downloads')
		);
		
		$this->sections[$lang_resources] = $this->EE->load->view('resources.html', $vars, TRUE);;
		
		//--------------------------------------------  
		//	Addon updates
		//--------------------------------------------
		
		//get data for addons
		$solspace_addons	= $this->get_solspace_addons();
		$installed_addons 	= $this->get_installed_addons();
		
		$lang_available_updates = $this->EE->lang->line('available_addon_updates');
		
		//Error getting remote data? bail
		if ($solspace_addons === '')
		{
			$this->sections[$lang_available_updates] = $this->EE->lang->line('data_error');
			return;
		}
		
		//check all addons for updates
		
		$output = '';
		
		foreach ($installed_addons['accessories'] as $name => $version)
		{
			if (isset($solspace_addons['plugins'][$name]) AND
				$solspace_addons['plugins'][$name] > $version)
			{
				$this->has_update[$name] = array(
					'addon_title'		=> $solspace_addons['addon_data'][$name]['title'],  
					'installed_version' => $version,
					'download'			=> $solspace_addons['addon_data'][$name]['download'],
					'current_version'	=> $solspace_addons['addon_data'][$name]['version'],
					'date_updated'		=> $solspace_addons['addon_data'][$name]['edit_date'],
					'docs'				=> $solspace_addons['addon_data'][$name]['docs'],
					'change_log'		=> $solspace_addons['addon_data'][$name]['change_log_url']
				);
			}
		}
		
		foreach ($installed_addons['extensions'] as $name => $version)
		{
			if (isset($solspace_addons['extensions'][$name]) AND
				$solspace_addons['extensions'][$name] > $version)
			{
				$this->has_update[$name] = array(
					'addon_title'		=> $solspace_addons['addon_data'][$name]['title'],  
					'installed_version' => $version,
					'download'			=> $solspace_addons['addon_data'][$name]['download'],
					'current_version'	=> $solspace_addons['addon_data'][$name]['version'],
					'date_updated'		=> $solspace_addons['addon_data'][$name]['edit_date'],
					'docs'				=> $solspace_addons['addon_data'][$name]['docs'],
					'change_log'		=> $solspace_addons['addon_data'][$name]['change_log_url']
				);
			}
		}
		
		foreach ($installed_addons['modules'] as $name => $version)
		{			
			if (isset($solspace_addons['modules'][$name]) AND
				$solspace_addons['modules'][$name] > $version)
			{
				$this->has_update[$name] = array(
					'addon_title'		=> $solspace_addons['addon_data'][$name]['title'],  
					'installed_version' => $version,
					'download'			=> $solspace_addons['addon_data'][$name]['download'],
					'current_version'	=> $solspace_addons['addon_data'][$name]['version'],
					'date_updated'		=> $solspace_addons['addon_data'][$name]['edit_date'],
					'docs'				=> $solspace_addons['addon_data'][$name]['docs'],
					'change_log'		=> $solspace_addons['addon_data'][$name]['change_log_url']
				);
			}
		}
		
		foreach ($installed_addons['plugins'] as $name => $version)
		{
			if (isset($solspace_addons['plugins'][$name]) AND
				$solspace_addons['plugins'][$name] > $version)
			{
				$this->has_update[$name] = array(
					'addon_title'		=> $solspace_addons['addon_data'][$name]['title'],  
					'installed_version' => $version,
					'download'			=> $solspace_addons['addon_data'][$name]['download'],
					'current_version'	=> $solspace_addons['addon_data'][$name]['version'],
					'date_updated'		=> $solspace_addons['addon_data'][$name]['edit_date'],
					'docs'				=> $solspace_addons['addon_data'][$name]['docs'],
					'change_log'		=> $solspace_addons['addon_data'][$name]['change_log_url']
				);
			}
		}
		
		//--------------------------------------------  
		//	lang items
		//--------------------------------------------
		
		$lang_items = array(
			'current_version',
			'installed_version',
			'online_documentation',
			'change_log',
			'date_updated',
			'addon',
			'update_header',
			'bridge_version',
			'all_addons_up_to_date'
		);
		
		foreach ($lang_items as $item)
		{
			$vars['lang_' . $item] = $this->EE->lang->line($item);
		}
		
		$vars['bridge_version'] 					= $this->get_bridge_version();
		
		$vars['update_array'] 						= $this->has_update;
		
		//javascript doesnt auto translate &amp; in links
		$vars['refresh_url']						= str_replace('&amp;', '&', BASE) . 
													  "&C=addons_accessories" . 
													  "&M=process_request" . 
													  "&accessory=solspace_update_advisor" . 
													  "&method=process_refresh_db";
							
		//send to sections	
		$this->sections[$lang_available_updates] 	= $this->EE->load->view(
			'addon_table.html', 
			$vars, 
			TRUE
		); 
	}
	//END set_sections


	// --------------------------------------------------------------------

	/**
	 * ajax called function to force refresh of tables
	 *
	 * @access	public
	 * @return	string		section of attributes	
	 */
	
	public function process_refresh_db()
	{
		//clear caches
		$this->EE->db->query("TRUNCATE TABLE exp_solspace_update_advisor_cache");
		
		//get data again
		$this->set_sections();
		
		//return the addon area so we can replace
		return $this->sections[$this->EE->lang->line('available_addon_updates')];
	}
	//END process_refresh_db


	// --------------------------------------------------------------------

	/**
	 * gets db data in one query so we move a little faster
	 *
	 * @access	private	
	 */
	
	private function get_db_data()
	{
		$query = $this->EE->db->query(
			"SELECT 	* 
			 FROM 		exp_solspace_update_advisor_cache"
		);
		
		if ($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
				$this->db_cache[$row['cache_type']] = $row;
			}
		}
	}
	//END get_db_data


	// --------------------------------------------------------------------

	/**
	 * gets the current version of bridge or returns false
	 *
	 * @access	private
	 * @return	mixed	string version number or bool false	
	 */

	private function get_bridge_version()
	{
		//--------------------------------------------  
		//	check cache
		//--------------------------------------------		
		
		if ($this->db_cache['bridge_version'])
		{
			$current_time 	= date('YmdH') . ( (date('i') > 29) ? "30" : "00" ); 
			
			$cache_time 	= date('YmdH', $this->db_cache['bridge_version']['cache_date']) . 
						( (date('i', $this->db_cache['bridge_version']['cache_date']) > 29) ? "30" : "00" );
			
			//only use the cache if it was pulled this half hour
			if ($cache_time == $current_time)
			{
				return $this->db_cache['bridge_version']['cache_data'];
			}
		}
		
		$bridge_version = FALSE;
		
		//--------------------------------------------  
		//	as of bridge 1.1.7, the bridge version is a constant
		//--------------------------------------------
		
		$bridge_constants 	= PATH_THIRD . 'bridge/constants.php';
		
		if (file_exists($bridge_constants))
		{
			require_once $bridge_constants;
			
			if (defined('BRIDGE_VERSION'))
			{
				$bridge_version = BRIDGE_VERSION;
			}
		}
				
		//--------------------------------------------  
		//	bridge 1.1.6 and below is dirty work
		//--------------------------------------------
		
		if ( ! $bridge_version)
		{		
			$bridge_loc 	= PATH_THIRD . 'bridge/lib/addon_builder/addon_builder.php';
				
			if ( file_exists($bridge_loc) AND 
				 $bridge_file = @file_get_contents($bridge_loc) AND
				 preg_match('/public \$bridge_version[\s]+= \'([\.0-9]+)\'/', $bridge_file, $matches)
				)
			{			
				$bridge_version = $matches[1];
			}
		}
		
		//--------------------------------------------  
		//	cache to db
		//--------------------------------------------
		
		if ($bridge_version)
		{
			//remove old
			$this->EE->db->query(
				"DELETE FROM 	exp_solspace_update_advisor_cache 
				 WHERE 			cache_type = 'bridge_version'"
			);

			//insert our new data for caching
			$this->EE->db->query(
				$this->EE->db->insert_string(
					'exp_solspace_update_advisor_cache', 
					array(
						'cache_date' => time(),
						'cache_data' => $bridge_version,
						'cache_type' => 'bridge_version'
					)
				)
			);
		}
		
		return $bridge_version;
	}
	//END get_bridge_version


	// --------------------------------------------------------------------

	/**
	 * install accessory
	 *
	 * @access	public
	 */

	public function install()
	{
		$this->EE->db->query(
			"CREATE TABLE IF NOT EXISTS `exp_solspace_update_advisor_cache` (
				`cache_type`	varchar(50) 	 	NOT NULL,
				`cache_date` 	int(10) 			NOT NULL,
				`cache_data` 	text 				NOT NULL,
				PRIMARY KEY 	(`cache_type`)
			) CHARACTER SET utf8 COLLATE utf8_general_ci"
		);
	}
	//END install


	// --------------------------------------------------------------------

	/**
	 * uninstall accessory
	 *
	 * @access	public
	 */
		
	public function uninstall()
	{
		$this->EE->db->query("DROP TABLE `exp_solspace_update_advisor_cache`");
	}
	//END uninstall
	

	// --------------------------------------------------------------------

	/**
	 * Fetch the addon json from cache or remote
	 *
	 * @access	private
	 * @return	string
	 */

	private function get_installed_addons()
	{	
		//--------------------------------------------  
		//	check for cache
		//--------------------------------------------
				
		if ($this->db_cache['local_addons'])
		{
			$current_time = date('YmdH') . ( (date('i') > 29) ? "30" : "00" ); 
			
			$cache_time = date('YmdH', $this->db_cache['local_addons']['cache_date']) . 
						( (date('i', $this->db_cache['local_addons']['cache_date']) > 29) ? "30" : "00" );
			
			//only use the cache if it was pulled this half hour
			if ($cache_time == $current_time)
			{
				return json_decode($this->db_cache['local_addons']['cache_data'], TRUE);
			}
		}
		
		$addon_data = array();
		
		$this->EE->lang->loadfile('addons');
		$this->EE->load->model('addons_model');
		
		//get all addon data if no cache is found
		$plugins 		= $this->EE->addons_model->get_plugins();
		$extensions 	= $this->EE->addons_model->get_installed_extensions();
		$modules 		= $this->EE->addons_model->get_installed_modules();
		$accessories 	= $this->get_installed_accessories();
		
		//--------------------------------------------  
		//	parse all addons as lower_classname => version
		//--------------------------------------------
		
		foreach ($plugins as $plugin_name => $plugin_info )
		{
			$addon_data['plugins'][strtolower($plugin_name)] = $plugin_info['pi_version'];
		}
		
		foreach ($extensions->result_array() as $row )
		{			
			$addon_data['extensions'][strtolower(str_replace('_ext', '', $row['class']))] = $row['version'];
		}
		
		foreach ($modules->result_array() as $row )
		{
			$addon_data['modules'][strtolower($row['module_name'])] = $row['module_version'];
		}
		
		foreach ($accessories->result_array() as $row )
		{
			$addon_data['accessories'][strtolower(str_replace('_acc', '', $row['class']))] = $row['accessory_version'];
		}
				
		//remove old
		$this->EE->db->query(
			"DELETE FROM 	exp_solspace_update_advisor_cache 
			 WHERE 			cache_type = 'local_addons'"
		);
		
		//insert our new data for caching
		$this->EE->db->query(
			$this->EE->db->insert_string(
				'exp_solspace_update_advisor_cache', 
				array(
					'cache_date' => time(),
					'cache_data' => $this->json_encode($addon_data),
					'cache_type' => 'local_addons'
				)
			)
		);
		
		return $addon_data;
	}	
	//END get installed addons
	
	
	// --------------------------------------------------------------------

	/**
	 * Fetch the addon json from cache or remote
	 *
	 * @access	private
	 * @return	string
	 */

	private function get_solspace_addons()
	{
		//--------------------------------------------  
		//	check for cache
		//--------------------------------------------
				
		if ($this->db_cache['solspace_addons'])
		{
			//only use the cache if it was pulled today
			if (date('Ymd', $this->db_cache['solspace_addons']['cache_date']) == date('Ymd'))
			{
				return json_decode($this->db_cache['solspace_addons']['cache_data'], TRUE);
			}
		}
		
		//--------------------------------------------  
		//	if we are here, its time to get some remote data
		//--------------------------------------------
		
		$remote_json_data 	= $this->fetch_url($this->addon_json_url);
				
				
		//no data? :( bail
		if ($remote_json_data == '')
		{
			return '';
		}
		
		//does the decoded json fail? return empty
		
		$decoded_json = @json_decode($remote_json_data, TRUE);
		
		if ( ! $decoded_json)
		{
			return '';
		}
		
		//remove old
		$this->EE->db->query(
			"DELETE FROM 	exp_solspace_update_advisor_cache 
			 WHERE 			cache_type = 'solspace_addons'"
		);
		
		//insert our new data for caching
		$this->EE->db->query(
			$this->EE->db->insert_string(
				'exp_solspace_update_advisor_cache', 
				array(
					'cache_date' => time(),
					'cache_data' => $remote_json_data,
					'cache_type' => 'solspace_addons'
				)
			)
		);
		
		return $decoded_json;
	}
	//end get_solspace_addons

	
	// --------------------------------------------------------------------

	/**
	 * Get Installed Acessories
	 *
	 * @access	private
	 * @return	array
	 */
	private function get_installed_accessories()
	{
		$this->EE->db->select('class, accessory_version');
		
		return $this->EE->db->get('accessories');
	}
	//END get_installed_accessories
	
	
	// --------------------------------------------------------------------

	/**
	 * Fetch the Data for a URL
	 *
	 * @access	private
	 * @param	string		$url - The URI that we are fetching
	 * @param	array		$post - The POST array we are sending
	 * @return	string
	 */
    
	function fetch_url($url, $post = array())
    {
    	$data = '';
    	
    	// --------------------------------------------
        //  file_get_contents()
        // --------------------------------------------
    	
    	if ((bool) @ini_get('allow_url_fopen') !== FALSE AND empty($post))
		{
			if ($data = @file_get_contents($url))
			{
				return trim($data);
			}
		}
		
		// --------------------------------------------
        //  cURL
        // --------------------------------------------

		if (function_exists('curl_init') === TRUE && ($ch = @curl_init()) !== FALSE)
		{
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			
			// prevent a PHP warning on certain servers
			if (! ini_get('safe_mode') && ! ini_get('open_basedir'))
			{
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
			}
			
			//	Are we posting?
			if ( ! empty( $post ) )
			{
				$str	= '';
				
				foreach ( $post as $key => $val )
				{
					$str	.= urlencode( $key ) . "=" . urlencode( $val ) . "&";
				}
				
				$str	= substr( $str, 0, -1 );
			
				curl_setopt( $ch, CURLOPT_POST, TRUE );
				curl_setopt( $ch, CURLOPT_POSTFIELDS, $str );
			}
			
			curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
			curl_setopt($ch, CURLOPT_TIMEOUT, 15);
			curl_setopt($ch, CURLOPT_HEADER, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			$data = curl_exec($ch);
			curl_close($ch);

			if ($data !== FALSE)
			{
				return trim($data);
			}
		}
		
		// --------------------------------------------
        //  fsockopen() - Last but only slightly least...
        // --------------------------------------------
		
		$parts	= parse_url($url);
		$host	= $parts['host'];
		$path	= (!isset($parts['path'])) ? '/' : $parts['path'];
		$port	= ($parts['scheme'] == "https") ? '443' : '80';
		$ssl	= ($parts['scheme'] == "https") ? 'ssl://' : '';
		
		if (isset($parts['query']) && $parts['query'] != '')
		{
			$path .= '?'.$parts['query'];
		}
		
		$data = '';

		$fp = @fsockopen($ssl.$host, $port, $error_num, $error_str, 7); 

		if (is_resource($fp))
		{
			$getpost	= ( ! empty( $post ) ) ? 'POST ': 'GET ';
		
			fputs($fp, $getpost.$path." HTTP/1.0\r\n" ); 
			fputs($fp, "Host: ".$host . "\r\n" );
			
			if ( ! empty( $post ) )
			{
				$str	= '';
				
				foreach ( $post as $key => $val )
				{
					$str	.= urlencode( $key ) . "=" . urlencode( $val ) . "&";
				}
				
				$str	= substr( $str, 0, -1 );

				fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
				fputs($fp, "Content-length: " . strlen( $str ) . "\r\n");
			}
			
			fputs($fp, "User-Agent: Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.2.1)\r\n");
			fputs($fp, "Connection: close\r\n\r\n");
			
			if ( ! empty( $post ) )
			{
				fputs($fp, $str . "\r\n\r\n");
			}
			
			// ------------------------------
			//  This error suppression has to do with a PHP bug involving
			//  SSL connections: http://bugs.php.net/bug.php?id=23220
			// ------------------------------
			
			$old_level = error_reporting(0);
			
			while ( ! feof($fp))
			{
				$data .= trim(fgets($fp, 128));
			}
			
			error_reporting($old_level);

			fclose($fp); 
		}
		
		return trim($data); 
	}
	// END fetch_url()
	
	
	// --------------------------------------------------------------------

	/**
	 * encodes json navitvely and falls back on an encode if not found
	 *
	 * @access	private
	 * @param	mixed		data to be turned to json data
	 * @return	string		encoded json data
	 */
	
	private function json_encode($data)
	{
		if ( function_exists('json_encode'))
		{
			return json_encode($data);
		}
		else
		{
			return $this->__json_encode($data);
		}
	}
	//END json_encode


	// --------------------------------------------------------------------

	/**
	 * backup if json_encode isnt present. EE 2.x has its own custom json_encode, 
	 * but this is more like native
	 *
	 * @access	private
	 * @param	mixed		data to be turned to json data
	 * @return	string		encoded json data
	 */

	private function __json_encode( $data ) 
	{           
	    if ( is_array($data) || is_object($data) ) 
		{
	        $islist = is_array($data) && ( empty($data) || array_keys($data) === range(0,count($data)-1) );

	        if( $islist ) {
	            $json = '[' . implode(',', array_map('__json_encode', $data) ) . ']';
	        } else {
	            $items = Array();
	            foreach( $data as $key => $value ) {
	                $items[] = $this->__json_encode("$key") . ':' . $this->__json_encode($value);
	            }
	            $json = '{' . implode(',', $items) . '}';
	        }
	    } 
		elseif( is_string($data) ) 
		{
	        # Escape non-printable or Non-ASCII characters.
	        # I also put the \\ character first, as suggested in comments on the 'addclashes' page.
	        $string = '"' . addcslashes($data, "\\\"\n\r\t/" . chr(8) . chr(12)) . '"';
	        $json    = '';
	        $len    = strlen($string);
	        # Convert UTF-8 to Hexadecimal Codepoints.
	        for( $i = 0; $i < $len; $i++ ) {

	            $char = $string[$i];
	            $c1 = ord($char);

	            # Single byte;
	            if( $c1 <128 ) {
	                $json .= ($c1 > 31) ? $char : sprintf("\\u%04x", $c1);
	                continue;
	            }

	            # Double byte
	            $c2 = ord($string[++$i]);
	            if ( ($c1 & 32) === 0 ) {
	                $json .= sprintf("\\u%04x", ($c1 - 192) * 64 + $c2 - 128);
	                continue;
	            }

	            # Triple
	            $c3 = ord($string[++$i]);
	            if( ($c1 & 16) === 0 ) {
	                $json .= sprintf("\\u%04x", (($c1 - 224) <<12) + (($c2 - 128) << 6) + ($c3 - 128));
	                continue;
	            }

	            # Quadruple
	            $c4 = ord($string[++$i]);
	            if( ($c1 & 8 ) === 0 ) {
	                $u = (($c1 & 15) << 2) + (($c2>>4) & 3) - 1;

	                $w1 = (54<<10) + ($u<<6) + (($c2 & 15) << 2) + (($c3>>4) & 3);
	                $w2 = (55<<10) + (($c3 & 15)<<6) + ($c4-128);
	                $json .= sprintf("\\u%04x\\u%04x", $w1, $w2);
	            }
	        }
	    } else {
	        # int, floats, bools, null
	        $json = strtolower(var_export( $data, true ));
	    }
	    return $json;
	}
	//END json_encode
	
}
// END CLASS