/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50051
Source Host           : localhost:3306
Source Database       : blue

Target Server Type    : MYSQL
Target Server Version : 50051
File Encoding         : 65001

Date: 2013-05-20 08:06:39
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for `yx_admin`
-- ----------------------------
DROP TABLE IF EXISTS `yx_admin`;
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

-- ----------------------------
-- Records of yx_admin
-- ----------------------------
INSERT INTO `yx_admin` VALUES ('1', '1', 'admin', 'YX', '168a73655bfecefdb15b14984dd2ad60', '1367216818', '127.0.0.1', '0');

-- ----------------------------
-- Table structure for `yx_comment`
-- ----------------------------
DROP TABLE IF EXISTS `yx_comment`;
CREATE TABLE `yx_comment` (
  `id` int(8) NOT NULL auto_increment,
  `pid` int(8) NOT NULL,
  `sort` text NOT NULL,
  `account` varchar(12) NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of yx_comment
-- ----------------------------

-- ----------------------------
-- Table structure for `yx_extend`
-- ----------------------------
DROP TABLE IF EXISTS `yx_extend`;
CREATE TABLE `yx_extend` (
  `id` int(10) NOT NULL auto_increment,
  `pid` int(10) default '0',
  `tableinfo` varchar(255) default NULL,
  `type` int(4) default '0',
  `defvalue` varchar(255) default NULL,
  `name` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of yx_extend
-- ----------------------------
INSERT INTO `yx_extend` VALUES ('1', '0', 'extend_product', '0', '', '产品拓展');
INSERT INTO `yx_extend` VALUES ('2', '1', 'stand', '1', '未知', '产品型号');
INSERT INTO `yx_extend` VALUES ('3', '1', 'price', '1', '0', '产品价格');
INSERT INTO `yx_extend` VALUES ('4', '1', 'brand', '1', '未知', '所属品牌');
INSERT INTO `yx_extend` VALUES ('5', '1', 'color', '1', '白色', '产品颜色');
INSERT INTO `yx_extend` VALUES ('6', '1', 'area', '1', '未知', '所在地区');
INSERT INTO `yx_extend` VALUES ('10', '9', 'zhiwei', '1', '', '招聘职位');
INSERT INTO `yx_extend` VALUES ('9', '0', 'extend_zhaopin', '0', '', '招聘');
INSERT INTO `yx_extend` VALUES ('11', '9', 'renshu', '1', '', '招聘人数');
INSERT INTO `yx_extend` VALUES ('12', '9', 'didian', '1', '', '工作地点');
INSERT INTO `yx_extend` VALUES ('13', '9', 'daiyu', '1', '', '工资待遇');
INSERT INTO `yx_extend` VALUES ('14', '9', 'youxiao', '1', '', '有效期');
INSERT INTO `yx_extend` VALUES ('15', '9', 'miaoshu', '1', '', '职位描述');
INSERT INTO `yx_extend` VALUES ('16', '0', 'extend_chanpin', '0', '', '产品信息');
INSERT INTO `yx_extend` VALUES ('17', '16', 'xinghao', '1', '', '型号');
INSERT INTO `yx_extend` VALUES ('18', '16', 'pinpai', '1', '', '品牌');

-- ----------------------------
-- Table structure for `yx_extend_chanpin`
-- ----------------------------
DROP TABLE IF EXISTS `yx_extend_chanpin`;
CREATE TABLE `yx_extend_chanpin` (
  `id` int(11) NOT NULL auto_increment,
  `xinghao` varchar(250) NOT NULL,
  `pinpai` varchar(250) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of yx_extend_chanpin
-- ----------------------------

-- ----------------------------
-- Table structure for `yx_extend_product`
-- ----------------------------
DROP TABLE IF EXISTS `yx_extend_product`;
CREATE TABLE `yx_extend_product` (
  `id` int(11) NOT NULL auto_increment,
  `stand` varchar(250) NOT NULL,
  `price` varchar(250) NOT NULL,
  `brand` varchar(250) NOT NULL,
  `color` varchar(250) NOT NULL,
  `area` varchar(250) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of yx_extend_product
-- ----------------------------

-- ----------------------------
-- Table structure for `yx_extend_zhaopin`
-- ----------------------------
DROP TABLE IF EXISTS `yx_extend_zhaopin`;
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

-- ----------------------------
-- Records of yx_extend_zhaopin
-- ----------------------------

-- ----------------------------
-- Table structure for `yx_fragment`
-- ----------------------------
DROP TABLE IF EXISTS `yx_fragment`;
CREATE TABLE `yx_fragment` (
  `id` int(10) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `sign` varchar(255) NOT NULL COMMENT '前台调用标记',
  `content` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of yx_fragment
-- ----------------------------
INSERT INTO `yx_fragment` VALUES ('1', '右侧公告信息', 'announce', '<p>\r\n	本站为YXcms的默认演示模板，YXcms是一款基于PHP+MYSQL构建的高效网站管理系统。 后台地址请在网址后面加上/index.php?r=admin进入。 后台的用户名:admin;密码:123456，请进入后修改默认密码。\r\n</p>');
INSERT INTO `yx_fragment` VALUES ('2', '多说评论代码', 'duoshuo', '<!-- Duoshuo Comment BEGIN -->\r\n<div class=\"ds-thread\">\r\n</div>\r\n<script type=\"text/javascript\">\r\n	          var duoshuoQuery = {short_name:\"yxcms\"};\r\n	          (function() {\r\n		         var ds = document.createElement(\'script\');\r\n		         ds.type = \'text/javascript\';ds.async = true;\r\n		         ds.src = \'http://static.duoshuo.com/embed.js\';\r\n		         ds.charset = \'UTF-8\';\r\n		         (document.getElementsByTagName(\'head\')[0] \r\n		         || document.getElementsByTagName(\'body\')[0]).appendChild(ds);\r\n	         })();\r\n	      </script>\r\n<!-- Duoshuo Comment END -->');
INSERT INTO `yx_fragment` VALUES ('3', '关于我们', 'jianjie', '<strong>关于&ldquo;为合作伙伴创造价值&rdquo;</strong></span></div>\r\n<div>\r\n	米拓信息认为客户、供应商、公司股东、公司员工等一切和自身有合作关系的单位和个人都是自己的合作伙伴，并只有通过努力为合作伙伴创造价值，才能体现自身的价值并获得发展和成功。</div>\r\n<div>\r\n	&nbsp;</div>\r\n<div>\r\n	<span style=\"font-size:14px;\"><strong>关于&ldquo;诚实、宽容、创新、服务&rdquo;</strong></span></div>\r\n<div>\r\n	<span style=\"font-size:12px;\">米拓信息认为诚信是一切合作的基础，宽容是解决问题的前提，创新是发展事业的利器，服务是创造价值的根本。');
INSERT INTO `yx_fragment` VALUES ('4', '联系方式', 'lianxi', '<div style=\"color:#333333;font-family:\'Microsoft YaHei\', Tahoma, Verdana, Simsun;line-height:24px;white-space:normal;-webkit-text-size-adjust:none;\">\r\n	请在<strong>后台-碎片列表</strong>中修改此段文字\r\n</div>\r\n<div style=\"color:#333333;font-family:\'Microsoft YaHei\', Tahoma, Verdana, Simsun;line-height:24px;white-space:normal;-webkit-text-size-adjust:none;\">\r\n	某某有限公司\r\n</div>\r\n<div style=\"color:#333333;font-family:\'Microsoft YaHei\', Tahoma, Verdana, Simsun;line-height:24px;white-space:normal;-webkit-text-size-adjust:none;\">\r\n	电 &nbsp;话：0000-888888\r\n</div>\r\n<div style=\"color:#333333;font-family:\'Microsoft YaHei\', Tahoma, Verdana, Simsun;line-height:24px;white-space:normal;-webkit-text-size-adjust:none;\">\r\n	邮 &nbsp;编：000000\r\n</div>\r\n<div style=\"color:#333333;font-family:\'Microsoft YaHei\', Tahoma, Verdana, Simsun;line-height:24px;white-space:normal;-webkit-text-size-adjust:none;\">\r\n	Email：admin@admin.cn\r\n</div>\r\n<div style=\"color:#333333;font-family:\'Microsoft YaHei\', Tahoma, Verdana, Simsun;line-height:24px;white-space:normal;-webkit-text-size-adjust:none;\">\r\n	网 &nbsp;址：www.xxxxx.cn\r\n</div>');

-- ----------------------------
-- Table structure for `yx_group`
-- ----------------------------
DROP TABLE IF EXISTS `yx_group`;
CREATE TABLE `yx_group` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `power` varchar(1000) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of yx_group
-- ----------------------------
INSERT INTO `yx_group` VALUES ('1', '超级管理员', '-1');

-- ----------------------------
-- Table structure for `yx_guestbook`
-- ----------------------------
DROP TABLE IF EXISTS `yx_guestbook`;
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

-- ----------------------------
-- Records of yx_guestbook
-- ----------------------------

-- ----------------------------
-- Table structure for `yx_link`
-- ----------------------------
DROP TABLE IF EXISTS `yx_link`;
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

-- ----------------------------
-- Records of yx_link
-- ----------------------------
INSERT INTO `yx_link` VALUES ('1', '2', '0', 'canphp', 'http://www.canphp.com/', '1342232505.jpg', '', '', '', '1');
INSERT INTO `yx_link` VALUES ('2', '2', '0', 'Yxcms', 'http://www.yxcms.net', '1342232581.jpg', '', '', '', '1');
INSERT INTO `yx_link` VALUES ('3', '1', '0', 'baidu', 'http://www.baidu.com', '', '', '', '', '1');
INSERT INTO `yx_link` VALUES ('4', '1', '0', 'Google', 'http://www.google.com', '', '', '', '', '1');
INSERT INTO `yx_link` VALUES ('5', '1', '0', '模板下载', 'http://www.xiaopangniu.net', '', '', '', '', '1');

-- ----------------------------
-- Table structure for `yx_members`
-- ----------------------------
DROP TABLE IF EXISTS `yx_members`;
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

-- ----------------------------
-- Records of yx_members
-- ----------------------------
INSERT INTO `yx_members` VALUES ('1', '2', 'admin', '15bf4d760b796a8d5340d741c7edf85c', '9000', '3774', '会员演示', '404138@qq.com', '13638816362', '404133749', '0', '', '1359771351', '127.0.0.1', '0');

-- ----------------------------
-- Table structure for `yx_member_group`
-- ----------------------------
DROP TABLE IF EXISTS `yx_member_group`;
CREATE TABLE `yx_member_group` (
  `id` int(3) NOT NULL auto_increment,
  `name` varchar(30) NOT NULL,
  `notallow` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of yx_member_group
-- ----------------------------
INSERT INTO `yx_member_group` VALUES ('1', '未登录', 'member/index/index|member/infor|member/order');
INSERT INTO `yx_member_group` VALUES ('2', '普通会员', '');

-- ----------------------------
-- Table structure for `yx_method`
-- ----------------------------
DROP TABLE IF EXISTS `yx_method`;
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

-- ----------------------------
-- Records of yx_method
-- ----------------------------
INSERT INTO `yx_method` VALUES ('1', '1', '0', 'admin', '后台登陆管理', '1');
INSERT INTO `yx_method` VALUES ('2', '1', '1', 'index', '管理员管理', '1');
INSERT INTO `yx_method` VALUES ('4', '1', '1', 'admindel', '管理员删除', '0');
INSERT INTO `yx_method` VALUES ('5', '1', '1', 'adminedit', '管理员编辑', '0');
INSERT INTO `yx_method` VALUES ('6', '1', '1', 'adminlock', '管理员锁定', '0');
INSERT INTO `yx_method` VALUES ('7', '1', '1', 'group', '权限管理', '1');
INSERT INTO `yx_method` VALUES ('8', '1', '1', 'groupedit', '管理组编辑', '0');
INSERT INTO `yx_method` VALUES ('9', '1', '1', 'groupdel', '管理组删除', '0');
INSERT INTO `yx_method` VALUES ('10', '10', '0', 'news', '资讯管理', '1');
INSERT INTO `yx_method` VALUES ('11', '10', '10', 'index', '已有资讯', '1');
INSERT INTO `yx_method` VALUES ('12', '10', '10', 'add', '添加资讯', '1');
INSERT INTO `yx_method` VALUES ('13', '10', '10', 'edit', '资讯编辑', '0');
INSERT INTO `yx_method` VALUES ('14', '10', '10', 'del', '资讯删除', '0');
INSERT INTO `yx_method` VALUES ('15', '10', '10', 'lock', '资讯锁定', '0');
INSERT INTO `yx_method` VALUES ('16', '10', '10', 'recmd', '资讯推荐', '0');
INSERT INTO `yx_method` VALUES ('17', '17', '0', 'dbback', '数据库管理', '1');
INSERT INTO `yx_method` VALUES ('18', '17', '17', 'index', '数据库备份', '1');
INSERT INTO `yx_method` VALUES ('19', '17', '17', 'recover', '备份恢复', '0');
INSERT INTO `yx_method` VALUES ('20', '17', '17', 'detail', '备份详细', '0');
INSERT INTO `yx_method` VALUES ('21', '17', '17', 'del', '备份删除', '0');
INSERT INTO `yx_method` VALUES ('22', '22', '0', 'index', '后台面板', '0');
INSERT INTO `yx_method` VALUES ('23', '22', '22', 'index', '后台首页', '0');
INSERT INTO `yx_method` VALUES ('24', '22', '22', 'login', '登陆', '0');
INSERT INTO `yx_method` VALUES ('25', '22', '22', 'logout', '退出登陆', '0');
INSERT INTO `yx_method` VALUES ('26', '22', '22', 'verify', '验证码', '0');
INSERT INTO `yx_method` VALUES ('27', '22', '22', 'welcome', '服务器环境', '0');
INSERT INTO `yx_method` VALUES ('28', '28', '0', 'set', '全局设置', '1');
INSERT INTO `yx_method` VALUES ('29', '28', '28', 'index', '网站设置', '1');
INSERT INTO `yx_method` VALUES ('30', '30', '0', 'sort', '前台栏目管理', '1');
INSERT INTO `yx_method` VALUES ('31', '30', '30', 'index', '栏目列表', '1');
INSERT INTO `yx_method` VALUES ('32', '30', '30', 'edit', '分类编辑', '0');
INSERT INTO `yx_method` VALUES ('33', '30', '30', 'del', '分类删除', '0');
INSERT INTO `yx_method` VALUES ('160', '150', '150', 'delpic', '图集单张图删除', '0');
INSERT INTO `yx_method` VALUES ('277', '0', '0', 'appmanage', '应用管理', '1');
INSERT INTO `yx_method` VALUES ('85', '28', '28', 'menuname', '后台菜单', '1');
INSERT INTO `yx_method` VALUES ('159', '150', '150', 'images_upload', '图片批量上传', '0');
INSERT INTO `yx_method` VALUES ('158', '10', '10', 'FileManagerJson', '编辑器上传管理', '0');
INSERT INTO `yx_method` VALUES ('157', '10', '10', 'UploadJson', '编辑器上传', '0');
INSERT INTO `yx_method` VALUES ('150', '150', '0', 'photo', '图集管理', '1');
INSERT INTO `yx_method` VALUES ('151', '150', '150', 'index', '已有图集', '1');
INSERT INTO `yx_method` VALUES ('152', '150', '150', 'add', '添加图集', '1');
INSERT INTO `yx_method` VALUES ('153', '150', '150', 'edit', '图集编辑', '0');
INSERT INTO `yx_method` VALUES ('154', '150', '150', 'del', '图集删除', '0');
INSERT INTO `yx_method` VALUES ('155', '150', '150', 'lock', '图集锁定', '0');
INSERT INTO `yx_method` VALUES ('156', '150', '150', 'recmd', '图集推荐', '0');
INSERT INTO `yx_method` VALUES ('174', '10', '10', 'cutcover', '封面图剪切', '0');
INSERT INTO `yx_method` VALUES ('236', '30', '30', 'PageUploadJson', '单页上传', '0');
INSERT INTO `yx_method` VALUES ('235', '30', '30', 'pageedit', '单页编辑', '0');
INSERT INTO `yx_method` VALUES ('234', '30', '30', 'pageadd', '添加单页栏目', '0');
INSERT INTO `yx_method` VALUES ('233', '30', '30', 'photoedit', '图集栏目编辑', '0');
INSERT INTO `yx_method` VALUES ('232', '30', '30', 'photoadd', '添加图集栏目', '0');
INSERT INTO `yx_method` VALUES ('231', '30', '30', 'newsedit', '文章栏目编辑', '0');
INSERT INTO `yx_method` VALUES ('230', '30', '30', 'newsadd', '添加文章栏目', '0');
INSERT INTO `yx_method` VALUES ('182', '28', '28', 'clear', '网站缓存', '1');
INSERT INTO `yx_method` VALUES ('188', '188', '0', 'link', '友情链接', '1');
INSERT INTO `yx_method` VALUES ('189', '188', '188', 'index', '链接列表', '1');
INSERT INTO `yx_method` VALUES ('190', '188', '188', 'add', '添加链接', '1');
INSERT INTO `yx_method` VALUES ('191', '188', '188', 'edit', '链接编辑', '0');
INSERT INTO `yx_method` VALUES ('192', '188', '188', 'del', '链接删除', '0');
INSERT INTO `yx_method` VALUES ('228', '1', '1', 'adminnow', '账户管理', '1');
INSERT INTO `yx_method` VALUES ('229', '188', '188', 'lock', '锁定', '0');
INSERT INTO `yx_method` VALUES ('237', '30', '30', 'PageFileManagerJson', '单页上传管理', '0');
INSERT INTO `yx_method` VALUES ('238', '238', '0', 'fragment', '信息碎片管理', '1');
INSERT INTO `yx_method` VALUES ('239', '238', '238', 'index', '碎片列表', '1');
INSERT INTO `yx_method` VALUES ('240', '238', '238', 'add', '碎片添加', '1');
INSERT INTO `yx_method` VALUES ('241', '238', '238', 'edit', '碎片编辑', '0');
INSERT INTO `yx_method` VALUES ('242', '238', '238', 'del', '碎片删除', '0');
INSERT INTO `yx_method` VALUES ('243', '238', '238', 'UploadJson', '编辑器上传', '0');
INSERT INTO `yx_method` VALUES ('244', '238', '238', 'FileManagerJson', '编辑器上传管理', '0');
INSERT INTO `yx_method` VALUES ('245', '28', '28', 'tpchange', '前台模板', '1');
INSERT INTO `yx_method` VALUES ('251', '30', '30', 'pluginadd', '添加应用栏目', '0');
INSERT INTO `yx_method` VALUES ('252', '30', '30', 'pluginedit', '应用栏目编辑', '0');
INSERT INTO `yx_method` VALUES ('258', '258', '0', 'extendfield', '自定义表', '1');
INSERT INTO `yx_method` VALUES ('259', '258', '258', 'index', '自定义表列表', '1');
INSERT INTO `yx_method` VALUES ('260', '258', '258', 'tableadd', '添加自定义表', '1');
INSERT INTO `yx_method` VALUES ('261', '258', '258', 'tableedit', '拓展表编辑', '0');
INSERT INTO `yx_method` VALUES ('262', '258', '258', 'tabledel', '拓展表删除', '0');
INSERT INTO `yx_method` VALUES ('263', '258', '258', 'fieldlist', '字段列表', '0');
INSERT INTO `yx_method` VALUES ('264', '258', '258', 'fieldadd', '添加字段', '0');
INSERT INTO `yx_method` VALUES ('265', '258', '258', 'fieldedit', '编辑字段', '0');
INSERT INTO `yx_method` VALUES ('266', '258', '258', 'fielddel', '字段删除', '0');
INSERT INTO `yx_method` VALUES ('267', '258', '258', 'file', '文件上传', '0');
INSERT INTO `yx_method` VALUES ('268', '10', '10', 'ex_field', '字段拓展', '0');
INSERT INTO `yx_method` VALUES ('269', '150', '150', 'ex_field', '字段拓展', '0');
INSERT INTO `yx_method` VALUES ('270', '30', '30', 'linkadd', '添加自定义栏目', '0');
INSERT INTO `yx_method` VALUES ('271', '30', '30', 'linkedit', '自定义栏目编辑', '0');
INSERT INTO `yx_method` VALUES ('282', '282', '0', 'guestbook', '留言版', '1');
INSERT INTO `yx_method` VALUES ('283', '0', '0', 'member', '会员管理(应用)', '1');
INSERT INTO `yx_method` VALUES ('284', '282', '282', 'index', '留言列表', '1');
INSERT INTO `yx_method` VALUES ('285', '282', '282', 'edit', '留言编辑', '0');
INSERT INTO `yx_method` VALUES ('286', '282', '282', 'del', '删除留言', '0');
INSERT INTO `yx_method` VALUES ('287', '282', '282', 'lock', '留言审核', '0');
INSERT INTO `yx_method` VALUES ('288', '10', '10', 'colchange', '资讯转移栏目', '0');
INSERT INTO `yx_method` VALUES ('289', '150', '150', 'colchange', '图集转移栏目', '0');
INSERT INTO `yx_method` VALUES ('290', '150', '150', 'UploadJson', '图集编辑器上传', '0');
INSERT INTO `yx_method` VALUES ('291', '150', '150', 'FileManagerJson', '图集编辑器上传管理', '0');
INSERT INTO `yx_method` VALUES ('292', '28', '28', 'tplist', '模板文件列表', '0');
INSERT INTO `yx_method` VALUES ('293', '28', '28', 'tpadd', '模板文件添加', '0');
INSERT INTO `yx_method` VALUES ('294', '28', '28', 'tpedit', '模板文件编辑', '0');
INSERT INTO `yx_method` VALUES ('295', '28', '28', 'tpdel', '删除模板文件', '0');
INSERT INTO `yx_method` VALUES ('296', '28', '28', 'tpgetcode', '获取模板内容', '0');
INSERT INTO `yx_method` VALUES ('297', '258', '258', 'meslist', '自定义表信息', '0');
INSERT INTO `yx_method` VALUES ('298', '258', '258', 'mesedit', '自定义表信息编辑', '0');
INSERT INTO `yx_method` VALUES ('299', '258', '258', 'mesdel', '自定义表信息删除', '0');
INSERT INTO `yx_method` VALUES ('300', '258', '258', 'meslock', '自定义表信息审核', '0');
INSERT INTO `yx_method` VALUES ('301', '30', '30', 'add', '添加栏目', '1');
INSERT INTO `yx_method` VALUES ('302', '30', '30', 'extendadd', '添加表单栏目', '0');
INSERT INTO `yx_method` VALUES ('303', '30', '30', 'extendedit', '表单栏目编辑', '0');

-- ----------------------------
-- Table structure for `yx_news`
-- ----------------------------
DROP TABLE IF EXISTS `yx_news`;
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

-- ----------------------------
-- Records of yx_news
-- ----------------------------
INSERT INTO `yx_news` VALUES ('14', ',000000,100023,100028', 'admin', '为什么企业需要有自己的网站', '', 'NoPic.gif', '企业,网站,互联网,优秀,一个,时代,建站,成功,重要,当今,步骤,网上,主页,自己,需要,展示,形象,基地,电子交易,开展,门户,为什么', '企业的主页是企业在Internet上展示形象的门户，是企业开展电子交易的基地，是企业网上的&quot;家&quot;，设计制作一个优秀的网站是建站企业成功迈向互联网的重要步骤。 \r在当今互联网时代，一', '<span style=\"font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;line-height:20px;white-space:normal;\">&nbsp;企业的主页是企业在Internet上展示形象的门户，是企业开展电子交易的基地，是企业网上的\"家\"，设计制作一个优秀的网站是建站企业成功迈向互联网的重要步骤。&nbsp;</span><br style=\"margin:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\" />\r\n<span style=\"font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;line-height:20px;white-space:normal;\">在当今互联网时代，一个企业没有自己的网站就像一个人没有住址，一个商店没有门脸。随着经济全球化和</span><a href=\"http://www.yxcms.net/view/757.htm\" target=\"_blank\" style=\"margin:0px;padding:0px;list-style:none;text-decoration:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;color:#333333;line-height:20px;white-space:normal;\">电子商务</a><span style=\"font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;line-height:20px;white-space:normal;\">经济的到来，企业如果还固守于传统模式则必定不能再适应经济全球化的趋势，企业建站和开展电子商务是一个不可回避的现实，当你的</span><a href=\"http://www.yxcms.net/view/89764.htm\" target=\"_blank\" style=\"margin:0px;padding:0px;list-style:none;text-decoration:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;color:#333333;line-height:20px;white-space:normal;\">竞争</a><span style=\"font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;line-height:20px;white-space:normal;\">对手正在通过INTERNET共享信息，通过电子商务降低成本，拓展</span><a href=\"http://www.yxcms.net/view/9250.htm\" target=\"_blank\" style=\"margin:0px;padding:0px;list-style:none;text-decoration:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;color:#333333;line-height:20px;white-space:normal;\">销售</a><span style=\"font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;line-height:20px;white-space:normal;\">渠道时，你却只能坐失良机。</span><br style=\"margin:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\" />\r\n<h3 style=\"margin:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;font-size:12px;line-height:20px;white-space:normal;\">\r\n	<a style=\"margin:0px;padding:0px;list-style:none;border:0px;color:#333333;\"></a><a style=\"margin:0px;padding:0px;list-style:none;border:0px;color:#333333;\"></a>一、竞争的需要\r\n</h3>\r\n<span style=\"font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;line-height:20px;white-space:normal;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 国际互联网的用户在迅猛地增长，中国上网用户由1995年的一万户速增至2001年上半年的2650万用户。这增长速度是全世界范围的普遍现象。在美国、欧洲、日本、台湾、港澳及其它许多国家，网站和电子信箱系统已经成为公司立业不可缺少的重要组成部分。人们用电子信箱已经比用电话多了，百分之九十以上的大小企业、学校、政府机关、服务业甚至酒吧都设法在热门网络上设立自己的网站，供数以百万计的人们前来参观、浏览和查询。中国及全世界的上网用户在未来几十年内还会迅速增加。您的企业要为这众多的民众、企业服务就必须建立自己的网站和电子信箱系统，在这信息的高速公路上宣传自己高效的工作。企业网站、电子信箱给客户、潜在客户，特别是大客户及海外客户，带来了便利的联系，增加了了解，增强了信任感。这些企业自然是他们要打交道的首选，没有网站和电子信箱的企业将失去越来越多的机会而最终被淘汰。</span><br style=\"margin:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\" />\r\n<h3 style=\"margin:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;font-size:12px;line-height:20px;white-space:normal;\">\r\n	<a style=\"margin:0px;padding:0px;list-style:none;border:0px;color:#333333;\"></a><a style=\"margin:0px;padding:0px;list-style:none;border:0px;color:#333333;\"></a>二、可以迅速树立企业形象\r\n</h3>\r\n<span style=\"font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;line-height:20px;white-space:normal;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 今天，国际互联网络已成为高科技和未来生活的代名词，要显示你公司的实力，提升公司的形象，没有什么比在员工名片、企业信笺、广告及各种公众能看得到的东西上印上自己公司独有的网络地址和专用的集团电子邮件地址更有说服力了。消费者、客户和海外投资者自然对您另眼相看。</span><br style=\"margin:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\" />\r\n<h3 style=\"margin:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;font-size:12px;line-height:20px;white-space:normal;\">\r\n	<a style=\"margin:0px;padding:0px;list-style:none;border:0px;color:#333333;\"></a><a style=\"margin:0px;padding:0px;list-style:none;border:0px;color:#333333;\"></a>三、可以让客户获得所需的商业信息\r\n</h3>\r\n<span style=\"font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;line-height:20px;white-space:normal;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 什么是商业信息？你的营业时间？你的服务项目？你的联系方法？你的支付方式？你的地址？你的新的产品资料？如果你让客户明白与你合作的所有原因和好处，那么何愁生意不上门？更重要的是，你的眼光已经放得非常长远，因为在许多你的销售人员未能到达的地方，人们已经可以通过上网这一最便捷的途径获取你的商业信息，并且不是你花大笔的宣传费用去让客户得到你公司的商业信息，而是客户愿意花钱从您那儿取得所需商业信息，这样一来，既能使你节约大量不必要的支出，又能使你的现有客户或潜在客户更满意。</span><br style=\"margin:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\" />\r\n<h3 style=\"margin:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;font-size:12px;line-height:20px;white-space:normal;\">\r\n	<a style=\"margin:0px;padding:0px;list-style:none;border:0px;color:#333333;\"></a><a style=\"margin:0px;padding:0px;list-style:none;border:0px;color:#333333;\"></a>四、可以为客户提供服务\r\n</h3>\r\n<span style=\"font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;line-height:20px;white-space:normal;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 让客户获得所需的信息是为客户服务的重要方法之一。但是如果你仔细研究了为客户服务的方法，你就会发现许多利用WWW技术为客户服务的方法。你不妨把售后服务项目做成电子表格，让你的员工开发你的客户所感兴趣的产品和服务，并且放在网上，让电脑自动记录客户的查询和订单，使你迅速掌握第一手的统计数据，而无需让员工天天守候在电话机前记录电话内容。你可以让你的客户在数据库中查询到你所生产的产品的颜色、规格。同样，你既不费力也无需花费太多精力就可以在互联网上从事上述活动了。</span><br style=\"margin:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\" />\r\n<h3 style=\"margin:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;font-size:12px;line-height:20px;white-space:normal;\">\r\n	<a style=\"margin:0px;padding:0px;list-style:none;border:0px;color:#333333;\"></a><a style=\"margin:0px;padding:0px;list-style:none;border:0px;color:#333333;\"></a>五、可以吸引公众的注意力\r\n</h3>\r\n<span style=\"font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;line-height:20px;white-space:normal;\">&nbsp;&nbsp;&nbsp;&nbsp; 你不可能将你的新产品信息在全球的周刊上发表，但你可以把上述信息放在你的企业网站上向全世界发表。即使你可以把上述信息在全球的周刊上发表，但消费者遗忘广告、忽略广告，你也无可奈何。有了网站上的信息，任何一个人都可在网上浏览你的网页，都会成为你的潜在客户。</span><br style=\"margin:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\" />\r\n<h3 style=\"margin:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;font-size:12px;line-height:20px;white-space:normal;\">\r\n	<a style=\"margin:0px;padding:0px;list-style:none;border:0px;color:#333333;\"></a><a style=\"margin:0px;padding:0px;list-style:none;border:0px;color:#333333;\"></a>六、可以及时发布时间性强的信息\r\n</h3>\r\n<span style=\"font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;line-height:20px;white-space:normal;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 如果你必须在当晚发表一篇文章、发布季度财政报告、发表新产品宣传信息、进行突发性事件的回应处理，在以前，这些都可能因时间太紧，媒体或印刷厂不能配合而被耽搁。而如今上述信息和附带的图片都可以在你希望的任何时间发布，这是一个全球性的概念，是抢在对手之前的竞争手段。</span><br style=\"margin:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\" />\r\n<h3 style=\"margin:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;font-size:12px;line-height:20px;white-space:normal;\">\r\n	<a style=\"margin:0px;padding:0px;list-style:none;border:0px;color:#333333;\"></a><a style=\"margin:0px;padding:0px;list-style:none;border:0px;color:#333333;\"></a>七、可以销售产品\r\n</h3>\r\n<span style=\"font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;line-height:20px;white-space:normal;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 许多人认为能够销售产品是使用互联网的主要原因，因为它可以到达推销员和销售渠道无法到达的地方，并且极大地方便了消费者。如果有人想成为你的用户，他们就想了解你是做什么的?你能为他们提供什么样的服务？但是在大多数情况下你的潜在用户总是找不到你的推销员，利用互联网你可以轻松廉价地展开销售攻势，你的潜在用户也可以轻松廉价地了解你公司的资料，与你的销售部门联络。</span><br style=\"margin:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\" />\r\n<h3 style=\"margin:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;font-size:12px;line-height:20px;white-space:normal;\">\r\n	<a style=\"margin:0px;padding:0px;list-style:none;border:0px;color:#333333;\"></a><a style=\"margin:0px;padding:0px;list-style:none;border:0px;color:#333333;\"></a>八、可以让公司简介、产品说明声情并茂\r\n</h3>\r\n<span style=\"font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;line-height:20px;white-space:normal;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 尽管你的产品非常好，但人们总是看不到它的样子和它到底是怎么样工作的；产品画册虽然非常好，但它是静止的，也没有人知道它工作时发出什么声音。如果以上因素对你的准用户非常重要，你就应该利用互联网来介绍你的公司和产品，因为万维网（WWW）技术可以很简便地为一段产品介绍加入声音、图形、动画甚至影像等等，这些不断涌现出来的多媒体技术已让网络世界变得丰富多彩。</span><br style=\"margin:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\" />\r\n<h3 style=\"margin:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;font-size:12px;line-height:20px;white-space:normal;\">\r\n	<a style=\"margin:0px;padding:0px;list-style:none;border:0px;color:#333333;\"></a><a style=\"margin:0px;padding:0px;list-style:none;border:0px;color:#333333;\"></a>九、可以进入一个高需求的市场\r\n</h3>\r\n<span style=\"font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;line-height:20px;white-space:normal;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 据统计，www的使用者们可能是一个需求最高的市场。通常，大学或更高学历的人已经获得一份较高的薪水，或者即将获得一份较高的薪水。进入INTERNET社会的这群人，会主动寻找或接受各种高档新产品的广告。尽管有其他因素影响，但这的确是一个目标高度集中的市场。</span><br style=\"margin:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\" />\r\n<h3 style=\"margin:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;font-size:12px;line-height:20px;white-space:normal;\">\r\n	<a style=\"margin:0px;padding:0px;list-style:none;border:0px;color:#333333;\"></a><a style=\"margin:0px;padding:0px;list-style:none;border:0px;color:#333333;\"></a>十、可以回答用户经常关心的问题\r\n</h3>\r\n<span style=\"font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;line-height:20px;white-space:normal;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 在你的公司里任何一个经常接电话的人的都会告诉你，他们的时间被消耗在一遍又一遍回答同一个问题上，你甚至要为回答这些售前和售后问题而专门增设人手；而把这些问题的答案放到企业网站上你，就既能使用户们弄清楚问题又节省了大量时间和人力资源。</span><br style=\"margin:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\" />\r\n<h3 style=\"margin:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;font-size:12px;line-height:20px;white-space:normal;\">\r\n	<a style=\"margin:0px;padding:0px;list-style:none;border:0px;color:#333333;\"></a><a style=\"margin:0px;padding:0px;list-style:none;border:0px;color:#333333;\"></a>十一、可以同你的销售人员随时保持联系\r\n</h3>\r\n<span style=\"font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;line-height:20px;white-space:normal;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 正在出差的员工可能需要产品资料和促成一笔生意的最新信息。如果你有这些信息，如何第一时间交到在外地的销售人员手上呢？派人送去？用速递？还是由他自生自灭？利用WWW技术你的销售人员可以在当地用市内电话上网，及时从企业主机上获取所需资料，无需长途电话费也无需派专人在公司留守。</span><br style=\"margin:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\" />\r\n<h3 style=\"margin:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;font-size:12px;line-height:20px;white-space:normal;\">\r\n	<a style=\"margin:0px;padding:0px;list-style:none;border:0px;color:#333333;\"></a><a style=\"margin:0px;padding:0px;list-style:none;border:0px;color:#333333;\"></a>十二、可以开拓国际市场\r\n</h3>\r\n<span style=\"font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;line-height:20px;white-space:normal;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 你可能对国际潜在市场的信函、电话或法律的含义不太了解，现在通过访问该国的一些企业站点，你可以象同公司对面的公司交谈一样方便地了解国际市场，事实上当你想利用互联网走入国际市场之前，外国的公司可能已经用同样的方法了解过你公司的情况了。当你收到一些外国公司的国际电子邮件的查询时，你就明白到国际市场已为你打开，而这一切都是你以前认为难以办到的。</span><br style=\"margin:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\" />\r\n<h3 style=\"margin:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;font-size:12px;line-height:20px;white-space:normal;\">\r\n	<a style=\"margin:0px;padding:0px;list-style:none;border:0px;color:#333333;\"></a><a style=\"margin:0px;padding:0px;list-style:none;border:0px;color:#333333;\"></a>十三、可以提供24小时服务\r\n</h3>\r\n<span style=\"font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;line-height:20px;white-space:normal;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 你也许有这样的经验，与大洋彼岸约定通话时间不是太早就是太晚，这样的情况难免让你觉得尴尬。因为你们之间存在时间差。你的业务也许遍布全球，但你的当地标准时间并非如此，你睡觉的时候正是你的客户的工作时间，怎么办？企业网站为你和你的客户提供每周7天每天24小时的不间断联系，无论什么时候你总能抢在竞争对手之前为客户提供他们需要的信息。甚至可以赶在他们上班之前做了一份计划书，当客户早上打开电脑，你的计划书就在那里了。</span><br style=\"margin:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\" />\r\n<h3 style=\"margin:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;font-size:12px;line-height:20px;white-space:normal;\">\r\n	<a style=\"margin:0px;padding:0px;list-style:none;border:0px;color:#333333;\"></a><a style=\"margin:0px;padding:0px;list-style:none;border:0px;color:#333333;\"></a>十四、可以尽可能快地更新信息\r\n</h3>\r\n<span style=\"font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;line-height:20px;white-space:normal;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 有时许多信息还没有发布就变成旧的信息了，需要更新了，而印好的资料在你的手上就变成一堆废纸。电子出版改变了你的一切，没有纸张、油墨无需、无需预订版面、不论面积大小、没有加收、随时修改内容……，任何传统印刷方式都不可能有这种灵活性。</span><br style=\"margin:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\" />\r\n<h3 style=\"margin:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;font-size:12px;line-height:20px;white-space:normal;\">\r\n	<a style=\"margin:0px;padding:0px;list-style:none;border:0px;color:#333333;\"></a><a style=\"margin:0px;padding:0px;list-style:none;border:0px;color:#333333;\"></a>十五、可以得到客户的反馈\r\n</h3>\r\n<span style=\"font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;line-height:20px;white-space:normal;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 你向客户发出各类目录和小册子，但是没有顾客上门，这到底是为什么？是产品的颜色、价格还是市场战略出了问题？你没有时间去寻找问题的答案，也没有大量金钱测试市场。有了企业网站，有了你的电子信箱系统，极大地方便客户/消费者及时向你反映情况，提出意见。</span>', 'news/content', 'news_content', '0', '0', '30', '1', '原创', '1367165990', '0');
INSERT INTO `yx_news` VALUES ('13', ',000000,100023,100028', 'admin', '企业建站需要提供哪些资料', '', 'NoPic.gif', '公司,信息,企业建站,资料,需要,用户,取决于,获得,可以,信任,很大,程度,是否,这些,上会,状况,之前,哪些,提供,准备,为了,初步,网站,了解', '企业建站之前需要准备好的资料、信息有：\r \r1、公司信息：公司信息是为了让公司网站的新访问者对公司状况有初步的了解，公司是否可以获得用户的信任，在很大程度上会取决于这些基', '<p style=\"margin-top:0px;margin-bottom:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	企业建站之前需要准备好的资料、信息有：\r\n</p>\r\n<p style=\"margin-top:0px;margin-bottom:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	&nbsp;\r\n</p>\r\n<p style=\"margin-top:0px;margin-bottom:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	1、<b style=\"margin:0px;padding:0px;list-style:none;border:0px;\">公司信息</b>：公司信息是为了让公司网站的新访问者对公司状况有初步的了解，公司是否可以获得用户的信任，在很大程度上会取决于这些基本信息。在公司信息中，如果内容比较丰富，可以进一步分解为若干子栏目，如：公司概况、发展历程、公司动态、媒体报道、主要业绩（证书、数据）、组织结构、企业主要领导人员介绍、联系方式等。\r\n</p>\r\n<p style=\"margin-top:0px;margin-bottom:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	&nbsp;\r\n</p>\r\n<p style=\"margin-top:0px;margin-bottom:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	2、<b style=\"margin:0px;padding:0px;list-style:none;border:0px;\">产品信息</b>：企业网站上的产品信息应全面反映所有系列和各种型号的产品，对产品进行详尽的介绍，如果必要，除了文字介绍之外，可配备相应的图片资料、视频文件等。其他有助于用户产生信任和购买决策的信息，都可以用适当的方式发布在企业网站上，如有关机构、专家的检测和鉴定、用户评论、相关产品知识等。\r\n</p>\r\n<p style=\"margin-top:0px;margin-bottom:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	产品信息通常可按照产品类别分为不同的子栏目。如果公司产品种类比较多，无法在简单的目录中全部列出，为了让用户能够方便的找到所需要的产品，除了设计详细的分级目录之外，还有必要增加产品搜索功能。\r\n</p>\r\n<p style=\"margin-top:0px;margin-bottom:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	&nbsp;\r\n</p>\r\n<p style=\"margin-top:0px;margin-bottom:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	3、<b style=\"margin:0px;padding:0px;list-style:none;border:0px;\">用户服务信息</b>：用户对不同企业、不同产品所期望获得的服务有很大差别。网站常见的服务信息有产品选择和使用常识、产品说明书、在线问答等。\r\n</p>\r\n<p style=\"margin-top:0px;margin-bottom:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	&nbsp;\r\n</p>\r\n<p style=\"margin-top:0px;margin-bottom:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	4、<b style=\"margin:0px;padding:0px;list-style:none;border:0px;\">促销信息</b>：但网站拥有一定的访问量是，企业网站本身便具有一定的<a href=\"http://www.yxcms.net/view/3115165.htm\" target=\"_blank\" style=\"margin:0px;padding:0px;list-style:none;text-decoration:none;border:0px;color:#333333;\">广告价值</a>，因此，可在自己的网站上发布促销信息，如<a href=\"http://www.yxcms.net/view/9184.htm\" target=\"_blank\" style=\"margin:0px;padding:0px;list-style:none;text-decoration:none;border:0px;color:#333333;\">网络广告</a>、有奖竞赛、有奖征文、下载优惠券等。网上的促销活动通常与网下结合进行，网站可以作为一种有效的补充，供用户了解促销互动细则、参与报名等。\r\n</p>\r\n<p style=\"margin-top:0px;margin-bottom:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	&nbsp;\r\n</p>\r\n<p style=\"margin-top:0px;margin-bottom:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	5、<b style=\"margin:0px;padding:0px;list-style:none;border:0px;\">销售信息</b>：当用户对于企业和产品有一定的了解，并且产生了购买动机之后，在网站上应为用户购买提供进一步的支持，以促成销售。在决定购买产品之后，用户仍需要进一步了解相关的购买信息，如最方便的网下销售地点、网上订购方式、售后服务措施等。\r\n</p>\r\n<p style=\"margin-top:0px;margin-bottom:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	&nbsp;\r\n</p>\r\n<p style=\"margin-top:0px;margin-bottom:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	6、<b style=\"margin:0px;padding:0px;list-style:none;border:0px;\">公众信息</b>：指并非作为用户的身份对于公司进行了解的信息，如投资人、媒体记者、调查研究人员等。公众信息包括股权结构、投资信息、<a href=\"http://www.yxcms.net/view/3897396.htm\" target=\"_blank\" style=\"margin:0px;padding:0px;list-style:none;text-decoration:none;border:0px;color:#333333;\">企业财务报告</a>、企业文化、公关活动等。\r\n</p>\r\n<div class=\"spctrl\" paragraphindex=\"54\" style=\"margin:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n</div>\r\n<p style=\"margin-top:0px;margin-bottom:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	7、<b style=\"margin:0px;padding:0px;list-style:none;border:0px;\">其他信息</b>：根据企业的需要，可以在网站上发表其他有关的信息，如招聘信息、采购信息等。对于产品销售范围跨国家的企业，通常还需要不同语言的网站内容。\r\n</p>\r\n<p style=\"margin-top:0px;margin-bottom:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	<br />\r\n</p>\r\n<p style=\"margin-top:0px;margin-bottom:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	在进行企业信息的选择和发布时，应掌握一定的原则：有价值的信息应尽量丰富、完整、及时；不必要的信息和服务，如天气预报、社会新闻、生活服务、免费邮箱等应力求避免。\r\n</p>', 'news/content', 'news_content', '0', '0', '30', '1', '原创', '1367165969', '0');
INSERT INTO `yx_news` VALUES ('12', ',000000,100023,100028', 'admin', 'YXcms如何在nginx正常使用curl规则', '', 'NoPic.gif', '规则,转换,使用,以上,下面,静态,正常,如何在,可以', 'apache转换 nginx可以使用的规则：\rRewriteEngine On\rRewriteRule ^/([a-z]+)/p_([0-9]+).html$ /list.php?id=$1\r以上为apache的伪静态规则。下面为转换后的nginx规则：\rrewrite ^/([a-z]+)/p_([0-9]+).html$ /list.php?id=$1 last;\rapa', '<div style=\"margin:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	apache转换 nginx可以使用的规则：\r\n</div>\r\n<div style=\"margin:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	RewriteEngine On\r\n</div>\r\n<div style=\"margin:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	RewriteRule ^/([a-z]+)/p_([0-9]+).html$ /list.php?id=$1\r\n</div>\r\n<div style=\"margin:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	以上为apache的伪静态规则。下面为转换后的nginx规则：\r\n</div>\r\n<div style=\"margin:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	rewrite ^/([a-z]+)/p_([0-9]+).html$ /list.php?id=$1 last;\r\n</div>\r\n<div style=\"margin:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n</div>\r\n<div style=\"margin:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	apache后则不能直接使用点和问号，所以将/list.php?id=$1中的\"\"去掉，即：/list.php?id=$1\r\n</div>\r\n<div style=\"margin:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	将RewriteRule 换为 rewrite，并在在每条规则后加上”last;“\r\n</div>\r\n<div style=\"margin:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	--------------------------------------------------------------------------------\r\n</div>\r\n<div style=\"margin:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	在nginx下.htaccess并不一定起效，使用yxcms自定义网址格式即启用Cur规则之前，请定义重写规则如下：\r\n</div>\r\n<span style=\"margin:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;color:#E53333;\">if (!-f $request_filename){set $rule_0 1$rule_0;}</span><br style=\"margin:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\" />\r\n<span style=\"margin:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;color:#E53333;\">if (!-d $request_filename){set $rule_0 2$rule_0;}</span><br style=\"margin:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\" />\r\n<span style=\"margin:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;color:#E53333;\">if ($rule_0 = \"21\"){rewrite /. /index.php;}</span>', 'news/content', 'news_content', '0', '0', '58', '1', '原创', '1367165927', '0');
INSERT INTO `yx_news` VALUES ('11', ',000000,100023,100028', 'admin', '新手使用yxcms建站步骤', '', '20130429/thumb_1367165922.png', '古典,公司,资产管理,福建,工艺,福建省,仪式,签约,举行,携手,家中,打造,战略合作,投资,使用,建站,新手,家具,公司股权,有限,步骤', '中国华融资产管理公司与福建山中古典工艺家具有限公司股权投资战略合作签约仪式5日在福建省仙游县举行。中国华融与山中古典将携手打造中国古典工艺家中国华融资产管理公司与福建山', '<span style=\"font-family:\'Microsoft YaHei\', 微软雅黑, Arial, \'Lucida Grande\', Tahoma, sans-serif;line-height:21px;white-space:normal;background-color:#FFFFFF;\">中国华融资产管理公司与福建山中古典工艺家具有限公司股权投资战略合作签约仪式5日在福建省仙游县举行。中国华融与山中古典将携手打造中国古典工艺家<span style=\"font-family:\'Microsoft YaHei\', 微软雅黑, Arial, \'Lucida Grande\', Tahoma, sans-serif;line-height:21px;white-space:normal;background-color:#FFFFFF;\">中国华融资产管理公司与福建山中古典工艺家具有限公司股权投资战略合作签约仪式5日在福建省仙游县举行。中国华融与山中古典将携手打造中国古典工艺家<span style=\"font-family:\'Microsoft YaHei\', 微软雅黑, Arial, \'Lucida Grande\', Tahoma, sans-serif;line-height:21px;white-space:normal;background-color:#FFFFFF;\">中国华融资产管理公司与福建山中古典工艺家具有限公司股权投资战略合作签约仪式5日在福建省仙游县举行。中国华融与山中古典将携手打造中国古典工艺家</span></span><span style=\"font-family:\'Microsoft YaHei\', 微软雅黑, Arial, \'Lucida Grande\', Tahoma, sans-serif;line-height:21px;white-space:normal;background-color:#FFFFFF;\">中国华融资产管理公司与福建山中古典工艺家具有限公司股权投资战略合作签约仪式5日在福建省仙游县举行。中国华融与山中古典将携手打造中国古典工艺家</span><span style=\"font-family:\'Microsoft YaHei\', 微软雅黑, Arial, \'Lucida Grande\', Tahoma, sans-serif;line-height:21px;white-space:normal;background-color:#FFFFFF;\">中国华融资产管理公司与福建山中古典工艺家具有限公司股权投资战略合作签约仪式5日在福建省仙游县举行。中国华融与山中古典将携手打造中国古典工艺家中国华融资产管理公司与福建山中古典工艺家具有限公司股权投资战略合作签约仪式5日在福建省仙游县举行。中国华融与山中古典将携手打造中国古典工艺家<span style=\"font-family:\'Microsoft YaHei\', 微软雅黑, Arial, \'Lucida Grande\', Tahoma, sans-serif;line-height:21px;white-space:normal;background-color:#FFFFFF;\">中国华融资产管理公司与福建山中古典工艺家具有限公司股权投资战略合作签约仪式5日在福建省仙游县举行。中国华融与山中古典将携手打造中国古典工艺家</span><span style=\"font-family:\'Microsoft YaHei\', 微软雅黑, Arial, \'Lucida Grande\', Tahoma, sans-serif;line-height:21px;white-space:normal;background-color:#FFFFFF;\">中国华融资产管理公司与福建山中古典工艺家具有限公司股权投资战略合作签约仪式5日在福建省仙游县举行。中国华融与山中古典将携手打造中国古典工艺家中国华融资产管理公司与福建山中古典工艺家具有限公司股权投资战略合作签约仪式5日在福建省仙游县举行。中国华融与山中古典将携手打造中国古典工艺家<span style=\"font-family:\'Microsoft YaHei\', 微软雅黑, Arial, \'Lucida Grande\', Tahoma, sans-serif;line-height:21px;white-space:normal;background-color:#FFFFFF;\">中国华融资产管理公司与福建山中古典工艺家具有限公司股权投资战略合作签约仪式5日在福建省仙游县举行。中国华融与山中古典将携手打造中国古典工艺家</span><span style=\"font-family:\'Microsoft YaHei\', 微软雅黑, Arial, \'Lucida Grande\', Tahoma, sans-serif;line-height:21px;white-space:normal;background-color:#FFFFFF;\">中国华融资产管理公司与福建山中古典工艺家具有限公司股权投资战略合作签约仪式5日在福建省仙游县举行。中国华融与山中古典将携手打造中国古典工艺家中国华融资产管理公司与福建山中古典工艺家具有限公司股权投资战略合作签约仪式5日在福建省仙游县举行。中国华融与山中古典将携手打造中国古典工艺家<span style=\"font-family:\'Microsoft YaHei\', 微软雅黑, Arial, \'Lucida Grande\', Tahoma, sans-serif;line-height:21px;white-space:normal;background-color:#FFFFFF;\">中国华融资产管理公司与福建山中古典工艺家具有限公司股权投资战略合作签约仪式5日在福建省仙游县举行。中国华融与山中古典将携手打造中国古典工艺家</span><span style=\"font-family:\'Microsoft YaHei\', 微软雅黑, Arial, \'Lucida Grande\', Tahoma, sans-serif;line-height:21px;white-space:normal;background-color:#FFFFFF;\">中国华融资产管理公司与福建山中古典工艺家具有限公司股权投资战略合作签约仪式5日在福建省仙游县举行。中国华融与山中古典将携手打造中国古典工艺家中国华融资产管理公司与福建山中古典工艺家具有限公司股权投资战略合作签约仪式5日在福建省仙游县举行。中国华融与山中古典将携手打造中国古典工艺家</span><span style=\"font-family:\'Microsoft YaHei\', 微软雅黑, Arial, \'Lucida Grande\', Tahoma, sans-serif;line-height:21px;white-space:normal;background-color:#FFFFFF;\"></span></span><span style=\"font-family:\'Microsoft YaHei\', 微软雅黑, Arial, \'Lucida Grande\', Tahoma, sans-serif;line-height:21px;white-space:normal;background-color:#FFFFFF;\"></span></span><span style=\"font-family:\'Microsoft YaHei\', 微软雅黑, Arial, \'Lucida Grande\', Tahoma, sans-serif;line-height:21px;white-space:normal;background-color:#FFFFFF;\"></span></span><span style=\"font-family:\'Microsoft YaHei\', 微软雅黑, Arial, \'Lucida Grande\', Tahoma, sans-serif;line-height:21px;white-space:normal;background-color:#FFFFFF;\"></span></span>', 'news/content', 'news_content', '0', '1', '31', '1', '原创', '1367165864', '0');
INSERT INTO `yx_news` VALUES ('15', ',000000,100023,100028', 'admin', '什么是企业网站', '', 'NoPic.gif', '企业,宣传,企业网站,一个,不但,名片,形象,同时,销售,辅助,可以,网络,良好,平台,互联,就是,概念,网上,进行,相当于', '企业网站的概念\r企业网站，就是企业在互联网上进行网络建设和形像宣传的平台。企业网站就相当于一个企业的网络名片，不但对企业的形象是一个良好的宣传，同时可以辅助企业的销售', '<p style=\"margin-top:0px;margin-bottom:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	<strong style=\"margin:0px;padding:0px;list-style:none;border:0px;\"><span style=\"margin:0px;padding:0px;list-style:none;border:0px;font-size:14px;\">企业网站的概念</span></strong><br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n企业网站，就是企业在互联网上进行网络建设和形像宣传的平台。企业网站就相当于一个企业的网络名片，不但对企业的形象是一个良好的宣传，同时可以辅助企业的销售，甚至可以通过网络直接帮助企业实现产品的销售，企业可以利用网站来进行宣传、产品资讯发布、招聘等等。企业网站的作用就是为展现公司形象，加强客户服务，完善网络业务，还可以与潜在客户建立商业联系。随着网络的发展，出现了提供网络资讯为盈利手段的网络公司，通常这些公司的网站上提供人们生活各个方面的资讯，如时事新闻、旅游、娱乐、经济等。\r\n</p>\r\n<p style=\"margin-top:0px;margin-bottom:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n<strong style=\"margin:0px;padding:0px;list-style:none;border:0px;\"><span style=\"margin:0px;padding:0px;list-style:none;border:0px;font-size:14px;\">企业网站的分类</span></strong><br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n<strong style=\"margin:0px;padding:0px;list-style:none;border:0px;\">电子商务型</strong><br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n主要面向供应商、客户或者企业产品（服务）的消费群体，以提供某种直属于企业业务范围的服务或交易、或者为业务服务的服务或者交易为主；这样的网站可以说是正处于电子商务化的一个中间阶段，由于行业特色和企业投入的深度广度的不同，其电子商务化程度可能处于从比较初级的服务支持、产品列表到比较高级的网上支付的其中某一阶段。通常这种类型可以形象的称为\"网上XX企业\"。例如，网上银行、网上酒店等。<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n<strong style=\"margin:0px;padding:0px;list-style:none;border:0px;\">多媒体广告型</strong><br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n主要面向客户或者企业产品（服务）的消费群体，以宣传企业的核心品牌形象或者主要产品（服务）为主。这种类型无论从目的上还是实际表现手法上相对于普通网站而言更像一个平面广告或者电视广告，因此用\"多媒体广告\"来称呼这种类型的网站更贴切一点。<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n<strong style=\"margin:0px;padding:0px;list-style:none;border:0px;\">产品展示型</strong><br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n主要面向需求商，展示自己产品的详细情况，以及公司的实力。对产品的价格、生产、详细介绍等做最全面的介绍。这种类型的企业站点主要目的是要展示自己产品的最直接有效的方式。在注重品牌和形象的同时也要重视您的产品的介绍。 　　在实际应用中，很多网站往往不能简单的归为某一种类型，无论是建站目的还是表现形式都可能涵盖了两种或两种以上类型；对于这种企业网站，可以按上述类型的区别划分为不同的部分，每一个部分都基本上可以认为是一个较为完整的网站类型。注意：由于互联网公司的特殊性，在这里不包含互联网的信息提供商或者服务提供商的网站。 　　提起企业网站,很多人都以为建立一个简单的具有展示性能的网站就可以了。但是往往忽略了一点——营销。其实建立一个企业网站,核心的观点就是如何使用这个网站推进或者推动企业营销,进而实现企业的信息化管理。 　　信息产业目前已成为第一大规模的产业，并位居全球第三位。这就意味着我国的企业信息化也迎来了前所未有的好时机。第四代智能网站的推出也为中小企业建站提供了思路，可以从企业实用角度出发，对网站进行“总体规划，分步实施”，既可以节省成本，又不影响企业的应用。这种方式目前已经为大多数中小企业所接受，并渐成热潮。\r\n</p>\r\n<p style=\"margin-top:0px;margin-bottom:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	&nbsp;\r\n</p>\r\n<p style=\"margin-top:0px;margin-bottom:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	<strong style=\"margin:0px;padding:0px;list-style:none;border:0px;\"><span style=\"margin:0px;padding:0px;list-style:none;border:0px;font-size:14px;\">企业网站的本质和特点</span></strong><br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n（1）<strong style=\"margin:0px;padding:0px;list-style:none;border:0px;\">企业网站具有自主性和灵活性</strong>\r\n</p>\r\n<p style=\"margin-top:0px;margin-bottom:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 企业网站完全是根据企业本身的需要建立的，并非由其他网络服务商所经营，因此在功能上有较大的自主性和灵活性，也正因为如此，每个企业网站的内容和功能会有较大的差别。企业网站效果的好坏，主动权掌握在自己手里，其前提是对企业网站有正确的认识，这样才能适应企业营销策略的需要，并且从经济上、技术上有实现的条件。因此，企业网站应适应企业的经营需要。\r\n</p>\r\n<p style=\"margin-top:0px;margin-bottom:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	（2）<strong style=\"margin:0px;padding:0px;list-style:none;border:0px;\">企业网站是主动性与被动性的矛盾同一体</strong>\r\n</p>\r\n<p style=\"margin-top:0px;margin-bottom:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 企业通过自己的网站可以主动发布信息，这是企业网站主动性的一面，但是发布在网站上的信息不会自动传递给用户，只能“被动地”等待用户自己来获取信息，这又表现出企业网站具有被动性的一面。同时具有主动性与被动性也是企业网站与搜索引擎和电子邮件等网络营销工具在信息传递方式上的主要差异。从网络营销信息的传递方式来看，搜索引擎完全是被动的，只能被动地等待用户检索，只有用户检索使用的关键词和企业网站相关，并且在检索结果中的信息可以被用户看到并被点击的情况下，这一次网络营销信息的传递才得以实现。电子邮件传递信息则基本上是主动的，发送什么信息、什么时间用什么时候发送，都是营销人员自己可以决定的。\r\n</p>\r\n<p style=\"margin-top:0px;margin-bottom:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	（3）<strong style=\"margin:0px;padding:0px;list-style:none;border:0px;\">企业网站的功能需要通过其他网络营销手段才能体现出来</strong>\r\n</p>\r\n<p style=\"margin-top:0px;margin-bottom:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;企业网站的网络营销价值，是通过网站的各种功能以及各种网络营销手段而体现出来的，网站的信息和功能是基础，网络营销方法的应用是条件。如果建设一个网站而不去合理应用，企业网站这个网络营销工具将不会发挥应用的作用，无论功能多么完善的网站，如果没有用户来浏览和应用，企业网站也就成为摆设，这也就是为什么网站推广作为网络营销首要职能的原因。在实际应用中，一些企业由于缺乏专业人员维护管理，于是呈现给浏览者的网站内容往往数年如一日，甚至用户的咨询邮件也不给予回复，这样的企业网站没有发挥其应用的作用，也就不足为怪了。\r\n</p>\r\n<p style=\"margin-top:0px;margin-bottom:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	（4）<strong style=\"margin:0px;padding:0px;list-style:none;border:0px;\">企业网站的功能具有相对稳定性</strong>\r\n</p>\r\n<p style=\"margin-top:0px;margin-bottom:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 企业网站功能的相对稳定性具有两方面的含义：一方面，一旦网站的结构和功能被设计完成并正式开始运作，在一定时期内将基本稳定，只有在运行一个阶段后进行功能升级的情况下，才能拥有新的功能，网站功能的相对稳定性对于无论网站的运营维护还是对于一些常规网络营销方法的应用都很有必要，一个不断变化中的企业网站是不利于网络营销；另一方面，功能的相对稳定性也意味着，如果存在某些功能方面的缺陷，在下次升级之前的一段时间内，将影响网络营销效果的发挥，因此在企业网站策划过程中应充分考虑到网站功能的这一特点，尽量做到在一定阶段内功能适用并具有一定的前瞻性。 　　（5）企业网站是其他网络营销手段和方法的基础 　　企业网站是一个综合性的网络营销工具，这也就决定了企业网站在网络营销中的作用不是孤立的，不仅与其他营销方法具有直接的关系，也构成了开展网络营销的基础。本章后面的内容也将介绍，整个网络营销方法体系可分为无站点网络营销和基于企业网站的网络营销，后者在网络营销中居于支配地位，这也是在网络营销体系中不能脱离企业网站的根本原因。\r\n</p>', 'news/content', 'news_content', '0', '0', '30', '1', '原创', '1367166015', '0');
INSERT INTO `yx_news` VALUES ('16', ',000000,100023,100029', 'admin', 'YXcmsApp 1.1.5发布', '', 'NoPic.gif', '用户,栏目,管理,收集,用于,功能,提交,前台,在后,可以,信息,新增,改善,日志,升级,发布,后台,界面,增加,简洁,定义', '1.1.5 版升级日志\r-------------------------------------------------\r1、改善：后台栏目管理界面简洁化\r2、新增：增加自定义表单功能用于用户收集用户提交的信息，并可以在后台“前台栏目管理”中添', '<p style=\"margin-top:0px;margin-bottom:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	1.1.5 版升级日志<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n-------------------------------------------------<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n1、改善：后台栏目管理界面简洁化<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n2、新增：增加自定义表单功能用于用户收集用户提交的信息，并可以在后台“前台栏目管理”中添加<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n3、取消：取消图集描述内容判断<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n---------------------------------<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n1.1.4到1.1.5升级方法：<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n---------------------------------<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n1、覆盖protected/apps/admin/controller中文件<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n|--调整了：<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n|--|--commonController.php<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n|--|--indexController.php<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n|--|--sortController.php<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n|--|--extendfieldController\r\n</p>\r\n<p style=\"margin-top:0px;margin-bottom:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	2、覆盖protected/apps/admin/view中文件<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n|--调整了：<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n|--|--所有栏目添加页面。栏目添加使用ajax方式加载<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n|--|--图集和栏目内容管理相关页面，解决自定义下拉菜单问题、增加多选框字段类型<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n|--添加了<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n|--|--sort_add.php,所有栏目添加在这个模板中ajax调用<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n|--|--独立表单相关页面\r\n</p>\r\n<p style=\"margin-top:0px;margin-bottom:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	3、覆盖public/codeEditor中codemirror.css文件\r\n</p>\r\n<p style=\"margin-top:0px;margin-bottom:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	4、覆盖protected/apps/default/controller中文件<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n|--调整了：<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n|--|--commonController.php<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n|--新增了：<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n|--|--extendController.php用于自定义表\r\n</p>\r\n<p style=\"margin-top:0px;margin-bottom:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	5、增加protected/apps/default/view/default/extend_index.php<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n|--自定义表单模板\r\n</p>\r\n<p style=\"margin-top:0px;margin-bottom:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	6、public/default/default/default.css中增加了自定义表模板中列表样式<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n|--修改过默认模板样式的童靴自行调整\r\n</p>\r\n<p style=\"margin-top:0px;margin-bottom:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	7、增加data/db_back/1367055300，并在后台数据库备份处还原该备份(2013-04-27 17:35:00)<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n|--该备份是method表备份。如果安装系统时不是yx_默认前缀请先打开备份文件，修改前缀。\r\n</p>', 'news/content', 'news_content', '0', '0', '30', '1', '原创', '1367166040', '0');
INSERT INTO `yx_news` VALUES ('17', ',000000,100023,100029', 'admin', 'YXcmsApp 1.1.4正式版发布', '', 'NoPic.gif', '下载,模板,字符,在线,问题,编辑,后台,覆盖,修改,文件,一个,修正,那些,发布,正式版,生命,重新,删除,中午', '请为那些逝去的生命默哀~\r请4月21日 中午12点前下载的童靴删除 重新下载下，修正了一个模板在线编辑的字符转义问题\r后台中模板文件修改不起效的童请靴覆盖protected/apps/admin/controller/setCon', '<p style=\"margin-top:0px;margin-bottom:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	请为那些逝去的生命默哀~\r\n</p>\r\n<p style=\"margin-top:0px;margin-bottom:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	<span style=\"margin:0px;padding:0px;list-style:none;border:0px;background-color:#FFE500;\">请4月21日 中午12点前下载的童靴删除&nbsp;</span><span style=\"margin:0px;padding:0px;list-style:none;border:0px;background-color:#FFE500;\">重新下载下，</span><span style=\"margin:0px;padding:0px;list-style:none;border:0px;background-color:#FFE500;\">修正了一个模板在线编辑的字符转义问题</span>\r\n</p>\r\n<p style=\"margin-top:0px;margin-bottom:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	<span style=\"margin:0px;padding:0px;list-style:none;border:0px;background-color:#FFE500;\">后台中模板文件修改不起效的童请靴覆盖protected/apps/admin/controller/setController.php</span>\r\n</p>\r\n<p style=\"margin-top:0px;margin-bottom:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	1.1.4 版升级日志<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n-------------------------------------------------------------<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n1、Bugfix：后台修正split PHP版本兼容问题<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n2、Bugfix：修正前台设置curl规则后搜索翻页链接返回首页问题<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n3、Bugfix：修正栏目预览异常问题<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n4、新增：增加前台头部幻灯后台管理（资讯方式）<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n---------------------------------------------<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n5、Bugfix：修正URL参数XSS漏洞<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n6、新增：后台会员权限列表增加游客组<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n7、Bugfix：修正后台会员管理组翻页错误<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n8、Bugfix：修正图集和资讯拓展字段名称不同的问题<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n9、Bugfix：修正PHP 5.1版本图集上传路径错误问题<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n10、改善：设置模板保护，直接访问模板文件将不会显示<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n11、Bugfix：修正卸载“安装向导”应用后后无法正常访问前台的问题<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n12、新增：增加导入导出应用数据库同步操作<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n13、改善：图集和资讯“添加时间”字段由字符串改为Linux时间戳<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n14、新增：拓展表“大型文本”字段增加编辑器管理<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n15、新增：后台增加前台模板在线增、删、改功能\r\n</p>\r\n<p style=\"margin-top:0px;margin-bottom:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	-------------------------------------------------------------\r\n</p>\r\n<p style=\"margin-top:0px;margin-bottom:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	1.1.3.5到1.1.4版升级方法<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n--------------------------------------------------<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n1.1.4正式版修改内容较多,已经完成的项目在不影响使用情况下可不必升级<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n1、覆盖除protected/config.php文件、protected/apps/default/view文件夹和public/default文件夹外其他所有部分。<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n2、将news和photo表中addtime字段改为int(11)类型<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n3、protected/apps/default/view中所有模板文件头部增加“<span style=\"margin:0px;padding:0px;list-style:none;border:0px;\">&lt; ? php if(!defined(\'APP_NAME\')) exit; ? &gt;</span>”<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n4、protected/apps/default/view中所有模板文件调用到“addtime”字段的，以时间戳方式替换（参见1.1.4前台模板）\r\n</p>', 'news/content', 'news_content', '0', '0', '30', '1', '原创', '1367166094', '0');
INSERT INTO `yx_news` VALUES ('18', ',000000,100023,100029', 'admin', '1.1.4正式版4月20日之前将发布', '', 'NoPic.gif', '各种,提供,升级,设备,模板,方法,程序,届时,改版,免费,永久,移动,兼容,修正,发布,之前,正式版,常规,累积,用户,专门,问题,台式', '1.1.4除修正各种常规累积问题外还专门为vip用户提供一套兼容各种台式和移动设备的模板。\r将提供1.1.3至1.1.4的升级方法。\r程序永久开源免费。\r官网届时将改版。', '<p style=\"margin-top:0px;margin-bottom:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	1.1.4除修正各种常规累积问题外还专门为vip用户提供一套兼容各种台式和移动设备的模板。\r\n</p>\r\n<p style=\"margin-top:0px;margin-bottom:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	将提供1.1.3至1.1.4的升级方法。\r\n</p>\r\n<p style=\"margin-top:0px;margin-bottom:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	程序永久开源免费。\r\n</p>\r\n<p style=\"margin-top:0px;margin-bottom:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	官网届时将改版。\r\n</p>', 'news/content', 'news_content', '0', '0', '30', '1', '原创', '1367166126', '0');
INSERT INTO `yx_news` VALUES ('19', ',000000,100023,100029', 'admin', 'YXcmsApp 1.1.3.5 紧急发布', '', 'NoPic.gif', '修正,版本,发布,问题,紧急,后台,日志,函数,兼容,前台,升级,包含,一些,中间,重要,一个,其中', '紧急发布一个中间版本是为修正一些重要的问题，其中包含一项安全漏洞\r1.1.3.5 版升级日志\r-------------------------------------------------------------\r1、后台修正split函数,PHP版本兼容问题\r2、修正前台', '紧急发布一个中间版本是为修正一些重要的问题，其中包含一项安全漏洞\r\n<p style=\"margin-top:0px;margin-bottom:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	1.1.3.5 版升级日志<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n-------------------------------------------------------------<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n1、后台修正split函数,PHP版本兼容问题<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n2、修正前台设置curl规则后，搜索结果翻页返回首页问题<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n3、修正栏目预览异常问题<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n4、增加前台头部幻灯后台管理（资讯方式）<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n5、修正XSS漏洞<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n6、后台会员权限增加默认游客组管理\r\n</p>\r\n<p style=\"margin-top:0px;margin-bottom:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	-------------------------------------------------------------\r\n</p>', 'news/content', 'news_content', '0', '0', '30', '1', '原创', '1367166145', '0');
INSERT INTO `yx_news` VALUES ('20', ',000000,100023,100029', 'admin', 'YXcmsApp 1.1.3发布', '', '20130429/thumb_1367166230.jpg', '不能,问题,修正,索引,部分,正常,系统,配置,安装,日志,升级,发布,开启,静态,设置', '1.1.3 版升级日志\r\n1、修正开启静态缓存后，设置curl不能起效的问题。\r\n2、修正索引字段过长在部分mysql配置下不能正常安装的问题。\r\n3、系统默试模式，修正报错没有提示显示空白的问题。', '<p style=\"margin-top:0px;margin-bottom:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	1.1.3 版升级日志<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n1、修正开启静态缓存后，设置curl不能起效的问题。<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n2、修正索引字段过长在部分mysql配置下不能正常安装的问题。<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n3、系统默认开启调试模式，修正报错没有提示显示空白的问题。<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n4、系统安装配置填写增加不能留空的判断。<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n5、后台栏目列表、资讯列表、图集列表增加点击标题预览功能。<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n6、纠正碎片描述文字和留言回复框过小的问题。<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n7、纠正后台‘前进’ ‘后退’功能键相反的问题。<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n8、修正IE6下默认前台头部错位问题。<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n9、新增chm格式使用手册\r\n</p>\r\n<p style=\"margin-top:0px;margin-bottom:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	--------------------------------------------------------------<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n1.1.2到1.1.3版升级方法<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n-------------------------------------------------------------<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n1、覆盖protectedappsadmin下controller文件夹和view文件夹<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n2、覆盖publicdefaultdefaultcss下default.css文件<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n3、覆盖protectedappsinstall下db.sql文件<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n4、覆盖publicadmincss下back.css文件<br style=\"margin:0px;padding:0px;list-style:none;border:0px;\" />\r\n5、进入后台-》网站配置开启‘调试模式’，若关闭此项系统所有报错将不显示并返回404状态。\r\n</p>\r\n<p style=\"margin-top:0px;margin-bottom:0px;padding:0px;list-style:none;font-family:\'Microsoft YaHei\', \'lucida grande\', verdana, lucida, STSong, sans-serif;border:0px;line-height:20px;white-space:normal;\">\r\n	-------------------------------------------------------------\r\n</p>', 'news/content', 'news_content', '0', '1', '30', '1', '原创', '1367166175', '0');
INSERT INTO `yx_news` VALUES ('21', ',000000,100030', 'admin', '和风熏柳，花香醉人，正是南国春光漫烂季节', '', 'NoPic.gif', '西门,和风,春光,季节,正是,建构,之前,一座,左右,大街,福建省,出去,直通', '和风熏柳，花香醉人，正是南国春光漫烂季节。福建省福州府西门大街，青石板路笔直的伸展出去，直通西门。一座建构宏伟的宅第之前，左右两座石坛中各竖一根两丈来高的旗杆，杆顶飘', '和风熏柳，花香醉人，正是南国春光漫烂季节。福建省福州府西门大街，青石板路笔直的伸展出去，直通西门。一座建构宏伟的宅第之前，左右两座石坛中各竖一根两丈来高的旗杆，杆顶飘扬青旗。右首旗上黄色丝线绣着一头张牙舞爪、神态威猛的雄狮，旗子随风招展，显得雄狮更奕奕若生。雄狮头顶有一对黑丝线绣的蝙蝠展翅飞翔。左首旗上绣着“福威镖局”四个黑字，银钩铁划，刚劲非凡。大宅朱漆大门，门上茶杯大小的铜钉闪闪发光，门顶匾额写着“福威镖局”四个金漆大字，下面横书“总号”两个小字。进门处两排长凳，分坐着八名劲装结束的汉子，个个腰板笔挺，显出一股英悍之气。<br />\r\n<br />\r\n　　突然间后院马蹄声响，那八名汉子一齐站起，抢出大门。只见镖局西侧门中冲出五骑马来，沿着马道冲到大门之前。当先一匹马全身雪白，马勒脚镫都是烂银打就，鞍上一个锦衣少年，约莫十八九岁年纪，左肩上停着一头猎鹰，腰悬宝剑，背负长弓，泼喇喇纵马疾驰。身后跟随四骑，骑者一色青布短衣。一行五人驰到镖局门口，八名汉子中有三个齐声叫了起来：“少镖头又打猎去啦！”那少年哈哈一笑，马鞭在空中拍的一响，虚击声下，胯下白马昂首长嘶，在青石板大路上冲了出去。一名汉子叫道：“史镖头，今儿再抬头野猪回来，大伙儿好饱餐一顿。”那少年身后一名四十来岁的汉子笑道：“一条野猪尾巴少不了你的，可先别灌饱了黄汤。”众人大笑声中，五骑马早去得远了。<br />', 'news/content', 'news_content', '0', '0', '30', '1', '原创', '1367166330', '0');
INSERT INTO `yx_news` VALUES ('22', ',000000,100030', 'admin', '五骑马一出城门，少镖头林平之双腿轻轻一挟', '', 'NoPic.gif', '轻轻,马上,远远,出来,出去,之间,后面', '　　五骑马一出城门，少镖头林平之双腿轻轻一挟，白马四蹄翻腾，直抢出去，片刻之间，便将后面四骑远远抛离。他纵马上了山坡，放起猎鹰，从林中赶了一对黄兔出来。他取下背上长弓', '<br />\r\n　　五骑马一出城门，少镖头林平之双腿轻轻一挟，白马四蹄翻腾，直抢出去，片刻之间，便将后面四骑远远抛离。他纵马上了山坡，放起猎鹰，从林中赶了一对黄兔出来。他取下背上长弓，从鞍旁箭袋中取出一支雕翎，弯弓搭箭，刷的一声响，一头黄兔应声而倒，待要再射时，另一头兔却钻入草丛中不见了。郑镖头纵马赶到，笑道：“少镖头，好箭！”只听得趟子手白二在左首林中叫道：“少镖头，快来，这里有野鸡！”林平之纵马过去，只见林中飞出一只雉鸡，林平之刷的一箭，那野鸡对正了从他头顶飞来，这一箭竟没射中。林平之急提马鞭向半空中抽去，劲力到处，波的一声响，将那野鸡打了下来，五色羽毛四散飞舞。五人齐声大笑。史镖头道：“少镖头这一鞭，别说野鸡，便大兀鹰也打下来了！”五人在林中追逐鸟兽，史、郑两名镖头和趟子手白二、陈七凑少镖头的兴，总是将猎物赶到他身前，自己纵有良机，也不下手。打了两个多时辰，林平之又射了两只兔子，两只雉鸡，只是没打到野猪和獐子之类的大兽，兴犹未足，说道：“咱们到前边山里再找找去。”<br />', 'news/content', 'news_content', '0', '0', '30', '1', '原创', '1367166364', '0');
INSERT INTO `yx_news` VALUES ('23', ',000000,100030', 'admin', '果然一听说怕伤马蹄，林平之便拍', '', 'NoPic.gif', '听说,不行,人大,不过,聪明,不会,你们', '果然一听说怕伤马蹄，林平之便拍了拍马头，道：“我这小雪龙聪明得紧，决不会踏到尖石，不过你们这四匹马却怕不行。好，大伙儿都回去吧，可别摔破了陈七的屁股。”五人大笑声中，', '果然一听说怕伤马蹄，林平之便拍了拍马头，道：“我这小雪龙聪明得紧，决不会踏到尖石，不过你们这四匹马却怕不行。好，大伙儿都回去吧，可别摔破了陈七的屁股。”五人大笑声中，兜转马头。林平之纵马疾驰，却不沿原路回去，转而向北，疾驰一阵，这才尽兴，勒马缓缓而行。只见前面路旁挑出一个酒招子。郑镖头道：“少镖头，咱们去喝一杯怎么样？新鲜兔肉、野鸡肉，正好炒了下酒。”林平之笑道：“你跟我出来打猎是假，喝酒才是正经事。若不请你喝上个够，明儿便懒洋洋的不肯跟我出来了。”一勒马，飘身跃下马背，缓步走向酒肆。若在往日，店主人老蔡早已抢出来接他手中马缰：“少镖头今儿打了这么多野味啊，当真箭法如神，当世少有！”这么奉承一番。但此刻来到店前，酒店中却静悄悄地，只见酒炉旁有个青衣少女，头束双鬟，插着两支荆钗，正在料理酒水，脸儿向里，也不转过身来。郑镖头叫道：“老蔡呢，怎么不出来牵马？”白二、陈七拉开长凳，用衣袖拂去灰尘，请林平之坐了。史郑二位镖头在下首相陪，两个趟子手另坐一席。内堂里咳嗽声响，走出一个白发老人来，说道：“客官请坐，喝酒么？”说的是北方口音。郑镖头道：“不喝酒，难道还喝茶？先打三斤竹叶青上来。老蔡哪里去啦？怎么？这酒店换了老板么？”那老人道：“是，是，宛儿，打三斤竹叶青。不瞒众位客官说，小老儿姓萨，原是本地人氏，自幼在外做生意，儿子媳妇都死了，心想树高千丈，叶落归根，这才带了这孙女儿回故乡来。哪知道离家四十多年，家乡的亲戚朋友一个都不在了。刚好这家酒店的老蔡不想干了，三十两银子卖了给小老儿。唉，总算回到故乡啦，听着人人说这家乡话，心里就说不出的受用，惭愧得紧，小老儿自己可都不会说啦。”那青衣少女低头托着一只木盘，在林平之等人面前放了杯筷，将三壶酒放在桌上，又低着头走了开去，始终不敢向客人瞧上一眼。林平之见这少女身形婀娜，肤色却黑黝黝地甚是粗糙，脸上似有不少痘瘢，容貌甚丑，想是她初做这卖酒勾当，举止甚是生硬，当下也不在意。', 'news/content', 'news_content', '0', '0', '30', '1', '原创', '1367166391', '0');
INSERT INTO `yx_news` VALUES ('24', ',000000,100030', 'admin', '承接网站制作，带售后服务', '', 'NoPic.gif', '承接,售后服务,网站制作,需要,联系', '承接网站制作，带售后服务。需要的联系QQ286084160', '承接网站制作，带售后服务。需要的联系QQ286084160', 'news/content', 'news_content', '0', '0', '30', '1', '原创', '1367166419', '0');
INSERT INTO `yx_news` VALUES ('25', ',000000,100030', 'admin', '宛儿低头走到两人桌前，低声问道', '', 'NoPic.gif', '可惜,突然,声音,十分,年轻', '宛儿低头走到两人桌前，低声问道：“要甚么酒？”声音虽低，却十分清脆动听。那年轻汉子一怔，突然伸出右手，托向宛儿的下颏，笑道：“可惜，可惜！”宛儿吃了一惊，急忙退后。另', '宛儿低头走到两人桌前，低声问道：“要甚么酒？”声音虽低，却十分清脆动听。那年轻汉子一怔，突然伸出右手，托向宛儿的下颏，笑道：“可惜，可惜！”宛儿吃了一惊，急忙退后。另一名汉子笑道：“余兄弟，这花姑娘的身材硬是要得，一张脸蛋嘛，却是钉鞋踏烂泥，翻转石榴皮，格老子好一张大麻皮。”那姓余的哈哈大笑。<br />\r\n<br />\r\n　　林平之气往上冲，伸右手往桌上重重一拍，说道：“甚么东西，两个不带眼的狗崽子，却到我们福州府来撒野！”那姓余的年轻汉子笑道：“贾老二，人家在骂街哪，你猜这兔儿爷是在骂谁？”林平之相貌像他母亲，眉清目秀，甚是俊美，平日只消有哪个男人向他挤眉弄眼的瞧上一眼，势必一个耳光打了过去，此刻听这汉子叫他“兔儿爷”，哪里还忍耐得住？提起桌上的一把锡酒壶，兜头摔将过去。那姓余汉子一避，锡酒壶直摔到酒店门外的草地上，酒水溅了一地。史镖头和郑镖头站起身来，抢到那二人身旁。<br />\r\n<br />\r\n　　那姓余的笑道：“这小子上台去唱花旦，倒真勾引得人，要打架可还不成！”郑镖头喝道：“这位是福威镖局的林少镖头，你天大胆子，到太岁头上动土？”这“土”字刚出口，左手一拳已向他脸上猛击过去。那姓余汉子左手上翻，搭上了郑镖头的脉门，用力一拖，郑镖头站立不定，身子向板桌急冲。那姓余汉子左肘重重往下一顿，撞在郑镖头的后颈。喀喇喇一声，郑镖头撞垮了板桌，连人带桌的摔倒。郑镖头在福威镖局之中虽然算不得是好手，却也不是脓包脚色，史镖头见他竟被这人一招之间便即撞倒，可见对方颇有来头，问道：“尊驾是谁？既是武林同道，难道就不将福威镖局瞧在眼里么？”那姓余汉子冷笑道：“福威镖局？从来没听见过！那是干甚么的？”<br />', 'news/content', 'news_content', '0', '0', '30', '1', '原创', '1367166459', '0');
INSERT INTO `yx_news` VALUES ('26', ',000000,100027', 'admin', '卫浴间家具如何防水', '', 'NoPic.gif', '卫浴,防水,家具,如何', '卫浴间家具如何防水卫浴间家具如何防水卫浴间家具如何防水卫浴间家具如何防水卫浴间家具如何防水卫浴间家具如何防水卫浴间家具如何防水卫浴间家具如何防水卫浴间家具如何防水卫浴', '卫浴间家具如何防水卫浴间家具如何防水卫浴间家具如何防水卫浴间家具如何防水卫浴间家具如何防水卫浴间家具如何防水卫浴间家具如何防水卫浴间家具如何防水卫浴间家具如何防水卫浴间家具如何防水卫浴间家具如何防水', 'news/content', 'news_content', '0', '0', '30', '1', '原创', '1367166487', '0');
INSERT INTO `yx_news` VALUES ('27', ',000000,100027', 'admin', '板式及实木家具如何远离潮湿', '', 'NoPic.gif', '远离,如何,家具,实木', '板式及实木家具如何远离潮湿', '板式及实木家具如何远离潮湿<br />', 'news/content', 'news_content', '0', '0', '30', '1', '原创', '1367166539', '0');
INSERT INTO `yx_news` VALUES ('28', ',000000,100027', 'admin', '低矮家具伤脊椎', '', 'NoPic.gif', '家具', '低矮家具伤脊椎', '低矮家具伤脊椎', 'news/content', 'news_content', '0', '0', '30', '1', '原创', '1367166567', '0');
INSERT INTO `yx_news` VALUES ('29', ',000000,100027', 'admin', '浴室柜选购诀窍', '', 'NoPic.gif', '选购,浴室', '浴室柜选购诀窍', '浴室柜选购诀窍', 'news/content', 'news_content', '0', '0', '30', '1', '原创', '1367166579', '0');
INSERT INTO `yx_news` VALUES ('30', ',000000,100027', 'admin', '大理石家具选购指南', '', 'NoPic.gif', '选购指南,家具', '大理石家具选购指南', '大理石家具选购指南', 'news/content', 'news_content', '0', '0', '30', '1', '原创', '1367166591', '0');
INSERT INTO `yx_news` VALUES ('31', ',000000,100027', 'admin', '喷漆家具如何保养', '', 'NoPic.gif', '保养,如何,家具', '喷漆家具如何保养', '喷漆家具如何保养', 'news/content', 'news_content', '0', '0', '30', '1', '原创', '1367166599', '0');
INSERT INTO `yx_news` VALUES ('32', ',000000,100032', 'admin', '这里测试第一张幻灯片', '', '20130429/thumb_1367168429.jpg', '11111', '11111', '11111', 'news/content', 'news_content', '0', '0', '30', '1', '原创', '1367168425', '0');
INSERT INTO `yx_news` VALUES ('33', ',000000,100032', 'admin', '22222222', '', '20130429/thumb_1367168453.jpg', '2222222', '2222222', '2222222', 'news/content', 'news_content', '0', '0', '30', '1', '原创', '1367168432', '0');
INSERT INTO `yx_news` VALUES ('34', ',000000,100032', 'admin', '33333333', '', '20130429/thumb_1367168467.jpg', '33333333', '33333333', '33333333', 'news/content', 'news_content', '0', '0', '30', '1', '原创', '1367168455', '0');
INSERT INTO `yx_news` VALUES ('35', ',000000,100032', 'admin', '这里测试第四张幻灯片', '', '20130429/thumb_1367168487.jpg', '444444444', '444444444', '444444444', 'news/content', 'news_content', '0', '0', '30', '1', '原创', '1367168474', '0');
INSERT INTO `yx_news` VALUES ('36', ',000000,100032', 'admin', '这里测试第五张幻灯片', '', '20130429/thumb_1367168507.jpg', '55555555555555', '55555555555555', '55555555555555', 'news/content', 'news_content', '0', '0', '30', '1', '原创', '1367168492', '0');
INSERT INTO `yx_news` VALUES ('37', ',000000,100033', 'admin', '这里测试系统公告一', '', 'NoPic.gif', '公告,这里', '这里测试系统公告这里测试系统公告这里测试系统公告这里测试系统公告这里测试系统公告这里测试系统公告这里测试系统公告这里测试系统公告', '这里测试系统公告这里测试系统公告这里测试系统公告这里测试系统公告这里测试系统公告这里测试系统公告这里测试系统公告这里测试系统公告', 'news/content', 'news_content', '0', '0', '30', '1', '原创', '1367169078', '0');
INSERT INTO `yx_news` VALUES ('38', ',000000,100033', 'admin', '这里测试系统公告二', '', 'NoPic.gif', '公告,这里', '这里测试系统公告这里测试系统公告这里测试系统公告这里测试系统公告这里测试系统公告这里测试系统公告', '这里测试系统公告这里测试系统公告这里测试系统公告这里测试系统公告这里测试系统公告这里测试系统公告', 'news/content', 'news_content', '0', '0', '30', '1', '原创', '1367169094', '0');
INSERT INTO `yx_news` VALUES ('39', ',000000,100033', 'admin', '这里测试系统公告三', '', 'NoPic.gif', '公告,这里', '这里测试系统公告这里测试系统公告这里测试系统公告这里测试系统公告这里测试系统公告这里测试系统公告', '这里测试系统公告这里测试系统公告这里测试系统公告这里测试系统公告这里测试系统公告这里测试系统公告', 'news/content', 'news_content', '0', '0', '31', '1', '原创', '1367169106', '0');

-- ----------------------------
-- Table structure for `yx_orders`
-- ----------------------------
DROP TABLE IF EXISTS `yx_orders`;
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

-- ----------------------------
-- Records of yx_orders
-- ----------------------------

-- ----------------------------
-- Table structure for `yx_order_detail`
-- ----------------------------
DROP TABLE IF EXISTS `yx_order_detail`;
CREATE TABLE `yx_order_detail` (
  `id` int(20) NOT NULL auto_increment,
  `code` varchar(10) NOT NULL COMMENT '商品编号',
  `ordernum` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` float NOT NULL,
  `num` int(5) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of yx_order_detail
-- ----------------------------

-- ----------------------------
-- Table structure for `yx_page`
-- ----------------------------
DROP TABLE IF EXISTS `yx_page`;
CREATE TABLE `yx_page` (
  `id` int(10) NOT NULL auto_increment,
  `sort` varchar(350) NOT NULL,
  `content` text NOT NULL,
  `edittime` varchar(20) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of yx_page
-- ----------------------------
INSERT INTO `yx_page` VALUES ('2', ',000000,100022', '关于我们关于我们关于我们关于我们关于我们关于我们关于我们关于我们<span style=\"white-space:normal;\">关于我们</span><span style=\"white-space:normal;\">关于我们</span><span style=\"white-space:normal;\">关于我们</span><span style=\"white-space:normal;\">关于我们</span><span style=\"white-space:normal;\">关于我们</span><span style=\"white-space:normal;\">关于我们</span><span style=\"white-space:normal;\">关于我们</span><span style=\"white-space:normal;\">关于我们<span style=\"white-space:normal;\">关于我们</span><span style=\"white-space:normal;\">关于我们</span><span style=\"white-space:normal;\">关于我们</span><span style=\"white-space:normal;\">关于我们</span><span style=\"white-space:normal;\">关于我们</span><span style=\"white-space:normal;\">关于我们</span><span style=\"white-space:normal;\">关于我们</span><span style=\"white-space:normal;\">关于我们<span style=\"white-space:normal;\">关于我们</span><span style=\"white-space:normal;\">关于我们</span><span style=\"white-space:normal;\">关于我们</span><span style=\"white-space:normal;\">关于我们</span><span style=\"white-space:normal;\">关于我们</span><span style=\"white-space:normal;\">关于我们</span><span style=\"white-space:normal;\">关于我们</span><span style=\"white-space:normal;\">关于我们<span style=\"white-space:normal;\">关于我们</span><span style=\"white-space:normal;\">关于我们</span><span style=\"white-space:normal;\">关于我们</span><span style=\"white-space:normal;\">关于我们</span><span style=\"white-space:normal;\">关于我们</span><span style=\"white-space:normal;\">关于我们</span><span style=\"white-space:normal;\">关于我们</span><span style=\"white-space:normal;\">关于我们<span style=\"white-space:normal;\">关于我们</span><span style=\"white-space:normal;\">关于我们</span><span style=\"white-space:normal;\">关于我们</span><span style=\"white-space:normal;\">关于我们</span><span style=\"white-space:normal;\">关于我们</span><span style=\"white-space:normal;\">关于我们</span><span style=\"white-space:normal;\">关于我们</span><span style=\"white-space:normal;\">关于我们<span style=\"white-space:normal;\">关于我们</span><span style=\"white-space:normal;\">关于我们</span><span style=\"white-space:normal;\">关于我们</span><span style=\"white-space:normal;\">关于我们</span><span style=\"white-space:normal;\">关于我们</span><span style=\"white-space:normal;\">关于我们</span><span style=\"white-space:normal;\">关于我们</span><span style=\"white-space:normal;\">关于我们</span></span></span></span></span></span>', '2013-04-29 00:10:16');
INSERT INTO `yx_page` VALUES ('3', ',000000,100022,100025', '联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们', '2013-04-29 00:11:30');
INSERT INTO `yx_page` VALUES ('4', ',000000,100022,100031', '公司简介公司简介公司简介公司简介公司简介', '2013-04-29 00:42:58');

-- ----------------------------
-- Table structure for `yx_photo`
-- ----------------------------
DROP TABLE IF EXISTS `yx_photo`;
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

-- ----------------------------
-- Records of yx_photo
-- ----------------------------
INSERT INTO `yx_photo` VALUES ('13', ',000000,100024,100034', 'admin', '还有一些其它的分类方法', '', '130429123918990241470.jpg ', '方法,分类,其它,一些,还有', '还有一些其它的分类方法\r\n还有一些其它的分类方法\r\n还有一些其它的分类方法\r\n还有一些其它的分类方法\r\n还有一些其它的分类方法\r\n还有一些其它的分类方法', '1304291239181146549645.jpg ,1304291239181073610404.jpg ,130429123918990241470.jpg ,130429123918496394035.jpg ', '还有一些其它的分类方法,还有一些其它的分类方法,还有一些其它的分类方法,还有一些其它的分类方法', '&lt;pre id=&quot;recommend-content-1063473045&quot; class=&quot;recommend-text mb-10&quot;&gt;还有一些其它的分类方法\r\n&lt;pre id=&quot;recommend-content-1063473045&quot; class=&quot;recommend-text mb-10&quot;&gt;还有一些其它的分类方法\r\n&lt;pre id=&quot;recommend-content-1063473045&quot; class=&quot;recommend-text mb-10&quot;&gt;还有一些其它的分类方法\r\n&lt;pre id=&quot;recommend-content-1063473045&quot; class=&quot;recommend-text mb-10&quot;&gt;还有一些其它的分类方法\r\n&lt;pre id=&quot;recommend-content-1063473045&quot; class=&quot;recommend-text mb-10&quot;&gt;还有一些其它的分类方法\r\n&lt;pre id=&quot;recommend-content-1063473045&quot; class=&quot;recommend-text mb-10&quot;&gt;还有一些其它的分类方法&lt;/pre&gt;\r\n&lt;/pre&gt;\r\n&lt;/pre&gt;\r\n&lt;/pre&gt;\r\n&lt;/pre&gt;\r\n&lt;/pre&gt;', 'photo/content', 'photo_content', '0', '1', '30', '1', '1367167144', '0');
INSERT INTO `yx_photo` VALUES ('12', ',000000,100024,100034', 'admin', '家具的种类根据不同的分类方法有所不同', '', '1304291238461613400623.jpg ', '家具,根据,不同,种类,有所不同,分类,方法', '家具的种类根据不同的分类方法有所不同\r\n家具的种类根据不同的分类方法有所不同\r\n家具的种类根据不同的分类方法有所不同\r\n家具的种类根据不同的分类方法有所不同\r\n家具的种类根据不同的', '130429123846190439528.jpg ,1304291238461613400623.jpg ,130429123846989690345.jpg ,1304291238471349493922.png ', '家具的种类根据不同的分类方法有所不同,家具的种类根据不同的分类方法有所不同,家具的种类根据不同的分类方法有所不同,家具的种类根据不同的分类方法有所不同', '&lt;pre id=&quot;recommend-content-1063473045&quot; class=&quot;recommend-text mb-10&quot;&gt;家具的种类根据不同的分类方法有所不同\r\n&lt;pre id=&quot;recommend-content-1063473045&quot; class=&quot;recommend-text mb-10&quot;&gt;家具的种类根据不同的分类方法有所不同\r\n&lt;pre id=&quot;recommend-content-1063473045&quot; class=&quot;recommend-text mb-10&quot;&gt;家具的种类根据不同的分类方法有所不同\r\n&lt;pre id=&quot;recommend-content-1063473045&quot; class=&quot;recommend-text mb-10&quot;&gt;家具的种类根据不同的分类方法有所不同\r\n&lt;pre id=&quot;recommend-content-1063473045&quot; class=&quot;recommend-text mb-10&quot;&gt;家具的种类根据不同的分类方法有所不同&lt;/pre&gt;\r\n&lt;/pre&gt;\r\n&lt;/pre&gt;\r\n&lt;/pre&gt;\r\n&lt;/pre&gt;', 'photo/content', 'photo_content', '0', '1', '30', '1', '1367167115', '0');
INSERT INTO `yx_photo` VALUES ('10', ',000000,100024,100034', 'admin', '软体家具软体家具', '', '130429123710682081664.jpg ', '家具', '软体家具\r\n软体家具\r\n软体家具\r\n软体家具\r\n软体家具\r\n软体家具\r\n软体家具', '1304291237101010752522.jpg ,13042912371014340486.jpg ,130429123710682081664.jpg ,1304291237311718275161.jpg ', '软体家具,软体家具,软体家具,软体家具', '&lt;pre id=&quot;recommend-content-1063473045&quot; class=&quot;recommend-text mb-10&quot;&gt;软体家具\r\n&lt;pre id=&quot;recommend-content-1063473045&quot; class=&quot;recommend-text mb-10&quot;&gt;软体家具\r\n&lt;pre id=&quot;recommend-content-1063473045&quot; class=&quot;recommend-text mb-10&quot;&gt;软体家具\r\n&lt;pre id=&quot;recommend-content-1063473045&quot; class=&quot;recommend-text mb-10&quot;&gt;软体家具\r\n&lt;pre id=&quot;recommend-content-1063473045&quot; class=&quot;recommend-text mb-10&quot;&gt;软体家具\r\n&lt;pre id=&quot;recommend-content-1063473045&quot; class=&quot;recommend-text mb-10&quot;&gt;软体家具\r\n&lt;pre id=&quot;recommend-content-1063473045&quot; class=&quot;recommend-text mb-10&quot;&gt;软体家具&lt;/pre&gt;\r\n&lt;/pre&gt;\r\n&lt;/pre&gt;\r\n&lt;/pre&gt;\r\n&lt;/pre&gt;\r\n&lt;/pre&gt;\r\n&lt;/pre&gt;', 'photo/content', 'photo_content', '0', '1', '30', '1', '1367167020', '0');
INSERT INTO `yx_photo` VALUES ('11', ',000000,100024,100034', 'admin', '金属家具金属家具', '', '1304291238091648570584.jpg ', '家具,金属', '金属家具金属家具金属家具金属家具金属家具金属家具金属家具金属家具金属家具金属家具金属家具金属家具金属家具金属家具金属家具金属家具金属家具金属家具', '130429123808989760841.jpg ,1304291238081948615963.jpg ,130429123809125499456.jpg ,130429123809324065626.png ,1304291238091648570584.jpg ,1304291238091763159904.jpg ', '金属家具金属家具,金属家具金属家具,金属家具金属家具,金属家具金属家具,金属家具金属家具,金属家具金属家具', '金属家具金属家具金属家具金属家具金属家具金属家具金属家具金属家具金属家具金属家具金属家具金属家具金属家具金属家具金属家具金属家具金属家具金属家具', 'photo/content', 'photo_content', '0', '1', '30', '1', '1367167073', '0');
INSERT INTO `yx_photo` VALUES ('9', ',000000,100024,100034', 'admin', '板式家具板式家具', '', '130429123559148198446.jpg ', '板式家具板式家具板式家具板式家具板式家具板式家具板式家具板式家具板式家具板式家具板式家具板式家具板式家具板式家具板式家具板式家具板式家具板式家具板式家具板式家具', '板式家具板式家具板式家具板式家具板式家具板式家具板式家具板式家具板式家具板式家具板式家具板式家具板式家具板式家具板式家具板式家具板式家具板式家具板式家具板式家具', '130429123559945090721.jpg ,130429123559866059245.jpg ,1304291235591630222158.jpg ,130429123559148198446.jpg ', '板式家具,板式家具,板式家具,板式家具', '&lt;pre id=&quot;recommend-content-1063473045&quot; class=&quot;recommend-text mb-10&quot;&gt;板式家具板式家具板式家具板式家具板式家具板式家具板式家具板式家具板式家具板式家具板式家具板式家具板式家具板式家具板式家具板式家具板式家具板式家具板式家具板式家具\r\n&lt;pre id=&quot;recommend-content-1063473045&quot; class=&quot;recommend-text mb-10&quot;&gt;\r\n\r\n&lt;pre id=&quot;recommend-content-1063473045&quot; class=&quot;recommend-text mb-10&quot;&gt;\r\n\r\n&lt;pre id=&quot;recommend-content-1063473045&quot; class=&quot;recommend-text mb-10&quot;&gt;\r\n\r\n&lt;pre id=&quot;recommend-content-1063473045&quot; class=&quot;recommend-text mb-10&quot;&gt;&lt;/pre&gt;\r\n&lt;/pre&gt;\r\n&lt;/pre&gt;\r\n&lt;/pre&gt;\r\n&lt;/pre&gt;', 'photo/content', 'photo_content', '0', '0', '30', '1', '1367166941', '0');
INSERT INTO `yx_photo` VALUES ('8', ',000000,100024,100034', 'admin', '实木家具实木家具', '', '1304291235171994899413.png ', '实木,家具', '实木家具实木家具实木家具实木家具实木家具实木家具实木家具实木家具实木家具实木家具实木家具实木家具实木家具实木家具实木家具实木家具实木家具实木家具实木家具实木家具实木家', '1304291235161400231475.jpg ,1304291235161354093773.jpg ,1304291235161522960933.jpg ,1304291235171371789977.jpg ,1304291235171994899413.png ', '实木家具实木家具,实木家具实木家具,实木家具实木家具,实木家具实木家具,实木家具实木家具', '实木家具实木家具实木家具实木家具实木家具实木家具实木家具实木家具实木家具实木家具实木家具实木家具实木家具实木家具实木家具实木家具实木家具实木家具实木家具实木家具实木家具实木家具实木家具实木家具', 'photo/content', 'photo_content', '0', '0', '30', '1', '1367166680', '0');
INSERT INTO `yx_photo` VALUES ('14', ',000000,100024,100034', 'admin', '使生产和销售的家具企业', '', '130429123955851833509.jpg ', '企业,家具,销售,生产', '使生产和销售的家具企业使生产和销售的家具企业使生产和销售的家具企业使生产和销售的家具企业使生产和销售的家具企业', '1304291239541145599569.jpg ,13042912395529266411.png ,130429123955905057949.jpg ,130429123955851833509.jpg ,1304291239551751561226.jpg ', '使生产和销售的家具企业,使生产和销售的家具企业,使生产和销售的家具企业,使生产和销售的家具企业,使生产和销售的家具企业', '使生产和销售的家具企业使生产和销售的家具企业使生产和销售的家具企业使生产和销售的家具企业使生产和销售的家具企业', 'photo/content', 'photo_content', '0', '1', '30', '1', '1367167172', '0');
INSERT INTO `yx_photo` VALUES ('15', ',000000,100024,100034', 'admin', '按家具从风格上可以分为:现代家具', '', '130429124036245261860.jpg ', '家具,风格,现代,可以,分为', '按家具从风格上可以分为:现代家具按家具从风格上可以分为:现代家具按家具从风格上可以分为:现代家具按家具从风格上可以分为:现代家具按家具从风格上可以分为:现代家具按家具从风格', '130429124035124002142.jpg ,130429124036411770329.jpg ,130429124036276842052.jpg ,130429124036223857941.jpg ,130429124036245261860.jpg ', '按家具从风格上可以分为:现代家具,按家具从风格上可以分为:现代家具,按家具从风格上可以分为:现代家具,按家具从风格上可以分为:现代家具,按家具从风格上可以分为:现代家具', '按家具从风格上可以分为:现代家具按家具从风格上可以分为:现代家具按家具从风格上可以分为:现代家具按家具从风格上可以分为:现代家具按家具从风格上可以分为:现代家具按家具从风格上可以分为:现代家具', 'photo/content', 'photo_content', '0', '1', '52', '1', '1367167213', '0');
INSERT INTO `yx_photo` VALUES ('16', ',000000,100024,100035', 'admin', '士大夫撒旦法第三方', '', '130429013237103608951.jpg ', '第三方', '士大夫撒旦法第三方士大夫撒旦法第三方士大夫撒旦法第三方士大夫撒旦法第三方士大夫撒旦法第三方士大夫撒旦法第三方', '1304290132371583131756.jpg ,130429013237103608951.jpg ,1304290132371766036949.jpg ', '000,000,0000', '士大夫撒旦法第三方士大夫撒旦法第三方士大夫撒旦法第三方士大夫撒旦法第三方士大夫撒旦法第三方士大夫撒旦法第三方', 'photo/content', 'photo_content', '0', '0', '31', '1', '1367170344', '0');

-- ----------------------------
-- Table structure for `yx_sort`
-- ----------------------------
DROP TABLE IF EXISTS `yx_sort`;
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

-- ----------------------------
-- Records of yx_sort
-- ----------------------------
INSERT INTO `yx_sort` VALUES ('100022', '3', ',000000', '关于我们', '1', '0', '1', 'page/index', 'page_index', '关于,我们', '关于我们关于我们关于我们关于我们关于我们关于我们关于我们关于我们关于我们关于我们关于我们关于我们关于我们关于我们关于我们关于我们关于我们关于我们关于我们关于我们关于我', '', '0');
INSERT INTO `yx_sort` VALUES ('100027', '1', ',000000', '家具知识', '1', '0', '1', 'news/index', 'news_index', '', '', '10', '0');
INSERT INTO `yx_sort` VALUES ('100028', '1', ',000000,100023', '公司新闻', '2', '0', '1', 'news/index', 'news_index', '', '', '10', '0');
INSERT INTO `yx_sort` VALUES ('100029', '1', ',000000,100023', '行业动态', '2', '0', '1', 'news/index', 'news_index', '', '', '10', '0');
INSERT INTO `yx_sort` VALUES ('100030', '1', ',000000', '售后服务', '1', '0', '1', 'news/index', 'news_index', '', '', '10', '0');
INSERT INTO `yx_sort` VALUES ('100031', '3', ',000000,100022', '公司简介', '2', '0', '1', 'page/index', 'page_index', '公司简介公司简介公司简介公司简介公司简介', '公司简介公司简介公司简介公司简介公司简介', '', '0');
INSERT INTO `yx_sort` VALUES ('100033', '1', ',000000', '系统公告', '1', '0', '0', 'news/index', 'news_index', '', '', '10', '0');
INSERT INTO `yx_sort` VALUES ('100032', '1', ',000000', '幻灯', '1', '0', '0', 'news/index', 'news_index', '', '', '10', '0');
INSERT INTO `yx_sort` VALUES ('100023', '1', ',000000', '新闻资讯', '1', '0', '1', 'news/index', 'news_index', '', '', '10', '0');
INSERT INTO `yx_sort` VALUES ('100024', '2', ',000000', '产品展示', '1', '0', '1', 'photo/index', 'photo_index', '', '', '10', '0');
INSERT INTO `yx_sort` VALUES ('100025', '3', ',000000,100022', '联系我们', '2', '0', '1', 'page/index', 'page_index', '我们,联系', '联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们', '', '0');
INSERT INTO `yx_sort` VALUES ('100026', '1', ',000000', '招贤纳士', '1', '0', '0', 'news/index', 'news_index', '', '', '10', '9');
INSERT INTO `yx_sort` VALUES ('100034', '2', ',000000,100024', '分类一', '2', '0', '1', 'photo/index', 'photo_index', '', '', '10', '0');
INSERT INTO `yx_sort` VALUES ('100035', '2', ',000000,100024', '分类二', '2', '0', '1', 'photo/index', 'photo_index', '', '', '10', '0');
INSERT INTO `yx_sort` VALUES ('100036', '5', ',000000', 'Yxcms', '1', '0', '1', '', '', '', '', 'http://www.yxcms.net', '1');
INSERT INTO `yx_sort` VALUES ('100037', '5', ',000000', '模板下载', '1', '0', '1', '', '', '', '', 'http://www.xiaopangniu.net', '1');
