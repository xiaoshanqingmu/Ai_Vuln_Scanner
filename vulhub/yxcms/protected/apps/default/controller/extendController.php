<?php
class extendController extends commonController{
  
	public function index()
    {
        $id=in($_GET['id']);
        if(empty($id)) $this->pageerror('404');
        else{
           $sortinfo=model('sort')->find("id='{$id}'",'name,path,url,type,deep,tplist,keywords,description,extendid');
           if(6!=$sortinfo['type']) $this->pageerror('404');
           $path=$sortinfo['path'].','.$id;
           $deep=$sortinfo['deep']+1;
        }
        $tableid=$sortinfo['extendid'];
        if(empty($tableid)) $this->error('参数错误~');
        $tableinfo = model('extend')->select("id='{$tableid}' OR pid='{$tableid}'",'id,tableinfo,name,type,defvalue','pid');
        if(empty($tableinfo)) $this->error('自定义表不存在~');
        if (!$this->isPost()) {
           $tablename=$tableinfo[0]['tableinfo'];
           $listRows=intval($sortinfo['url']);//每页显示的信息条数
           $url=url('extend/index',array('id'=>$id,'page'=>'{page}'));
           $limit=$this->pageLimit($url,$listRows);
           $count=model('extend')->Extcount($tablename,"ispass='1'");//获取行数
           $list=model('extend')->Extselect($tablename,"ispass='1'",'id desc',$limit);
           $this->list=$list;
           $this->id=$id;
           $this->title=$tableinfo[0]['name'];
           $this->tableinfo=$tableinfo;
           $this->daohang=$this->crumbs($path);//面包屑导航
           $this->sortlist=$this->sortArray(0,$deep,$path);//子分类信息
           $this->title=$sortinfo['name'];//title标签
           if(!empty($sortinfo['keywords'])) $this->keywords=$sortinfo['keywords'];
           if(!empty($sortinfo['description'])) $this->description=$sortinfo['description'];
           $this->rootid=$this->getrootid($_GET['id']);//根节点id
           $this->page=$this->pageShow($count);
           $this->display($sortinfo['tplist']);
        }else{
           if(isset($_SESSION['verify']) && $_POST['checkcode']!=$_SESSION['verify']) $this->error('验证码错误，请重新输入');
           for($i=1;$i<count($tableinfo);$i++){
            if(is_array($_POST[$tableinfo[$i]['tableinfo']]))
              $data[$tableinfo[$i]['tableinfo']]=implode(',',$_POST[$tableinfo[$i]['tableinfo']]);
            else
              $data[$tableinfo[$i]['tableinfo']]=in($_POST[$tableinfo[$i]['tableinfo']]);
           }
           $data['ip']=get_client_ip();
           $data['ispass']=0;
           $data['addtime']=time();
           if(model('extend')->Extin($tableinfo[0]['tableinfo'],$data)) $this->success('提交成功请等待审核~',url());
           else $this->error('提交失败~');
         }
    }
    //文件上传
    public function file()
    {
        header("content-type:text/html; charset=utf-8");
        if (!$this->isPost()) {
            $inputName = $_GET['inputName'];
            echo '<form action="'.url('extend/file').'" method="post" style=" margin:0; padding:0;"  enctype="multipart/form-data" name="form1" id="form1">';
            echo '<label for="fileField"></label>';
            echo '<input type="file" name="fileField" id="fileField" style=" width:180px;" size="17" />';
            echo '<input name="do" type="hidden" value="yes" />';
            echo '<input name="inputName" type="hidden" value="'.$inputName.'" />';
            echo '&nbsp;<input type="submit" value="上传" />';
            echo '</form>';
        }else{
            if (empty($_FILES['fileField']['name'])){
                $this->error('未选择文件');
            }
            $upload= $this->upload(ROOT_PATH.'/upload/extend/'.date('Y-m-d').'/',config('fileupSize'), config('allowType'));
            $upload->saveRule = date('ymdhis') . mt_rand(); //命名规范
            $upload->upload(); //上传
            $info = $upload->getUploadFileInfo(); //返回信息 Array ( [0] => Array ( [name] => 未命名.jpg [type] => image/pjpeg [size] => 53241 [key] => Filedata [extension] => jpg [savepath] => ../../../upload/2011-12-17/ [savename] => 1112170727041127335395.jpg ) )
            $errorinfo=$upload->getErrorMsg();
            if(!empty($errorinfo)) echo '<a style="font-size:12px; color:#333" href="'.url('extendfield/file').'/&inputName='.$_POST['inputName'].'">'.$errorinfo.'</a>';
            else {
                echo '上传成功~<a style="font-size:12px; color:#333" href="'.url('extendfield/file',array('inputName'=>$_POST['inputName'])).'">点击重新上传</a>';
                echo '<script>parent.$("#'.$_POST['inputName'].'").val("'.__ROOT__.'/upload/extend/'.date('Y-m-d').'/'.$info[0]['savename'].'")</script>';
            }
        }

    }
}