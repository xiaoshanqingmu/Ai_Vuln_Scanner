<?php
class sortController extends commonController
{
	static public $sort=array(
	   1=>array('name'=>'文章','mark'=>'news'),
	   2=>array('name'=>'图集','mark'=>'photo'),
	   3=>array('name'=>'单页','mark'=>'page'),
	   4=>array('name'=>'应用','mark'=>'plugin'),
       5=>array('name'=>'自定义','mark'=>'link'),
       6=>array('name'=>'表单','mark'=>'extend')
	);
	//static public $templepath;//前台模板路径
	static public $extendtab=array();
	public function __construct()
	{
		parent::__construct();
		$this->extendtab=model('extend')->select("pid='0' AND type='0'",'id,name');//拓展表信息
	}

    private function sortadd($parentid){
        if($parentid==0){
			$data['path']=',000000';
			$data['deep']=1;
		}else{
			$parent=model('sort')->find("id='{$parentid}'",'id,path,deep');
			$data['path']=$parent['path'].','.$parent['id'];
			$data['deep']=$parent['deep']+1;
		}
		return $data;
    }	
    private function sortedit($path,$newparentid,$id,$mark=''){
		if($id==$newparentid) $this->error('不能将自身作为父类~');
		//判断是否有子类
		$where='\''.$path.','.$id.'\'';
		if(model('sort')->find('path ='.$where))
		$this->error('该分类下有子类不可以任意移动~');
		if($newparentid==0){
			$data['path']=',000000';
			$data['deep']=1;
		}else{
			$parent=model('sort')->find("id='{$newparentid}'",'id,path,deep');
			$data['path']=$parent['path'].','.$parent['id'];
			$data['deep']=$parent['deep']+1;
		}
        if(!empty($mark)){//修改分类下所有信息类别
	    	$updata['sort']=$data['path'].','.$id;
            model($mark)->update('sort='.$where,$updata);
	    }
		return $data;
    }
	//类别管理
	public function index()
	{
		$list=model('sort')->select('','id,type,name,deep,ifmenu,path,norder,method,extendid,url');
		if(!empty($list)){
			$list=re_sort($list);
			foreach ($list as $key=>$vo)
			{
				$list[$key]['url']=getURl($vo['type'],$vo['method'],$vo['url'],$vo['id'],$vo['extendid']);
			}
			$this->list=$list;
		}
		$this->sort=self::$sort;
		$this->display();
	}
	public function add()
	{
	    $sortaction=$_GET['sortaction'];
		  switch ($sortaction) {
		  	case 'noadd':
		  		break;
		  	case 'newsadd':
		  		$this->newsadd();
		  		break;
		  	case 'photoadd':
		  		$this->photoadd();
		  		break;
		  	case 'pageadd':
		  		$this->pageadd();
		  		break;
		  	case 'pluginadd':
		  		$this->pluginadd();
		  		break;
		  	case 'linkadd':
		  		$this->linkadd();
		  		break;
		  	case 'extendadd':
		  		$this->extendadd();
		  		break;
		  	default:
		  		$this->display();
		  		break;
		  }
	}
   
