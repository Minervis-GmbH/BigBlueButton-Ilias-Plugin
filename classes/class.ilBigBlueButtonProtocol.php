<?php

include_once("./Customizing/global/plugins/Services/Repository/RepositoryObject/BigBlueButton/classes/bbb-api/bbb_api.php");
require_once './Customizing/global/plugins/Services/Repository/RepositoryObject/BigBlueButton/vendor/autoload.php';

use BigBlueButton\Core\Record;
use BigBlueButton\Parameters\CreateMeetingParameters;
use BigBlueButton\Parameters\JoinMeetingParameters;
use BigBlueButton\Parameters\GetRecordingsParameters;
use BigBlueButton\Parameters\DeleteRecordingsParameters;
use BigBlueButton\Parameters\EndMeetingParameters;
use BigBlueButton\Parameters\GetMeetingInfoParameters;
use BigBlueButton\Parameters\IsMeetingRunningParameters;
use BigBlueButton\Responses\GetRecordingsResponse;
use BigBlueButton\Responses\GetMeetingsResponse;
use BigBlueButton\Util\UrlBuilder;

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

    public function __construct($object)
    {
        $this->object = $object;
        $this->bbb = new BBB($this->object->getSvrSalt(), $this->object->getSvrPublicURL());
        $this->setCreateMeetingParams();
    }
    public function getMeetingID()
    {
    }
    public function setMeetingID()
    {
    }
    public function hasMeetingAModerator()
    {
    }
    public function isModerator()
    {
    }
    public function isAdmin()
    {
    }
    public function getRole()
    {
    }
    public function setRole()
    {
    }
    public function getRolePwd()
    {
    }
    public function setRolePwd()
    {
    }
    public function isMeetingStartable()
    {
    }
    public function getDisplayName()
    {
    }
    public function setDisplayName($displayName)
    {
    }
    public function getAvatar()
    {
        return $this->avatar;
    }
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
    }
    public function getParentObj()
    {
    }
    public function setParentOnj()
    {
    }
    public function hasModerator()
    {
    }
    public function getMeetings()
    {
    }
    public function getJoinURL()
    {
    }
    public function hasSession()
    {
    }
    public function getMeetingInfo()
    {
    }
    public function setCreateMeetingParams()
    {
    }
    

    public function getVideoDownloadStreamUrl(string $url)
    {
        $record_part = explode('2.0/playback.html?meetingId=', $url);
        $video_path = $record_part[0] . $record_part[1] . "/" . $record_part[1] . ".mp4";
        $video_url = str_replace("playback", "download", $video_path);
        //Check if the MP4 exists
        $ch = curl_init($video_url);
        curl_exec($ch);
        $http_status = 400;
        if (!curl_errno($ch)) {
            $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        }
        curl_close($ch);
        return  (int)$http_status ===200 ? $video_url : '';
    }
    public function isRecordable()
    {
    }
    public function getInviteUrl($title = "Guest")
    {
        $link = ILIAS_HTTP_PATH . "/" . substr(dirname(__FILE__), strpos(dirname(__FILE__), 'Customizing'), -8) . '/guest.php?';
        $query = "ref_id=" . $this->object->getRefId() . "&client=" . CLIENT_ID;
        return $link . $query;
    }



    public function createMeeting($object, $record = false)
    {
        global $DIC; /** @var Container $DIC */


        $logger =  $DIC->logger()->root();
        $logger->dump($this->bbb->getDefaultConfigXML());
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


        include_once('./Services/Link/classes/class.ilLink.php');
        $logoutURL = ilLink::_getLink($object->getRefId());

        $mPW=$object->getModeratorPwd();

        $aPW=$object->getAttendeePwd();

        $SALT=trim($object->getSvrSalt());

        $srvURL=$object->getSvrPublicURL()/*.":".$object->getSvrPublicPort()*/ ;
        //$srvURL=$object->getSvrPrivateURL()/*.":".$object->getSvrPrivatePort()*/ ;


        $this->createMeetingParam = new CreateMeetingParameters($meetingID, $meetingTitle);
        $this->createMeetingParam->setAttendeePassword($object->getAttendeePwd())
            ->setModeratorPassword($object->getModeratorPwd())
            ->setLogoutUrl($logoutURL)
            ->setAutoStartRecording(false)
            ->setAllowStartStopRecording($record)
            ->setRecord($record)
            ->setDuration($this->object->getMeetingDuration())
            ;

        if (trim($welcomeString)) {
            $this->createMeetingParam->setWelcomeMessage($welcomeString);
        }
        if ($object->getMaxParticipants()>0){
            $this->createMeetingParam->setMaxParticipants($object->getMaxParticipants());
        }
        if( (bool)(strlen($pdf = $this->object->getPresentationUrl())) ) {
            $this->createMeetingParam->addPresentation($pdf);
        }

        //$response=BigBlueButton::createMeetingArray($meetingTitle, $meetingID, $welcomeString, $mPW, $aPW, $SALT, $srvURL, $logoutURL, $record );

        //return $response;
        $this->bbb->createMeeting($this->createMeetingParam);
    }



    public function joinURL($object)
    {
        global $ilUser, $DIC;
        $logger =  $DIC->logger()->root();
        $logger->dump($this->bbb->getDefaultConfigXML()->getRawXml());
        $userName=$ilUser->getFullname();
        $meetingID=$object->getBBBId();
        $aPW=$object->getAttendeePwd();


        $joinParameters = new JoinMeetingParameters($meetingID, $userName, $aPW);
        $joinParameters->setJoinViaHtml5(true)
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
        $joinParameters->setJoinViaHtml5(true)
            ->setRedirect(true)
            ->setClientURL($DIC->http()->request()->getUri());
        return $this->bbb->getJoinMeetingURL($joinParameters);
    }

    public function isMeetingRecorded($object)
    {
        $meetingID=$object->getBBBId();
        $mPW=$object->getModeratorPwd();
        ;
        $meetingInfo = $this->bbb->getMeetingInfo(new GetMeetingInfoParameters($meetingID, $mPW));
        return $meetingInfo->success();
    }

    /*
    function getCloseURL($object){


        $meetingID=$object->getBBBId();

        $mPW=$object->getModeratorPwd();

        $SALT=trim($object->getSvrSalt());

        $srvURL=$object->getSvrPublicURL().":".$object->getSvrPublicPort() ;

        $closeUrl=BigBlueButton::endMeetingURL($meetingID, $mPW, $srvURL, $SALT);

        return $closeUrl;

    }*/

    public function endMeeting($object)
    {
        $meetingID=$object->getBBBId();
        $mPW=$object->getModeratorPwd();
        $endParams = new EndMeetingParameters($meetingID, $mPW);
        $endMeetingResponse = $this->bbb->endMeeting($endParams);
    }

    public function getRecordings($object)
    {
		global $DIC;
		$logger =$DIC->logger()->root();
        require_once "./Services/Calendar/classes/class.ilDateTime.php";


        $meetingID=$object->getBBBId();

        $meetingParams = new GetMeetingInfoParameters($meetingID, $object->getModeratorPwd());
        $info = $this->bbb->getMeetingInfo($meetingParams);
        //$logger->dump($info);
        $recordParameters = new GetRecordingsParameters();
        $recordParameters->setMeetingID($meetingID);
        
        $records =[];
		$all_records = $this->bbb->getRecordings($recordParameters);
		//$logger->dump($all_records->getRawXml());
        
        foreach ($all_records->getRecords() as $key =>$record) {
			
            $recordID = $record->getRecordId();
            $records[$recordID]['startTime'] =  date("d.m.Y H:i", substr($record->getStartTime(), 0, 10));
            $records[$recordID]['endTime'] = date("d.m.Y H:i", substr($record->getEndTime(), 0, 10));
            $records[$recordID]['playback'] = $record->getPlaybackUrl();
            $records[$recordID]['playback_format'] = $record->getPlaybackType();
            $records[$recordID]['playback_length'] = $this->processPlaybackLength($record->getPlaybackLength());
            $records[$recordID]['download'] = $this->getVideoDownloadStreamUrl($records[$recordID]['playback']);
            $records[$recordID]['meetingID'] = $record->getMeetingID();
        }
        return $records;
    }
