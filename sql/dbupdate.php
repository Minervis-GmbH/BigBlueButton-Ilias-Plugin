<#1>
<?php
if(!$ilDB->tableExists('rep_robj_xbbb_data')){
	$fields_data = array(
		'id' => array(
			'type' => 'integer',
			'length' => 8,
			'notnull' => true
		),
		'is_online' => array(
			'type' => 'integer',
			'length' => 1,
			'notnull' => false
		),
		'attendeepwd' => array(
			'type' => 'text',
			'length' => 256,
			'notnull' => false
		),
		'moderatorpwd' => array(
			'type' => 'text',
			'length' => 256,
			'notnull' => false
		),
		'welcometext' => array(
			'type' => 'text',
			'length' => 1000,
			'notnull' => false
		),
		'maxparticipants' => array(
			'type' => 'integer',
			'length' => 2,
			'notnull' => false
		),
		'sequence' => array(
			'type' => 'integer',
			'length' => 8,
			'notnull' => false
		)
		
	);

	$ilDB->createTable("rep_robj_xbbb_data", $fields_data);
	$ilDB->addPrimaryKey("rep_robj_xbbb_data", array("id"));
}


if(!$ilDB->tableExists('rep_robj_xbbb_conf')){
	$fields_conf = array(
			'id' => array(
					'type' => 'integer',
					'length' => 4,
					'notnull' => true
			),
			'svrpublicurl' => array(
					'type' => 'text',
					'length' => 256,
					'notnull' => true
			),
			/*'svrpublicport' => array(
					'type' => 'integer',
					'length' => 8,
					'notnull' => true
			),*/
			'svrprivateurl' => array(
					'type' => 'text',
					'length' => 256,
					'notnull' => true
			),
			/*'svrprivateport' => array(
					'type' => 'integer',
					'length' => 8,
					'notnull' => true
			),*/
			'svrsalt' => array(
					'type' => 'text',
					'length' => 256,
					'notnull' => true
			)
	);



	$ilDB->createTable("rep_robj_xbbb_conf", $fields_conf);
	$ilDB->addPrimaryKey("rep_robj_xbbb_conf", array("id"));
}
?>

<#2>
<?php
if(!$ilDB->tableExists('rep_robj_xbbb_data')){
	$fields_data = array(
		'id' => array(
			'type' => 'integer',
			'length' => 8,
			'notnull' => true
		),
		'is_online' => array(
			'type' => 'integer',
			'length' => 1,
			'notnull' => false
		),
		'attendeepwd' => array(
			'type' => 'text',
			'length' => 256,
			'notnull' => false
		),
		'moderatorpwd' => array(
			'type' => 'text',
			'length' => 256,
			'notnull' => false
		),
		'welcometext' => array(
			'type' => 'text',
			'length' => 1000,
			'notnull' => false
		),
		'maxparticipants' => array(
			'type' => 'integer',
			'length' => 2,
			'notnull' => false
		),
		'sequence' => array(
			'type' => 'integer',
			'length' => 8,
			'notnull' => false
		)
		
	);

	$ilDB->createTable("rep_robj_xbbb_data", $fields_data);
	$ilDB->addPrimaryKey("rep_robj_xbbb_data", array("id"));
}


if(!$ilDB->tableExists('rep_robj_xbbb_conf')){
	$fields_conf = array(
			'id' => array(
					'type' => 'integer',
					'length' => 4,
					'notnull' => true
			),
			'svrpublicurl' => array(
					'type' => 'text',
					'length' => 256,
					'notnull' => true
			),
			/*'svrpublicport' => array(
					'type' => 'integer',
					'length' => 8,
					'notnull' => true
			),*/
			'svrprivateurl' => array(
					'type' => 'text',
					'length' => 256,
					'notnull' => true
			),
			/*'svrprivateport' => array(
					'type' => 'integer',
					'length' => 8,
					'notnull' => true
			),*/
			'svrsalt' => array(
					'type' => 'text',
					'length' => 256,
					'notnull' => true
			)
	);



	$ilDB->createTable("rep_robj_xbbb_conf", $fields_conf);
	$ilDB->addPrimaryKey("rep_robj_xbbb_conf", array("id"));
}
?>
