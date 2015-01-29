<?php

/**
* 
*/
class PhotoModel extends RelationModel{

	protected $_link = array(
		"tags" => array(
			'mapping_type' => MANY_TO_MANY,
			'class_name' => 'Tag',
			'foreign_key' => 'photoId',
			'relation_foreign_key' => 'tagId',
			'relation_table' => 'tagphoto'
		)
	);

}


?>