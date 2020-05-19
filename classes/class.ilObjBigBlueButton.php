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
	/**
	* Constructor
	*
	* @access	public
	*/
	function __construct($a_ref_id = 0)
	{
		parent::__construct($a_ref_id);
	}
	

	/**
	* Get type.
	*/
	final function initType()
	{
		$this->setType("xbbb");
	}
	
	/**
	* Create object
	*/
	function doCreate()
	{
		global $ilDB;
		
		
		
		$this->setAttendeePwd(substr(md5(rand()), 0, 16)); 
		$this->setModeratorPwd(substr(md5(rand()), 0, 16));	
		$this->setMaxParticipants(1000);
		$this->setSequence(1);
		
		$ilDB->manipulate("INSERT INTO rep_robj_xbbb_data ".
        	"(id, is_online, attendeepwd, moderatorpwd, welcometext, maxparticipants, sequence) VALUES (".
            $ilDB->quote($this->getId(), "integer").",".
            $ilDB->quote($this->getOnline(),"integer").",".
            $ilDB->quote($this->getAttendeePwd(),"text").",".
            $ilDB->quote($this->getModeratorPwd(),"text").",".
            $ilDB->quote($this->getWelcomeText(),"text").",".
            $ilDB->quote($this->getMaxParticipants(),"text").",".
            $ilDB->quote($this->getSequence(),"integer").
            ")");
                    

		$result = $ilDB->query("SELECT * FROM rep_robj_xbbb_conf");
        
		while ($record = $ilDB->fetchAssoc($result))
        {
       		$this->setSvrPublicURL($record["svrpublicurl"]);
       		//$this->setSvrPublicPort($record["svrpublicport"]);
      		$this->setSvrPrivateURL($record["svrprivateurl"]);
       		//$this->setSvrPrivatePort($record["svrprivateport"]);
        	$this->setSvrSalt($record["svrsalt"]);
      
        }
    }
	
	/**
	* Read data from db
	*/
	function doRead()
	{
		global $ilDB;
		
		$set = $ilDB->query("SELECT * FROM rep_robj_xbbb_data ".
			" WHERE id = ".$ilDB->quote($this->getId(), "integer")
			);
		while ($rec = $ilDB->fetchAssoc($set))
		{
			$this->setOnline($rec["is_online"]);	
			$this->setAttendeePwd($rec["attendeepwd"]);
			$this->setModeratorPwd($rec["moderatorpwd"]); 	
			$this->setWelcomeText($rec["welcometext"]); 	
			$this->setMaxParticipants($rec["maxparticipants"]); 
			$this->setSequence($rec["sequence"]); 	
		}
		
		$result = $ilDB->query("SELECT * FROM rep_robj_xbbb_conf");
        
		while ($record = $ilDB->fetchAssoc($result))
        {
       		$this->setSvrPublicURL($record["svrpublicurl"]);
       		//$this->setSvrPublicPort($record["svrpublicport"]);
      		$this->setSvrPrivateURL($record["svrprivateurl"]);
       		//$this->setSvrPrivatePort($record["svrprivateport"]);
        	$this->setSvrSalt($record["svrsalt"]);
      
        }
		
	}
	
	/**
	* Update data
	*/
	function doUpdate()
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
		
		$ilDB->manipulate($up = "UPDATE rep_robj_xbbb_data SET ".
			" is_online = ".$ilDB->quote($this->getOnline(), "integer").",".
			" attendeepwd = ".$ilDB->quote($this->getAttendeePwd(), "text").",".
			" moderatorpwd = ".$ilDB->quote($this->getModeratorPwd(), "text").",".
  		    " welcometext = ".$ilDB->quote($this->getWelcomeText(), "text").",".
		    " maxparticipants = ".$ilDB->quote($this->getMaxParticipants(), "integer").",".
		    " sequence = ".$ilDB->quote($this->getSequence(), "integer").
			" WHERE id = ".$ilDB->quote($this->getId(), "integer")
			);
			
		
						
	}
	
	/**
	* Delete data from db
	*/
	function doDelete()
	{
		global $ilDB;
		
		$ilDB->manipulate("DELETE FROM rep_robj_xbbb_data WHERE ".
			" id = ".$ilDB->quote($this->getId(), "integer")
			);
		
	}
	
	/**
	* Do Cloning
	*/
	function doClone($a_target_id,$a_copy_id,$new_obj)
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
		
		$new_obj->update();
		

	}
	
	
	

	/**
	* Set online
	*
	* @param	boolean		online
	*/
	function setOnline($a_val)
	{
		$this->online = $a_val;
	}
	
	/**
	* Get online
	*
	* @return	boolean		online
	*/
	function getOnline()
	{
		return $this->online;
	}
			
	function setSvrPublicURL($a_val){
		$this->svrPublicUrl = $a_val;
	}
	function getSvrPublicURL(){
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

	function setSvrPrivateURL($a_val){
		$this->svrPrivateURL = $a_val;
	}
	function getSvrPrivateURL(){
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

	function setSvrSalt($a_val){
		$this->svrSalt = $a_val;
	}
	function getSvrSalt(){
		return $this->svrSalt;
	}
	
	function setAttendeePwd($a_val){
		$this->attendeePwd = $a_val;
	}
	function getAttendeePwd(){
		return $this->attendeePwd;
	}
	
	function setModeratorPwd($a_val){
		$this->moderatorPwd = $a_val;
	}
	function getModeratorPwd(){
		return $this->moderatorPwd;
	}
	
	function setWelcomeText($a_val){
		$this->welcomeText = $a_val;
	}
	function getWelcomeText(){
		return $this->welcomeText;
	}
	
	function setMaxParticipants($a_val){
		$this->maxParticipants = $a_val;
	}
	function getMaxParticipants(){
		return $this->maxParticipants;
	}
	
	function setSequence($a_val){
		$this->sequence = $a_val;
	}
	function getSequence(){
		return $this->sequence;
	}
	
	function incSequence(){
		//No synchronization... who cares at this stage of the development...
		$this->sequence=$this->sequence+1;
		$this->update();
		return $this->sequence;
	}	
	
	function getBBBId(){
		return "ilias-bbb_".CLIENT_ID."_".$this->getId()."_".$this->getSequence();
	}
	
	
}
?>
