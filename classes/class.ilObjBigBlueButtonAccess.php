<?php
/*
    +-----------------------------------------------------------------------------+
    | ILIAS open source                                                           |
    +-----------------------------------------------------------------------------+
    | Copyright (c) 1998-2001 ILIAS open source, University of Cologne            |
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
* Access/Condition checking for BigBlueButton object
*
* @version $Id$
*/
class ilObjBigBlueButtonAccess extends ilObjectPluginAccess
{
    /**
    * Checks wether a user may invoke a command or not
    * (this method is called by ilAccessHandler::checkAccess)
    *
    * Please do not check any preconditions handled by
    * ilConditionHandler here. Also don't do usual RBAC checks.
    *
    * @param	string		$a_cmd			command (not permission!)
    * @param	string		$a_permission	permission
    * @param	int			$a_ref_id		reference id
    * @param	int			$a_obj_id		object id
    * @param	int			$a_user_id		user id (if not provided, current user is taken)
    *
    * @return	boolean		true, if everything is ok
    */
    public function _checkAccess($a_cmd, $a_permission, $a_ref_id, $a_obj_id, $a_user_id = ""): bool
    {
        global $ilUser, $ilAccess;

        if ($a_user_id == "") {
            $a_user_id = $ilUser->getId();
        }

        switch ($a_permission) {
            case "read":
                if (!ilObjBigBlueButtonAccess::checkOnline($a_obj_id) &&
                    !$ilAccess->checkAccessOfUser($a_user_id, "write", "", $a_ref_id)) {
                    return false;
                }
                break;
            case "write":
                return $ilAccess->checkAccessOfUser($a_user_id, "write", "", $a_ref_id);
                break;
        }

        return true;
    }

    /**
    * Check online status of BigBlueButton object
    */
    public static function checkOnline($a_id)
    {
        global $ilDB;

        $set = $ilDB->query(
            "SELECT is_online FROM rep_robj_xbbb_data ".
            " WHERE id = ".$ilDB->quote($a_id, "integer")
        );
        $rec  = $ilDB->fetchAssoc($set);
        return (bool) $rec["is_online"];
    }
}
