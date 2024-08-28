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
* Application class for BigBlueButton repository object.
*
*
* $Id$
*/
class ilObjBigBlueButton extends ilObjectPlugin
{
    /**
     * @var
     */
    private $objSession;
    /**
     * @var
     */
    private int $duration = 0;
    /**
     * @var
     */
    private $accessCode;
    /**
     * @var
     */
    private string $dialNumber = '';
    /**
     * @var
     */
    private bool $guestChooseEnabled = false;
    /**
     * @var
     */
    private bool $guestGlobalEnabled = false;
    /**
     * @var int
     */
    private int $maxParticipants = 0;

    /**
     * @var string
     */
    private string $accessToken = '';
    /**
     * @var
     */
    private $refreshToken = '';
    /**
     * @var bool
     */
    private $publish = true;
    /**
     * @var bool
     */
    private $allow_download = false;
    /**
     * @var bool
     */
    private $enable_userlimit = false;
    /**
     * @var bool
     */
    private $enable_max_concurrent = false;
    /**
     * @var int
     */
    private $max_concurrent_sessions = 0;
    /**
     * @var
     */
    private $max_concurrent_sessions_msg;
    private bool $online = false;
    private string $svrPublicUrl = '';
    private string $svrSalt = '';
    private string $attendeePwd = '';
    private string $moderatorPwd = '';
    private string $welcomeText = '';
    private string $sequence = '';
    private string $presentationUrl = '';

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
    final protected function initType(): void
    {
        $this->setType("xbbb");
    }

    /**
    * Create object
    */
    protected function doCreate(bool $clone_mode = false): void
    {
        global $ilDB;



        $this->setAttendeePwd(substr(md5(rand()), 0, 16));
        $this->setModeratorPwd(substr(md5(rand()), 0, 16));
        //$this->setMaxParticipants(1000);
        $this->setSequence(1);
        $this->generateCode();

        $ilDB->manipulate("INSERT INTO rep_robj_xbbb_data ".
            "(id, is_online, attendeepwd, moderatorpwd, welcometext, maxparticipants, sequence, dialnumber, accesscode, duration, presentationurl, allow_download, guestchoose) VALUES (".
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
            $ilDB->quote((int)$this->isDownloadAllowed(), "integer"). ",".
            $ilDB->quote((int)$this->isGuestLinkAllowed(), "integer").
            ")");


        $result = $ilDB->query("SELECT * FROM rep_robj_xbbb_conf");

        while ($record = $ilDB->fetchAssoc($result)) {
            $this->setSvrPublicURL($record["svrpublicurl"]);
            $this->setSvrSalt($record["svrsalt"]);
            $this->setGuestGlobalEnabled((bool)$record["guestglobalchoose"]);
            $this->enableUserLimit((bool) $record['enable_userlimit']);
            $this->enableMaxConcurrentSession((bool) $record['sess_enable_max_concurrent']);
            $this->setMaxConcurrentSessions((int) $record['sess_max_concurrent']);
            $this->setMaxConcurrentSessionsMsg($record['sess_msg_concurrent'] ?? '');
        }
    }

    /**
    * Read data from db
    */
    protected function doRead(): void
    {
        global $ilDB;

        $set = $ilDB->query(
            "SELECT * FROM rep_robj_xbbb_data ".
            " WHERE id = ".$ilDB->quote($this->getId(), "integer")
        );
        while ($rec = $ilDB->fetchAssoc($set)) {
            $this->setOnline((bool) ($rec["is_online"] ?? false));
            $this->setAttendeePwd((string) ($rec["attendeepwd"] ?? ''));
            $this->setModeratorPwd((string) ($rec["moderatorpwd"] ?? ''));
            $this->setWelcomeText((string) $rec["welcometext"]);
            $this->setMaxParticipants((int) ($rec["maxparticipants"] ?? 0));
            $this->setSequence((string) ($rec["sequence"] ?? ''));
            $this->setDialNumber((string) ($rec["dialnumber"] ?? ''));
            $this->setAccessCode((string) ($rec["accesscode"] ?? ''));
            $this->setMeetingDuration((int) ($rec["duration"] ?? 0));
            $this->setGuestLinkAllowed((bool) $rec["guestchoose"]);
            $this->setDownloadAllowed((bool) $rec["allow_download"]);
            $this->setPresentationUrl((string) ($rec["presentationurl"] ?? ''));
            $this->setPublish((bool) ($rec["publish"] ?? false));
        }

        $result = $ilDB->query("SELECT * FROM rep_robj_xbbb_conf");

        while ($record = $ilDB->fetchAssoc($result)) {
            $this->setSvrPublicURL($record["svrpublicurl"]);
            $this->setSvrSalt($record["svrsalt"]);
            $this->setGuestGlobalEnabled((bool)$record["guestglobalchoose"]);
            $this->enableUserLimit((bool) $record['enable_userlimit']);
            $this->enableMaxConcurrentSession((bool) $record['sess_enable_max_concurrent']);
            $this->setMaxConcurrentSessions((int) $record['sess_max_concurrent']);
            $this->setMaxConcurrentSessionsMsg($record['sess_msg_concurrent'] ?? '');
        }
    }

