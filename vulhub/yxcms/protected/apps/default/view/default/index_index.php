<?php if(!defined('APP_NAME')) exit;?>

<div class="inner met_flash">
  <link href='css/css.css' rel='stylesheet' type='text/css' />
  <script src='js/jquery.bxSlider.min.js'></script>
  <div class='flash flash6' style='width:980px; height:245px;'>
    <ul id='slider6'>
      {newstop:{table=(news) field=(id,title,picture,method) order=(recmd DESC,norder desc,id desc) where=(ispass='1' AND sort like '%100032%') limit=(4)}}
      <li><a href='#' target='_blank' title='[newstop:title]'> <img src='{$NewImgPath}[newstop:picture]' alt='[newstop:title]' width='980' height='245'></a></li>
      {/newstop}
    </ul>
  </div>
  <script type='text/javascript'>$(document).ready(function(){ $('#slider6').bxSlider({ mode:'vertical',autoHover:true,auto:true,pager: true,pause: 5000,controls:false});});</script>
</div>
<div class="index inner">
  <div class="aboutus style-1">
    <h3 class="title"> <span class='myCorner' data-corner='top 5px'>关于我们</span> <a href="about/" class="more" title="链接关键词">更多>></a> </h3>
    <div class="active editor clear contour-1">
      <div> <img alt="" src="images/20120716_094159.jpg" style="margin: 8px; width: 196px; float: left; height: 209px; " /></div>
      <div style="padding-top:10px;"> <span style="font-size:14px;">{piece:jianjie}</span></div>
      <div class="clear"></div>
    </div>
  </div>
  <div class="case style-2">
    <h3 class='title myCorner' data-corner='top 5px'> 客户案例 <a href="case/" title="链接关键词" class="more">更多>></a> </h3>
    <div class="active dl-jqrun contour-1">
	{photo:{table=(photo) field=(id,title,addtime,description,picture,method) order=(recmd DESC,norder desc,id desc) where=(ispass='1' AND sort like '%100024%') limit=(2)}}
      <dl class="ind">
        <dt><a href="{url($photo['method'],array('id'=>$photo['id']))}" target='_self'><img src="{$PhotoImgPath}thumb_[photo:picture]" alt="[photo:title]" style="width:116px; height:80px;" /></a></dt>
        <dd>
          <h4><a href="{url($photo['method'],array('id'=>$photo['id']))}" target='_self' title="[photo:title]">[photo:title $len=9]</a></h4>
          <p class="desc" title="[photo:description]">[photo:description $len=30]</p>
        </dd>
      </dl>{/photo}
     
      <div class="clear"></div>
    </div>
  </div>
  <div class="clear"></div>
  <div class="index-news style-1">
    <h3 class="title"> <span class='myCorner' data-corner='top 5px'>公司动态</span> <a href="{url($sorts[100028]['method'],array('id'=>100028))}" class="more" title="链接关键词">更多>></a> </h3>
    <div class="active clear listel contour-2">
      <ol class='list-none metlist'>
	  {newso:{table=(news) field=(id,title,color,addtime,method) order=(recmd DESC,norder desc,id desc) where=(ispass='1' AND sort like '%100028%') limit=(5)}}
        <li class='list top'><span class='time'>{date($newso['addtime'],Y-m-d)}</span><a href='{url($newso['method'],array('id'=>$newso['id']))}' title='[newso:title]' target='_self'>[newso:title $len=16]</a></li>{/newso}
        
      </ol>
    </div>
  </div>
  <div class="index-news style-1">
    <h3 class="title"> <span class='myCorner' data-corner='top 5px'>业界资讯</span> <a href="{url($sorts[100029]['method'],array('id'=>100029))}" class="more" title="链接关键词">更多>></a> </h3>
    <div class="active clear listel contour-2">
      <ol class='list-none metlist'>
        {newso:{table=(news) field=(id,title,color,addtime,method) order=(recmd DESC,norder desc,id desc) where=(ispass='1' AND sort like '%100029%') limit=(5)}}
        <li class='list top'><span class='time'>{date($newso['addtime'],Y-m-d)}</span><a href='{url($newso['method'],array('id'=>$newso['id']))}' title='[newso:title]' target='_self'>[newso:title $len=16]</a></li>{/newso}
      </ol>
    </div>
  </div>
  <div class="index-conts style-2">
    <h3 class='title myCorner' data-corner='top 5px'> 家具知识 <a href="{url($sorts[100028]['method'],array('id'=>100027))}" title="更多家具知识" class="more">更多>></a> </h3>
    <div class="active clear listel contour-2">
      <ol class='list-none metlist'>
        {newso:{table=(news) field=(id,title,color,addtime,method) order=(recmd DESC,norder desc,id desc) where=(ispass='1' AND sort like '%100027%') limit=(5)}}
        <li class='list top'><span class='time'>{date($newso['addtime'],Y-m-d)}</span><a href='{url($newso['method'],array('id'=>$newso['id']))}' title='[newso:title]' target='_self'>[newso:title $len=16]</a></li>{/newso}
      </ol>
    </div>
  </div>
  <div class="clear p-line"></div>
  <div class="index-product style-2">
    <h3 class='title myCorner' data-corner='top 5px'> <span>产品展示</span>
      <div class="flip">
        <p id="trigger"></p>
        <a class="prev" id="car_prev" href="javascript:void(0);"></a> <a class="next" id="car_next" href="javascript:void(0);"></a> </div>
      <a href="{url($sorts[100024]['method'],array('id'=>100024))}" title="链接关键词" class="more">更多>></a> </h3>
    <div class="active clear">
      <div class="profld" id="indexcar" data-listnum="5">
        <ol class='list-none metlist'>
		{photo:{table=(photo) field=(id,title,addtime,picture,method) order=(recmd DESC,norder desc,id desc) where=(ispass='1' AND sort like '%100024%') limit=(8)}}
          <li class='list'><a href='{url($photo['method'],array('id'=>$photo['id']))}' title='[photo:title]' target='_self' class='img'><img src='{$PhotoImgPath}thumb_[photo:picture]' alt='[photo:title]' title='[photo:title]' width='160' height='130' /></a>
            <h3><a href='{url($photo['method'],array('id'=>$photo['id']))}' title='[photo:title]' target='_self'>[photo:title $len=12]</a></h3>
          </li>{/photo}
          
        </ol>
      </div>
    </div>
  </div>
  <div class="clear"></div>
  <div class="index-links">
    <h3 class="title"> 友情链接 <a href="#" title="链接关键词" class="more">欢迎申请链接。QQ888888</a> </h3>
    <div class="active clear">
      <div class="img">
        <ul>
        </ul>
      </div>
      <div class="txt">
        <ul>
          {link:{table=(link) field=(name,url,type,picture,logourl) order=(norder desc,id desc) where=(ispass='1')}}}
        <li><A href="[link:url]" target=_blank>[link:name]</A></li>{/link}
        </ul>
      </div>
    </div>
    <div class="clear"></div>
  </div>
</div>
