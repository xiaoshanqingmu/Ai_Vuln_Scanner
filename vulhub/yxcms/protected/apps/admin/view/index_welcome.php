<?php if(!defined('APP_NAME')) exit;?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="__PUBLICAPP__/css/back.css" type=text/css rel=stylesheet>
<script type="text/javascript" src="__PUBLIC__/js/jquery.js"></script>
<script language="javascript">
  $(function ($) { 
	//行颜色效果
	$('.all_cont tr').hover(
	function () {
        $(this).children().css('background-color', '#f4f8ff');
	},
	function () {
        $(this).children().css('background-color', '#fff');
	});
  });
</script>
<title>配置</title>
</head>
<body>
<div class="contener">
   <div class="list_head_m">
		  <div class="list_head_ml">当前位置：【服务器环境】</div>
		  <div class="list_head_mr"></div>
   </div>


		<table width="100%" border="0" cellpadding="0" cellspacing="1" class="all_cont">
			<tr>
				<th width="100%" colspan="2">服务器概况及PHP配置信息</th>
			</tr>
			<tr>
				<td>服务器域名/IP地址：<?php echo $_SERVER['SERVER_NAME']?>(<?php echo @gethostbyname($_SERVER['SERVER_NAME'])?>)</td>
				<td>服务器时间：<?php echo date("Y年n月j日 H:i:s")?></td>
			</tr>

			<tr>
				<td>服务器操作系统：<?php $os = explode(" ", php_uname());?><?php echo $os[0];?>&nbsp;(内核版本：<?php echo $os[2]?>)</td>
				<td>站点物理路径：<?php echo dirname(dirname($_SERVER['SCRIPT_FILENAME']))?></td>
			</tr>
			<tr>
				<td>服务器解译引擎：<?php echo $_SERVER['SERVER_SOFTWARE']?></td>
				<td>Web服务端口：<?php echo $_SERVER['SERVER_PORT']?></td>
			</tr>
			<tr>
				<td>PHP版本：<?php echo PHP_VERSION?></td>
				<td>PHP运行方式：<?php echo strtoupper(php_sapi_name())?></td>
			</tr>
			<tr>
				<td>支持ZEND编译运行：<?php echo (get_cfg_var("zend_optimizer.optimization_level")||get_cfg_var("zend_extension_manager.optimizer_ts")||get_cfg_var("zend_extension_ts")) ?'<font color="green">√</font>':'<font color="red">×</font>';?></td>
				<td>自动定义全局变量(register_globals)：<?php echo get_cfg_var("register_globals")?'<font color="green">√</font>' : '<font color="red">×</font>'; ?></td>
			</tr>
			<tr>
				<td>允许使用URL打开文件(allow_url_fopen)：<?php echo get_cfg_var("allow_url_fopen")?'<font color="green">√</font>' : '<font color="red">×</font>'; ?></td>
				<td>允许动态加载链接库(enable_dl)：<?php echo get_cfg_var("enable_dl")?'<font color="green">√</font>' : '<font color="red">×</font>'; ?></td>
			</tr>
			<tr>
				<td>显示错误信息(display_errors)：<?php echo get_cfg_var("display_errors")?'<font color="green">√</font>' : '<font color="red">×</font>'; ?></td>
				<td>程序最多允许使用内存量(memory_limit)：<?php echo get_cfg_var("memory_limit"); ?></td>
			</tr>
			<tr>
				<td>POST最大字节数(post_max_size)：<?php echo get_cfg_var("post_max_size"); ?></td>
				<td>允许最大上传文件(upload_max_filesize)：<?php echo get_cfg_var("upload_max_filesize"); ?></td>
			</tr>
			<tr>
				<td>程序最长运行时间(max_execution_time)：<?php echo get_cfg_var("max_execution_time"); ?>秒</td>
				<td>magic_quotes_gpc：<?php echo (1===get_magic_quotes_gpc()) ? '<font color="green">√</font>':'<font color="red">×</font>';?></td>
			</tr>
		</table>



		<table width="100%" border="0" cellpadding="0" cellspacing="1" class="all_cont">
			<tr>
				<th width="100%" colspan="3">服务器组件支持状况</th>
			</tr>
			<tr>
				<td width="33%">Session支持：<?php echo (function_exists('session_start'))?'<font color="green">√</font>' : '<font color="red">×</font>' ?></td>
				<td width="34%">拼写检查(ASpell Library):<?php echo (function_exists('aspell_check_raw'))?'<font color="green">√</font>' : '<font color="red">×</font>' ?></td>
				<td width="33%">高精度数学运算(BCMath)：<?php echo (function_exists('bcadd'))?'<font color="green">√</font>' : '<font color="red">×</font>' ?></td>
			</tr>
			<tr>
				<td>Socket支持：<?php echo (function_exists('fsockopen'))?'<font color="green">√</font>' : '<font color="red">×</font>' ?></td>
				<td>历法运算(Calendar)：<?php echo (function_exists('cal_days_in_month'))?'<font color="green">√</font>' : '<font color="red">×</font>' ?></td>
				<td>DBA数据库：<?php echo (function_exists('dba_close'))?'<font color="green">√</font>' : '<font color="red">×</font>' ?>
				(DBM数据库：<?php echo (function_exists('dbmclose'))?'<font color="green">√</font>' : '<font color="red">×</font>' ?>)</td>
			</tr>
			<tr>
				<td>FTP：<?php echo (function_exists('ftp_login'))?'<font color="green">√</font>' : '<font color="red">×</font>' ?></td>
				<td>dBase数据库：<?php echo (function_exists('dbase_close'))?'<font color="green">√</font>' : '<font color="red">×</font>' ?></td>
				<td>ODBC数据库连接：<?php echo (function_exists('odbc_close'))?'<font color="green">√</font>' : '<font color="red">×</font>' ?></td>
			</tr>
			<tr>
				<td>压缩文件支持(Zlib)：<?php echo (function_exists('gzclose'))?'<font color="green">√</font>' : '<font color="red">×</font>' ?></td>
				<td>FDF表单资料格式：<?php echo (function_exists('fdf_get_ap'))?'<font color="green">√</font>' : '<font color="red">×</font>' ?></td>
				<td>FilePro数据库：<?php echo (function_exists('filepro_fieldcount'))?'<font color="green">√</font>' : '<font color="red">×</font>' ?></td>
			</tr>
			<tr>
				<td>XML解析： <?php echo (function_exists('xml_set_object'))?'<font color="green">√</font>' : '<font color="red">×</font>' ?></td>
				<td>Hyperwave数据库：<?php echo (function_exists('hw_close'))?'<font color="green">√</font>' : '<font color="red">×</font>' ?></td>
				<td>图形处理(GD Library)：<?php echo (function_exists('gd_info'))?'<font color="green">√</font>' : '<font color="red">×</font>' ?></td>
			</tr>
			<tr>
				<td>WDDX支持： <?php echo (function_exists('wddx_add_vars'))?'<font color="green">√</font>' : '<font color="red">×</font>' ?></td>
				<td>IMAP电子邮件系统：<?php echo (function_exists('imap_close'))?'<font color="green">√</font>' : '<font color="red">×</font>' ?></td>
				<td>Informix数据库：<?php echo (function_exists('ifx_close'))?'<font color="green">√</font>' : '<font color="red">×</font>' ?></td>
			</tr>
			<tr>
				<td>VMailMgr邮件处理： <?php echo (function_exists('vm_adduser'))?'<font color="green">√</font>' : '<font color="red">×</font>' ?></td>
				<td>LDAP目录协议：<?php echo (function_exists('ldap_close'))?'<font color="green">√</font>' : '<font color="red">×</font>' ?></td>
				<td>MCrypt加密处理：<?php echo (function_exists('mcrypt_cbc'))?'<font color="green">√</font>' : '<font color="red">×</font>' ?></td>
			</tr>
			<tr>
				<td>SNMP网络管理协议： <?php echo (function_exists('snmpget'))?'<font color="green">√</font>' : '<font color="red">×</font>' ?></td>
				<td>Postgre SQL数据库： <?php echo (function_exists('pg_close'))?'<font color="green">√</font>' : '<font color="red">×</font>' ?></td>
				<td>mSQL数据库：<?php echo (function_exists('msql_close'))?'<font color="green">√</font>' : '<font color="red">×</font>' ?></td>
			</tr>
			<tr>
				<td>PDF文档支持： <?php echo (function_exists('pdf_close'))?'<font color="green">√</font>' : '<font color="red">×</font>' ?></td>
				<td>SQL Server数据库：<?php echo (function_exists('mssql_close'))?'<font color="green">√</font>' : '<font color="red">×</font>' ?></td>
				<td>MySQL数据库：<?php echo (function_exists('mysql_close'))?'<font color="green">√</font>' : '<font color="red">×</font>' ?></td>
			</tr>
		</table>

</div>
</body>
</html>
