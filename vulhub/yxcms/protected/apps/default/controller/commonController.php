<?php
//公共类
class commonController extends memberController {
	protected $layout = 'layout';
	public function __construct()
	{
		parent::__construct();              		
		$this->NewImgPath=__ROOT__.'/upload/news/image/';
		$this->PhotoImgPath=__ROOT__.'/upload/photos/';
		$this->LinkImgPath=__ROOT__.'/upload/links/';
		$this->sorts=$this->sortArray();//树状菜单
		$this->title=config('sitename');
		$this->keywords=config('keywords');
		$this->description=config('description');
		$this->telephone=config('telephone');
		$this->QQ=config('QQ');
		$this->email=config('email');
		$this->address=config('address');
		$this->icp=config('icp');
		$this->view()->addTags(array(//自定义标签
			"/{(\S+):{(.*)}}/i"=>"<?php $$1=getlist(\"$2\"); $$1_i=0; if(!empty($$1)) foreach($$1 as $$1){  $$1_i++; ?> ",
            "/{\/([a-zA-Z_]+)}/i"=> "<?php } ?>",
            "/\[([a-zA-Z_]+)\:\i\]/i"=>"<?php echo \$$1_i ?>",
            "/\#\[([a-zA-Z_]+)\:([a-zA-Z_]+)\]\#/i"=>'".\$$1[\'$2\']."',
            "/\#\[([a-zA-Z_]+)\:([a-zA-Z_]+)\]\#/i"=> '".\$$1[\'$2\']."',
            "/\#\\$(\S+)\#/i"=>'".$$1."',
            "/\[([a-zA-Z_]+)\:([a-zA-Z_]+)\]/i"=>"<?php echo \$$1['$2'] ?>",
            "/\[([a-zA-Z_]+)\:([a-zA-Z_]+) \\\$len\=([0-9]+)\]/i"=>"<?php echo msubstr(\$$1['$2'],0,$3); ?>",
            "/\[([a-zA-Z_]+)\:([a-zA-Z_]+) \\\$elen\=([0-9]+)\]/i"=>"<?php echo substr(\$$1['$2'],0,$3); ?>",
            "/{piece:([a-zA-Z_]+)}/i"=> "<?php model('fragment')->fragment($1);?>"
			),true);
	}
    //获得根节点
    protected function getrootid($id){
        $id=in($id); 
        $rootpath=model('sort')->find("id='{$id}'",'path');
        $rootid= empty($rootpath['path'])? '': substr($rootpath['path'].','.$id, 8, 6);
        return $rootid;
    }
	//返回无限分类数组
	protected  function  sortArray($type=0,$deep=0,$path='')
	{
		$where="";
		if($type) $where.="type='{$type}' ";
		if($deep) $where.=empty($where)?"deep='{$deep}' ":" AND deep='{$deep}'";
		if(!empty($path)) $where.=empty($where)?"path LIKE '{$path}%'":" AND path LIKE '{$path}%'";
		$list=model('sort')->select($where,'id,deep,name,path,norder,method,url,type,extendid,ifmenu');
		if(!empty($list)) $list=re_sort($list);
		$newList=array();
		if(!empty($list)){
			foreach ($list as $vo)
			{
				$newList[$vo['id']]['name']=$vo['name'];
				$newList[$vo['id']]['path']=$vo['path'].','.$vo['id'];
				$newList[$vo['id']]['deep']=$vo['deep'];
				$newList[$vo['id']]['method']=$vo['method'];
				$newList[$vo['id']]['ifmenu']=$vo['ifmenu'];
				$newList[$vo['id']]['url']=getURl($vo['type'],$vo['method'],$vo['url'],$vo['id'],$vo['extendid']);
			}
		}
		return $newList;
	}
	//面包屑导航
	protected  function  crumbs($path=',000000')
	{
		$crumb="首页   >> ";
		if(strlen($path)>7){
			$ids=substr($path,8);
			$CnameArray=model('sort')->select("id IN($ids)",'id,type,name,method,url,extendid','deep');
			foreach ($CnameArray as $vo){
				$vo['url']=getURl($vo['type'],$vo['method'],$vo['url'],$vo['id'],$vo['extendid']);
                $crumb.="<a href='".$vo['url']."'>".$vo['name']."</a> >> ";
			}
		}
		return $crumb;
	}
	//文件上传
	protected  function  upload($savePath='',$maxSize='',$allowExts='',$allowTypes='',$saveRule='')
	{
		$upload=new UploadFile($savePath,$maxSize,$allowExts,$allowTypes,$saveRule);
		return $upload;
	}
}
?>