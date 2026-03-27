<?php
class photoController extends commonController
{
	static protected $sorttype;//图集分类
	static protected $uploadpath='';//图片上传路径
	public function __construct()
	{
		parent::__construct();
		$this->uploadpath=ROOT_PATH.'upload/photos/';
		$this->sorttype=2;
	}
	//列表
	public function index()
	{
		$listRows=20;//每页显示的信息条数
		$url=url('photo/index',array('page'=>'{page}'));
		$sortlist=model('sort')->select('','id,name,deep,path,norder,type');
		if(!empty($sortlist)){
			$sortlist=re_sort($sortlist);
			$sortname=array();
			//栏目选项
			foreach($sortlist as $vo){
                $space = str_repeat('├┈', $vo['deep']-1);
                $sortnow=$vo['path'].','.$vo['id'];
                $selected=($sort==$sortnow)?'selected="selected"':'';   
                $disable=($this->sorttype==$vo['type'])?'':'disabled="disabled"'; 
                $option.= '<option '.$selected.' value="'.$sortnow.'" '.$disable.'>'.$space.$vo ['name'].'</option>';

                $sortname[$vo['id']]=$vo['name'];
            }
            $this->option=$option;
            $this->sortname=$sortname;
		}

		//类别条件
		$sort=$_GET['sort'];
		if($sort){
			$url=url('photo/index',array('sort'=>$sort,'page'=>'{page}'));
			$this->sort=$sort;
		}
		//搜索条件
		$keyword=in(urldecode(trim($_GET['keyword'])));
		if(!empty($keyword)){
			$url=url('photo/index',array('keyword'=>urlencode($keyword),'page'=>'{page}'));
			$this->keyword=$keyword;
		}
		$limit=$this->pageLimit($url,$listRows);
		$count=model('photo')->photocount($sort,$keyword);
        $list=model('photo')->photoANDadmin($sort,$keyword,$limit);
		$this->list= $list;
		$this->count= $count;
		$this->page=$this->pageShow($count);
		$this->url=url('photo');
		$this->display();
	}

	//添加
	public function add()
	{
		if(!$this->isPost()){
			$sortlist=model('sort')->select('','id,name,deep,path,norder,type');
			if(empty($sortlist)) $this->error('请先添加图集栏目~',url('sort/photoadd'));
			$sortlist=re_sort($sortlist);

			$choose=$this->tempchoose('photo','content');
            if(!empty($choose)) $this->choose=$choose;	

			$this->choose=$choose;
			$this->sortlist=$sortlist;
			$this->type=$this->sorttype;
			$this->twidth=config('thumbMaxwidth');
			$this->theight=config('thumbMaxheight');
			$this->picpath=__ROOT__.'/upload/photos/';
			$this->display();
		}else{
			if(empty($_POST['sort'])||empty($_POST['title'])||empty($_POST['content']))
			$this->error('请填写完整的信息~');
			$data=array();
			//扩展模型开始
			if (!empty($_POST['tableid'])) {
				$tableid = intval($_POST['tableid']);
				$info = model('extend')->find("id='{$tableid}'",'tableinfo'); //查询表
				$list = model('extend')->select("pid='{$tableid}'",'','id desc'); //查询表中字段
				foreach ($list as $vo) {
					if (!empty($vo['tableinfo'])) {
						if(is_array($_POST['ext_'.$vo['tableinfo']]))
							$fvalue=implode(',',$_POST['ext_'.$vo['tableinfo']]);
						else
						    $fvalue=in($_POST['ext_'.$vo['tableinfo']]);
						$ex_data[$vo['tableinfo']] = empty($fvalue)?$vo['defvalue']:$fvalue; //循环post字段
					}
				}
				$extfield=model('extend')->Extin($info['tableinfo'],$ex_data);
				$data['extfield']=$extfield;
			}
			//扩展模型结束
			$data['account']=$_SESSION['admin_username'];
			$data['sort']=$_POST['sort'];
			$data['title']=in($_POST['title']);
			$data['color']=$_POST['color'];
			$data['keywords']=in($_POST['keywords']);
			$data['picture']=$_POST['picture'];
			$data['description']=in($_POST['description']);
			$data['content']=in($_POST['content']);
			$data['method']=in($_POST['method']);
			$data['tpcontent']=in($_POST['tpcontent']);
			$data['ispass']=intval($_POST['ispass']);
			$data['recmd']=intval($_POST['recmd']);
			$data['hits']=intval(in($_POST['hits']));
			$data['norder']=intval(in($_POST['norder']));
			$data['addtime']=strtotime(in($_POST['addtime']));
            if (empty($data['description'])) {
                 $data['description']=in(substr(deletehtml($_POST['content']), 0, 250)); //自动提取描述   
            }
            if(empty($data['keywords'])){    
                $data['keywords']= $this->getkeyword($data['title'],$data['description']); //自动获取中文关键词 
                if(empty($data['keywords'])) $data['keywords']=str_replace(' ',',',$data['description']);//非中文
            }
			if(!empty($_POST['photolist']))
			$data['photolist']=implode(',',$_POST['photolist']);
			if(!empty($_POST['conlist']))
			$data['conlist']=implode(',',in($_POST['conlist']));
			if(model('photo')->insert($data))
			$this->success('图集添加成功~',url('photo/index'));
			else $this->error('图集添加失败');
		}
	}

