<?php

class PhotoAction extends Action{

	public function photolist($albumId){

		// $condition['album.id'] = $albumId;
		// $photos = $Photo->join("scene on scene.id = photo.sceneId")->join("album on album.id = scene.albumId")->where($condition)->select();
		$Photo = M("Photo");
		$this->photos = $Photo->where("album_id = ".$albumId)->select();
		//dump($this->photos);
		$this->display();
	}

	public function view($pid){
		$Photo = M('Photo');
		$this->photo = $Photo->find($pid);
		// dump($this->photo);
		layout(false);
		$this->display();
	}
}



?>