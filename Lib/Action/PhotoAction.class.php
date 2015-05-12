<?php

require 'iptc.php';

class PhotoAction extends Action{

    /**
     * 检查是否有session存在，不存在，则重定向到登录页
     */
    public function checkIfLogin(){
        $userid = session('userid');
        if (empty($userid)) {
            $this->redirect("User/login");
        }
    }
    /**
     * 照片展示列表
     * @param $albumId
     */
	public function photolist($albumId){
        // check the session.
        $this->checkIfLogin();

		$Photo = D("Photo");
        $Album = M('Album');

        $album = $Album->find($albumId);

        $this->tags = explode( ",",$album["theme"]);
        $this->album = $album;
        $this->albumId = $albumId;
		$this->photos = $Photo->where("album_id = ".$albumId)->relation(true)->select();
		// dump($this->photos);
		$this->display();
	}

    /**
     * 跳转到查看照片页面
     * @param $albumId
     * @param $pid
     */
	public function view($albumId, $pid){
        // check the session.
        $this->checkIfLogin();

		$Photo = D('Photo');
        // find all photos according to the album id.
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
                break;
            }
        }

        $this->currentIndex = $i + 1;
        // the total number of photos.
        $this->totalSize = sizeof($allPhotos);
        // next photo id
        $this->next = $allPhotos[$next]['id'];
        // pre photo id
        $this->pre = $allPhotos[$pre]["id"];
        $this->albumId = $albumId;
		$this->photo = $Photo->relation(true)->find($pid);

//		dump($this->photo);
		layout(false);
		$this->display();
	}

    /**
     * 给照片增加评论
     * @param $photoId
     * @param $desc
     */
    function addDesc($photoId, $desc){
        // check the session.
        $this->checkIfLogin();

        $Photo = M('Photo');
        $Photo->find($photoId);
        $path = $Photo->path;
        // Save photo description to database.
        $Photo->description = $desc;
        $Photo->save();

        Log::write("after write.....:".$path, "INFO");
        // Save photo description to photo self.
        $photoAbsPath = substr($path, 1);
        if (file_exists($photoAbsPath)) {
            Log::write("begin write", "INFO");
            $iptcPhoto = new iptc($photoAbsPath);
            $iptcPhoto->set(IPTC_CAPTION, $desc);
            $iptcPhoto->write();
            Log::write("Write description info to photo '".$photoId."', dsc = '".$desc."'", "INFO");
        }
        Log::write("after write-======:".$photoAbsPath, "INFO");
        $this->ajaxReturn($photoId, 'add desc success', 1);
    }

    /**
     * 删除照片
     * @param $photoId
     */
    function delete($photoId){
        // check the session.
        $this->checkIfLogin();

        $Photo = M('Photo');
        $Photo->delete($photoId);
    }

}



?>