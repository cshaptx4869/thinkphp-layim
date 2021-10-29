/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 50724
 Source Host           : localhost:3306
 Source Schema         : chat

 Target Server Type    : MySQL
 Target Server Version : 50724
 File Encoding         : 65001

 Date: 29/10/2021 16:23:24
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for chat_friend
-- ----------------------------
DROP TABLE IF EXISTS `chat_friend`;
CREATE TABLE `chat_friend`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `group_id` int(11) UNSIGNED NOT NULL COMMENT '分组id',
  `member_id` int(11) UNSIGNED NOT NULL COMMENT '好友id',
  `nickname` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '好友昵称',
  `create_time` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `update_time` timestamp(0) NULL DEFAULT NULL,
  `delete_time` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '会员分组下的好友列表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of chat_friend
-- ----------------------------
INSERT INTO `chat_friend` VALUES (1, 4, 1, '', '2021-10-29 16:19:41', '2021-10-29 16:19:41', NULL);
INSERT INTO `chat_friend` VALUES (2, 1, 2, '', '2021-10-29 16:19:41', '2021-10-29 16:19:41', NULL);

-- ----------------------------
-- Table structure for chat_friend_group
-- ----------------------------
DROP TABLE IF EXISTS `chat_friend_group`;
CREATE TABLE `chat_friend_group`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id` int(10) UNSIGNED NOT NULL COMMENT '会员id',
  `group_name` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '分组名称',
  `weight` tinyint(4) NOT NULL DEFAULT 0 COMMENT '好友分组排序 越小越前',
  `create_time` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `update_time` timestamp(0) NULL DEFAULT NULL,
  `delete_time` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '会员好友分组表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of chat_friend_group
-- ----------------------------
INSERT INTO `chat_friend_group` VALUES (1, 1, '我的好友', 0, '2020-11-07 12:45:54', NULL, NULL);
INSERT INTO `chat_friend_group` VALUES (2, 1, '网红声优', 0, '2020-11-07 12:49:38', NULL, NULL);
INSERT INTO `chat_friend_group` VALUES (3, 1, '女神艺人', 0, '2020-11-07 12:49:54', NULL, NULL);
INSERT INTO `chat_friend_group` VALUES (4, 2, '我的好友', 0, '2020-11-08 11:33:31', NULL, NULL);
INSERT INTO `chat_friend_group` VALUES (5, 3, '我的好友', 0, '2021-10-27 14:39:03', NULL, NULL);
INSERT INTO `chat_friend_group` VALUES (6, 4, '我的好友', 0, '2021-10-27 14:39:11', NULL, NULL);
INSERT INTO `chat_friend_group` VALUES (7, 5, '我的好友', 0, '2021-10-27 14:39:17', NULL, NULL);
INSERT INTO `chat_friend_group` VALUES (8, 6, '我的好友', 0, '2021-10-27 14:39:24', NULL, NULL);
INSERT INTO `chat_friend_group` VALUES (9, 7, '我的好友', 0, '2021-10-29 13:50:42', '2021-10-29 13:50:42', NULL);

-- ----------------------------
-- Table structure for chat_group
-- ----------------------------
DROP TABLE IF EXISTS `chat_group`;
CREATE TABLE `chat_group`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `account` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '群号',
  `group_name` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '群名称',
  `avatar` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '/static/images/pkq.png' COMMENT '群头像',
  `belong` int(11) UNSIGNED NOT NULL COMMENT '群主',
  `desc` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '描述',
  `group_status` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0正常 1全体禁言',
  `create_time` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `update_time` timestamp(0) NULL DEFAULT NULL,
  `delete_time` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '聊天群表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of chat_group
-- ----------------------------
INSERT INTO `chat_group` VALUES (1, '1122546488', '老司机群', '/static/images/gtva1.jpg', 1, '今晚秋名山不见不散', 0, '2020-11-07 16:07:29', NULL, NULL);
INSERT INTO `chat_group` VALUES (2, '8739481981', 'Fly社区官方群', '/static/images/gtva2.jpg', 2, '带你装逼带你飞 ︿(￣︶￣)︿', 0, '2020-11-09 10:22:12', NULL, NULL);

-- ----------------------------
-- Table structure for chat_group_member
-- ----------------------------
DROP TABLE IF EXISTS `chat_group_member`;
CREATE TABLE `chat_group_member`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `group_id` int(11) UNSIGNED NOT NULL COMMENT '群ID',
  `member_id` int(11) UNSIGNED NOT NULL COMMENT '用户ID',
  `add_time` int(11) UNSIGNED NOT NULL COMMENT '加群时间',
  `type` tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '0群主 1会员',
  `forbidden_speech_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '禁言到某个时间',
  `nickname` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '群员的群昵称',
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0正常 1群黑名单',
  `create_time` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `update_time` timestamp(0) NULL DEFAULT NULL,
  `delete_time` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '群员表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of chat_group_member
