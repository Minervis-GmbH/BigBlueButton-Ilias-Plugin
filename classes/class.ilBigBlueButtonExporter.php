<?php

/**
 * BigBlueButton exporter class
 *
 * @version $Id$
 *
 */
class ilBigBlueButtonExporter extends ilXmlExporter
{
    public function getXmlRepresentation($a_entity, $a_schema_version, $a_id): string
    {
        return ''; // Currently no export possible
    }

    public function init(): void
    {
        // TODO: Implement init() method.
    }

    public function getValidSchemaVersions($a_entity): array
    {
        return array(
            "5.2.0" => array(
                "namespace" => "http://www.ilias.de/Services/Repository/RepositoryObject/BigBlueButton/html/5_2",
                "xsd_file" => "ilias_xbbb_5_2.xsd",
                "uses_dataset" => false,
                "min" => "5.2.0",
                "max" => "")
        );
    }
}
