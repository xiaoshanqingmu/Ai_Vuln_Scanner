<?php if(!defined('APP_NAME')) exit;?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="__PUBLICAPP__/css/back.css" type=text/css rel=stylesheet>
<script type="text/javascript" src="__PUBLIC__/js/jquery.js"></script>
<title>【后台模型】</title>
<script language="javascript">
  $(function ($) { 
	//多选框
	$('.group_con input:checkbox').click(function(){
	    var id='#'+$(this).attr('class');
		var tclass='.'+$(this).attr('class');
		var judge=false;
		$(tclass).each( function(n){
           if($(this).attr('checked'))
		      judge=true;
        });
		if(judge)  $(id).attr('checked',true);
		else $(id).attr('checked',false);
	});
  });
</script>
</head>
<body>
<div class="contener">
<div class="list_head_m">
           <div class="list_head_ml">当前位置：【后台模型】</div>
           <div class="list_head_mr">

           </div>
        </div>


         <table width="100%" border="0" cellpadding="0" cellspacing="1"   class="all_cont">
          <form action="{url('set/menuname')}"  method="post">
          <tr>
            <td align="left">
            <?php          
             if(!empty($list)){
                $menus='<fieldset><legend><font color="red">在这里可以修改后台模型和模型中方法的中文名称以便理解，并可以勾选方法使其显示于左侧菜单中</font></legend>';
                foreach($list as $vo){
                    if(empty($vo['name'])) $vo['name']=$vo['operate'];
                    $check=$vo['ifmenu']?'checked="checked"':'';
					if($vo['rootid']!=0){
                       if($vo['pid']==0)
                          $menus.='</fieldset><fieldset class="pgroup"><legend class="group_tit" title="'.$vo['operate'].'"><input  name="menu[]" '.$check.' style="display:none" id="check'.$vo['id'].'" type="checkbox" value="'.$vo['id'].'" /><input class="gname" name="mname['.$vo['id'].']"  type="text" value="'.$vo['name'].'" /></legend>';
                       else 
                          $menus.='<div class="group_con" title="'.$vo['operate'].'"><input name="menu[]" '.$check.' class="check'.$vo['pid'].'" type="checkbox" value="'.$vo['id'].'" /><input class="cname" name="mname['.$vo['id'].']"  type="text" value="'.$vo['name'].'" /></div>';
                    }
				}
                echo $menus;
             }
           ?>
            </td>
          </tr>         
          <tr>
            <td align="center">
              
              <input type="submit" value="设置" class="button">
            </td>
          </tr> 
          </form>      
        </table>
</div>
</body>
</html>