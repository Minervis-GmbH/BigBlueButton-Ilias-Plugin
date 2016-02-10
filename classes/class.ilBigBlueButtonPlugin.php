<?php

include_once("./Services/Repository/classes/class.ilRepositoryObjectPlugin.php");
 
/**
* Big Blue Button repository object plugin
*
*
*/
class ilBigBlueButtonPlugin extends ilRepositoryObjectPlugin
{
	function getPluginName()
	{
		return "BigBlueButton";
	}

	protected function uninstallCustom() {
                global $ilDB;
				// removes plugin tables if they exist                
               /* if($ilDB->tableExists('rep_robj_xbbb_data'))
                	$ilDB->dropTable('rep_robj_xbbb_data');
                	
                if($ilDB->tableExists('rep_robj_xbbb_conf'))
                	$ilDB->dropTable('rep_robj_xbbb_conf');*/
    		return true;
	}
}
?>