	//添加文章栏目
	public function newsadd()
	{
		$type=1;//文章类型
		if(!$this->isPost())
		{
			$list=model('sort')->select('','id,name,deep,path,norder');
			if(!empty($list)){
				$list=re_sort($list);
				$this->list=$list;
			}
			$choose=$this->tempchoose(self::$sort[$type]['mark'],'index');
            if(!empty($choose)) $this->choose=$choose;
            $exts=$this->extendtab;
			if(!empty($exts)){//拓展表选项
				$extendoption='';
				foreach($exts as $vo)
				   $extendoption.='<option value="'.$vo['id'].'">'.$vo['name'].'</option>';
				$this->extendoption= $extendoption;
			}
			$this->md=self::$sort[$type]['mark'];
			$this->url=url('sort');
			$this->display('sort_newsadd');
		}else{
			if(empty($_POST['sortname']) || empty($_POST['method']) || empty($_POST['tplist'])) $this->error('请填写完整栏目信息！');
			$data=array();
			$parentid=intval($_POST['parentid']);
			$data=$this->sortadd($parentid);//分类添加
			$data['type']=$type;
			$data['name']=in($_POST['sortname']);
			$data['keywords']=in($_POST['keywords']);
			$data['description']=in($_POST['description']);
			$data['url']=intval($_POST['num']);
			$data['method']=in($_POST['method']);
			$data['tplist']=$_POST['tplist'];
			$data['norder']=intval($_POST['norder']);
			$data['ifmenu']=intval($_POST['ifmenu']);
			$data['extendid']=intval($_POST['extendid']);
			//插入数据
			if(model('sort')->insert($data)){
				$this->success('文章栏目添加成功~',url('sort/index'));
			}
			else $this->error('文章栏目添加失败~');
		}
	}
	//编辑文章栏目
	public function newsedit()
	{
		$type=1;//文章类型
		$id=intval($_GET['id']);
		if(empty($id)) $this->error('空的类别参数');
		$info=model('sort')->find("id='$id'",'name,norder,path,ifmenu,url,method,tplist,keywords,description,extendid');
		$info['url']=empty($info['url'])?10:$info['url'];
		$oldparentid=intval(substr ($info['path'], -6));
		if(!$this->isPost())
		{
			$list=model('sort')->select('','id,name,deep,path,norder');
			if(!empty($list)){
				$list=re_sort($list);
				$this->list=$list;
			}
			$tpdef=explode('_',$info['tplist']);
			if(!isset($tpdef[1])) $this->error('非法的模板参数~');
			$choose=$this->tempchoose(self::$sort[$type]['mark'],$tpdef[1]);
            if(!empty($choose)) $this->choose=$choose;	

            $exts=$this->extendtab;
			if(!empty($exts)){//拓展表选项
				$extendoption='';
				foreach($exts as $vo){
					if($vo[id]==$info['extendid'])
					$extendoption.='<option value="'.$vo['id'].'" selected="selected">'.$vo['name'].'</option>';
					else $extendoption.='<option value="'.$vo['id'].'">'.$vo['name'].'</option>';
				}
				$this->extendoption=$extendoption;
			}
			$this->id=$id;
			$this->info=$info;
			$this->oldparentid=$oldparentid;
			$this->display();
		}else{
			if(empty($_POST['sortname']) || empty($_POST['method']) || empty($_POST['tplist'])) $this->error('请填写完整栏目信息！');
			//数据处理
			$data=array();
			$newparentid=intval($_POST['parentid']);
			if($oldparentid!=$newparentid) $data=$this->sortedit($info['path'],$newparentid,$id,self::$sort[$type]['mark']);//分类编辑

			$data['name']=$_POST['sortname'];
			$data['keywords']=in($_POST['keywords']);
			$data['description']=in($_POST['description']);
			$data['url']=intval($_POST['num']);
			$data['method']=in($_POST['method']);
			$data['tplist']=$_POST['tplist'];
			$data['ifmenu']=intval($_POST['ifmenu']);
			$data['norder']=intval($_POST['norder']);
			if($_POST['extendid']!=$_POST['oldextendid']){
				$nsort=$info['path'].','.$id;
			    $ifhas=model(self::$sort[$type]['mark'])->find("sort='$nsort'");
			    if(!empty($ifhas)) $this->alert('栏目下有信息，不能随意更换拓展表~');
			    else $data['extendid']=intval($_POST['extendid']);
			}
			//更新数据
			if(model('sort')->update("id = '$id'",$data)){
				$this->success('文章栏目修改成功',url('sort/index'));
			}
			else $this->error('文章栏目没有任何修改，不需要执行');
		}
	}

