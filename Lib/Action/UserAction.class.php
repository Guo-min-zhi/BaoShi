<?php

class UserAction extends Action{
	/**
	 * 登陆逻辑
	 */
	public function login(){
		if ($this->isGet()) {
			$this->display();
		}else if($this->isPost()){
			$username = $_POST["username"];
			$password = $_POST["password"];
			$user = $this->hasUser($username, $password);
			if($user != null){
				echo "login successful!";
			}
			else{
				echo "login fail!";
			}
		}
	}

	/**
	 *	根据username和password判断用户是否存在
	 */
	private function hasUser($username, $password){
		$User = M('User');
		$condition['username'] = $username;
		$condition['password'] = $password;
		$find = $User->where($condition)->find();
		return $find;
	}

	/**
	 * 注册逻辑
	 */
	public function register(){
		if($this->isGet()){
			$this->display();
		}else if($this->isPost()){
			$User = M("User");
			if($User->create()){
				$result = $User->add();
				if($result){
					echo "add success";
				}else{
					echo "add fail";
				}
			}else{
				echo "create fail";
			}
		}
	}

	/**
	 *	用户列表
	 */
	public function userlist(){
		$User = M("User");
		$this->users = $User->select();
		$this->display();
	}

	/**
	 *	根据用户id删除用户
	 */
	public function deleteuser($id){
		$userid = $id;
		if($userid != null){
			$User = M('User');
			$User->delete($userid);
			$this->success('删除成功');
		}else{
			$this->error('删除失败，无法获得用户ID.');
		}
	}

	/**
	 *	根据用户id获得用户
	 */
	public function userinfo($id){
		if($id != null){
			$User = M('User');
			$this->user = $User->find($id);
			$this->display();
		}
	}

}

?>