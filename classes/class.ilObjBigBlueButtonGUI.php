<?php
/*
 +-----------------------------------------------------------------------------+
 | ILIAS open source                                                           |
 +-----------------------------------------------------------------------------+
 | Copyright (c) 1998-2009 ILIAS open source, University of Cologne            |
 |                                                                             |
 | This program is free software; you can redistribute it and/or               |
 | modify it under the terms of the GNU General Public License                 |
 | as published by the Free Software Foundation; either version 2              |
 | of the License, or (at your option) any later version.                      |
 |                                                                             |
 | This program is distributed in the hope that it will be useful,             |
 | but WITHOUT ANY WARRANTY; without even the implied warranty of              |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the               |
 | GNU General Public License for more details.                                |
 |                                                                             |
 | You should have received a copy of the GNU General Public License           |
 | along with this program; if not, write to the Free Software                 |
 | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA. |
 +-----------------------------------------------------------------------------+
 */


include_once("./Services/Repository/classes/class.ilObjectPluginGUI.php");


/**
 * User  class for BigBlueButton repository object.
 *
 * User  classes process GET and POST parameter and call
 * application classes to fulfill certain tasks.
 *
 *
 * $Id$
 *
 * Integration into control structure:
 * - The GUI class is called by ilRepositoryGUI
 * - GUI classes used by this class are ilPermissionGUI (provides the rbac
 *   screens) and ilInfoScreenGUI (handles the info screen).
 *
 * @ilCtrl_isCalledBy ilObjBigBlueButtonGUI: ilRepositoryGUI, ilAdministrationGUI, ilObjPluginDispatchGUI
 * @ilCtrl_Calls ilObjBigBlueButtonGUI: ilPermissionGUI, ilInfoScreenGUI, ilObjectCopyGUI
 * @ilCtrl_Calls ilObjBigBlueButtonGUI: ilCommonActionDispatcherGUI
 *
 */
class ilObjBigBlueButtonGUI extends ilObjectPluginGUI
{
	/**
	 * Initialisation
	 */
	protected function afterConstructor()
	{
		// anything needed after object has been constructed
		// - example: append my_id GET parameter to each request
		//   $ilCtrl->saveParameter($this, array("my_id"));

		//$this->deactivateCreationForm(ilObject2GUI::CFORM_IMPORT);
		//$this->deactivateCreationForm(ilObject2GUI::CFORM_CLONE);
            $this->tpl->addCss("./Customizing/global/plugins/Services/Repository/RepositoryObject/BigBlueButton/templates/bbb.css");
            //$my_tpl->addCss( "./Customizing/global/plugins/Services/Repository/RepositoryObject/BigBlueButton/templates/bbb.css");
	}

	/**
	 * Get type.
	 */
	final function getType()
	{
		return "xbbb";
	}

	/**
	 * Handles all commmands of this class, centralizes permission checks
	 */
	function performCommand($cmd)
	{
		$this->setTitleAndDescription();
		
		switch ($cmd)
		{
			case "editProperties":		// list all commands that need write permission here
			case "updateProperties":
			case "endClass":
			case "startClass":
                        case "deleteRecording":    
				//case "...":
				$this->checkPermission("write");
				$this->$cmd();
				break;
					
			case "showContent":			// list all commands that need read permission here
				//case "...":
				//case "...":
					$this->checkPermission("read");
					$this->$cmd();
					break;
		}
	}

	/**
	 * After object has been created -> jump to this command
	 */
	function getAfterCreationCmd()
	{
		return "editProperties";
	}

	/**
	 * Get standard command
	 */
	function getStandardCmd()
	{
		return "showContent";
	}

	//
	// DISPLAY TABS
	//

