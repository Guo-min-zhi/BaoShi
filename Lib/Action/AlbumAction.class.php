<?php

require 'iptc.php';

class AlbumAction extends Action{

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
     * 跳转到创建影集页面
     */
    public function create(){
        // check the session.
        $this->checkIfLogin();

        $this->display();
    }
    /**
     * 创建影集
     * @param $albumName
     * @param $albumTheme
     * @param $albumDescription
     */
	public function createAlbum($albumName, $albumTheme, $albumDescription){
        // check the session.
        $this->checkIfLogin();

		$album['name'] = $albumName;
		$album['theme'] = $albumTheme;
		$album['description'] = $albumDescription;
		$album['time'] = date("Y-m-d H:i:s");
		$album['publish'] = 0;
        $album['userId'] = session('userid');

		$Album = M('Album');
		if($Album->create($album)){
			$result = $Album->add($album);
			if($result){
				//redirect('uploadphoto/albumId/'.$result, 2, '创建成功，页面跳转中');
				$this->success('创建成功，页面跳转中', '/index.php/Album/uploadphoto/albumId/'.$result);
			}else{
				$this->error("创建失败");
			}
		}
	}

    /**
     * 跳转到上传照片页面
     * @param $albumId
     */
	public function uploadphoto($albumId){
		$this->albumId = $albumId;
		$this->display();
	}

    /**
     * 上传照片
     */
	public function upload(){
		//=============================================================
		// 上传参数的设置
		//=============================================================
		import('ORG.Net.UploadFile');
		$upload = new UploadFile();// 实例化上传类
	    $upload->maxSize  = 3145728 ;// 设置附件上传大小
	    $upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
	    $upload->savePath =  './Public/Uploads/';// 设置附件上传目录
	    $upload->thumb = true;
	    $upload->thumbPrefix = 'm_,s_'; 
	    $upload->thumbMaxWidth = '200,50';
	    $upload->thumbMaxHeight = '200,50';
	    //=============================================================

	    if(!$upload->upload()) {// 上传错误提示错误信息
	        $this->error($upload->getErrorMsg());
	    }else{// 上传成功
	    	$Photo = M('Photo');
	    	$info = $upload->getUploadFileInfo();
            // 将图片路径、时间存入数据库
            $one['time'] = date("Y-m-d H:i:s");
            $photoPath = substr($info[0]['savepath'].$info[0]['savename'], 1);   //Example: /Public/Uploads/546c033110bfc.jpg
            $one['path'] = $photoPath;
            $one['album_id'] = $_POST['albumId'];

            // get the photo's take time from exif data.
            $photoAbsPath = substr($photoPath, 1);     //Example: Public/Uploads/546c033110bfc.jpg
            if (file_exists($photoAbsPath)) {
            	$exif = exif_read_data($photoAbsPath, 0, true);
            	if (array_key_exists("EXIF", $exif) && array_key_exists("DateTimeOriginal", $exif["EXIF"])) {
            		$dateOriginal = $exif['EXIF']['DateTimeOriginal'];
            		$one['take_time'] = $dateOriginal;
            	}
            } 
            // return the photo id.
            $result = $Photo->add($one);
            // get the photo according above photo id.
            $photo = $Photo->find($result);
            $this->ajaxReturn($photo, 'upload success', 1);
	    }
	}


    /**
     * 跳转到编辑影集页面
     * @param $albumId
     */
	public function edit($albumId){
        // check the session.
        $this->checkIfLogin();

		$Photo = D('Photo');
		$photosArray = array();
		$photoList = $Photo->where('album_id = '.$albumId)->relation(true)->select();
		foreach ($photoList as $photo) {
			if (empty($photo['take_time'])) {
				$createDate = "没有拍摄时间";
			}else{
				$createDate = date('Y-m-d', strtotime($photo['take_time']));
			}
			if (!array_key_exists($createDate, $photosArray)) {
				$photosArray[$createDate] = array();
				$photosArray[$createDate]['photos'] = array();
				$photosArray[$createDate]['tags'] = array();
			}
			array_push($photosArray[$createDate]['photos'], $photo);
			$tags = $photo['tags'];
			foreach ($tags as $tag) {
				if (!array_key_exists($photosArray[$createDate]['tags'], $tag)) {
					array_push($photosArray[$createDate]['tags'], $tag);
				}
			}
		}
		
		// dump($photosArray);
        $this->photosArray = $photosArray;
		$this->photoList = $photoList;
//		$this->albumId = date('Y-m-d', strtotime('2014-11-19 10:35:47'));
		$this->albumId = $albumId;
		$this->display();
	}

    /**
     * 影集列表页面
     */
	public function albumlist(){
        // check the session.
		$this->checkIfLogin();

        // get user id from session.
		$user_id = session('userid');

		// find user information.
		$User = M('User');
		$this->userinfo = $User->find($user_id);

		// find album information.
		$Album = D('Album');
		$this->albums = $Album->where('userId = '.$user_id)->relation(true)->order('time desc')->select();
		//dump($this->albums);
		$this->display();
	}

    /**
     * 删除影集
     * @param $albumId
     */
	public function delete($albumId){
        // check the session.
        $this->checkIfLogin();

		$Album = M('Album');
		$result = $Album->delete($albumId);
		if ($result) {
			$this->success('删除成功', '/index.php/Album/albumlist');
		}else{
			$this->error('删除失败', '/index.php/Album/albumlist');
		}
	}

    /**
     * 发布影集
     * @param $albumId
     */
	public function publish($albumId){

		if ($albumId) {
			$Album = M('Album');
			$Album->find($albumId);
			$Album->publish = 1;
			$Album->save();
		}
	}

}


?>