<?php

use BigBlueButton\Parameters\CreateMeetingParameters;
use BigBlueButton\Parameters\JoinMeetingParameters;
use BigBlueButton\Parameters\GetRecordingsParameters;
use BigBlueButton\Parameters\DeleteRecordingsParameters;
use BigBlueButton\Parameters\EndMeetingParameters;
use BigBlueButton\Parameters\GetMeetingInfoParameters;
use BigBlueButton\Parameters\IsMeetingRunningParameters;

/**
* BigBlueButton comunication helper class
*
*  @author Paul <ilias@gdconsulting.it>
* @version $Id$
*
*/

class ilBigBlueButtonProtocol
{
    private $object;
    private $bbb;
    private $meetingParams;
    private $createMeetingParam;
    private $avatar;
    private $user;
    private $meetings;

    public function __construct($object)
    {
        $this->object = $object;
        $this->bbb = new ilBBB($this->object->getSvrSalt(), $this->object->getSvrPublicURL());
        $this->meetings = $this->bbb->getMeetings();
    }
    public function getAvatar()
    {
        return $this->avatar;
    }
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
    }

    public function getVideoDownloadStreamUrl(string $url)
    {
        $record_part = explode('playback/presentation/2.3/', $url);
        $recordID = trim($record_part[1]);
        $video_path = trim($record_part[0]) . "presentation/" . $recordID . "/" . $recordID . "_full.webm";
        return $video_path ;
    }

    public function getInviteUrl($title = "Guest")
    {
        $link = ILIAS_HTTP_PATH . "/" . substr(dirname(__FILE__), strpos(dirname(__FILE__), 'Customizing'), -8) . '/guest.php?';
        $query = "ref_id=" . $this->object->getRefId() . "&client_id=" . CLIENT_ID;
        return $link . $query;
    }



    public function createMeeting($object, $record = false)
    {

        $meetingID=$object->getBBBId();
        $meetingTitle=$object->getTitle();

        $welcomeString=$object->getWelcomeText();
        /*if(!$object->isWelcomeTextSet()){
            $welcomeString=str_replace(
                [
                    '{MEETING_TITLE}'
                ],
                [
                    $meetingTitle
                ],
                $dic->language()->txt('rep_robj_xbbb_welcome_text_content')
            );
        }*/


        $logoutURL = ilLink::_getLink($object->getRefId());

        $this->createMeetingParam = new CreateMeetingParameters($meetingID, $meetingTitle);
        $this->createMeetingParam->setAttendeePassword($object->getAttendeePwd())
            ->setModeratorPassword($object->getModeratorPwd())
            ->setLogoutUrl($logoutURL)
            ->setAutoStartRecording(false)
            ->setAllowStartStopRecording($record)
            ->setRecord($record)
	    ->setGuestPolicy($this->object->getGuestPolicy())
            ->setDuration($this->object->getMeetingDuration())
            ;

        if (trim($welcomeString)) {
            $this->createMeetingParam->setWelcomeMessage($welcomeString);
        }
        if ($object->getMaxParticipants()>0){
            $this->createMeetingParam->setMaxParticipants($object->getMaxParticipants());
        }
        if( (bool)(strlen($pdf = $this->object->getPresentationUrl())) && $this->isPDFValid($pdf)) {
            $this->createMeetingParam->addPresentation($pdf);
        }
        if(trim($object->getDialNumber())){
            $this->createMeetingParam->setDialNumber($object->getDialNumber());
            $this->createMeetingParam->setVoiceBridge($object->getAccessCode());//voicebridge
        }
        $this->bbb->createMeeting($this->createMeetingParam);
    }



    public function joinURL($object)
    {
        global $ilUser, $DIC;
        $userName=$ilUser->getFullname();
        $meetingID=$object->getBBBId();
        $aPW=$object->getAttendeePwd();


        $joinParameters = new JoinMeetingParameters($meetingID, $userName, $aPW);
        $joinParameters->setRole('VIEWER')
            ->setRedirect(true)
            ->setClientURL($DIC->http()->request()->getUri());
        return $this->bbb->getJoinMeetingURL($joinParameters);
    }

    public function joinURLModerator($object)
    {
        global $ilUser, $DIC;

        $userName=$ilUser->getFullname();
        $meetingID=$object->getBBBId();
        $mPW=$object->getModeratorPwd();
        $joinParameters = new JoinMeetingParameters($meetingID, $userName, $mPW);
        $joinParameters->setRole('MODERATOR')
            ->setRedirect(true)
            ->setClientURL($DIC->http()->request()->getUri());
        return $this->bbb->getJoinMeetingURL($joinParameters);
    }

    public function isMeetingRecorded($object)
    {
        $meetingID=$object->getBBBId();
        $mPW=$object->getModeratorPwd();
        ;
        $meetingInfo= null;
        try{
            $meetingInfo = $this->bbb->getMeetingInfo(new GetMeetingInfoParameters($meetingID, $mPW));
            return $meetingInfo->success();
        }catch(Exception $e){
            return $meetingInfo;
        }


    }


    public function endMeeting($object)
    {
        $meetingID=$object->getBBBId();
        $mPW=$object->getModeratorPwd();
        $endParams = new EndMeetingParameters($meetingID, $mPW);
        $endMeetingResponse = $this->bbb->endMeeting($endParams);
    }

    public function getRecordingsRaw()
    {
        return $this->getRecordings()->getRawXml();
    }
    public function getRecordings()
    {
        $meetingID=$this->object->getBBBId();
        $recordParameters = new GetRecordingsParameters();
        $recordParameters->setMeetingID($meetingID);
		return $this->bbb->getRecordings($recordParameters);
    }


    public function processPlaybackLength($playbackLength){
        if ($playbackLength=== 0) return '<1 min';
        if ($playbackLength >= 60) return (int)$playbackLength/60 ."h" . $playbackLength%60;
        return $playbackLength;
    }
    public function getDeleteRecordingUrl($object, $recordID)
    {
        $meetingID=$object->getBBBId();
        $recordParameters = new GetRecordingsParameters();
        $recordParameters->setMeetingID($meetingID);
        return $this->bbb->getDeleteRecordingsUrl($recordParameters);
    }

    public function deleteRecording($object, $recordID)
    {
        $deletRecordParameters = new DeleteRecordingsParameters($recordID);
        return $this->bbb->deleteRecordings($deletRecordParameters);
    }

    public function getPublishRecordingsUrl($object, $recordID, $publish = true)
    {
        $parameters = new BigBlueButton\Parameters\PublishRecordingsParameters($recordID, $publish);
        return $this->bbb->getPublishRecordingsUrl($parameters);
    }

    public function publishRecordings($object, $recordID, $publish = true){
        $parameters = new BigBlueButton\Parameters\PublishRecordingsParameters($recordID, $publish);
        return $this->bbb->publishRecordings($parameters);
    }

    public function isMeetingRunning($object)
    {
        $meetingID=$object->getBBBId();
        $meetingRunning = false;
        try {
            $meetingParameters = new IsMeetingRunningParameters($meetingID);
            $response = $this->bbb->isMeetingRunning($meetingParameters);
            $meetingRunning = $response->isRunning();
        } catch (Exception $e) {
        }
        return $meetingRunning;
    }
    public function getAPI()
    {
        $apiVersion = $this->bbb->getApiVersion();
        return $apiVersion->success();
    }
    private function isPDFValid(string $pdf){

        return filter_var($pdf, FILTER_VALIDATE_URL) ? true : false;
    }


    public function getMaximumSessionsAvailable($meeting_id = null)
    {
        $participants_count = 0;
        $sessions_available = array(
            "current_meeting_userlimit" => false,
            "max_sessions" => false,
        );
        $available = array();
        foreach($this->meetings->getMeetings() as $meeting){
            $participants_count = $participants_count + $meeting->getParticipantCount();
            $userlimit_exceeded =( $this->object->getMaxParticipants() > 0 && ($meeting->getMaxUsers() - $meeting->getParticipantCount() -1 <= 0));
               $available[$meeting->getMeetingId()] = [

                'participants' => $meeting->getParticipantCount(),
                'max_users' => $meeting->getMaxUsers(),
                'userlimit' => $userlimit_exceeded

            ];
            if($meeting_id && $meeting->getMeetingId() == $meeting_id && $userlimit_exceeded){
                $sessions_available['current_meeting_userlimit'] = true;
            }
        }
        $sessions_available['meetings'] = $available;
        if ($this->object->getMaxConcurrentSessions() > 0 && $participants_count >= $this->object->getMaxConcurrentSessions() - 1){
            $sessions_available["max_sessions"] = true;
        }
        return $sessions_available;

    }
}