	/**
	 * Set tabs
	 */
	function setTabs()
	{
		global $ilTabs, $ilCtrl, $ilAccess;

		// tab for the "show content" command
		if ($ilAccess->checkAccess("read", "", $this->object->getRefId()))
		{
			$ilTabs->addTab("content", $this->txt("content"), $ilCtrl->getLinkTarget($this, "showContent"));
		}

		// standard info screen tab
		$this->addInfoTab();

		// a "properties" tab
		if ($ilAccess->checkAccess("write", "", $this->object->getRefId()))
		{
			$ilTabs->addTab("properties", $this->txt("properties"), $ilCtrl->getLinkTarget($this, "editProperties"));
		}

		// standard permission tab
		$this->addPermissionTab();
	}



	/**
	 * Edit Properties. This commands uses the form class to display an input form.
	 */
	function editProperties()
	{
		global $tpl, $ilTabs;

		$ilTabs->activateTab("properties");
		$this->initPropertiesForm();
		$this->getPropertiesValues();
		$tpl->setContent($this->form->getHTML());
	}

	/**
	 * Init  form.
	 *
	 * @param        int        $a_mode        Edit Mode
	 */
	public function initPropertiesForm()
	{
		global $ilCtrl;

		include_once("Services/Form/classes/class.ilPropertyFormGUI.php");
		$this->form = new ilPropertyFormGUI();

		// title
		$ti = new ilTextInputGUI($this->txt("title"), "title");
		$ti->setRequired(true);
		$this->form->addItem($ti);

		// description
		$ta = new ilTextAreaInputGUI($this->txt("description"), "desc");
		$this->form->addItem($ta);

		// online
		$cb = new ilCheckboxInputGUI($this->lng->txt("online"), "online");
		$this->form->addItem($cb);

		// welcometext
		$ti = new ilTextInputGUI($this->txt("welcometext"), "welcometext");
		$ti->setMaxLength(1000);
		$ti->setSize(120);
		$this->form->addItem($ti);



		$this->form->addCommandButton("updateProperties", $this->txt("save"));
		 
		$this->form->setTitle($this->txt("edit_properties"));
		$this->form->setFormAction($ilCtrl->getFormAction($this));
	}

	/**
	 * Get values for edit properties form
	 */
	function getPropertiesValues()
	{
		$values["title"] = $this->object->getTitle();
		$values["desc"] = $this->object->getDescription();
		$values["online"] = $this->object->getOnline();
		$values["welcometext"] = $this->object->getWelcomeText();
		$this->form->setValuesByArray($values);

	}

	/**
	 * Update properties
	 */
	public function updateProperties()
	{
		global $tpl, $lng, $ilCtrl;

		$this->initPropertiesForm();
		if ($this->form->checkInput())
		{
			$this->object->setTitle($this->form->getInput("title"));
			$this->object->setDescription($this->form->getInput("desc"));
			$this->object->setWelcomeText($this->form->getInput("welcometext"));
			$this->object->setOnline($this->form->getInput("online"));

			$this->object->update();
			ilUtil::sendSuccess($lng->txt("msg_obj_modified"), true);
			$ilCtrl->redirect($this, "editProperties");
		}

		$this->form->setValuesByPost();
		$tpl->setContent($this->form->getHtml());
	}



	//
	// Show content
	//

