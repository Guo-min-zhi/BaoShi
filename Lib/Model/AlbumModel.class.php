<?php

class AlbumModel extends RelationModel{

	protected $_link = array(
		"Photo" => array(
			'mapping_type'=>HAS_MANY,
			'class_name'=>'Photo',
            'foreign_key'=>'album_id',
            'mapping_name'=>'photos'
		)
	);
}



?>