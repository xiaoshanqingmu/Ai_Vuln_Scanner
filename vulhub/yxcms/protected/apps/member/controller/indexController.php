<?php
class indexController extends commonController
{
	    public function index()
	    {
        $this->act=empty($_GET['act'])?url('index/welcome'):$_GET['act'];
        $this->display();
	    }

      public function welcome()
      {
          $this->display();
      }

      public function login()
      {
        if(!$this->isPost()){
            $cookie_auth=get_cookie('auth');
            if(!empty($this->auth)) $this->redirect(url('default/index/index'));
            $this->returnurl=$_SERVER['HTTP_REFERER'];
            $this->display();
        }else{
            if(isset($_SESSION['verify']) && $_POST['checkcode']!=$_SESSION['verify']) $this->error('验证码错误，请重新输入');
            if(empty($_POST['name'])||empty($_POST['word'])) $this->error('请填写完整信息~');
            $account=in(trim($_POST['name']));
            $password=$_POST['word'];
            $cookietime=empty($_POST['cooktime'])?0:intval($_POST['cooktime']);
            $returnurl=empty($_POST['returnurl'])?$_SERVER['HTTP_REFERER']:$_POST['returnurl'];
            if($this->_login($account,$password,$cookietime))
            {
                $this->redirect($returnurl);
            }
            else $this->error('用户名或密码错误，或者您的账户已被锁定');
        }
      }

      protected function _login($account,$password,$cookietime=0)
      {
          $acc=model('members')->find("account='{$account}'");
          if($acc['password']!=$this->codepwd($password) || $acc['islock']) return false;
          if($cookietime!=0) $cookietime=time()+$cookietime;
          $data['lastip'] = get_client_ip();
          $data['lasttime']=time();
          model('members')->update("account='{$account}'",$data);
          $cookie_auth = $acc['id'].'\t'.$acc['groupid'].'\t'.$acc['account'].'\t'.$acc['nickname'].'\t'.$acc['lastip'];
          if(set_cookie('auth',$cookie_auth,$cookietime)) return true;
          return false;
      }

      //用户退出
      public function logout()
      {
          $url=empty($_GET['url'])?$_SERVER['HTTP_REFERER']:$_GET['url'];
          if(set_cookie('auth','',time()-1)) $this->success('您已成功退出~',$url);
      }

      public function regist()
      {
        if(!$this->isPost()){
            if(!empty($this->auth)) $this->redirect(url('default/index/index'));
            $this->display();
        }else{
            if(empty($_POST['checkcode'])||$_POST['checkcode']!=$_SESSION['verify']) $this->error('验证码错误~');
            if(empty($_POST['name'])||empty($_POST['word'])||empty($_POST['email'])) $this->error('请填写完整信息~');
            
            $data['account']=in(trim($_POST['name']));
            $acc=model('members')->find("account='".$data['account']."'");
            if(!empty($acc['account'])) $this->error('该账户已经有人注册~');
            $data['email']=in(trim($_POST['email']));
            if($_POST['word']!=$_POST['sureword']) $this->error('两次密码不相同~');
            $data['password']=$this->codepwd($_POST['word']);
            $data['regip']=$data['lastip']=get_client_ip();
            $data['regtime']=$data['lasttime']=time();
            $data['rmb']=$data['crmb']=0;
            $data['islock']=0;
            $data['groupid']=2;
            $id=model('members')->insert($data);
            if($id){
               $cookie_auth = $id.'\t'.$data['groupid'].'\t'.$data['account'].'\t'.$data['nickname'].'\t'.$data['lastip'];
               if(set_cookie('auth',$cookie_auth,0)) $this->success('注册成功~',url('index/index'));
            }else $this->error('数据库写入失败~');
        }
      }
      
      //生成验证码
      public function verify()
      {
          Image::buildImageVerify();
      }
}
?>