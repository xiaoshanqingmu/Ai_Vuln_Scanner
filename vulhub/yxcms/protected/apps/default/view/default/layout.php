<?php if(!defined('APP_NAME')) exit;?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="{$keywords}"/>
<meta name="description" content="{$description}"/>
<title>{$title}</title>
<link rel="stylesheet" type="text/css" href="css/metinfo.css" />
<script src="js/jQuery1.7.2.js" type="text/javascript"></script>
<!--[if IE]>
<script src="http://api.metinfo.cn/met_html5.js" type="text/javascript"></script>
<![endif]-->
</head>
<body>
<header>
  <div class="inner">
    <div class="top-logo"> <a href="" title="网站名称" id="web_logo"> <img src="images/1342516579.png" alt="网站名称" title="网站名称" style="margin:20px 0px 0px 0px;" /> </a>
      <ul class="top-nav list-none">
        <li class="t"><a href='#' onclick='SetHome(this,window.location);' style='cursor:pointer;' title='设为首页'  >设为首页</a><span>|</span><a href='#' onclick='addFavorite();' style='cursor:pointer;' title='收藏本站'  >收藏本站</a><span>|</span><a class=fontswitch id=StranLink href="javascript:StranBody()">繁体中文</a>
          <script src="js/ch.js" type="text/javascript"></script>
        </li>
        <li class="b">
          <p> 优化推广-SEO参数设置-头部优化文字</p>
        </li>
      </ul>
    </div>
    <nav>
      <ul class="list-none">
        <li id="nav_10001" style='width:121px;'><a href='{url()}' title='网站首页' class='nav'><span>网站首页</span></a></li>
        <li class="line"></li>
		{loop $sorts $key $vo}
        {if $vo['ifmenu']}         
           {if $vo['deep']==1}
            <li id='nav_1' style='width:121px;' {if $rootid==$key} class="current" {/if} ><a {$mclass} href="{$vo['url']}">{$vo['name']}</a></li>
           {/if}
         {/if}
      {/loop}
        
      </ul>
    </nav>
  </div>
</header>
{include file="$__template_file"}

<footer data-module="10001" data-navdown="10001" data-classnow="10001">
  <div class="inner">
    <div class="foot-nav"></div>
    <div class="foot-text">
      <p>Copyright @ 2012-2013 Yxcms Inc. All right reserved. </p>
      <p>联系电话:{$telephone}&nbsp;&nbsp;&nbsp;&nbsp;QQ:{$QQ}&nbsp;&nbsp;&nbsp;&nbsp;站长邮箱：{$email}&nbsp;&nbsp;&nbsp;&nbsp;地址:{$address}&nbsp;&nbsp;&nbsp;&nbsp;ICP:{$icp}</p>
    </div>
  </div>
</footer>
<script src="js/fun.inc.js" type="text/javascript"></script>
</body>
</html>
