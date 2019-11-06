<?php

//include_once("./Services/Component/classes/class.ilPluginConfigGUI.php");

/**
 * BigBlueButton exporter class
 *
 * @version $Id$
 *
 */
class ilBigBlueButtonExporter extends ilXmlExporter{


    public function getXmlRepresentation($a_entity, $a_schema_version, $a_id)
    {
        //include_once './Modules/TestQuestionPool/classes/class.ilObjQuestionPool.php';
        $bbb = new ilObjBigBlueButton($a_id);
    }

    public function init()
    {
        // TODO: Implement init() method.
    }

    public function getValidSchemaVersions($a_entity)
    {
        return array (
            "5.2.0" => array(
                "namespace" => "http://www.ilias.de/Services/Repository/RepositoryObject/BigBlueButton/html/5_2",
                "xsd_file" => "ilias_xbbb_5_2.xsd",
                "uses_dataset" => false,
                "min" => "5.2.0",
                "max" => "")
        );
    }
}