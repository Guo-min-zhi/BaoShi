<?php

class TagAction extends Action{

	public function create($tagName){
		$Tag = M('Tag');
		$condition['name'] = $tagName;
		$find = $Tag->where($condition)->find();

		if (!$find) {
			$id = $Tag->add($condition);
			$condition['id'] = $id;
			$this->ajaxReturn($condition, "新增成功", 1);
		} else {
			$this->ajaxReturn($find, "查找成功", 1);
		}
	}

	public function photoAddTag($photoId, $tagId){
		$Tagphoto = M('Tagphoto');
		$condition['tagId'] = $tagId;
		$condition['photoId'] = $photoId;
		$id = $Tagphoto->add($condition);
		if ($id != false) {
			$this->ajaxReturn($id, '关联成功', 1);
		} else {
			$this->ajaxReturn($id, '关联失败', 0);
		}
	}

	public function photoDeleteTag($photoId, $tagId){
		$Tagphoto = M('Tagphoto');
		$condition['tagId'] = $tagId;
		$condition['photoId'] = $photoId;
		$id = $Tagphoto->where($condition)->delete();
		if ($id != false) {
			$this->ajaxReturn($id, '解除关联成功', 1);
		} else {
			$this->ajaxReturn($id, '解除关联失败', 0);
		}
	}


}

?>