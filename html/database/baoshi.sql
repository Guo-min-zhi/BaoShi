/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50022
Source Host           : localhost:3306
Source Database       : baoshi

Target Server Type    : MYSQL
Target Server Version : 50022
File Encoding         : 65001

Date: 2014-10-30 15:05:35
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `album`
-- ----------------------------
DROP TABLE IF EXISTS `album`;
CREATE TABLE `album` (
  `id` int(11) NOT NULL auto_increment COMMENT '主键',
  `name` varchar(45) default NULL COMMENT '影集名称',
  `theme` varchar(45) default NULL COMMENT '影集主题',
  `description` varchar(500) default NULL COMMENT '影集描述',
  `time` datetime default NULL COMMENT '创建时间',
  `publish` tinyint(1) default NULL COMMENT '影集是否发布',
  `userId` int(11) default NULL COMMENT '外键，影集所属人',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of album
-- ----------------------------

-- ----------------------------
-- Table structure for `photo`
-- ----------------------------
DROP TABLE IF EXISTS `photo`;
CREATE TABLE `photo` (
  `id` int(11) NOT NULL auto_increment COMMENT '主键',
  `path` varchar(100) default NULL COMMENT '照片路径',
  `time` datetime default NULL COMMENT '照片上传时间',
  `location` varchar(45) default NULL COMMENT '照片地点',
  `description` varchar(500) default NULL COMMENT '照片描述',
  `sceneId` int(11) default NULL COMMENT '照片所属场景',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of photo
-- ----------------------------

-- ----------------------------
-- Table structure for `scene`
-- ----------------------------
DROP TABLE IF EXISTS `scene`;
CREATE TABLE `scene` (
  `id` int(11) NOT NULL COMMENT '主键',
  `name` varchar(45) default NULL COMMENT '场景名称',
  `description` varchar(500) default NULL COMMENT '场景描述',
  `albumId` int(11) default NULL COMMENT '外键，属于哪个影集',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of scene
-- ----------------------------

-- ----------------------------
-- Table structure for `tag`
-- ----------------------------
DROP TABLE IF EXISTS `tag`;
CREATE TABLE `tag` (
  `id` int(11) NOT NULL auto_increment COMMENT '主键',
  `name` varchar(45) default NULL COMMENT 'tag名称',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tag
-- ----------------------------

-- ----------------------------
-- Table structure for `tagphoto`
-- ----------------------------
DROP TABLE IF EXISTS `tagphoto`;
CREATE TABLE `tagphoto` (
  `id` int(11) NOT NULL auto_increment COMMENT '主键',
  `tagId` int(11) default NULL COMMENT '标签外键',
  `photoId` int(11) default NULL COMMENT '照片外键',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tagphoto
-- ----------------------------

-- ----------------------------
-- Table structure for `user`
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL auto_increment COMMENT '主键',
  `email` varchar(45) default NULL COMMENT '邮箱',
  `username` varchar(45) default NULL COMMENT '用户名',
  `password` varchar(45) default NULL COMMENT '密码',
  `head` varchar(100) default NULL COMMENT '头像url',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user
-- ----------------------------
