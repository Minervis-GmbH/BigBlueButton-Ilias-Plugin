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
		),


		
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
<#3>
<?php
if (!$ilDB->tableColumnExists("rep_robj_xbbb_conf", "choose_recording")){
	$ilDB->addTableColumn('rep_robj_xbbb_conf', 'choose_recording', array(
		'type' => 'integer',
		'length' => 1,
		'notnull' => true,
		'default' => 1
	));
}
?>
<#4>
<?php
if (!$ilDB->tableColumnExists("rep_robj_xbbb_data", "dialnumber") &&
	!$ilDB->tableColumnExists("rep_robj_xbbb_data", "duration") &&
	!$ilDB->tableColumnExists("rep_robj_xbbb_data", "accesstoken") &&
	!$ilDB->tableColumnExists("rep_robj_xbbb_data", "refreshtoken") &&
	!$ilDB->tableColumnExists("rep_robj_xbbb_data", "guestlink") &&
	!$ilDB->tableColumnExists("rep_robj_xbbb_data", "guestchoose") &&
	!$ilDB->tableColumnExists("rep_robj_xbbb_data", "accesscode")){
	$ilDB->addTableColumn('rep_robj_xbbb_data','dialnumber', array(
		'type' => 'text',
		'length' => 256,
		'notnull' => false
	));
	$ilDB->addTableColumn('rep_robj_xbbb_data','accesstoken', array(
		'type' => 'text',
		'length' => 256,
		'notnull' => false
	));
	$ilDB->addTableColumn('rep_robj_xbbb_data','refreshtoken', array(
		'type' => 'text',
		'length' => 256,
		'notnull' => false
	));
	$ilDB->addTableColumn('rep_robj_xbbb_data','guestlink', array(
		'type' => 'text',
		'length' => 256,
		'notnull' => false
	));
	
	$ilDB->addTableColumn('rep_robj_xbbb_data','accesscode', array(
		'type' => 'integer',
		'length' => 8,
		'notnull' => false
	));
	$ilDB->addTableColumn('rep_robj_xbbb_data', 'duration', array(
		'type' => 'integer',
		'length' => 8,
		'notnull' => false,
		'default' => 0
	));
	$ilDB->addTableColumn('rep_robj_xbbb_data', 'guestchoose', array(
		'type' => 'integer',
		'length' => 2,
		'notnull' => true,
		'default' => 0
	));

	
}
	if(!$ilDB->tableColumnExists('rep_robj_xbbb_conf', 'guestglobalchoose')){
		$ilDB->addTableColumn('rep_robj_xbbb_conf', 'guestglobalchoose', array(
			'type' => 'integer',
			'length' => 2,
			'notnull' => true,
			'default' => 0
		));
		
	}
?>
<#5>
<?php
if (
	!$ilDB->tableColumnExists("rep_robj_xbbb_data", "accesstoken") &&
	!$ilDB->tableColumnExists("rep_robj_xbbb_data", "refreshtoken") &&
	!$ilDB->tableColumnExists("rep_robj_xbbb_data", "guestlink") &&
	!$ilDB->tableColumnExists("rep_robj_xbbb_data", "guestchoose")){

	$ilDB->addTableColumn('rep_robj_xbbb_data','accesstoken', array(
		'type' => 'text',
		'length' => 256,
		'notnull' => false
	));
	$ilDB->addTableColumn('rep_robj_xbbb_data','refreshtoken', array(
		'type' => 'text',
		'length' => 256,
		'notnull' => false
	));
	$ilDB->addTableColumn('rep_robj_xbbb_data','guestlink', array(
		'type' => 'text',
		'length' => 256,
		'notnull' => false
	));
	

	$ilDB->addTableColumn('rep_robj_xbbb_data', 'guestchoose', array(
		'type' => 'integer',
		'length' => 2,
		'notnull' => true,
		'default' => 0
	));

	
}

?>
<#6>
<?php
if (!$ilDB->tableColumnExists("rep_robj_xbbb_data", "presentationurl")){
	$ilDB->addTableColumn('rep_robj_xbbb_data','presentationurl', array(
		'type' => 'text',
		'length' => 256,
		'notnull' => false,
		'default' => ''
	));
	
}
?>
<#7>
<?php
if (!$ilDB->tableColumnExists("rep_robj_xbbb_data", "publish")){
	$ilDB->addTableColumn('rep_robj_xbbb_data','publish', array(
		'type' => 'integer',
		'length' => 2,
		'notnull' => false,
		'default' => 1
	));
	
}
?>
<#8>
<?php
if (!$ilDB->tableColumnExists("rep_robj_xbbb_data", "allow_download")){
	$ilDB->addTableColumn('rep_robj_xbbb_data','allow_download', array(
		'type' => 'integer',
		'length' => 2,
		'notnull' => false,
		'default' => 0
	));
	
}
?>
<#9>
<?php
if ($ilDB->tableColumnExists("rep_robj_xbbb_conf", "svrpublicport")){
	$ilDB->dropTableColumn('rep_robj_xbbb_conf','svrpublicport');	
}
if ($ilDB->tableColumnExists("rep_robj_xbbb_conf", "svrprivateport")){
	$ilDB->dropTableColumn('rep_robj_xbbb_conf','svrprivateport');	
}
if ($ilDB->tableColumnExists("rep_robj_xbbb_conf", "svrprivateurl")){
	$ilDB->dropTableColumn('rep_robj_xbbb_conf','svrprivateurl');	
}
?>
<#10>
<?php
if (!$ilDB->tableColumnExists("rep_robj_xbbb_conf", "sess_msg_concurrent")
  && !$ilDB->tableColumnExists("rep_robj_xbbb_conf", "sess_max_concurrent")
  && !$ilDB->tableColumnExists("rep_robj_xbbb_conf", "sess_enable_max_concurrent")
  && !$ilDB->tableColumnExists("rep_robj_xbbb_conf", "enable_userlimit")
){
	$ilDB->addTableColumn('rep_robj_xbbb_conf','sess_enable_max_concurrent', array(
		'type' => 'integer',
		'length' => 2,
		'notnull' => false,
		'default' => 0
	));
	$ilDB->addTableColumn('rep_robj_xbbb_conf','enable_userlimit', array(
		'type' => 'integer',
		'length' => 2,
		'notnull' => false,
		'default' => 0
	));
	$ilDB->addTableColumn('rep_robj_xbbb_conf','sess_max_concurrent', array(
		'type' => 'integer',
		'length' => 2,
		'notnull' => false,
		'default' => 0
	));
	$ilDB->addTableColumn('rep_robj_xbbb_conf','sess_msg_concurrent', array(
		'type' => 'text',
		'length' => 1000,
		'notnull' => false
	));
	
}
?>
<#11>
<?php
	if ($ilDB->tableColumnExists("rep_robj_xbbb_data", "guestchoose")){
		$ilDB->dropTableColumn('rep_robj_xbbb_data','guestchoose');	
	}
	
	if (!$ilDB->tableColumnExists("rep_robj_xbbb_data", "guestpolicy")){
	$ilDB->addTableColumn('rep_robj_xbbb_data','guestpolicy', array(
		'type' => 'text',
		'length' => 256,
		'notnull' => false,
		'default' => ''
	));
	}
	
	$ilDB->manipulate("UPDATE rep_robj_xbbb_data SET guestpolicy = ".$ilDB->quote("ASK_MODERATOR", "text"));
?>
