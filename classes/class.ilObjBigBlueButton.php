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

include_once("./Services/Repository/classes/class.ilObjectPlugin.php");


/**
* Application class for BigBlueButton repository object.
*
*
* $Id$
*/
class ilObjBigBlueButton extends ilObjectPlugin
{
    private $objSession;
    private $duration;
    private $accessCode;
    private $dialNumber;
    private $guestChooseEnabled;
    private $guestGlobalEnabled;

    private $accessToken;
    private $refreshToken;
    /**
    * Constructor
    *
    * @access	public
    */
    public function __construct($a_ref_id = 0)
    {
        parent::__construct($a_ref_id);
    }


    /**
    * Get type.
    */
    final public function initType()
    {
        $this->setType("xbbb");
    }

    /**
    * Create object
    */
    public function doCreate()
    {
        global $ilDB;



        $this->setAttendeePwd(substr(md5(rand()), 0, 16));
        $this->setModeratorPwd(substr(md5(rand()), 0, 16));
        //$this->setMaxParticipants(1000);
        $this->setSequence(1);
        $this->generateCode();

        $ilDB->manipulate("INSERT INTO rep_robj_xbbb_data ".
            "(id, is_online, attendeepwd, moderatorpwd, welcometext, maxparticipants, sequence, dialnumber, accesscode, duration, presentationurl, guestchoose) VALUES (".
            $ilDB->quote($this->getId(), "integer").",".
            $ilDB->quote($this->getOnline(), "integer").",".
            $ilDB->quote($this->getAttendeePwd(), "text").",".
            $ilDB->quote($this->getModeratorPwd(), "text").",".
            $ilDB->quote($this->getWelcomeText(), "text").",".
            $ilDB->quote($this->getMaxParticipants(), "text").",".
            $ilDB->quote($this->getSequence(), "integer").",".
            $ilDB->quote($this->getDialNumber(), "text"). ",".
            $ilDB->quote($this->getAccessCode(), "text"). ",".
            $ilDB->quote($this->getMeetingDuration(), "integer"). ",".
            $ilDB->quote($this->getPresentationUrl(), "text"). ",".
            $ilDB->quote((int)$this->isGuestLinkAllowed(), "integer").
            ")");


        $result = $ilDB->query("SELECT * FROM rep_robj_xbbb_conf");

        while ($record = $ilDB->fetchAssoc($result)) {
            $this->setSvrPublicURL($record["svrpublicurl"]);
            //$this->setSvrPublicPort($record["svrpublicport"]);
            $this->setSvrPrivateURL($record["svrprivateurl"]);
            //$this->setSvrPrivatePort($record["svrprivateport"]);
            $this->setSvrSalt($record["svrsalt"]);
            $this->setGuestGlobalEnabled((bool)$record["guestglobalchoose"]);
        }
    }

    /**
    * Read data from db
    */
    public function doRead()
    {
        global $ilDB;
        global $DIC;
        $logger =$DIC->logger()->root();

        $set = $ilDB->query(
            "SELECT * FROM rep_robj_xbbb_data ".
            " WHERE id = ".$ilDB->quote($this->getId(), "integer")
        );
        while ($rec = $ilDB->fetchAssoc($set)) {
            $this->setOnline($rec["is_online"]);
            $this->setAttendeePwd($rec["attendeepwd"]);
            $this->setModeratorPwd($rec["moderatorpwd"]);
            $this->setWelcomeText($rec["welcometext"]);
            $this->setMaxParticipants($rec["maxparticipants"]);
            $this->setSequence($rec["sequence"]);
            $this->setDialNumber($rec["dialnumber"]);
            $this->setAccessCode($rec["accesscode"]);
            $this->setMeetingDuration($rec["duration"]);
            $this->setGuestLinkAllowed((bool)$rec["guestchoose"]);
            $this->setPresentationUrl($rec["presentationurl"]!==null? $rec["presentationurl"]: '');
        }

        $result = $ilDB->query("SELECT * FROM rep_robj_xbbb_conf");

        while ($record = $ilDB->fetchAssoc($result)) {
            $this->setSvrPublicURL($record["svrpublicurl"]);
            //$this->setSvrPublicPort($record["svrpublicport"]);
            $this->setSvrPrivateURL($record["svrprivateurl"]);
            //$this->setSvrPrivatePort($record["svrprivateport"]);
            $this->setSvrSalt($record["svrsalt"]);
            $this->setGuestGlobalEnabled((bool)$record["guestglobalchoose"]);
            
        }
    }

