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

 Date: 24/10/2021 14:48:09
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
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '会员分组下的好友列表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of chat_friend
-- ----------------------------
INSERT INTO `chat_friend` VALUES (1, 1, 2, '', '2020-11-07 12:53:19', NULL, NULL);
INSERT INTO `chat_friend` VALUES (2, 1, 3, '', '2020-11-07 12:53:28', NULL, NULL);
INSERT INTO `chat_friend` VALUES (3, 1, 4, '', '2020-11-07 12:53:34', NULL, NULL);
INSERT INTO `chat_friend` VALUES (4, 2, 5, '', '2020-11-07 15:49:52', NULL, NULL);
INSERT INTO `chat_friend` VALUES (5, 3, 6, '', '2020-11-07 16:03:08', NULL, NULL);
INSERT INTO `chat_friend` VALUES (6, 4, 1, '', '2020-11-08 11:34:02', NULL, NULL);

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
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '会员好友分组表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of chat_friend_group
-- ----------------------------
INSERT INTO `chat_friend_group` VALUES (1, 1, '知名人物', 0, '2020-11-07 12:45:54', NULL, NULL);
INSERT INTO `chat_friend_group` VALUES (2, 1, '网红声优', 5, '2020-11-07 12:49:38', NULL, NULL);
INSERT INTO `chat_friend_group` VALUES (3, 1, '女神艺人', 10, '2020-11-07 12:49:54', NULL, NULL);
INSERT INTO `chat_friend_group` VALUES (4, 2, '我的好友', 0, '2020-11-08 11:33:31', NULL, NULL);

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
  `number` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '人数',
  `desc` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '描述',
  `approval` tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '0无需验证 1需要验证',
  `group_status` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0正常 1全体禁言',
  `create_time` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `update_time` timestamp(0) NULL DEFAULT NULL,
  `delete_time` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '聊天群表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of chat_group
-- ----------------------------
INSERT INTO `chat_group` VALUES (1, '1122546488', '老司机群', '/static/images/gtva1.jpg', 1, 1, '', 1, 0, '2020-11-07 16:07:29', NULL, NULL);
INSERT INTO `chat_group` VALUES (2, '8739481981', 'Fly社区官方群', '/static/images/gtva2.jpg', 1, 1, '', 1, 0, '2020-11-09 10:22:12', NULL, NULL);