	//添加图集栏目
	public function photoadd()
	{
		$type=2;//图集类型
		if(!$this->isPost())
		{
			$list=model('sort')->select('','id,name,deep,path,norder');
			if(!empty($list)){
				$list=re_sort($list);
				$this->list=$list;
			}
			
			$choose=$this->tempchoose(self::$sort[$type]['mark'],'index');
            if(!empty($choose)) $this->choose=$choose;	

			$exts=$this->extendtab;
			if(!empty($exts)){
				$extendoption='';
				foreach($exts as $vo)
				$extendoption.='<option value="'.$vo['id'].'">'.$vo['name'].'</option>';
				$this->extendoption=$extendoption;
			}
			$this->md=self::$sort[$type]['mark'];
			$this->url=url('sort');
			$this->display('sort_photoadd');
		}else{
			if(empty($_POST['sortname']) || empty($_POST['method']) || empty($_POST['tplist'])) $this->error('请填写完整栏目信息！');
			$data=array();
			$parentid=intval($_POST['parentid']);
			$data=$this->sortadd($parentid);//分类添加
			$data['type']=$type;
			$data['name']=$_POST['sortname'];
			$data['keywords']=in($_POST['keywords']);
			$data['description']=in($_POST['description']);
			$data['url']=intval($_POST['num']);
			$data['method']=in($_POST['method']);
			$data['tplist']=$_POST['tplist'];
			$data['norder']=intval($_POST['norder']);
			$data['ifmenu']=intval($_POST['ifmenu']);
			$data['extendid']=intval($_POST['extendid']);
			//插入数据
			if(model('sort')->insert($data)){
				$this->success('图集栏目添加成功~',url('sort/index'));
			}
			else $this->error('图集栏目添加失败~');
		}

	}
	//编辑图集栏目
	public function photoedit()
	{
		$type=2;//图集类型
		$id=intval($_GET['id']);
		if(empty($id)) $this->error('空的类别参数');
		$info=model('sort')->find("id='$id'",'name,norder,path,ifmenu,url,method,tplist,keywords,description,extendid');
		$info['url']=empty($info['url'])?10:$info['url'];
		$oldparentid=intval(substr ($info['path'], -6));
		if(!$this->isPost())
		{
			$list=model('sort')->select('','id,name,deep,path,norder');
			if(!empty($list)){
				$list=re_sort($list);
				$this->list=$list;
			}
			$tpdef=explode('_',$info['tplist']);
			if(!isset($tpdef[1])) $this->error('非法的模板参数~');
			$choose=$this->tempchoose(self::$sort[$type]['mark'],$tpdef[1]);
            if(!empty($choose)) $this->choose=$choose;	

			$exts=$this->extendtab;
			if(!empty($exts)){//拓展表选项
				$extendoption='';
				foreach($exts as $vo){
					if($vo[id]==$info['extendid'])
					$extendoption.='<option value="'.$vo['id'].'" selected="selected">'.$vo['name'].'</option>';
					else $extendoption.='<option value="'.$vo['id'].'">'.$vo['name'].'</option>';
				}
				$this->extendoption=$extendoption;
			}
			$this->id=$id;
			$this->info=$info;
			$this->oldparentid=$oldparentid;
			$this->display();
		}else{
			if(empty($_POST['sortname']) || empty($_POST['method']) || empty($_POST['tplist'])) $this->error('请填写完整栏目信息！');
			//数据处理
			$data=array();
			$newparentid=intval($_POST['parentid']);
			if($oldparentid!=$newparentid) $data=$this->sortedit($info['path'],$newparentid,$id,self::$sort[$type]['mark']);//分类编辑

			$data['name']=$_POST['sortname'];
			$data['keywords']=in($_POST['keywords']);
			$data['description']=in($_POST['description']);
			$data['url']=intval($_POST['num']);
			$data['method']=in($_POST['method']);
			$data['tplist']=$_POST['tplist'];
			$data['ifmenu']=intval($_POST['ifmenu']);
			$data['norder']=intval($_POST['norder']);
		    if($_POST['extendid']!=$_POST['oldextendid']){
				$nsort=$info['path'].','.$id;
			    $ifhas=model(self::$sort[$type]['mark'])->find("sort='$nsort'");
			    if(!empty($ifhas)) $this->alert('栏目下有信息，不能随意更换拓展表~');
			    else $data['extendid']=intval($_POST['extendid']);
			}
			//更新数据
			if(model('sort')->update("id = '$id'",$data)){
				$this->success('图集栏目修改成功',url('sort/index'));
			}
			else $this->error('图集栏目没有任何修改，不需要执行');
		}
	}