    /**
    * Update data
    */
    protected function doUpdate(): void
    {
        global $ilDB;

        $ilDB->manipulate(
            $up = "UPDATE rep_robj_xbbb_data SET ".
            " is_online = ".$ilDB->quote($this->getOnline(), "integer").",".
            " attendeepwd = ".$ilDB->quote($this->getAttendeePwd(), "text").",".
            " moderatorpwd = ".$ilDB->quote($this->getModeratorPwd(), "text").",".
            " welcometext = ".$ilDB->quote($this->getWelcomeText(), "text").",".
            " maxparticipants = ".$ilDB->quote($this->getMaxParticipants(), "integer").",".
            " duration = ".$ilDB->quote($this->getMeetingDuration(), "integer").",".
            " dialnumber = ".$ilDB->quote($this->getDialNumber(), "text").",".
            " sequence = ".$ilDB->quote($this->getSequence(), "integer"). "," .
            " guestchoose = ".$ilDB->quote((int)$this->isGuestLinkAllowed()). ",".
            " allow_download = ".$ilDB->quote((int)$this->isDownloadAllowed()). ",".
            " publish = ".$ilDB->quote((int)$this->getPublish()). ",".
            " presentationurl = ". $ilDB->quote($this->getPresentationUrl()) .
            " WHERE id = ".$ilDB->quote($this->getId(), "integer")
        );
    }

    /**
    * Delete data from db
    */
    protected function doDelete(): void
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
    public function doClone($a_target_id, $a_copy_id, $new_obj): void
    {
        $new_obj->setOnline($this->getOnline());
        $new_obj->setSvrPublicURL($this->getSvrPublicURL());
        $new_obj->setSvrSalt($this->getSvrSalt());
        $new_obj->setAttendeePwd($this->getAttendeePwd());
        $new_obj->setModeratorPwd($this->getModeratorPwd());
        $new_obj->setWelcomeText($this->getWelcomeText());
        $new_obj->setMaxParticipants($this->getMaxParticipants());
        $new_obj->setSequence($this->getSequence());
        $new_obj->setGuestLinkAllowed($this->isGuestLinkAllowed());
        $new_obj->setPublish($this->getPublish());
        $new_obj->setDownloadAllowed($this->isDownloadAllowed());
        $new_obj->enableMaxConcurrentSession($this->isMaxConcurrentSessionEnabled());
        $new_obj->enableUserLimit($this->isUserLimitEnabled());
        $new_obj->setMaxConcurrentSession($this->getMaxConcurrentSessions());
        $new_obj->setMaxConcurrentSessionsMsg($this->getMaxConcurrentSessionsMsg());

        $new_obj->update();
    }




    /**
    * Set online
    *
    * @param	boolean $a_val online
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

    /**
     * @param $a_val
     * @return void
     */
    public function setSvrPublicURL($a_val)
    {
        $this->svrPublicUrl = $a_val;
    }

    /**
     * @return mixed
     */
    public function getSvrPublicURL()
    {
        return $this->svrPublicUrl;
    }

    /**
     * @param $a_val
     * @return void
     */
    public function setSvrSalt($a_val)
    {
        $this->svrSalt = $a_val;
    }

    /**
     * @return mixed
     */
    public function getSvrSalt()
    {
        return $this->svrSalt;
    }

    /**
     * @param $a_val
     * @return void
     */
    public function setAttendeePwd($a_val)
    {
        $this->attendeePwd = $a_val;
    }

    /**
     * @return mixed
     */
    public function getAttendeePwd()
    {
        return $this->attendeePwd;
    }

    /**
     * @param $a_val
     * @return void
     */
    public function setModeratorPwd($a_val)
    {
        $this->moderatorPwd = $a_val;
    }

    /**
     * @return mixed
     */
    public function getModeratorPwd()
    {
        return $this->moderatorPwd;
    }

    /**
     * @param $a_val
     * @return void
     */
    public function setWelcomeText($a_val)
    {
        $this->welcomeText = $a_val;
    }

    /**
     * @return mixed
     */
    public function getWelcomeText()
    {
        return $this->welcomeText;
    }

    /**
     * @param $a_val
     * @return void
     */
    public function setMaxParticipants($a_val)
    {
        $this->maxParticipants = $a_val;
    }

    /**
     * @return mixed
     */
    public function getMaxParticipants()
    {
        return $this->maxParticipants;
    }

    /**
     * @param $a_val
     * @return void
     */
    public function setSequence($a_val)
    {
        $this->sequence = $a_val;
    }