	//编辑
	public function edit()
	{
		$id=intval($_GET['id']);
		if(empty($id)) $this->error('参数错误');
		if(!$this->isPost()){
			$sortlist=model('sort')->select('','id,name,deep,path,norder,type');
			if(empty($sortlist)) $this->error('图集分类被清空了~');
			$sortlist=re_sort($sortlist);
			$info=model('photo')->find("id='$id'");
			$info['addtime']=date("Y-m-d H:i:s",$info['addtime']);
			
			$tpdef=explode('_',$info['tpcontent']);
			if(!isset($tpdef[1])) $this->error('非法的模板参数~');
			$choose=$this->tempchoose('photo',$tpdef[1]);
            if(!empty($choose)) $this->choose=$choose;	

			$this->choose=$choose;
			$this->info=$info;
			$this->type=$this->sorttype;
			$this->twidth=config('thumbMaxwidth');
			$this->theight=config('thumbMaxheight');
			$this->sortlist=$sortlist;
			$this->picpath=__ROOT__.'/upload/photos/';
			$this->display();
		}else{
			if(empty($_POST['sort'])||empty($_POST['title'])||empty($_POST['content']))
			$this->error('请填写完整的信息~');
			$data=array();
			//扩展模型编辑
			if (!empty($_POST['tableid'])) {
				$tableid = intval($_POST['tableid']);
				$info = model('extend')->find("id='{$tableid}'",'tableinfo'); //查询表
				$list = model('extend')->select("pid='{$tableid}'",'','id desc'); //查询表中字段
				foreach ($list as $vo) {
					if (!empty($vo['tableinfo'])) {
						if(is_array($_POST['ext_'.$vo['tableinfo']]))
							$fvalue=implode(',',$_POST['ext_'.$vo['tableinfo']]);
						else
						    $fvalue=in($_POST['ext_'.$vo['tableinfo']]);
						$ex_data[$vo['tableinfo']] = empty($fvalue)?$vo['defvalue']:$fvalue; //循环post字段
					}
				}
			}
			$extmesid=intval($_POST['extfield']);
			if($_POST['sort']==$_POST['oldsort']){//信息没有更换分类
			  if(isset($ex_data) && $extmesid)
				 model('extend')->Extup($info['tableinfo'],"id='{$extmesid}'",$ex_data);//更新拓展数据
			}else{//信息更换了分类
				    if($extmesid){//原分类使用了拓展表
				        $oldsid=substr($_POST['oldsort'],-6,6);
				        $oldexid=model('sort')->find("id='{$oldsid}'",'extendid');
				        $oldtable=model('extend')->find("id='{$oldexid['extendid']}'",'tableinfo');
				        model('extend')->Extdel($oldtable['tableinfo'],"id='$extmesid'");//删除旧拓展表中关联项目
				    }
				    if(isset($ex_data)){//新栏目也试用拓展表
				        $extfield=model('extend')->Extin($info['tableinfo'],$ex_data);//插入新拓展数据
				        $data['extfield']=$extfield;
				    }else $data['extfield']=0;
			}
			//扩展模型编辑结束
			$data['account']=$_SESSION['admin_username'];
			$data['sort']=$_POST['sort'];
			$data['title']=in($_POST['title']);
			$data['color']=$_POST['color'];
			$data['keywords']=in($_POST['keywords']);
			$data['picture']=$_POST['picture'];
			$data['description']=in($_POST['description']);
			$data['content']=in($_POST['content']);
			$data['method']=in($_POST['method']);
			$data['tpcontent']=in($_POST['tpcontent']);
			$data['ispass']=intval($_POST['ispass']);
			$data['recmd']=intval($_POST['recmd']);
			$data['hits']=intval(in($_POST['hits']));
			$data['norder']=intval(in($_POST['norder']));
			$data['addtime']=strtotime(in($_POST['addtime']));
            if (empty($data['description'])) {
                $data['description']=in(substr(deletehtml($_POST['content']), 0, 250)); //自动提取描述   
            }
            if(empty($data['keywords'])){    
                $data['keywords']= $this->getkeyword($data['title'],$data['description']); //自动获取中文关键词 
                if(empty($data['keywords'])) $data['keywords']=str_replace(' ',',',$data['description']);//非中文
            }
			if(!empty($_POST['photolist']))
			$data['photolist']=implode(',',$_POST['photolist']);
			else $data['photolist']='';
			if(!empty($_POST['conlist']))
			$data['conlist']=implode(',',in($_POST['conlist']));
			else $data['conlist']='';

			if(model('photo')->update("id='$id'",$data))
			$this->success('图集编辑成功~',url('photo/index'));
			else $this->error('没有信息被修改 ~');
		}
	}
	//ajax拓展字段获取
	public function ex_field(){
		$this->extend_field();
	}
	//图片上传,ajax方式使用
	public function images_upload()
	{
		$this->AjaxUpload('photos',true);
	}
	//单图删除,ajax方式使用
	public function delpic()
	{
		if(empty($_POST['picname'])) $this->error('参数错误~');
		$picname=$_POST['picname'];
		$path=$this->uploadpath;
		if(file_exists($path.$picname))
		  @unlink($path.$picname);
		else{echo '图片不存在~';return;} 
		if(file_exists($path.'thumb_'.$picname))
		   @unlink($path.'thumb_'.$picname);
		else {echo '缩略图不存在~';return;}
		echo '原图以及缩略图删除成功~';
	}
	//图集删除
	public function del()
	{
		$path=$this->uploadpath;
		if(!$this->isPost()){
			$id=intval($_GET['id']);
			if(empty($id)) $this->error('您没有勾选~');
			else{
				$photos=model('photo')->find("id='$id'",'photolist,sort,extfield');
				$sortid=substr($photos['sort'],-6,6);
				$exid=model('sort')->find("id='{$sortid}'",'extendid');
				if($exid['extendid']!=0){
					$table=model('extend')->find("id='{$exid['extendid']}'",'tableinfo');
					if(!($this->model->table($table['tableinfo'])->where("id='{$photos['extfield']}'")->delete())){//删除拓展表中关联信息
						echo '删除拓展信息失败~';
						return;
					}
				}
				if(!empty($photos['photolist'])){
					$phoarr=explode(',',$photos['photolist']);
					foreach ($phoarr as $vo){
						if(file_exists($path.$vo))
						@unlink($path.$vo);
						if(file_exists($path.'thumb_'.$vo))
						@unlink($path.'thumb_'.$vo);
					}
				}
				if(model('photo')->delete("id='$id'"))
				echo 1;
				else echo '删除失败~';
			}
		}else{
			if('del'!=$_POST['dotype']) $this->error('操作类型错误~');
			if(empty($_POST['delid'])) $this->error('您没有选择~');
			$delid=implode(',',$_POST['delid']);
			$photos=model('photo')->select('id in ('.$delid.')','photolist,sort,extfield');
			foreach ($photos as $plist){
			    $sortid=substr($plist['sort'],-6,6);
				$exid=model('sort')->find("id='{$sortid}'",'extendid');
				if($exid['extendid']!=0){
					$table=model('extend')->find("id='{$exid['extendid']}'",'tableinfo');
					if(!($this->model->table($table['tableinfo'])->where("id='{$plist['extfield']}'")->delete()))//删除拓展表中关联信息
					$this->error('删除ID为'.$info['extfield'].'的拓展信息失败~');
				}
				if(!empty($plist[photolist])){
					$phoarr=explode(',',$plist[photolist]);
					foreach ($phoarr as $vo){
						if(file_exists($path.$vo))
						@unlink($path.$vo);
						if(file_exists($path.'thumb_'.$vo))
						@unlink($path.'thumb_'.$vo);
					}
				}
			}
			if(model('photo')->delete('id in ('.$delid.')'))
			$this->success('删除成功',url('photo/index'));
			else $this->error('删除失败');
		}
	}
	public function colchange()
	{
		 if('change'!=$_POST['dotype']) $this->error('操作类型错误~');
         if(empty($_POST['delid'])||empty($_POST['col'])) $this->error('您没有选择~');
		 $changeid=implode(',',$_POST['delid']);
		 $data['sort']=$_POST['col'];
		 if(model('photo')->update('id in ('.$changeid.')',$data)) $this->success('栏目移动成功~',url('news/index'));
		 else $this->error('栏目移动失败~');
	}
	//编辑器上传
	public function UploadJson(){
		$this->EditUploadJson('photos');
	}
	//编辑器文件管理
	public function FileManagerJson(){
		$this->EditFileManagerJson('photos');
	}
	//审核,ajax
	public function lock()
	{
		$id=intval($_POST['id']);
		$lock['ispass']=intval($_POST['ispass']);
		if(model('photo')->update("id='{$id}'",$lock))
		echo 1;
		else echo '操作失败~';
	}
	//推荐，ajax
	public function recmd()
	{
		$id=intval($_POST['id']);
		$recmd['recmd']=intval($_POST['recmd']);
		if(model('photo')->update("id='{$id}'",$recmd))
		echo 1;
		else echo '操作失败~';
	}
}