	//添加单页栏目
	public function pageadd()
	{
		$type=3;//单页类型
		if(!$this->isPost())
		{
			$list=model('sort')->select('','id,name,deep,path,norder');
			if(!empty($list)){
				$list=re_sort($list);
				$this->list=$list;
			}
			$choose=$this->tempchoose(self::$sort[$type]['mark'],'index');
            if(!empty($choose)) $this->choose=$choose;	

			$this->md=self::$sort[$type]['mark'];
			$this->url=url('sort');
			$this->display('sort_pageadd');
		}else{
			// print_r($_POST);exit();
			if(empty($_POST['sortname']) || empty($_POST['method'])||empty($_POST['content']) || empty($_POST['tplist'])) $this->error('请填写完整栏目信息！');
			$data=array();
			$parentid=intval($_POST['parentid']);
			$data=$this->sortadd($parentid);//分类添加
			$data['type']=$type;
			$data['name']=$_POST['sortname'];
			$data['keywords']=in($_POST['keywords']);
			$data['description']=in($_POST['description']);
			$data['method']=in($_POST['method']);
			$data['tplist']=$_POST['tplist'];
			$data['norder']=intval($_POST['norder']);
			$data['ifmenu']=intval($_POST['ifmenu']);
            if (empty($data['description'])) {
                   $data['description']=in(substr(deletehtml($_POST['content']), 0, 250)); //自动提取描述   
                }
                 if(empty($data['keywords'])){    
                     $data['keywords']= $this->getkeyword($data['name'],$data['description']); //自动获取中文关键词 
                     if(empty($data['keywords'])) $data['keywords']=str_replace(' ',',',$data['description']);//非中文
                 }
			$data1=array();
			if (get_magic_quotes_gpc()) {
				$data1['content'] = stripslashes($_POST['content']);
			} else {
				$data1['content'] = $_POST['content'];
			}
			$data1['edittime']=in($_POST['edittime']);
			//插入数据
			$newid=model('sort')->insert($data);
			if($newid){
				$data1['sort']=$data['path'].','.$newid;
				if(model('page')->insert($data1))
				$this->success('单页添加成功~',url('sort/index'));
			}
			else $this->error('单页添加失败~');
		}
	}
	//编辑单页栏目
	public function pageedit()
	{
		$type=3;//单页类型
		$id=intval($_GET['id']);
		if(empty($id)) $this->error('空的类别参数');
		$info=model('sort')->find("id='$id'",'name,norder,path,ifmenu,method,tplist,keywords,description');
		$oldparentid=intval(substr ($info['path'], -6));
		$oldsort=$info['path'].','.$id;//单页sort字段
		if(!$this->isPost())
		{
			$list=model('sort')->select('','id,name,deep,path,norder');
			if(!empty($list)){
				$list=re_sort($list);
				$this->list=$list;
			}
			$tpdef=explode('_',$info['tplist']);
			if(!isset($tpdef[1])) $this->error('非法的模板参数~');
			$choose=$this->tempchoose(self::$sort[$type]['mark'],$tpdef[1]);
            if(!empty($choose)) $this->choose=$choose;	

			$info1=model('page')->find("sort='$oldsort'");
			$this->id=$id;
			$this->info=$info;//栏目信息
			$this->info1=$info1;//单页信息
			$this->oldparentid=$oldparentid;
			$this->display();
		}else{
            $pageid=intval($_GET['pageid']);
		    if(empty($pageid)) $this->error('空的单页id参数');
			if(empty($_POST['sortname']) || empty($_POST['method'])||empty($_POST['content']) || empty($_POST['tplist'])) $this->error('请填写完整的栏目信息！');
			//数据处理
			$data=array();
			$data1=array();
            $newparentid=intval($_POST['parentid']);
			if($oldparentid!=$newparentid){
				$data=$this->sortedit($info['path'],$newparentid,$id);//分类编辑
                $data1['sort']=$data['path'].','.$id;
			}
			$data['name']=$_POST['sortname'];
			$data['keywords']=in($_POST['keywords']);
			$data['description']=in($_POST['description']);
			$data['method']=in($_POST['method']);
			$data['tplist']=$_POST['tplist'];
			$data['ifmenu']=intval($_POST['ifmenu']);
			$data['norder']=intval($_POST['norder']);
            if (empty($data['description'])) {
                $data['description']=in(substr(deletehtml($_POST['content']), 0, 250)); //自动提取描述   
            }
            if(empty($data['keywords'])){    
                $data['keywords']= $this->getkeyword($data['name'],$data['description']); //自动获取中文关键词 
                if(empty($data['keywords'])) $data['keywords']=str_replace(' ',',',$data['description']);//非中文
            }
			
			if (get_magic_quotes_gpc()) {
				$data1['content'] = stripslashes($_POST['content']);
			} else {
				$data1['content'] = $_POST['content'];
			}
			$data1['edittime']=in($_POST['edittime']);
			if(model('page')->update("id = '$pageid'",$data1) && model('sort')->update("id = '$id'",$data))
			$this->success('单页修改成功',url('sort/index'));
			else $this->error('单页没有任何修改，不需要执行');
		}
	}
	//添加应用栏目
	public function pluginadd()
	{
		$type=4;//插件类型
		if(!$this->isPost())
		{
			$list=model('sort')->select('','id,name,deep,path,norder');
			if(!empty($list)){
				$list=re_sort($list);
				$this->list=$list;
			}
				$plugs=api(getApps(),'getdefaultMenu');//已开启的应用列表
				if(!empty($plugs)){
					$choose='<option value="">=选择已安装的应用=</option>';
				   foreach ($plugs as $vo){
					   if(!empty($vo))
					       $choose.='<option value="'.$vo['r'].'">'.$vo['name'].'</option>';
				    }
				    $this->choose=$choose;
				}

			$this->url=url('sort');
			$this->display('sort_pluginadd');
		}else{
			if(empty($_POST['sortname']) || empty($_POST['method'])) $this->error('请填写完整栏目信息！');
			$data=array();
			$parentid=intval($_POST['parentid']);
			$data=$this->sortadd($parentid);//分类添加
			$data['type']=$type;
			$data['name']=$_POST['sortname'];
			$data['keywords']=in($_POST['keywords']);
			$data['description']=in($_POST['description']);
			$data['method']=in($_POST['method']);
			$data['tplist']=$_POST['tplist'];
			$data['norder']=intval($_POST['norder']);
			$data['ifmenu']=intval($_POST['ifmenu']);
			//插入数据
			if(model('sort')->insert($data)){
				$this->success('插件栏目添加成功~',url('sort/index'));
			}
			else $this->error('插件栏目添加失败~');
		}
	}