	/**
	 * Show content
	 */
	function showContent()
	{
		global $tpl, $ilTabs, $ilUser, $ilCtrl, $ilDB;;

		$values = array();
		$result = $ilDB->query("SELECT * FROM rep_robj_xbbb_conf");

		while ($record = $ilDB->fetchAssoc($result))
		{
			$svrPublicURL = $record["svrpublicurl"];
			$svrPublicPort = $record["svrpublicport"];
			$values["choose_recording"] = $record["choose_recording"];
		}



		global $ilCtrl, $ilAccess;
		//$ilTabs->clearTargets();
                $ilTabs->activateTab("content");

		$isModerator=false;

		if($ilAccess->checkAccess("write", "showContent", $this->object->getRefId())){
			$isModerator=true;
		}

		include_once("./Customizing/global/plugins/Services/Repository/RepositoryObject/BigBlueButton/classes/class.ilBigBlueButtonProtocol.php");
		$BBBHelper=new ilBigBlueButtonProtocol();

		//$bbbURL=$BBBHelper->createAndGetURL($this->object,$isModerator);
		
		
		if($isModerator){
			$my_tpl = new ilTemplate("./Customizing/global/plugins/Services/Repository/RepositoryObject/BigBlueButton/templates/tpl.BigBlueButtonModeratorClient.html", true, true);
			
			$my_tpl->setVariable("CMD_END_CLASS","cmd[endClass]");
			$my_tpl->setVariable("END_CLASS",$this->txt('end_bbb_class'));
			$my_tpl->setVariable("FORMACTION",$this->ctrl->getFormAction($this));
			
			
			$my_tpl->setVariable("CMD_START_CLASS","cmd[startClass]");
			$my_tpl->setVariable("START_CLASS",$this->txt('start_bbb_class'));
			$my_tpl->setVariable("FORMACTION2",$this->ctrl->getFormAction($this));

            $my_tpl->setVariable("CMD_DELETE_RECORDING","cmd[deleteRecording]");
			$my_tpl->setVariable("DELETE_RECORDING",$this->txt('delete_bbb_recording'));
			$my_tpl->setVariable("FORMACTION3",$this->ctrl->getFormAction($this));
                        
			$my_tpl->setVariable("classRunning", $this->txt("class_running"));
			$my_tpl->setVariable("noClassRunning", $this->txt("no_class_running"));
			$my_tpl->setVariable("startClass", $this->txt("start_class"));
			$my_tpl->setVariable("endClass", $this->txt("end_class"));
			$my_tpl->setVariable("endClassComment", $this->txt("end_class_comment"));
                        
			$table_template = new ilTemplate("tpl.BigBlueButtonRecordTable.html",
								true,
								true,
								"Customizing/global/plugins/Services/Repository/RepositoryObject/BigBlueButton");
			
			$table_content ="";
			$recordcount=0;
			$all_recordings=$BBBHelper->getRecordings($this->object)->recordings->recording;
			if ($all_recordings){
				foreach($all_recordings as $recording){
					$table_row_template = new ilTemplate("tpl.BigBlueButtonRecordTableRow.html",
									true,
									true,
									"Customizing/global/plugins/Services/Repository/RepositoryObject/BigBlueButton");
					$table_row_template->setVariable("Date",date("d.m.Y H:i",  substr ($recording->startTime,0,10)));
					$table_row_template->setVariable("Length",$recording->playback->format->length);
					$table_row_template->setVariable("Link",$recording->playback->format->url);
					$table_row_template->setVariable("DeleteLink", $recording->recordID);
					
					$table_row_template->setVariable("Link_Title", $this->txt("link_title"));
					$table_row_template->setVariable("DeleteLink_Title", $this->txt("deletelink_title"));
					
					$table_content .= $table_row_template->get();
					$recordcount++;
				}
			}                        
			$table_template->setVariable("BBB_RECORD_CONTENT", $table_content);
			$table_template->setVariable("Date_Title", $this->txt("Date_Title"));
			$table_template->setVariable("Length_Title", $this->txt("Length_Title"));
			$table_template->setVariable("Link_Title", $this->txt("Link_Title"));
			$my_tpl->setVariable("recordings", $table_template->get());  
			$my_tpl->setVariable("Headline_Recordings", $this->txt("Headline_Recordings"));
			if ($values["choose_recording"]){
				$my_tpl->setVariable("CHOOSE_RECORDING_VISIBLE", "visible");
			}else{
				$my_tpl->setVariable("CHOOSE_RECORDING_VISIBLE", "hidden");
			}
			$my_tpl->setVariable("checkbox_record_meeting", $this->txt("checkbox_record_meeting"));
			$my_tpl->setVariable("hasMeetingRecordings", $recordcount > 0?"true":"false");

			$bbbURL=$BBBHelper->joinURLModerator($this->object);
		}else{
			$my_tpl = new ilTemplate("./Customizing/global/plugins/Services/Repository/RepositoryObject/BigBlueButton/templates/tpl.BigBlueButtonClient.html", true, true);

			$my_tpl->setVariable("classNotStartedText", $this->txt("class_not_started_yet"));
			
			$bbbURL=$BBBHelper->joinURL($this->object);
		}
		
		$my_tpl->setVariable("clickToOpenClass", $this->txt("click_to_open_class"));
		
		
		$isMeetingRunning=$BBBHelper->isMeetingRunning($this->object);
                
		$my_tpl->setVariable("isMeetingRunning", $isMeetingRunning?"true":"false");
                
                $isMeetingRecorded = $BBBHelper->isMeetingRecorded($this->object);
                
                $my_tpl->setVariable("isMeetingRecorded", $isMeetingRecorded?"true":"false");
		
		$my_tpl->setVariable("bbbURL", $bbbURL);
                
                $my_tpl->setVariable("meetingRecordedMessage", $this->txt("meetingRecordedMessage"));
                
                

		$tpl->setContent($my_tpl->get());
	}

