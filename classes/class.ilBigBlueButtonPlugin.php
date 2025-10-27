<?php
require_once __DIR__ . "/../vendor/autoload.php";

/**
* Bigbluebutton repository object plugin
*
*
*/
class ilBigBlueButtonPlugin extends ilRepositoryObjectPlugin
{
    public function getPluginName(): string
    {
        return "BigBlueButton";
    }

    protected function uninstallCustom(): void
    {
        global $ilDB;
        // removes plugin tables if they exist
         if($ilDB->tableExists('rep_robj_xbbb_data'))
         	$ilDB->dropTable('rep_robj_xbbb_data');

         if($ilDB->tableExists('rep_robj_xbbb_conf'))
         	$ilDB->dropTable('rep_robj_xbbb_conf');

    }

    // public static function _getIcon(string $a_type): string
    // {
    //     return "Customizing/plugins/Repository/RepositoryObject/BigBlueButton/templates/images/icon_" . $a_type . ".svg";
    // }

    public static function _getIcon(string $a_type): string
    {
        $plugin_folder = "BigBlueButton";

        return "Customizing/global/plugins/Services/Repository/RepositoryObject/" . $plugin_folder
            . "/templates/images/icon_" . $a_type . ".svg";
    }


}