	//编辑应用栏目
	public function pluginedit()
	{
		$type=4;//插件类型
		$id=intval($_GET['id']);
		if(empty($id)) $this->error('空的类别参数');
		$info=model('sort')->find("id='$id'",'name,norder,path,ifmenu,method,tplist,keywords,description');
		$oldparentid=intval(substr ($info['path'], -6));
		if(!$this->isPost())
		{
			$list=model('sort')->select('','id,name,deep,path,norder');
			if(!empty($list)){
				$list=re_sort($list);
				$this->list=$list;
			}
            $plugs=api(getApps(),'getdefaultMenu');//已开启的应用列表
				if(!empty($plugs)){
				   foreach ($plugs as $vo){
					   if(!empty($vo))
					   	if($vo['r']==$info['method']) $choose.='<option selected="selected" value="'.$vo['r'].'">'.$vo['name'].'</option>';
					    else $choose.='<option value="'.$vo['r'].'">'.$vo['name'].'</option>';
				    }
				    $this->choose=$choose;
				}
			$this->id=$id;
			$this->info=$info;
			$this->oldparentid=$oldparentid;
			$this->display();
		}else{
			if(empty($_POST['sortname']) || empty($_POST['method'])) $this->error('请填写完整栏目信息！');
			//数据处理
			$data=array();
			$newparentid=intval($_POST['parentid']);
			if($oldparentid!=$newparentid) $data=$this->sortedit($info['path'],$newparentid,$id);//分类编辑

			$data['type']=$type;
			$data['name']=$_POST['sortname'];
			$data['keywords']=in($_POST['keywords']);
			$data['description']=in($_POST['description']);
			$data['method']=in($_POST['method']);
			$data['tplist']=$_POST['tplist'];
			$data['ifmenu']=intval($_POST['ifmenu']);
			$data['norder']=intval($_POST['norder']);

			
			//更新数据
			if(model('sort')->update("id = '$id'",$data)){
				$this->success('插件栏目修改成功',url('sort/index'));
			}
			else $this->error('插件栏目没有任何修改，不需要执行');
		}
	}
    //添加自定义栏目
	public function linkadd()
	{
		$type=5;//栏目类型
		if(!$this->isPost())
		{
			$list=model('sort')->select('','id,name,deep,path,norder');
			if(!empty($list)){
				$list=re_sort($list);
				$this->list=$list;
			}
			$this->url=url('sort');
			$this->display('sort_linkadd');
		}else{
			if(empty($_POST['sortname']) || empty($_POST['url'])) $this->error('请填写完整栏目信息！');
			$data=array();
			$parentid=intval($_POST['parentid']);
			$data=$this->sortadd($parentid);//分类添加
			$data['type']=$type;
			$data['name']=$_POST['sortname'];
			$data['extendid']=intval($_POST['ifout']);
			$data['url']=$_POST['url'];
			$data['norder']=intval($_POST['norder']);
			$data['ifmenu']=intval($_POST['ifmenu']);
			//插入数据
			if(model('sort')->insert($data)){
				$this->success('外链栏目添加成功~',url('sort/index'));
			}
			else $this->error('外链栏目添加失败~');
		}
	}

