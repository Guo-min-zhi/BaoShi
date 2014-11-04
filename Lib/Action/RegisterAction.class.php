<?php

class RegisterAction extends Action{

	public function register(){
		if($this->isGet()){

			$user = M('user');
			$u = $user->select();

			$this->display();
		}else if($this->isPost()){
			
			$User = M("User");
			if($User->create($data)){
				$result = $User->add();
				if($result){
					echo "add success";
				}else{
					// $this->error("add fail");
					echo "add fail";
				}
			}else{
				echo "create fail";
			}
			
		}
		
	}
}