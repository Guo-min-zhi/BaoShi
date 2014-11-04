<?php

class FormAction extends Action{
    public function insert(){
        $Form   =   D('Form');
        if($Form->create()) {
            $result =   $Form->add();
            if($result) {
                $this->success('操作成功！');
            }else{
                $this->error('写入错误！');
            }
        }else{
            $this->error($Form->getError());
        }
    }

    // public function userlogin($username='aa', $password='aa'){
    //     $User = M('User');
    //     $condition['username'] = $username;
    //     $condition['password'] = $password;
    //     $find = $User->where($condition)->find();
    //     Log::write(dump($find));
    // }
    public function ulist(){
        $Model = new Model();
        $this->users = $Model->query("select * from think_user");
        dump($this->users);
        $this->display();
    }
}