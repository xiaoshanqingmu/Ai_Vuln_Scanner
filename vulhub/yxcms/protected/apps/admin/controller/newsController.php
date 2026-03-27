<?php
class newsController extends commonController
{
	static protected $sorttype;//资讯分类
	static protected $uploadpath='';//封面图路径
    static public $nopic='';//前台模板路径
	public function __construct()
	{
		parent::__construct();
		$this->uploadpath=ROOT_PATH.'upload/news/image/';
        $this->nopic='NoPic.gif';//默认封面
		$this->sorttype=1;
	}
	//列表
	public function index()
	{
		$listRows=20;//每页显示的信息条数
		$url=url('news/index',array('page'=>'{page}'));
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
			$url=url('news/index',array('sort'=>$sort,'page'=>'{page}'));
			$this->sort=$sort;
		}
		//搜索条件
		$keyword=in(urldecode(trim($_GET['keyword'])));
		if(!empty($keyword)){
			$url=url('news/index',array('keyword'=>urlencode($keyword),'page'=>'{page}'));
			$this->keyword=$keyword;
		}
		
		$limit=$this->pageLimit($url,$listRows);
		$count=model('news')->newscount($sort,$keyword);
        $list=model('news')->newsANDadmin($sort,$keyword,$limit);

		$this->list=$list;
		$this->count=$count;
		$this->page=$this->pageShow($count);
		$this->display();
	}

	//添加
	public function add()
	{
		if(!$this->isPost()){
			$sortlist=model('sort')->select('','id,name,deep,path,norder,type');
			if(empty($sortlist))  $this->error('请先添加文章栏目~',url('sort/newsadd'));
			$sortlist=re_sort($sortlist);
			$choose=$this->tempchoose('news','content');
            if(!empty($choose)) $this->choose=$choose;
			
            $this->sortlist=$sortlist;
			$this->type=$this->sorttype;
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
			$data['from']=empty($_POST['from'])?'原创':in($_POST['from']);
			$data['keywords']=in($_POST['keywords']);
			$data['description']=in($_POST['description']);
			if (get_magic_quotes_gpc()) {
				$data['content'] = stripslashes($_POST['content']);
			} else {
				$data['content'] = $_POST['content'];
			}
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
			if (empty($_FILES['picture']['name']) === false){
                $tfile=date("Ymd");
				$imgupload= $this->upload($this->uploadpath.$tfile.'/',config('imgupSize'),'jpg,bmp,gif,png');
                $imgupload->saveRule='thumb_'.time();
				$imgupload->upload();
				$fileinfo=$imgupload->getUploadFileInfo();
				$errorinfo=$imgupload->getErrorMsg();
				if(!empty($errorinfo)){ 
                                    $data['picture']=$this->nopic;
                                    $this->alert($errorinfo);
                                    }
				else $data['picture']=$tfile.'/'.$fileinfo[0]['savename'];
			}else{        
                $firstpath=in($this->onepic(html_out($data['content'])));
                if(!empty($firstpath)){
                    $lastlocation=strrpos($firstpath,'/');
                    $timefile=substr($firstpath,$lastlocation-8,8);
                    $covername=substr($firstpath,$lastlocation+1);
                    if(file_exists($this->uploadpath.$timefile.'/'.$covername)){
                        @copy($this->uploadpath.$timefile.'/'.$covername, $this->uploadpath.$timefile.'/thumb_'.$covername);//复制第一张图片为缩略图 
                         $data['picture']= $timefile.'/thumb_'.$covername;  
                    } 
                    else   $data['picture']=$this->nopic;  
                }else   $data['picture']=$this->nopic;                       
            }
			if(model('news')->insert($data))
			$this->success('资讯添加成功~',url('news/index'));
			else $this->error('资讯添加失败');
		}
	}

	//编辑
	public function edit()
	{
		$id=intval($_GET['id']);
		if(empty($id)) $this->error('参数错误');
		if(!$this->isPost()){
			$sortlist=model('sort')->select('','id,name,deep,path,norder,type');
			if(empty($sortlist)) $this->error('资讯分类被清空了');
			$sortlist=re_sort($sortlist);
			$info=model('news')->find("id='$id'");
			$info['addtime']=date("Y-m-d H:i:s",$info['addtime']);
			
			$tpdef=explode('_',$info['tpcontent']);
			if(!isset($tpdef[1])) $this->error('非法的模板参数~');
			$choose=$this->tempchoose('news',$tpdef[1]);
            if(!empty($choose)) $this->choose=$choose;	

			$this->info=$info;
	        $this->type=$this->sorttype;
            $this->sortlist=$sortlist;
            $this->twidth=config('coverMaxwidth');
			$this->theight=config('coverMaxheight');
			$this->path=__ROOT__.'/upload/news/image/';
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
			//$data['account']=$_SESSION['admin_username'];
			$data['sort']=$_POST['sort'];
			$data['title']=in($_POST['title']);
			$data['color']=$_POST['color'];
			$data['from']=empty($_POST['from'])?'原创':in($_POST['from']);
			$data['keywords']=in($_POST['keywords']);
			$data['description']=in($_POST['description']);
			if (get_magic_quotes_gpc()) {
				$data['content'] = stripslashes($_POST['content']);
			} else {
				$data['content'] = $_POST['content'];
			}
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
			if (empty($_FILES['picture']['name']) === false){
                $tfile=date("Ymd");
				$imgupload= $this->upload($this->uploadpath.$tfile.'/',config('imgupSize'),'jpg,bmp,gif,png');
                $imgupload->saveRule='thumb_'.time();
				if(!empty($_POST['oldpicture']) && $_POST['oldpicture']!=$this->nopic){
					$picpath=$this->uploadpath.$_POST['oldpicture'];
					if(file_exists($picpath)) @unlink($picpath);
				}
				$imgupload->upload();
				$fileinfo=$imgupload->getUploadFileInfo();
				$errorinfo=$imgupload->getErrorMsg();
				if(!empty($errorinfo)) $this->alert($errorinfo);
				$data['picture']=$tfile.'/'.$fileinfo[0]['savename'];
			}else{
                 if(empty($_POST['oldpicture']) || $_POST['oldpicture']==$this->nopic){                              
                     $firstpath=in($this->onepic(html_out($data['content'])));
                    if(!empty($firstpath)){
                        $lastlocation=strrpos($firstpath,'/');
                        $timefile=substr($firstpath,$lastlocation-8,8);
                        $covername=substr($firstpath,$lastlocation+1);
                        if(file_exists($this->uploadpath.$timefile.'/'.$covername)){
                            @copy($this->uploadpath.$timefile.'/'.$covername, $this->uploadpath.$timefile.'/thumb_'.$covername);//复制第一张图片为缩略图 
                            $data['picture']= $timefile.'/thumb_'.$covername;  
                        }
                    }
                }
            }
			if(model('news')->update("id='$id'",$data))
			$this->success('资讯编辑成功~',url('news/index'));
			else $this->error('没有信息被修改 ~');
		}
	}

	//删除
	public function del()
	{
		if(!$this->isPost()){
			$id=intval($_GET['id']);
			if(empty($id)) $this->error('您没有选择~');
			$info=model('news')->find("id='$id'",'sort,picture,extfield');

			$sortid=substr($info['sort'],-6,6);
			$exid=model('sort')->find("id='{$sortid}'",'extendid');
			if($exid['extendid']!=0){
				$table=model('extend')->find("id='{$exid['extendid']}'",'tableinfo');
				if(!(model('extend')->Extdel($table['tableinfo'],"id='{$info['extfield']}'"))){//删除拓展表中关联信息
					echo '删除拓展信息失败~';
					return;
				}
			}
			if(!empty($info[picture])){
				$picpath=$this->uploadpath.$info[picture];
				if(file_exists($picpath)) @unlink($picpath);
			}
			if(model('news')->delete("id='$id'"))
			echo 1;
			else echo '删除失败~';
		}else{
			if('del'!=$_POST['dotype']) $this->error('操作类型错误~');
			if(empty($_POST['delid'])) $this->error('您没有选择~');
			$delid=implode(',',$_POST['delid']);
			$list=model('news')->select('id in ('.$delid.')','sort,picture,extfield');
			foreach($list as $vo){
				$sortid=substr($vo['sort'],-6,6);
				$exid=model('sort')->find("id='{$sortid}'",'extendid');
				if($exid['extendid']!=0){
					$table=model('extend')->find("id='{$exid['extendid']}'",'tableinfo');
					if(!(model('extend')->Extdel($table['tableinfo'],"id='{$vo['extfield']}'")))//删除拓展表中关联信息
					   $this->error('删除ID为'.$info['extfield'].'的拓展信息失败~');
				}
				
				if(!empty($vo[picture])){
					$picpath=$this->uploadpath.$vo[picture];
					if(file_exists($picpath)) @unlink($picpath);
				}
			}
			if(model('news')->delete('id in ('.$delid.')'))
			$this->success('删除成功',url('news/index'));
		}
	}
	public function colchange()
	{
		 if('change'!=$_POST['dotype']) $this->error('操作类型错误~');
         if(empty($_POST['delid'])||empty($_POST['col'])) $this->error('您没有选择~');
		 $changeid=implode(',',$_POST['delid']);
		 $data['sort']=$_POST['col'];
		 if(model('news')->update('id in ('.$changeid.')',$data)) $this->success('栏目移动成功~',url('news/index'));
		 else $this->error('栏目移动失败~');
	}

	//审核,ajax
	public function lock()
	{
		$id=intval($_POST['id']);
		$lock['ispass']=intval($_POST['ispass']);
		if(model('news')->update("id='{$id}'",$lock))
		echo 1;
		else echo '操作失败~';
	}

	//推荐，ajax
	public function recmd()
	{
		$id=intval($_POST['id']);
		$recmd['recmd']=intval($_POST['recmd']);
		if(model('news')->update("id='{$id}'",$recmd))
		echo 1;
		else echo '操作失败~';
	}
	//编辑器上传
	public function UploadJson(){
		$this->EditUploadJson('news');
	}
	//编辑器文件管理
	public function FileManagerJson(){
		$this->EditFileManagerJson('news');
	}
	//ajax拓展字段获取
	public function ex_field(){
		$this->extend_field();
	}
	//封面图剪切
	public function cutcover()
	{
		//文件保存目录
		$picname=($_POST['name']);
		$thumb_image_location=$large_image_location=$this->uploadpath.$picname;
		$thumb_width=intval($_POST["thumb_w"]);//剪切后图片宽度
		$x1 = intval($_POST["x1"]);
		$y1 = intval($_POST["y1"]);
		$w =intval($_POST["w"]);
		$h = intval($_POST["h"]);
		if(empty($thumb_width)||empty($x1)||empty($y1)||empty($w)||empty($h)) echo 0;
		$scale = $thumb_width/$w;
		$cropped = resizeThumbnailImage($thumb_image_location,$large_image_location,$w,$h,$x1,$y1,$scale);
		if(empty($cropped)) echo 0;
		else echo $picname;
	}
}