	//编辑自定义栏目
	public function linkedit()
	{
		$type=5;//栏目类型
		$id=intval($_GET['id']);
		if(empty($id)) $this->error('空的类别参数');
		$info=model('sort')->find("id='$id'",'name,norder,path,ifmenu,url,extendid');
		$oldparentid=intval(substr ($info['path'], -6));
		if(!$this->isPost())
		{
			$list=model('sort')->select('','id,name,deep,path,norder');
			if(!empty($list)){
				$list=re_sort($list);
				$this->list=$list;
			}
			$this->id=$id;
			$this->info=$info;
			$this->oldparentid=$oldparentid;
			$this->display();
		}else{
			if(empty($_POST['sortname']) || empty($_POST['url'])) $this->error('请填写完整栏目信息！');
			//数据处理
			$data=array();
			$newparentid=intval($_POST['parentid']);
			if($oldparentid!=$newparentid) $data=$this->sortedit($info['path'],$newparentid,$id);//分类编辑

			$data['type']=$type;
			$data['name']=$_POST['sortname'];
			$data['extendid']=intval($_POST['ifout']);
			$data['url']=$_POST['url'];
			$data['ifmenu']=intval($_POST['ifmenu']);
			$data['norder']=intval($_POST['norder']);

			//更新数据
			if(model('sort')->update("id = '$id'",$data)){
				$this->success('外链栏目修改成功',url('sort/index'));
			}
			else $this->error('外链栏目没有任何修改，不需要执行');
		}
	 }
	 //添加表单
	public function extendadd()
	{
		$type=6;//栏目类型
		if(!$this->isPost())
		{
			$list=model('sort')->select('','id,name,deep,path,norder');
			if(!empty($list)){
				$list=re_sort($list);
				$this->list=$list;
			}
			$choose=$this->tempchoose(self::$sort[$type]['mark'],'index');
            if(!empty($choose)) $this->choose=$choose;	
            $this->md=self::$sort[$type]['mark'];

			$forminfo = model('extend')->select("type='1' AND pid='0'",'id,name');
			$this->forminfo=$forminfo;
			$this->url=url('sort');
			$this->display('sort_extendadd');
		}else{
			if(empty($_POST['sortname']) || empty($_POST['formid'])) $this->error('请填写完整栏目信息！');
			$data=array();
			$parentid=intval($_POST['parentid']);
			$data=$this->sortadd($parentid);//分类添加
			$data['type']=$type;
			$data['name']=$_POST['sortname'];
			$data['keywords']=in($_POST['keywords']);
			$data['description']=in($_POST['description']);
			$data['extendid']=intval($_POST['formid']);
			$data['url']=intval($_POST['num']);
			$data['method']=in($_POST['method']);
			$data['tplist']=$_POST['tplist'];
			$data['norder']=intval($_POST['norder']);
			$data['ifmenu']=intval($_POST['ifmenu']);
			//插入数据
			if(model('sort')->insert($data)){
				$this->success('表单栏目添加成功~',url('sort/index'));
			}
			else $this->error('表单栏目添加失败~');
		}
	}

