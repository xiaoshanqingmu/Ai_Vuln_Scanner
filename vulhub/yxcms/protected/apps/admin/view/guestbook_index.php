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
//前台不显示
function lock(obj){
	     obj.click(function(){
			var nowobj=$(this);
			var id=nowobj.parent().parent().attr('id');
			$.post("{url('guestbook/lock')}", {id:id,status:0},
   				function(data){
					if(data==1){
                      nowobj.html("显示");
					  nowobj.attr('class','unlock');
					  nowobj.unbind("click");
					  unlock(nowobj);
					}else alert(data);
   			});
		});
}
//前台显示
function unlock(obj){
		obj.click(function(){
			var nowobj=$(this);
			var id=nowobj.parent().parent().attr('id');
			
			$.post("{url('guestbook/lock')}", {id:id,status:1},
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
        $(this).children().css('background-color', '#d5efff');
	},
	function () {
        $(this).children().css('background-color', '#fff');
	}
	);
	//ajax操作
	lock($('.lock'));
	unlock($('.unlock'));
	 $('.del').click(function(){
			if(confirm('删除将不可恢复~')){
			var delobj=$(this).parent().parent();
			var id=delobj.attr('id');
			$.get("{url('guestbook/del')}", {id:id},
   				function(data){
					if(data==1){
                      delobj.remove();
					}else alert(data);
   			});
			}
	  });
  });
</script>
<title>留言列表</title>
</head>
<body>
<div class="contener">
<div class="list_head_m">
           <div class="list_head_ml">当前位置：【留言列表】</div>
           <div class="list_head_mr">
           </div>
        </div>

         <table width="100%" border="0" cellpadding="0" cellspacing="1" class="all_cont" >
         <form action="{url('guestbook/del')}" method="post" onSubmit="return confirm('删除不可以恢复~确定要删除吗？');"> 
          <tr>
              <th align="center" width="70"><input type="checkbox" name="chkAll" value="checkbox" onClick="CheckAll(this.form)"/></th>
              <th width="10%">姓名</th>
              <th width="20%">电话</th>
              <th width="20%">QQ</th>
              <th width="20%">IP</th>
              <th width="15%">留言时间</th>
              <th width="15%">管理选项</th>
          </tr>
          <?php 
              if(!empty($list)){
                   foreach($list as $vo){
                     $book.='<tr id="'.$vo['id'].'"><td align="center"><input type="checkbox" name="delid[]" value="'.$vo['id'].'" /></td><td align="center">'.$vo['name'].'</td><td align="center">'.$vo['tel'].'</td><td align="center">'.$vo['qq'].'</td><td align="center">'.$vo['ip'].'</td>';
                     $book.='<td align="center">'.date('Y/m/d h:m:s',$vo['addtime']).'</td><td>'; 
                     $book.=$vo['status']?'<div class="lock">取消</div>':'<div class="unlock">显示</div>';
                     $book.='<a href="'.url('guestbook/edit',array('id'=>$vo['id'])).'" class="edt">编辑</a><div class="del">删除</div></td></tr>';
                    } 
                   echo $book;
               }               
            ?>   
            <tr>
             <td align="center"><input type="submit" class="all_del"  value="删除"></td>
             <td colspan="6"><div class="pagelist">{$page}</div></td>
          </tr>
          </form>  
        </table>
</div>
</body>
</html>