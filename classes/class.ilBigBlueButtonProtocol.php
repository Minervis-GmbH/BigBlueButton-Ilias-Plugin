<?php

include_once("./Customizing/global/plugins/Services/Repository/RepositoryObject/BigBlueButton/classes/bbb-api/bbb_api.php");

/**
* BigBlueButton comunication helper class
*
*  @author Paul <ilias@gdconsulting.it>
* @version $Id$
*
*/

class ilBigBlueButtonProtocol
{
	
	/*
	function createAndGetURL($object,$isModerator){
		
		global $ilUser;
		
		$userName=$ilUser->getFullname();
		
		$meetingID=$object->getBBBId();
		
		$welcomeString=$object->getWelcomeText();
		
		$mPW=$object->getModeratorPwd();
		
		$aPW=$object->getAttendeePwd();

		$SALT=trim($object->getSvrSalt());
		
		$srvURL=$object->getSvrPublicURL().":".$object->getSvrPublicPort() ;
		
		include_once('classes/class.ilLink.php');
		$logoutURL = ilLink::_getLink($object->getRefId());
	
			
		if($isModerator){
			$url=BigBlueButton::createMeetingAndGetJoinURL( $userName, $meetingID, $welcomeString, $mPW, $aPW, $SALT, $srvURL, $logoutURL );			
			
		}else{
			$url=BigBlueButton::joinURL( $meetingID, $userName, $aPW, $SALT, $srvURL );	
		}
		
		return $url;
	
	}
	*/
	
	
	function createMeeting($object, $record = false){
		
		
		$meetingID=$object->getBBBId();
		
		$welcomeString=$object->getWelcomeText();
		
		$mPW=$object->getModeratorPwd();
		
		$aPW=$object->getAttendeePwd();

		$SALT=trim($object->getSvrSalt());
		
		$srvURL=$object->getSvrPublicURL()/*.":".$object->getSvrPublicPort()*/ ;
		//$srvURL=$object->getSvrPrivateURL()/*.":".$object->getSvrPrivatePort()*/ ;
		
		
		include_once('./Services/Link/classes/class.ilLink.php');
		$logoutURL = ilLink::_getLink($object->getRefId());
		
		
		$response=BigBlueButton::createMeetingArray($meetingID, $meetingID, $welcomeString, $mPW, $aPW, $SALT, $srvURL, $logoutURL, $record );
		
		return $response;
		
	}
	
	
	
	function joinURL($object){

		global $ilUser;
		
		$userName=$ilUser->getFullname();
		
		$meetingID=$object->getBBBId();
		
		$aPW=$object->getAttendeePwd();

		$SALT=trim($object->getSvrSalt());
		
		$srvURL=$object->getSvrPublicURL()/*.":".$object->getSvrPublicPort()*/ ;

		$url=BigBlueButton::joinURL($meetingID, $userName, $aPW, $SALT, $srvURL);
		
		return $url;
	}
	
	function joinURLModerator($object){

		global $ilUser;
		
		$userName=$ilUser->getFullname();
		
		$meetingID=$object->getBBBId();
		
		$mPW=$object->getModeratorPwd();

		$SALT=trim($object->getSvrSalt());
		
		$srvURL=$object->getSvrPublicURL()/*.":".$object->getSvrPublicPort()*/ ;

		$url=BigBlueButton::joinURL($meetingID, $userName, $mPW, $SALT, $srvURL);
		
		return $url;
	}
        
        function isMeetingRecorded($object){
         
		$meetingID=$object->getBBBId();
		
		$mPW=$object->getModeratorPwd();

		$SALT=trim($object->getSvrSalt());
		
		$srvURL=$object->getSvrPublicURL()/*.":".$object->getSvrPublicPort()*/ ;
                
                return BigBlueButton::isMeetingRecorded($meetingID, $mPW, $srvURL, $SALT);
                
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
	
	function endMeeting($object){
		
		$meetingID=$object->getBBBId();
		
		$mPW=$object->getModeratorPwd();
		
		$SALT=trim($object->getSvrSalt());
		
		//$srvURL=$object->getSvrPublicURL().":".$object->getSvrPublicPort() ;
		$srvURL=$object->getSvrPrivateURL()/*.":".$object->getSvrPrivatePort()*/ ;
	
		BigBlueButton::endMeeting($meetingID, $mPW, $srvURL, $SALT); 
	}
        
        function getRecordings($object){
		
		$meetingID=$object->getBBBId();
		
		//$mPW=$object->getModeratorPwd();
		
		$SALT=trim($object->getSvrSalt());
		
		//$srvURL=$object->getSvrPublicURL().":".$object->getSvrPublicPort() ;
		$srvURL=$object->getSvrPrivateURL()/*.":".$object->getSvrPrivatePort()*/ ;
	
		return BigBlueButton::getRecordings($srvURL, $SALT, $meetingID); 
	}
        
        function getDeleteRecordingUrl($object, $recordID){
		
		$meetingID=$object->getBBBId();

		$SALT=trim($object->getSvrSalt());
		
		//$srvURL=$object->getSvrPublicURL().":".$object->getSvrPublicPort() ;
		$srvURL=$object->getSvrPrivateURL()/*.":".$object->getSvrPrivatePort()*/ ;
	
		return BigBlueButton::deleteRecordingURL( $recordID, $srvURL, $SALT ); 
	}
        
        function deleteRecording($object, $recordID){
            	$meetingID=$object->getBBBId();

		$SALT=trim($object->getSvrSalt());
		
		//$srvURL=$object->getSvrPublicURL().":".$object->getSvrPublicPort() ;
        $srvURL=$object->getSvrPrivateURL()/*.":".$object->getSvrPrivatePort() */;        
                return BigBlueButton::deleteRecording( $recordID, $srvURL, $SALT );
        
        }
       
    function isMeetingRunning($object){

    	$meetingID=$object->getBBBId();
    	
    	$mPW=$object->getModeratorPwd();
		
		$SALT=trim($object->getSvrSalt());
		
		$srvURL=$object->getSvrPrivateURL()/*.":".$object->getSvrPrivatePort() */;
	
		//This version checks if the meeting is created, not if it has any attendee
    	$response=BigBlueButton::getMeetingInfoArray( $meetingID, $mPW, $srvURL, $SALT );
		
		if($response && !array_key_exists('returncode',$response) && $response['hasBeenForciblyEnded']=='false'){
			return true;
		}else{
			return false;
		}
		
		/* It checks if there is anyone inside the meeting
    	return BigBlueButton::isMeetingRunning( $meetingID, $srvURL, $SALT );
    	*/
    }  
}
?>