	//编辑表单
	public function extendedit()
	{
		$type=6;//栏目类型
		$id=intval($_GET['id']);
		if(empty($id)) $this->error('空的类别参数');
		$info=model('sort')->find("id='$id'",'name,keywords,description,norder,path,ifmenu,url,method,tplist,extendid');
		$oldparentid=intval(substr ($info['path'], -6));
		if(!$this->isPost())
		{
			$list=model('sort')->select('','id,name,deep,path,norder');
			if(!empty($list)){
				$list=re_sort($list);
				$this->list=$list;
			}
			$tpdef=explode('_',$info['tplist']);
			if(!isset($tpdef[1])) $this->error('非法的模板参数~');
			$choose=$this->tempchoose(self::$sort[$type]['mark'],$tpdef[1]);
            if(!empty($choose)) $this->choose=$choose;	

			$forminfo = model('extend')->select("type='1' AND pid='0'",'id,name');
			$this->forminfo=$forminfo;
			$this->id=$id;
			$this->info=$info;
			$this->oldparentid=$oldparentid;
			$this->display();
		}else{
			if(empty($_POST['sortname']) || empty($_POST['formid'])) $this->error('请填写完整栏目信息！');
			//数据处理
			$data=array();
			$newparentid=intval($_POST['parentid']);
			if($oldparentid!=$newparentid) $data=$this->sortedit($info['path'],$newparentid,$id);//分类编辑

			$data['type']=$type;
			$data['name']=$_POST['sortname'];
			$data['keywords']=in($_POST['keywords']);
			$data['description']=in($_POST['description']);
			$data['extendid']=intval($_POST['formid']);
			$data['url']=intval($_POST['num']);
			$data['method']=in($_POST['method']);
			$data['tplist']=$_POST['tplist'];
			$data['ifmenu']=intval($_POST['ifmenu']);
			$data['norder']=intval($_POST['norder']);

			//更新数据
			if(model('sort')->update("id = '$id'",$data)){
				$this->success('表单栏目修改成功',url('sort/index'));
			}
			else $this->error('表单栏目没有任何修改，不需要执行');
		}
	 }
	//删除栏目
	public function del()
	{
		$type=intval($_GET['type']);
		$id=intval($_GET['id']);
		if(empty($id)||empty($type))
		{
			$this->error('错误的参数~');
		}
		$condition['id']=$id;
		$target=model('sort')->find($condition,'path');
		$where='path = \''.$target['path'].','.$id.'\'';
		if(model('sort')->find($where))
		$this->error('请先删除该类下的子分类~');
		else{
			//判断类下有无内容
			$table=self::$sort[$type]['mark'];
			if(empty($table)) $this->error('没有该类别');
			if($table!='plugin' && $table!='link'&& $table!='extend'){//插件栏目不用做以下操作
				$info=model($table)->find('sort = \''.$target['path'].','.$id.'\'','id');
				if($info){
					$delid=$info['id'];
					if(!in_array($table,array('page'))) $this->error('请先删除属于该类下的内容~');//一栏目对多信息情况
					elseif(!model($table)->delete("id='{$delid}'")) $this->error('删除栏目下信息失败~');//一栏目对一信息情况
				}
			}

			if(model('sort')->delete($condition))
			$this->success('栏目删除成功',url('sort/index'));
		}
	}
	//单页编辑器上传
	public function PageUploadJson(){
		$this->EditUploadJson('pages');
	}
	//单页编辑器文件管理
	public function PageFileManagerJson(){
		$this->EditFileManagerJson('pages');
	}
}
?>