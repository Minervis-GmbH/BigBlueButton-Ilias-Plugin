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
 * @ilCtrl_isCalledBy ilObjBigBlueButtonGUI: ilRepositoryGUI, ilAdministrationGUI, ilObjPluginDispatchGUI, ilUIPluginRouterGUI
 * @ilCtrl_Calls ilObjBigBlueButtonGUI: ilPermissionGUI, ilInfoScreenGUI, ilObjectCopyGUI
 * @ilCtrl_Calls ilObjBigBlueButtonGUI: ilCommonActionDispatcherGUI
 *
 */
class ilObjBigBlueButtonGUI extends ilObjectPluginGUI
{
    public bool $has_meeting_recordings = false;

    /**
     * Initialisation
     */
    protected function afterConstructor(): void
    {
        $this->tpl->addCss("./Customizing/global/plugins/Services/Repository/RepositoryObject/BigBlueButton/templates/bbb.css");
    }

    /**
     * Get type.
     */
    final public function getType(): string
    {
        return "xbbb";
    }

    /**
     * Handles all commmands of this class, centralizes permission checks
     */
    function performCommand($cmd): void
    {
        $this->setTitleAndDescription();

        switch ($cmd) {
            case "publish":
                $this->checkPermission("write");
                $this->$cmd();
                break;
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

            default:
                //nothing
        }
    }

    /**
     * After object has been created -> jump to this command
     */
    public function getAfterCreationCmd(): string
    {
        return "editProperties";
    }

    /**
     * Get standard command
     */
    public function getStandardCmd(): string
    {
        return "showContent";
    }

    //
    // DISPLAY TABS
    //

    /**
     * Set tabs
     */
    protected function setTabs(): void
    {
        global $ilTabs, $ilCtrl, $ilAccess;

        // tab for the "show content" command
        if ($ilAccess->checkAccess("read", "", $this->object->getRefId())) {
            $ilTabs->addTab("content", $this->txt("content"), $ilCtrl->getLinkTarget($this, "showContent"));
        }

        // standard info screen tab
        $this->addInfoTab();

        // a "properties" tab
        if ($ilAccess->checkAccess("write", "", $this->object->getRefId())) {
            $ilTabs->addTab("properties", $this->txt("properties"), $ilCtrl->getLinkTarget($this, "editProperties"));
        }

        // standard permission tab
        $this->addPermissionTab();
    }



    /**
     * Edit Properties. This commands uses the form class to display an input form.
     */
    public function editProperties()
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
        $ti = new ilTextAreaInputGUI($this->txt("welcometext"), "welcometext");
        $this->form->addItem($ti);

        //dial number
        $ti_dial = new ilTextInputGUI($this->txt("dialnumber"), "dialnumber");
        //$ti_dial->setSize();
        $this->form->addItem($ti_dial);

        //access code
        $ti_access = new ilNonEditableValueGUI($this->txt("accesscode"));
        $ti_access->setValue($this->object->getAccessCode());
        $this->form->addItem($ti_access);

        //duration
        $ni_duration = new ilNumberInputGUI($this->txt("max_duration"), "duration");
        $this->form->addItem($ni_duration);

        //Guest Link allow
        $cb = new ilCheckboxInputGUI($this->txt("guestchoose"), "guestchoose");
        $this->form->addItem($cb);

        //Participants
        $ni_participants=new ilNumberInputGUI($this->txt("maxparticipants"), "maxparticipants");
        $ni_participants->setInfo($this->txt("maxparticipants_info"));
        $this->form->addItem($ni_participants);

        //Download allow
        $cb_download = new ilCheckboxInputGUI($this->txt("allow_download"), "allow_download");
        $cb_download->setInfo($this->txt("allow_download_info"));
        $this->form->addItem($cb_download);

        //PresentationUrl
        $ti = new ilTextInputGUI($this->txt("presentationurl"), "presentationurl");
        $ti->setInfo($this->txt("presentationurl_info"));
        $this->form->addItem($ti);



        $this->form->addCommandButton("updateProperties", $this->txt("save"));

