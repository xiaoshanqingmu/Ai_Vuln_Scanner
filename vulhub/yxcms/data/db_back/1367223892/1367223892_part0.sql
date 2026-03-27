-- 管理员信息表
DROP TABLE IF EXISTS yx_admin;
CREATE TABLE `yx_admin` (  
  `id` int(10) unsigned NOT NULL auto_increment,  
  `groupid` tinyint(4) NOT NULL default '1',  
  `username` char(10) NOT NULL,  
  `realname` char(15) NOT NULL,  
  `password` char(32) NOT NULL,  
  `lastlogin_time` int(10) unsigned NOT NULL,  
  `lastlogin_ip` char(15) NOT NULL,  
  `iflock` tinyint(1) unsigned NOT NULL default '0',  
  PRIMARY KEY  (`id`),  
  UNIQUE KEY `usename` (`username`),  
  KEY `groupid` (`groupid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='管理员信息表';

INSERT INTO yx_admin VALUES('1','1','admin','YX','168a73655bfecefdb15b14984dd2ad60','1367216818','127.0.0.1','0');

-- 评论表
DROP TABLE IF EXISTS yx_comment;
CREATE TABLE `yx_comment` (  
  `id` int(8) NOT NULL auto_increment,  
  `pid` int(8) NOT NULL,  
  `sort` text NOT NULL,  
  `account` varchar(12) NOT NULL,  
  `content` text NOT NULL,  
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 拓展字段表
DROP TABLE IF EXISTS yx_extend;
CREATE TABLE `yx_extend` (  
  `id` int(10) NOT NULL auto_increment,  
  `pid` int(10) default '0',  
  `tableinfo` varchar(255) default NULL,  
  `type` int(4) default '0',  
  `defvalue` varchar(255) default NULL,  
  `name` varchar(255) default NULL,  
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

INSERT INTO yx_extend VALUES('1','0','extend_product','0','','产品拓展');
INSERT INTO yx_extend VALUES('2','1','stand','1','未知','产品型号');
INSERT INTO yx_extend VALUES('3','1','price','1','0','产品价格');
INSERT INTO yx_extend VALUES('4','1','brand','1','未知','所属品牌');
INSERT INTO yx_extend VALUES('5','1','color','1','白色','产品颜色');
INSERT INTO yx_extend VALUES('6','1','area','1','未知','所在地区');
INSERT INTO yx_extend VALUES('10','9','zhiwei','1','','招聘职位');
INSERT INTO yx_extend VALUES('9','0','extend_zhaopin','0','','招聘');
INSERT INTO yx_extend VALUES('11','9','renshu','1','','招聘人数');
INSERT INTO yx_extend VALUES('12','9','didian','1','','工作地点');
INSERT INTO yx_extend VALUES('13','9','daiyu','1','','工资待遇');
INSERT INTO yx_extend VALUES('14','9','youxiao','1','','有效期');
INSERT INTO yx_extend VALUES('15','9','miaoshu','1','','职位描述');
INSERT INTO yx_extend VALUES('16','0','extend_chanpin','0','','产品信息');
INSERT INTO yx_extend VALUES('17','16','xinghao','1','','型号');
INSERT INTO yx_extend VALUES('18','16','pinpai','1','','品牌');

-- 产品拓展表
DROP TABLE IF EXISTS yx_extend_chanpin;
CREATE TABLE `yx_extend_chanpin` (  
  `id` int(11) NOT NULL auto_increment,  
  `xinghao` varchar(250) NOT NULL,  
  `pinpai` varchar(250) NOT NULL,  
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 产品拓展表(旧)
DROP TABLE IF EXISTS yx_extend_product;
CREATE TABLE `yx_extend_product` (  
  `id` int(11) NOT NULL auto_increment,  
  `stand` varchar(250) NOT NULL,  
  `price` varchar(250) NOT NULL,  
  `brand` varchar(250) NOT NULL,  
  `color` varchar(250) NOT NULL,  
  `area` varchar(250) NOT NULL,  
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- 招聘拓展表
DROP TABLE IF EXISTS yx_extend_zhaopin;
CREATE TABLE `yx_extend_zhaopin` (  
  `id` int(11) NOT NULL auto_increment,  
  `zhiwei` varchar(250) NOT NULL,  
  `renshu` varchar(250) NOT NULL,  
  `didian` varchar(250) NOT NULL,  
  `daiyu` varchar(250) NOT NULL,  
  `youxiao` varchar(250) NOT NULL,  
  `miaoshu` varchar(250) NOT NULL,  
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 碎片表
DROP TABLE IF EXISTS yx_fragment;
CREATE TABLE `yx_fragment` (  
  `id` int(10) NOT NULL auto_increment,  
  `title` varchar(255) NOT NULL,  
  `sign` varchar(255) NOT NULL COMMENT '前台调用标记',  
  `content` text NOT NULL,  
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

INSERT INTO yx_fragment VALUES('1','右侧公告信息','announce','<p>本站为YXcms的默认演示模板，YXcms是一款基于PHP+MYSQL构建的高效网站管理系统。 后台地址请在网址后面加上/index.php?r=admin进入。 后台的用户名:admin;密码:123456，请进入后修改默认密码。</p>');
INSERT INTO yx_fragment VALUES('2','多说评论代码','duoshuo','<!-- Duoshuo Comment BEGIN --><div class="ds-thread"></div><script type="text/javascript">var duoshuoQuery = {short_name:"yxcms"};(function() {var ds = document.createElement(\'script\');ds.type = \'text/javascript\';ds.async = true;ds.src = \'http://static.duoshuo.com/embed.js\';ds.charset = \'UTF-8\';(document.getElementsByTagName(\'head\')[0] || document.getElementsByTagName(\'body\')[0]).appendChild(ds);})();</script><!-- Duoshuo Comment END -->');
INSERT INTO yx_fragment VALUES('3','关于我们','jianjie','<strong>关于&ldquo;为合作伙伴创造价值&rdquo;</strong></span></div><div>米拓信息认为客户、供应商、公司股东、公司员工等一切和自身有合作关系的单位和个人都是自己的合作伙伴，并只有通过努力为合作伙伴创造价值，才能体现自身的价值并获得发展和成功。</div><div>&nbsp;</div><div><span style="font-size:14px;"><strong>关于&ldquo;诚实、宽容、创新、服务&rdquo;</strong></span></div><div><span style="font-size:12px;">米拓信息认为诚信是一切合作的基础，宽容是解决问题的前提，创新是发展事业的利器，服务是创造价值的根本。</div>');
INSERT INTO yx_fragment VALUES('4','联系方式','lianxi','<div style="color:#333333;font-family:\'Microsoft YaHei\', Tahoma, Verdana, Simsun;line-height:24px;white-space:normal;-webkit-text-size-adjust:none;">请在<strong>后台-碎片列表</strong>中修改此段文字</div><div style="color:#333333;font-family:\'Microsoft YaHei\', Tahoma, Verdana, Simsun;line-height:24px;white-space:normal;-webkit-text-size-adjust:none;">某某有限公司</div><div style="color:#333333;font-family:\'Microsoft YaHei\', Tahoma, Verdana, Simsun;line-height:24px;white-space:normal;-webkit-text-size-adjust:none;">电 &nbsp;话：0000-888888</div><div style="color:#333333;font-family:\'Microsoft YaHei\', Tahoma, Verdana, Simsun;line-height:24px;white-space:normal;-webkit-text-size-adjust:none;">邮 &nbsp;编：000000</div><div style="color:#333333;font-family:\'Microsoft YaHei\', Tahoma, Verdana, Simsun;line-height:24px;white-space:normal;-webkit-text-size-adjust:none;">Email：admin@admin.cn</div><div style="color:#333333;font-family:\'Microsoft YaHei\', Tahoma, Verdana, Simsun;line-height:24px;white-space:normal;-webkit-text-size-adjust:none;">网 &nbsp;址：www.xxxxx.cn</div>');

-- 管理员组表
DROP TABLE IF EXISTS yx_group;
CREATE TABLE `yx_group` (  
  `id` tinyint(3) unsigned NOT NULL auto_increment,  
  `name` varchar(255) NOT NULL,  
  `power` varchar(1000) NOT NULL,  
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO yx_group VALUES('1','超级管理员','-1');

-- 留言本表
DROP TABLE IF EXISTS yx_guestbook;
CREATE TABLE `yx_guestbook` (  
  `id` int(11) NOT NULL auto_increment,  
  `status` tinyint(1) default '0',  
  `name` varchar(15) default NULL,  
  `tel` varchar(15) default NULL,  
  `qq` varchar(20) default NULL,  
  `ip` varchar(16) default NULL,  
  `content` text,  
  `reply` text,  
  `addtime` int(11) default NULL,  
  `backtime` int(11) NOT NULL,  
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 友情链接表
DROP TABLE IF EXISTS yx_link;
CREATE TABLE `yx_link` (  
  `id` int(10) NOT NULL auto_increment,  
  `type` tinyint(1) NOT NULL COMMENT '类型',  
  `norder` int(5) NOT NULL COMMENT '排序',  
  `name` varchar(30) NOT NULL COMMENT '站点名',  
  `url` varchar(40) NOT NULL COMMENT '站点地址',  
  `picture` varchar(30) NOT NULL COMMENT '本地logo',  
  `logourl` varchar(50) NOT NULL COMMENT '远程logo',  
  `siteowner` varchar(30) NOT NULL COMMENT '站点所有者',  
  `info` varchar(300) NOT NULL COMMENT '介绍',  
  `ispass` tinyint(1) NOT NULL,  
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

INSERT INTO yx_link VALUES('1','2','0','canphp','http://www.canphp.com/','1342232505.jpg','','','','1');
INSERT INTO yx_link VALUES('2','2','0','Yxcms','http://www.yxcms.net','1342232581.jpg','','','','1');
INSERT INTO yx_link VALUES('3','1','0','baidu','http://www.baidu.com','','','','','1');
INSERT INTO yx_link VALUES('4','1','0','Google','http://www.google.com','','','','','1');
INSERT INTO yx_link VALUES('5','1','0','模板下载','http://www.xiaopangniu.net','','','','','1');

-- 会员组表
DROP TABLE IF EXISTS yx_member_group;
CREATE TABLE `yx_member_group` (  
  `id` int(3) NOT NULL auto_increment,  
  `name` varchar(30) NOT NULL,  
  `notallow` text NOT NULL,  
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

INSERT INTO yx_member_group VALUES('1','未登录','member/index/index|member/infor|member/order');
INSERT INTO yx_member_group VALUES('2','普通会员','');

-- 会员表
DROP TABLE IF EXISTS yx_members;
CREATE TABLE `yx_members` (  
  `id` int(20) NOT NULL auto_increment,  
  `groupid` int(3) NOT NULL,  
  `account` varchar(30) NOT NULL,  
  `password` varchar(60) NOT NULL,  
  `rmb` int(8) NOT NULL default '0',  
  `crmb` int(8) NOT NULL default '0',  
  `nickname` varchar(30) NOT NULL,  
  `email` varchar(30) NOT NULL,  
  `tel` varchar(15) NOT NULL,  
  `qq` varchar(20) NOT NULL,  
  `regtime` int(11) NOT NULL,  
  `regip` varchar(16) NOT NULL,  
  `lasttime` int(11) NOT NULL,  
  `lastip` varchar(16) NOT NULL,  
  `islock` tinyint(4) NOT NULL,  
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO yx_members VALUES('1','2','admin','15bf4d760b796a8d5340d741c7edf85c','9000','3774','会员演示','404138@qq.com','13638816362','404133749','0','','1359771351','127.0.0.1','0');

-- 权限方法表
DROP TABLE IF EXISTS yx_method;
CREATE TABLE `yx_method` (  
  `id` int(10) unsigned NOT NULL auto_increment,  
  `rootid` int(10) unsigned NOT NULL,  
  `pid` float unsigned NOT NULL,  
  `operate` varchar(255) NOT NULL,  
  `name` varchar(255) NOT NULL,  
  `ifmenu` tinyint(1) NOT NULL default '0' COMMENT '是否菜单显示',  
  PRIMARY KEY  (`id`),  
  KEY `pid` (`pid`)
) ENGINE=MyISAM AUTO_INCREMENT=304 DEFAULT CHARSET=utf8;

INSERT INTO yx_method VALUES('1','1','0','admin','后台登陆管理','1');
INSERT INTO yx_method VALUES('2','1','1','index','管理员管理','1');
INSERT INTO yx_method VALUES('4','1','1','admindel','管理员删除','0');
INSERT INTO yx_method VALUES('5','1','1','adminedit','管理员编辑','0');
INSERT INTO yx_method VALUES('6','1','1','adminlock','管理员锁定','0');
INSERT INTO yx_method VALUES('7','1','1','group','权限管理','1');
INSERT INTO yx_method VALUES('8','1','1','groupedit','管理组编辑','0');
INSERT INTO yx_method VALUES('9','1','1','groupdel','管理组删除','0');
INSERT INTO yx_method VALUES('10','10','0','news','资讯管理','1');
INSERT INTO yx_method VALUES('11','10','10','index','已有资讯','1');
INSERT INTO yx_method VALUES('12','10','10','add','添加资讯','1');
INSERT INTO yx_method VALUES('13','10','10','edit','资讯编辑','0');
INSERT INTO yx_method VALUES('14','10','10','del','资讯删除','0');
INSERT INTO yx_method VALUES('15','10','10','lock','资讯锁定','0');
INSERT INTO yx_method VALUES('16','10','10','recmd','资讯推荐','0');
INSERT INTO yx_method VALUES('17','17','0','dbback','数据库管理','1');
INSERT INTO yx_method VALUES('18','17','17','index','数据库备份','1');
INSERT INTO yx_method VALUES('19','17','17','recover','备份恢复','0');
INSERT INTO yx_method VALUES('20','17','17','detail','备份详细','0');
INSERT INTO yx_method VALUES('21','17','17','del','备份删除','0');
INSERT INTO yx_method VALUES('22','22','0','index','后台面板','0');
INSERT INTO yx_method VALUES('23','22','22','index','后台首页','0');
INSERT INTO yx_method VALUES('24','22','22','login','登陆','0');
INSERT INTO yx_method VALUES('25','22','22','logout','退出登陆','0');
INSERT INTO yx_method VALUES('26','22','22','verify','验证码','0');
INSERT INTO yx_method VALUES('27','22','22','welcome','服务器环境','0');
INSERT INTO yx_method VALUES('28','28','0','set','全局设置','1');
INSERT INTO yx_method VALUES('29','28','28','index','网站设置','1');
INSERT INTO yx_method VALUES('30','30','0','sort','前台栏目管理','1');
INSERT INTO yx_method VALUES('31','30','30','index','栏目列表','1');
INSERT INTO yx_method VALUES('32','30','30','edit','分类编辑','0');
INSERT INTO yx_method VALUES('33','30','30','del','分类删除','0');
INSERT INTO yx_method VALUES('160','150','150','delpic','图集单张图删除','0');
INSERT INTO yx_method VALUES('277','0','0','appmanage','应用管理','1');
INSERT INTO yx_method VALUES('85','28','28','menuname','后台菜单','1');
INSERT INTO yx_method VALUES('159','150','150','images_upload','图片批量上传','0');
INSERT INTO yx_method VALUES('158','10','10','FileManagerJson','编辑器上传管理','0');
INSERT INTO yx_method VALUES('157','10','10','UploadJson','编辑器上传','0');
INSERT INTO yx_method VALUES('150','150','0','photo','图集管理','1');
INSERT INTO yx_method VALUES('151','150','150','index','已有图集','1');
INSERT INTO yx_method VALUES('152','150','150','add','添加图集','1');
INSERT INTO yx_method VALUES('153','150','150','edit','图集编辑','0');
INSERT INTO yx_method VALUES('154','150','150','del','图集删除','0');
INSERT INTO yx_method VALUES('155','150','150','lock','图集锁定','0');
INSERT INTO yx_method VALUES('156','150','150','recmd','图集推荐','0');
INSERT INTO yx_method VALUES('174','10','10','cutcover','封面图剪切','0');
INSERT INTO yx_method VALUES('236','30','30','PageUploadJson','单页上传','0');
INSERT INTO yx_method VALUES('235','30','30','pageedit','单页编辑','0');
INSERT INTO yx_method VALUES('234','30','30','pageadd','添加单页栏目','0');
INSERT INTO yx_method VALUES('233','30','30','photoedit','图集栏目编辑','0');
INSERT INTO yx_method VALUES('232','30','30','photoadd','添加图集栏目','0');
INSERT INTO yx_method VALUES('231','30','30','newsedit','文章栏目编辑','0');
INSERT INTO yx_method VALUES('230','30','30','newsadd','添加文章栏目','0');
INSERT INTO yx_method VALUES('182','28','28','clear','网站缓存','1');
INSERT INTO yx_method VALUES('188','188','0','link','友情链接','1');
INSERT INTO yx_method VALUES('189','188','188','index','链接列表','1');
INSERT INTO yx_method VALUES('190','188','188','add','添加链接','1');
INSERT INTO yx_method VALUES('191','188','188','edit','链接编辑','0');
INSERT INTO yx_method VALUES('192','188','188','del','链接删除','0');
INSERT INTO yx_method VALUES('228','1','1','adminnow','账户管理','1');
INSERT INTO yx_method VALUES('229','188','188','lock','锁定','0');
INSERT INTO yx_method VALUES('237','30','30','PageFileManagerJson','单页上传管理','0');
INSERT INTO yx_method VALUES('238','238','0','fragment','信息碎片管理','1');
INSERT INTO yx_method VALUES('239','238','238','index','碎片列表','1');
INSERT INTO yx_method VALUES('240','238','238','add','碎片添加','1');
INSERT INTO yx_method VALUES('241','238','238','edit','碎片编辑','0');
INSERT INTO yx_method VALUES('242','238','238','del','碎片删除','0');
INSERT INTO yx_method VALUES('243','238','238','UploadJson','编辑器上传','0');
INSERT INTO yx_method VALUES('244','238','238','FileManagerJson','编辑器上传管理','0');
INSERT INTO yx_method VALUES('245','28','28','tpchange','前台模板','1');
INSERT INTO yx_method VALUES('251','30','30','pluginadd','添加应用栏目','0');
INSERT INTO yx_method VALUES('252','30','30','pluginedit','应用栏目编辑','0');
INSERT INTO yx_method VALUES('258','258','0','extendfield','自定义表','1');
INSERT INTO yx_method VALUES('259','258','258','index','自定义表列表','1');
INSERT INTO yx_method VALUES('260','258','258','tableadd','添加自定义表','1');
INSERT INTO yx_method VALUES('261','258','258','tableedit','拓展表编辑','0');
INSERT INTO yx_method VALUES('262','258','258','tabledel','拓展表删除','0');
INSERT INTO yx_method VALUES('263','258','258','fieldlist','字段列表','0');
INSERT INTO yx_method VALUES('264','258','258','fieldadd','添加字段','0');
INSERT INTO yx_method VALUES('265','258','258','fieldedit','编辑字段','0');
INSERT INTO yx_method VALUES('266','258','258','fielddel','字段删除','0');
INSERT INTO yx_method VALUES('267','258','258','file','文件上传','0');
INSERT INTO yx_method VALUES('268','10','10','ex_field','字段拓展','0');
INSERT INTO yx_method VALUES('269','150','150','ex_field','字段拓展','0');
INSERT INTO yx_method VALUES('270','30','30','linkadd','添加自定义栏目','0');
INSERT INTO yx_method VALUES('271','30','30','linkedit','自定义栏目编辑','0');
INSERT INTO yx_method VALUES('282','282','0','guestbook','留言版','1');
INSERT INTO yx_method VALUES('283','0','0','member','会员管理(应用)','1');
INSERT INTO yx_method VALUES('284','282','282','index','留言列表','1');
INSERT INTO yx_method VALUES('285','282','282','edit','留言编辑','0');
INSERT INTO yx_method VALUES('286','282','282','del','删除留言','0');
INSERT INTO yx_method VALUES('287','282','282','lock','留言审核','0');
INSERT INTO yx_method VALUES('288','10','10','colchange','资讯转移栏目','0');
INSERT INTO yx_method VALUES('289','150','150','colchange','图集转移栏目','0');
INSERT INTO yx_method VALUES('290','150','150','UploadJson','图集编辑器上传','0');
INSERT INTO yx_method VALUES('291','150','150','FileManagerJson','图集编辑器上传管理','0');
INSERT INTO yx_method VALUES('292','28','28','tplist','模板文件列表','0');
INSERT INTO yx_method VALUES('293','28','28','tpadd','模板文件添加','0');
INSERT INTO yx_method VALUES('294','28','28','tpedit','模板文件编辑','0');
INSERT INTO yx_method VALUES('295','28','28','tpdel','删除模板文件','0');
INSERT INTO yx_method VALUES('296','28','28','tpgetcode','获取模板内容','0');
INSERT INTO yx_method VALUES('297','258','258','meslist','自定义表信息','0');
INSERT INTO yx_method VALUES('298','258','258','mesedit','自定义表信息编辑','0');
INSERT INTO yx_method VALUES('299','258','258','mesdel','自定义表信息删除','0');
INSERT INTO yx_method VALUES('300','258','258','meslock','自定义表信息审核','0');
INSERT INTO yx_method VALUES('301','30','30','add','添加栏目','1');
INSERT INTO yx_method VALUES('302','30','30','extendadd','添加表单栏目','0');
INSERT INTO yx_method VALUES('303','30','30','extendedit','表单栏目编辑','0');

-- 资讯表
DROP TABLE IF EXISTS yx_news;
CREATE TABLE `yx_news` (  
  `id` int(20) NOT NULL auto_increment,  
  `sort` varchar(350) NOT NULL COMMENT '类别',  
  `account` char(15) NOT NULL COMMENT '发布者账户',  
  `title` varchar(60) NOT NULL COMMENT '标题',  
  `color` varchar(7) NOT NULL COMMENT '标题颜色',  
  `picture` varchar(80) NOT NULL,  
  `keywords` varchar(100) NOT NULL COMMENT '关键字',  
  `description` varchar(300) NOT NULL,  
  `content` text NOT NULL COMMENT '内容',  
  `method` varchar(100) NOT NULL COMMENT '方法',  
  `tpcontent` varchar(100) NOT NULL COMMENT '模板',  
  `norder` int(4) NOT NULL COMMENT '排序',  
  `recmd` tinyint(1) NOT NULL COMMENT '推荐',  
  `hits` int(10) NOT NULL COMMENT '点击量',  
  `ispass` tinyint(1) NOT NULL,  
  `from` varchar(30) NOT NULL COMMENT '来源',  
  `addtime` int(11) NOT NULL,  
  `extfield` int(10) NOT NULL default '0' COMMENT '拓展字段',  
  PRIMARY KEY  (`id`),  
  FULLTEXT KEY `sort` (`sort`)
) ENGINE=MyISAM AUTO_INCREMENT=40 DEFAULT CHARSET=utf8;

-- 订单详情表
DROP TABLE IF EXISTS yx_order_detail;
CREATE TABLE `yx_order_detail` (  
  `id` int(20) NOT NULL auto_increment,  
  `code` varchar(10) NOT NULL COMMENT '商品编号',  
  `ordernum` varchar(20) NOT NULL,  
  `name` varchar(100) NOT NULL,  
  `price` float NOT NULL,  
  `num` int(5) NOT NULL,  
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 订单表
DROP TABLE IF EXISTS yx_orders;
CREATE TABLE `yx_orders` (  
  `id` int(15) NOT NULL auto_increment,  
  `ordernum` varchar(20) NOT NULL COMMENT ' 订单号',  
  `account` varchar(30) NOT NULL COMMENT '账户',  
  `total` float NOT NULL COMMENT '总价',  
  `freight` float NOT NULL COMMENT '运费',  
  `ordertime` int(11) NOT NULL COMMENT '订单时间',  
  `state` tinyint(1) NOT NULL COMMENT '订单状态',  
  `mess` text NOT NULL COMMENT '订单信息',  
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 单页表
DROP TABLE IF EXISTS yx_page;
CREATE TABLE `yx_page` (  
  `id` int(10) NOT NULL auto_increment,  
  `sort` varchar(350) NOT NULL,  
  `content` text NOT NULL,  
  `edittime` varchar(20) NOT NULL,  
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

INSERT INTO yx_page VALUES('2',',000000,100022','关于我们关于我们关于我们关于我们关于我们关于我们关于我们关于我们<span style="white-space:normal;">关于我们</span><span style="white-space:normal;">关于我们</span><span style="white-space:normal;">关于我们</span><span style="white-space:normal;">关于我们</span><span style="white-space:normal;">关于我们</span><span style="white-space:normal;">关于我们</span><span style="white-space:normal;">关于我们</span><span style="white-space:normal;">关于我们<span style="white-space:normal;">关于我们</span><span style="white-space:normal;">关于我们</span><span style="white-space:normal;">关于我们</span><span style="white-space:normal;">关于我们</span><span style="white-space:normal;">关于我们</span><span style="white-space:normal;">关于我们</span><span style="white-space:normal;">关于我们</span><span style="white-space:normal;">关于我们<span style="white-space:normal;">关于我们</span><span style="white-space:normal;">关于我们</span><span style="white-space:normal;">关于我们</span><span style="white-space:normal;">关于我们</span><span style="white-space:normal;">关于我们</span><span style="white-space:normal;">关于我们</span><span style="white-space:normal;">关于我们</span><span style="white-space:normal;">关于我们<span style="white-space:normal;">关于我们</span><span style="white-space:normal;">关于我们</span><span style="white-space:normal;">关于我们</span><span style="white-space:normal;">关于我们</span><span style="white-space:normal;">关于我们</span><span style="white-space:normal;">关于我们</span><span style="white-space:normal;">关于我们</span><span style="white-space:normal;">关于我们<span style="white-space:normal;">关于我们</span><span style="white-space:normal;">关于我们</span><span style="white-space:normal;">关于我们</span><span style="white-space:normal;">关于我们</span><span style="white-space:normal;">关于我们</span><span style="white-space:normal;">关于我们</span><span style="white-space:normal;">关于我们</span><span style="white-space:normal;">关于我们</span></span></span></span></span></span>','2013-04-29 00:10:16');
INSERT INTO yx_page VALUES('3',',000000,100022,100025','联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们','2013-04-29 00:11:30');
INSERT INTO yx_page VALUES('4',',000000,100022,100031','公司简介公司简介公司简介公司简介公司简介','2013-04-29 00:42:58');

-- 图集表
DROP TABLE IF EXISTS yx_photo;
CREATE TABLE `yx_photo` (  
  `id` int(20) NOT NULL auto_increment,  
  `sort` varchar(350) NOT NULL COMMENT '类别',  
  `account` char(15) NOT NULL COMMENT '发布者账户',  
  `title` varchar(60) NOT NULL COMMENT '标题',  
  `color` varchar(7) NOT NULL COMMENT '标题颜色',  
  `picture` varchar(80) NOT NULL COMMENT '封面图',  
  `keywords` varchar(100) NOT NULL COMMENT '关键字',  
  `description` varchar(300) NOT NULL,  
  `photolist` text NOT NULL COMMENT '图片集',  
  `conlist` text NOT NULL COMMENT '图片说明',  
  `content` varchar(900) NOT NULL COMMENT '内容',  
  `method` varchar(100) NOT NULL COMMENT '方法',  
  `tpcontent` varchar(100) NOT NULL COMMENT '模板',  
  `norder` int(4) NOT NULL COMMENT '排序',  
  `recmd` tinyint(1) NOT NULL COMMENT '推荐',  
  `hits` int(10) NOT NULL COMMENT '点击量',  
  `ispass` tinyint(1) NOT NULL,  
  `addtime` int(11) NOT NULL,  
  `extfield` int(10) NOT NULL default '0' COMMENT '拓展字段',  
  PRIMARY KEY  (`id`),  
  FULLTEXT KEY `sort` (`sort`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

-- 栏目表
DROP TABLE IF EXISTS yx_sort;
CREATE TABLE `yx_sort` (  
  `id` int(6) unsigned NOT NULL auto_increment,  
  `type` tinyint(2) unsigned NOT NULL default '0' COMMENT '模型类别',  
  `path` varchar(255) default NULL,  
  `name` varchar(255) default NULL,  
  `deep` int(5) unsigned NOT NULL default '1' COMMENT '深度',  
  `norder` tinyint(10) unsigned NOT NULL default '0' COMMENT '排序',  
  `ifmenu` tinyint(1) NOT NULL COMMENT '是否前台显示',  
  `method` varchar(100) NOT NULL COMMENT '模型方法',  
  `tplist` varchar(100) NOT NULL COMMENT '列表模板',  
  `keywords` varchar(255) NOT NULL COMMENT '描述',  
  `description` varchar(300) NOT NULL COMMENT '描述',  
  `url` varchar(100) NOT NULL COMMENT '外部链接',  
  `extendid` int(10) NOT NULL COMMENT '拓展表id',  
  PRIMARY KEY  (`id`),  
  FULLTEXT KEY `path` (`path`)
) ENGINE=MyISAM AUTO_INCREMENT=100038 DEFAULT CHARSET=utf8;

INSERT INTO yx_sort VALUES('100022','3',',000000','关于我们','1','0','1','page/index','page_index','关于,我们','关于我们关于我们关于我们关于我们关于我们关于我们关于我们关于我们关于我们关于我们关于我们关于我们关于我们关于我们关于我们关于我们关于我们关于我们关于我们关于我们关于我','','0');
INSERT INTO yx_sort VALUES('100027','1',',000000','家具知识','1','0','1','news/index','news_index','','','10','0');
INSERT INTO yx_sort VALUES('100028','1',',000000,100023','公司新闻','2','0','1','news/index','news_index','','','10','0');
INSERT INTO yx_sort VALUES('100029','1',',000000,100023','行业动态','2','0','1','news/index','news_index','','','10','0');
INSERT INTO yx_sort VALUES('100030','1',',000000','售后服务','1','0','1','news/index','news_index','','','10','0');
INSERT INTO yx_sort VALUES('100031','3',',000000,100022','公司简介','2','0','1','page/index','page_index','公司简介公司简介公司简介公司简介公司简介','公司简介公司简介公司简介公司简介公司简介','','0');
INSERT INTO yx_sort VALUES('100033','1',',000000','系统公告','1','0','0','news/index','news_index','','','10','0');
INSERT INTO yx_sort VALUES('100032','1',',000000','幻灯','1','0','0','news/index','news_index','','','10','0');
INSERT INTO yx_sort VALUES('100023','1',',000000','新闻资讯','1','0','1','news/index','news_index','','','10','0');
INSERT INTO yx_sort VALUES('100024','2',',000000','产品展示','1','0','1','photo/index','photo_index','','','10','0');
INSERT INTO yx_sort VALUES('100025','3',',000000,100022','联系我们','2','0','1','page/index','page_index','我们,联系','联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们','','0');
INSERT INTO yx_sort VALUES('100026','1',',000000','招贤纳士','1','0','0','news/index','news_index','','','10','9');
INSERT INTO yx_sort VALUES('100034','2',',000000,100024','分类一','2','0','1','photo/index','photo_index','','','10','0');
INSERT INTO yx_sort VALUES('100035','2',',000000,100024','分类二','2','0','1','photo/index','photo_index','','','10','0');
INSERT INTO yx_sort VALUES('100036','5',',000000','Yxcms','1','0','1','','','','','http://www.yxcms.net','1');
INSERT INTO yx_sort VALUES('100037','5',',000000','模板下载','1','0','1','','','','','http://www.xiaopangniu.net','1');