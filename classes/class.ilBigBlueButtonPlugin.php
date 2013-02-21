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
}
?>
