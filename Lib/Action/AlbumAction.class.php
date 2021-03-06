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
     * 发送post请求，用于系统间数据交换
     * @param $url, $data_string
     */
    public function http_post_data($url, $data_string) {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=utf-8',
            'Content-Length: ' . strlen($data_string))
        );
        ob_start();
        curl_exec($ch);
        $return_content = ob_get_contents();
        ob_end_clean();

        $return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        return array($return_code, $return_content);
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

        $Photo = M('Photo');
        $photoNumber = $Photo->where('album_id = '.$albumId)->count();

        $this->num = 20 - $photoNumber;
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
            $albumId = $_POST['albumId'];
            $one['album_id'] = $albumId;

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

            // Get the album name, theme, and username of this album, then set them in the photo iptc.
            $Album = M('Album');
            $Album->find($albumId);
            $albumName = $Album->name;
            $albumTheme = $Album->theme;
            $userId = session('userid');
            $User = M('User');
            $User->find($userId);
            $userName = $User->username;
            $iptcPhoto = new iptc($photoAbsPath);
            $iptcPhoto->set(IPTC_LOCAL_CAPTION, $albumName);
            $iptcPhoto->set(IPTC_CATEGORY, $albumTheme);
            $iptcPhoto->set(IPTC_COPYRIGHT_STRING, $userName);
            $iptcPhoto->write();
            Log::write("Write album name to photo '".$result."', album name = '".$albumName."'", "INFO");
            Log::write("Write album theme to photo '".$result."', album theme = '".$albumTheme."'", "INFO");
            Log::write("Write username to photo '".$result."', username = '".$userName."'", "INFO");

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
                $new = true;
                foreach($photosArray[$createDate]['tags'] as $arrayTag){
                    if($arrayTag['id'] == $tag['id']){
                        $new = false;
                    }
                }
                if($new){
                    array_push($photosArray[$createDate]['tags'], $tag);
                }
//				if (!array_key_exists($photosArray[$createDate]['tags'], $tag)) {
//					array_push($photosArray[$createDate]['tags'], $tag);
//				}
			}
		}
		
//		dump($photosArray);
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

//		dump($this->albums);
		$this->display();
	}

    public function listForMobile($userId){
        if(empty($userId)){
            $this->ajaxReturn("user id is null", "get album list fail", 0);
        }else{
            // find user information.
            //$User = M('User');
            //$this->userinfo = $User->find($userId);

            // find album information.
            $Album = D('Album');
            $Photo = D('Photo');
            // find all albums
            $albums = $Album->where('userId = '.$userId)->order('time desc')->select();
            $albumsArray = array();
            foreach($albums as $album){
                // find all photos according to album id.
                $photoList = $Photo->where("album_id = ".$album['id'])->order('time asc')->relation(true)->select();

//                $photoArray = array();
//                foreach($photoList as $onePhoto){
//                    $splits = explode("/", $onePhoto['path']);
//                    $splits[3] = "m_".$splits[3];
//                    $onePhoto['path'] = implode("/", $splits);
//                    array_push($photoArray, $onePhoto);
//                }
                $album['photos'] = $photoList;
                // set album cover.
                if(count($album['photos']) > 0 ){
                    // change cover photo to middle size photo.
                    $album['cover'] = $album['photos'][0]['path'];
                    $tmp = explode("/", $album['cover']);
                    $tmp[3] = "m_".$tmp[3];
                    $album['cover'] = implode("/", $tmp);
                    array_push($albumsArray, $album);
                }
            }
            $this->ajaxReturn($albumsArray, "get album list success", 1);
        }
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
            // ======================
            // publish the album, set `publish` field to 1
            // ======================
            $Album = M('Album');
            $Album->find($albumId);
            $Album->publish = 1;
            $Album->save();

            // ======================
            // send the pictures of this album to another system by http post.
            // ======================
            $url  = "http://xiaoxi.cgs.gov.cn:8001/Services/CoverService.svc/insertPicture";
            $pictureList = array();
            $Photo = M('Photo');
            $condition['album_id'] = $albumId;
            $list = $Photo->where($condition)->select();
            foreach ($list as $picture) {
                $one = array();
                $one['Title'] = '';
                $one['Url'] = $picture['path'];
                $one['Description'] = $picture['description'];
                $one['CameraTime'] = $picture['take_time'];
                $Tagphoto = M('Tagphoto');
                $photoTagId = $Tagphoto->where('photoId='.$picture['id'])->getField('tagId');
                $Tag = M('Tag');
                $tagName = $Tag->where('id='.$photoTagId)->getField('name');
                $one['PictureTag'] = $tagName;
                $one['Author'] = '';
                $one['CategoryUrl'] = '';
                array_push($pictureList, $one);
            }
            $data = array();
            $data['PictureList'] = $pictureList;
            $data = json_encode($data);
            list($return_code, $return_content) = $this->http_post_data($url, $data);
            Log::write("Post returen data:  code = '".$return_code."', content = '".$return_content."'", "INFO");
            // echo $data;
            // dump($data);
           
            $this->success("发布成功", '/index.php/Album/show/albumId/'.$albumId);
        }
	}

    /**
     * 影集发布成功后的展示页面
     * @param string $albumId
     * @return mixed|void
     */
    public function show($albumId){
        if(!$albumId)
            return;

        $Album = M('Album');
        $album = $Album->find($albumId);
        $this->album = $album;
        $this->albumId = $albumId;
        $this->tags = explode( ",",$album["theme"]);

        $Photo = D('Photo');
        $photoList = $Photo->where('album_id = '.$albumId)->relation(true)->select();
//        dump($photoList);
        $this->photos = $photoList;
//        dump(explode( ",",$tags));
        $this->display();

    }
    
}


?>