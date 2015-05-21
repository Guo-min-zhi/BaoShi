<?php

require 'iptc.php';

class TagAction extends Action{

    /**
     * 创建标签
     * @param $tagName
     */
	public function create($tagId, $tagName, $country, $province){
		$Tag = M('Tag');
		$condition['name'] = $tagName;
        $condition['country'] = $country;
        $condition['province'] = $province;

        $isNew = false;
        // new tag is tag id is null.
        if($tagId == "" or $tagId == null){
            $isNew = true;
            $only = $Tag->where($condition)->find();
            if($only != null){
                $this->ajaxReturn($only, "创建地点成功", 1);
            }else{
                // add
                $result = $Tag->add($condition);
                if( $result!= false){
                    $condition['id'] = $result;
                    $this->ajaxReturn($condition, "创建地点成功", 1);
                } else {
                    $this->ajaxReturn($condition, "创建地点失败", 0);
                }
            }
        } else {
            // modify
            $result = $Tag->where('id='.$tagId)->save($condition);
            if($result){
                $this->ajaxReturn($condition, "修改地点成功", 1);
            } else {
                $this->ajaxReturn($condition, "修改地点失败", 0);
            }
        }
	}

    /**
     * 标签和照片关联
     * @param $photoId
     * @param $tagId
     */
	public function photoAddTag($photoId, $tagId){
		$Tagphoto = M('Tagphoto');
		$condition['tagId'] = $tagId;
		$condition['photoId'] = $photoId;
        // only once.
        $result = $Tagphoto->where('photoId='.$photoId)->find();
        if($result == null){
            $id = $Tagphoto->add($condition);
            if ($id != false) {
                // Save photo tag to photo self.
                $this->operateTag($photoId, $tagId, 1);
                // end save
                $this->ajaxReturn($id, '关联成功', 1);
            } else {
                $this->ajaxReturn($id, '关联失败', 0);
            }
        } else {
            $Tagphoto->where('photoId='.$photoId)->save($condition);
            // Save photo tag to photo self.
            $this->operateTag($photoId, $tagId, 1);
            // end save
            $this->ajaxReturn($id, '关联成功', 1);
        }
	}

    /**
     * 取消照片和标签的关联
     * @param $photoId
     * @param $tagId
     */
	public function photoDeleteTag($photoId, $tagId){
		$Tagphoto = M('Tagphoto');
		$condition['tagId'] = $tagId;
		$condition['photoId'] = $photoId;
		$id = $Tagphoto->where($condition)->delete();
		if ($id != false) {
            // delete photo tag from photo.
            $this->operateTag($photoId, $tagId, 2);
            // end delete

			$this->ajaxReturn($id, '解除关联成功', 1);
		} else {
			$this->ajaxReturn($id, '解除关联失败', 0);
		}
	}

    /**
     * 将照片和标签关联
     * @param $photoId
     * @param $tagId
     * @param $operation
     */
    private function operateTag($photoId, $tagId, $operation){
        $Tag = M('Tag');
        $Tag->find($tagId);
        $Photo = M('Photo');
        $Photo->find($photoId);
        $photoAbsPath = substr($Photo->path, 1);
        if (file_exists($photoAbsPath)) {
            $iptcPhoto = new iptc($photoAbsPath);
            $tagOriginal = $iptcPhoto->get(IPTC_KEYWORDS);
            $tagArray = explode(",", trim($tagOriginal));

            if($operation == 1){
                // add tag
                if(empty($tagOriginal)){
                    $tagArray[0] = $Tag->name;
                }else{
                    array_push($tagArray, $Tag->name);
                }
            }elseif($operation == 2){
                // delete tag
                for($i=0; $i<sizeof($tagArray); $i++){
                    if($tagArray[$i] == $Tag->name){
                        array_splice($tagArray, $i, 1);
                        break;
                    }
                }
            }
            $newTag = join(",", $tagArray);
            $iptcPhoto->set(IPTC_KEYWORDS, $newTag);
            $iptcPhoto->write();
        }

    }


}

?>