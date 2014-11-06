<?php

class PhotoAction extends Action{

	public function photolist($albumId){
		if($albumId == null)
			return

		$condition['album.id'] = $albumId;
		$Photo = M("Photo");
		$photos = $Photo->join("scene on scene.id = photo.sceneId")->join("album on album.id = scene.albumId")->where($condition)->select();
		dump($photos);
	}
}



?>