	function endClass(){
		
		global $tpl, $ilTabs;
		
		//$ilTabs->clearTargets();
                $ilTabs->activateTab("content");
	
		include_once("./Customizing/global/plugins/Services/Repository/RepositoryObject/BigBlueButton/classes/class.ilBigBlueButtonProtocol.php");
		$BBBHelper=new ilBigBlueButtonProtocol();
		$BBBHelper->endMeeting($this->object);
		
		//$this->object->incSequence();
		
		$my_tpl = new ilTemplate("./Customizing/global/plugins/Services/Repository/RepositoryObject/BigBlueButton/templates/tpl.BigBlueButtonModeratorMeetingEnded.html", true, true);
		
		$my_tpl->setVariable("classEnded", $this->txt("class_ended"));
		
		$tpl->setContent($my_tpl->get());
                
                $this->showContent();
	}
	
	function startClass(){
	
		global $tpl, $ilTabs;
		
		//$ilTabs->clearTargets();
		$ilTabs->activateTab("content");
                
		include_once("./Customizing/global/plugins/Services/Repository/RepositoryObject/BigBlueButton/classes/class.ilBigBlueButtonProtocol.php");
		$BBBHelper=new ilBigBlueButtonProtocol();
		
		$BBBHelper->createMeeting($this->object, isset($_POST["recordmeeting"]));
		
		$my_tpl = new ilTemplate("./Customizing/global/plugins/Services/Repository/RepositoryObject/BigBlueButton/templates/tpl.BigBlueButtonModeratorMeetingCreated.html", true, true);
		
		//$tpl->addJavaScript("./Customizing/global/plugins/Services/Repository/RepositoryObject/BigBlueButton/js/jquery-1.5.2.min.js");
		
		$bbbURL=$BBBHelper->joinURLModerator($this->object);

		$my_tpl->setVariable("newClassCreated", $this->txt("new_class_created"));
		$my_tpl->setVariable("newClassCreatedWarning", $this->txt("new_class_created_warning"));
		$my_tpl->setVariable("newClassCreatedJoinManual", $this->txt("new_class_created_join_manual"));
		$my_tpl->setVariable("bbbURL", $bbbURL);
		
		$tpl->setContent($my_tpl->get());
	}
	
	function deleteRecording(){
		
		global $tpl, $ilTabs;
		
		//$ilTabs->clearTargets();
		$ilTabs->activateTab("content");
                include_once("./Customizing/global/plugins/Services/Repository/RepositoryObject/BigBlueButton/classes/class.ilBigBlueButtonProtocol.php");
		
                $BBBHelper=new ilBigBlueButtonProtocol();
                $BBBHelper->deleteRecording($this->object, $_POST["recordID"]);
                $this->showContent();
        
        }

}
?>
