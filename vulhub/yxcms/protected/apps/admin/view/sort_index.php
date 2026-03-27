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
	}
	);
  });
</script>
<title>栏目管理</title>
</head>
<body>
<div class="contener">
<div class="list_head_m">
           <div class="list_head_ml">当前位置：【栏目管理】</div>
           <div class="list_head_mr"><a href="{url('sort/add')}" class="add">新增</a></div>  
</div>


         <table width="100%" border="0" cellpadding="0" cellspacing="1"    class="all_cont">
          <tr>
            <th width="100">ID</th>
            <th width="80">模型</th>
            <th>栏目名称</th>            
            <th width="50">排序</th>
            <th width="110">管理选项</th>
          </tr>
          <?php          
             if(!empty($list)){
                foreach($list as $vo){
                     $ext=$vo['extendid']?($vo['type']==5)?'<font color=green>（外链）</font>':'<font color=green>（拓展字段）</font>':'';
					 $ext.=$vo['ifmenu']?'':'<font color=green>（前台隐藏）</font>';
                     $space = str_repeat('├┈┈┈', $vo['deep']-1); 
                     $tlist.= '<tr><td align="center">'.$vo['id'].'</td>';
                     $tlist.= '<td align="center">'.$sort[$vo['type']]['name'].'模型</td>';   
                     $tlist.= '<td>'.$space.'<a title="点击预览"  target="_blank" href="'.$vo['url'].'">'.$vo['name'].'</a>'.$ext.'</td>';                    
                     $tlist.= '<td align="center">'.$vo['norder'].'</td>';   
                     $tlist.= '<td><a href="'.url('sort/'.$sort[$vo['type']]['mark'].'edit',array('id'=>$vo['id'])).'" class="edt">编辑</a><a href="'.url('sort/del',array('type'=>$vo['type'],'id'=>$vo['id'])).'" class="del" onClick="return confirm(\'删除不可以恢复~确定要删除吗？\')">删除</a></td></tr>';  
                    }
                echo $tlist;
             }
           ?>         
        </table>

</div>
</body>
</html>