<?php

include 'EXIF.php';

class PhotoAction extends Action{



	public function photolist($albumId){

		$Photo = D("Photo");
        $Album = M('Album');

        $album = $Album->find($albumId);

        $this->album = $album;
        $this->albumId = $albumId;
		$this->photos = $Photo->where("album_id = ".$albumId)->relation(true)->select();
		// dump($this->photos);
		$this->display();
	}

	public function view($albumId, $pid){
		$Photo = D('Photo');
        $allPhotos = $Photo->field('id')->where("album_id = ".$albumId)->order('id')->select();
        for($i=0; $i<sizeof($allPhotos); $i++){
            $one = $allPhotos[$i];
            $one_id = $one["id"];
            if($one_id == $pid){
                if($i == 0){
                    $pre = sizeof($allPhotos)-1;
                    $next = $i + 1;
                }elseif($i == sizeof($allPhotos)-1){
                    $pre = $i - 1;
                    $next = 0;
                }else{
                    $pre = $i - 1;
                    $next = $i + 1;
                }
            }
        }

        $this->next = $allPhotos[$next]['id'];
        $this->pre = $allPhotos[$pre]["id"];
        $this->albumId = $albumId;
		$this->photo = $Photo->relation(true)->find($pid);
//        dump($this->photo);
        // $path = $this->photo['path'];
//        $exif = exif_read_data($path, 0, true);
//        dump($path);

//        $this->name = $exif[$path][FileName];

        // $data = get_EXIF_JPEG("../..".$path);
        // dump($data);

		// dump($this->photo);
		layout(false);
		$this->display();
	}

    /**
     * 给照片增加评论
     * @param $photoId
     * @param $desc
     */
    function addDesc($photoId, $desc){
        $Photo = M('Photo');
        $photo = $Photo->find($photoId);
        $Photo->description = $desc;
        $Photo->save();

        $this->ajaxReturn($photoId, 'add desc success', 1);
    }

    /**
     * 删除照片
     * @param $photoId
     */
    function delete($photoId){
        $Photo = M('Photo');
        $Photo->delete($photoId);
    }

}



?>