-- ----------------------------
-- Table structure for chat_group_member
-- ----------------------------
DROP TABLE IF EXISTS `chat_group_member`;
CREATE TABLE `chat_group_member`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `group_id` int(11) UNSIGNED NOT NULL COMMENT '群id',
  `member_id` int(11) UNSIGNED NOT NULL COMMENT '用户id',
  `add_time` int(11) UNSIGNED NOT NULL COMMENT '加群时间',
  `type` tinyint(3) UNSIGNED NOT NULL DEFAULT 2 COMMENT '0群主 1管理员 2会员',
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
INSERT INTO `chat_group_member` VALUES (1, 1, 1, 1604736540, 0, 0, '', 0, '2020-11-07 16:10:20', NULL, NULL);
INSERT INTO `chat_group_member` VALUES (2, 2, 1, 1604888436, 2, 0, '', 0, '2020-11-09 10:20:47', NULL, NULL);
INSERT INTO `chat_group_member` VALUES (3, 1, 2, 1604891821, 2, 0, '', 0, '2020-11-09 11:17:19', NULL, NULL);

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
  `birthday` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '生日',
  `sex` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '性别 0保密 1男 2女',
  `avatar` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '/static/images/pkq.png' COMMENT '头像',
  `mobile` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '手机',
  `email` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '邮箱',
  `signature` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '签名',
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '在线状态 0隐身 1在线',
  `login_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '上次登录时间',
  `create_time` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `update_time` timestamp(0) NULL DEFAULT NULL,
  `delete_time` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '会员表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of chat_member
-- ----------------------------
INSERT INTO `chat_member` VALUES (1, 'cshaptx4869', 'f7825af16d0d23e3dbb7571969c78aa3', '123!@#', '白開水', 0, 0, '/static/images/pkq.png', '', '', '淡而无味', 1, 0, '2020-11-07 11:00:16', '2021-10-24 11:25:02', NULL);
INSERT INTO `chat_member` VALUES (2, 'xianxin', 'f7825af16d0d23e3dbb7571969c78aa3', '123!@#', '贤心', 0, 0, '/static/images/tva1.jpg', '', '', '代码在囧途，也要写到底', 0, 0, '2020-11-07 12:41:45', '2021-10-24 11:59:50', NULL);
INSERT INTO `chat_member` VALUES (3, 'liuxiaotao', 'f7825af16d0d23e3dbb7571969c78aa3', '123!@#', '刘小涛', 0, 0, '/static/images/tva4.jpg', '', '', '如约而至，不负姊妹欢乐颂', 0, 0, '2020-11-07 12:43:20', NULL, NULL);
INSERT INTO `chat_member` VALUES (4, 'xiexiaonan', 'f7825af16d0d23e3dbb7571969c78aa3', '123!@#', '谢晓楠', 0, 0, '/static/images/tva2.jpg', '', '', '让天下没有难写的代码', 0, 0, '2020-11-07 12:44:26', NULL, NULL);
INSERT INTO `chat_member` VALUES (5, 'luoxiaofeng', 'f7825af16d0d23e3dbb7571969c78aa3', '123!@#', '罗小凤', 0, 0, '/static/images/tva4.jpg', '', '', '在自己实力不济的时候，不要去相信什么媒体和记者。他们不是善良的人，有时候候他们的采访对当事人而言就是陷阱', 0, 0, '2020-11-07 15:49:33', NULL, NULL);
INSERT INTO `chat_member` VALUES (6, 'xuxiaozheng', 'f7825af16d0d23e3dbb7571969c78aa3', '123!@#', '徐小峥', 0, 0, '/static/images/tva1.jpg', '', '', '我瘋了！這也太準了吧 超級笑點低', 0, 0, '2020-11-07 16:02:50', NULL, NULL);

-- ----------------------------
-- Table structure for chat_message
-- ----------------------------
DROP TABLE IF EXISTS `chat_message`;
CREATE TABLE `chat_message`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `msg_type` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0请求添加用户 1系统消息(加好友) 2请求加群 3系统消息(加群) 4群体会员消息',
  `send` int(11) UNSIGNED NOT NULL COMMENT '消息发送者',
  `receive` int(11) UNSIGNED NOT NULL COMMENT '消息接收者',
  `msg_status` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0未读 1同意 2拒绝 3同意且返回消息已读 4拒绝且返回消息已读 5全体消息已读',
  `remark` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '附加消息',
  `send_time` int(11) UNSIGNED NOT NULL COMMENT '发送消息时间',
  `read_time` int(11) UNSIGNED NOT NULL COMMENT '读消息时间',
  `receive_group` int(11) NOT NULL COMMENT '接收消息的群主',
  `handle_group` int(11) NOT NULL COMMENT '处理该请求的群主',
  `my_group` int(11) NOT NULL COMMENT '好友分组',
  `create_time` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `update_time` timestamp(0) NULL DEFAULT NULL,
  `delete_time` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '通知表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of chat_message
-- ----------------------------

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
) ENGINE = InnoDB AUTO_INCREMENT = 13 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '聊天记录表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of chat_record
-- ----------------------------
INSERT INTO `chat_record` VALUES (1, 1, 2, '1', 'friend', 1635052317, '2021-10-24 13:11:58', '2021-10-24 13:11:58', NULL);
INSERT INTO `chat_record` VALUES (2, 2, 1, '2', 'friend', 1635052357, '2021-10-24 13:12:38', '2021-10-24 13:12:38', NULL);
INSERT INTO `chat_record` VALUES (3, 1, 2, 'shazi', 'friend', 1635053649, '2021-10-24 13:34:09', '2021-10-24 13:34:09', NULL);
INSERT INTO `chat_record` VALUES (4, 2, 1, '你才是啥子', 'friend', 1635053673, '2021-10-24 13:34:34', '2021-10-24 13:34:34', NULL);
INSERT INTO `chat_record` VALUES (5, 1, 6, '是是是是是是是', 'friend', 1635053784, '2021-10-24 13:36:25', '2021-10-24 13:36:25', NULL);
INSERT INTO `chat_record` VALUES (6, 1, 1, '111', 'group', 1635056481, '2021-10-24 14:21:22', '2021-10-24 14:21:22', NULL);
INSERT INTO `chat_record` VALUES (7, 2, 1, '222', 'group', 1635056549, '2021-10-24 14:22:30', '2021-10-24 14:22:30', NULL);
INSERT INTO `chat_record` VALUES (8, 1, 1, '111', 'group', 1635056583, '2021-10-24 14:23:04', '2021-10-24 14:23:04', NULL);
INSERT INTO `chat_record` VALUES (9, 1, 1, '111', 'group', 1635056735, '2021-10-24 14:25:36', '2021-10-24 14:25:36', NULL);
INSERT INTO `chat_record` VALUES (10, 1, 1, 'q', 'group', 1635056738, '2021-10-24 14:25:39', '2021-10-24 14:25:39', NULL);
INSERT INTO `chat_record` VALUES (11, 1, 1, 'q', 'group', 1635056739, '2021-10-24 14:25:39', '2021-10-24 14:25:39', NULL);
INSERT INTO `chat_record` VALUES (12, 1, 1, 'q', 'group', 1635056739, '2021-10-24 14:25:40', '2021-10-24 14:25:40', NULL);

-- ----------------------------
-- Table structure for chat_skin
-- ----------------------------
DROP TABLE IF EXISTS `chat_skin`;
CREATE TABLE `chat_skin`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id` int(11) UNSIGNED NOT NULL COMMENT '会员id',
  `url` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '皮肤地址',
  `is_user_upload` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0默认 1用户自定义',
  `create_time` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `update_time` timestamp(0) NULL DEFAULT NULL,
  `delete_time` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '皮肤表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of chat_skin
-- ----------------------------
INSERT INTO `chat_skin` VALUES (1, 1, 'http://chat.test/static/layuiadmin/layui/css/modules/layim/skin/4.jpg', 0, '2021-10-22 17:22:49', NULL, NULL);
INSERT INTO `chat_skin` VALUES (2, 2, 'http://chat.test/static/layuiadmin/layui/css/modules/layim/skin/1.jpg', 0, '2021-10-24 10:58:15', NULL, NULL);

SET FOREIGN_KEY_CHECKS = 1;
