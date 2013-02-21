<?php

include_once("./Services/Component/classes/class.ilPluginConfigGUI.php");
 
/**
 * BigBlueButton configuration class
 *
 * @version $Id$
 *
 */
class ilBigBlueButtonConfigGUI extends ilPluginConfigGUI
{
	/**
	* Handles all commmands, default is "configure"
	*/
	function performCommand($cmd)
	{

		switch ($cmd)
		{
			case "configure":
			case "save":
				$this->$cmd();
				break;

		}
	}

	/**
	 * Configure screen
	 */
	function configure()
	{
		global $tpl;

		$form = $this->initConfigurationForm();
		$tpl->setContent($form->getHTML());
		
	}
	
	
	/**
	 * Init configuration form.
	 *
	 * @return object form object
	 */
	public function initConfigurationForm()
	{
		global $lng, $ilCtrl, $ilDB;
		 
		$values = array();
		$result = $ilDB->query("SELECT * FROM rep_robj_xbbb_conf");
		while ($record = $ilDB->fetchAssoc($result))
		{
	        $values["svrpublicurl"] = $record["svrpublicurl"];
	        $values["svrpublicport"] = $record["svrpublicport"];
	        $values["svrprivateurl"] = $record["svrprivateurl"];
	        $values["svrprivateport"] = $record["svrprivateport"];
	        $values["svrsalt"] = $record["svrsalt"];
		}

		
		$pl = $this->getPluginObject();
	
		include_once("Services/Form/classes/class.ilPropertyFormGUI.php");
		$form = new ilPropertyFormGUI();
		
	
		
		// public url (text)
		$ti = new ilTextInputGUI($pl->txt("publicurl"), "frmpublicurl");
		$ti->setRequired(true);
		$ti->setMaxLength(256);
		$ti->setSize(60);
		$ti->setValue($values["svrpublicurl"]);
		$form->addItem($ti);
		
		// public port (text)
		$ti = new ilTextInputGUI($pl->txt("publicport"), "frmpublicport");
		$ti->setRequired(true);
		$ti->setMaxLength(10);
		$ti->setSize(10);
		$ti->setValue($values["svrpublicport"]);
		$form->addItem($ti);
		
		// private url (text)
		$ti = new ilTextInputGUI($pl->txt("privateurl"), "frmprivateurl");
		$ti->setRequired(true);
		$ti->setMaxLength(256);
		$ti->setSize(60);
		$ti->setValue($values["svrprivateurl"]);
		$form->addItem($ti);
		
		// private port (text)
		$ti = new ilTextInputGUI($pl->txt("privateport"), "frmprivateport");
		$ti->setRequired(true);
		$ti->setMaxLength(10);
		$ti->setSize(10);
		$ti->setValue($values["svrprivateport"]);
		$form->addItem($ti);
		
		// salt (text)
		$ti = new ilTextInputGUI($pl->txt("salt"), "frmsalt");
		$ti->setRequired(true);
		$ti->setMaxLength(256);
		$ti->setSize(40);
		$ti->setValue( $values["svrsalt"]);
		$form->addItem($ti);
		
	
		$form->addCommandButton("save", $lng->txt("save"));
	                
		$form->setTitle($pl->txt("BigBlueButton_plugin_configuration"));
		$form->setFormAction($ilCtrl->getFormAction($this));
		
		return $form;
	}
	
	
	/**
	 * Save form input
	 *
	 */
	public function save()
	{
		global $tpl, $lng, $ilCtrl, $ilDB;
	
		$pl = $this->getPluginObject();
		
		$form = $this->initConfigurationForm();
		if ($form->checkInput())
		{
			$setPublicURL = $form->getInput("frmpublicurl");
			$setPublicPort = $form->getInput("frmpublicport");
			$setPrivateURL = $form->getInput("frmprivateurl");
			$setPrivatePort = $form->getInput("frmprivateport");
			$setSalt= $form->getInput("frmsalt");
			
			// check if data exisits decide to update or insert
			$result = $ilDB->query("SELECT * FROM rep_robj_xbbb_conf");
			$num = $ilDB->numRows($result);
			if($num == 0){
				$ilDB->manipulate("INSERT INTO rep_robj_xbbb_conf ".
				"(id, svrpublicurl , svrpublicport, svrprivateurl , svrprivateport , svrsalt) VALUES (".
				$ilDB->quote(1, "integer").",". // id
				$ilDB->quote($setPublicURL, "text").",". //public url
				$ilDB->quote($setPublicPort , "integer").",". //public port
				$ilDB->quote($setPrivateURL, "text").",". //private url
				$ilDB->quote($setPrivatePort , "integer").",". //privateport
	            $ilDB->quote($setSalt, "text"). //salt
				")");
			}else{
				$ilDB->manipulate($up = "UPDATE rep_robj_xbbb_conf  SET ".
				" svrpublicurl = ".$ilDB->quote($setPublicURL, "text").",".
				" svrpublicport = ".$ilDB->quote($setPublicPort, "integer").",".
				" svrprivateurl = ".$ilDB->quote($setPublicURL, "text").",".
				" svrprivateport = ".$ilDB->quote($setPublicPort, "integer").",".
				" svrsalt = ".$ilDB->quote($setSalt, "text").
				" WHERE id = ".$ilDB->quote(1, "integer")
				);
			}
			
			ilUtil::sendSuccess($pl->txt("saving_invoked"), true);
			$ilCtrl->redirect($this, "configure");
		}
		else
		{
			$form->setValuesByPost();
			$tpl->setContent($form->getHtml());
		}
	}
	


}
?>
