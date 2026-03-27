<?php
class inforController extends commonController
{
	    public function index()
	    {
        if(!$this->isPost()){
           $auth=$this->auth;
           $id=$auth['id'];
           $info=model('members')->find("id='{$id}'");
           $this->info=$info;
           $this->display();
        }else{
           $id=intval($_POST['id']);

           $data['nickname']=in(trim($_POST['nickname']));
           $acc=model('members')->find("id!='{$id}' AND nickname='".$data['nickname']."'");
           if(!empty($acc['nickname'])) $this->error('该昵称已经有人使用~');

           $data['email']=$_POST['email'];
           $data['tel']=in($_POST['tel']);
           $data['qq']=in($_POST['qq']);
           if(model('members')->update("id='{$id}'",$data)) $this->success('信息编辑成功~');
           else $this->error('信息编辑失败~');
        }
	    }
      
      public function password()
      {
         if(!$this->isPost()){
           $this->display();
        }else{
           if($_POST['password']!=$_POST['surepassword']) $this->error('确认密码与新密码不符~');
           $auth=$this->auth;
           $id=$auth['id'];
           $info=model('members')->find("id='{$id}'",'password');
           $oldpassword=$this->codepwd($_POST['oldpassword']);
           if($oldpassword!=$info['password']) $this->error('旧密码不正确~');
           
           $data['password']=$this->codepwd($_POST['password']);
           if(model('members')->update("id='{$id}'",$data)) $this->success('密码修改成功~',url('index/password'));
           else $this->error('密码修改失败~');
        }
      }
      public function rmb()
      {
        $auth=$this->auth;
        $id=$auth['id'];
        $info=model('members')->find("id='{$id}'","rmb,crmb");
        $info['rrmb']=$info['rmb']-$info['crmb'];
        $this->info=$info;
        $this->display();
      }
}
?>