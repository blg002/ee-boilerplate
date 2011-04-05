<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Reelocate module by Crescendo (support@crescendo.net.nz)
 * 
 * Copyright (c) 2010 Crescendo Multimedia Ltd
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 * 3. The name of the author may not be used to endorse or promote products
 *    derived from this software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE AUTHOR "AS IS" AND ANY EXPRESS OR
 * IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
 * OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
 * IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT
 * NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
 * THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

class Reelocate_upd { 
	
	var $version = '1.2';
	
	public function __construct()
	{
		$this->EE =& get_instance();
	}
	
	public function install()
	{	
		// register module
		$this->EE->db->insert('modules', array(
			'module_name' => 'Reelocate',
			'module_version' => $this->version,
			'has_cp_backend' => 'y',
			'has_publish_fields' => 'n'));
		
		return TRUE;
	}
	
	public function update($current = '')
	{
		return $current < $this->version;
	}
	
	public function uninstall()
	{
		$this->EE->db->where('module_name', 'Reelocate');
		$this->EE->db->delete('modules');
		
		return TRUE;
	}
}

/* End of file ./system/expressionengine/third_party/reelocate/upd.reelocate.php */