<?php if(!defined('APP_NAME')) exit;?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="__PUBLICAPP__/css/back.css" type=text/css rel=stylesheet>
<script type="text/javascript" src="__PUBLIC__/js/jquery.js"></script>
<script language="javascript">
function CheckAll(form) { //复选框全选/取消
	for (var i=0;i<form.elements.length;i++) { 
		var e = form.elements[i]; 
		if (e.Name != "chkAll"&&e.disabled!=true) 
		e.checked = form.chkAll.checked; 
	} 
  } 
//锁定
function lock(obj){
	     obj.click(function(){
			var nowobj=$(this);
			var id=nowobj.parent().parent().attr('id');
			$.post("{url('link/lock')}", {id:id,ispass:0},
   				function(data){
					if(data==1){
                      nowobj.html("审核");
					  nowobj.attr('class','unlock');
					  nowobj.unbind("click");
					  unlock(nowobj);
					}else alert(data);
   			});
		});
}
//解锁
function unlock(obj){
		obj.click(function(){
			var nowobj=$(this);
			var id=nowobj.parent().parent().attr('id');
			$.post("{url('link/lock')}", {id:id,ispass:1},
   				function(data){
					if(data==1){
                      nowobj.html("取消");
					  nowobj.attr('class','lock');
					  nowobj.unbind("click");
					  lock(nowobj);
					}else alert(data);
   			});
		});
}
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
	lock($('.lock'));
	unlock($('.unlock'));
	$('.del').click(function(){
			if(confirm('删除将不可恢复~')){
			var delobj=$(this).parent().parent();
			var id=delobj.attr('id');
			$.get("{url('link/del')}", {id:id},
   				function(data){
					if(data==1){
                      delobj.remove();
					}else alert(data);
   			});
			}
	  });
  });
</script>
<title>单页列表</title>
</head>
<body>
<div class="contener">
<div class="list_head_m">        
           <div class="list_head_ml">当前位置：【单页列表】</div>
           <div class="list_head_mr"><a href="{url('link/add')}" class="add">新增</a></div>                           
        </div>
         
         <form action="{url('link/del')}" method="post" onSubmit="return confirm('删除不可以恢复~确定要删除吗？');"> 
         <table width="100%" border="0" cellpadding="0" cellspacing="1"   class="all_cont">
          <tr>
            <th width="70"><input type="checkbox" name="chkAll" value="checkbox" onClick="CheckAll(this.form)"/></th>
            <th width="30%">网站LOGO</th>
            <th width="10%">链接类型</th>
            <th width="30%">网站名称</th>
            <th width="10%">排序</th>
            <th width="20%">操作</th>
          </tr>
          <?php 
                 if(!empty($list)){
                      foreach($list as $vo){
                          $logo=empty($vo['picture'])?empty($vo['logourl'])?$public.'/images/youlink.gif':$vo['logourl']:$path.$vo['picture'];
                          $cont.= '<tr id="'.$vo['id'].'"><td align="center" width="70"><input type="checkbox" name="delid[]" value="'.$vo['id'].'"/></td>';
                          $cont.= '<td align="center"><img src="'.$logo.'" border="0"></td><td align="center">';
                          $cont.= ($vo['type']==1)?'文字链接':'图片链接';
                          $cont.= '</td><td align="center"><a target="_blank" href="'.$vo['url'].'">'.$vo['name'].'</a></td>';
                          $cont.='<td align="center">'.$vo['norder'].'</td><td  width="130">';
                          $cont.=$vo['ispass']?'<div class="lock" >取消</div>':'<div class="unlock">审核</div>';
                          $cont.='<a href="'.url('link/edit',array('id'=>$vo['id'])).'" class="edt">编辑</a><div class="del">删除</div></td></tr>';
                       }
                       echo $cont;
                     }
          ?>
          <tr>
             <td align="center"><input type="submit" class="all_del"  value="删除"></td>
             <td colspan="5"><div class="pagelist">{$page}</div></td>
          </tr>
         </table>
       </form>
</div>
</body>
</html>