    /**
    * Update data
    */
    public function doUpdate()
    {
        global $ilDB;

        /*
        include_once("./Customizing/global/plugins/Services/Repository/RepositoryObject/Openmeetings/classes/class.ilOpenmeetingsSOAP.php");

        $svURL = $this->getsvrURL();
        $svPort = $this->getsvrPort();
        $svUsername = $this->getsvrUsername();
        $svPassword = $this->getsvrPassword();

        $this->WSDL = new ilOpenmeetingsSOAP();
        $this->WSDL->Openmeetings_loginuser($svURL,$svPort, $svUsername,$svPassword );
        $rmNum = $this->WSDL->Openmeetings_createroomwithmod($this->getsvrUsername(),$this->getsvrPassword(), $this->getrmComment() , 2 , 'ILIAS ROOM' , 20 , true , false , false , 0 , 1);
        */

        $ilDB->manipulate(
            $up = "UPDATE rep_robj_xbbb_data SET ".
            " is_online = ".$ilDB->quote($this->getOnline(), "integer").",".
            " attendeepwd = ".$ilDB->quote($this->getAttendeePwd(), "text").",".
            " moderatorpwd = ".$ilDB->quote($this->getModeratorPwd(), "text").",".
            " welcometext = ".$ilDB->quote($this->getWelcomeText(), "text").",".
            " maxparticipants = ".$ilDB->quote($this->getMaxParticipants(), "integer").",".
            " sequence = ".$ilDB->quote($this->getSequence(), "integer"). "," .
            "guestchoose = ".$ilDB->quote((int)$this->isGuestLinkAllowed()). ",".
            "presentationurl = ". $ilDB->quote($this->getPresentationUrl()) .
            " WHERE id = ".$ilDB->quote($this->getId(), "integer")
        );
    }

    /**
    * Delete data from db
    */
    public function doDelete()
    {
        global $ilDB;

        $ilDB->manipulate(
            "DELETE FROM rep_robj_xbbb_data WHERE ".
            " id = ".$ilDB->quote($this->getId(), "integer")
        );
    }

    /**
    * Do Cloning
    */
    public function doClone($a_target_id, $a_copy_id, $new_obj)
    {
        $new_obj->setOnline($this->getOnline());
        //$new_obj->setSvrPublicPort($this->getSvrPubicPort());
        $new_obj->setSvrPublicURL($this->getSvrPublicURL());
        //$new_obj->setSvrPrivatePort($this->getSvrPrivatePort());
        $new_obj->setSvrPrivateURL($this->getSvrPrivateURL());
        $new_obj->setSvrSalt($this->getSvrSalt());
        $new_obj->setAttendeePwd($this->getAttendeePwd());
        $new_obj->setModeratorPwd($this->getModeratorPwd());
        $new_obj->setWelcomeText($this->getWelcomeText());
        $new_obj->setMaxParticipants($this->getMaxParticipants());
        $new_obj->setSequence($this->getSequence());
        $new_obj->setGuestLinkAllowed($this->isGuestLinkAllowed());

        $new_obj->update();
    }




    /**
    * Set online
    *
    * @param	boolean		online
    */
    public function setOnline($a_val)
    {
        $this->online = $a_val;
    }

    /**
    * Get online
    *
    * @return	boolean		online
    */
    public function getOnline()
    {
        return $this->online;
    }