        $this->form->setTitle($this->txt("edit_properties"));
        $this->form->setFormAction($ilCtrl->getFormAction($this));
    }

    /**
     * Get values for edit properties form
     */
    public function getPropertiesValues()
    {
        $values["title"] = $this->object->getTitle();
        $values["desc"] = $this->object->getDescription();
        $values["online"] = $this->object->getOnline();
        $values["welcometext"] = $this->object->getWelcomeText();
        $values['accesscode'] =$this->object->getAccessCode();
        $values['dialnumber'] = $this->object->getDialNumber();
        $values['duration'] = $this->object->getMeetingDuration();
        $values['guestchoose'] = $this->object->isGuestLinkAllowed();
        $values['maxparticipants'] =$this->object->getMaxParticipants();
        $values['presentationurl'] = $this->object->getPresentationUrl();
        $values['allow_download'] = $this->object->isDownloadAllowed();
        $this->form->setValuesByArray($values);
    }

    /**
     * Update properties
     */
    public function updateProperties()
    {
        global $tpl, $lng, $ilCtrl, $DIC;

        $this->initPropertiesForm();
        if ($this->form->checkInput()) {
            $this->object->setTitle($this->form->getInput("title"));
            $this->object->setDescription($this->form->getInput("desc"));
            $this->object->setWelcomeText($this->form->getInput("welcometext"));
            $this->object->setOnline($this->form->getInput("online"));
            $this->object->setMeetingDuration($this->form->getInput("duration"));
            $this->object->setMaxParticipants($this->form->getInput("maxparticipants"));
            $this->object->setPresentationUrl($this->form->getInput("presentationurl"));
            $this->object->setDialNumber($this->form->getInput("dialnumber"));
            $this->object->setGuestLinkAllowed(($this->form->getInput("guestchoose")));
            $this->object->setDownloadAllowed(($this->form->getInput("allow_download")));

            $this->object->update();
            $this->tpl->setOnScreenMessage(ilGlobalTemplateInterface::MESSAGE_TYPE_SUCCESS, $lng->txt("msg_obj_modified"), true);
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
    public function showContent()
    {
        global $tpl, $ilTabs, $ilUser, $ilCtrl, $ilDB, $DIC, $ilAccess;

        $values = array();
        $result = $ilDB->query("SELECT * FROM rep_robj_xbbb_conf");

        while ($record = $ilDB->fetchAssoc($result)) {
            $svrPublicURL = $record["svrpublicurl"];
            $values["choose_recording"] = $record["choose_recording"];
        }

        $ilTabs->activateTab("content");

        $isModerator=false;

        if ($ilAccess->checkAccess("write", "showContent", $this->object->getRefId())) {
            $isModerator=true;
        }

        $BBBHelper=new ilBigBlueButtonProtocol($this->object);

        $available_sessions = $BBBHelper->getMaximumSessionsAvailable();
        //$BBBHelper->getMeetings();

        $client_js = new ilTemplate("./Customizing/global/plugins/Services/Repository/RepositoryObject/BigBlueButton/templates/client.js", true, true);

        if ($isModerator) {
            $my_tpl = new ilTemplate("./Customizing/global/plugins/Services/Repository/RepositoryObject/BigBlueButton/templates/tpl.BigBlueButtonModeratorClient.html", true, true);

            $my_tpl->setVariable("CMD_END_CLASS", "cmd[endClass]");
            $my_tpl->setVariable("END_CLASS", $this->txt('end_bbb_class'));
            $my_tpl->setVariable("FORMACTION", $this->ctrl->getFormAction($this));

            $my_tpl->setVariable("CMD_START_CLASS", "cmd[startClass]");
            $my_tpl->setVariable("START_CLASS", $this->txt('start_bbb_class'));
            $my_tpl->setVariable("FORMACTION2", $this->ctrl->getFormAction($this));

            $my_tpl->setVariable("CMD_DELETE_RECORDING", "cmd[deleteRecording]");
            $my_tpl->setVariable("DELETE_RECORDING", $this->txt('delete_bbb_recording'));
            $my_tpl->setVariable("FORMACTION3", $this->ctrl->getFormAction($this));

            $my_tpl->setVariable("classRunning", $this->txt("class_running"));
            $my_tpl->setVariable("noClassRunning", $this->txt("no_class_running"));
            $my_tpl->setVariable("startClass", $this->txt("start_class"));
            $my_tpl->setVariable("endClass", $this->txt("end_class"));
            $my_tpl->setVariable("endClassComment", $this->txt("end_class_comment"));
            if($this->object->isGuestGlabalAllowed() && $this->object->isGuestLinkAllowed()){
                $my_tpl->setVariable("GUEST_INVITE_INFO", $this->txt("guest_invite_info"));
                $my_tpl->setVariable("GUEST_INVITE_URL", $BBBHelper->getInviteUrl());
            }else{
                $my_tpl->setVariable("HIDE_GUESTLINK", "hide");
            }

            if ($values["choose_recording"]){
                $my_tpl->setVariable("recordings", $this->buildRecordingUI());
                $my_tpl->setVariable("Headline_Recordings", $this->txt("Headline_Recordings"));
                $my_tpl->setVariable("checkbox_record_meeting", $this->txt("checkbox_record_meeting"));
            }else{
                $my_tpl->setVariable("CHOOSE_RECORDING_VISIBLE", "hidden");

            }           
            $client_js->setVariable("hasMeetingRecordings", $this->has_meeting_recordings  && boolval($values["choose_recording"]) ? "true" : "false");
            $client_js->setCurrentBlock('moderator');
            $client_js->setVariable("DUMMY_VAL", 1);
            $client_js->parseCurrentBlock();


            $bbbURL=$BBBHelper->joinURLModerator($this->object);
        } else {
            $my_tpl = new ilTemplate("./Customizing/global/plugins/Services/Repository/RepositoryObject/BigBlueButton/templates/tpl.BigBlueButtonClient.html", true, true);

            $my_tpl->setVariable("classNotStartedText", $this->txt("class_not_started_yet"));

            $bbbURL=$BBBHelper->joinURL($this->object);
        }

        $client_js->setVariable('isMaxNumberOfSessionsExceeded', 'false');
        if($this->object->isMaxConcurrentSessionEnabled()){
            if($available_sessions['max_sessions'] ||  (  key_exists($this->object->getBBBId(), $available_sessions['meetings']) && $available_sessions['meetings'][$this->object->getBBBId()]['userlimit'])){
                $client_js->setVariable('isMaxNumberOfSessionsExceeded', 'true');
                $my_tpl->setVariable('maxNumberofSessionsExceededText', $this->object->getMaxConcurrentSessionsMsg());
            }
        }

        $my_tpl->setVariable("clickToOpenClass", $this->txt("click_to_open_class"));
        $isMeetingRunning=$BBBHelper->isMeetingRunning($this->object);
        $client_js->setVariable("isMeetingRunning", $isMeetingRunning ? "true" : "false");
        $isMeetingRecorded = $BBBHelper->isMeetingRecorded($this->object);
        $client_js->setVariable("isMeetingRecorded", $isMeetingRecorded ? "true" : "false");
        $my_tpl->setVariable("bbbURL", $bbbURL);
        $tpl->addOnLoadCode($client_js->get());


        $tpl->setContent($my_tpl->get());
    }
    private function buildRecordingUI()
    {
        global $DIC;
        $BBBHelper=new ilBigBlueButtonProtocol($this->object);
        $table_template = new ilTemplate(
                "tpl.BigBlueButtonRecordTable.html",
                true,
                true,
                "Customizing/global/plugins/Services/Repository/RepositoryObject/BigBlueButton"
        );
        $table_content = [];
        $recordcount=0;
        $all_recordings=$BBBHelper->getRecordingsRaw()->recordings->recording;
        
        
        if ($all_recordings){
            foreach($all_recordings as $recording){
                $table_row_template = new ilTemplate("tpl.BigBlueButtonRecordTableRow.html",
                                true,
                                true,
                                "Customizing/global/plugins/Services/Repository/RepositoryObject/BigBlueButton");
                $table_row_template->setVariable("Date",date("d.m.Y H:i",  substr ($recording->startTime,0,10)));
                $seconds = round(($recording->endTime - $recording->startTime)/1000);
                $table_row_template->setVariable("Duration", $this->formatTimeDiff( $seconds ));

                $table_links = [];
                foreach($recording->playback->format as $format) {
                    $table_link_template = new ilTemplate("tpl.BigBlueButtonRecordTableLink.html",
                                    true,
                                    true,
                                    "Customizing/global/plugins/Services/Repository/RepositoryObject/BigBlueButton");
                    $table_link_template->setVariable("URL",$format->url);
                    if($format->type=="presentation" && $this->object->isDownloadAllowed() ){
                        $node = '<a href="'.$BBBHelper->getVideoDownloadStreamUrl($format->url).'" download>' .$this->txt("DownloadText") . '</a>';
                        // $table_row_template->setVariable("DownloadLink", $BBBHelper->getVideoDownloadStreamUrl($format->url));
                        // $table_row_template->setVariable("DownloadText", $this->txt("DownloadText"));
                        $table_row_template->setVariable("Download", $node);
                    }
                    $table_link_template->setVariable("Link_Title", $this->txt('Recording_type_' . $format->type));
                    $table_links[] = $table_link_template->get();
                }
                //Actions
                
                $actions = array(
                    $DIC->ui()->factory()->button()->shy($this->txt("deletelink_title"), $this->editLink($recording->recordID, true, true))
                );
                $isPublished = $recording->published->__toString() === 'true';
                
                if ($isPublished){
                    if ($this->object->isDownloadAllowed()){
                        $actions[] = $DIC->ui()->factory()->button()->shy($this->txt("DownloadText"), $BBBHelper->getVideoDownloadStreamUrl($format->url));
                    }
                    // $actions[] = $DIC->ui()->factory()->button()->shy($this->txt("unpublish_link"), $this->editLink($recording->recordID, 0));
                    // $actions[] = $DIC->ui()->factory()->button()->shy($this->txt("publish_link"), $this->editLink($recording->recordID, 1)); 
                }else{
                    // $actions[] = $DIC->ui()->factory()->button()->shy($this->txt("publish_link"), $this->editLink($recording->recordID, 1));
                                        
                }

                $actions_html = $DIC->ui()->renderer()->render($DIC->ui()->factory()->dropdown()->standard($actions)->withAriaLabel("Actions"));
                $table_row_template->setVariable("Links", implode(' · ', $table_links));
                $table_row_template->setVariable("Actions", $actions_html);
                /*$table_row_template->setVariable("DeleteLink", $recording->recordID);
                $table_row_template->setVariable("DeleteLink_Title", $this->txt("deletelink_title"));*/

                $table_content[] = $table_row_template->get();
                $recordcount++;
            }
        }
        $this->has_meeting_recordings = $recordcount > 0;
        $table_template->setVariable("BBB_RECORD_CONTENT", implode($table_content));
        $table_template->setVariable("Date_Title", $this->txt("Date_Title"));
        $table_template->setVariable("Duration_Title", $this->txt("Duration_Title"));
        $table_template->setVariable("Link_Title", $this->txt("Link_Title"));
        
        return $table_template->get();
        
    }

    public function endClass()
    {
        global $tpl, $ilTabs;

        //$ilTabs->clearTargets();
        $ilTabs->activateTab("content");

        $BBBHelper=new ilBigBlueButtonProtocol($this->object);
        $BBBHelper->endMeeting($this->object);

        //$this->object->incSequence();

        $my_tpl = new ilTemplate("./Customizing/global/plugins/Services/Repository/RepositoryObject/BigBlueButton/templates/tpl.BigBlueButtonModeratorMeetingEnded.html", true, true);
        $my_tpl->setVariable("classEnded", $this->txt("class_ended"));
        $tpl->setContent($my_tpl->get());
        $this->showContent();
    }

    public function startClass()
    {
        global $tpl, $ilTabs;

        //$ilTabs->clearTargets();
        $ilTabs->activateTab("content");


        $BBBHelper=new ilBigBlueButtonProtocol($this->object);

        $BBBHelper->createMeeting($this->object, isset($_POST["recordmeeting"]));

        $my_tpl = new ilTemplate("./Customizing/global/plugins/Services/Repository/RepositoryObject/BigBlueButton/templates/tpl.BigBlueButtonModeratorMeetingCreated.html", true, true);

        $bbbURL=$BBBHelper->joinURLModerator($this->object);

        $my_tpl->setVariable("newClassCreated", $this->txt("new_class_created"));
        $my_tpl->setVariable("newClassCreatedWarning", $this->txt("new_class_created_warning"));
        $my_tpl->setVariable("newClassCreatedJoinManual", $this->txt("new_class_created_join_manual"));
        $my_tpl->setVariable("bbbURL", $bbbURL);

        $tpl->setContent($my_tpl->get());
    }

    public function deleteRecording()
    {
        global $tpl, $ilTabs;

        //$ilTabs->clearTargets();
        $ilTabs->activateTab("content");

        $recordID = filter_input(INPUT_GET, "recordID");

        $BBBHelper=new ilBigBlueButtonProtocol($this->object);
        $BBBHelper->deleteRecording($this->object, $recordID);
        $this->showContent();
    }

    private function formatTimeDiff($seconds) {
		$dtF = new \DateTime('@0');
        $dtT = new \DateTime("@$seconds");
        return $dtF->diff($dtT)->format( $this->txt("Date_Format") );
	}
    
    public function publish()
    {
        global $ilCtrl;


        $BBBHelper= new ilBigBlueButtonProtocol($this->object);
         $recordID = filter_input(INPUT_GET, "recordID");
         $publish = boolval(filter_input(INPUT_GET, "publish"));

        $BBBHelper->publishRecordings($this->object,$recordID, $publish );
        $this->object=$bbb_obj;
        $ilCtrl->redirect($this, "showContent");

    }

    private function editLink($recordID, $publish, $delete_link = false){
        global $ilCtrl;
        $cmd = 'publish';
        if ($delete_link){
            $cmd = 'deleteRecording';
        }else{
            $ilCtrl->setParameterByClass(ilObjBigBlueButtonGUI::class, "publish", $publish);
        }
        $ilCtrl->setParameterByClass(ilObjBigBlueButtonGUI::class, "recordID", $recordID);
        $link = $ilCtrl->getLinkTargetByClass([
            ilObjPluginDispatchGUI::class,
            ilObjBigBlueButtonGUI::class
        ], $cmd);
        return $link;
    }
}
