<?php

class UserAction extends Action{
	/**
	 * 登陆逻辑
	 */
	public function login(){
		if ($this->isGet()) {
            layout(false);
			$this->display();
		}else if($this->isPost()){
			$username = $_POST["username"];
			$password = $_POST["password"];
			$user = $this->hasUser($username, $password);
			if($user != null){
//				echo "login successful!";
                // save user info into session;
                session('username', $username);
                session('userid', $user['id']);

                Log::write("User '".$username."' have login from web.", "INFO");

                $this->redirect("Album/albumlist");
			} else {
//				echo "login fail!";
                $this->redirect("User/login");
			}
		}
	}

    /**
     * 移动端登陆逻辑
     */
    public function loginForMobile(){
        $username = $_GET["username"];
        $password = $_GET["password"];
        $user = $this->hasUser($username, $password);
        if($user != null){
            Log::write("User '".$username."' have login from mobile.", "INFO");
            $this->ajaxReturn($user, "login success", 1);
        }else{
            $this->ajaxReturn($user, "login fail", 0);
        }
    }

    public function logout(){
        session(null);
        $this->redirect("User/login");
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
            layout(false);
			$this->display();
		}else if($this->isPost()){
			$User = M("User");
			if($User->create()){
				$result = $User->add();
				if($result){
//					echo "add success";

                    Log::write("User '".$_POST['username']."' register.", "INFO");
                    session(array('name'=>$_POST["username"], 'expire'=>3600));
                    $this->redirect("Album/albumlist");
				}else{
//					echo "add fail";
                    $this->redirect("User/register");
				}
			}else{
				echo "create fail";
			}
		}
	}

    /**
     * 移动端的注册逻辑
     */
    public function registerForMobile(){
        $username = $_POST["username"];
        $email = $_POST["email"];
        $User = M("User");
        $userByName = $User->where('username = "'.$username.'"')->find();
        $userByEmail = $User->where('email = "'.$email.'"')->find();
        if(empty($userByName) and empty($userByEmail)){
            $data["username"] = $username;
            $data["password"] = $_POST["password"];
            $data["email"] = $email;
            $result = $User->add($data);
            if($result){
                $this->ajaxReturn($result, "register success", 1);
            }else{
                $this->ajaxReturn($result, "register fail", 0);
            }
        }else{
            $this->ajaxReturn("username or email is same", "register fail", 0);
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
     * 增加、单向同步用户
     * to: Wang Fei
     */
    public function add(){
        if($this->isPost()){
            $User = M("User");
            if($User->create()){
                $result = $User->add();
                if($result){
                    Log::write("User '".$_POST['username']."' add successful.", "INFO");
                    $this->ajaxReturn($_POST['username'], "add successful", 1);
                }else{
                    $this->ajaxReturn($_POST['username'], "add fail", 0);
                }
            }else{
                $this->ajaxReturn("add fail", "add fail", 0);
            }
        } else {
            $this->ajaxReturn("add fail", "not post method", 0);
        }

    }
    /**
     *	根据用户名称删除用户
     *  to: Wang Fei
     */
    public function delete($username){
        if($username != null){
            $User = M('User');
            $condition['username'] = $username;
            $User->where($condition)->delete();
            Log::write("delete user according username=".$username, "INFO");
            $this->ajaxReturn($username,"delete successful.",1);
        }else{
            $this->ajaxReturn($username,"delete fail.",0);
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

    /**
     * 检查用户名是否重复
     * @param string $username
     */
    public function checkName($username=""){
        if(!empty($username)){
            $User = M("User");
            if($User->getByUsername($username)){
                $this->ajaxReturn("d", "用户名重复", 0);
            }else{
                $this->ajaxReturn("d", "success", 1);
            }
        }else{
            $this->ajaxReturn("asd", "必须填写用户名", 0);
        }
    }

    public function changePassword(){
        if($this->isGet()){
            $this->display();
        }else if($this->isPost()){
            $old = $_POST["originalPassword"];
            $newPassword = $_POST["newPassword"];
            $newPasswordAgain = $_POST["newPasswordAgain"];

            $username = $_SESSION["name"];
            $User = M("User");
            $one = $User->getByUsername($username);
            if($one != null and $one["password"] == $old and $newPassword == $newPasswordAgain){
                $one["password"] = $newPassword;
                $User->save($one);
                $this->success("修改密码成功");
//                echo "successful";
            }else{
                $this->error("修改密码失败");
//                echo "error";
            }
        }
    }

    /**
     * 上传用户头像
     */
    public function uploadHead(){
        $username = "aa";
        if($username != null) {
            //=============================================================
            // 上传参数的设置
            //=============================================================
            import('ORG.Net.UploadFile');
            $upload = new UploadFile();// 实例化上传类
            $upload->maxSize  = 3145728 ;// 设置附件上传大小
            $upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->savePath =  './Public/heads/';// 设置附件上传目录
            $upload->thumb = true;
            $upload->thumbPrefix = 'm_,s_';
            $upload->thumbMaxWidth = '140,50';
            $upload->thumbMaxHeight = '140,50';
            //=============================================================

            if(!$upload->upload()) {// 上传错误提示错误信息
                $this->error($upload->getErrorMsg());
            }else{
                // 上传成功, 返回头像路径
                $User = M("User");
                $headPhoto = $upload->getUploadFileInfo();
                $one = $User->getByUsername($username);
                if($one != null){
                    $one["head"] = substr($headPhoto[0]['savepath'].$headPhoto[0]['savename'], 1);
                    $User->save($one);
                    echo $one["head"];
                }
            }
        }else {
            echo "username is null";
        }
    }

    public function info(){
        $username = "aa";
        $User = M("User");
        $one = $User->getByUsername($username);
        if($one != null){
            if($one.head == null){
                $one->head = "/Public/img/photo.svg";
            }
            $this->one = $one;
            $this->display();
        }
    }

}

?>