<?php
class controller{
	protected $model = NULL; //数据库模型
	protected $layout = NULL; //布局视图
	private $_data = array();
	
	protected function init(){
		if(!file_exists(BASE_PATH . 'apps/install/install.lock') && api('install','ifexist')) $this->redirect(url('install/index/index'));
	}
	
	public function __construct(){
		if( 1 != config('APP_STATE') ){
			$this->error('该应用尚未开启!');
		}
		$this->model = model('base')->model;
		$this->init();
	}

	public function __get($name){
		return isset( $this->_data[$name] ) ? $this->_data[$name] : NULL;
	}

	public function __set($name, $value){
		$this->_data[$name] = $value;
	}
	
	protected function view(){
		static $view = NULL;
		if( empty($view) ){
			$tpconfig=config('TPL');
			$tppath=BASE_PATH . 'apps/' . config('_APP_NAME') .'/view/';
			$tpconfig['TPL_TEMPLATE_PATH']=empty($tpconfig['TPL_TEMPLATE_PATH'])? $tppath : $tppath.$tpconfig['TPL_TEMPLATE_PATH'].'/';
			$view = new cpTemplate($tpconfig);
		}
		return $view;
	}
	//模板赋值
	protected function assign($name, $value){
		return $this->view()->assign($name, $value);
	}
	
	protected function display($tpl = '', $return = false, $is_tpl = true ){
		if( $is_tpl ){
			$tpl = empty($tpl) ? CONTROLLER_NAME . '_'. ACTION_NAME : $tpl;
			if( $is_tpl && $this->layout ){
				$this->__template_file = $tpl;
				$tpl = $this->layout;
			}
		}
		$this->view()->assign( $this->_data );
		return $this->view()->display($tpl, $return, $is_tpl).cpright();
	}
	//获取分页查询limit
	protected function pageLimit($url, $num = 10){
		$url = str_replace(urlencode('{page}'), '{page}', $url);
		$page = is_object($this->pager['obj']) ? $this->pager['obj'] : new Page();	
		$cur_page = $page->getCurPage($url);
		$limit_start = ($cur_page-1) * $num;
		$limit = $limit_start.','.$num;
		$this->pager = array('obj'=>$page, 'url'=>$url, 'num'=>$num, 'cur_page'=>$cur_page, 'limit'=>$limit);
		return $limit;
	}
	
	//分页结果显示
	protected function pageShow($count){
		return $this->pager['obj'] ->show($this->pager['url'], $count, $this->pager['num']);
	}
	//判断是否是数据提交	
	protected function isPost(){
		return $_SERVER['REQUEST_METHOD'] == 'POST';
	}
	
	//直接跳转
	protected function redirect( $url, $code=302) {
		header('location:' . $url, true, $code);
		exit;
	}
	//弹出信息
	protected function alert($msg){
		if (!headers_sent()) header("Content-type: text/html; charset=utf-8");
		echo "<script>alert('$msg');</script>";
	}
    //操作成功之后跳转,默认三秒钟跳转
	protected  function success($msg,$url=NULL,$waitSecond=3)
	{
		if (!headers_sent()) header("Content-type: text/html; charset=utf-8");
		if($url===NULL) $url=url(CONTROLLER_NAME.'/index');
		echo "<!DOCTYPE><html><head><meta http-equiv='Refresh' content='".$waitSecond.";URL=".$url."'>";
		echo '<link href="'.__PUBLIC__.'/artDialog/skins/blue.css" rel="stylesheet" type="text/css" /><script src="'.__PUBLIC__.'/artDialog/artDialog.js"></script>';
		echo '</head><title></title><body></body></html>';
		echo "<script language='javascript'>
var dialog = art.dialog({
    title: 'YXcms提示',
    content: '{$msg}',
    icon: 'succeed',
    ok: function(){
        window.location.href='$url';
        return false;
    }
});
</script>";
		exit;
	}
	
	protected function error($msg,$url=NULL)
	{		
		if (!headers_sent()) header("Content-type: text/html; charset=utf-8");
		if($url==NULL) $jump= "history.go(-1);";
		else $jump= "window.location.href='$url';";
		echo "<!DOCTYPE><html><head>";
		echo '<link href="'.__PUBLIC__.'/artDialog/skins/blue.css" rel="stylesheet" type="text/css" /><script src="'.__PUBLIC__.'/artDialog/artDialog.js"></script>';
		echo '</head><title></title><body></body></html>';
		echo "<script language='javascript'>
var dialog = art.dialog({
    title: 'YXcms提示',
    content: '{$msg}',
    icon: 'error',
    ok: function(){
        {$jump}
        return false;
    }
});
</script>";
		exit;
	}
	protected function pageerror($num='404',$url='')
	{	
	  switch ($num) {
	  	case '404':
	  		header('HTTP/1.1 404 Not Found');
            header("status: 404 Not Found");
	  		break;	
	  	default:
	  		# code...
	  		break;
	  }
      $this->layout='';
      $this->display($num);
      exit;
	}
}