    /**
     * @return int
     */
    public function getSequence(): int
    {
        return $this->sequence;
    }

    /**
     * @return int
     */
    public function incSequence(): int
    {
        //No synchronization... who cares at this stage of the development...
        $this->sequence=$this->sequence+1;
        $this->update();
        return $this->sequence;
    }

    /**
     * @return string
     */
    public function getBBBId(): string
    {
        return "ilias-bbb_".CLIENT_ID."_".$this->getId()."_".$this->getSequence();
    }

    /**
     * @return bool
     */
    public function isWelcomeTextSet(): bool
    {
        if ($this->welcomeText!=='') {
            return true;
        }
        return false;
    }

    /**
     * @param $duration
     * @return void
     */
    public function setMeetingDuration($duration)
    {
        $this->duration = $duration;

    }

    /**
     * @return mixed
     */
    public function getMeetingDuration()
    {
        return $this->duration;
    }

    /**
     * @return string
     */
    public function getPresentationUrl(): string
    {
        return $this->presentationUrl;
    }

    /**
     * @param string $presentationUrl
     * @return void
     */
    public function setPresentationUrl(string $presentationUrl)
    {
        $this->presentationUrl = $presentationUrl;
    }

    /**
     * @return mixed
     */
    public function getAccessCode()
    {
        return $this->accessCode;

    }

    /**
     * @param $code
     * @return void
     */
    public function setAccessCode($code){
        $this->accessCode= $code;
    }

    /**
     * @return mixed
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @param string $accessToken
     * @return void
     */
    public function setAccessToken(string $accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @return mixed
     */
    public function getRefreshToken(){
        return $this->refreshToken;
    }

    /**
     * @param string $refreshToken
     * @return void
     */
    public function setRefreshToken(string $refreshToken)
    {
        $this->refreshToken = $refreshToken;
    }

    /**
     * @param string $dialNumber
     * @return void
     */
    public function setDialNumber(string $dialNumber)
    {
        $this->dialNumber = $dialNumber;
        
    }

    /**
     * @return mixed
     */
    public function getDialNumber()
    {
        return $this->dialNumber;
    }

    /**
     * @return void
     */
    public function generateCode()
    {
        $this->accessCode = mt_rand(10000, 99999);
    }

    /**
     * @return mixed
     */
    public function isGuestLinkAllowed()
    {
        return $this->guestChooseEnabled;
    }

    /**
     * @param bool $allowed
     * @return void
     */
    public function setGuestLinkAllowed(bool $allowed)
    {
        $this->guestChooseEnabled = $allowed;
    }

    /**
     * @return mixed
     */
    public function isGuestGlabalAllowed()
    {
        return $this->guestGlobalEnabled;
    }

    /**
     * @param bool $enabled
     * @return void
     */
    public function setGuestGlobalEnabled(bool $enabled)
    {
        $this->guestGlobalEnabled = $enabled;
    }


    /**
     * @return bool
     */
    public function getPublish(): bool
    {
        return $this->publish ? $this->publish : true;
    }

    /**
     * @param $publish
     * @return void
     */
    public function setPublish($publish)
    {
        $this->publish = $publish;
    }

    /**
     * @return bool
     */
    public function isDownloadAllowed(): bool
    {
        return $this->allow_download;
    }

    /**
     * @param $allow_download
     * @return void
     */
    public function setDownloadAllowed($allow_download)
    {
        $this->allow_download = $allow_download;
    }

    /**
     * @return bool
     */
    public function isMaxConcurrentSessionEnabled(): bool
    {
        return $this->enable_max_concurrent;

    }

    /**
     * @param bool $enabled
     * @return void
     */
    public function enableMaxConcurrentSession(bool $enabled = false)
    {
        $this->enable_max_concurrent = $enabled;

    }

    /**
     * @return int
     */
    public function getMaxConcurrentSessions(): int
    {
        return $this->max_concurrent_sessions;
    }

    /**
     * @param int $max_concurrent_sess
     * @return void
     */
    public function setMaxConcurrentSessions(int $max_concurrent_sess = 0)
    {
        $this->max_concurrent_sessions = $max_concurrent_sess;
    }

    /**
     * @return bool
     */
    public function isUserLimitEnabled(): bool
    {
        return $this->enable_userlimit;
    }

    /**
     * @param bool $enabled
     * @return void
     */
    public function enableUserLimit(bool $enabled = false)
    {
        $this->enable_userlimit = $enabled;
    }

    /**
     * @param string $message
     * @return void
     */
    public function setMaxConcurrentSessionsMsg(string $message = "")
    {
        $this->max_concurrent_sessions_msg = $message;
    }

    /**
     * @return mixed
     */
    public function getMaxConcurrentSessionsMsg()
    {
        return $this->max_concurrent_sessions_msg;
    }

}