public function processPlaybackLength($playbackLength){
	if ($playbackLength=== 0) return '<1 min';
	if ($playbackLength >= 60) return (int)$playbackLength/60 ."h" . $playbackLength%60;
    return $playbackLength;
}
    public function getDeleteRecordingUrl($object, $recordID)
    {
        $meetingID=$object->getBBBId();
        $SALT=trim($object->getSvrSalt());

        //$srvURL=$object->getSvrPublicURL().":".$object->getSvrPublicPort() ;
        $srvURL=$object->getSvrPrivateURL()/*.":".$object->getSvrPrivatePort()*/ ;
        $recordParameters = new GetRecordingsParameters();
        $recordParameters->setMeetingID($meetingID);
        return $this->bbb->getDeleteRecordingsUrl($recordParameters);

        //return BigBlueButton::deleteRecordingURL( $recordID, $srvURL, $SALT );
    }

    public function deleteRecording($object, $recordID)
    {
        $meetingID=$object->getBBBId();
        $SALT=trim($object->getSvrSalt());
        //$srvURL=$object->getSvrPublicURL().":".$object->getSvrPublicPort() ;
        $srvURL=$object->getSvrPrivateURL()/*.":".$object->getSvrPrivatePort() */;
        $deletRecordParameters = new DeleteRecordingsParameters($recordID);
        return $this->bbb->deleteRecordings($deletRecordParameters);
        //return BigBlueButton::deleteRecording( $recordID, $srvURL, $SALT );
    }

    public function isMeetingRunning($object)
    {
        $meetingID=$object->getBBBId();

        $mPW=$object->getModeratorPwd();

        $SALT=trim($object->getSvrSalt());

        $srvURL=$object->getSvrPrivateURL()/*.":".$object->getSvrPrivatePort() */;
        //This version checks if the meeting is created, not if it has any attendee
        //$response=BigBlueButton::getMeetingInfoArray( $meetingID, $mPW, $srvURL, $SALT );
        $meetingRunning = false;
        try {
            $meetingParameters = new IsMeetingRunningParameters($meetingID);
            $response = $this->bbb->isMeetingRunning($meetingParameters);
            $meetingRunning = $response->isRunning();
        } catch (Exception $e) {
        }
        return $meetingRunning;


        // if($response && !array_key_exists('returncode',$response) && $response['hasBeenForciblyEnded']=='false'){
        // 	return true;
        // }else{
        // 	return false;
        // }

        /* It checks if there is anyone inside the meeting
    	return BigBlueButton::isMeetingRunning( $meetingID, $srvURL, $SALT );
    	*/
    }
}

class BBB extends \BigBlueButton\BigBlueButton
{
    public function __construct($securitySecret, $baseUrl)
    {
        parent::__construct();
        $this->securitySecret = $securitySecret;
        $this->bbbServerBaseUrl = $baseUrl;
        $this->urlBuilder       = new UrlBuilder($this->securitySecret, $this->bbbServerBaseUrl);
    }
}