-- ----------------------------
INSERT INTO `chat_group_member` VALUES (1, 1, 1, 1604765249, 0, 0, '', 0, '2020-11-07 16:07:29', NULL, NULL);
INSERT INTO `chat_group_member` VALUES (2, 2, 2, 1604917332, 0, 0, '', 0, '2020-11-09 10:22:12', NULL, NULL);
INSERT INTO `chat_group_member` VALUES (3, 2, 1, 1635495631, 1, 0, '', 0, '2021-10-29 16:20:31', '2021-10-29 16:20:31', NULL);

-- ----------------------------
-- Table structure for chat_member
-- ----------------------------
DROP TABLE IF EXISTS `chat_member`;
CREATE TABLE `chat_member`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `account` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '账号',
  `password` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '发送者',
  `salt` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '密钥',
  `nickname` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '匿名' COMMENT '昵称',
  `avatar` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '/static/images/pkq.png' COMMENT '头像',
  `signature` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '签名',
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '在线状态 0在线 1下线 2隐身',
  `login_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '上次登录时间',
  `create_time` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `update_time` timestamp(0) NULL DEFAULT NULL,
  `delete_time` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '会员表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of chat_member
-- ----------------------------
INSERT INTO `chat_member` VALUES (1, 'cshaptx4869', 'f7825af16d0d23e3dbb7571969c78aa3', '123!@#', '白開水', '/static/images/pkq.png', '淡而无味', 0, 0, '2020-11-07 11:00:16', '2021-10-29 14:49:00', NULL);
INSERT INTO `chat_member` VALUES (2, 'xianxin', 'f7825af16d0d23e3dbb7571969c78aa3', '123!@#', '贤心', '/static/images/tva1.jpg', '代码在囧途，也要写到底', 2, 1635495552, '2020-11-07 12:41:45', '2021-10-29 16:22:39', NULL);
INSERT INTO `chat_member` VALUES (3, 'liuxiaotao', 'f7825af16d0d23e3dbb7571969c78aa3', '123!@#', '刘小涛', '/static/images/tva4.jpg', '如约而至，不负姊妹欢乐颂', 0, 1635495430, '2020-11-07 12:43:20', '2021-10-29 16:17:11', NULL);
INSERT INTO `chat_member` VALUES (4, 'xiexiaonan', 'f7825af16d0d23e3dbb7571969c78aa3', '123!@#', '谢晓楠', '/static/images/tva2.jpg', '让天下没有难写的代码', 0, 0, '2020-11-07 12:44:26', NULL, NULL);
INSERT INTO `chat_member` VALUES (5, 'luoxiaofeng', 'f7825af16d0d23e3dbb7571969c78aa3', '123!@#', '罗小凤', '/static/images/tva4.jpg', '在自己实力不济的时候，不要去相信什么媒体和记者。他们不是善良的人，有时候候他们的采访对当事人而言就是陷阱', 0, 0, '2020-11-07 15:49:33', NULL, NULL);
INSERT INTO `chat_member` VALUES (6, 'xuxiaozheng', 'f7825af16d0d23e3dbb7571969c78aa3', '123!@#', '徐小峥', '/static/images/tva1.jpg', '我瘋了！這也太準了吧 超級笑點低', 0, 0, '2020-11-07 16:02:50', NULL, NULL);
INSERT INTO `chat_member` VALUES (7, 'zhangxiaofan', '41ff5530105dfd4dc84e60e3d1286687', 'U3pyET', '张小凡', '/static/images/pkq.png', 'Hi', 0, 1635486697, '2021-10-29 13:50:42', '2021-10-29 13:51:37', NULL);

-- ----------------------------
-- Table structure for chat_msgbox
-- ----------------------------
DROP TABLE IF EXISTS `chat_msgbox`;
CREATE TABLE `chat_msgbox`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `from` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '消息发送者',
  `to` int(11) UNSIGNED NOT NULL COMMENT '消息接收者',
  `type` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0请求添加用户 1系统消息(加好友) 2请求加群 3系统消息(加群)',
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0待处理 1同意 2拒绝 3无须处理',
  `remark` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '附加消息',
  `content` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '信息备注',
  `friend_group_id` int(11) NULL DEFAULT NULL COMMENT '好友分组ID',
  `group_id` int(11) NULL DEFAULT NULL COMMENT '群ID',
  `send_time` int(11) UNSIGNED NOT NULL COMMENT '发送消息时间',
  `read_time` int(11) UNSIGNED NULL DEFAULT NULL COMMENT '读消息时间',
  `create_time` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `update_time` timestamp(0) NULL DEFAULT NULL,
  `delete_time` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '通知表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of chat_msgbox
-- ----------------------------
INSERT INTO `chat_msgbox` VALUES (1, 1, 2, 0, 1, '大佬好', '申请添加你为好友', 1, NULL, 1635495576, 1635495579, '2021-10-29 16:19:37', '2021-10-29 16:19:41', NULL);
INSERT INTO `chat_msgbox` VALUES (2, 0, 1, 1, 3, NULL, '你和 [xianxin] 已经是好友了', NULL, NULL, 1635495581, 1635495585, '2021-10-29 16:19:41', '2021-10-29 16:19:41', NULL);
INSERT INTO `chat_msgbox` VALUES (3, 1, 2, 2, 1, '大佬拉我', '申请加入群 [Fly社区官方群]', NULL, 2, 1635495626, 1635495629, '2021-10-29 16:20:26', '2021-10-29 16:20:31', NULL);
INSERT INTO `chat_msgbox` VALUES (4, 0, 1, 3, 3, NULL, '你已加入群 [8739481981]', NULL, NULL, 1635495631, 1635495637, '2021-10-29 16:20:31', '2021-10-29 16:20:31', NULL);

-- ----------------------------
-- Table structure for chat_record
-- ----------------------------
DROP TABLE IF EXISTS `chat_record`;
CREATE TABLE `chat_record`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sender` int(11) UNSIGNED NOT NULL COMMENT '发送者',
  `receiver` int(11) UNSIGNED NOT NULL COMMENT '接收者',
  `content` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '发送内容',
  `type` enum('friend','group') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'friend' COMMENT '聊天类型',
  `send_time` int(11) UNSIGNED NOT NULL COMMENT '发送时间',
  `create_time` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `update_time` timestamp(0) NULL DEFAULT NULL,
  `delete_time` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `send`(`sender`) USING BTREE,
  INDEX `receive`(`receiver`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '聊天记录表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of chat_record
-- ----------------------------
INSERT INTO `chat_record` VALUES (1, 2, 1, '地方', 'friend', 1635495599, '2021-10-29 16:20:00', '2021-10-29 16:20:00', NULL);
INSERT INTO `chat_record` VALUES (2, 1, 2, '咚咚咚', 'friend', 1635495608, '2021-10-29 16:20:08', '2021-10-29 16:20:08', NULL);
INSERT INTO `chat_record` VALUES (3, 1, 2, '是是是', 'group', 1635495651, '2021-10-29 16:20:52', '2021-10-29 16:20:52', NULL);
INSERT INTO `chat_record` VALUES (4, 1, 2, '123', 'friend', 1635495772, '2021-10-29 16:22:53', '2021-10-29 16:22:53', NULL);

-- ----------------------------
-- Table structure for chat_skin
-- ----------------------------
DROP TABLE IF EXISTS `chat_skin`;
CREATE TABLE `chat_skin`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id` int(11) UNSIGNED NOT NULL COMMENT '会员id',
  `filename` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '皮肤文件名',
  `create_time` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `update_time` timestamp(0) NULL DEFAULT NULL,
  `delete_time` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '皮肤表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of chat_skin
-- ----------------------------
INSERT INTO `chat_skin` VALUES (1, 1, '3.jpg', '2021-10-22 17:22:49', '2021-10-29 14:43:31', NULL);
INSERT INTO `chat_skin` VALUES (2, 2, '4.jpg', '2021-10-24 10:58:15', '2021-10-29 14:45:58', NULL);

SET FOREIGN_KEY_CHECKS = 1;
