<?php
class dbbackController extends commonController
{
	static protected  $db='';
	public function __construct()
	{
		parent::__construct();
		self::$db=new Dbbak(config('DB_HOST'),config('DB_USER'),config('DB_PWD'),config('DB_NAME'),'utf8',ROOT_PATH.'data/db_back/');
	}

	//显示备份
	public function index()
	{
		$list=self::$db->getTables(config('DB_NAME'));//数据库表名
		if(!$this->isPost()){
			$this->table=$list;
			//$this->assign('list',$this->getFileName('../data/db_back'));//文件夹下所有文件信息
			$this->files=getDir(self::$db->dataDir);//获得文件夹列表
			$this->display();
		}else{
			@set_time_limit(0);
			$backtype=intval($_POST['backtype']);
			$table=$_POST['table'];
			$db_size=$_POST['size'];
			if($backtype)
			{
				$table=$list;
			}
			else {if(empty($table)) $this->error('请选择需要备份的表~');}
			if(self::$db->exportSql($table,$db_size))
			$this->success('备份成功',url('dbback/index'));
			else $this->error('备份失败');
		}

	}

	//恢复已存在备份
	public function recover()
	{
		@set_time_limit(0);
		$file=$_GET['f'];
		if(empty($file)) $this->error('参数错误');
		if(self::$db->importSql($file.'/'))
		{
			$this->success('数据恢复成功！',url('dbback/index'));
		}
		else{
			$this->error('数据恢复失败！');
		}
	}
	
	//ajax显示备份详细信息
	public function detail(){
		$file=$_GET['f'];
		if(empty($file)) {echo '参数错误'; return;}
		$list=getFileName(self::$db->dataDir.$file.'/');
		if(empty($list)) echo '没有详细信息';
		else{
		$str.='<table width="100%"><tr><th>分卷</th><th>大小</th><th>修改时间</th></tr>';
		foreach($list as $vo)
		   $str.='<tr><td align="center">'.$vo['name'].'</td><td align="center">'.$vo['size'].'kb</td><td align="center">'.$vo['time'].'</td></tr>';
		$str.='</table>';
		echo $str;
		}
	}

	//上传备份并恢复
//	public function recovery_file()
//	{
//		@set_time_limit(0);
//		if($_FILES['dbfile']['name']!="")
//		{
//			$uploadpath='../data/db_back'; //设置上传文件的路径
//			$imgpath='data/db_back/'; //设置文件的路径
//			if($imgupload=$this->_upload($uploadpath)){
//				if(self::$db->importSql($imgupload['0']['savename']))
//				{
//					$this->success('数据恢复成功！','/show_rec');;
//				}
//				else{
//					$this->error('数据恢复失败！');
//				}
//			}else{}
//		}
//	}

	public function del()
	{
		$file=$_GET['f'];
		if(empty($file)) $this->error('参数错误');
		if(del_dir(self::$db->dataDir.$file))
		$this->success('删除成功',url('dbback/index'));
		else $this->error('删除失败');
	}
}
?>