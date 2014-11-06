<?php

class AlbumAction extends Action{

	public function createAlbum($albumName, $albumTheme, $albumDescription){
		
		$album['name'] = $albumName;
		$album['theme'] = $albumTheme;
		$album['description'] = $albumDescription;
		$album['time'] = date("Y-m-d H:i:s");
		$album['publish'] = 0;

		$Album = M('Album');
		if($Album->create($album)){
			$result = $Album->add($album);
			if($result){
				redirect('uploadphoto', 2, '页面跳转中');
			}else{
				echo "create album fail.";
			}
		}
	}

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
	    	foreach ($info as $photo) {
	    		// 将图片路径、时间存入数据库
	    		$one['time'] = date("Y-m-d H:i:s");
	    		$one['path'] = $photo['savepath'].$photo['savename'];
	    		$result = $Photo->add($one);
	    	}
	    	
	        $this->success('上传成功！');
	    }
	}


	public function edit($albumId){

		$this->albumId = $albumId;
		$this->display();
	}

}


?>