    public function setSvrPublicURL($a_val)
    {
        $this->svrPublicUrl = $a_val;
    }
    public function getSvrPublicURL()
    {
        return $this->svrPublicUrl;
    }

    /*
    function setSvrPublicPort($a_val){
        $this->svrPublicPort = $a_val;
    }
    function getSvrPublicPort(){
        return $this->svrPublicPort;
    }
    */

    public function setSvrPrivateURL($a_val)
    {
        $this->svrPrivateURL = $a_val;
    }
    public function getSvrPrivateURL()
    {
        return $this->svrPrivateURL;
    }

    /*
    function setSvrPrivatePort($a_val){
        $this->svrPrivatePort = $a_val;
    }
    function getSvrPrivatePort(){
        return $this->svrPrivatePort;
    }
    */

    public function setSvrSalt($a_val)
    {
        $this->svrSalt = $a_val;
    }
    public function getSvrSalt()
    {
        return $this->svrSalt;
    }

    public function setAttendeePwd($a_val)
    {
        $this->attendeePwd = $a_val;
    }
    public function getAttendeePwd()
    {
        return $this->attendeePwd;
    }

    public function setModeratorPwd($a_val)
    {
        $this->moderatorPwd = $a_val;
    }
    public function getModeratorPwd()
    {
        return $this->moderatorPwd;
    }

    public function setWelcomeText($a_val)
    {
        $this->welcomeText = $a_val;
    }
    public function getWelcomeText()
    {
        return $this->welcomeText;
    }

    public function setMaxParticipants($a_val)
    {
        $this->maxParticipants = $a_val;
    }
    public function getMaxParticipants()
    {
        return $this->maxParticipants;
    }

    public function setSequence($a_val)
    {
        $this->sequence = $a_val;
    }
    public function getSequence()
    {
        return $this->sequence;
    }

    public function incSequence()
    {
        //No synchronization... who cares at this stage of the development...
        $this->sequence=$this->sequence+1;
        $this->update();
        return $this->sequence;
    }

    public function getBBBId()
    {
        return "ilias-bbb_".CLIENT_ID."_".$this->getId()."_".$this->getSequence();
    }

    public function isWelcomeTextSet()
    {
        if ($this->welcomeText!=='') {
            return true;
        }
        return false;
    }
    public function setMeetingDuration(  $duration)
    {
        $this->duration = $duration;

    }
    public function getMeetingDuration()
    {
        return $this->duration;
    }

    public function getPresentationUrl()
    {
        return $this->presentationUrl;
    }
    public function setPresentationUrl(string $presentationUrl)
    {
        $this->presentationUrl = $presentationUrl;
    }
    public function getAccessCode()
    {
        return $this->accessCode;

    }
    public function setAccessCode( $code){
        $this->accessCode= $code;
    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }
    public function setAccessToken(string $accessToken)
    {
        $this->accessToken = $accessToken;
    }

    public function getRefreshToken(){
        return $this->refreshToken;
    }

    public function setRefreshToken(string $refreshToken)
    {
        $this->refreshToken = $refreshToken;
    }
    public function setDialNumber($dialNumber) {
        $this->dialNumber = $dialNumber;
    }
    public function getDialNumber()
    {
        $this->dialNumber;
    }

    public function generateCode()
    {
        $this->accessCode = mt_rand(10000, 99999);
    }
    public function isGuestLinkAllowed()
    {
        return $this->guestChooseEnabled;
    }
    public function setGuestLinkAllowed( bool $allowed)
    {
        $this->guestChooseEnabled = $allowed;
    }
    public function isGuestGlabalAllowed()
    {
        return $this->guestGlobalEnabled;
    }
    public function setGuestGlobalEnabled(bool $enabled)
    {
        $this->guestGlobalEnabled = $enabled;
    }
    public function isGuestLink()
    {
        return $this->is_guest_link;
    }
    public function getGuestLink()
    {

    }
    public function setGuestLink()
    {

    }

    

    
}
