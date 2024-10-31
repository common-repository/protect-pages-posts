<?php
class class_update_htaccess {
	private $pyp_function;
	public function check_htaccess_updated(){
		$htaccess_writable = $this->pyp_function->htaccess_writable();
		$plugin = plugin_basename(__FILE__);
		$is_plugin_active = is_plugin_active($plugin);
		if($htaccess_writable	!==	 true && $is_plugin_active){
			delete_option('updated_htaccess_success');
		}
		$updated_htaccess_success = get_option('updated_htaccess_success',false);
		if ($updated_htaccess_success === true){
			return;
		}
		if ( $htaccess_writable === true && $is_plugin_active) {
            flush_rewrite_rules(); // re-trigger mod_rewrite_rules
            add_option('updated_htaccess_success', true);
        }
	}

}
