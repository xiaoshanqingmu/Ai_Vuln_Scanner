<?php
class newsController extends commonController
{
	public function index()
	{
		$id=in($_GET['id']);
		if(empty($id)) $this->pageerror('404');
		else{
			$sortinfo=model('sort')->find("id='{$id}'",'id,name,path,url,type,deep,tplist,keywords,description');
			if(1!=$sortinfo['type']) $this->pageerror('404');
			$path=$sortinfo['path'].','.$sortinfo['id'];
			$deep=$sortinfo['deep']+1;
		}

		$listRows=empty($sortinfo['url'])?10:intval($sortinfo['url']);//每页显示的信息条数
		$url=url('news/index',array('id'=>$id,'page'=>'{page}'));
	    $limit=$this->pageLimit($url,$listRows);
	    
		$where="sort LIKE '{$path}%' AND ispass='1'";
		$count=model('news')->count($where);
		$list=model('news')->select($where,'id,title,color,method,addtime,picture,description,hits','recmd DESC,norder desc,id DESC',$limit);

		$this->daohang=$this->crumbs($path);//面包屑导航
		$this->sortlist=$this->sortArray(0,$deep,$path);//子分类信息
		$this->alist=$list;
		$this->page=$this->pageShow($count);
		$this->title=$sortinfo['name'];//title标签
		if(!empty($sortinfo['keywords'])) $this->keywords=$sortinfo['keywords'];
		if(!empty($sortinfo['description'])) $this->description=$sortinfo['description'];
		$this->rootid=$this->getrootid($_GET['id']);//根节点id
		$this->display($sortinfo['tplist']);
	}
        
	public function content()
	{
		$id=intval(in($_GET['id']));
		if(empty($id)) $this->pageerror('404');
		$info=model('news')->find("id='{$id}'");
		if(empty($info)) $this->pageerror('404');
        model('news')->update("id='$id'","hits=hits+1");//点击
        
		//文章分页
		$page = new Page();
		$url =url($info['method'],array('id'=>$id));
		$info['content'] = $page->contentPage(html_out($info['content']), '<hr style="page-break-after:always;" class="ke-pagebreak" />',$url,10,4); //文章分页
		//获取拓展数据
		$sortid=substr($info['sort'],-6,6);
		$tabid=model('sort')->find("id='{$sortid}'",'extendid');//获取拓展表
		if($tabid['extendid']!=0 && !empty($tabid['extendid'])){
			$tab=model('extend')->select("id='{$tabid['extendid']}' OR pid='{$tabid['extendid']}'",'id,name,tableinfo,type','id');//获取拓展表名和字段
			if(!empty($tab[0]['tableinfo'])){
				$extdata=model('extend')->Extfind($tab[0]['tableinfo'],"id='{$info['extfield']}'");	
				$extinfo=array();
				for($i=1;$i<count($tab);$i++){
					$extinfo[$tab[$i]['id']]=array('name'=>$tab[$i]['name'],'value'=>$extdata[$tab[$i]['tableinfo']],'type'=>$tab[$i]['type']);
				}
				$this->extinfo=$extinfo;//拓展信息	
			}
		}
		//获取拓展数据结束
        $topsort=substr($info['sort'],0,14); //获取顶级类
		$upnews=model('news')->find("ispass='1'  AND id>'$id' AND sort like '{$topsort}%'",'id,title,method','id ASC');//上一篇
		$downnews=model('news')->find("ispass='1' AND id<'$id' AND sort like '{$topsort}%'",'id,title,method','id DESC');//下一篇
		$this->title=$info['title'];//title标题
		if(!empty($info['keywords'])) $this->keywords=$info['keywords'];
		if(!empty($info['description'])) $this->description=$info['description'];
		$this->daohang=$this->crumbs($info['sort']);//面包屑导航
		$this->info=$info;
		$this->downnews=$downnews;
		$this->upnews=$upnews;
		$this->display($info['tpcontent']);
	}
}
?>