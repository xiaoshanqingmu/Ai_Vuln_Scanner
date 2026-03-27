<?php
class pageController extends commonController
{
	public function index()
	{
		$id=in($_GET['id']);
		if(empty($id)) $this->pageerror('404');
        $sortinfo=model('sort')->find("id='{$id}'",'id,path,type,deep,method,name,keywords,description,tplist');
        if(3!=$sortinfo['type']) $this->pageerror('404');
        $deep=$sortinfo['deep']+1;
		$path=$sortinfo['path'].','.$sortinfo['id'];
		$info=model('page')->find("sort='{$path}'");

		//文章分页
		$page = new Page();
		$url = url($sortinfo['method'],array('id'=>$id,'page'=>'{page}'));
		$info['content'] = $page->contentPage(html_out($info['content']), '<hr style="page-break-after:always;" class="ke-pagebreak" />',$url,10,4); //文章分页

		$this->sortlist=$this->sortArray(0,$deep,$path);//子分类信息
		$this->daohang=$this->crumbs($info['sort']);//面包屑导航
        $this->title=$sortinfo['name'];//title标签
		if(!empty($sortinfo['keywords'])) $this->keywords=$sortinfo['keywords'];
		if(!empty($sortinfo['description'])) $this->description=$sortinfo['description'];
		$this->info=$info;
		$this->rootid=$this->getrootid($_GET['id']);//根节点id
		$this->display($sortinfo['tplist']);
	}
}
?>