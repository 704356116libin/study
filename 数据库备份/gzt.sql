/*
 Navicat Premium Data Transfer

 Source Server         : gzt-线上
 Source Server Type    : MySQL
 Source Server Version : 50724
 Source Host           : 47.93.18.113:3306
 Source Schema         : gzt

 Target Server Type    : MySQL
 Target Server Version : 50724
 File Encoding         : 65001

 Date: 16/04/2019 09:15:26
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for admin_menu
-- ----------------------------
DROP TABLE IF EXISTS `admin_menu`;
CREATE TABLE `admin_menu`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT 0,
  `order` int(11) NOT NULL DEFAULT 0,
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `uri` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `permission` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 12 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admin_menu
-- ----------------------------
INSERT INTO `admin_menu` VALUES (1, 0, 1, 'Index', 'fa-bar-chart', '/', NULL, NULL, NULL);
INSERT INTO `admin_menu` VALUES (2, 0, 2, 'Admin', 'fa-tasks', '', NULL, NULL, NULL);
INSERT INTO `admin_menu` VALUES (3, 2, 3, 'Users', 'fa-users', 'auth/users', NULL, NULL, NULL);
INSERT INTO `admin_menu` VALUES (4, 2, 4, 'Roles', 'fa-user', 'auth/roles', NULL, NULL, NULL);
INSERT INTO `admin_menu` VALUES (5, 2, 5, 'Permission', 'fa-ban', 'auth/permissions', NULL, NULL, NULL);
INSERT INTO `admin_menu` VALUES (6, 2, 6, 'Menu', 'fa-bars', 'auth/menu', NULL, NULL, NULL);
INSERT INTO `admin_menu` VALUES (7, 2, 7, 'Operation log', 'fa-history', 'auth/logs', NULL, NULL, NULL);
INSERT INTO `admin_menu` VALUES (8, 0, 0, '工作通管理', 'fa-bars', NULL, '*', '2018-11-21 09:59:16', '2018-11-21 09:59:16');
INSERT INTO `admin_menu` VALUES (9, 8, 0, '用户管理', 'fa-bars', 'users', '*', '2018-11-21 09:59:58', '2018-11-21 09:59:58');
INSERT INTO `admin_menu` VALUES (10, 0, 0, '商品管理', 'fa-bars', '/products', NULL, '2019-04-12 21:26:48', '2019-04-12 21:26:48');
INSERT INTO `admin_menu` VALUES (11, 0, 0, '订单管理', 'fa-bars', '/orders', NULL, '2019-04-14 22:56:21', '2019-04-14 22:56:21');

-- ----------------------------
-- Table structure for admin_operation_log
-- ----------------------------
DROP TABLE IF EXISTS `admin_operation_log`;
CREATE TABLE `admin_operation_log`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `method` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `input` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `admin_operation_log_user_id_index`(`user_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 467 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admin_operation_log
-- ----------------------------
INSERT INTO `admin_operation_log` VALUES (1, 1, 'admin', 'GET', '192.168.10.1', '[]', '2018-11-17 16:19:45', '2018-11-17 16:19:45');
INSERT INTO `admin_operation_log` VALUES (2, 1, 'admin', 'GET', '192.168.10.1', '[]', '2018-11-21 09:30:13', '2018-11-21 09:30:13');
INSERT INTO `admin_operation_log` VALUES (3, 1, 'admin/auth/users', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2018-11-21 09:30:18', '2018-11-21 09:30:18');
INSERT INTO `admin_operation_log` VALUES (4, 1, 'admin/auth/users/1/edit', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2018-11-21 09:30:21', '2018-11-21 09:30:21');
INSERT INTO `admin_operation_log` VALUES (5, 1, 'admin/auth/users', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2018-11-21 09:30:25', '2018-11-21 09:30:25');
INSERT INTO `admin_operation_log` VALUES (6, 1, 'admin/auth/users/1/edit', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2018-11-21 09:31:13', '2018-11-21 09:31:13');
INSERT INTO `admin_operation_log` VALUES (7, 1, 'admin/auth/users/1', 'PUT', '192.168.10.1', '{\"username\":\"admin\",\"name\":\"Administrator\",\"password\":\"$2y$10$\\/1n6WYBhkZStv9ooOWbiBeoVpZ4vs3wijsAEAeOhlk0t6FKOuPbXK\",\"password_confirmation\":\"$2y$10$\\/1n6WYBhkZStv9ooOWbiBeoVpZ4vs3wijsAEAeOhlk0t6FKOuPbXK\",\"roles\":[\"1\",null],\"permissions\":[null],\"_token\":\"PXgXMlX2kGEKEhuNZwdPBeScZ7r0dd5LXGirjx56\",\"after-save\":\"1\",\"_method\":\"PUT\",\"_previous_\":\"http:\\/\\/gzt.test\\/admin\\/auth\\/users\"}', '2018-11-21 09:31:37', '2018-11-21 09:31:37');
INSERT INTO `admin_operation_log` VALUES (8, 1, 'admin/auth/users/1/edit', 'GET', '192.168.10.1', '[]', '2018-11-21 09:31:38', '2018-11-21 09:31:38');
INSERT INTO `admin_operation_log` VALUES (9, 1, 'admin/auth/users/1/edit', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2018-11-21 09:32:11', '2018-11-21 09:32:11');
INSERT INTO `admin_operation_log` VALUES (10, 1, 'admin/auth/users', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2018-11-21 09:32:42', '2018-11-21 09:32:42');
INSERT INTO `admin_operation_log` VALUES (11, 1, 'admin/auth/users', 'GET', '192.168.10.1', '[]', '2018-11-21 09:32:46', '2018-11-21 09:32:46');
INSERT INTO `admin_operation_log` VALUES (12, 1, 'admin/users', 'GET', '192.168.10.1', '[]', '2018-11-21 09:32:53', '2018-11-21 09:32:53');
INSERT INTO `admin_operation_log` VALUES (13, 1, 'admin/users', 'GET', '192.168.10.1', '{\"id\":null,\"_pjax\":\"#pjax-container\"}', '2018-11-21 09:33:27', '2018-11-21 09:33:27');
INSERT INTO `admin_operation_log` VALUES (14, 1, 'admin/users', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\",\"_export_\":\"all\"}', '2018-11-21 09:33:37', '2018-11-21 09:33:37');
INSERT INTO `admin_operation_log` VALUES (15, 1, 'admin/users', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\",\"_export_\":\"all\"}', '2018-11-21 09:34:31', '2018-11-21 09:34:31');
INSERT INTO `admin_operation_log` VALUES (16, 1, 'admin', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2018-11-21 09:34:58', '2018-11-21 09:34:58');
INSERT INTO `admin_operation_log` VALUES (17, 1, 'admin/users', 'GET', '192.168.10.1', '[]', '2018-11-21 09:36:46', '2018-11-21 09:36:46');
INSERT INTO `admin_operation_log` VALUES (18, 1, 'admin', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2018-11-21 09:43:14', '2018-11-21 09:43:14');
INSERT INTO `admin_operation_log` VALUES (19, 1, 'admin/auth/roles', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2018-11-21 09:43:18', '2018-11-21 09:43:18');
INSERT INTO `admin_operation_log` VALUES (20, 1, 'admin/auth/roles/1/edit', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2018-11-21 09:43:21', '2018-11-21 09:43:21');
INSERT INTO `admin_operation_log` VALUES (21, 1, 'admin/auth/roles', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2018-11-21 09:43:44', '2018-11-21 09:43:44');
INSERT INTO `admin_operation_log` VALUES (22, 1, 'admin', 'GET', '192.168.10.1', '[]', '2018-11-21 09:47:42', '2018-11-21 09:47:42');
INSERT INTO `admin_operation_log` VALUES (23, 1, 'admin/users', 'GET', '192.168.10.1', '[]', '2018-11-21 09:57:52', '2018-11-21 09:57:52');
INSERT INTO `admin_operation_log` VALUES (24, 1, 'admin/users/2/edit', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2018-11-21 09:57:55', '2018-11-21 09:57:55');
INSERT INTO `admin_operation_log` VALUES (25, 1, 'admin/users', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2018-11-21 09:57:57', '2018-11-21 09:57:57');
INSERT INTO `admin_operation_log` VALUES (26, 1, 'admin/users/1/edit', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2018-11-21 09:58:07', '2018-11-21 09:58:07');
INSERT INTO `admin_operation_log` VALUES (27, 1, 'admin/auth/menu', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2018-11-21 09:58:24', '2018-11-21 09:58:24');
INSERT INTO `admin_operation_log` VALUES (28, 1, 'admin/auth/menu', 'POST', '192.168.10.1', '{\"parent_id\":\"0\",\"title\":\"\\u5de5\\u4f5c\\u901a\\u7ba1\\u7406\",\"icon\":\"fa-bars\",\"uri\":null,\"roles\":[\"1\",null],\"permission\":\"*\",\"_token\":\"PXgXMlX2kGEKEhuNZwdPBeScZ7r0dd5LXGirjx56\"}', '2018-11-21 09:59:16', '2018-11-21 09:59:16');
INSERT INTO `admin_operation_log` VALUES (29, 1, 'admin/auth/menu', 'GET', '192.168.10.1', '[]', '2018-11-21 09:59:17', '2018-11-21 09:59:17');
INSERT INTO `admin_operation_log` VALUES (30, 1, 'admin/auth/menu', 'GET', '192.168.10.1', '[]', '2018-11-21 09:59:20', '2018-11-21 09:59:20');
INSERT INTO `admin_operation_log` VALUES (31, 1, 'admin', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2018-11-21 09:59:23', '2018-11-21 09:59:23');
INSERT INTO `admin_operation_log` VALUES (32, 1, 'admin/auth/menu', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2018-11-21 09:59:31', '2018-11-21 09:59:31');
INSERT INTO `admin_operation_log` VALUES (33, 1, 'admin/auth/menu', 'POST', '192.168.10.1', '{\"parent_id\":\"8\",\"title\":\"\\u7528\\u6237\\u7ba1\\u7406\",\"icon\":\"fa-bars\",\"uri\":\"users\",\"roles\":[\"1\",null],\"permission\":\"*\",\"_token\":\"PXgXMlX2kGEKEhuNZwdPBeScZ7r0dd5LXGirjx56\"}', '2018-11-21 09:59:58', '2018-11-21 09:59:58');
INSERT INTO `admin_operation_log` VALUES (34, 1, 'admin/auth/menu', 'GET', '192.168.10.1', '[]', '2018-11-21 09:59:58', '2018-11-21 09:59:58');
INSERT INTO `admin_operation_log` VALUES (35, 1, 'admin/users', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2018-11-21 10:00:02', '2018-11-21 10:00:02');
INSERT INTO `admin_operation_log` VALUES (36, 1, 'admin/users/2/edit', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2018-11-21 10:00:05', '2018-11-21 10:00:05');
INSERT INTO `admin_operation_log` VALUES (37, 1, 'admin/users', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2018-11-21 10:00:09', '2018-11-21 10:00:09');
INSERT INTO `admin_operation_log` VALUES (38, 1, 'admin', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2018-11-21 10:00:13', '2018-11-21 10:00:13');
INSERT INTO `admin_operation_log` VALUES (39, 1, 'admin/auth/menu', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2018-11-21 10:00:21', '2018-11-21 10:00:21');
INSERT INTO `admin_operation_log` VALUES (40, 1, 'admin/auth/menu/8/edit', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2018-11-21 10:00:28', '2018-11-21 10:00:28');
INSERT INTO `admin_operation_log` VALUES (41, 1, 'admin/auth/menu/8', 'PUT', '192.168.10.1', '{\"parent_id\":\"0\",\"title\":\"\\u5de5\\u4f5c\\u901a\\u7ba1\\u7406\",\"icon\":\"fa-bars\",\"uri\":null,\"roles\":[\"1\",null],\"permission\":\"*\",\"_token\":\"PXgXMlX2kGEKEhuNZwdPBeScZ7r0dd5LXGirjx56\",\"_method\":\"PUT\",\"_previous_\":\"http:\\/\\/gzt.test\\/admin\\/auth\\/menu\"}', '2018-11-21 10:00:38', '2018-11-21 10:00:38');
INSERT INTO `admin_operation_log` VALUES (42, 1, 'admin/auth/menu', 'GET', '192.168.10.1', '[]', '2018-11-21 10:00:39', '2018-11-21 10:00:39');
INSERT INTO `admin_operation_log` VALUES (43, 1, 'admin/auth/menu', 'GET', '192.168.10.1', '[]', '2018-11-21 10:00:46', '2018-11-21 10:00:46');
INSERT INTO `admin_operation_log` VALUES (44, 1, 'admin', 'GET', '192.168.10.1', '[]', '2018-12-15 09:56:34', '2018-12-15 09:56:34');
INSERT INTO `admin_operation_log` VALUES (45, 1, 'admin', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2018-12-15 09:56:37', '2018-12-15 09:56:37');
INSERT INTO `admin_operation_log` VALUES (46, 1, 'admin', 'GET', '192.168.10.1', '[]', '2019-02-16 14:19:05', '2019-02-16 14:19:05');
INSERT INTO `admin_operation_log` VALUES (47, 1, 'admin', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-02-16 14:19:17', '2019-02-16 14:19:17');
INSERT INTO `admin_operation_log` VALUES (48, 2, 'GZTadmin', 'GET', '192.168.10.1', '[]', '2019-03-11 11:55:47', '2019-03-11 11:55:47');
INSERT INTO `admin_operation_log` VALUES (49, 2, 'GZTadmin', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-11 11:56:31', '2019-03-11 11:56:31');
INSERT INTO `admin_operation_log` VALUES (50, 2, 'GZTadmin', 'GET', '192.168.10.1', '[]', '2019-03-11 13:28:28', '2019-03-11 13:28:28');
INSERT INTO `admin_operation_log` VALUES (51, 2, 'GZTadmin', 'GET', '192.168.10.1', '[]', '2019-03-11 13:30:22', '2019-03-11 13:30:22');
INSERT INTO `admin_operation_log` VALUES (52, 2, 'GZTadmin', 'GET', '192.168.10.1', '[]', '2019-03-11 13:31:18', '2019-03-11 13:31:18');
INSERT INTO `admin_operation_log` VALUES (53, 2, 'GZTadmin', 'GET', '192.168.10.1', '[]', '2019-03-11 13:31:28', '2019-03-11 13:31:28');
INSERT INTO `admin_operation_log` VALUES (54, 2, 'GZTadmin', 'GET', '192.168.10.1', '[]', '2019-03-11 13:31:53', '2019-03-11 13:31:53');
INSERT INTO `admin_operation_log` VALUES (55, 2, 'GZTadmin', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-11 13:36:52', '2019-03-11 13:36:52');
INSERT INTO `admin_operation_log` VALUES (56, 2, 'GZTadmin', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-11 13:36:53', '2019-03-11 13:36:53');
INSERT INTO `admin_operation_log` VALUES (57, 2, 'GZTadmin', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-11 13:36:57', '2019-03-11 13:36:57');
INSERT INTO `admin_operation_log` VALUES (58, 2, 'GZTadmin', 'GET', '192.168.10.1', '[]', '2019-03-11 14:11:29', '2019-03-11 14:11:29');
INSERT INTO `admin_operation_log` VALUES (59, 2, 'GZTadmin/companyCertification', 'GET', '192.168.10.1', '[]', '2019-03-11 14:11:36', '2019-03-11 14:11:36');
INSERT INTO `admin_operation_log` VALUES (60, 2, 'GZTadmin/users', 'GET', '192.168.10.1', '[]', '2019-03-11 14:13:15', '2019-03-11 14:13:15');
INSERT INTO `admin_operation_log` VALUES (61, 2, 'GZTadmin', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-11 14:13:31', '2019-03-11 14:13:31');
INSERT INTO `admin_operation_log` VALUES (62, 2, 'GZTadmin', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-11 14:13:35', '2019-03-11 14:13:35');
INSERT INTO `admin_operation_log` VALUES (63, 2, 'GZTadmin', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-11 14:13:54', '2019-03-11 14:13:54');
INSERT INTO `admin_operation_log` VALUES (64, 2, 'GZTadmin/companyCertification', 'GET', '192.168.10.1', '[]', '2019-03-11 14:14:03', '2019-03-11 14:14:03');
INSERT INTO `admin_operation_log` VALUES (65, 2, 'GZTadmin/companyCertification', 'GET', '192.168.10.1', '[]', '2019-03-11 14:26:00', '2019-03-11 14:26:00');
INSERT INTO `admin_operation_log` VALUES (66, 2, 'GZTadmin/companyCertification', 'GET', '192.168.10.1', '[]', '2019-03-11 14:33:15', '2019-03-11 14:33:15');
INSERT INTO `admin_operation_log` VALUES (67, 2, 'GZTadmin', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-11 14:33:18', '2019-03-11 14:33:18');
INSERT INTO `admin_operation_log` VALUES (68, 2, 'GZTadmin', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-11 14:33:25', '2019-03-11 14:33:25');
INSERT INTO `admin_operation_log` VALUES (69, 2, 'GZTadmin', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-11 14:33:30', '2019-03-11 14:33:30');
INSERT INTO `admin_operation_log` VALUES (70, 2, 'GZTadmin', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-11 14:38:44', '2019-03-11 14:38:44');
INSERT INTO `admin_operation_log` VALUES (71, 2, 'GZTadmin', 'GET', '192.168.10.1', '[]', '2019-03-11 14:50:31', '2019-03-11 14:50:31');
INSERT INTO `admin_operation_log` VALUES (72, 2, 'GZTadmin/users', 'GET', '192.168.10.1', '[]', '2019-03-11 14:50:52', '2019-03-11 14:50:52');
INSERT INTO `admin_operation_log` VALUES (73, 2, 'GZTadmin/companyCertification', 'GET', '192.168.10.1', '[]', '2019-03-11 14:55:20', '2019-03-11 14:55:20');
INSERT INTO `admin_operation_log` VALUES (74, 2, 'GZTadmin/example', 'GET', '192.168.10.1', '[]', '2019-03-11 15:02:26', '2019-03-11 15:02:26');
INSERT INTO `admin_operation_log` VALUES (75, 2, 'GZTadmin/example', 'GET', '192.168.10.1', '[]', '2019-03-11 15:03:41', '2019-03-11 15:03:41');
INSERT INTO `admin_operation_log` VALUES (76, 2, 'GZTadmin/example', 'GET', '192.168.10.1', '[]', '2019-03-11 15:04:40', '2019-03-11 15:04:40');
INSERT INTO `admin_operation_log` VALUES (77, 2, 'GZTadmin', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-11 15:04:45', '2019-03-11 15:04:45');
INSERT INTO `admin_operation_log` VALUES (78, 2, 'GZTadmin/example', 'GET', '192.168.10.1', '[]', '2019-03-11 15:05:02', '2019-03-11 15:05:02');
INSERT INTO `admin_operation_log` VALUES (79, 2, 'GZTadmin/example', 'GET', '192.168.10.1', '[]', '2019-03-11 15:13:05', '2019-03-11 15:13:05');
INSERT INTO `admin_operation_log` VALUES (80, 2, 'GZTadmin/users', 'GET', '192.168.10.1', '[]', '2019-03-11 15:13:23', '2019-03-11 15:13:23');
INSERT INTO `admin_operation_log` VALUES (81, 2, 'GZTadmin/companyCertification', 'GET', '192.168.10.1', '[]', '2019-03-11 15:14:59', '2019-03-11 15:14:59');
INSERT INTO `admin_operation_log` VALUES (82, 2, 'GZTadmin/companyCertification', 'GET', '192.168.10.1', '[]', '2019-03-11 15:15:49', '2019-03-11 15:15:49');
INSERT INTO `admin_operation_log` VALUES (83, 2, 'GZTadmin', 'GET', '192.168.10.1', '[]', '2019-03-11 15:17:45', '2019-03-11 15:17:45');
INSERT INTO `admin_operation_log` VALUES (84, 2, 'admin/users', 'GET', '192.168.10.1', '[]', '2019-03-12 10:41:47', '2019-03-12 10:41:47');
INSERT INTO `admin_operation_log` VALUES (85, 2, 'admin/users', 'GET', '192.168.10.1', '[]', '2019-03-12 10:42:22', '2019-03-12 10:42:22');
INSERT INTO `admin_operation_log` VALUES (86, 2, 'admin/companyCertification', 'GET', '192.168.10.1', '[]', '2019-03-12 10:42:43', '2019-03-12 10:42:43');
INSERT INTO `admin_operation_log` VALUES (87, 2, 'admin/example', 'GET', '192.168.10.1', '[]', '2019-03-12 10:44:25', '2019-03-12 10:44:25');
INSERT INTO `admin_operation_log` VALUES (88, 2, 'admin/companyCertification', 'GET', '192.168.10.1', '[]', '2019-03-12 10:45:12', '2019-03-12 10:45:12');
INSERT INTO `admin_operation_log` VALUES (89, 2, 'admin/companyCertification', 'GET', '192.168.10.1', '[]', '2019-03-12 10:45:21', '2019-03-12 10:45:21');
INSERT INTO `admin_operation_log` VALUES (90, 2, 'admin/companyCertification', 'GET', '192.168.10.1', '[]', '2019-03-12 10:45:57', '2019-03-12 10:45:57');
INSERT INTO `admin_operation_log` VALUES (91, 2, 'admin/companyCertification', 'GET', '192.168.10.1', '[]', '2019-03-12 10:50:19', '2019-03-12 10:50:19');
INSERT INTO `admin_operation_log` VALUES (92, 2, 'admin/companyCertification', 'GET', '192.168.10.1', '[]', '2019-03-12 11:37:18', '2019-03-12 11:37:18');
INSERT INTO `admin_operation_log` VALUES (93, 2, 'admin/companyCertification', 'GET', '192.168.10.1', '[]', '2019-03-12 11:43:11', '2019-03-12 11:43:11');
INSERT INTO `admin_operation_log` VALUES (94, 1, 'padmin', 'GET', '192.168.10.1', '[]', '2019-03-12 13:35:18', '2019-03-12 13:35:18');
INSERT INTO `admin_operation_log` VALUES (95, 1, 'padmin', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-12 13:35:25', '2019-03-12 13:35:25');
INSERT INTO `admin_operation_log` VALUES (96, 1, 'padmin/auth/users', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-12 13:35:28', '2019-03-12 13:35:28');
INSERT INTO `admin_operation_log` VALUES (97, 1, 'padmin/users', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-12 13:35:44', '2019-03-12 13:35:44');
INSERT INTO `admin_operation_log` VALUES (98, 1, 'padmin', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-12 13:35:50', '2019-03-12 13:35:50');
INSERT INTO `admin_operation_log` VALUES (99, 2, 'padmin', 'GET', '192.168.10.1', '[]', '2019-03-12 14:32:15', '2019-03-12 14:32:15');
INSERT INTO `admin_operation_log` VALUES (100, 2, 'padmin/auth/users', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-12 14:32:22', '2019-03-12 14:32:22');
INSERT INTO `admin_operation_log` VALUES (101, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-12 14:44:49', '2019-03-12 14:44:49');
INSERT INTO `admin_operation_log` VALUES (102, 2, 'padmin/company_certification/2', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-12 14:44:57', '2019-03-12 14:44:57');
INSERT INTO `admin_operation_log` VALUES (103, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-12 14:44:59', '2019-03-12 14:44:59');
INSERT INTO `admin_operation_log` VALUES (104, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-12 14:45:30', '2019-03-12 14:45:30');
INSERT INTO `admin_operation_log` VALUES (105, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-12 14:46:43', '2019-03-12 14:46:43');
INSERT INTO `admin_operation_log` VALUES (106, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-12 14:46:43', '2019-03-12 14:46:43');
INSERT INTO `admin_operation_log` VALUES (107, 2, 'padmin/company_certification/2/edit', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-12 14:47:03', '2019-03-12 14:47:03');
INSERT INTO `admin_operation_log` VALUES (108, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-12 14:47:11', '2019-03-12 14:47:11');
INSERT INTO `admin_operation_log` VALUES (109, 2, 'padmin/users', 'GET', '192.168.10.1', '[]', '2019-03-12 17:46:11', '2019-03-12 17:46:11');
INSERT INTO `admin_operation_log` VALUES (110, 2, 'padmin/auth/login', 'POST', '192.168.10.1', '{\"username\":\"aaa\",\"password\":\"123456..\",\"_token\":\"nRoMb9RR978XimTWPqvdXwjT7k2EV6hHIvfMcMT3\"}', '2019-03-12 17:46:12', '2019-03-12 17:46:12');
INSERT INTO `admin_operation_log` VALUES (111, 2, 'padmin', 'GET', '192.168.10.1', '[]', '2019-03-12 17:46:13', '2019-03-12 17:46:13');
INSERT INTO `admin_operation_log` VALUES (112, 2, 'padmin/users', 'GET', '192.168.10.1', '[]', '2019-03-12 17:46:19', '2019-03-12 17:46:19');
INSERT INTO `admin_operation_log` VALUES (113, 2, 'padmin/users', 'GET', '192.168.10.1', '[]', '2019-03-13 08:16:29', '2019-03-13 08:16:29');
INSERT INTO `admin_operation_log` VALUES (114, 2, 'padmin/users', 'GET', '192.168.10.1', '[]', '2019-03-13 08:16:50', '2019-03-13 08:16:50');
INSERT INTO `admin_operation_log` VALUES (115, 2, 'padmin', 'GET', '192.168.10.1', '[]', '2019-03-13 08:52:45', '2019-03-13 08:52:45');
INSERT INTO `admin_operation_log` VALUES (116, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-13 08:53:15', '2019-03-13 08:53:15');
INSERT INTO `admin_operation_log` VALUES (117, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-13 08:53:52', '2019-03-13 08:53:52');
INSERT INTO `admin_operation_log` VALUES (118, 2, 'padmin/company_certification/create', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-13 08:53:55', '2019-03-13 08:53:55');
INSERT INTO `admin_operation_log` VALUES (119, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-13 08:53:57', '2019-03-13 08:53:57');
INSERT INTO `admin_operation_log` VALUES (120, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-13 08:54:05', '2019-03-13 08:54:05');
INSERT INTO `admin_operation_log` VALUES (121, 2, 'padmin/company_certification/create', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-13 08:54:16', '2019-03-13 08:54:16');
INSERT INTO `admin_operation_log` VALUES (122, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-13 08:54:21', '2019-03-13 08:54:21');
INSERT INTO `admin_operation_log` VALUES (123, 2, 'padmin/company_certification/2', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-13 08:54:58', '2019-03-13 08:54:58');
INSERT INTO `admin_operation_log` VALUES (124, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-13 08:55:00', '2019-03-13 08:55:00');
INSERT INTO `admin_operation_log` VALUES (125, 2, 'padmin', 'GET', '192.168.10.1', '[]', '2019-03-13 09:52:07', '2019-03-13 09:52:07');
INSERT INTO `admin_operation_log` VALUES (126, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-13 09:52:25', '2019-03-13 09:52:25');
INSERT INTO `admin_operation_log` VALUES (127, 2, 'padmin/company_certification/2', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-13 09:52:32', '2019-03-13 09:52:32');
INSERT INTO `admin_operation_log` VALUES (128, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-13 09:52:37', '2019-03-13 09:52:37');
INSERT INTO `admin_operation_log` VALUES (129, 2, 'padmin/company_certification/2', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-13 09:53:04', '2019-03-13 09:53:04');
INSERT INTO `admin_operation_log` VALUES (130, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-13 09:53:05', '2019-03-13 09:53:05');
INSERT INTO `admin_operation_log` VALUES (131, 2, 'padmin/company_certification/2/edit', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-13 09:53:18', '2019-03-13 09:53:18');
INSERT INTO `admin_operation_log` VALUES (132, 2, 'padmin/company_certification/2/edit', 'GET', '192.168.10.1', '[]', '2019-03-13 09:56:24', '2019-03-13 09:56:24');
INSERT INTO `admin_operation_log` VALUES (133, 2, 'padmin/company_certification/2/edit', 'GET', '192.168.10.1', '[]', '2019-03-13 10:46:40', '2019-03-13 10:46:40');
INSERT INTO `admin_operation_log` VALUES (134, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-13 10:47:39', '2019-03-13 10:47:39');
INSERT INTO `admin_operation_log` VALUES (135, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-13 10:47:43', '2019-03-13 10:47:43');
INSERT INTO `admin_operation_log` VALUES (136, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-13 10:49:02', '2019-03-13 10:49:02');
INSERT INTO `admin_operation_log` VALUES (137, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-13 10:49:15', '2019-03-13 10:49:15');
INSERT INTO `admin_operation_log` VALUES (138, 2, 'padmin/company_certification/1', 'DELETE', '192.168.10.1', '{\"_method\":\"delete\",\"_token\":\"z6Ao5c0Lwk6steUnufGUIRXzuYXc7fnUA7WUsobf\"}', '2019-03-13 10:49:20', '2019-03-13 10:49:20');
INSERT INTO `admin_operation_log` VALUES (139, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-13 10:49:21', '2019-03-13 10:49:21');
INSERT INTO `admin_operation_log` VALUES (140, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-13 10:56:34', '2019-03-13 10:56:34');
INSERT INTO `admin_operation_log` VALUES (141, 2, 'padmin/company_certification/3', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-13 11:38:08', '2019-03-13 11:38:08');
INSERT INTO `admin_operation_log` VALUES (142, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-13 11:38:11', '2019-03-13 11:38:11');
INSERT INTO `admin_operation_log` VALUES (143, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-13 11:39:00', '2019-03-13 11:39:00');
INSERT INTO `admin_operation_log` VALUES (144, 2, 'padmin/company_certification/3', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-13 11:39:08', '2019-03-13 11:39:08');
INSERT INTO `admin_operation_log` VALUES (145, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-13 11:39:10', '2019-03-13 11:39:10');
INSERT INTO `admin_operation_log` VALUES (146, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-13 11:42:51', '2019-03-13 11:42:51');
INSERT INTO `admin_operation_log` VALUES (147, 2, 'padmin/company_certification/3', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-13 11:42:55', '2019-03-13 11:42:55');
INSERT INTO `admin_operation_log` VALUES (148, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-13 11:42:57', '2019-03-13 11:42:57');
INSERT INTO `admin_operation_log` VALUES (149, 2, 'padmin/company_certification/3', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-13 11:45:21', '2019-03-13 11:45:21');
INSERT INTO `admin_operation_log` VALUES (150, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-13 11:45:23', '2019-03-13 11:45:23');
INSERT INTO `admin_operation_log` VALUES (151, 2, 'padmin/company_certification/3', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-13 11:46:36', '2019-03-13 11:46:36');
INSERT INTO `admin_operation_log` VALUES (152, 2, 'padmin/company_certification/3', 'GET', '192.168.10.1', '[]', '2019-03-13 11:47:00', '2019-03-13 11:47:00');
INSERT INTO `admin_operation_log` VALUES (153, 2, 'padmin', 'GET', '192.168.10.1', '[]', '2019-03-13 11:47:39', '2019-03-13 11:47:39');
INSERT INTO `admin_operation_log` VALUES (154, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-13 11:47:51', '2019-03-13 11:47:51');
INSERT INTO `admin_operation_log` VALUES (155, 2, 'padmin/company_certification/3', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-13 11:48:12', '2019-03-13 11:48:12');
INSERT INTO `admin_operation_log` VALUES (156, 2, 'padmin/company_certification/3', 'GET', '192.168.10.1', '[]', '2019-03-13 11:49:10', '2019-03-13 11:49:10');
INSERT INTO `admin_operation_log` VALUES (157, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-13 11:49:15', '2019-03-13 11:49:15');
INSERT INTO `admin_operation_log` VALUES (158, 2, 'padmin/company_certification/3', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-13 11:49:20', '2019-03-13 11:49:20');
INSERT INTO `admin_operation_log` VALUES (159, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-13 11:50:21', '2019-03-13 11:50:21');
INSERT INTO `admin_operation_log` VALUES (160, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-13 11:50:29', '2019-03-13 11:50:29');
INSERT INTO `admin_operation_log` VALUES (161, 2, 'padmin/company_certification/3', 'PUT', '192.168.10.1', '{\"_token\":\"z6Ao5c0Lwk6steUnufGUIRXzuYXc7fnUA7WUsobf\",\"_method\":\"PUT\",\"_previous_\":\"http:\\/\\/gzt.test\\/padmin\\/company_certification\"}', '2019-03-13 11:54:34', '2019-03-13 11:54:34');
INSERT INTO `admin_operation_log` VALUES (162, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-13 11:54:35', '2019-03-13 11:54:35');
INSERT INTO `admin_operation_log` VALUES (163, 2, 'padmin/company_certification/3', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-13 11:55:44', '2019-03-13 11:55:44');
INSERT INTO `admin_operation_log` VALUES (164, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-13 11:55:46', '2019-03-13 11:55:46');
INSERT INTO `admin_operation_log` VALUES (165, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-13 11:55:50', '2019-03-13 11:55:50');
INSERT INTO `admin_operation_log` VALUES (166, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-13 11:55:54', '2019-03-13 11:55:54');
INSERT INTO `admin_operation_log` VALUES (167, 2, 'padmin/company_certification/3', 'PUT', '192.168.10.1', '{\"_token\":\"z6Ao5c0Lwk6steUnufGUIRXzuYXc7fnUA7WUsobf\",\"_method\":\"PUT\",\"_previous_\":\"http:\\/\\/gzt.test\\/padmin\\/company_certification\"}', '2019-03-13 11:56:01', '2019-03-13 11:56:01');
INSERT INTO `admin_operation_log` VALUES (168, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-13 11:56:02', '2019-03-13 11:56:02');
INSERT INTO `admin_operation_log` VALUES (169, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-13 12:01:58', '2019-03-13 12:01:58');
INSERT INTO `admin_operation_log` VALUES (170, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-13 13:41:51', '2019-03-13 13:41:51');
INSERT INTO `admin_operation_log` VALUES (171, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-13 13:46:50', '2019-03-13 13:46:50');
INSERT INTO `admin_operation_log` VALUES (172, 2, 'padmin', 'GET', '192.168.10.1', '[]', '2019-03-14 10:12:13', '2019-03-14 10:12:13');
INSERT INTO `admin_operation_log` VALUES (173, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-14 10:13:23', '2019-03-14 10:13:23');
INSERT INTO `admin_operation_log` VALUES (174, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-14 10:24:56', '2019-03-14 10:24:56');
INSERT INTO `admin_operation_log` VALUES (175, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 10:25:27', '2019-03-14 10:25:27');
INSERT INTO `admin_operation_log` VALUES (176, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 10:25:51', '2019-03-14 10:25:51');
INSERT INTO `admin_operation_log` VALUES (177, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 10:26:13', '2019-03-14 10:26:13');
INSERT INTO `admin_operation_log` VALUES (178, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 10:27:20', '2019-03-14 10:27:20');
INSERT INTO `admin_operation_log` VALUES (179, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 10:31:00', '2019-03-14 10:31:00');
INSERT INTO `admin_operation_log` VALUES (180, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 10:31:12', '2019-03-14 10:31:12');
INSERT INTO `admin_operation_log` VALUES (181, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 10:31:27', '2019-03-14 10:31:27');
INSERT INTO `admin_operation_log` VALUES (182, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 10:34:41', '2019-03-14 10:34:41');
INSERT INTO `admin_operation_log` VALUES (183, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 10:39:58', '2019-03-14 10:39:58');
INSERT INTO `admin_operation_log` VALUES (184, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 10:50:47', '2019-03-14 10:50:47');
INSERT INTO `admin_operation_log` VALUES (185, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 11:19:17', '2019-03-14 11:19:17');
INSERT INTO `admin_operation_log` VALUES (186, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 13:26:07', '2019-03-14 13:26:07');
INSERT INTO `admin_operation_log` VALUES (187, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 13:28:37', '2019-03-14 13:28:37');
INSERT INTO `admin_operation_log` VALUES (188, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-14 13:31:16', '2019-03-14 13:31:16');
INSERT INTO `admin_operation_log` VALUES (189, 2, 'padmin/company_certification/2/edit', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-14 13:31:26', '2019-03-14 13:31:26');
INSERT INTO `admin_operation_log` VALUES (190, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-14 13:31:36', '2019-03-14 13:31:36');
INSERT INTO `admin_operation_log` VALUES (191, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-14 13:40:32', '2019-03-14 13:40:32');
INSERT INTO `admin_operation_log` VALUES (192, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 13:48:07', '2019-03-14 13:48:07');
INSERT INTO `admin_operation_log` VALUES (193, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 13:48:20', '2019-03-14 13:48:20');
INSERT INTO `admin_operation_log` VALUES (194, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 13:52:00', '2019-03-14 13:52:00');
INSERT INTO `admin_operation_log` VALUES (195, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 13:52:21', '2019-03-14 13:52:21');
INSERT INTO `admin_operation_log` VALUES (196, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 13:53:53', '2019-03-14 13:53:53');
INSERT INTO `admin_operation_log` VALUES (197, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 13:54:01', '2019-03-14 13:54:01');
INSERT INTO `admin_operation_log` VALUES (198, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 13:54:11', '2019-03-14 13:54:11');
INSERT INTO `admin_operation_log` VALUES (199, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 13:54:47', '2019-03-14 13:54:47');
INSERT INTO `admin_operation_log` VALUES (200, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 13:59:10', '2019-03-14 13:59:10');
INSERT INTO `admin_operation_log` VALUES (201, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 13:59:34', '2019-03-14 13:59:34');
INSERT INTO `admin_operation_log` VALUES (202, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 13:59:46', '2019-03-14 13:59:46');
INSERT INTO `admin_operation_log` VALUES (203, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 14:00:53', '2019-03-14 14:00:53');
INSERT INTO `admin_operation_log` VALUES (204, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 14:01:06', '2019-03-14 14:01:06');
INSERT INTO `admin_operation_log` VALUES (205, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-14 14:01:13', '2019-03-14 14:01:13');
INSERT INTO `admin_operation_log` VALUES (206, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-14 14:01:31', '2019-03-14 14:01:31');
INSERT INTO `admin_operation_log` VALUES (207, 2, 'padmin/company_certification/3', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-14 14:01:47', '2019-03-14 14:01:47');
INSERT INTO `admin_operation_log` VALUES (208, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-14 14:01:48', '2019-03-14 14:01:48');
INSERT INTO `admin_operation_log` VALUES (209, 2, 'padmin/company_certification/3', 'GET', '192.168.10.1', '[]', '2019-03-14 14:02:32', '2019-03-14 14:02:32');
INSERT INTO `admin_operation_log` VALUES (210, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 14:04:41', '2019-03-14 14:04:41');
INSERT INTO `admin_operation_log` VALUES (211, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 14:05:10', '2019-03-14 14:05:10');
INSERT INTO `admin_operation_log` VALUES (212, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 14:05:55', '2019-03-14 14:05:55');
INSERT INTO `admin_operation_log` VALUES (213, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 14:06:30', '2019-03-14 14:06:30');
INSERT INTO `admin_operation_log` VALUES (214, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 14:07:22', '2019-03-14 14:07:22');
INSERT INTO `admin_operation_log` VALUES (215, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 14:07:58', '2019-03-14 14:07:58');
INSERT INTO `admin_operation_log` VALUES (216, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 14:08:06', '2019-03-14 14:08:06');
INSERT INTO `admin_operation_log` VALUES (217, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 14:08:59', '2019-03-14 14:08:59');
INSERT INTO `admin_operation_log` VALUES (218, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 15:14:59', '2019-03-14 15:14:59');
INSERT INTO `admin_operation_log` VALUES (219, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 15:15:25', '2019-03-14 15:15:25');
INSERT INTO `admin_operation_log` VALUES (220, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 15:18:38', '2019-03-14 15:18:38');
INSERT INTO `admin_operation_log` VALUES (221, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 15:19:13', '2019-03-14 15:19:13');
INSERT INTO `admin_operation_log` VALUES (222, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 15:20:04', '2019-03-14 15:20:04');
INSERT INTO `admin_operation_log` VALUES (223, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 15:21:34', '2019-03-14 15:21:34');
INSERT INTO `admin_operation_log` VALUES (224, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 15:22:17', '2019-03-14 15:22:17');
INSERT INTO `admin_operation_log` VALUES (225, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 15:22:31', '2019-03-14 15:22:31');
INSERT INTO `admin_operation_log` VALUES (226, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 15:28:50', '2019-03-14 15:28:50');
INSERT INTO `admin_operation_log` VALUES (227, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 15:29:53', '2019-03-14 15:29:53');
INSERT INTO `admin_operation_log` VALUES (228, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 15:30:47', '2019-03-14 15:30:47');
INSERT INTO `admin_operation_log` VALUES (229, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 15:30:53', '2019-03-14 15:30:53');
INSERT INTO `admin_operation_log` VALUES (230, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 15:31:09', '2019-03-14 15:31:09');
INSERT INTO `admin_operation_log` VALUES (231, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 15:31:12', '2019-03-14 15:31:12');
INSERT INTO `admin_operation_log` VALUES (232, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 15:31:17', '2019-03-14 15:31:17');
INSERT INTO `admin_operation_log` VALUES (233, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 15:31:23', '2019-03-14 15:31:23');
INSERT INTO `admin_operation_log` VALUES (234, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 15:32:19', '2019-03-14 15:32:19');
INSERT INTO `admin_operation_log` VALUES (235, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 15:32:26', '2019-03-14 15:32:26');
INSERT INTO `admin_operation_log` VALUES (236, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 15:33:09', '2019-03-14 15:33:09');
INSERT INTO `admin_operation_log` VALUES (237, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 15:33:09', '2019-03-14 15:33:09');
INSERT INTO `admin_operation_log` VALUES (238, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 15:33:50', '2019-03-14 15:33:50');
INSERT INTO `admin_operation_log` VALUES (239, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 15:34:13', '2019-03-14 15:34:13');
INSERT INTO `admin_operation_log` VALUES (240, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 15:35:18', '2019-03-14 15:35:18');
INSERT INTO `admin_operation_log` VALUES (241, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 15:35:32', '2019-03-14 15:35:32');
INSERT INTO `admin_operation_log` VALUES (242, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 15:35:42', '2019-03-14 15:35:42');
INSERT INTO `admin_operation_log` VALUES (243, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 15:39:08', '2019-03-14 15:39:08');
INSERT INTO `admin_operation_log` VALUES (244, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 15:39:54', '2019-03-14 15:39:54');
INSERT INTO `admin_operation_log` VALUES (245, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 15:40:15', '2019-03-14 15:40:15');
INSERT INTO `admin_operation_log` VALUES (246, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 15:41:25', '2019-03-14 15:41:25');
INSERT INTO `admin_operation_log` VALUES (247, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 15:42:58', '2019-03-14 15:42:58');
INSERT INTO `admin_operation_log` VALUES (248, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 15:43:19', '2019-03-14 15:43:19');
INSERT INTO `admin_operation_log` VALUES (249, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 15:43:29', '2019-03-14 15:43:29');
INSERT INTO `admin_operation_log` VALUES (250, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 15:43:42', '2019-03-14 15:43:42');
INSERT INTO `admin_operation_log` VALUES (251, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 15:44:09', '2019-03-14 15:44:09');
INSERT INTO `admin_operation_log` VALUES (252, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 15:44:39', '2019-03-14 15:44:39');
INSERT INTO `admin_operation_log` VALUES (253, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 15:45:04', '2019-03-14 15:45:04');
INSERT INTO `admin_operation_log` VALUES (254, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 15:47:53', '2019-03-14 15:47:53');
INSERT INTO `admin_operation_log` VALUES (255, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 15:50:56', '2019-03-14 15:50:56');
INSERT INTO `admin_operation_log` VALUES (256, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 15:59:18', '2019-03-14 15:59:18');
INSERT INTO `admin_operation_log` VALUES (257, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 15:59:34', '2019-03-14 15:59:34');
INSERT INTO `admin_operation_log` VALUES (258, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 16:00:03', '2019-03-14 16:00:03');
INSERT INTO `admin_operation_log` VALUES (259, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 16:00:28', '2019-03-14 16:00:28');
INSERT INTO `admin_operation_log` VALUES (260, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 16:00:42', '2019-03-14 16:00:42');
INSERT INTO `admin_operation_log` VALUES (261, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 16:01:11', '2019-03-14 16:01:11');
INSERT INTO `admin_operation_log` VALUES (262, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 16:02:11', '2019-03-14 16:02:11');
INSERT INTO `admin_operation_log` VALUES (263, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 16:12:54', '2019-03-14 16:12:54');
INSERT INTO `admin_operation_log` VALUES (264, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 16:13:24', '2019-03-14 16:13:24');
INSERT INTO `admin_operation_log` VALUES (265, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 16:15:26', '2019-03-14 16:15:26');
INSERT INTO `admin_operation_log` VALUES (266, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 16:16:35', '2019-03-14 16:16:35');
INSERT INTO `admin_operation_log` VALUES (267, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 16:17:44', '2019-03-14 16:17:44');
INSERT INTO `admin_operation_log` VALUES (268, 2, 'padmin/company_certification/1', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-14 16:17:58', '2019-03-14 16:17:58');
INSERT INTO `admin_operation_log` VALUES (269, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 16:17:59', '2019-03-14 16:17:59');
INSERT INTO `admin_operation_log` VALUES (270, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-14 16:18:06', '2019-03-14 16:18:06');
INSERT INTO `admin_operation_log` VALUES (271, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 16:18:58', '2019-03-14 16:18:58');
INSERT INTO `admin_operation_log` VALUES (272, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 16:19:08', '2019-03-14 16:19:08');
INSERT INTO `admin_operation_log` VALUES (273, 2, 'padmin/company_certification/1', 'PUT', '192.168.10.1', '{\"_token\":\"Pu1wZDMg8lLyR8P7ngMv2viAV9rK4yg2RZMfb9aj\",\"_method\":\"PUT\"}', '2019-03-14 16:19:25', '2019-03-14 16:19:25');
INSERT INTO `admin_operation_log` VALUES (274, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-14 16:19:26', '2019-03-14 16:19:26');
INSERT INTO `admin_operation_log` VALUES (275, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-14 16:19:34', '2019-03-14 16:19:34');
INSERT INTO `admin_operation_log` VALUES (276, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 16:22:12', '2019-03-14 16:22:12');
INSERT INTO `admin_operation_log` VALUES (277, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 16:22:48', '2019-03-14 16:22:48');
INSERT INTO `admin_operation_log` VALUES (278, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 16:23:29', '2019-03-14 16:23:29');
INSERT INTO `admin_operation_log` VALUES (279, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 16:37:37', '2019-03-14 16:37:37');
INSERT INTO `admin_operation_log` VALUES (280, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 16:40:23', '2019-03-14 16:40:23');
INSERT INTO `admin_operation_log` VALUES (281, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 16:41:52', '2019-03-14 16:41:52');
INSERT INTO `admin_operation_log` VALUES (282, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 16:43:12', '2019-03-14 16:43:12');
INSERT INTO `admin_operation_log` VALUES (283, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 16:44:17', '2019-03-14 16:44:17');
INSERT INTO `admin_operation_log` VALUES (284, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 16:45:37', '2019-03-14 16:45:37');
INSERT INTO `admin_operation_log` VALUES (285, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 16:45:48', '2019-03-14 16:45:48');
INSERT INTO `admin_operation_log` VALUES (286, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 16:45:57', '2019-03-14 16:45:57');
INSERT INTO `admin_operation_log` VALUES (287, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 16:47:31', '2019-03-14 16:47:31');
INSERT INTO `admin_operation_log` VALUES (288, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 16:48:18', '2019-03-14 16:48:18');
INSERT INTO `admin_operation_log` VALUES (289, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 16:48:30', '2019-03-14 16:48:30');
INSERT INTO `admin_operation_log` VALUES (290, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 16:49:42', '2019-03-14 16:49:42');
INSERT INTO `admin_operation_log` VALUES (291, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 16:50:08', '2019-03-14 16:50:08');
INSERT INTO `admin_operation_log` VALUES (292, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-14 16:52:42', '2019-03-14 16:52:42');
INSERT INTO `admin_operation_log` VALUES (293, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-14 16:52:59', '2019-03-14 16:52:59');
INSERT INTO `admin_operation_log` VALUES (294, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-14 16:56:19', '2019-03-14 16:56:19');
INSERT INTO `admin_operation_log` VALUES (295, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-14 16:56:40', '2019-03-14 16:56:40');
INSERT INTO `admin_operation_log` VALUES (296, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-14 16:57:11', '2019-03-14 16:57:11');
INSERT INTO `admin_operation_log` VALUES (297, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-14 17:03:56', '2019-03-14 17:03:56');
INSERT INTO `admin_operation_log` VALUES (298, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-14 17:04:51', '2019-03-14 17:04:51');
INSERT INTO `admin_operation_log` VALUES (299, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-15 08:20:19', '2019-03-15 08:20:19');
INSERT INTO `admin_operation_log` VALUES (300, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-15 08:20:40', '2019-03-15 08:20:40');
INSERT INTO `admin_operation_log` VALUES (301, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-15 09:06:25', '2019-03-15 09:06:25');
INSERT INTO `admin_operation_log` VALUES (302, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-15 09:06:40', '2019-03-15 09:06:40');
INSERT INTO `admin_operation_log` VALUES (303, 2, 'padmin/company_certification/3', 'PUT', '192.168.10.1', '{\"verified\":\"2\",\"_token\":\"sPFHo3oaO4AZq6UrroufGnunRuYv1paHYE6b28IE\",\"_method\":\"PUT\"}', '2019-03-15 09:09:43', '2019-03-15 09:09:43');
INSERT INTO `admin_operation_log` VALUES (304, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-15 09:09:44', '2019-03-15 09:09:44');
INSERT INTO `admin_operation_log` VALUES (305, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-15 09:09:55', '2019-03-15 09:09:55');
INSERT INTO `admin_operation_log` VALUES (306, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-15 09:16:44', '2019-03-15 09:16:44');
INSERT INTO `admin_operation_log` VALUES (307, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '{\"per_page\":\"10\",\"_pjax\":\"#pjax-container\"}', '2019-03-15 09:18:29', '2019-03-15 09:18:29');
INSERT INTO `admin_operation_log` VALUES (308, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '{\"per_page\":\"10\"}', '2019-03-15 09:57:50', '2019-03-15 09:57:50');
INSERT INTO `admin_operation_log` VALUES (309, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-15 09:57:55', '2019-03-15 09:57:55');
INSERT INTO `admin_operation_log` VALUES (310, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-15 10:00:41', '2019-03-15 10:00:41');
INSERT INTO `admin_operation_log` VALUES (311, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-15 10:05:22', '2019-03-15 10:05:22');
INSERT INTO `admin_operation_log` VALUES (312, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-15 10:06:10', '2019-03-15 10:06:10');
INSERT INTO `admin_operation_log` VALUES (313, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-15 10:06:45', '2019-03-15 10:06:45');
INSERT INTO `admin_operation_log` VALUES (314, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-15 10:08:47', '2019-03-15 10:08:47');
INSERT INTO `admin_operation_log` VALUES (315, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-15 10:09:31', '2019-03-15 10:09:31');
INSERT INTO `admin_operation_log` VALUES (316, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-15 10:21:41', '2019-03-15 10:21:41');
INSERT INTO `admin_operation_log` VALUES (317, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-27 08:53:29', '2019-03-27 08:53:29');
INSERT INTO `admin_operation_log` VALUES (318, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-27 08:54:35', '2019-03-27 08:54:35');
INSERT INTO `admin_operation_log` VALUES (319, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-27 09:05:41', '2019-03-27 09:05:41');
INSERT INTO `admin_operation_log` VALUES (320, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-27 09:06:25', '2019-03-27 09:06:25');
INSERT INTO `admin_operation_log` VALUES (321, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-27 09:06:30', '2019-03-27 09:06:30');
INSERT INTO `admin_operation_log` VALUES (322, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-27 09:06:58', '2019-03-27 09:06:58');
INSERT INTO `admin_operation_log` VALUES (323, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-27 09:08:25', '2019-03-27 09:08:25');
INSERT INTO `admin_operation_log` VALUES (324, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-27 09:25:23', '2019-03-27 09:25:23');
INSERT INTO `admin_operation_log` VALUES (325, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-27 09:27:39', '2019-03-27 09:27:39');
INSERT INTO `admin_operation_log` VALUES (326, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-27 09:29:55', '2019-03-27 09:29:55');
INSERT INTO `admin_operation_log` VALUES (327, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-27 09:31:43', '2019-03-27 09:31:43');
INSERT INTO `admin_operation_log` VALUES (328, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-27 09:33:23', '2019-03-27 09:33:23');
INSERT INTO `admin_operation_log` VALUES (329, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-27 09:39:25', '2019-03-27 09:39:25');
INSERT INTO `admin_operation_log` VALUES (330, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-27 09:39:46', '2019-03-27 09:39:46');
INSERT INTO `admin_operation_log` VALUES (331, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-27 09:40:32', '2019-03-27 09:40:32');
INSERT INTO `admin_operation_log` VALUES (332, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-27 09:42:50', '2019-03-27 09:42:50');
INSERT INTO `admin_operation_log` VALUES (333, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-27 09:46:07', '2019-03-27 09:46:07');
INSERT INTO `admin_operation_log` VALUES (334, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-27 09:46:43', '2019-03-27 09:46:43');
INSERT INTO `admin_operation_log` VALUES (335, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-27 09:46:58', '2019-03-27 09:46:58');
INSERT INTO `admin_operation_log` VALUES (336, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-27 09:47:31', '2019-03-27 09:47:31');
INSERT INTO `admin_operation_log` VALUES (337, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-27 09:49:09', '2019-03-27 09:49:09');
INSERT INTO `admin_operation_log` VALUES (338, 2, 'padmin/company_certification/3/edit', 'GET', '192.168.10.1', '[]', '2019-03-27 09:50:34', '2019-03-27 09:50:34');
INSERT INTO `admin_operation_log` VALUES (339, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-27 09:53:57', '2019-03-27 09:53:57');
INSERT INTO `admin_operation_log` VALUES (340, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-27 09:58:01', '2019-03-27 09:58:01');
INSERT INTO `admin_operation_log` VALUES (341, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-27 10:07:36', '2019-03-27 10:07:36');
INSERT INTO `admin_operation_log` VALUES (342, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-27 10:08:25', '2019-03-27 10:08:25');
INSERT INTO `admin_operation_log` VALUES (343, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-27 10:09:18', '2019-03-27 10:09:18');
INSERT INTO `admin_operation_log` VALUES (344, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-27 10:09:26', '2019-03-27 10:09:26');
INSERT INTO `admin_operation_log` VALUES (345, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-27 10:10:43', '2019-03-27 10:10:43');
INSERT INTO `admin_operation_log` VALUES (346, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-27 10:12:11', '2019-03-27 10:12:11');
INSERT INTO `admin_operation_log` VALUES (347, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-27 10:12:26', '2019-03-27 10:12:26');
INSERT INTO `admin_operation_log` VALUES (348, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-27 10:13:18', '2019-03-27 10:13:18');
INSERT INTO `admin_operation_log` VALUES (349, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-27 10:13:37', '2019-03-27 10:13:37');
INSERT INTO `admin_operation_log` VALUES (350, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-27 10:13:58', '2019-03-27 10:13:58');
INSERT INTO `admin_operation_log` VALUES (351, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-27 10:14:16', '2019-03-27 10:14:16');
INSERT INTO `admin_operation_log` VALUES (352, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-27 10:14:40', '2019-03-27 10:14:40');
INSERT INTO `admin_operation_log` VALUES (353, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-27 10:14:52', '2019-03-27 10:14:52');
INSERT INTO `admin_operation_log` VALUES (354, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-27 10:15:15', '2019-03-27 10:15:15');
INSERT INTO `admin_operation_log` VALUES (355, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-27 10:15:30', '2019-03-27 10:15:30');
INSERT INTO `admin_operation_log` VALUES (356, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-27 10:16:08', '2019-03-27 10:16:08');
INSERT INTO `admin_operation_log` VALUES (357, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-27 10:16:23', '2019-03-27 10:16:23');
INSERT INTO `admin_operation_log` VALUES (358, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-27 10:17:16', '2019-03-27 10:17:16');
INSERT INTO `admin_operation_log` VALUES (359, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-27 10:17:27', '2019-03-27 10:17:27');
INSERT INTO `admin_operation_log` VALUES (360, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-27 10:17:55', '2019-03-27 10:17:55');
INSERT INTO `admin_operation_log` VALUES (361, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-27 10:19:29', '2019-03-27 10:19:29');
INSERT INTO `admin_operation_log` VALUES (362, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-27 10:21:17', '2019-03-27 10:21:17');
INSERT INTO `admin_operation_log` VALUES (363, 2, 'padmin/company_certification/1', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-27 10:22:23', '2019-03-27 10:22:23');
INSERT INTO `admin_operation_log` VALUES (364, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-27 10:22:25', '2019-03-27 10:22:25');
INSERT INTO `admin_operation_log` VALUES (365, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-27 10:24:03', '2019-03-27 10:24:03');
INSERT INTO `admin_operation_log` VALUES (366, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-27 10:26:40', '2019-03-27 10:26:40');
INSERT INTO `admin_operation_log` VALUES (367, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-27 10:26:57', '2019-03-27 10:26:57');
INSERT INTO `admin_operation_log` VALUES (368, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-27 10:27:18', '2019-03-27 10:27:18');
INSERT INTO `admin_operation_log` VALUES (369, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-27 10:27:37', '2019-03-27 10:27:37');
INSERT INTO `admin_operation_log` VALUES (370, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-27 10:28:02', '2019-03-27 10:28:02');
INSERT INTO `admin_operation_log` VALUES (371, 2, 'padmin/company_certification/2/edit', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-27 10:28:05', '2019-03-27 10:28:05');
INSERT INTO `admin_operation_log` VALUES (372, 2, 'padmin/company_certification/2/edit', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-27 10:28:09', '2019-03-27 10:28:09');
INSERT INTO `admin_operation_log` VALUES (373, 2, 'padmin/company_certification/2/edit', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-27 10:28:14', '2019-03-27 10:28:14');
INSERT INTO `admin_operation_log` VALUES (374, 2, 'padmin/company_certification/2/edit', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-27 10:28:16', '2019-03-27 10:28:16');
INSERT INTO `admin_operation_log` VALUES (375, 2, 'padmin/company_certification/2/edit', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-27 10:28:22', '2019-03-27 10:28:22');
INSERT INTO `admin_operation_log` VALUES (376, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-27 10:28:25', '2019-03-27 10:28:25');
INSERT INTO `admin_operation_log` VALUES (377, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-27 10:28:29', '2019-03-27 10:28:29');
INSERT INTO `admin_operation_log` VALUES (378, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-03-27 10:29:00', '2019-03-27 10:29:00');
INSERT INTO `admin_operation_log` VALUES (379, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-27 10:29:09', '2019-03-27 10:29:09');
INSERT INTO `admin_operation_log` VALUES (380, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-27 10:31:02', '2019-03-27 10:31:02');
INSERT INTO `admin_operation_log` VALUES (381, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-03-27 10:31:13', '2019-03-27 10:31:13');
INSERT INTO `admin_operation_log` VALUES (382, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-03-27 11:29:51', '2019-03-27 11:29:51');
INSERT INTO `admin_operation_log` VALUES (383, 2, 'padmin', 'GET', '192.168.10.1', '[]', '2019-04-09 10:10:16', '2019-04-09 10:10:16');
INSERT INTO `admin_operation_log` VALUES (384, 2, 'padmin', 'GET', '192.168.10.1', '[]', '2019-04-09 10:10:54', '2019-04-09 10:10:54');
INSERT INTO `admin_operation_log` VALUES (385, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-04-09 10:11:40', '2019-04-09 10:11:40');
INSERT INTO `admin_operation_log` VALUES (386, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-04-09 10:12:01', '2019-04-09 10:12:01');
INSERT INTO `admin_operation_log` VALUES (387, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-04-09 10:12:12', '2019-04-09 10:12:12');
INSERT INTO `admin_operation_log` VALUES (388, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-04-09 13:40:47', '2019-04-09 13:40:47');
INSERT INTO `admin_operation_log` VALUES (389, 2, 'padmin', 'GET', '192.168.10.1', '[]', '2019-04-09 16:08:34', '2019-04-09 16:08:34');
INSERT INTO `admin_operation_log` VALUES (390, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-04-09 16:09:31', '2019-04-09 16:09:31');
INSERT INTO `admin_operation_log` VALUES (391, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-04-09 16:09:41', '2019-04-09 16:09:41');
INSERT INTO `admin_operation_log` VALUES (392, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-04-09 16:09:54', '2019-04-09 16:09:54');
INSERT INTO `admin_operation_log` VALUES (393, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-04-09 16:12:24', '2019-04-09 16:12:24');
INSERT INTO `admin_operation_log` VALUES (394, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-04-09 16:18:22', '2019-04-09 16:18:22');
INSERT INTO `admin_operation_log` VALUES (395, 2, 'padmin/company_certification/15/edit', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-04-09 16:18:31', '2019-04-09 16:18:31');
INSERT INTO `admin_operation_log` VALUES (396, 2, 'padmin/company_certification/15/edit', 'GET', '192.168.10.1', '[]', '2019-04-09 16:18:37', '2019-04-09 16:18:37');
INSERT INTO `admin_operation_log` VALUES (397, 2, 'padmin/company_certification/15', 'PUT', '192.168.10.1', '{\"verified\":\"2\",\"_token\":\"kMwJGQGWzGPDsiPyn2grNsTKIhhmpV7Q4KVVRRje\",\"_method\":\"PUT\",\"_previous_\":\"http:\\/\\/gzt.test\\/padmin\\/company_certification\"}', '2019-04-09 16:18:54', '2019-04-09 16:18:54');
INSERT INTO `admin_operation_log` VALUES (398, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-04-09 16:18:55', '2019-04-09 16:18:55');
INSERT INTO `admin_operation_log` VALUES (399, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-04-10 17:23:29', '2019-04-10 17:23:29');
INSERT INTO `admin_operation_log` VALUES (400, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-04-10 17:23:48', '2019-04-10 17:23:48');
INSERT INTO `admin_operation_log` VALUES (401, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '[]', '2019-04-10 17:24:24', '2019-04-10 17:24:24');
INSERT INTO `admin_operation_log` VALUES (402, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-04-10 17:24:30', '2019-04-10 17:24:30');
INSERT INTO `admin_operation_log` VALUES (403, 2, 'padmin/company_certification/15/edit', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-04-10 17:24:38', '2019-04-10 17:24:38');
INSERT INTO `admin_operation_log` VALUES (404, 2, 'padmin/company_certification/15', 'PUT', '192.168.10.1', '{\"verified\":\"3\",\"_token\":\"kMwJGQGWzGPDsiPyn2grNsTKIhhmpV7Q4KVVRRje\",\"_method\":\"PUT\",\"_previous_\":\"http:\\/\\/gzt.test\\/padmin\\/company_certification\"}', '2019-04-10 18:15:34', '2019-04-10 18:15:34');
INSERT INTO `admin_operation_log` VALUES (405, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-04-10 18:15:36', '2019-04-10 18:15:36');
INSERT INTO `admin_operation_log` VALUES (406, 2, 'padmin/company_certification', 'GET', '192.168.10.1', '[]', '2019-04-10 18:37:11', '2019-04-10 18:37:11');
INSERT INTO `admin_operation_log` VALUES (407, 2, 'padmin/company_certification/1/edit', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-04-10 18:37:21', '2019-04-10 18:37:21');
INSERT INTO `admin_operation_log` VALUES (408, 1, 'pstadmin', 'GET', '192.168.10.1', '[]', '2019-04-12 21:22:36', '2019-04-12 21:22:36');
INSERT INTO `admin_operation_log` VALUES (409, 1, 'pstadmin/auth/menu', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-04-12 21:26:01', '2019-04-12 21:26:01');
INSERT INTO `admin_operation_log` VALUES (410, 1, 'pstadmin/auth/menu', 'POST', '192.168.10.1', '{\"parent_id\":\"0\",\"title\":\"\\u5546\\u54c1\\u7ba1\\u7406\",\"icon\":\"fa-bars\",\"uri\":\"\\/products\",\"roles\":[null],\"permission\":null,\"_token\":\"j53D7GHfe1Lx82QKeUw6pMpfdTTHiqiXMxB0FKTp\"}', '2019-04-12 21:26:47', '2019-04-12 21:26:47');
INSERT INTO `admin_operation_log` VALUES (411, 1, 'pstadmin/auth/menu', 'GET', '192.168.10.1', '[]', '2019-04-12 21:26:49', '2019-04-12 21:26:49');
INSERT INTO `admin_operation_log` VALUES (412, 1, 'pstadmin/auth/menu', 'GET', '192.168.10.1', '[]', '2019-04-12 21:26:55', '2019-04-12 21:26:55');
INSERT INTO `admin_operation_log` VALUES (413, 1, 'pstadmin/products', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-04-12 21:27:12', '2019-04-12 21:27:12');
INSERT INTO `admin_operation_log` VALUES (414, 1, 'pstadmin/products', 'GET', '192.168.10.1', '[]', '2019-04-12 21:27:24', '2019-04-12 21:27:24');
INSERT INTO `admin_operation_log` VALUES (415, 1, 'pstadmin/products', 'GET', '192.168.10.1', '[]', '2019-04-12 21:27:28', '2019-04-12 21:27:28');
INSERT INTO `admin_operation_log` VALUES (416, 1, 'pstadmin/products/create', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-04-12 21:31:28', '2019-04-12 21:31:28');
INSERT INTO `admin_operation_log` VALUES (417, 1, 'pstadmin/products/create', 'GET', '192.168.10.1', '[]', '2019-04-12 21:32:12', '2019-04-12 21:32:12');
INSERT INTO `admin_operation_log` VALUES (418, 1, 'pstadmin/products/create', 'GET', '192.168.10.1', '[]', '2019-04-12 21:32:16', '2019-04-12 21:32:16');
INSERT INTO `admin_operation_log` VALUES (419, 1, 'pstadmin/products', 'GET', '192.168.10.1', '[]', '2019-04-12 21:32:22', '2019-04-12 21:32:22');
INSERT INTO `admin_operation_log` VALUES (420, 1, 'pstadmin/products/create', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-04-12 21:32:27', '2019-04-12 21:32:27');
INSERT INTO `admin_operation_log` VALUES (421, 1, 'pstadmin/products/create', 'GET', '192.168.10.1', '[]', '2019-04-12 21:33:12', '2019-04-12 21:33:12');
INSERT INTO `admin_operation_log` VALUES (422, 1, 'pstadmin/products', 'POST', '192.168.10.1', '{\"title\":\"\\u4eba\\u6570\\u7248\",\"description\":\"<p>\\u4eba\\u6570\\u7248<\\/p>\",\"on_sale\":\"1\",\"skus\":{\"new_1\":{\"title\":\"100\\u4eba\\u4ee5\\u4e0b\",\"description\":\"100\\u4eba\\u4ee5\\u4e0b\",\"price\":\"299\",\"id\":null,\"_remove_\":\"0\"},\"new_2\":{\"title\":\"200\\u4eba\\u4ee5\\u4e0b\",\"description\":\"200\\u4eba\\u4ee5\\u4e0b\",\"price\":\"259\",\"id\":null,\"_remove_\":\"0\"},\"new_3\":{\"title\":\"300\\u4eba\\u4ee5\\u4e0b\",\"description\":\"300\\u4eba\\u4ee5\\u4e0b\",\"price\":\"219\",\"id\":null,\"_remove_\":\"0\"},\"new_4\":{\"title\":\"400\\u4eba\\u4ee5\\u4e0b\",\"description\":\"400\\u4eba\\u4ee5\\u4e0b\",\"price\":\"179\",\"id\":null,\"_remove_\":\"0\"},\"new_5\":{\"title\":\"500\\u4eba\\u4ee5\\u4e0b\",\"description\":\"500\\u4eba\\u4ee5\\u4e0b\",\"price\":\"139\",\"id\":null,\"_remove_\":\"0\"}},\"_token\":\"j53D7GHfe1Lx82QKeUw6pMpfdTTHiqiXMxB0FKTp\",\"_previous_\":\"http:\\/\\/gzt.test\\/pstadmin\\/products\"}', '2019-04-12 21:35:33', '2019-04-12 21:35:33');
INSERT INTO `admin_operation_log` VALUES (423, 1, 'pstadmin/products/create', 'GET', '192.168.10.1', '[]', '2019-04-12 21:35:37', '2019-04-12 21:35:37');
INSERT INTO `admin_operation_log` VALUES (424, 1, 'pstadmin/products/create', 'GET', '192.168.10.1', '[]', '2019-04-12 21:41:26', '2019-04-12 21:41:26');
INSERT INTO `admin_operation_log` VALUES (425, 1, 'pstadmin/products', 'POST', '192.168.10.1', '{\"title\":\"\\u4eba\\u6570\\u7248\",\"description\":\"<p>\\u4eba\\u6570\\u7248<\\/p>\",\"on_sale\":\"1\",\"skus\":{\"new_1\":{\"title\":\"100\\u4eba\\u4ee5\\u4e0b\",\"description\":\"100\\u4eba\\u4ee5\\u4e0b\",\"price\":\"299\",\"id\":null,\"_remove_\":\"0\"},\"new_2\":{\"title\":\"200\\u4eba\\u4ee5\\u4e0b\",\"description\":\"200\\u4eba\\u4ee5\\u4e0b\",\"price\":\"259\",\"id\":null,\"_remove_\":\"0\"},\"new_3\":{\"title\":\"300\\u4eba\\u4ee5\\u4e0b\",\"description\":\"300\\u4eba\\u4ee5\\u4e0b\",\"price\":\"219\",\"id\":null,\"_remove_\":\"0\"},\"new_4\":{\"title\":\"400\\u4eba\\u4ee5\\u4e0b\",\"description\":\"400\\u4eba\\u4ee5\\u4e0b\",\"price\":\"179\",\"id\":null,\"_remove_\":\"0\"},\"new_5\":{\"title\":\"500\\u4eba\\u4ee5\\u4e0b\",\"description\":\"500\\u4eba\\u4ee5\\u4e0b\",\"price\":\"139\",\"id\":null,\"_remove_\":\"0\"}},\"_token\":\"j53D7GHfe1Lx82QKeUw6pMpfdTTHiqiXMxB0FKTp\"}', '2019-04-12 21:42:39', '2019-04-12 21:42:39');
INSERT INTO `admin_operation_log` VALUES (426, 1, 'pstadmin/products', 'GET', '192.168.10.1', '[]', '2019-04-12 21:42:45', '2019-04-12 21:42:45');
INSERT INTO `admin_operation_log` VALUES (427, 1, 'pstadmin/products/1/edit', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-04-12 21:44:57', '2019-04-12 21:44:57');
INSERT INTO `admin_operation_log` VALUES (428, 1, 'pstadmin/products', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-04-12 21:45:04', '2019-04-12 21:45:04');
INSERT INTO `admin_operation_log` VALUES (429, 1, 'pstadmin/products', 'GET', '192.168.10.1', '[]', '2019-04-14 11:10:10', '2019-04-14 11:10:10');
INSERT INTO `admin_operation_log` VALUES (430, 1, 'pstadmin/products/create', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-04-14 11:10:18', '2019-04-14 11:10:18');
INSERT INTO `admin_operation_log` VALUES (431, 1, 'pstadmin/products', 'POST', '192.168.10.1', '{\"title\":\"\\u77ed\\u4fe1\\u670d\\u52a1\",\"description\":\"<p>\\u77ed\\u4fe1\\u670d\\u52a1<\\/p>\",\"on_sale\":\"1\",\"skus\":{\"new_1\":{\"title\":\"5000\\u6761\",\"description\":\"5000\\u6761\",\"price\":\"500\",\"id\":null,\"_remove_\":\"0\"},\"new_2\":{\"title\":\"1\\u4e07\\u6761\",\"description\":\"1\\u4e07\\u6761\",\"price\":\"900\",\"id\":null,\"_remove_\":\"0\"},\"new_3\":{\"title\":\"2\\u4e07\\u6761\",\"description\":\"2\\u4e07\\u6761\",\"price\":\"1800\",\"id\":null,\"_remove_\":\"0\"}},\"_token\":\"KLNmoAwpBTDfoAPGYdkGwgAQQO5t6PVwgL1t6uN8\",\"_previous_\":\"http:\\/\\/gzt.test\\/pstadmin\\/products\"}', '2019-04-14 11:12:25', '2019-04-14 11:12:25');
INSERT INTO `admin_operation_log` VALUES (432, 1, 'pstadmin/products', 'GET', '192.168.10.1', '[]', '2019-04-14 11:12:27', '2019-04-14 11:12:27');
INSERT INTO `admin_operation_log` VALUES (433, 1, 'pstadmin/products/create', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-04-14 11:12:31', '2019-04-14 11:12:31');
INSERT INTO `admin_operation_log` VALUES (434, 1, 'pstadmin/products', 'POST', '192.168.10.1', '{\"title\":\"\\u7f51\\u76d8\\u5b58\\u50a8\",\"description\":\"<p>\\u7f51\\u76d8\\u5b58\\u50a8<\\/p>\",\"on_sale\":\"1\",\"skus\":{\"new_1\":{\"title\":\"200GB\",\"description\":\"200GB\",\"price\":\"500\",\"id\":null,\"_remove_\":\"0\"},\"new_2\":{\"title\":\"400GB\",\"description\":\"400GB\",\"price\":\"900\",\"id\":null,\"_remove_\":\"0\"},\"new_3\":{\"title\":\"800GB\",\"description\":\"800GB\",\"price\":\"1600\",\"id\":null,\"_remove_\":\"0\"},\"new_4\":{\"title\":\"1TB\",\"description\":\"1TB\",\"price\":\"1900\",\"id\":null,\"_remove_\":\"0\"}},\"_token\":\"KLNmoAwpBTDfoAPGYdkGwgAQQO5t6PVwgL1t6uN8\",\"_previous_\":\"http:\\/\\/gzt.test\\/pstadmin\\/products\"}', '2019-04-14 11:14:28', '2019-04-14 11:14:28');
INSERT INTO `admin_operation_log` VALUES (435, 1, 'pstadmin/products', 'GET', '192.168.10.1', '[]', '2019-04-14 11:14:31', '2019-04-14 11:14:31');
INSERT INTO `admin_operation_log` VALUES (436, 1, 'pstadmin/products/2/edit', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-04-14 11:29:34', '2019-04-14 11:29:34');
INSERT INTO `admin_operation_log` VALUES (437, 1, 'pstadmin/products/2', 'PUT', '192.168.10.1', '{\"title\":\"\\u77ed\\u4fe1\\u670d\\u52a1\",\"description\":\"<p>\\u77ed\\u4fe1\\u670d\\u52a1<\\/p>\",\"on_sale\":\"1\",\"skus\":{\"6\":{\"title\":\"5000\\u6761\",\"description\":\"5000\\u6761\",\"price\":\"0.1\",\"id\":\"6\",\"_remove_\":\"0\"},\"7\":{\"title\":\"1\\u4e07\\u6761\",\"description\":\"1\\u4e07\\u6761\",\"price\":\"0.09\",\"id\":\"7\",\"_remove_\":\"0\"},\"8\":{\"title\":\"2\\u4e07\\u6761\",\"description\":\"2\\u4e07\\u6761\",\"price\":\"0.085\",\"id\":\"8\",\"_remove_\":\"0\"}},\"_token\":\"KLNmoAwpBTDfoAPGYdkGwgAQQO5t6PVwgL1t6uN8\",\"_method\":\"PUT\",\"_previous_\":\"http:\\/\\/gzt.test\\/pstadmin\\/products\"}', '2019-04-14 11:30:42', '2019-04-14 11:30:42');
INSERT INTO `admin_operation_log` VALUES (438, 1, 'pstadmin/products', 'GET', '192.168.10.1', '[]', '2019-04-14 11:30:45', '2019-04-14 11:30:45');
INSERT INTO `admin_operation_log` VALUES (439, 1, 'pstadmin/products/3/edit', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-04-14 11:35:57', '2019-04-14 11:35:57');
INSERT INTO `admin_operation_log` VALUES (440, 1, 'pstadmin/products/3', 'PUT', '192.168.10.1', '{\"title\":\"\\u7f51\\u76d8\\u5b58\\u50a8\",\"description\":\"<p>\\u7f51\\u76d8\\u5b58\\u50a8<\\/p>\",\"on_sale\":\"1\",\"skus\":{\"9\":{\"title\":\"200GB\",\"description\":\"200GB\",\"price\":\"2.5\",\"id\":\"9\",\"_remove_\":\"0\"},\"10\":{\"title\":\"400GB\",\"description\":\"400GB\",\"price\":\"2.0\",\"id\":\"10\",\"_remove_\":\"0\"},\"11\":{\"title\":\"800GB\",\"description\":\"800GB\",\"price\":\"1.9\",\"id\":\"11\",\"_remove_\":\"0\"},\"12\":{\"title\":\"1TB\",\"description\":\"1TB\",\"price\":\"1.8\",\"id\":\"12\",\"_remove_\":\"0\"}},\"_token\":\"KLNmoAwpBTDfoAPGYdkGwgAQQO5t6PVwgL1t6uN8\",\"_method\":\"PUT\",\"_previous_\":\"http:\\/\\/gzt.test\\/pstadmin\\/products\"}', '2019-04-14 11:37:43', '2019-04-14 11:37:43');
INSERT INTO `admin_operation_log` VALUES (441, 1, 'pstadmin/products', 'GET', '192.168.10.1', '[]', '2019-04-14 11:37:47', '2019-04-14 11:37:47');
INSERT INTO `admin_operation_log` VALUES (442, 1, 'pstadmin/products', 'GET', '192.168.10.1', '[]', '2019-04-14 21:22:27', '2019-04-14 21:22:27');
INSERT INTO `admin_operation_log` VALUES (443, 1, 'pstadmin/products/1/edit', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-04-14 21:23:00', '2019-04-14 21:23:00');
INSERT INTO `admin_operation_log` VALUES (444, 1, 'pstadmin/products/1', 'PUT', '192.168.10.1', '{\"title\":\"\\u7ec4\\u7ec7\\u4eba\\u6570\",\"description\":\"<p>\\u4eba\\u6570\\u7248<\\/p>\",\"on_sale\":\"1\",\"skus\":{\"1\":{\"title\":\"100\\u4eba\\u4ee5\\u4e0b\",\"description\":\"100\\u4eba\\u4ee5\\u4e0b\",\"price\":\"299.00\",\"id\":\"1\",\"_remove_\":\"0\"},\"2\":{\"title\":\"200\\u4eba\\u4ee5\\u4e0b\",\"description\":\"200\\u4eba\\u4ee5\\u4e0b\",\"price\":\"259.00\",\"id\":\"2\",\"_remove_\":\"0\"},\"3\":{\"title\":\"300\\u4eba\\u4ee5\\u4e0b\",\"description\":\"300\\u4eba\\u4ee5\\u4e0b\",\"price\":\"219.00\",\"id\":\"3\",\"_remove_\":\"0\"},\"4\":{\"title\":\"400\\u4eba\\u4ee5\\u4e0b\",\"description\":\"400\\u4eba\\u4ee5\\u4e0b\",\"price\":\"179.00\",\"id\":\"4\",\"_remove_\":\"0\"},\"5\":{\"title\":\"500\\u4eba\\u4ee5\\u4e0b\",\"description\":\"500\\u4eba\\u4ee5\\u4e0b\",\"price\":\"139.00\",\"id\":\"5\",\"_remove_\":\"0\"}},\"_token\":\"Ke6EJu4tTVw3wTx65fZRxYjm8XNj8Bgfo4z2ahcs\",\"_method\":\"PUT\",\"_previous_\":\"http:\\/\\/gzt.test\\/pstadmin\\/products\"}', '2019-04-14 21:23:23', '2019-04-14 21:23:23');
INSERT INTO `admin_operation_log` VALUES (445, 1, 'pstadmin/products', 'GET', '192.168.10.1', '[]', '2019-04-14 21:23:25', '2019-04-14 21:23:25');
INSERT INTO `admin_operation_log` VALUES (446, 1, 'pstadmin/products', 'GET', '192.168.10.1', '[]', '2019-04-14 22:52:22', '2019-04-14 22:52:22');
INSERT INTO `admin_operation_log` VALUES (447, 1, 'pstadmin/auth/menu', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-04-14 22:55:58', '2019-04-14 22:55:58');
INSERT INTO `admin_operation_log` VALUES (448, 1, 'pstadmin/auth/menu', 'POST', '192.168.10.1', '{\"parent_id\":\"0\",\"title\":\"\\u8ba2\\u5355\\u7ba1\\u7406\",\"icon\":\"fa-bars\",\"uri\":\"\\/orders\",\"roles\":[null],\"permission\":null,\"_token\":\"Ke6EJu4tTVw3wTx65fZRxYjm8XNj8Bgfo4z2ahcs\"}', '2019-04-14 22:56:20', '2019-04-14 22:56:20');
INSERT INTO `admin_operation_log` VALUES (449, 1, 'pstadmin/auth/menu', 'GET', '192.168.10.1', '[]', '2019-04-14 22:56:23', '2019-04-14 22:56:23');
INSERT INTO `admin_operation_log` VALUES (450, 1, 'pstadmin/orders', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-04-14 22:56:36', '2019-04-14 22:56:36');
INSERT INTO `admin_operation_log` VALUES (451, 1, 'pstadmin/orders', 'GET', '192.168.10.1', '[]', '2019-04-14 22:59:26', '2019-04-14 22:59:26');
INSERT INTO `admin_operation_log` VALUES (452, 1, 'pstadmin/orders/23', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-04-14 22:59:31', '2019-04-14 22:59:31');
INSERT INTO `admin_operation_log` VALUES (453, 1, 'pstadmin/orders', 'GET', '192.168.10.1', '[]', '2019-04-14 22:59:34', '2019-04-14 22:59:34');
INSERT INTO `admin_operation_log` VALUES (454, 1, 'pstadmin/orders/24', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-04-14 23:00:33', '2019-04-14 23:00:33');
INSERT INTO `admin_operation_log` VALUES (455, 1, 'pstadmin/orders/24', 'GET', '192.168.10.1', '[]', '2019-04-14 23:00:35', '2019-04-14 23:00:35');
INSERT INTO `admin_operation_log` VALUES (456, 1, 'pstadmin/orders/24', 'GET', '192.168.10.1', '[]', '2019-04-14 23:01:06', '2019-04-14 23:01:06');
INSERT INTO `admin_operation_log` VALUES (457, 1, 'pstadmin/orders', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-04-14 23:01:39', '2019-04-14 23:01:39');
INSERT INTO `admin_operation_log` VALUES (458, 1, 'pstadmin/orders/24', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-04-14 23:01:44', '2019-04-14 23:01:44');
INSERT INTO `admin_operation_log` VALUES (459, 1, 'pstadmin/orders/24', 'GET', '192.168.10.1', '[]', '2019-04-14 23:02:45', '2019-04-14 23:02:45');
INSERT INTO `admin_operation_log` VALUES (460, 1, 'pstadmin/orders/24', 'GET', '192.168.10.1', '[]', '2019-04-14 23:03:35', '2019-04-14 23:03:35');
INSERT INTO `admin_operation_log` VALUES (461, 1, 'pstadmin/orders/24', 'GET', '192.168.10.1', '[]', '2019-04-14 23:03:51', '2019-04-14 23:03:51');
INSERT INTO `admin_operation_log` VALUES (462, 1, 'pstadmin/orders/24', 'GET', '192.168.10.1', '[]', '2019-04-14 23:05:09', '2019-04-14 23:05:09');
INSERT INTO `admin_operation_log` VALUES (463, 1, 'pstadmin/orders/24', 'GET', '192.168.10.1', '[]', '2019-04-14 23:05:59', '2019-04-14 23:05:59');
INSERT INTO `admin_operation_log` VALUES (464, 1, 'pstadmin/orders/24', 'GET', '192.168.10.1', '[]', '2019-04-14 23:06:17', '2019-04-14 23:06:17');
INSERT INTO `admin_operation_log` VALUES (465, 1, 'pstadmin/orders/24', 'GET', '192.168.10.1', '[]', '2019-04-14 23:06:48', '2019-04-14 23:06:48');
INSERT INTO `admin_operation_log` VALUES (466, 1, 'pstadmin/orders', 'GET', '192.168.10.1', '{\"_pjax\":\"#pjax-container\"}', '2019-04-14 23:07:18', '2019-04-14 23:07:18');

-- ----------------------------
-- Table structure for admin_permissions
-- ----------------------------
DROP TABLE IF EXISTS `admin_permissions`;
CREATE TABLE `admin_permissions`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `http_method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `http_path` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `admin_permissions_name_unique`(`name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admin_permissions
-- ----------------------------
INSERT INTO `admin_permissions` VALUES (1, 'All permission', '*', '', '*', NULL, NULL);
INSERT INTO `admin_permissions` VALUES (2, 'Dashboard', 'dashboard', 'GET', '/', NULL, NULL);
INSERT INTO `admin_permissions` VALUES (3, 'Login', 'auth.login', '', '/auth/login\r\n/auth/logout', NULL, NULL);
INSERT INTO `admin_permissions` VALUES (4, 'User setting', 'auth.setting', 'GET,PUT', '/auth/setting', NULL, NULL);
INSERT INTO `admin_permissions` VALUES (5, 'Auth management', 'auth.management', '', '/auth/roles\r\n/auth/permissions\r\n/auth/menu\r\n/auth/logs', NULL, NULL);

-- ----------------------------
-- Table structure for admin_role_menu
-- ----------------------------
DROP TABLE IF EXISTS `admin_role_menu`;
CREATE TABLE `admin_role_menu`  (
  `role_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  INDEX `admin_role_menu_role_id_menu_id_index`(`role_id`, `menu_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admin_role_menu
-- ----------------------------
INSERT INTO `admin_role_menu` VALUES (1, 2, NULL, NULL);
INSERT INTO `admin_role_menu` VALUES (1, 8, NULL, NULL);
INSERT INTO `admin_role_menu` VALUES (1, 9, NULL, NULL);

-- ----------------------------
-- Table structure for admin_role_permissions
-- ----------------------------
DROP TABLE IF EXISTS `admin_role_permissions`;
CREATE TABLE `admin_role_permissions`  (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  INDEX `admin_role_permissions_role_id_permission_id_index`(`role_id`, `permission_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admin_role_permissions
-- ----------------------------
INSERT INTO `admin_role_permissions` VALUES (1, 1, NULL, NULL);

-- ----------------------------
-- Table structure for admin_role_users
-- ----------------------------
DROP TABLE IF EXISTS `admin_role_users`;
CREATE TABLE `admin_role_users`  (
  `role_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  INDEX `admin_role_users_role_id_user_id_index`(`role_id`, `user_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admin_role_users
-- ----------------------------
INSERT INTO `admin_role_users` VALUES (1, 1, NULL, NULL);
INSERT INTO `admin_role_users` VALUES (1, 2, NULL, NULL);

-- ----------------------------
-- Table structure for admin_roles
-- ----------------------------
DROP TABLE IF EXISTS `admin_roles`;
CREATE TABLE `admin_roles`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `admin_roles_name_unique`(`name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admin_roles
-- ----------------------------
INSERT INTO `admin_roles` VALUES (1, 'Administrator', 'administrator', '2018-11-17 16:19:17', '2018-11-17 16:19:17');

-- ----------------------------
-- Table structure for admin_user_permissions
-- ----------------------------
DROP TABLE IF EXISTS `admin_user_permissions`;
CREATE TABLE `admin_user_permissions`  (
  `user_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  INDEX `admin_user_permissions_user_id_permission_id_index`(`user_id`, `permission_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for admin_users
-- ----------------------------
DROP TABLE IF EXISTS `admin_users`;
CREATE TABLE `admin_users`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `admin_users_username_unique`(`username`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admin_users
-- ----------------------------
INSERT INTO `admin_users` VALUES (1, 'admin', '$2y$10$/1n6WYBhkZStv9ooOWbiBeoVpZ4vs3wijsAEAeOhlk0t6FKOuPbXK', 'Administrator', NULL, NULL, '2018-11-17 16:19:17', '2018-11-17 16:19:17');
INSERT INTO `admin_users` VALUES (2, 'aaa', '$2y$10$tapc8RUXs415WAFN.mrsQeaAwySPj96Y0Rsj5oKUZjLAUElxwtAi6', '侃大山', NULL, NULL, '2019-03-11 11:48:29', '2019-03-11 11:48:31');

-- ----------------------------
-- Table structure for approval
-- ----------------------------
DROP TABLE IF EXISTS `approval`;
CREATE TABLE `approval`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `applicant` int(11) NOT NULL COMMENT '该评审申请人id',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '审批名称',
  `type_id` int(11) NOT NULL COMMENT '类型id',
  `form_template` json NULL COMMENT '表单模板',
  `process_template` json NULL COMMENT '流程模板',
  `cc_my` json NULL COMMENT '抄送人信息',
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '审批描述',
  `end_status` int(11) NOT NULL DEFAULT 0 COMMENT '该审批最终状态(0进行中1通过或2不通过)',
  `cancel_or_archive` int(11) NOT NULL DEFAULT 0 COMMENT '默认为0.1是撤销,2是归档',
  `numbering` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '审批编号没有默认null',
  `company_id` int(11) NOT NULL COMMENT '公司id标志',
  `complete_time` datetime(0) NULL DEFAULT NULL,
  `archive_time` datetime(0) NULL DEFAULT NULL,
  `approval_method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '自由流程' COMMENT '流程方式',
  `opinion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '意见',
  `extra_data` json NULL COMMENT '从外部调用审批时需要传递的额外数据',
  `related_pst_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '相关的评审通id',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `related_pst_index`(`related_pst_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of approval
-- ----------------------------
INSERT INTO `approval` VALUES (1, 1, '123', 1, '[{\"type\": \"SELECT\", \"field\": {\"name\": \"category\", \"label\": \"分类\", \"required\": false}, \"value\": \"预算\"}, {\"type\": \"INPUT\", \"field\": {\"name\": \"amount_of_review\", \"label\": \"送审金额\", \"required\": false}, \"value\": \"12312\"}]', '[{\"type\": \"normal\", \"checkedInfo\": {\"checkedKeys\": [\"xVQtP021072\", \"Tu673A30599\", \"W9W3yA87761\", \"SGRamE97288\", \"UE7ha5154450\", \"sEDa7F11545\"], \"checkedPersonnels\": [{\"key\": \"sEDa7F11545\", \"type\": \"personnel\", \"title\": \"Pirvate\", \"linKey\": [{\"key\": \"caAAYg11545\", \"title\": \"探知科技\"}, {\"key\": \"xVQtP021072\", \"type\": \"department\", \"title\": \"人事部\"}, {\"key\": \"sEDa7F11545\", \"type\": \"personnel\", \"title\": \"Pirvate\"}]}]}}]', '[]', '此处是假数据,正确数据需要从表单中摘取', 0, 0, 'PST-11555322703', 1, NULL, NULL, '固定流程', '', '{\"state\": \"begin_start\", \"pst_id\": 6, \"content\": \"\", \"operate_id\": 1, \"callback_result\": null}', 6, '2019-04-15 18:05:03', '2019-04-15 18:05:03');

-- ----------------------------
-- Table structure for approval_cc_my
-- ----------------------------
DROP TABLE IF EXISTS `approval_cc_my`;
CREATE TABLE `approval_cc_my`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `approval_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL DEFAULT 0,
  `company_id` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for approval_template
-- ----------------------------
DROP TABLE IF EXISTS `approval_template`;
CREATE TABLE `approval_template`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '模板名称',
  `form_template` json NULL COMMENT '表单模板',
  `process_template` json NULL COMMENT '流程模板',
  `type_id` int(11) NOT NULL COMMENT '模板的类型id',
  `approval_method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '自由流程' COMMENT '审批方式,自由审批和固定审批',
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '审批模板描述',
  `numbering` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'HHHH' COMMENT '审批编号默认是\"GZT\"',
  `company_id` int(11) NULL DEFAULT NULL COMMENT '公司id',
  `is_show` tinyint(4) NULL DEFAULT 1,
  `per` json NULL COMMENT '模板可见范围',
  `cc_user` json NULL COMMENT '抄送人',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  `deleted_at` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 68 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of approval_template
-- ----------------------------
INSERT INTO `approval_template` VALUES (1, '出勤', '[{\"type\": \"TEXTAREA\", \"field\": {\"label\": \"多行文本框\", \"required\": false, \"placeholder\": \"请输入\"}}, {\"type\": \"CHECKBOX\", \"field\": {\"label\": \"多选框\", \"required\": false, \"checkboxOptions\": [{\"key\": 1, \"value\": \"选项1\"}, {\"key\": 2, \"value\": \"选项2\"}, {\"key\": 3, \"value\": \"选项3\"}]}}, {\"type\": \"DATERANGE\", \"field\": {\"label\": \"日期区间\", \"required\": false}}, {\"type\": \"ANNEX\", \"field\": {\"label\": \"附件\", \"required\": false}}, {\"type\": \"MONEY\", \"field\": {\"label\": \"金额\", \"required\": false, \"placeholder\": \"请输入金额\"}}]', '[{\"type\": \"countersign\", \"checkedInfo\": {\"checkedKeys\": [\"100\", \"101\", \"102\", \"103\", \"35796314\", \"Ly5fEh287828\", \"qrxdXi306882\", \"8CoSqT297355\", \"VJbKqK316409\", \"ebhBuf325936\", \"vkoGt5335463\"], \"checkedPersonnels\": [{\"key\": \"100\", \"type\": \"personnel\", \"title\": \"黑蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"100\", \"type\": \"personnel\", \"title\": \"黑蛋\"}]}, {\"key\": \"101\", \"type\": \"personnel\", \"title\": \"狗蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"Ly5fEh287828\", \"type\": \"department\", \"title\": \"财务部\"}, {\"key\": \"101\", \"type\": \"personnel\", \"title\": \"狗蛋\"}]}, {\"key\": \"102\", \"type\": \"personnel\", \"title\": \"狗蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"Ly5fEh287828\", \"type\": \"department\", \"title\": \"财务部\"}, {\"key\": \"qrxdXi306882\", \"type\": \"department\", \"title\": \"小财务部\"}, {\"key\": \"102\", \"type\": \"personnel\", \"title\": \"狗蛋\"}]}, {\"key\": \"103\", \"type\": \"personnel\", \"title\": \"大锤\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"8CoSqT297355\", \"type\": \"department\", \"title\": \"人事部\"}, {\"key\": \"103\", \"type\": \"personnel\", \"title\": \"大锤\"}]}]}}]', 4, '自由流程', '经典模板--出勤', 'GZT', 0, 0, NULL, '{\"checkedKeys\": [\"100\", \"101\", \"102\", \"103\", \"35796314\", \"Ly5fEh287828\", \"qrxdXi306882\", \"8CoSqT297355\", \"VJbKqK316409\", \"ebhBuf325936\", \"vkoGt5335463\"], \"checkedPersonnels\": [{\"key\": \"100\", \"type\": \"personnel\", \"title\": \"黑蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"100\", \"type\": \"personnel\", \"title\": \"黑蛋\"}]}, {\"key\": \"101\", \"type\": \"personnel\", \"title\": \"狗蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"Ly5fEh287828\", \"type\": \"department\", \"title\": \"财务部\"}, {\"key\": \"101\", \"type\": \"personnel\", \"title\": \"狗蛋\"}]}, {\"key\": \"102\", \"type\": \"personnel\", \"title\": \"狗蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"Ly5fEh287828\", \"type\": \"department\", \"title\": \"财务部\"}, {\"key\": \"qrxdXi306882\", \"type\": \"department\", \"title\": \"小财务部\"}, {\"key\": \"102\", \"type\": \"personnel\", \"title\": \"狗蛋\"}]}, {\"key\": \"103\", \"type\": \"personnel\", \"title\": \"大锤\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"8CoSqT297355\", \"type\": \"department\", \"title\": \"人事部\"}, {\"key\": \"103\", \"type\": \"personnel\", \"title\": \"大锤\"}]}]}', '2019-01-25 17:24:42', '2019-01-26 15:39:43', NULL);
INSERT INTO `approval_template` VALUES (26, '13164377353', '[{\"type\": \"CHECKBOX\", \"field\": {\"label\": \"多选框\", \"required\": false, \"checkboxOptions\": [{\"key\": 1, \"value\": \"选项1\"}, {\"key\": 2, \"value\": \"选项2\"}, {\"key\": 3, \"value\": \"选项3\"}]}}, {\"type\": \"DATERANGE\", \"field\": {\"label\": \"日期区间\", \"required\": false}}]', '[{\"type\": \"countersign\", \"checkedInfo\": {\"checkedKeys\": [\"100\", \"101\", \"102\", \"103\", \"35796314\", \"Ly5fEh287828\", \"qrxdXi306882\", \"8CoSqT297355\", \"VJbKqK316409\", \"ebhBuf325936\", \"vkoGt5335463\"], \"checkedPersonnels\": [{\"key\": \"100\", \"type\": \"personnel\", \"title\": \"黑蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"100\", \"type\": \"personnel\", \"title\": \"黑蛋\"}]}, {\"key\": \"101\", \"type\": \"personnel\", \"title\": \"狗蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"Ly5fEh287828\", \"type\": \"department\", \"title\": \"财务部\"}, {\"key\": \"101\", \"type\": \"personnel\", \"title\": \"狗蛋\"}]}, {\"key\": \"102\", \"type\": \"personnel\", \"title\": \"狗蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"Ly5fEh287828\", \"type\": \"department\", \"title\": \"财务部\"}, {\"key\": \"qrxdXi306882\", \"type\": \"department\", \"title\": \"小财务部\"}, {\"key\": \"102\", \"type\": \"personnel\", \"title\": \"狗蛋\"}]}, {\"key\": \"103\", \"type\": \"personnel\", \"title\": \"大锤\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"8CoSqT297355\", \"type\": \"department\", \"title\": \"人事部\"}, {\"key\": \"103\", \"type\": \"personnel\", \"title\": \"大锤\"}]}]}}]', 8, '固定流程', '112111', '5888', 5, 1, '{\"staffId\": [\"7BdmJp30599\", \"7BdmJp21072\", \"7BdmJp40126\", \"7BdmJp11545\"], \"departmentId\": [\"35796314\", \"Ly5fEh287828\", \"qrxdXi306882\", \"8CoSqT297355\", \"VJbKqK316409\", \"ebhBuf325936\", \"vkoGt5335463\"]}', '{\"checkedKeys\": [\"100\", \"101\", \"102\", \"103\", \"35796314\", \"Ly5fEh287828\", \"qrxdXi306882\", \"8CoSqT297355\", \"VJbKqK316409\", \"ebhBuf325936\", \"vkoGt5335463\"], \"checkedPersonnels\": [{\"key\": \"100\", \"type\": \"personnel\", \"title\": \"黑蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"100\", \"type\": \"personnel\", \"title\": \"黑蛋\"}]}, {\"key\": \"101\", \"type\": \"personnel\", \"title\": \"狗蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"Ly5fEh287828\", \"type\": \"department\", \"title\": \"财务部\"}, {\"key\": \"101\", \"type\": \"personnel\", \"title\": \"狗蛋\"}]}, {\"key\": \"102\", \"type\": \"personnel\", \"title\": \"狗蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"Ly5fEh287828\", \"type\": \"department\", \"title\": \"财务部\"}, {\"key\": \"qrxdXi306882\", \"type\": \"department\", \"title\": \"小财务部\"}, {\"key\": \"102\", \"type\": \"personnel\", \"title\": \"狗蛋\"}]}, {\"key\": \"103\", \"type\": \"personnel\", \"title\": \"大锤\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"8CoSqT297355\", \"type\": \"department\", \"title\": \"人事部\"}, {\"key\": \"103\", \"type\": \"personnel\", \"title\": \"大锤\"}]}]}', '2019-01-24 14:00:03', '2019-01-24 14:00:03', NULL);
INSERT INTO `approval_template` VALUES (39, '百词斩', '[{\"type\": \"CHECKBOX\", \"field\": {\"label\": \"多选框\", \"required\": false, \"checkboxOptions\": [{\"key\": 1, \"value\": \"选项1\"}, {\"key\": 2, \"value\": \"选项2\"}, {\"key\": 3, \"value\": \"选项3\"}]}}, {\"type\": \"DATERANGE\", \"field\": {\"label\": \"日期区间\", \"required\": false}}, {\"type\": \"TEXTAREA\", \"field\": {\"label\": \"多行文本框\", \"required\": false, \"placeholder\": \"请输入\"}}]', '[{\"type\": \"countersign\", \"checkedInfo\": {\"checkedKeys\": [\"35796314\", \"Ly5fEh287828\", \"qrxdXi306882\", \"7BdmJp30599\", \"7BdmJp21072\", \"8CoSqT297355\", \"VJbKqK316409\", \"ebhBuf325936\", \"vkoGt5335463\", \"7BdmJp40126\", \"7BdmJp11545\"], \"checkedPersonnels\": [{\"key\": \"7BdmJp30599\", \"type\": \"personnel\", \"title\": \"狗蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"Ly5fEh287828\", \"type\": \"department\", \"title\": \"财务部\"}, {\"key\": \"qrxdXi306882\", \"type\": \"department\", \"title\": \"小财务部\"}, {\"key\": \"7BdmJp30599\", \"type\": \"personnel\", \"title\": \"狗蛋\"}]}, {\"key\": \"7BdmJp21072\", \"type\": \"personnel\", \"title\": \"狗蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"Ly5fEh287828\", \"type\": \"department\", \"title\": \"财务部\"}, {\"key\": \"7BdmJp21072\", \"type\": \"personnel\", \"title\": \"狗蛋\"}]}, {\"key\": \"7BdmJp40126\", \"type\": \"personnel\", \"title\": \"大锤\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"8CoSqT297355\", \"type\": \"department\", \"title\": \"人事部\"}, {\"key\": \"7BdmJp40126\", \"type\": \"personnel\", \"title\": \"大锤\"}]}, {\"key\": \"7BdmJp11545\", \"type\": \"personnel\", \"title\": \"黑蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"7BdmJp11545\", \"type\": \"personnel\", \"title\": \"黑蛋\"}]}]}}, {\"type\": \"normal\", \"checkedInfo\": {\"checkedKeys\": [\"8CoSqT297355\", \"VJbKqK316409\", \"ebhBuf325936\", \"vkoGt5335463\", \"7BdmJp40126\"], \"checkedPersonnels\": [{\"key\": \"7BdmJp40126\", \"type\": \"personnel\", \"title\": \"大锤\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"8CoSqT297355\", \"type\": \"department\", \"title\": \"人事部\"}, {\"key\": \"7BdmJp40126\", \"type\": \"personnel\", \"title\": \"大锤\"}]}]}}, {\"type\": \"normal\", \"checkedInfo\": {\"checkedKeys\": [\"7BdmJp11545\"], \"checkedPersonnels\": [{\"key\": \"7BdmJp11545\", \"type\": \"personnel\", \"title\": \"黑蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"7BdmJp11545\", \"type\": \"personnel\", \"title\": \"黑蛋\"}]}]}}]', 8, '固定流程', 'ccccc', 'bcz', 1, 0, '{\"staffId\": [\"7BdmJp30599\", \"7BdmJp21072\", \"7BdmJp40126\", \"7BdmJp11545\"], \"rangeInfo\": {\"checkedKeys\": [\"35796314\", \"Ly5fEh287828\", \"qrxdXi306882\", \"7BdmJp30599\", \"7BdmJp21072\", \"8CoSqT297355\", \"VJbKqK316409\", \"ebhBuf325936\", \"vkoGt5335463\", \"7BdmJp40126\", \"7BdmJp11545\"], \"checkedPersonnels\": [{\"key\": \"7BdmJp30599\", \"type\": \"personnel\", \"title\": \"狗蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"Ly5fEh287828\", \"type\": \"department\", \"title\": \"财务部\"}, {\"key\": \"qrxdXi306882\", \"type\": \"department\", \"title\": \"小财务部\"}, {\"key\": \"7BdmJp30599\", \"type\": \"personnel\", \"title\": \"狗蛋\"}]}, {\"key\": \"7BdmJp21072\", \"type\": \"personnel\", \"title\": \"狗蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"Ly5fEh287828\", \"type\": \"department\", \"title\": \"财务部\"}, {\"key\": \"7BdmJp21072\", \"type\": \"personnel\", \"title\": \"狗蛋\"}]}, {\"key\": \"7BdmJp40126\", \"type\": \"personnel\", \"title\": \"大锤\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"8CoSqT297355\", \"type\": \"department\", \"title\": \"人事部\"}, {\"key\": \"7BdmJp40126\", \"type\": \"personnel\", \"title\": \"大锤\"}]}, {\"key\": \"7BdmJp11545\", \"type\": \"personnel\", \"title\": \"黑蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"7BdmJp11545\", \"type\": \"personnel\", \"title\": \"黑蛋\"}]}]}, \"departmentId\": [\"35796314\", \"Ly5fEh287828\", \"qrxdXi306882\", \"8CoSqT297355\", \"VJbKqK316409\", \"ebhBuf325936\", \"vkoGt5335463\"]}', '{\"checkedKeys\": [\"35796314\", \"Ly5fEh287828\", \"qrxdXi306882\", \"7BdmJp30599\", \"7BdmJp21072\", \"8CoSqT297355\", \"VJbKqK316409\", \"ebhBuf325936\", \"vkoGt5335463\", \"7BdmJp40126\", \"7BdmJp11545\"], \"checkedPersonnels\": [{\"key\": \"7BdmJp30599\", \"type\": \"personnel\", \"title\": \"狗蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"Ly5fEh287828\", \"type\": \"department\", \"title\": \"财务部\"}, {\"key\": \"qrxdXi306882\", \"type\": \"department\", \"title\": \"小财务部\"}, {\"key\": \"7BdmJp30599\", \"type\": \"personnel\", \"title\": \"狗蛋\"}]}, {\"key\": \"7BdmJp21072\", \"type\": \"personnel\", \"title\": \"狗蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"Ly5fEh287828\", \"type\": \"department\", \"title\": \"财务部\"}, {\"key\": \"7BdmJp21072\", \"type\": \"personnel\", \"title\": \"狗蛋\"}]}, {\"key\": \"7BdmJp40126\", \"type\": \"personnel\", \"title\": \"大锤\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"8CoSqT297355\", \"type\": \"department\", \"title\": \"人事部\"}, {\"key\": \"7BdmJp40126\", \"type\": \"personnel\", \"title\": \"大锤\"}]}, {\"key\": \"7BdmJp11545\", \"type\": \"personnel\", \"title\": \"黑蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"7BdmJp11545\", \"type\": \"personnel\", \"title\": \"黑蛋\"}]}]}', '2019-01-25 16:36:21', '2019-03-08 17:11:18', NULL);
INSERT INTO `approval_template` VALUES (40, '提升素养', '[{\"type\": \"CHECKBOX\", \"field\": {\"label\": \"多选框\", \"required\": false, \"checkboxOptions\": [{\"key\": 1, \"value\": \"选项1\"}, {\"key\": 2, \"value\": \"选项2\"}, {\"key\": 3, \"value\": \"选项3\"}]}}, {\"type\": \"CHECKBOX\", \"field\": {\"label\": \"多选框\", \"required\": false, \"checkboxOptions\": [{\"key\": 1, \"value\": \"选项1\"}, {\"key\": 2, \"value\": \"选项2\"}, {\"key\": 3, \"value\": \"选项3\"}]}}, {\"type\": \"DATERANGE\", \"field\": {\"label\": \"日期区间\", \"required\": false}}]', '[{\"type\": \"countersign\", \"checkedInfo\": {\"checkedKeys\": [\"Ly5fEh287828\", \"qrxdXi306882\", \"7BdmJp30599\", \"7BdmJp21072\"], \"checkedPersonnels\": [{\"key\": \"7BdmJp30599\", \"type\": \"personnel\", \"title\": \"狗蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"Ly5fEh287828\", \"type\": \"department\", \"title\": \"财务部\"}, {\"key\": \"qrxdXi306882\", \"type\": \"department\", \"title\": \"小财务部\"}, {\"key\": \"7BdmJp30599\", \"type\": \"personnel\", \"title\": \"狗蛋\"}]}, {\"key\": \"7BdmJp21072\", \"type\": \"personnel\", \"title\": \"狗蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"Ly5fEh287828\", \"type\": \"department\", \"title\": \"财务部\"}, {\"key\": \"7BdmJp21072\", \"type\": \"personnel\", \"title\": \"狗蛋\"}]}]}}, {\"type\": \"normal\", \"checkedInfo\": {\"checkedKeys\": [\"7BdmJp11545\"], \"checkedPersonnels\": [{\"key\": \"7BdmJp11545\", \"type\": \"personnel\", \"title\": \"黑蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"7BdmJp11545\", \"type\": \"personnel\", \"title\": \"黑蛋\"}]}]}}, {\"type\": \"orSign\", \"checkedInfo\": {\"checkedKeys\": [\"Ly5fEh287828\", \"qrxdXi306882\", \"7BdmJp30599\", \"7BdmJp21072\"], \"checkedPersonnels\": [{\"key\": \"7BdmJp30599\", \"type\": \"personnel\", \"title\": \"狗蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"Ly5fEh287828\", \"type\": \"department\", \"title\": \"财务部\"}, {\"key\": \"qrxdXi306882\", \"type\": \"department\", \"title\": \"小财务部\"}, {\"key\": \"7BdmJp30599\", \"type\": \"personnel\", \"title\": \"狗蛋\"}]}, {\"key\": \"7BdmJp21072\", \"type\": \"personnel\", \"title\": \"狗蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"Ly5fEh287828\", \"type\": \"department\", \"title\": \"财务部\"}, {\"key\": \"7BdmJp21072\", \"type\": \"personnel\", \"title\": \"狗蛋\"}]}]}}]', 8, '固定流程', '55555', 'tssy', 1, 1, NULL, '{\"checkedKeys\": [\"35796314\", \"Ly5fEh287828\", \"qrxdXi306882\", \"7BdmJp30599\", \"7BdmJp21072\", \"8CoSqT297355\", \"VJbKqK316409\", \"ebhBuf325936\", \"vkoGt5335463\", \"7BdmJp40126\", \"7BdmJp11545\"], \"checkedPersonnels\": [{\"key\": \"7BdmJp30599\", \"type\": \"personnel\", \"title\": \"狗蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"Ly5fEh287828\", \"type\": \"department\", \"title\": \"财务部\"}, {\"key\": \"qrxdXi306882\", \"type\": \"department\", \"title\": \"小财务部\"}, {\"key\": \"7BdmJp30599\", \"type\": \"personnel\", \"title\": \"狗蛋\"}]}, {\"key\": \"7BdmJp21072\", \"type\": \"personnel\", \"title\": \"狗蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"Ly5fEh287828\", \"type\": \"department\", \"title\": \"财务部\"}, {\"key\": \"7BdmJp21072\", \"type\": \"personnel\", \"title\": \"狗蛋\"}]}, {\"key\": \"7BdmJp40126\", \"type\": \"personnel\", \"title\": \"大锤\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"8CoSqT297355\", \"type\": \"department\", \"title\": \"人事部\"}, {\"key\": \"7BdmJp40126\", \"type\": \"personnel\", \"title\": \"大锤\"}]}, {\"key\": \"7BdmJp11545\", \"type\": \"personnel\", \"title\": \"黑蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"7BdmJp11545\", \"type\": \"personnel\", \"title\": \"黑蛋\"}]}]}', '2019-01-25 22:02:24', '2019-03-08 17:11:27', NULL);
INSERT INTO `approval_template` VALUES (41, '出出出', '[{\"type\": \"CHECKBOX\", \"field\": {\"label\": \"多选框\", \"required\": false, \"checkboxOptions\": [{\"key\": 1, \"value\": \"选项1\"}, {\"key\": 2, \"value\": \"选项2\"}, {\"key\": 3, \"value\": \"选项3\"}]}}, {\"type\": \"DATERANGE\", \"field\": {\"label\": \"日期区间\", \"required\": false}}, {\"type\": \"ANNEX\", \"field\": {\"label\": \"附件\", \"required\": false}}]', '[{\"type\": \"countersign\", \"checkedInfo\": {\"checkedKeys\": [\"Ly5fEh287828\", \"qrxdXi306882\", \"7BdmJp30599\", \"7BdmJp21072\"], \"checkedPersonnels\": [{\"key\": \"7BdmJp30599\", \"type\": \"personnel\", \"title\": \"狗蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"Ly5fEh287828\", \"type\": \"department\", \"title\": \"财务部\"}, {\"key\": \"qrxdXi306882\", \"type\": \"department\", \"title\": \"小财务部\"}, {\"key\": \"7BdmJp30599\", \"type\": \"personnel\", \"title\": \"狗蛋\"}]}, {\"key\": \"7BdmJp21072\", \"type\": \"personnel\", \"title\": \"狗蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"Ly5fEh287828\", \"type\": \"department\", \"title\": \"财务部\"}, {\"key\": \"7BdmJp21072\", \"type\": \"personnel\", \"title\": \"狗蛋\"}]}]}}]', 8, '自由流程', '鹅鹅鹅鹅鹅鹅', 'GZT', 1, 1, '{\"staffId\": [\"HIybuR135396\", \"Yxlui040126\", \"IZSZ0l11545\"], \"rangeInfo\": {\"checkedKeys\": [\"PSuRSI21072\", \"2jYIeJ30599\", \"hUyipR87761\", \"HIybuR135396\", \"Yxlui040126\", \"IZSZ0l11545\"], \"checkedPersonnels\": [{\"key\": \"HIybuR135396\", \"type\": \"personnel\", \"title\": \"132\", \"linKey\": [{\"key\": \"nRlz5A11545\", \"title\": \"探知科技\"}, {\"key\": \"PSuRSI21072\", \"type\": \"department\", \"title\": \"人事部\"}, {\"key\": \"HIybuR135396\", \"type\": \"personnel\", \"title\": \"132\"}]}, {\"key\": \"Yxlui040126\", \"type\": \"personnel\", \"title\": \"侃大山\", \"linKey\": [{\"key\": \"nRlz5A11545\", \"title\": \"探知科技\"}, {\"key\": \"PSuRSI21072\", \"type\": \"department\", \"title\": \"人事部\"}, {\"key\": \"Yxlui040126\", \"type\": \"personnel\", \"title\": \"侃大山\"}]}, {\"key\": \"IZSZ0l11545\", \"type\": \"personnel\", \"title\": \"Pirvate\", \"linKey\": [{\"key\": \"nRlz5A11545\", \"title\": \"探知科技\"}, {\"key\": \"PSuRSI21072\", \"type\": \"department\", \"title\": \"人事部\"}, {\"key\": \"IZSZ0l11545\", \"type\": \"personnel\", \"title\": \"Pirvate\"}]}]}, \"departmentId\": [\"PSuRSI21072\", \"2jYIeJ30599\", \"hUyipR87761\"]}', 'null', '2019-01-29 14:14:32', '2019-03-22 17:31:36', NULL);
INSERT INTO `approval_template` VALUES (43, '加班', '[{\"type\": \"TEXTAREA\", \"field\": {\"label\": \"请假缘由\", \"required\": false, \"placeholder\": \"请输入\"}}, {\"type\": \"DATERANGE\", \"field\": {\"label\": \"请假时间\", \"required\": true}}]', NULL, 8, '自由流程', '适用于加班请假模板', 'QJ', 1, 1, '{\"staffId\": [\"7BdmJp30599\", \"7BdmJp21072\", \"7BdmJp40126\", \"7BdmJp11545\", \"7BdmJp106815\"], \"rangeInfo\": {\"checkedKeys\": [\"35796314\", \"Ly5fEh287828\", \"qrxdXi306882\", \"7BdmJp30599\", \"7BdmJp21072\", \"8CoSqT297355\", \"VJbKqK316409\", \"ebhBuf325936\", \"vkoGt5335463\", \"7BdmJp40126\", \"7BdmJp11545\", \"7BdmJp106815\"], \"checkedPersonnels\": [{\"key\": \"7BdmJp30599\", \"type\": \"personnel\", \"title\": \"狗蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"Ly5fEh287828\", \"type\": \"department\", \"title\": \"财务部\"}, {\"key\": \"qrxdXi306882\", \"type\": \"department\", \"title\": \"小财务部\"}, {\"key\": \"7BdmJp30599\", \"type\": \"personnel\", \"title\": \"狗蛋\"}]}, {\"key\": \"7BdmJp21072\", \"type\": \"personnel\", \"title\": \"狗蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"Ly5fEh287828\", \"type\": \"department\", \"title\": \"财务部\"}, {\"key\": \"7BdmJp21072\", \"type\": \"personnel\", \"title\": \"狗蛋\"}]}, {\"key\": \"7BdmJp40126\", \"type\": \"personnel\", \"title\": \"大锤\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"8CoSqT297355\", \"type\": \"department\", \"title\": \"人事部\"}, {\"key\": \"7BdmJp40126\", \"type\": \"personnel\", \"title\": \"大锤\"}]}, {\"key\": \"7BdmJp11545\", \"type\": \"personnel\", \"title\": \"黑蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"7BdmJp11545\", \"type\": \"personnel\", \"title\": \"黑蛋\"}]}, {\"key\": \"7BdmJp106815\", \"type\": \"personnel\", \"title\": \"xxx\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"7BdmJp106815\", \"type\": \"personnel\", \"title\": \"xxx\"}]}]}, \"departmentId\": [\"35796314\", \"Ly5fEh287828\", \"qrxdXi306882\", \"8CoSqT297355\", \"VJbKqK316409\", \"ebhBuf325936\", \"vkoGt5335463\"]}', NULL, '2019-01-29 21:25:55', '2019-03-03 21:20:16', NULL);
INSERT INTO `approval_template` VALUES (51, '222222222222', '[{\"type\": \"INPUT\", \"field\": {\"label\": \"单行文本框\", \"required\": false, \"placeholder\": \"请输入\"}}, {\"type\": \"TEXTAREA\", \"field\": {\"label\": \"多行文本框\", \"required\": false, \"placeholder\": \"请输入\"}}]', '[{\"type\": \"countersign\", \"checkedInfo\": {\"checkedKeys\": [\"Ly5fEh287828\", \"qrxdXi306882\", \"7BdmJp30599\", \"7BdmJp21072\"], \"checkedPersonnels\": [{\"key\": \"7BdmJp30599\", \"type\": \"personnel\", \"title\": \"狗蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"Ly5fEh287828\", \"type\": \"department\", \"title\": \"财务部\"}, {\"key\": \"qrxdXi306882\", \"type\": \"department\", \"title\": \"小财务部\"}, {\"key\": \"7BdmJp30599\", \"type\": \"personnel\", \"title\": \"狗蛋\"}]}, {\"key\": \"7BdmJp21072\", \"type\": \"personnel\", \"title\": \"狗蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"Ly5fEh287828\", \"type\": \"department\", \"title\": \"财务部\"}, {\"key\": \"7BdmJp21072\", \"type\": \"personnel\", \"title\": \"狗蛋\"}]}]}}]', 8, '自由流程', '56525', 'GZT', 1, 1, NULL, NULL, '2019-01-31 18:08:56', '2019-01-31 22:52:06', NULL);
INSERT INTO `approval_template` VALUES (52, '666666666流程6666', '[{\"type\": \"INPUT\", \"field\": {\"label\": \"单行文本框\", \"required\": false, \"placeholder\": \"请输入\"}}]', 'null', 8, '自由流程', '121213', 'GZT', 1, 1, '{\"staffId\": [\"7BdmJp30599\", \"7BdmJp21072\", \"7BdmJp40126\"], \"rangeInfo\": {\"checkedKeys\": [\"Ly5fEh287828\", \"qrxdXi306882\", \"7BdmJp30599\", \"7BdmJp21072\", \"8CoSqT297355\", \"VJbKqK316409\", \"ebhBuf325936\", \"vkoGt5335463\", \"7BdmJp40126\"], \"checkedPersonnels\": [{\"key\": \"7BdmJp30599\", \"type\": \"personnel\", \"title\": \"狗蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"Ly5fEh287828\", \"type\": \"department\", \"title\": \"财务部\"}, {\"key\": \"qrxdXi306882\", \"type\": \"department\", \"title\": \"小财务部\"}, {\"key\": \"7BdmJp30599\", \"type\": \"personnel\", \"title\": \"狗蛋\"}]}, {\"key\": \"7BdmJp21072\", \"type\": \"personnel\", \"title\": \"狗蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"Ly5fEh287828\", \"type\": \"department\", \"title\": \"财务部\"}, {\"key\": \"7BdmJp21072\", \"type\": \"personnel\", \"title\": \"狗蛋\"}]}, {\"key\": \"7BdmJp40126\", \"type\": \"personnel\", \"title\": \"大锤\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"8CoSqT297355\", \"type\": \"department\", \"title\": \"人事部\"}, {\"key\": \"7BdmJp40126\", \"type\": \"personnel\", \"title\": \"大锤\"}]}]}, \"departmentId\": [\"Ly5fEh287828\", \"qrxdXi306882\", \"8CoSqT297355\", \"VJbKqK316409\", \"ebhBuf325936\", \"vkoGt5335463\"]}', NULL, '2019-01-31 18:15:48', '2019-01-31 22:53:13', NULL);
INSERT INTO `approval_template` VALUES (54, '11111212流', 'null', 'null', 4, '自由流程', NULL, 'GZT', 1, 1, NULL, NULL, '2019-01-31 22:55:02', '2019-01-31 22:55:59', NULL);
INSERT INTO `approval_template` VALUES (57, '固定流程测试', '[{\"type\": \"TEXTAREA\", \"field\": {\"label\": \"多行文本框\", \"required\": false, \"placeholder\": \"请输入\"}}, {\"type\": \"CHECKBOX\", \"field\": {\"label\": \"多选框\", \"required\": false, \"checkboxOptions\": [{\"key\": 1, \"value\": \"选项1\"}, {\"key\": 2, \"value\": \"选项2\"}, {\"key\": 3, \"value\": \"选项3\"}]}}, {\"type\": \"DATERANGE\", \"field\": {\"label\": \"日期区间\", \"required\": false}}]', '[{\"type\": \"normal\", \"checkedInfo\": {\"checkedKeys\": [\"8CoSqT297355\", \"VJbKqK316409\", \"ebhBuf325936\", \"vkoGt5335463\", \"7BdmJp40126\"], \"checkedPersonnels\": [{\"key\": \"7BdmJp40126\", \"type\": \"personnel\", \"title\": \"大锤\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"8CoSqT297355\", \"type\": \"department\", \"title\": \"人事部\"}, {\"key\": \"7BdmJp40126\", \"type\": \"personnel\", \"title\": \"大锤\"}]}]}}]', 8, '固定流程', '85555555', 'gdlc', 1, 1, '{\"staffId\": [\"7BdmJp30599\", \"7BdmJp21072\"], \"rangeInfo\": {\"checkedKeys\": [\"Ly5fEh287828\", \"qrxdXi306882\", \"7BdmJp30599\", \"7BdmJp21072\"], \"checkedPersonnels\": [{\"key\": \"7BdmJp30599\", \"type\": \"personnel\", \"title\": \"狗蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"Ly5fEh287828\", \"type\": \"department\", \"title\": \"财务部\"}, {\"key\": \"qrxdXi306882\", \"type\": \"department\", \"title\": \"小财务部\"}, {\"key\": \"7BdmJp30599\", \"type\": \"personnel\", \"title\": \"狗蛋\"}]}, {\"key\": \"7BdmJp21072\", \"type\": \"personnel\", \"title\": \"狗蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"Ly5fEh287828\", \"type\": \"department\", \"title\": \"财务部\"}, {\"key\": \"7BdmJp21072\", \"type\": \"personnel\", \"title\": \"狗蛋\"}]}]}, \"departmentId\": [\"Ly5fEh287828\", \"qrxdXi306882\"]}', '{\"checkedKeys\": [\"Ly5fEh287828\", \"qrxdXi306882\", \"7BdmJp30599\", \"7BdmJp21072\"], \"checkedPersonnels\": [{\"key\": \"7BdmJp30599\", \"type\": \"personnel\", \"title\": \"狗蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"Ly5fEh287828\", \"type\": \"department\", \"title\": \"财务部\"}, {\"key\": \"qrxdXi306882\", \"type\": \"department\", \"title\": \"小财务部\"}, {\"key\": \"7BdmJp30599\", \"type\": \"personnel\", \"title\": \"狗蛋\"}]}, {\"key\": \"7BdmJp21072\", \"type\": \"personnel\", \"title\": \"狗蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"Ly5fEh287828\", \"type\": \"department\", \"title\": \"财务部\"}, {\"key\": \"7BdmJp21072\", \"type\": \"personnel\", \"title\": \"狗蛋\"}]}]}', '2019-02-01 16:27:38', '2019-02-20 13:15:06', NULL);
INSERT INTO `approval_template` VALUES (60, '测试固定流程', '[{\"type\": \"INPUT\", \"field\": {\"label\": \"单行文本框\", \"required\": true, \"placeholder\": \"请输入\"}}]', '[{\"type\": \"normal\", \"checkedInfo\": {\"checkedKeys\": [\"7BdmJp11545\"], \"checkedPersonnels\": [{\"key\": \"7BdmJp11545\", \"type\": \"personnel\", \"title\": \"黑蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"7BdmJp11545\", \"type\": \"personnel\", \"title\": \"黑蛋\"}]}]}}, {\"type\": \"normal\", \"checkedInfo\": {\"checkedKeys\": [\"7BdmJp11545\"], \"checkedPersonnels\": [{\"key\": \"7BdmJp11545\", \"type\": \"personnel\", \"title\": \"黑蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"7BdmJp11545\", \"type\": \"personnel\", \"title\": \"黑蛋\"}]}]}}]', 4, '固定流程', '测试固定流程', 'GZT', 1, 1, NULL, '{\"checkedKeys\": [\"7BdmJp106815\"], \"checkedPersonnels\": [{\"key\": \"7BdmJp106815\", \"type\": \"personnel\", \"title\": \"xxx\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"7BdmJp106815\", \"type\": \"personnel\", \"title\": \"xxx\"}]}]}', '2019-02-03 08:58:48', '2019-03-08 14:38:36', NULL);
INSERT INTO `approval_template` VALUES (61, '自由流程测试', '[{\"type\": \"CHECKBOX\", \"field\": {\"label\": \"多选框\", \"required\": true, \"checkboxOptions\": [{\"key\": 1, \"value\": \"选项1\"}, {\"key\": 2, \"value\": \"选项2\"}, {\"key\": 3, \"value\": \"选项3\"}]}}]', NULL, 8, '自由流程', '自由流程测试', 'GZT', 1, 1, NULL, NULL, '2019-02-03 09:02:50', '2019-03-03 21:15:10', NULL);
INSERT INTO `approval_template` VALUES (63, 'iopi', '[{\"type\": \"MONEY\", \"field\": {\"label\": \"金额\", \"required\": false, \"placeholder\": \"请输入金额\"}}, {\"type\": \"SELECT\", \"field\": {\"label\": \"下拉选择框\", \"required\": true, \"selectOptions\": [{\"key\": 1, \"value\": \"选项1\"}, {\"key\": 2, \"value\": \"选项2\"}, {\"key\": 3, \"value\": \"选项3\"}]}}, {\"type\": \"INPUT\", \"field\": {\"label\": \"单行文本框\", \"required\": false, \"placeholder\": \"请输入\"}}]', NULL, 8, '自由流程', '初步设计评审收费计算器权限', 'kjhl', 1, 1, '{\"staffId\": [], \"rangeInfo\": {\"checkedKeys\": [], \"checkedPersonnels\": []}, \"departmentId\": []}', NULL, '2019-02-19 10:03:01', '2019-03-08 17:11:18', NULL);
INSERT INTO `approval_template` VALUES (65, 'hh', NULL, NULL, 8, '自由流程', '劳动安全卫生评审费计算器', 'GZT', 1, 1, NULL, NULL, '2019-03-05 15:47:22', '2019-03-05 15:47:22', NULL);
INSERT INTO `approval_template` VALUES (66, '自由流程测试', '[{\"type\": \"MONEY\", \"field\": {\"label\": \"金额\", \"required\": false, \"placeholder\": \"请输入金额\"}}]', NULL, 14, '自由流程', '12345', '自由', 1, 1, NULL, NULL, '2019-03-06 16:52:39', '2019-03-08 17:11:12', NULL);
INSERT INTO `approval_template` VALUES (67, '默认模板', '[{\"type\": \"ANNEX\", \"field\": {\"label\": \"附件\", \"required\": false}}, {\"type\": \"TEXTAREA\", \"field\": {\"label\": \"多行文本框\", \"required\": false, \"placeholder\": \"请输入\"}}, {\"type\": \"DATERANGE\", \"field\": {\"label\": \"日期区间\", \"required\": false}}, {\"type\": \"RADIO\", \"field\": {\"label\": \"单选框\", \"required\": false, \"radioOptions\": [{\"key\": 1, \"value\": \"选项1\"}, {\"key\": 2, \"value\": \"选项2\"}, {\"key\": 3, \"value\": \"选项3\"}]}}, {\"type\": \"INPUT\", \"field\": {\"label\": \"单行文本框\", \"required\": false, \"placeholder\": \"请输入\"}}, {\"type\": \"CHECKBOX\", \"field\": {\"label\": \"多选框\", \"required\": false, \"checkboxOptions\": [{\"key\": 1, \"value\": \"选项1\"}, {\"key\": 2, \"value\": \"选项2\"}, {\"key\": 3, \"value\": \"选项3\"}]}}, {\"type\": \"NUMBER\", \"field\": {\"unit\": null, \"label\": \"数字\", \"required\": false, \"placeholder\": \"请输入数字\"}}, {\"type\": \"DATEPICKER\", \"field\": {\"label\": \"日期\", \"required\": false}}, {\"type\": \"NUMBER\", \"field\": {\"unit\": null, \"label\": \"数字\", \"required\": false, \"placeholder\": \"请输入数字\"}}, {\"type\": \"MONEY\", \"field\": {\"label\": \"金额\", \"required\": false, \"placeholder\": \"请输入金额\"}}, {\"type\": \"DATERANGE\", \"field\": {\"label\": \"日期区间\", \"required\": false}}, {\"type\": \"MONEY\", \"field\": {\"label\": \"金额\", \"required\": false, \"placeholder\": \"请输入金额\"}}, {\"type\": \"ANNEX\", \"field\": {\"label\": \"附件\", \"required\": false}}]', 'null', 26, '自由流程', '劳动安全卫生评审费计算器', 'GZT', 1, 1, NULL, NULL, '2019-04-03 17:35:19', '2019-04-15 18:30:46', NULL);

-- ----------------------------
-- Table structure for approval_type
-- ----------------------------
DROP TABLE IF EXISTS `approval_type`;
CREATE TABLE `approval_type`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '类型名称',
  `company_id` int(11) NOT NULL COMMENT '公司id',
  `sequence` int(11) NOT NULL DEFAULT 0 COMMENT '排序,根据此字段查找排序,针对于用户拖拽后的排序更新',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 28 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of approval_type
-- ----------------------------
INSERT INTO `approval_type` VALUES (1, '评审通', 0, 0, NULL, NULL);
INSERT INTO `approval_type` VALUES (2, '111', 0, 0, NULL, NULL);
INSERT INTO `approval_type` VALUES (3, '132', 0, 0, NULL, NULL);
INSERT INTO `approval_type` VALUES (4, '经典模板类型one', 0, 2, '2019-03-05 16:36:54', '2019-03-05 16:36:54');
INSERT INTO `approval_type` VALUES (5, '经典模板类型two', 0, 1, '2019-03-05 16:37:01', '2019-03-05 16:37:01');
INSERT INTO `approval_type` VALUES (6, '财务', 1, 9, '2019-01-14 13:47:34', '2019-03-06 10:22:07');
INSERT INTO `approval_type` VALUES (7, '出纳', 1, 7, '2019-01-14 20:43:24', '2019-03-06 10:22:07');
INSERT INTO `approval_type` VALUES (8, '人事', 1, 8, '2019-01-15 16:56:33', '2019-03-06 10:22:07');
INSERT INTO `approval_type` VALUES (9, '公司年会', 1, 5, '2019-01-15 16:57:24', '2019-03-06 10:22:07');
INSERT INTO `approval_type` VALUES (10, '福利待遇fc', 1, 2, '2019-01-14 13:30:08', '2019-03-06 10:22:07');
INSERT INTO `approval_type` VALUES (11, '出勤', 1, 1, '2019-01-14 13:37:41', '2019-03-06 10:22:07');
INSERT INTO `approval_type` VALUES (12, '吾问无为谓', 1, 3, '2019-01-24 13:05:15', '2019-03-06 10:22:07');
INSERT INTO `approval_type` VALUES (13, '测试', 1, 4, '2019-02-14 17:45:52', '2019-03-06 10:22:07');
INSERT INTO `approval_type` VALUES (14, '招标', 1, 0, '2019-02-16 14:40:37', '2019-03-06 10:22:07');
INSERT INTO `approval_type` VALUES (17, 'pppp', 1, 11, '2019-03-01 14:32:43', '2019-03-06 10:22:08');
INSERT INTO `approval_type` VALUES (25, '123', 1, 0, '2019-03-16 11:56:18', '2019-03-16 11:56:18');
INSERT INTO `approval_type` VALUES (26, '123', 1, 0, '2019-03-16 11:56:25', '2019-03-16 11:56:25');
INSERT INTO `approval_type` VALUES (27, '4564', 1, 0, '2019-03-18 14:44:44', '2019-03-18 14:44:44');

-- ----------------------------
-- Table structure for approval_user
-- ----------------------------
DROP TABLE IF EXISTS `approval_user`;
CREATE TABLE `approval_user`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `approval_id` int(11) NOT NULL COMMENT '审批id',
  `approver_id` int(11) NOT NULL COMMENT '审批人id',
  `approval_method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '审批方式',
  `approval_level` int(11) NOT NULL COMMENT '审批级数,用于确定该审批人是处于第几级,(查找数据是用于排序)',
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '会签???',
  `status` int(11) NOT NULL DEFAULT 0 COMMENT '我的这一级审批状态(通过or不通过)0是未收到,1是审核中,2通过,3不通过,4是已转交',
  `level_status` int(11) NULL DEFAULT 0 COMMENT '这是等级的状态0审批中,1审批结束',
  `level_end_time` date NULL DEFAULT NULL COMMENT '该等级的结束时间',
  `opinion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '审批意见',
  `transferee_id` int(11) NULL DEFAULT 0 COMMENT '被转交者的id',
  `complete_time` datetime(0) NULL DEFAULT NULL COMMENT '完成时间',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of approval_user
-- ----------------------------
INSERT INTO `approval_user` VALUES (1, 1, 1, '固定流程', 1, 'normal', 1, 0, NULL, NULL, 0, NULL, '2019-04-15 18:05:03', '2019-04-15 18:05:03');

-- ----------------------------
-- Table structure for basic
-- ----------------------------
DROP TABLE IF EXISTS `basic`;
CREATE TABLE `basic`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of basic
-- ----------------------------
INSERT INTO `basic` VALUES (1, '敏感词', 'dulifei|fuck|习近平|你妈B|你妈的|你妈逼|分裂台湾|分裂西藏|卖假药|卖假货|台独|尼玛的|强奸|恐怖袭击|抢劫|杀人|毛泽东|江泽民|独立费|胡锦涛|藏独|邓小平|第一代领导|第二代领导|第三代领导|第四代领导|第五代领导|第六代领导|第七代领导|第1代领导|第2代领导|第3代领导|第4代领导|第5代领导|第6代领导|第7代领导|一位老同志的谈话|国办发|中办发|腐败中国|三个呆婊|你办事我放心|社会主义灭亡|打倒中国|灭亡中国|亡党亡国|粉碎四人帮|激流中国|特供|特贡|特共|zf大楼|殃视|贪污腐败|强制拆除|形式主义|政治风波|太子党|上海帮|北京帮|清华帮|红色贵族|权贵集团|河蟹社会|喝血社会|九风|9风|十七大|十7大|17da|九学|9学|四风|4风|双规|南街村|最淫官员|警匪|官匪|独夫民贼|官商勾结|城管暴力执法|强制捐款|毒豺|一党执政|一党专制|一党专政|专制政权|宪法法院|胡平|苏晓康|贺卫方|谭作人|焦国标|万润南|张志新|辛灝年|高勤荣|王炳章|高智晟|司马璐|刘晓竹|刘宾雁|魏京生|寻找林昭的灵魂|别梦成灰|谁是新中国|讨伐中宣部|异议人士|民运人士|启蒙派|选国家主席|民一主|min主|民竹|民珠|民猪|chinesedemocracy|大赦国际|国际特赦|da选|投公|公头|宪政|平反|党章|维权|昝爱宗|宪章|08宪|08xz|觉醒的中国公民日渐清楚地认识到|抿主|民主|敏主|人拳|人木又|人quan|renquan|中国人权|中国的人权|中国新民党|群体事件|群体性事件|上中央|去中央|讨说法|请愿|请命|公开信|联名上书|万人大签名|万人骚动|截访|上访|shangfang|信访|访民|集合|集会|组织集体|静坐|静zuo|jing坐|示威|示wei|游行|you行|油行|游xing|youxing|官逼民反|反party|反共|抗议|亢议|抵制|低制|底制|di制|抵zhi|dizhi|boycott|血书|焚烧中国国旗|baoluan|流血冲突|出现暴动|发生暴动|引起暴动|baodong|灭共|杀毙|罢工|霸工|罢考|罢餐|霸餐|罢参|罢饭|罢吃|罢食|罢课|罢ke|霸课|ba课|罢教|罢学|罢运|网特|网评员|网络评论员|五毛党|五毛们|5毛党|戒严|jieyan|jie严|戒yan|8的平方事件|知道64|八九年|贰拾年|2o年|20和谐年|贰拾周年|六四|六河蟹四|六百度四|六和谐四|陆四|陆肆|198964|5月35|89年春夏之交|64惨案|64时期|64运动|4事件|四事件|北京风波|学潮|学chao|xuechao|学百度潮|门安天|天按门|坦克压大学生|民主女神|历史的伤口|高自联|北高联|维多利亚公园|thegateofheavenlypeace|bloodisonthesquare|为了忘却的纪念|血洗京城|四二六社论|王丹|柴玲|沈彤|封从德|王超华|王维林|吾尔开希|吾尔开西|侯德健|阎明复|方励之|蒋捷连|丁子霖|辛灏年|蒋彦永|严家其|陈一咨|中华局域网|党的喉舌|互联网审查|当局严密封锁|新闻封锁|封锁消息|爱国者同盟|关闭所有论坛|网络封锁|金盾工程|gfw|无界浏览|无界网络|自由门|何清涟|中国的陷阱|汪兆钧|记者无疆界|境外媒体|维基百科|纽约时报|bbc中文网|华盛顿邮报|世界日报|东森新闻网|东森电视|基督教科学箴言报|星岛日报|亚洲周刊|泰晤士报|美联社|中央社|雅虎香港|wikipedia|youtube|googleblogger|美国之音|美国广播公司|英国金融时报|自由亚洲|中央日报|自由时报|中国时报|反分裂|威胁论|左翼联盟|钓鱼岛|保钓组织|主权|弓单|火乍|木仓|石肖|核蛋|步qiang|bao炸|爆zha|baozha|zha药|zha弹|炸dan|炸yao|zhadan|zhayao|hmtd|三硝基甲苯|六氟化铀|炸药配方|弹药配方|炸弹配方|皮箱炸弹|火药配方|人体炸弹|人肉炸弹|解放军|兵力部署|军转|军事社|8341部队|第21集团军|七大军区|7大军区|北京军区|沈阳军区|济南军区|成都军区|广州军区|南京军区|兰州军区|颜色革命|规模冲突|塔利班|基地组织|恐怖分子|恐怖份子|三股势力|印尼屠华|印尼事件|蒋公纪念歌|马英九|mayingjiu|李天羽|苏贞昌|林文漪|陈水扁|陈s扁|陈随便|阿扁|a扁|告全国同胞书|台百度湾|台完|台wan|taiwan|台弯|湾台|台湾国|台湾共和国|台军|台独|台毒|台du|taidu|twdl|一中一台|打台湾|两岸关系|两岸战争|攻占台湾|支持台湾|进攻台湾|占领台湾|统一台湾|收复台湾|登陆台湾|解放台湾|解放tw|解决台湾|光复民国|台湾独立|台湾问题|台海问题|台海危机|台海统一|台海大战|台海战争|台海局势|入联|入耳关|中华联邦|国民党|x民党|民进党|青天白日|闹独立|duli|fenlie|日本万岁|小泽一郎|劣等民族|汉人|汉维|维汉|维吾|吾尔|热比娅|伊力哈木|疆独|东突厥斯坦解放组织|东突解放组织|蒙古分裂分子|列确|阿旺晋美|藏人|臧人|zang人|藏民|藏m|达赖|赖达|dalai|哒赖|dl喇嘛|丹增嘉措|打砸抢|西独|藏独|葬独|臧独|藏毒|藏du|zangdu|支持zd|藏暴乱|藏青会|雪山狮子旗|拉萨|啦萨|啦沙|啦撒|拉sa|lasa|la萨|西藏|藏西|xizang|xi藏|x藏|西z|tibet|希葬|希藏|硒藏|稀藏|西脏|西奘|西葬|西臧|援藏|bjork|王千源|安拉|回教|回族|回回|回民|穆斯林|穆罕穆德|穆罕默德|默罕默德|伊斯兰|圣战组织|清真|清zhen|qingzhen|真主|阿拉伯|高丽棒子|韩国狗|满洲第三帝国|满狗|鞑子|胡的接班人|钦定接班人|习近平|平近习|xjp|习太子|习明泽|老习|温家宝|温加宝|温x|温jia宝|温宝宝|温加饱|温加保|张培莉|温云松|温如春|温jb|胡温|胡x|胡jt|胡boss|胡总|胡王八|hujintao|胡jintao|胡j涛|胡惊涛|胡景涛|胡紧掏|湖紧掏|胡紧套|锦涛|hjt|胡派|胡主席|刘永清|胡海峰|胡海清|江泽民|民泽江|江胡|江哥|江主席|江书记|江浙闽|江沢民|江浙民|择民|则民|茳泽民|zemin|ze民|老江|老j|江core|江x|江派|江zm|jzm|江戏子|江蛤蟆|江某某|江贼|江猪|江氏集团|江绵恒|江绵康|王冶坪|江泽慧|邓小平|平小邓|xiao平|邓xp|邓晓平|邓朴方|邓榕|邓质方|毛泽东|猫泽东|猫则东|chairmanmao|猫贼洞|毛zd|毛zx|z东|ze东|泽d|zedong|毛太祖|毛相|主席画像|改革历程|朱镕基|朱容基|朱镕鸡|朱容鸡|朱云来|李鹏|李peng|里鹏|李月月鸟|李小鹏|李小琳|华主席|华国|国锋|国峰|锋同志|白春礼|薄熙来|薄一波|蔡赴朝|蔡武|曹刚川|常万全|陈炳德|陈德铭|陈建国|陈良宇|陈绍基|陈同海|陈至立|戴秉国|丁一平|董建华|杜德印|杜世成|傅锐|郭伯雄|郭金龙|贺国强|胡春华|耀邦|华建敏|黄华华|黄丽满|黄兴国|回良玉|贾庆林|贾廷安|靖志远|李长春|李春城|李建国|李克强|李岚清|李沛瑶|李荣融|李瑞环|李铁映|李先念|李学举|李源潮|栗智|梁光烈|廖锡龙|林树森|林炎志|林左鸣|令计划|柳斌杰|刘奇葆|刘少奇|刘延东|刘云山|刘志军|龙新民|路甬祥|罗箭|吕祖善|马飚|马恺|孟建柱|欧广源|强卫|沈跃跃|宋平顺|粟戎生|苏树林|孙家正|铁凝|屠光绍|王东明|汪东兴|王鸿举|王沪宁|王乐泉|王洛林|王岐山|王胜俊|王太华|王学军|王兆国|王振华|吴邦国|吴定富|吴官正|无官正|吴胜利|吴仪|奚国华|习仲勋|徐才厚|许其亮|徐绍史|杨洁篪|叶剑英|由喜贵|于幼军|俞正声|袁纯清|曾培炎|曾庆红|曾宪梓|曾荫权|张德江|张定发|张高丽|张立昌|张荣坤|张志国|赵洪祝|紫阳|周生贤|周永康|朱海仑|政治局常委|中纪委|主席像|总书记|中南海|大陆当局|中国当局|北京当局|共产党|党产共|gcd|共贪党|gongchandang|阿共|共一产一党|产党共|公产党|工产党|共c党|共x党|共铲|供产|共惨|供铲党|供铲谠|供铲裆|共残党|共残主义|共产主义的幽灵|拱铲|老共|中共|中珙|中gong|gc党|贡挡|gong党|g产|狗产蛋|共残裆|恶党|邪党|共产专制|共产王朝|裆中央|土共|土g|共狗|g匪|共匪|仇共|communistparty|政府|症腐|政腐|政付|正府|政俯|政一府|政百度府|政f|zhengfu|政zhi|挡中央|档中央|中央领导|中国zf|中央zf|国wu院|中华帝国|gong和|大陆官方|北京政权|福音会|中国教徒|统一教|观音法门|清海无上师|盘古|李洪志|志洪李|李宏志|轮功|法轮|轮法功|三去车仑|氵去车仑|发论工|法x功|法o功|法0功|法一轮一功|轮子功|车仑工力|法lun|fa轮|法lg|flg|fl功|falungong|大法弟子|大纪元|dajiyuan|明慧网|明慧周报|正见网|新唐人|伪火|退党|tuidang|退dang|超越红墙|自fen|真善忍|九评|9评|9ping|九ping|jiuping|藏字石|集体自杀|自sha|zi杀|suicide|titor|逢8必灾|逢八必灾|逢9必乱|逢九必乱|朱瑟里诺|根达亚文明|诺查丹玛斯|人类灭亡进程表|按照马雅历法|推背图|推bei图|济世灵文|诸世纪|爱女人|爱液|按摩棒|拔出来|爆草|包二奶|暴干|暴奸|暴乳|爆乳|暴淫|屄|被操|被插|被干|逼奸|仓井空|插暴|操逼|操黑|操烂|肏你|肏死|操死|操我|厕奴|插比|插b|插逼|插进|插你|插我|插阴|潮吹|潮喷|成人电影|成人论坛|成人色情|成人网站|成人文学|成人小说|艳情小说|成人游戏|吃精|赤裸|抽插|扌由插|抽一插|春药|大波|大力抽送|大乳|荡妇|荡女|盗撮|多人轮|发浪|放尿|肥逼|粉穴|封面女郎|风月大陆|干死你|干穴|肛交|肛门|龟头|裹本|国产av|好嫩|豪乳|黑逼|后庭|后穴|虎骑|花花公子|换妻俱乐部|黄片|几吧|鸡吧|鸡巴|鸡奸|寂寞男|寂寞女|妓女|激情|集体淫|奸情|叫床|脚交|金鳞岂是池中物|金麟岂是池中物|精液|就去日|巨屌|菊花洞|菊门|巨奶|巨乳|菊穴|开苞|口爆|口活|口交|口射|口淫|裤袜|狂操|狂插|浪逼|浪妇|浪叫|浪女|狼友|聊性|流淫|铃木麻|凌辱|漏乳|露b|乱交|乱伦|轮暴|轮操|轮奸|裸陪|买春|美逼|美少妇|美乳|美腿|美穴|美幼|秘唇|迷奸|密穴|蜜穴|蜜液|摸奶|摸胸|母奸|奈美|奶子|男奴|内射|嫩逼|嫩女|嫩穴|捏弄|女优|炮友|砲友|喷精|屁眼|品香堂|前凸后翘|强jian|强暴|强奸处女|情趣用品|情色|拳交|全裸|群交|惹火身材|人妻|人兽|日逼|日烂|肉棒|肉逼|肉唇|肉洞|肉缝|肉棍|肉茎|肉具|揉乳|肉穴|肉欲|乳爆|乳房|乳沟|乳交|乳头|三级片|骚逼|骚比|骚女|骚水|骚穴|色逼|色界|色猫|色盟|色情网站|色区|色色|色诱|色欲|色b|少年阿宾|少修正|射爽|射颜|食精|释欲|兽奸|兽交|手淫|兽欲|熟妇|熟母|熟女|爽片|爽死我了|双臀|死逼|丝袜|丝诱|松岛枫|酥痒|汤加丽|套弄|体奸|体位|舔脚|舔阴|调教|偷欢|偷拍|推油|脱内裤|文做|我就色|无码|舞女|无修正|吸精|夏川纯|相奸|小逼|校鸡|小穴|小xue|写真|性感妖娆|性感诱惑|性虎|性饥渴|性技巧|性交|性奴|性虐|性息|性欲|胸推|穴口|学生妹|穴图|亚情|颜射|阳具|杨思敏|要射了|夜勤病栋|一本道|一夜欢|一夜情|一ye情|阴部|淫虫|阴唇|淫荡|阴道|淫电影|阴阜|淫妇|淫河|阴核|阴户|淫贱|淫叫|淫教师|阴茎|阴精|淫浪|淫媚|淫糜|淫魔|淫母|淫女|淫虐|淫妻|淫情|淫色|淫声浪语|淫兽学园|淫书|淫术炼金士|淫水|淫娃|淫威|淫亵|淫样|淫液|淫照|阴b|应召|幼交|幼男|幼女|欲火|欲女|玉女心经|玉蒲团|玉乳|欲仙欲死|玉穴|援交|原味内衣|援助交际|张筱雨|招鸡|招妓|中年美妇|抓胸|自拍|自慰|作爱|18禁|99bb|a4u|a4y|adult|amateur|anal|a片|fuck|gay片|g点|g片|hardcore|h动画|h动漫|incest|porn|secom|sexinsex|sm女王|xiao77|xing伴侣|tokyohot|yin荡|汉芯造假|杨树宽|中印边界谈判结果|喂奶门|摸nai门|酒瓶门|脱裤门|75事件|乌鲁木齐|新疆骚乱|针刺|打针|食堂涨价|饭菜涨价|h1n1|瘟疫爆发|yangjia|y佳|yang佳|杨佳|杨j|袭警|杀警|武侯祠|川b26931|贺立旗|周正毅|px项目|骂四川|家l福|家le福|加了服|麦当劳被砸|豆腐渣|这不是天灾|龙小霞|震其国土|yuce|提前预测|地震预测|隐瞒地震|李四光预测|蟾蜍迁徙|地震来得更猛烈|八级地震毫无预报|踩踏事故|聂树斌|万里大造林|陈相贵|张丹红|尹方明|李树菲|王奉友|零八奥运艰|惨奥|奥晕|凹晕|懊运|懊孕|奥孕|奥你妈的运|反奥|628事件|weng安|wengan|翁安|瓮安事件|化工厂爆炸|讨回工资|专业代理|帮忙点一下|帮忙点下|请点击进入|详情请进入|私人侦探|私家侦探|针孔摄象|调查婚外情|信用卡提现|无抵押贷款|广告代理|原音铃声|借腹生子|找个妈妈|找个爸爸|代孕妈妈|代生孩子|代开发票|腾讯客服电话|销售热线|免费订购热线|低价出售|款到发货|回复可见|连锁加盟|加盟连锁|免费二级域名|免费使用|免费索取|蚁力神|婴儿汤|售肾|刻章办|买小车|套牌车|玛雅网|电脑传讯|视频来源|下载速度|高清在线|全集在线|在线播放|txt下载|六位qq|6位qq|位的qq|个qb|送qb|用刀横向切腹|完全自杀手册|四海帮|足球投注|地下钱庄|中国复兴党|阿波罗网|曾道人|六合彩|改卷内幕|替考试|隐形耳机|出售答案|考中答案|答an|da案|资金周转|救市|股市圈钱|崩盘|资金短缺|证监会|质押贷款|小额贷款|周小川|刘明康|尚福林|孔丹|安眠酮|血浆|普萘洛尔|呋塞米|西布曲明|testosterone|胰岛素样生长因子|促红细胞生成素|地西泮|尼可刹米|甲睾酮|adrenaline|erythropoietin|地奈德|莫达非尼|氯噻嗪|苯巴比妥|促性腺激素|泼尼松|麻黄草|雄烯二醇|地塞米松|tamoxifen|strychnine|androst|新型毒品|杜冷丁|兴奋剂|mdma|海洛因|海luo因|heroin|diamorphine|diacetylmorphine|鸦片|阿芙蓉|咖啡因|cocain|三唑仑|美沙酮|麻古|k粉|凯他敏|ketamine|冰毒|苯丙胺|cannabis|大麻|爱他死|氯胺酮|benzodiazepines|甲基安非他明|安非他命|吗啡|morphine|摇头丸|迷药|乖乖粉|narcotic|麻醉药|精神药品|穴海|协警|纳米比亚|专业调查|有华龙|jq的来|电信路|第一夫人|党鞭|黄巨|荡尽天下|家元自称玉皇大帝|主席李世民|何祚庥|刘刚|不要沉默|后勤集团|食堂涨价|发国难财|浪漫邂逅|红满堂|张小洋|炸学校|子宫|叫晶晶的女孩|回派|社会黑暗|国之母|国母|国姆|东方微点|震惊全球|nowto|chengdu|徐明|六月飞雪|暴力虐待|暴力袭击|天府广场|粮荒|洗脑班|复制地址到地址栏|李愚蠢|中国猪|台湾猪|进化不完全的生命体|震死他们|贱人|装b|大sb|傻逼|傻b|煞逼|煞笔|刹笔|傻比|沙比|欠干|婊子养的|我日你|我操|我草|卧艹|卧槽|爆你菊|艹你|cao你|你他妈|真他妈|别他吗|草你吗|草你丫|操你妈|擦你妈|操你娘|操他妈|日你妈|干你妈|干你娘|娘西皮|狗操|狗草|狗杂种|狗日的|操你祖宗|操你全家|操你大爷|妈逼|你麻痹|麻痹的|妈了个逼|马勒|狗娘养|贱比|贱b|下贱|死全家|全家死光|全家不得好死|全家死绝|白痴|无耻|sb|杀b|你吗b|你妈的|婊子|贱货|人渣|混蛋|媚外|和弦|兼职|限量|铃声|性伴侣|男公关|火辣|精子|射精|诱奸|强奸|做爱|性爱|发生关系|按摩|快感|处男|猛男|少妇|屌|屁股|下体|a片|内裤|浑圆|咪咪|发情|刺激|白嫩|粉嫩|兽性|风骚|呻吟|sm|阉割|高潮|裸露|不穿|一丝不挂|脱光|干你|干死|我干|中日没有不友好的|木牛流马的污染比汽车飞机大|他们嫌我挡了城市的道路|当官靠后台|警察我们是为人民服务的|中石化说亏损|做人不能太cctv了|领导干部吃王八|工商税务两条狼|公检法是流氓|公安把秩序搞乱|剖腹一刀五千几|读不起选个学校三万起|父母下岗儿下地|裙中性运动|自制|制造|制作|收购|求购|电话|手机|销售|联系|qq|出售|买|卖|匕首|管制刀具|弹药|bb弹|违禁品|军用品|电棍|手枪|机枪|步枪|气枪|电狗|手狗|枪模|模型枪|仿真枪|狙击枪|信号枪|麻醉枪|来福枪|来复枪|冲锋枪|散弹枪|卡宾枪|枪支|枪械|炸药|硝铵|火药|燃烧瓶|可燃物|爆炸物|炸弹|雷管|原子弹|燃烧弹|tnt|自制|制造|制作|收购|求购|电话|手机|销售|联系|qq|出售|买|卖|sb|sm|第一类政治、民族安全|10八|香港总部|颠覆中国政权|烟草局|温总理|文革|文化部|文化大革命|文物局|文学城|自由亚州|自由运动|对日强硬|一部分人因年龄或健康原因将不得不退下去|一党专政|一党专制|一贯道|一国两制|国际法院|新疆独|新疆独立|找政府评理|贾庆|林黄菊|18大的人事|政变|政府|政局雾里看花|银行联合管理局|政权|正法|镇压|政治|中功|伊拉克|伊斯兰|伊斯兰运动|中共|一中一台|政治反对派|政治犯|政治风波|政治局十八|政治字眼（如两岸关系等）|郑恩宠|蒋介石|中南海|中南海波澜起伏|中南海斗争|中南海高层权利斗争|中南海惊现东宫小朝廷|中南海秘闻|原子能机构|中共权斗|中共权利斗争|中南海权力斗争|中宣部|中央|治军方针定调|中央军委|中央派系斗争|中央团系|李三共志|国家安全|造反|李克强第五代领导人|李克强接班人|国家机密|国家主要部委|国军|国民党|胡紧掏|胡锦涛|胡锦滔|胡锦淘|胡景涛|国民根本大法|国庆|非典|东突厥斯坦伊斯兰运动|海关总署|国务院|国贼|海洋局|海外民运|国资委|封杀|东西南北论坛|分析|中共派系力量再达成妥协|反革命|反攻|反攻大陆|反共|反华|反恐委员会|反社会|游龙商务联盟|游戏管理员|反政府|反目|优化官员|应召|印章|反日|反人类|反腐败|反封锁|颠覆中华人民共和国政|东北独立|帝国之梦|帝国主义|弟大物勃|北京市亿霖|公司|布局十八大|藏独|党政一把手|党中央|党风日下|邓小平|邓晓平|党禁|党退|布什|大纪元|测绘局|大陆当局|分裂|压迫|分裂中国|分裂中华人民共和国|第5代领导|第四代|多党|第五代领导|第五代领导人|第五代领导新星|第五代中共领导人|独裁|独裁政治|独夫|独立|动乱|北京|18大|1717wg.88448.com|北京当局|绑架|18da|暗杀|报警|北京政权|暴力|暴乱|暴政|爆炸|安理会|安全部|安全监管|安全局|保监会|保密室|西藏|西藏独|西藏独立|西藏天葬|西山会议|打压|爱滋|出卖|18届|18届常委|18届名单|18届委员|18届中央|18权力布局|18预测|爱国运动正名|爱国者同盟|奥运|奥运会|18大的人事安排意见|18大人事变动|连战|两岸关系|两岸三地论坛|亡党|两个中国|两会|连准|秘书长|密码|南联盟|内幕|深圳国领|深圳红岭|年轻高知团系接班|收货|收容所|示威|世华商务|事实独立|廉政大论坛|柳树中学|六四|卖国|卖国唐捷|人弹|人民|人民报|人民报讯|天安门|天安门录影带|天安门母亲|天安门事件|天安门屠杀|天安门一代|末世论|人民大会堂|绵恒|绵恒异议人士|人民大众|人民大众时事参考|人民内情真相|人民日报|人民真实|人民真实报道|人民真实报告|人民之声|人民之声论坛|人权|人事变动|人事布局出手既稳又重|人事接班|人体炸弹|人员安排|人员变动|人渣|人质|六四民主运动|天府广场集会|通信维权|铁道部|统独|统计局|统战|美国参考|美国佬|美国之音|台*湾|台办|台独|台盟|台湾|台湾独|台湾独立|台湾共合国|台湾狗|台湾建国运动组织|台湾联盟|台湾青年独立联盟|台湾政论区|台湾自由联盟|摊牌要权|贪官|贪污犯|唐人电视台|讨伐|讨伐中宣部|缅甸|美利坚|权威主义国家的合法性理论|全国两会|蒙独|蒙古独|亲美|亲民党|亲日|青天白日旗|蒙古独立|六四事件|六四学生运动|领事馆|令狐计划|同胞书|令计划|透支|突厥斯坦|涂运普|屠杀|团派|团派政治明星|推翻|推翻社会主义制度|退党|外汇局|外交部|外交论坛|外交与方略|外蒙|外围|晚年周恩来|平反|迫害|罗蒙马格赛基金会|十大穷人|十六大|十八大|十八大权力变更|十八大人事|十八大人事安排意见|十八大人事调整|十八大未来|十八大预测|省市换班第五代冒起|省委大门集合|省政府大门集合|十八大政治局|萨达姆|萨拉托加|三个代表|如何推翻中共|瑞士金融大学|上访|上海帮|上海孤儿院|上海垮台|上海市劳动和社会保障局违规使用社保资金|社保基金会|社会主义|融资|审计署|生当作人杰之昨日重现|十八换血|十八届|十八年|商务部|商务领航|十八庆红|数据中国|刷卡|刷卡消费|双十节|刘奇葆热门人选|四川独|四川独立|出台|膏药旗|高层人事变动|加拿大皇家骑警|胡权利瓜分|胡书记|胡温|胡晓炼|胡耀邦|胡玉敏|胡泽君|胡振民|胡总书记|建国党|检查部|韩长赋|韩东方|韩联潮|韩正|韩正降职副市长|捍卫社会主义共和国|监管局|建设部|僵贼|僵贼民|姜春云|姜大明|姜洪禄|姜建清|姜伟新|姜异康|将则民|江Core|江core.江ze民|江澤民.江八|江八点.江独裁|江蛤蟆.江核心|江流氓|江戏子|江责民|江则民|江泽慧江泽林|江泽民.江贼|江贼民.江折民|江猪.江猪媳|江主|酱猪媳|抗日|抗议|教养院|教育部|老人政治|公证|公证处|共军|锦涛|共青团背景|共铲党|老江|联合行动|联大|联合国|联合起诉最高人民法院|老毛|老毛子|积克馆|共产|共产党|回民|回民暴|回民|暴动|换届隐忧|共党|大史|复转军人|公关|公馆|军长发威|军事委员会|军委|军委主席|军转安置|军转干部|公开批评中央高层领导人|公开信胡|公开信胡书记空中民主墙|鸡毛信文汇|恐怖份子|恐怖主义|纪元|公投|理想信念斗争|持不同政见|腐败|从陈良宇倒台看中国政局走势|周恩来后悔|周恩来自责|周刊纪事|周恩来忏悔|中央召开北戴河会议决定十八大高层人事|中国在统一问题上的投降主义|中国真实内容|子女任职名单|咨讯紫阳|中国政坛“清华帮”盛极而衰|中国政坛新星中的四大天王|中国政治新星|中国泛蓝联盟|中国复兴论坛|中国改革年代政治斗争|西藏论坛|中国高层权力斗争|中国高层人事变动解读|中国六四真相|中国论坛|中国媒体十大事件|中国民主党联合总部|中国社会的艾滋病|中国社会进步党|中国社会论坛|中国共产党|中国孤儿院|中国和平|中国联通|中国太子|中国网通|中共当局|中共独裁|中共反腐风暴大背景揭秘|王兆国将任常委或取代贾庆龄|中共领导人黑幕|香港明报|星亚网络影视公司|中共十八布局|中共十八大的基层选举|中共十八大前夕|中共十八大前夕人事布局出手既稳又重|中共十八大热门人选|中共十八热门人选|中共十八人事|中共十八人事变动|中共十八人选|中国威胁论|中国问题论坛|中国移动|中国移动通信|中国银联|18大委员名单|大中国论坛|大中华论坛|18高官互调|4．25事件|钟山风雨论坛|重新评定|邮政局|印尼伊斯兰祈祷团|中央政治局|中医药局|40万名车车主名单|信访局|信号|信息产业部|信用卡|信用卡空卡|信用危机|星相|版署|新生网|新势力|英雄纪念碑|九评共产党|新党|中华人民正邪|中华时事|燕南评论|央视内部晚会|中华养生益智功|中华真实报道|中华大地思考|宇明网|宇宙真理|语音|中华大众|中华讲清|阿拉|安南|东洲|北韩|鲜族|俄国|民告官|民国|民航局|民进党|民联|民意|民意论坛|民运|民阵|民政部|民猪|民主|民主潮|民主墙|民族矛盾|民族问题|诺贝尔和平奖|大法|大法弟子|dpp大法|falun|falu|发抡|发抡功|洗脑|下法轮|发轮|发伦|发伦功|发仑|发沦|发纶|发论|发论功|发论公|发正念|发囵|发愣|发瞟|罚抡|罚伦|罚仑|罚沦|罚纶|罚囵|筏抡|筏轮|筏伦|筏仑|筏沦|筏纶|筏论|筏囵|伐抡|伐轮|伐伦|伐仑|伐沦|伐论|伐囵|乏抡|乏轮|乏伦|乏仑|乏沦|乏纶|乏论|乏囵|阀抡|阀伦|阀仑|阀沦|阀纶|阀论|阀囵|法轮功|法*功|法.轮.功|法L功|法lun功|法功|法会|法抡|法抡功|法轮|法轮大法|法轮佛法|法轮功|法伦|法仑|法沦|法纶|法论|法十轮十功|法西斯|法院|法正|法谪|法谪功|法輪|法囵|法愣|珐.輪功|珐抡|珐轮|珐伦|珐仑|珐沦|五不|五不争鸣论坛|五出三进|五套功法|邝锦文|垡抡|垡轮|垡伦|垡仑|垡沦|垡纶|垡论|垡囵|茳澤民|荭志|闳志|闵维方|氵去|氵去车仑工力|转法轮|砝抡|砝轮|砝伦|砝仑|砝沦|砝纶|真、善、忍|真理教|真善美|真善忍|砝论|砝囵|泓志|屙民|珐纶|珐论|珐囵|falun|Falundafa|fa轮|Flg|弟子|地下教会|炼功|梦网洪志|轮大|抡功|轮功|伦功|摩门教|三水法轮|三唑仑|天皇|天怒|天葬|车臣|达赖|功法|讲法|基督|基督教|护法|回教|教徒|功友|大师|达赖喇嘛|虹志|鸿志|洪传|贯通两极法|光祖|洪吟|洪哲胜|洪志|宏法|观世音|宏志|弘志|古兰经|红志|车库|车仑|经文|东方红时空|风水宝地|舆论|舆论反制|风雨神州|自杀手册|自杀指南|自已的故事|自由|自由民主论坛|自由网|自由写作奖|自制手枪|自主择业|宗教压迫|先天健康法|阻止中华人民共和国统|昨日重现|咨询|咨讯|风雨神州论坛|东方时空|独立台湾会|访问链接|飞扬论坛|独立中文笔会|东南西北论谈|真相|真象|侦探设备|争鸣论坛|正帮通信公司|正见|正邪大决战|正义党论坛|北京之春民主论坛|北美讲坛|倒陈运动的最大受益人|反封锁技术|反腐败论坛|地下刊物|恶搞晚会|北美讲坛|北美论坛|地铁十号线塌方|逊克农场26队|春夏论坛|邓颖超日记|国研新闻邮件|国色天香网|春夏之交|春夏自由论坛|亚洲周刊|亚洲自由之声|博讯|邓小平和他的儿子|政坛两黑马|政坛明日之星|中國當局|中朝|中电信|中俄边界|中俄边界新约|中俄密约|致胡书记的公开信|指点江山论坛|北美自由论坛|参加者回忆录|大纪元新闻网|九-评|九.评|九、评|九—评|九成|九成新|九码|九评|新观察论坛|新华举报|新华内情|新华社|新华通论坛|大众真人真事|大家论坛|新唐人|新唐人电视台|新闻办|新闻封锁|新语丝|印尼伊斯兰祈祷团|北大三角|地论坛|9.635|9评|中华人民实话实说|杂志|E周刊|安装卫星电视|代表大会|论坛|南大论坛|南大自由论坛|强硬发言|南华早报|你说我说论坛|木子论坛|两会报道|两会新闻|轻舟快讯|热站政论网|时代论坛|时事参考|时事论坛|万维读者论坛|统独论坛|特讯|世界经济导报|世界通|超常科学|传说的胡曾联手是一种假象|大纪元|港澳办|跨世纪的良心犯|告全国股民同胞书|华岳|华岳时事论坛|华夏文摘快递|华语世界论坛|和平请愿书|华人媒体|华通时事论坛|华夏文摘|广闻|广电局|广东五元集团|广告|广闻|红潮谎言录|告全体网民书|告中国人民解放军广大官兵书|港料|大史记|联总之声|集会|集体上访|讲事实|要说法|霍英东临终遗言|大史纪|高校群体事件|传中共中央关于18大的人事安排意见|安眠酮|代血浆|普萘洛尔|呋塞米|西布曲明|testosterone|胰岛素样生长因子|促红细胞生成素|地西泮|尼可刹米|甲睾酮|adrenaline|erythropoietin|地奈德|莫达非尼|氯噻嗪|苯巴比妥|促性腺激素|泼尼松|麻黄草|雄烯二醇|地塞米松|tamoxifen|strychnine|androst|新型毒品|杜冷丁|兴奋剂|mdma|海洛因|海luo因|heroin|diamorphine|diacetylmorphine|鸦片|阿芙蓉|咖啡因|cocain|三唑仑|美沙酮|麻古|k粉|凯他敏|ketamine|冰毒|苯丙胺|cannabis|大麻|爱他死|氯胺酮|benzodiazepines|甲基安非他明|安非他命|吗啡|morphine|摇头丸|迷药|乖乖粉|narcotic|麻醉药|精神药品|爱女人|爱液|按摩棒|拔出来|爆草|包二奶|暴干|暴奸|暴乳|爆乳|暴淫|屄|被操|被插|被干|逼奸|仓井空|插暴|操逼|操黑|操烂|肏你|肏死|操死|操我|厕奴|插比|插b|插逼|插进|插你|插我|插阴|潮吹|潮喷|成人电影|成人论坛|成人色情|成人网站|成人文学|成人小说|艳情小说|成人游戏|吃精|赤裸|抽插|扌由插|抽一插|春药|大波|大力抽送|大乳|荡妇|荡女|盗撮|多人轮|发浪|放尿|肥逼|粉穴|封面女郎|风月大陆|干死你|干穴|肛交|肛门|龟头|裹本|国产av|好嫩|豪乳|黑逼|后庭|后穴|虎骑|花花公子|换妻俱乐部|黄片|几吧|鸡吧|鸡巴|鸡奸|寂寞男|寂寞女|妓女|激情|集体淫|奸情|叫床|脚交|金鳞岂是池中物|金麟岂是池中物|精液|就去日|巨屌|菊花洞|菊门|巨奶|巨乳|菊穴|开苞|口爆|口活|口交|口射|口淫|裤袜|狂操|狂插|浪逼|浪妇|浪叫|浪女|狼友|聊性|流淫|铃木麻|凌辱|漏乳|露b|乱交|乱伦|轮暴|轮操|轮奸|裸陪|买春|美逼|美少妇|美乳|美腿|美穴|美幼|秘唇|迷奸|密穴|蜜穴|蜜液|摸奶|摸胸|母奸|奈美|奶子|男奴|内射|嫩逼|嫩女|嫩穴|捏弄|女优|炮友|砲友|喷精|屁眼|品香堂|前凸后翘|强jian|强暴|强奸处女|情趣用品|情色|拳交|全裸|群交|惹火身材|人妻|人兽|日逼|日烂|肉棒|肉逼|肉唇|肉洞|肉缝|肉棍|肉茎|肉具|揉乳|肉穴|肉欲|乳爆|乳房|乳沟|乳交|乳头|三级片|骚逼|骚比|骚女|骚水|骚穴|色逼|色界|色猫|色盟|色情网站|色区|色色|色诱|色欲|色b|少年阿宾|少修正|射爽|射颜|食精|释欲|兽奸|兽交|手淫|兽欲|熟妇|熟母|熟女|爽片|爽死我了|双臀|死逼|丝袜|丝诱|松岛枫|酥痒|汤加丽|套弄|体奸|体位|舔脚|舔阴|调教|偷欢|偷拍|推油|脱内裤|文做|我就色|无码|舞女|无修正|吸精|夏川纯|相奸|小逼|校鸡|小穴|小xue|写真|性感妖娆|性感诱惑|性虎|性饥渴|性技巧|性交|性奴|性虐|性息|性欲|胸推|穴口|学生妹|穴图|亚情|颜射|阳具|杨思敏|要射了|夜勤病栋|一本道|一夜欢|一夜情|一ye情|阴部|淫虫|阴唇|淫荡|阴道|淫电影|阴阜|淫妇|淫河|阴核|阴户|淫贱|淫叫|淫教师|阴茎|阴精|淫浪|淫媚|淫糜|淫魔|淫母|淫女|淫虐|淫妻|淫情|淫色|淫声浪语|淫兽学园|淫书|淫术炼金士|淫水|淫娃|淫威|淫亵|淫样|淫液|淫照|阴b|应召|幼交|幼男|幼女|欲火|欲女|玉女心经|玉蒲团|玉乳|欲仙欲死|玉穴|援交|原味内衣|援助交际|张筱雨|招鸡|招妓|中年美妇|抓胸|自拍|自慰|作爱|18禁|99bb|a4u|a4y|adult|amateur|anal|a片|fuck|gay片|g点|g片|hardcore|h动画|h动漫|incest|porn|secom|sexinsex|sm女王|xiao77|xing伴侣|tokyohot|yin荡|KC短信|KC嘉年华|短信|短信广告|短信平台|短信群发|短信群发器|短信商务广告|草泥马|妈的|传销|草你妈|尼玛比', '2018-11-30 09:12:44', '2018-11-30 09:12:46');
INSERT INTO `basic` VALUES (2, '公司基础权限', 'nothing', '2019-01-08 15:30:49', '2019-01-08 15:30:51');
INSERT INTO `basic` VALUES (3, '公司基础角色', 'nothing', '2019-01-08 15:35:45', '2019-01-08 15:35:46');
INSERT INTO `basic` VALUES (4, '公司基础公告栏目', '[{\"name\":\"\\u4f01\\u4e1a\\u52a8\\u6001\",\"description\":\"\\u52a8\\u6001\\u63cf\\u8ff0\"},{\"name\":\"\\u653e\\u5047\\u901a\\u77e5\",\"description\":\"\\u653e\\u5047\\u901a\\u77e5\\u63cf\\u8ff0\"},{\"name\":\"\\u4f01\\u4e1a\\u65b0\\u95fb\",\"description\":\"\\u4f01\\u4e1a\\u65b0\\u95fb\\u63cf\\u8ff0\"},{\"name\":\"\\u798f\\u5229\\u901a\\u544a\",\"description\":\"\\u798f\\u5229\\u901a\\u544a\\u63cf\\u8ff0\"}] ', '2019-01-12 08:11:13', '2019-01-12 08:11:13');
INSERT INTO `basic` VALUES (5, '公司基础组织结构树', '{\"type\":\"root\",\"name\":\"\",\"children\":[{\"name\":\"\\u4eba\\u4e8b\\u90e8\",\"type\":\"node\",\"children\":[{\"name\":\"HR\",\"type\":\"node\",\"children\":[]}]},{\"name\":\"\\u8d22\\u52a1\\u90e8\",\"type\":\"node\",\"children\":[]},{\"name\":\"\\u7ecf\\u7406\",\"type\":\"node\",\"children\":[]}]}', '2019-01-24 09:51:27', '2019-01-24 09:51:27');
INSERT INTO `basic` VALUES (6, '评审通-表单计算类别基础数据', '[\"\\u6982\\u7b97\",\"\\u9884\\u7b97\",\"\\u5de5\\u7a0b\\u63a7\\u5236\\u4ef7\",\"\\u5176\\u4ed6\"]', '2019-02-28 08:51:17', '2019-02-28 08:51:17');
INSERT INTO `basic` VALUES (7, '评审通-表单工程分类基础数据', '[\"\\u5e02\\u653f\\u5de5\\u7a0b\",\"\\u56ed\\u6797\",\"\\u6c34\\u5229\",\"\\u571f\\u6728\\u5de5\\u7a0b\"]', '2019-02-28 08:53:15', '2019-02-28 08:53:15');
INSERT INTO `basic` VALUES (8, '评审通-送审业务负责科室基础数据', '[\"\\u6982\\u7b97\\u4e00\\u79d1\",\"\\u4e8c\\u79d1\",\"\\u4e09\\u79d1\",\"\\u56db\\u79d1\"]', '2019-02-28 09:25:59', '2019-02-28 09:25:59');
INSERT INTO `basic` VALUES (9, '评审通-行为标签基础数据', '[\"\\u5c3d\\u60c5\\u53d1\\u6325\",\"\\u7d27\\u6025\",\"\\u5341\\u4e07\\u706b\\u6025\"]', '2019-02-28 09:29:07', '2019-02-28 09:29:07');

-- ----------------------------
-- Table structure for cache
-- ----------------------------
DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cache` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for collaboration_invitation
-- ----------------------------
DROP TABLE IF EXISTS `collaboration_invitation`;
CREATE TABLE `collaboration_invitation`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `initiate_user` int(11) NOT NULL COMMENT '邀请发起者id',
  `receive_user` int(11) NOT NULL COMMENT '邀请接收者id',
  `status` int(11) NOT NULL DEFAULT 3 COMMENT '状态:0拒绝,1接受,2完成,3未选择',
  `collaborative_task_id` int(11) NOT NULL COMMENT '协作任务id',
  `difference` int(11) NOT NULL DEFAULT 1 COMMENT '用于在回收站中编辑时区别负责任务还是协助的任务',
  `is_delete` int(11) NOT NULL DEFAULT 0 COMMENT '放入回收站',
  `company_id` int(11) NOT NULL DEFAULT 0 COMMENT '公司id',
  `complete_time` datetime(0) NULL DEFAULT NULL COMMENT '参与人完成时间',
  `transferred_person` int(11) NULL DEFAULT NULL COMMENT '被转交人id',
  `transfer_reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '转交理由',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  `replace_company_id` int(11) NULL DEFAULT NULL COMMENT '被转交人公司id',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 13 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of collaboration_invitation
-- ----------------------------
INSERT INTO `collaboration_invitation` VALUES (1, 14, 11, 3, 1, 1, 0, 1, NULL, NULL, NULL, '2019-04-15 14:09:41', '2019-04-15 14:09:41', NULL);
INSERT INTO `collaboration_invitation` VALUES (2, 14, 18, 3, 1, 1, 0, 1, NULL, NULL, NULL, '2019-04-15 14:09:41', '2019-04-15 14:09:41', NULL);
INSERT INTO `collaboration_invitation` VALUES (3, 14, 21, 3, 1, 1, 0, 1, NULL, NULL, NULL, '2019-04-15 14:09:41', '2019-04-15 14:09:41', NULL);
INSERT INTO `collaboration_invitation` VALUES (4, 14, 14, 1, 1, 1, 0, 1, NULL, NULL, NULL, '2019-04-15 14:09:41', '2019-04-15 17:13:49', NULL);
INSERT INTO `collaboration_invitation` VALUES (5, 14, 14, 3, 1, 1, 0, 15, NULL, NULL, NULL, '2019-04-15 14:09:41', '2019-04-15 14:09:41', NULL);
INSERT INTO `collaboration_invitation` VALUES (6, 14, 10, 3, 2, 1, 0, 1, NULL, NULL, NULL, '2019-04-15 17:01:13', '2019-04-15 17:01:13', NULL);
INSERT INTO `collaboration_invitation` VALUES (7, 14, 4, 3, 2, 1, 0, 1, NULL, NULL, NULL, '2019-04-15 17:01:13', '2019-04-15 17:01:13', NULL);
INSERT INTO `collaboration_invitation` VALUES (8, 14, 14, 1, 2, 1, 0, 2, NULL, NULL, NULL, '2019-04-15 17:01:13', '2019-04-15 17:01:13', NULL);
INSERT INTO `collaboration_invitation` VALUES (9, 14, 14, 1, 2, 1, 0, 3, NULL, NULL, NULL, '2019-04-15 17:01:13', '2019-04-15 17:01:13', NULL);
INSERT INTO `collaboration_invitation` VALUES (10, 14, 14, 1, 2, 1, 0, 15, NULL, NULL, NULL, '2019-04-15 17:01:13', '2019-04-15 17:01:13', NULL);
INSERT INTO `collaboration_invitation` VALUES (11, 14, 3, 3, 2, 1, 0, 0, NULL, NULL, NULL, '2019-04-15 17:01:13', '2019-04-15 17:01:13', NULL);
INSERT INTO `collaboration_invitation` VALUES (12, 14, 14, 1, 2, 1, 0, 0, NULL, NULL, NULL, '2019-04-15 17:01:13', '2019-04-15 17:01:13', NULL);

-- ----------------------------
-- Table structure for collaborative_task
-- ----------------------------
DROP TABLE IF EXISTS `collaborative_task`;
CREATE TABLE `collaborative_task`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '协作任务描述',
  `form_area` json NULL COMMENT '任务相关数据,发起者填写,被邀请者可添加补充内容',
  `status` int(11) NULL DEFAULT 0 COMMENT '状态:0是未完成,1是已完成',
  `initiate_id` int(11) NULL DEFAULT NULL COMMENT '发起者id',
  `principal_id` int(11) NULL DEFAULT NULL COMMENT '指定负责人id,若不指定默认为发起者id',
  `limit_time` datetime(0) NULL DEFAULT NULL COMMENT '任务期限',
  `edit_form` int(11) NULL DEFAULT 3 COMMENT '表单编辑,0:都可以编辑,1:仅负责人和发起者可编辑,2:仅协助者可编辑,3:都不能编辑',
  `difference` int(11) NULL DEFAULT 0 COMMENT '用于在回收站中编辑时区别负责任务还是协助的任务',
  `is_delete` int(11) NULL DEFAULT 0 COMMENT '放入回收站',
  `is_receive` int(11) NULL DEFAULT 0 COMMENT '负责人是否接收任务,0未接受,1接收,2拒绝',
  `form_edit` int(11) NULL DEFAULT 0 COMMENT '是否添加表单',
  `form_people` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '可编辑表单的人',
  `company_id` int(11) NULL DEFAULT 0 COMMENT '公司id',
  `initiate_opinion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '发起人意见',
  `review_time` datetime(0) NULL DEFAULT NULL COMMENT '发起人审核时间',
  `complete_time` datetime(0) NULL DEFAULT NULL COMMENT '负责人点击完成时间',
  `principal_opinion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '负责人意见',
  `pst_id` int(11) NULL DEFAULT NULL COMMENT '评审通关联id',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of collaborative_task
-- ----------------------------
INSERT INTO `collaborative_task` VALUES (1, '1111111', '<p>11111111</p>', NULL, 0, 14, 10, NULL, 0, 0, 0, 0, 1, '[\"\\u534f\\u52a9\\u7684\\u53d1\\u8d77\\u4eba\\u3001\\u8d1f\\u8d23\\u4eba\",\"\\u53c2\\u4e0e\\u4eba\"]', 1, NULL, NULL, NULL, NULL, NULL, '2019-04-15 14:09:41', '2019-04-15 14:09:41');
INSERT INTO `collaborative_task` VALUES (2, '2222222', '<p>2222222</p>', NULL, 0, 14, 18, NULL, 0, 0, 0, 0, 1, '[\"\\u534f\\u52a9\\u7684\\u53d1\\u8d77\\u4eba\\u3001\\u8d1f\\u8d23\\u4eba\",\"\\u53c2\\u4e0e\\u4eba\"]', 1, NULL, NULL, NULL, NULL, NULL, '2019-04-15 17:01:13', '2019-04-15 17:01:13');

-- ----------------------------
-- Table structure for company
-- ----------------------------
DROP TABLE IF EXISTS `company`;
CREATE TABLE `company`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '公司名称',
  `creator_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '公司创建者的id',
  `verified` smallint(6) NOT NULL DEFAULT 0 COMMENT '公司认证标识,0未认证,1等待认证,2审核通过,3审核不通过',
  `email_count` int(11) NOT NULL COMMENT '可用邮件条数',
  `sms_count` int(11) NOT NULL COMMENT '可用短信条数',
  `abbreviation` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '公司简称',
  `number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '000000' COMMENT '企业号',
  `logo_id` int(11) NULL DEFAULT NULL COMMENT '公司logo 的id',
  `tel` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '企业电话',
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '企业类型',
  `district` json NULL COMMENT '所属地区',
  `industry` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '所属行业',
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '公司地址',
  `zip_code` int(11) NULL DEFAULT NULL COMMENT '邮编',
  `fax` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '传真',
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '公司网址',
  `license_id` int(11) NULL DEFAULT NULL COMMENT '执照文件id',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 29 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of company
-- ----------------------------
INSERT INTO `company` VALUES (1, '探知科技', '4', 1, 20, 470, '探知', NULL, 4, '14523658745', 'stateOwned', '[\"天津市\", \"市辖区\", \"和平区\"]', 'stateOwned', '1231', NULL, '212131', NULL, 1, '2019-03-14 16:52:27', '2019-04-14 22:47:26');
INSERT INTO `company` VALUES (2, '君都大盘鸡', '14', 0, 20, 20, '大盘鸡', '000000', NULL, '15896854785', '私营企业', NULL, NULL, '长葛市,丁字路', 10010, 'sdsfsfs', 'www.aaaa.com', NULL, '2019-03-12 17:05:19', '2019-03-12 17:05:15');
INSERT INTO `company` VALUES (3, '111', '14', 0, 20, 20, '123', '000000', NULL, '12546538955', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-03-14 16:52:25', '2019-03-15 09:09:43');
INSERT INTO `company` VALUES (14, '111111', '14', 0, 20, 20, NULL, '000000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-04-08 11:11:04', '2019-04-08 11:11:04');
INSERT INTO `company` VALUES (15, 'ttt', '14', 3, 20, 20, '12', NULL, 1, '18749817085', 'private', NULL, 'stateOwned', '1231', NULL, '10000', NULL, 15, '2019-04-08 11:59:42', '2019-04-10 18:15:34');
INSERT INTO `company` VALUES (24, 'vvvv', '1', 0, 20, 20, NULL, '000000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-04-13 09:55:52', '2019-04-13 09:55:52');
INSERT INTO `company` VALUES (27, '智障集中营', '4', 0, 20, 20, NULL, '000000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-04-13 09:58:53', '2019-04-13 09:58:53');
INSERT INTO `company` VALUES (28, 'ppp大本营', '4', 0, 20, 20, NULL, '000000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-04-13 14:11:45', '2019-04-13 14:11:45');

-- ----------------------------
-- Table structure for company_department
-- ----------------------------
DROP TABLE IF EXISTS `company_department`;
CREATE TABLE `company_department`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) UNSIGNED NULL DEFAULT NULL COMMENT '所属公司id',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '部门名称',
  `_lft` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `_rgt` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `parent_id` int(10) UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `department__lft__rgt_parent_id_index`(`_lft`, `_rgt`, `parent_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 117 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of company_department
-- ----------------------------
INSERT INTO `company_department` VALUES (1, 1, '探知科技', 1, 36, NULL);
INSERT INTO `company_department` VALUES (2, 1, '人事部', 16, 25, 1);
INSERT INTO `company_department` VALUES (3, 1, 'HR', 17, 18, 2);
INSERT INTO `company_department` VALUES (4, 1, '财务部11', 2, 5, 1);
INSERT INTO `company_department` VALUES (5, 1, '经理', 8, 11, 1);
INSERT INTO `company_department` VALUES (6, 1, '总总总总总经理部', 6, 7, 1);
INSERT INTO `company_department` VALUES (7, 1, '测试部门(创建成功后查看是否同步更新info)', 12, 13, 1);
INSERT INTO `company_department` VALUES (8, 1, '666', 14, 15, 1);
INSERT INTO `company_department` VALUES (9, 1, '5410', 19, 20, 2);
INSERT INTO `company_department` VALUES (10, 1, '5658', 21, 22, 2);
INSERT INTO `company_department` VALUES (11, 1, '造价一部', 26, 27, 1);
INSERT INTO `company_department` VALUES (12, 1, '造价二部', 28, 29, 1);
INSERT INTO `company_department` VALUES (13, 1, '反对法士大夫', 30, 31, 1);
INSERT INTO `company_department` VALUES (14, 1, '245245244', 32, 33, 1);
INSERT INTO `company_department` VALUES (15, 1, '2453245324', 34, 35, 1);
INSERT INTO `company_department` VALUES (16, 1, '654663163163', 23, 24, 2);
INSERT INTO `company_department` VALUES (17, 1, 'dsdasas', 3, 4, 4);
INSERT INTO `company_department` VALUES (18, 1, '46546546', 9, 10, 5);
INSERT INTO `company_department` VALUES (44, 14, '111111', 37, 46, NULL);
INSERT INTO `company_department` VALUES (45, 14, '人事部', 38, 41, 44);
INSERT INTO `company_department` VALUES (46, 14, 'HR', 39, 40, 45);
INSERT INTO `company_department` VALUES (47, 14, '财务部', 42, 43, 44);
INSERT INTO `company_department` VALUES (48, 14, '经理', 44, 45, 44);
INSERT INTO `company_department` VALUES (49, 15, '2222222', 47, 62, NULL);
INSERT INTO `company_department` VALUES (50, 15, '人事部', 48, 51, 49);
INSERT INTO `company_department` VALUES (51, 15, 'HR', 49, 50, 50);
INSERT INTO `company_department` VALUES (52, 15, '财务部', 52, 53, 49);
INSERT INTO `company_department` VALUES (53, 15, '经理', 54, 55, 49);
INSERT INTO `company_department` VALUES (54, 15, 'ccc', 56, 61, 49);
INSERT INTO `company_department` VALUES (55, 15, 'ppbl', 57, 58, 54);
INSERT INTO `company_department` VALUES (56, 15, 'aaa', 59, 60, 54);
INSERT INTO `company_department` VALUES (92, 24, 'vvvv', 63, 72, NULL);
INSERT INTO `company_department` VALUES (93, 24, '人事部', 64, 67, 92);
INSERT INTO `company_department` VALUES (94, 24, 'HR', 65, 66, 93);
INSERT INTO `company_department` VALUES (95, 24, '财务部', 68, 69, 92);
INSERT INTO `company_department` VALUES (96, 24, '经理', 70, 71, 92);
INSERT INTO `company_department` VALUES (107, 27, '智障集中营', 73, 82, NULL);
INSERT INTO `company_department` VALUES (108, 27, '人事部', 74, 77, 107);
INSERT INTO `company_department` VALUES (109, 27, 'HR', 75, 76, 108);
INSERT INTO `company_department` VALUES (110, 27, '财务部', 78, 79, 107);
INSERT INTO `company_department` VALUES (111, 27, '经理', 80, 81, 107);
INSERT INTO `company_department` VALUES (112, 28, 'ppp大本营', 83, 92, NULL);
INSERT INTO `company_department` VALUES (113, 28, '人事部', 84, 87, 112);
INSERT INTO `company_department` VALUES (114, 28, 'HR', 85, 86, 113);
INSERT INTO `company_department` VALUES (115, 28, '财务部', 88, 89, 112);
INSERT INTO `company_department` VALUES (116, 28, '经理', 90, 91, 112);

-- ----------------------------
-- Table structure for company_department_info
-- ----------------------------
DROP TABLE IF EXISTS `company_department_info`;
CREATE TABLE `company_department_info`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` int(10) UNSIGNED NOT NULL COMMENT '所属哪个公司',
  `info` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '公司部门的树信息',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `company_department_info_company_id_unique`(`company_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of company_department_info
-- ----------------------------
INSERT INTO `company_department_info` VALUES (2, 1, '{\"version\":\"HaZ8NbtXv\",\"data\":{\"id\":\"Vr8gct11545\",\"name\":\"\\u63a2\\u77e5\\u79d1\\u6280\",\"number_people\":10,\"users\":[{\"company_id\":null,\"type\":\"user\",\"id\":\"fBcEba97288\",\"name\":\"Pirvate2\",\"email\":null,\"tel\":\"13333333333\",\"avator\":\"http:\\/\\/gzts.oss-cn-beijing.aliyuncs.com\\/avators\\/cat.jpg\",\"gender\":\"\\u7537\\u5973\",\"roomNo\":\"110120119\",\"is_enable\":null},{\"company_id\":null,\"type\":\"user\",\"id\":\"rzw9Rv40126\",\"name\":\"\\u4f83\\u5927\\u5c71\",\"email\":null,\"tel\":\"15100000002\",\"avator\":\"http:\\/\\/gzts.oss-cn-beijing.aliyuncs.com\\/avators\\/cat.jpg\",\"gender\":\"\\u7537\\u5973\",\"roomNo\":\"110120119\",\"is_enable\":null},{\"company_id\":null,\"type\":\"user\",\"id\":\"DLHZeO106815\",\"name\":\"\\u7528\\u6237_15939965336\",\"email\":null,\"tel\":\"15939965336\",\"avator\":\"http:\\/\\/gzts.oss-cn-beijing.aliyuncs.com\\/avators\\/cat.jpg\",\"gender\":\"\\u7537\\u5973\",\"roomNo\":\"110120119\",\"is_enable\":null},{\"company_id\":null,\"type\":\"user\",\"id\":\"L3BqeF173504\",\"name\":\"\\u7528\\u6237_17634766666\",\"email\":null,\"tel\":\"17634766666\",\"avator\":\"http:\\/\\/gzts.oss-cn-beijing.aliyuncs.com\\/avators\\/cat.jpg\",\"gender\":\"\\u7537\\u5973\",\"roomNo\":\"110120119\",\"is_enable\":null},{\"company_id\":null,\"type\":\"user\",\"id\":\"J9OPLj135396\",\"name\":\"132\",\"email\":null,\"tel\":\"18749817085\",\"avator\":\"http:\\/\\/gzts.oss-cn-beijing.aliyuncs.com\\/avators\\/cat.jpg\",\"gender\":\"\\u7537\\u5973\",\"roomNo\":\"110120119\",\"is_enable\":null},{\"company_id\":null,\"type\":\"user\",\"id\":\"SrOieZ202085\",\"name\":\"\\u7528\\u6237_18749817055\",\"email\":null,\"tel\":\"18749817055\",\"avator\":\"http:\\/\\/gzts.oss-cn-beijing.aliyuncs.com\\/avators\\/cat.jpg\",\"gender\":\"\\u7537\\u5973\",\"roomNo\":\"110120119\",\"is_enable\":null},{\"company_id\":null,\"type\":\"user\",\"id\":\"VLqyAl183031\",\"name\":\"\\u7528\\u6237_15100000001\",\"email\":null,\"tel\":\"15100000001\",\"avator\":\"http:\\/\\/gzts.oss-cn-beijing.aliyuncs.com\\/avators\\/cat.jpg\",\"gender\":\"\\u7537\\u5973\",\"roomNo\":\"110120119\",\"is_enable\":null},{\"company_id\":null,\"type\":\"user\",\"id\":\"mh8yTd211612\",\"name\":\"\\u7528\\u6237_15100000003\",\"email\":null,\"tel\":\"15100000003\",\"avator\":\"http:\\/\\/gzts.oss-cn-beijing.aliyuncs.com\\/avators\\/cat.jpg\",\"gender\":\"\\u7537\\u5973\",\"roomNo\":\"110120119\",\"is_enable\":null}],\"children\":[{\"id\":\"K1khJZ21072\",\"name\":\"\\u4eba\\u4e8b\\u90e8\",\"number_people\":1,\"users\":[{\"company_id\":null,\"type\":\"user\",\"id\":\"qXp47111545\",\"name\":\"Pirvate\",\"email\":\"704356116@qq.com\",\"tel\":\"16638638285\",\"avator\":\"http:\\/\\/gzts.oss-cn-beijing.aliyuncs.com\\/avators\\/cat.jpg\",\"gender\":\"\\u7537\\u5973\",\"roomNo\":\"110120119\",\"is_enable\":null}],\"children\":[{\"id\":\"TWiIC130599\",\"name\":\"HR\",\"number_people\":0,\"users\":[],\"children\":[]},{\"id\":\"mwaELL87761\",\"name\":\"5410\",\"number_people\":0,\"users\":[],\"children\":[]},{\"id\":\"RgJIcT97288\",\"name\":\"5658\",\"number_people\":0,\"users\":[],\"children\":[]},{\"id\":\"NZxV8G154450\",\"name\":\"654663163163\",\"number_people\":0,\"users\":[],\"children\":[]}]},{\"id\":\"8xB52V40126\",\"name\":\"\\u8d22\\u52a1\\u90e811\",\"number_people\":0,\"users\":[],\"children\":[{\"id\":\"HhIh5D163977\",\"name\":\"dsdasas\",\"number_people\":0,\"users\":[],\"children\":[]}]},{\"id\":\"QvQBnY49653\",\"name\":\"\\u7ecf\\u7406\",\"number_people\":0,\"users\":[],\"children\":[{\"id\":\"XaoXh3173504\",\"name\":\"46546546\",\"number_people\":0,\"users\":[],\"children\":[]}]},{\"id\":\"7KQvBX59180\",\"name\":\"\\u603b\\u603b\\u603b\\u603b\\u603b\\u7ecf\\u7406\\u90e8\",\"number_people\":1,\"users\":[{\"company_id\":null,\"type\":\"user\",\"id\":\"8KTy2s49653\",\"name\":\"Soul\",\"email\":null,\"tel\":\"13733607139\",\"avator\":\"http:\\/\\/gzts.oss-cn-beijing.aliyuncs.com\\/avators\\/cat.jpg\",\"gender\":\"\\u7537\\u5973\",\"roomNo\":\"110120119\",\"is_enable\":null}],\"children\":[]},{\"id\":\"SQPmT168707\",\"name\":\"\\u6d4b\\u8bd5\\u90e8\\u95e8(\\u521b\\u5efa\\u6210\\u529f\\u540e\\u67e5\\u770b\\u662f\\u5426\\u540c\\u6b65\\u66f4\\u65b0info)\",\"number_people\":0,\"users\":[],\"children\":[]},{\"id\":\"4zwWM078234\",\"name\":\"666\",\"number_people\":0,\"users\":[],\"children\":[]},{\"id\":\"FoubVK106815\",\"name\":\"\\u9020\\u4ef7\\u4e00\\u90e8\",\"number_people\":0,\"users\":[],\"children\":[]},{\"id\":\"Pk4LSh116342\",\"name\":\"\\u9020\\u4ef7\\u4e8c\\u90e8\",\"number_people\":0,\"users\":[],\"children\":[]},{\"id\":\"POAmft125869\",\"name\":\"\\u53cd\\u5bf9\\u6cd5\\u58eb\\u5927\\u592b\",\"number_people\":0,\"users\":[],\"children\":[]},{\"id\":\"A9IgHr135396\",\"name\":\"245245244\",\"number_people\":0,\"users\":[],\"children\":[]},{\"id\":\"NMc536144923\",\"name\":\"2453245324\",\"number_people\":0,\"users\":[],\"children\":[]}]}}');
INSERT INTO `company_department_info` VALUES (3, 14, '{\"version\":\"9mTGjEhPd\",\"data\":{\"id\":\"7cGrdk421206\",\"name\":\"111111\",\"number_people\":1,\"users\":[{\"company_id\":14,\"type\":\"user\",\"id\":\"vrlEE1135396\",\"name\":\"132\",\"email\":null,\"tel\":\"18749817085\",\"avator\":\"http:\\/\\/gzts.oss-cn-beijing.aliyuncs.com\\/avators\\/cat.jpg\",\"gender\":\"\\u7537\\u5973\",\"roomNo\":\"110120119\",\"is_enable\":1}],\"children\":[{\"id\":\"UWjY7D430733\",\"name\":\"\\u4eba\\u4e8b\\u90e8\",\"number_people\":0,\"users\":[],\"children\":[{\"id\":\"j9ssgI440260\",\"name\":\"HR\",\"number_people\":0,\"users\":[],\"children\":[]}]},{\"id\":\"iZthUg449787\",\"name\":\"\\u8d22\\u52a1\\u90e8\",\"number_people\":0,\"users\":[],\"children\":[]},{\"id\":\"8ucuHG459314\",\"name\":\"\\u7ecf\\u7406\",\"number_people\":0,\"users\":[],\"children\":[]}]}}');
INSERT INTO `company_department_info` VALUES (4, 15, '{\"version\":\"ndTCO5cCz\",\"data\":{\"id\":\"YZH4Ha468841\",\"name\":\"2222222\",\"number_people\":1,\"users\":[],\"children\":[{\"id\":\"c8MRmE478368\",\"name\":\"\\u4eba\\u4e8b\\u90e8\",\"number_people\":0,\"users\":[],\"children\":[{\"id\":\"OkA0Ds487895\",\"name\":\"HR\",\"number_people\":0,\"users\":[],\"children\":[]}]},{\"id\":\"GHLKjy497422\",\"name\":\"\\u8d22\\u52a1\\u90e8\",\"number_people\":0,\"users\":[],\"children\":[]},{\"id\":\"xYFYRs506949\",\"name\":\"\\u7ecf\\u7406\",\"number_people\":0,\"users\":[],\"children\":[]},{\"id\":\"QyuOt0516476\",\"name\":\"ccc\",\"number_people\":1,\"users\":[{\"company_id\":null,\"type\":\"user\",\"id\":\"7GinJG135396\",\"name\":\"132\",\"email\":null,\"tel\":\"18749817085\",\"avator\":\"http:\\/\\/gzts.oss-cn-beijing.aliyuncs.com\\/avators\\/cat.jpg\",\"gender\":\"\\u7537\\u5973\",\"roomNo\":\"110120119\",\"is_enable\":null}],\"children\":[{\"id\":\"zjb4y8526003\",\"name\":\"ppbl\",\"number_people\":0,\"users\":[],\"children\":[]},{\"id\":\"ANYIC3535530\",\"name\":\"aaa\",\"number_people\":0,\"users\":[],\"children\":[]}]}]}}');
INSERT INTO `company_department_info` VALUES (5, 27, '{\"version\":\"dVG7rAYJ0\",\"data\":{\"id\":\"rhq89r1021407\",\"name\":\"\\u667a\\u969c\\u96c6\\u4e2d\\u8425\",\"number_people\":1,\"users\":[{\"company_id\":27,\"type\":\"user\",\"id\":\"Z8fmtp40126\",\"name\":\"\\u4f83\\u5927\\u5c71\",\"email\":null,\"tel\":\"15100000002\",\"avator\":\"http:\\/\\/gzts.oss-cn-beijing.aliyuncs.com\\/avators\\/cat.jpg\",\"gender\":\"\\u7537\\u5973\",\"roomNo\":\"110120119\",\"is_enable\":1}],\"children\":[{\"id\":\"4bdcfy1030934\",\"name\":\"\\u4eba\\u4e8b\\u90e8\",\"number_people\":0,\"users\":[],\"children\":[{\"id\":\"H2B3lX1040461\",\"name\":\"HR\",\"number_people\":0,\"users\":[],\"children\":[]}]},{\"id\":\"H28utv1049988\",\"name\":\"\\u8d22\\u52a1\\u90e8\",\"number_people\":0,\"users\":[],\"children\":[]},{\"id\":\"ZJtugI1059515\",\"name\":\"\\u7ecf\\u7406\",\"number_people\":0,\"users\":[],\"children\":[]}]}}');

-- ----------------------------
-- Table structure for company_department_manage_role
-- ----------------------------
DROP TABLE IF EXISTS `company_department_manage_role`;
CREATE TABLE `company_department_manage_role`  (
  `department_id` int(11) NOT NULL COMMENT '部门id',
  `role_id` int(11) NOT NULL COMMENT '拥有管理权的角色id'
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for company_external_contact
-- ----------------------------
DROP TABLE IF EXISTS `company_external_contact`;
CREATE TABLE `company_external_contact`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL COMMENT '公司id',
  `external_contact_id` int(11) NOT NULL COMMENT '外部联系人id',
  `status` int(11) NULL DEFAULT 2 COMMENT '2待验证,1同意,0拒绝',
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '申请描述',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of company_external_contact
-- ----------------------------
INSERT INTO `company_external_contact` VALUES (2, 1, 14, 1, '邀请成为外部联系人');
INSERT INTO `company_external_contact` VALUES (3, 1, 22, 1, '1111111111111111111111');

-- ----------------------------
-- Table structure for company_has_fun
-- ----------------------------
DROP TABLE IF EXISTS `company_has_fun`;
CREATE TABLE `company_has_fun`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `per_sort_id` int(11) NOT NULL COMMENT '公司拥有功能模块的id',
  `company_id` int(11) NOT NULL,
  `is_enable` int(11) NOT NULL DEFAULT 1 COMMENT '功能是否启用默认1为启用0禁用',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 87 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of company_has_fun
-- ----------------------------
INSERT INTO `company_has_fun` VALUES (1, 1, 1, 1, NULL, NULL);
INSERT INTO `company_has_fun` VALUES (2, 2, 1, 0, NULL, '2019-04-02 09:47:35');
INSERT INTO `company_has_fun` VALUES (3, 3, 1, 0, NULL, '2019-04-02 09:52:56');
INSERT INTO `company_has_fun` VALUES (4, 4, 1, 1, NULL, NULL);
INSERT INTO `company_has_fun` VALUES (31, 1, 14, 1, NULL, NULL);
INSERT INTO `company_has_fun` VALUES (32, 2, 14, 1, NULL, NULL);
INSERT INTO `company_has_fun` VALUES (33, 3, 14, 1, NULL, NULL);
INSERT INTO `company_has_fun` VALUES (34, 4, 14, 1, NULL, NULL);
INSERT INTO `company_has_fun` VALUES (35, 1, 15, 1, NULL, NULL);
INSERT INTO `company_has_fun` VALUES (36, 2, 15, 1, NULL, NULL);
INSERT INTO `company_has_fun` VALUES (37, 3, 15, 1, NULL, NULL);
INSERT INTO `company_has_fun` VALUES (38, 4, 15, 1, NULL, NULL);
INSERT INTO `company_has_fun` VALUES (67, 1, 24, 1, NULL, NULL);
INSERT INTO `company_has_fun` VALUES (68, 2, 24, 1, NULL, NULL);
INSERT INTO `company_has_fun` VALUES (69, 3, 24, 1, NULL, NULL);
INSERT INTO `company_has_fun` VALUES (70, 4, 24, 1, NULL, NULL);
INSERT INTO `company_has_fun` VALUES (79, 1, 27, 1, NULL, NULL);
INSERT INTO `company_has_fun` VALUES (80, 2, 27, 1, NULL, NULL);
INSERT INTO `company_has_fun` VALUES (81, 3, 27, 1, NULL, NULL);
INSERT INTO `company_has_fun` VALUES (82, 4, 27, 1, NULL, NULL);
INSERT INTO `company_has_fun` VALUES (83, 1, 28, 1, NULL, NULL);
INSERT INTO `company_has_fun` VALUES (84, 2, 28, 1, NULL, NULL);
INSERT INTO `company_has_fun` VALUES (85, 3, 28, 1, NULL, NULL);
INSERT INTO `company_has_fun` VALUES (86, 4, 28, 1, NULL, NULL);

-- ----------------------------
-- Table structure for company_license
-- ----------------------------
DROP TABLE IF EXISTS `company_license`;
CREATE TABLE `company_license`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 18 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of company_license
-- ----------------------------
INSERT INTO `company_license` VALUES (1, 1);
INSERT INTO `company_license` VALUES (15, 15);

-- ----------------------------
-- Table structure for company_logo
-- ----------------------------
DROP TABLE IF EXISTS `company_logo`;
CREATE TABLE `company_logo`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of company_logo
-- ----------------------------
INSERT INTO `company_logo` VALUES (1, 15);
INSERT INTO `company_logo` VALUES (4, 1);

-- ----------------------------
-- Table structure for company_notice
-- ----------------------------
DROP TABLE IF EXISTS `company_notice`;
CREATE TABLE `company_notice`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` int(10) UNSIGNED NOT NULL COMMENT '公司/组织id',
  `c_notice_column_id` int(10) UNSIGNED NOT NULL COMMENT '企业栏目id',
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '公告标题',
  `content` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '公告内容',
  `type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '公告类型',
  `organiser` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '发起人name',
  `order` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序字段',
  `is_show` smallint(6) NOT NULL DEFAULT 0 COMMENT '发布状态',
  `is_draft` smallint(6) NOT NULL DEFAULT 0 COMMENT '是否是草稿',
  `is_top` smallint(6) NOT NULL DEFAULT 0 COMMENT '是否置顶',
  `browse_count` smallint(6) NOT NULL DEFAULT 0 COMMENT '浏览次数',
  `notified` smallint(6) NOT NULL DEFAULT 0 COMMENT '是否进行通知过',
  `allow_download` smallint(6) NOT NULL DEFAULT 0 COMMENT '是否允许下载',
  `allow_user` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '可见人数组',
  `guard_json` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '选择的部门/人员信息数据',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 33 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of company_notice
-- ----------------------------
INSERT INTO `company_notice` VALUES (1, 1, 2, '用户管理', '<p>啊飒飒撒旦</p>', '企业新闻', 'Pirvate', 1, 1, 0, 0, 4, 1, 1, '{\"company_u_ids\":[5,14,4,1,2,3],\"wai_ids\":[]}', '\"all\"', '2019-03-19 11:21:10', '2019-03-22 14:49:46');
INSERT INTO `company_notice` VALUES (2, 1, 2, '用户管理', '<p>啊飒飒撒旦</p>', '企业新闻', 'Pirvate', 2, 1, 0, 0, 5, 1, 1, '{\"company_u_ids\":[5,14,4,1,2,3],\"wai_ids\":[]}', '\"all\"', '2019-03-19 11:21:18', '2019-03-19 16:57:16');
INSERT INTO `company_notice` VALUES (6, 1, 2, '评审通流程2.0', '<p>评审通流程2.0评审通流程2.0</p>', '企业新闻', '侃大山', 6, 1, 0, 0, 0, 1, 0, '{\"company_u_ids\":[5,14,4,1,2,3],\"wai_ids\":[]}', '\"all\"', '2019-03-22 09:35:47', '2019-03-22 09:35:50');
INSERT INTO `company_notice` VALUES (7, 1, 1, '省份汇编大放送', '<p>省份汇编大放送省份汇编大放送</p>', '企业动态', '侃大山', 7, 1, 0, 0, 1, 1, 0, '{\"company_u_ids\":[5,14,4,1,2,3],\"wai_ids\":[]}', '\"all\"', '2019-03-22 09:44:20', '2019-03-22 15:21:18');
INSERT INTO `company_notice` VALUES (8, 1, 2, '沪发改投', '<p>沪发改投沪发改投</p>', '企业新闻', '侃大山', 8, 1, 0, 0, 1, 1, 0, '{\"company_u_ids\":[5,14,4,1,2,3],\"wai_ids\":[]}', '\"all\"', '2019-03-22 09:52:31', '2019-03-22 11:55:37');
INSERT INTO `company_notice` VALUES (9, 1, 2, '沪发改投', '<p>沪发改投沪发改投</p>', '企业新闻', '侃大山', 9, 1, 0, 0, 0, 1, 0, '{\"company_u_ids\":[5,14,4,1,2,3],\"wai_ids\":[]}', '\"all\"', '2019-03-22 09:54:12', '2019-03-22 09:54:15');
INSERT INTO `company_notice` VALUES (10, 1, 2, '沪发改投', '<p>沪发改投沪发改投</p>', '企业新闻', '侃大山', 10, 1, 0, 0, 11, 1, 0, '{\"company_u_ids\":[5,14,4,1,2,3],\"wai_ids\":[]}', '\"all\"', '2019-03-22 09:59:31', '2019-03-22 15:21:12');
INSERT INTO `company_notice` VALUES (11, 1, 2, '345345', '<p><span style=\"color:#795e26\">showSearchInfoshowSearchInfo</span></p>', '企业新闻', '侃大山', 11, 1, 0, 0, 18, 1, 0, '{\"company_u_ids\":[5,14,4,1,2,3],\"wai_ids\":[]}', '\"all\"', '2019-03-22 10:03:03', '2019-03-29 15:58:39');
INSERT INTO `company_notice` VALUES (12, 1, 2, '樱花绽放季！樱花本周盛放来袭，错过等一年！', '<p>樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！樱花绽放季！樱花本周盛放来袭，错过等一年！</p>', '企业新闻', '用户_17634766666', 12, 1, 0, 0, 15, 1, 1, '{\"company_u_ids\":[4,14,5,18],\"wai_ids\":[]}', '{\"user_ids\":[\"lWtWb040126\"],\"department_ids\":[\"C9UhAb68707\"],\"rangeInfo\":{\"checkedKeys\":[\"lWtWb040126\",\"C9UhAb68707\"],\"checkedPersonnels\":[{\"type\":\"personnel\",\"key\":\"lWtWb040126\",\"title\":\"\\u4f83\\u5927\\u5c71\",\"linKey\":[{\"key\":\"yi4P3N11545\",\"title\":\"\\u63a2\\u77e5\\u79d1\\u6280\"},{\"type\":\"personnel\",\"key\":\"lWtWb040126\",\"title\":\"\\u4f83\\u5927\\u5c71\"}]}]}}', '2019-03-26 21:18:45', '2019-04-09 14:53:36');
INSERT INTO `company_notice` VALUES (13, 1, 1, '用户管理', '<p>大大是</p>', '企业动态', 'Pirvate', 13, 1, 0, 0, 0, 1, 1, '{\"company_u_ids\":[10,4,11,18,14,1,5],\"wai_ids\":[]}', '\"all\"', '2019-04-10 17:20:28', '2019-04-10 17:20:33');
INSERT INTO `company_notice` VALUES (14, 1, 2, '预期加入计算器', '<p>哈哈哈哈哈</p>', '企业新闻', 'Pirvate', 14, 1, 0, 0, 0, 1, 1, '{\"company_u_ids\":[10,4,11,18,14,1,5],\"wai_ids\":[]}', '\"all\"', '2019-04-10 17:25:04', '2019-04-10 17:25:08');
INSERT INTO `company_notice` VALUES (15, 1, 2, '山西省', '<p>犯得上发射点发射点</p>', '企业新闻', '侃大山', 15, 1, 0, 0, 1, 1, 0, '{\"company_u_ids\":[10,4,11,18,14,1,5],\"wai_ids\":[]}', '\"all\"', '2019-04-10 17:34:46', '2019-04-10 17:35:14');
INSERT INTO `company_notice` VALUES (16, 1, 2, '用户管理', '<p>wasfasf</p>', '企业新闻', 'Pirvate', 16, 1, 0, 0, 0, 1, 1, '{\"company_u_ids\":[10,4,11,18,14,1,5],\"wai_ids\":[]}', '\"all\"', '2019-04-10 17:35:58', '2019-04-10 17:36:03');
INSERT INTO `company_notice` VALUES (17, 1, 1, '用户管理', '<p>asda</p>', '企业动态', 'Pirvate', 17, 1, 0, 0, 0, 1, 1, '{\"company_u_ids\":[10,4,11,18,14,1,5],\"wai_ids\":[]}', '\"all\"', '2019-04-10 17:36:56', '2019-04-10 17:37:01');
INSERT INTO `company_notice` VALUES (18, 1, 2, '用户管理', '<p>asasfdsa</p>', '企业新闻', 'Pirvate', 18, 1, 0, 0, 0, 1, 0, '{\"company_u_ids\":[10,4,11,18,14,1,5],\"wai_ids\":[]}', '\"all\"', '2019-04-10 17:37:34', '2019-04-10 17:37:38');
INSERT INTO `company_notice` VALUES (19, 1, 2, '用户管理', '<p>adasdasd</p>', '企业新闻', 'Pirvate', 19, 1, 0, 0, 0, 1, 1, '{\"company_u_ids\":[10,4,11,18,14,1,5],\"wai_ids\":[]}', '\"all\"', '2019-04-10 17:38:06', '2019-04-10 17:38:11');
INSERT INTO `company_notice` VALUES (20, 1, 2, '用户管理', '<p>adasdasd</p>', '企业新闻', 'Pirvate', 20, 1, 0, 0, 1, 1, 1, '{\"company_u_ids\":[10,4,11,18,14,1,5],\"wai_ids\":[]}', '\"all\"', '2019-04-10 17:42:08', '2019-04-10 18:05:00');
INSERT INTO `company_notice` VALUES (25, 1, 1, '山西省', '<p>25425432453543</p>', '企业动态', '侃大山', 25, 1, 0, 0, 1, 1, 1, '{\"company_u_ids\":[10,4,11,18,14,1,5],\"wai_ids\":[]}', '\"all\"', '2019-04-10 18:25:38', '2019-04-15 18:08:49');
INSERT INTO `company_notice` VALUES (26, 1, 1, '山西省', '<p>25425432453543</p>', '企业动态', '侃大山', 26, 1, 0, 0, 1, 1, 1, '{\"company_u_ids\":[10,4,11,18,14,1,5],\"wai_ids\":[]}', '\"all\"', '2019-04-10 18:27:05', '2019-04-11 09:40:37');
INSERT INTO `company_notice` VALUES (27, 1, 2, '山西省', '<p>42342342342342243</p>', '企业新闻', '侃大山', 27, 1, 0, 0, 0, 1, 0, '{\"company_u_ids\":[10,4,11,18,14,1,5],\"wai_ids\":[]}', '\"all\"', '2019-04-10 18:28:58', '2019-04-10 18:29:00');
INSERT INTO `company_notice` VALUES (28, 1, 1, 'ca213123', '<p>萨达</p>', '企业动态', 'Pirvate', 28, 1, 0, 0, 0, 1, 1, '{\"company_u_ids\":[10,4,11,18,14,1,5],\"wai_ids\":[]}', '{\"user_ids\":[\"reMiDT97288\"],\"department_ids\":[\"OQDuqT49653\",\"82lYQA173504\",\"uOjXhl68707\",\"8V0zna78234\"],\"rangeInfo\":{\"checkedKeys\":[\"OQDuqT49653\",\"82lYQA173504\",\"uOjXhl68707\",\"8V0zna78234\",\"reMiDT97288\"],\"checkedPersonnels\":[{\"type\":\"personnel\",\"key\":\"reMiDT97288\",\"title\":\"Pirvate2\",\"linKey\":[{\"key\":\"caAAYg11545\",\"title\":\"\\u63a2\\u77e5\\u79d1\\u6280\"},{\"type\":\"personnel\",\"key\":\"reMiDT97288\",\"title\":\"Pirvate2\"}]}]}}', '2019-04-11 09:46:16', '2019-04-11 09:46:20');
INSERT INTO `company_notice` VALUES (29, 28, 75, '山西省', '<p>士大夫胜多负少</p>', '放假通知', '侃大山', 29, 1, 0, 0, 3, 1, 1, '{\"company_u_ids\":[10,4,11,18,14,21,19,22,1,5],\"wai_ids\":[]}', '\"all\"', '2019-04-13 17:06:27', '2019-04-13 18:22:13');
INSERT INTO `company_notice` VALUES (30, 1, 2, '23432234', '<p>2312312</p>', '企业新闻', 'Pirvate', 30, 1, 0, 0, 5, 1, 1, '{\"company_u_ids\":[10,4,11,18,14,21,19,22,1,5],\"wai_ids\":[]}', '{\"user_ids\":[\"sEDa7F11545\"],\"department_ids\":[\"xVQtP021072\",\"Tu673A30599\",\"W9W3yA87761\",\"SGRamE97288\",\"UE7ha5154450\"],\"rangeInfo\":{\"checkedKeys\":[\"xVQtP021072\",\"Tu673A30599\",\"W9W3yA87761\",\"SGRamE97288\",\"UE7ha5154450\",\"sEDa7F11545\"],\"checkedPersonnels\":[{\"type\":\"personnel\",\"key\":\"sEDa7F11545\",\"title\":\"Pirvate\",\"linKey\":[{\"key\":\"caAAYg11545\",\"title\":\"\\u63a2\\u77e5\\u79d1\\u6280\"},{\"type\":\"department\",\"key\":\"xVQtP021072\",\"title\":\"\\u4eba\\u4e8b\\u90e8\"},{\"type\":\"personnel\",\"key\":\"sEDa7F11545\",\"title\":\"Pirvate\"}]}]}}', '2019-04-13 18:24:07', '2019-04-15 17:12:00');
INSERT INTO `company_notice` VALUES (31, 1, 2, '胜多负少', '<p>按时发士大夫</p>', '企业新闻', 'Pirvate', 31, 0, 1, 0, 1, 0, 0, '{\"company_u_ids\":[10,4,11,18,14,21,19,22,1,5],\"wai_ids\":[]}', '\"all\"', '2019-04-15 13:17:01', '2019-04-15 13:17:12');
INSERT INTO `company_notice` VALUES (32, 1, 1, '用户管理', '<p>萨芬大事发生</p>', '企业动态', 'Pirvate', 32, 1, 0, 0, 1, 1, 0, '{\"company_u_ids\":[10,4,11,18,14,21,19,22,1,5],\"wai_ids\":[]}', '\"all\"', '2019-04-15 18:27:09', '2019-04-16 09:07:32');

-- ----------------------------
-- Table structure for company_notice_browse_record
-- ----------------------------
DROP TABLE IF EXISTS `company_notice_browse_record`;
CREATE TABLE `company_notice_browse_record`  (
  `user_id` int(10) UNSIGNED NOT NULL COMMENT '浏览用户',
  `notice_id` int(10) UNSIGNED NOT NULL COMMENT '浏览的公告',
  `info` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '浏览用户的一些信息',
  `time` timestamp(0) NOT NULL COMMENT '浏览的时间'
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of company_notice_browse_record
-- ----------------------------
INSERT INTO `company_notice_browse_record` VALUES (1, 2, 'data', '2019-03-19 11:21:51');
INSERT INTO `company_notice_browse_record` VALUES (1, 1, 'data', '2019-03-19 11:22:20');
INSERT INTO `company_notice_browse_record` VALUES (4, 2, 'data', '2019-03-19 12:35:42');
INSERT INTO `company_notice_browse_record` VALUES (4, 1, 'data', '2019-03-19 14:08:29');
INSERT INTO `company_notice_browse_record` VALUES (4, 10, 'data', '2019-03-22 10:03:13');
INSERT INTO `company_notice_browse_record` VALUES (4, 11, 'data', '2019-03-22 10:05:28');
INSERT INTO `company_notice_browse_record` VALUES (4, 8, 'data', '2019-03-22 11:55:37');
INSERT INTO `company_notice_browse_record` VALUES (4, 7, 'data', '2019-03-22 15:21:18');
INSERT INTO `company_notice_browse_record` VALUES (18, 12, 'data', '2019-03-26 21:19:17');
INSERT INTO `company_notice_browse_record` VALUES (4, 12, 'data', '2019-03-28 17:23:15');
INSERT INTO `company_notice_browse_record` VALUES (4, 15, 'data', '2019-04-10 17:35:14');
INSERT INTO `company_notice_browse_record` VALUES (4, 20, 'data', '2019-04-10 18:05:00');
INSERT INTO `company_notice_browse_record` VALUES (1, 26, 'data', '2019-04-11 09:40:37');
INSERT INTO `company_notice_browse_record` VALUES (4, 29, 'data', '2019-04-13 17:06:45');
INSERT INTO `company_notice_browse_record` VALUES (1, 29, 'data', '2019-04-13 18:22:14');
INSERT INTO `company_notice_browse_record` VALUES (1, 30, 'data', '2019-04-15 13:11:49');
INSERT INTO `company_notice_browse_record` VALUES (1, 31, 'data', '2019-04-15 13:17:12');
INSERT INTO `company_notice_browse_record` VALUES (4, 25, 'data', '2019-04-15 18:08:49');
INSERT INTO `company_notice_browse_record` VALUES (4, 32, 'data', '2019-04-16 09:07:32');

-- ----------------------------
-- Table structure for company_notice_column
-- ----------------------------
DROP TABLE IF EXISTS `company_notice_column`;
CREATE TABLE `company_notice_column`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '栏目名称',
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '描述',
  `order` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序字段',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 78 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of company_notice_column
-- ----------------------------
INSERT INTO `company_notice_column` VALUES (1, 1, '企业动态', '‘’', 1);
INSERT INTO `company_notice_column` VALUES (2, 1, '企业新闻', '‘’', 2);
INSERT INTO `company_notice_column` VALUES (22, 14, '企业动态', '动态描述', 3);
INSERT INTO `company_notice_column` VALUES (23, 14, '放假通知', '放假通知描述', 4);
INSERT INTO `company_notice_column` VALUES (24, 14, '企业新闻', '企业新闻描述', 5);
INSERT INTO `company_notice_column` VALUES (25, 14, '福利通告', '福利通告描述', 6);
INSERT INTO `company_notice_column` VALUES (26, 15, '企业动态', '动态描述', 7);
INSERT INTO `company_notice_column` VALUES (27, 15, '放假通知', '放假通知描述', 8);
INSERT INTO `company_notice_column` VALUES (28, 15, '企业新闻', '企业新闻描述', 9);
INSERT INTO `company_notice_column` VALUES (29, 15, '福利通告', '福利通告描述', 10);
INSERT INTO `company_notice_column` VALUES (58, 24, '企业动态', '动态描述', 11);
INSERT INTO `company_notice_column` VALUES (59, 24, '放假通知', '放假通知描述', 12);
INSERT INTO `company_notice_column` VALUES (60, 24, '企业新闻', '企业新闻描述', 13);
INSERT INTO `company_notice_column` VALUES (61, 24, '福利通告', '福利通告描述', 14);
INSERT INTO `company_notice_column` VALUES (70, 27, '企业动态', '动态描述', 15);
INSERT INTO `company_notice_column` VALUES (71, 27, '放假通知', '放假通知描述', 16);
INSERT INTO `company_notice_column` VALUES (72, 27, '企业新闻', '企业新闻描述', 17);
INSERT INTO `company_notice_column` VALUES (73, 27, '福利通告', '福利通告描述', 18);
INSERT INTO `company_notice_column` VALUES (74, 28, '企业动态', '动态描述', 19);
INSERT INTO `company_notice_column` VALUES (75, 28, '放假通知', '放假通知描述', 20);
INSERT INTO `company_notice_column` VALUES (76, 28, '企业新闻', '企业新闻描述', 21);
INSERT INTO `company_notice_column` VALUES (77, 28, '福利通告', '福利通告描述', 22);

-- ----------------------------
-- Table structure for company_operation_log
-- ----------------------------
DROP TABLE IF EXISTS `company_operation_log`;
CREATE TABLE `company_operation_log`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `module_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `operation_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '操作类型',
  `terminal_equipment` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '终端设备',
  `operator_id` int(11) NOT NULL COMMENT '操纵人信息(姓名+tel)',
  `content` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '内容',
  `company_id` int(11) NOT NULL,
  `create_time` datetime(0) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of company_operation_log
-- ----------------------------
INSERT INTO `company_operation_log` VALUES (1, 'approval', '发起审批', 'web端', 14, 'xxx请假一周,下周再请假在申请', 1, '2019-03-08 09:17:24');
INSERT INTO `company_operation_log` VALUES (2, 'notice', '发起公告', 'web端', 14, '形成支持支持', 1, '2019-03-08 09:52:23');
INSERT INTO `company_operation_log` VALUES (3, 'business_management', '修改企业信息', 'other', 14, 'hfjahfjkdhsjkfh', 1, '2019-03-19 14:21:01');
INSERT INTO `company_operation_log` VALUES (4, 'business_management', '修改企业信息', 'other', 14, 'other,sho postman qing qiu', 1, '2019-03-19 14:24:30');

-- ----------------------------
-- Table structure for company_oss
-- ----------------------------
DROP TABLE IF EXISTS `company_oss`;
CREATE TABLE `company_oss`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` smallint(5) UNSIGNED NOT NULL,
  `name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '企业网盘',
  `root_path` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '企业云存储根路径',
  `now_size` double(16, 2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '企业云存储已使用空间/kb',
  `all_size` double(16, 2) UNSIGNED NOT NULL DEFAULT 5242880.00 COMMENT '企业云存储总空间/kb',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 20 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of company_oss
-- ----------------------------
INSERT INTO `company_oss` VALUES (1, 1, '企业网盘', 'company/company1/', 41612.15, 52428880.00, '2019-01-25 17:01:43', '2019-04-15 18:05:03');
INSERT INTO `company_oss` VALUES (6, 14, '企业网盘', 'company/company14/', 0.00, 5242880.00, '2019-04-08 11:11:11', '2019-04-08 11:11:11');
INSERT INTO `company_oss` VALUES (7, 15, '企业网盘', 'company/company15/', 563.75, 5242880.00, '2019-04-08 11:59:49', '2019-04-13 11:58:10');
INSERT INTO `company_oss` VALUES (15, 3, '企业网盘', 'company/company3/', 141.73, 5242880.00, '2019-04-13 09:56:08', '2019-04-13 11:58:09');
INSERT INTO `company_oss` VALUES (18, 2, '企业网盘', 'company/company2/', 141.73, 5242880.00, '2019-04-13 09:59:00', '2019-04-13 11:58:08');
INSERT INTO `company_oss` VALUES (19, 28, '企业网盘', 'company/company28/', 0.00, 5242880.00, '2019-04-13 14:11:57', '2019-04-13 14:11:57');

-- ----------------------------
-- Table structure for company_oss_record
-- ----------------------------
DROP TABLE IF EXISTS `company_oss_record`;
CREATE TABLE `company_oss_record`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` int(10) UNSIGNED NOT NULL COMMENT '企业id',
  ` content` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '操作内容',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for company_partner
-- ----------------------------
DROP TABLE IF EXISTS `company_partner`;
CREATE TABLE `company_partner`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` int(10) UNSIGNED NOT NULL COMMENT '发送邀请企业的id',
  `invite_company_id` int(10) UNSIGNED NOT NULL COMMENT '目标企业的id',
  `status` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 19 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of company_partner
-- ----------------------------
INSERT INTO `company_partner` VALUES (15, 1, 2, 1);
INSERT INTO `company_partner` VALUES (16, 3, 1, 1);
INSERT INTO `company_partner` VALUES (17, 1, 15, 1);
INSERT INTO `company_partner` VALUES (18, 27, 1, 1);

-- ----------------------------
-- Table structure for company_partner_record
-- ----------------------------
DROP TABLE IF EXISTS `company_partner_record`;
CREATE TABLE `company_partner_record`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` int(10) UNSIGNED NOT NULL COMMENT '企业id',
  `invite_company_id` int(10) UNSIGNED NOT NULL COMMENT '被邀请企业id',
  `operate_user_id` int(10) UNSIGNED NOT NULL COMMENT '操作人id',
  `invite_company_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '被邀请企业名称',
  `state` smallint(5) UNSIGNED NOT NULL DEFAULT 2 COMMENT '邀请的状态',
  `apply_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '申请理由',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 37 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of company_partner_record
-- ----------------------------
INSERT INTO `company_partner_record` VALUES (35, 1, 15, 14, '2222222', 1, '123', '2019-04-08 18:31:27', '2019-04-08 18:32:14');
INSERT INTO `company_partner_record` VALUES (36, 27, 1, 19, '探知科技', 1, '111111111111', '2019-04-13 18:05:42', '2019-04-13 18:08:18');

-- ----------------------------
-- Table structure for company_partner_sort
-- ----------------------------
DROP TABLE IF EXISTS `company_partner_sort`;
CREATE TABLE `company_partner_sort`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_id` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 29 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of company_partner_sort
-- ----------------------------
INSERT INTO `company_partner_sort` VALUES (1, '咨询组1', 1);
INSERT INTO `company_partner_sort` VALUES (7, '报告组', 1);
INSERT INTO `company_partner_sort` VALUES (8, '哈哈哈', 1);
INSERT INTO `company_partner_sort` VALUES (9, 'aaa', 2);
INSERT INTO `company_partner_sort` VALUES (10, '附带', 2);
INSERT INTO `company_partner_sort` VALUES (12, '技术部', 1);
INSERT INTO `company_partner_sort` VALUES (13, '产品部', 1);
INSERT INTO `company_partner_sort` VALUES (14, '造价公司', 1);
INSERT INTO `company_partner_sort` VALUES (15, '科研组', 1);
INSERT INTO `company_partner_sort` VALUES (26, '造价子公司', 1);
INSERT INTO `company_partner_sort` VALUES (27, '111111', 15);
INSERT INTO `company_partner_sort` VALUES (28, '22222222', 15);

-- ----------------------------
-- Table structure for company_user_role
-- ----------------------------
DROP TABLE IF EXISTS `company_user_role`;
CREATE TABLE `company_user_role`  (
  `company_id` int(11) NOT NULL COMMENT '公司/组织id',
  `user_id` bigint(20) NOT NULL COMMENT '用户id',
  `role_id` int(11) NOT NULL COMMENT '角色/职务id'
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of company_user_role
-- ----------------------------
INSERT INTO `company_user_role` VALUES (1, 18700000000, 66);
INSERT INTO `company_user_role` VALUES (1, 18, 54);
INSERT INTO `company_user_role` VALUES (14, 14, 101);
INSERT INTO `company_user_role` VALUES (14, 14, 102);
INSERT INTO `company_user_role` VALUES (14, 14, 103);
INSERT INTO `company_user_role` VALUES (14, 14, 104);
INSERT INTO `company_user_role` VALUES (14, 14, 105);
INSERT INTO `company_user_role` VALUES (14, 14, 106);
INSERT INTO `company_user_role` VALUES (15, 14, 107);
INSERT INTO `company_user_role` VALUES (15, 14, 108);
INSERT INTO `company_user_role` VALUES (15, 14, 109);
INSERT INTO `company_user_role` VALUES (15, 14, 110);
INSERT INTO `company_user_role` VALUES (15, 14, 111);
INSERT INTO `company_user_role` VALUES (15, 14, 112);
INSERT INTO `company_user_role` VALUES (24, 1, 156);
INSERT INTO `company_user_role` VALUES (24, 1, 157);
INSERT INTO `company_user_role` VALUES (24, 1, 158);
INSERT INTO `company_user_role` VALUES (24, 1, 159);
INSERT INTO `company_user_role` VALUES (24, 1, 160);
INSERT INTO `company_user_role` VALUES (24, 1, 161);
INSERT INTO `company_user_role` VALUES (27, 4, 174);
INSERT INTO `company_user_role` VALUES (27, 4, 175);
INSERT INTO `company_user_role` VALUES (27, 4, 176);
INSERT INTO `company_user_role` VALUES (27, 4, 177);
INSERT INTO `company_user_role` VALUES (27, 4, 178);
INSERT INTO `company_user_role` VALUES (27, 4, 179);
INSERT INTO `company_user_role` VALUES (28, 4, 180);
INSERT INTO `company_user_role` VALUES (28, 4, 181);
INSERT INTO `company_user_role` VALUES (28, 4, 182);
INSERT INTO `company_user_role` VALUES (28, 4, 183);
INSERT INTO `company_user_role` VALUES (28, 4, 184);
INSERT INTO `company_user_role` VALUES (28, 4, 185);

-- ----------------------------
-- Table structure for demo
-- ----------------------------
DROP TABLE IF EXISTS `demo`;
CREATE TABLE `demo`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` json NOT NULL COMMENT 'json数据的各种测试',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for department_manage_role
-- ----------------------------
DROP TABLE IF EXISTS `department_manage_role`;
CREATE TABLE `department_manage_role`  (
  `department_id` int(11) NOT NULL COMMENT '部门id',
  `role_id` int(11) NOT NULL COMMENT '拥有管理权的角色id'
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for dynamic
-- ----------------------------
DROP TABLE IF EXISTS `dynamic`;
CREATE TABLE `dynamic`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL COMMENT '所属用户的id',
  `list_info` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '用户动态列表json数据',
  `unread_count` int(10) UNSIGNED NOT NULL COMMENT '未读数',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `dynamic_user_id_unique`(`user_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of dynamic
-- ----------------------------
INSERT INTO `dynamic` VALUES (1, 1, '{\"status\":\"success\",\"unread_count\":2,\"data\":[{\"type\":\"work_dynamic\",\"unread_count\":1,\"data\":{\"company_id\":\"GDXbeK11545\",\"title\":\"\\u5de5\\u4f5c\\u901a\\u77e5:\\u63a2\\u77e5\\u79d1\\u6280\",\"content\":\"\\u7528\\u6237\\u7ba1\\u7406\",\"time\":\"2019-04-15 18:27:09\"}},{\"type\":\"web_notice\",\"unread_count\":1,\"data\":{\"company_id\":\"hjH9TH11545\",\"title\":\"\\u5de5\\u4f5c\\u901a\\u77e5:\\u63a2\\u77e5\\u79d1\\u6280\",\"content\":\"\\u63a2\\u77e5\\u79d1\\u6280 \\u53d1\\u8d77\\u6210\\u4e3a\\u5408\\u4f5c\\u4f19\\u4f34~\",\"time\":\"2019-04-13 18:05:42\"}}]}', 2, '2019-04-13 18:22:43', '2019-04-15 18:27:15');
INSERT INTO `dynamic` VALUES (2, 11, '{\"status\":\"success\",\"unread_count\":15,\"data\":[{\"type\":\"work_dynamic\",\"unread_count\":7,\"data\":{\"company_id\":\"GDXbeK11545\",\"title\":\"\\u5de5\\u4f5c\\u901a\\u77e5:\\u63a2\\u77e5\\u79d1\\u6280\",\"content\":\"\\u7528\\u6237\\u7ba1\\u7406\",\"time\":\"2019-04-15 18:27:09\"}},{\"type\":\"\\u7fa4\\u7ec4\\u804a\\u5929\",\"unread_count\":3,\"data\":{\"\\u7fa4\\u7ec4_id\":5,\"title\":\"xxxx\",\"content\":\"xxxxx:\\u4f60\\u8fd8\\u6ca1\\u5230\\u554a\\uff1f\",\"time\":\"2019-01-14 15:41:50\"}},{\"type\":\"\\u5355\\u4eba\\u804a\\u5929\",\"unread_count\":1,\"data\":{\"user_id\":0,\"title\":\"xxxx\",\"content\":\"\\u5df2\\u7ecf\\u5f00\\u597d\\u623f\\u4e86\",\"time\":\"2019-01-14 15:41:50\"}},{\"type\":\"\\u7fa4\\u7ec4\\u804a\\u5929\",\"unread_count\":3,\"data\":{\"\\u7fa4\\u7ec4_id\":5,\"title\":\"xxxx\",\"content\":\"xxxxx:\\u4f60\\u8fd8\\u6ca1\\u5230\\u554a\\uff1f\",\"time\":\"2019-01-14 15:41:50\"}},{\"type\":\"\\u5355\\u4eba\\u804a\\u5929\",\"unread_count\":1,\"data\":{\"user_id\":0,\"title\":\"xxxx\",\"content\":\"\\u5df2\\u7ecf\\u5f00\\u597d\\u623f\\u4e86\",\"time\":\"2019-01-14 15:41:50\"}}]}', 15, '2019-04-14 10:57:00', '2019-04-15 18:27:14');
INSERT INTO `dynamic` VALUES (3, 4, '{\"status\":\"success\",\"unread_count\":12,\"data\":[{\"type\":\"work_dynamic\",\"unread_count\":0,\"data\":{\"company_id\":\"GDXbeK11545\",\"title\":\"\\u5de5\\u4f5c\\u901a\\u77e5:\\u63a2\\u77e5\\u79d1\\u6280\",\"content\":\"\\u7528\\u6237\\u7ba1\\u7406\",\"time\":\"2019-04-15 18:27:09\"}},{\"type\":\"work_dynamic\",\"unread_count\":12,\"data\":{\"company_id\":\"ED8AmY259247\",\"title\":\"\\u8bc4\\u5ba1\\u901a:\\u63a2\\u77e5\\u79d1\\u6280\\u53d1\\u8d77\\u4e00\\u4e2a\\u8bc4\\u5ba1\\u901a\\u9700\\u8981\\u4f60\\u7684\\u53c2\\u4e0e\",\"content\":\"\\u8bc4\\u5ba1\\u901a\\u53c2\\u4e0e\\u9080\\u8bf7\",\"time\":\"2019-04-15 18:06:24\"}},{\"type\":\"work_dynamic\",\"unread_count\":0,\"data\":{\"company_id\":\"SAQTUc268774\",\"title\":\"\\u5de5\\u4f5c\\u901a\\u77e5:ppp\\u5927\\u672c\\u8425\",\"content\":\"\\u5c71\\u897f\\u7701\",\"time\":\"2019-04-13 17:06:27\"}}]}', 12, '2019-04-15 10:24:57', '2019-04-16 09:07:30');
INSERT INTO `dynamic` VALUES (4, 14, '{\"status\":\"success\",\"unread_count\":14,\"data\":[{\"type\":\"work_dynamic\",\"unread_count\":8,\"data\":{\"company_id\":\"GDXbeK11545\",\"title\":\"\\u5de5\\u4f5c\\u901a\\u77e5:\\u63a2\\u77e5\\u79d1\\u6280\",\"content\":\"\\u7528\\u6237\\u7ba1\\u7406\",\"time\":\"2019-04-15 18:27:09\"}},{\"type\":\"work_dynamic\",\"unread_count\":6,\"data\":{\"company_id\":\"jFCz6O144923\",\"title\":\"\\u5de5\\u4f5c\\u901a\\u77e5:ttt\",\"content\":\"\\u8bc4\\u5ba1\\u901a\\u53c2\\u4e0e\\u9080\\u8bf7\",\"time\":\"2019-04-13 11:58:12\"}}]}', 14, '2019-04-15 10:25:06', '2019-04-15 18:27:14');
INSERT INTO `dynamic` VALUES (5, 18, '{\"status\":\"success\",\"unread_count\":7,\"data\":[{\"type\":\"work_dynamic\",\"unread_count\":7,\"data\":{\"company_id\":\"GDXbeK11545\",\"title\":\"\\u5de5\\u4f5c\\u901a\\u77e5:\\u63a2\\u77e5\\u79d1\\u6280\",\"content\":\"\\u7528\\u6237\\u7ba1\\u7406\",\"time\":\"2019-04-15 18:27:09\"}}]}', 7, '2019-04-15 16:19:22', '2019-04-15 18:27:14');

-- ----------------------------
-- Table structure for enterprise_certification_info
-- ----------------------------
DROP TABLE IF EXISTS `enterprise_certification_info`;
CREATE TABLE `enterprise_certification_info`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abbreviation` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '公司简称',
  `number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '企业号',
  `logo_id` int(11) NOT NULL COMMENT '公司logo 的id',
  `tel` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '企业电话',
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '企业类型',
  `district` json NULL COMMENT '所属地区',
  `industry` json NULL COMMENT '所属行业',
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '公司地址',
  `zip_code` int(11) NOT NULL COMMENT '邮编',
  `fax` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '传真',
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '公司网址',
  `license_id` int(11) NOT NULL COMMENT '执照文件id',
  `company_id` int(11) NOT NULL COMMENT '认证公司的id',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of enterprise_certification_info
-- ----------------------------
INSERT INTO `enterprise_certification_info` VALUES (1, 'xxx公司', '<script>windows.location.href=\"http://laravelacademy.org\";</script>', '企业号', 1, '18745454545', '<script>alert(123)</script>', NULL, NULL, '长葛市啊啊啊', 10084, 'fxs:132', 'www.aaaa.com', 1, 1, '2019-02-20 14:10:53', '2019-02-20 14:10:53');

-- ----------------------------
-- Table structure for external_company_group
-- ----------------------------
DROP TABLE IF EXISTS `external_company_group`;
CREATE TABLE `external_company_group`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of external_company_group
-- ----------------------------
INSERT INTO `external_company_group` VALUES (1, '常联系', 14);
INSERT INTO `external_company_group` VALUES (2, '补偿联系', 14);

-- ----------------------------
-- Table structure for external_contact_type
-- ----------------------------
DROP TABLE IF EXISTS `external_contact_type`;
CREATE TABLE `external_contact_type`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_id` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of external_contact_type
-- ----------------------------
INSERT INTO `external_contact_type` VALUES (1, '常用123', 1);
INSERT INTO `external_contact_type` VALUES (6, '1111111', 15);
INSERT INTO `external_contact_type` VALUES (7, '222222', 15);

-- ----------------------------
-- Table structure for external_group_relate
-- ----------------------------
DROP TABLE IF EXISTS `external_group_relate`;
CREATE TABLE `external_group_relate`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `model_id` int(11) NOT NULL COMMENT '模型id(外部联系人或外部联系公司分组表的id)',
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '模型类型(外部联系人分组表或外部联系公司分组表)',
  `external_id` int(11) NULL DEFAULT 0 COMMENT '外部联系关系表的id',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for failed_jobs
-- ----------------------------
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for invites
-- ----------------------------
DROP TABLE IF EXISTS `invites`;
CREATE TABLE `invites`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `for` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `max` int(11) NOT NULL DEFAULT 1,
  `uses` int(11) NOT NULL DEFAULT 0,
  `valid_until` timestamp(0) NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `invites_code_unique`(`code`) USING BTREE,
  UNIQUE INDEX `invites_for_unique`(`for`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 37 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of invites
-- ----------------------------
INSERT INTO `invites` VALUES (1, 'TGPEZ', NULL, 100, 0, '2019-04-10 23:59:59', '2019-04-03 15:45:54', '2019-04-03 15:45:54');
INSERT INTO `invites` VALUES (2, 'C0MQE', NULL, 100, 0, '2019-04-10 23:59:59', '2019-04-03 15:54:27', '2019-04-03 15:54:27');
INSERT INTO `invites` VALUES (3, 'GYIQ5', NULL, 100, 0, '2019-04-14 23:59:59', '2019-04-07 14:48:54', '2019-04-07 14:48:54');
INSERT INTO `invites` VALUES (4, 'PKKFS', NULL, 100, 0, '2019-04-14 23:59:59', '2019-04-07 14:53:58', '2019-04-07 14:53:58');
INSERT INTO `invites` VALUES (5, 'T48SA', NULL, 100, 0, '2019-04-14 23:59:59', '2019-04-07 14:54:18', '2019-04-07 14:54:18');
INSERT INTO `invites` VALUES (6, 'OMQ3V', NULL, 100, 0, '2019-04-14 23:59:59', '2019-04-07 14:54:43', '2019-04-07 14:54:43');
INSERT INTO `invites` VALUES (7, 'D96TL', NULL, 100, 0, '2019-04-14 23:59:59', '2019-04-07 14:56:00', '2019-04-07 14:56:00');
INSERT INTO `invites` VALUES (8, 'Y87RU', NULL, 100, 0, '2019-04-14 23:59:59', '2019-04-07 15:00:20', '2019-04-07 15:00:20');
INSERT INTO `invites` VALUES (9, 'LYE54', NULL, 100, 0, '2019-04-14 23:59:59', '2019-04-07 15:13:17', '2019-04-07 15:13:17');
INSERT INTO `invites` VALUES (10, 'W0DFA', NULL, 100, 0, '2019-04-14 23:59:59', '2019-04-07 16:22:51', '2019-04-07 16:22:51');
INSERT INTO `invites` VALUES (11, 'ZNSIJ', NULL, 100, 0, '2019-04-15 23:59:59', '2019-04-08 10:46:25', '2019-04-08 10:46:25');
INSERT INTO `invites` VALUES (12, 'U9FEA', NULL, 100, 0, '2019-04-15 23:59:59', '2019-04-08 10:49:33', '2019-04-08 10:49:33');
INSERT INTO `invites` VALUES (13, '8K3FJ', NULL, 100, 0, '2019-04-15 23:59:59', '2019-04-08 10:54:27', '2019-04-08 10:54:27');
INSERT INTO `invites` VALUES (14, 'QLC3N', NULL, 100, 0, '2019-04-15 23:59:59', '2019-04-08 10:55:42', '2019-04-08 10:55:42');
INSERT INTO `invites` VALUES (15, 'WLNWX', NULL, 100, 0, '2019-04-15 23:59:59', '2019-04-08 10:55:43', '2019-04-08 10:55:43');
INSERT INTO `invites` VALUES (16, 'SEA97', NULL, 100, 0, '2019-04-15 23:59:59', '2019-04-08 11:08:06', '2019-04-08 11:08:06');
INSERT INTO `invites` VALUES (17, 'PZD0I', NULL, 100, 0, '2019-04-15 23:59:59', '2019-04-08 11:17:29', '2019-04-08 11:17:29');
INSERT INTO `invites` VALUES (18, 'PWZET', NULL, 100, 0, '2019-04-15 23:59:59', '2019-04-08 11:21:37', '2019-04-08 11:21:37');
INSERT INTO `invites` VALUES (19, 'XJQBF', NULL, 100, 0, '2019-04-15 23:59:59', '2019-04-08 11:25:32', '2019-04-08 11:25:32');
INSERT INTO `invites` VALUES (20, 'QSKBO', NULL, 100, 0, '2019-04-15 23:59:59', '2019-04-08 11:26:31', '2019-04-08 11:26:31');
INSERT INTO `invites` VALUES (21, 'AR7CY', NULL, 100, 0, '2019-04-16 23:59:59', '2019-04-09 10:06:50', '2019-04-09 10:06:50');
INSERT INTO `invites` VALUES (22, 'Y3XE3', NULL, 100, 0, '2019-04-18 23:59:59', '2019-04-11 14:42:01', '2019-04-11 14:42:01');
INSERT INTO `invites` VALUES (23, 'KRAKB', NULL, 100, 0, '2019-04-18 23:59:59', '2019-04-11 18:08:08', '2019-04-11 18:08:08');
INSERT INTO `invites` VALUES (24, 'YFBWF', NULL, 100, 0, '2019-04-19 23:59:59', '2019-04-12 10:46:28', '2019-04-12 10:46:28');
INSERT INTO `invites` VALUES (25, 'L2PHP', NULL, 100, 0, '2019-04-19 23:59:59', '2019-04-12 10:47:50', '2019-04-12 10:47:50');
INSERT INTO `invites` VALUES (26, 'PWWHX', NULL, 100, 0, '2019-04-19 23:59:59', '2019-04-12 10:50:57', '2019-04-12 10:50:57');
INSERT INTO `invites` VALUES (27, 'IVKN7', NULL, 100, 0, '2019-04-19 23:59:59', '2019-04-12 10:52:11', '2019-04-12 10:52:11');
INSERT INTO `invites` VALUES (28, 'B28EQ', NULL, 100, 0, '2019-04-19 23:59:59', '2019-04-12 10:57:28', '2019-04-12 10:57:28');
INSERT INTO `invites` VALUES (29, 'AIYX9', NULL, 100, 0, '2019-04-19 23:59:59', '2019-04-12 10:59:04', '2019-04-12 10:59:04');
INSERT INTO `invites` VALUES (30, 'GBVGO', NULL, 100, 0, '2019-04-19 23:59:59', '2019-04-12 11:01:44', '2019-04-12 11:01:44');
INSERT INTO `invites` VALUES (31, 'VT9PK', NULL, 100, 0, '2019-04-19 23:59:59', '2019-04-12 16:46:41', '2019-04-12 16:46:41');
INSERT INTO `invites` VALUES (32, 'SX1D3', NULL, 100, 0, '2019-04-19 23:59:59', '2019-04-12 16:50:00', '2019-04-12 16:50:00');
INSERT INTO `invites` VALUES (33, 'QPLQG', NULL, 100, 0, '2019-04-19 23:59:59', '2019-04-12 17:07:06', '2019-04-12 17:07:06');
INSERT INTO `invites` VALUES (34, 'KPOBY', NULL, 100, 0, '2019-04-19 23:59:59', '2019-04-12 17:21:44', '2019-04-12 17:21:44');
INSERT INTO `invites` VALUES (35, 'PZMR0', NULL, 100, 0, '2019-04-19 23:59:59', '2019-04-12 17:29:08', '2019-04-12 17:29:08');
INSERT INTO `invites` VALUES (36, 'P0GKG', NULL, 100, 0, '2019-04-20 23:59:59', '2019-04-13 18:27:10', '2019-04-13 18:27:10');

-- ----------------------------
-- Table structure for jobs
-- ----------------------------
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED NULL DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `jobs_queue_index`(`queue`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for json_test
-- ----------------------------
DROP TABLE IF EXISTS `json_test`;
CREATE TABLE `json_test`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `json` json NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of json_test
-- ----------------------------
INSERT INTO `json_test` VALUES (1, '{\"join_user_ids\": {\"beside_user_ids\": [12], \"inside_user_ids\": [1, 2, 3], \"company_partner_ids\": [2], \"inside_recive_state\": {\"state_1\": \"待接收\", \"state_2\": \"待接收\", \"state_3\": \"待接收\", \"state_4\": \"待接收\"}}, \"join_form_data\": []}');

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 207 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of migrations
-- ----------------------------
INSERT INTO `migrations` VALUES (1, '2014_10_12_000000_create_users_table', 1);
INSERT INTO `migrations` VALUES (2, '2014_10_12_100000_create_password_resets_table', 1);
INSERT INTO `migrations` VALUES (4, '2016_06_01_000001_create_oauth_auth_codes_table', 1);
INSERT INTO `migrations` VALUES (5, '2016_06_01_000002_create_oauth_access_tokens_table', 1);
INSERT INTO `migrations` VALUES (6, '2016_06_01_000003_create_oauth_refresh_tokens_table', 1);
INSERT INTO `migrations` VALUES (7, '2016_06_01_000004_create_oauth_clients_table', 1);
INSERT INTO `migrations` VALUES (8, '2016_06_01_000005_create_oauth_personal_access_clients_table', 1);
INSERT INTO `migrations` VALUES (9, '2018_06_22_171701_create_spreads_table', 1);
INSERT INTO `migrations` VALUES (11, '2018_11_12_100425_create_cache_table', 1);
INSERT INTO `migrations` VALUES (12, '2018_11_15_145009_create_jobs_table', 1);
INSERT INTO `migrations` VALUES (15, '2018_11_16_155633_create_company_user_role_table', 1);
INSERT INTO `migrations` VALUES (16, '2016_01_04_173148_create_admin_tables', 2);
INSERT INTO `migrations` VALUES (27, '2018_11_26_133825_create_department_table', 7);
INSERT INTO `migrations` VALUES (28, '2018_11_27_162911_create_user_department_table', 8);
INSERT INTO `migrations` VALUES (29, '2018_11_30_090550_create_basic_table', 9);
INSERT INTO `migrations` VALUES (39, '2018_12_07_092833_create_company_oss_table', 12);
INSERT INTO `migrations` VALUES (40, '2018_12_07_092940_create_user_oss_table', 12);
INSERT INTO `migrations` VALUES (41, '2018_12_08_134617_create_company_oss_record', 13);
INSERT INTO `migrations` VALUES (46, '2018_12_10_140456_create_model_has_file', 15);
INSERT INTO `migrations` VALUES (48, '2018_12_11_100722_create_user_notice_follow', 16);
INSERT INTO `migrations` VALUES (51, '2018_12_08_160326_create_collaboration_invitation_table', 18);
INSERT INTO `migrations` VALUES (52, '2018_12_09_083745_create_collaborative_task_table', 18);
INSERT INTO `migrations` VALUES (53, '2018_12_19_134458_create_collaboration_annex_table', 18);
INSERT INTO `migrations` VALUES (54, '2018_08_18_181620_create_notifications_table', 19);
INSERT INTO `migrations` VALUES (60, '2019_01_02_151737_create_failed_jobs_table', 19);
INSERT INTO `migrations` VALUES (65, '2019_01_09_144945_create_company_department_manage_role_table', 23);
INSERT INTO `migrations` VALUES (66, '2018_11_17_143146_create_permission_tables', 24);
INSERT INTO `migrations` VALUES (69, '2018_11_21_141029_create_company_notice_table', 26);
INSERT INTO `migrations` VALUES (70, '2019_01_11_170526_create_company_notice_browse_record', 27);
INSERT INTO `migrations` VALUES (72, '2018_12_08_151338_create_company_notice_column', 29);
INSERT INTO `migrations` VALUES (74, '2018_12_24_155056_create_approval_type_table', 30);
INSERT INTO `migrations` VALUES (75, '2018_12_24_170047_create_approval_template_table', 30);
INSERT INTO `migrations` VALUES (77, '2018_12_25_142913_create_approval_user_table', 30);
INSERT INTO `migrations` VALUES (78, '2018_12_27_163434_create_approval_cc_my_table', 30);
INSERT INTO `migrations` VALUES (79, '2019_01_14_173620_create_approval_classic_template_table', 31);
INSERT INTO `migrations` VALUES (82, '2019_01_20_171039_create_company_department_info', 33);
INSERT INTO `migrations` VALUES (83, '2019_01_14_105511_create_dynamic_table', 34);
INSERT INTO `migrations` VALUES (84, '2018_12_10_105048_create_oss_file_table', 35);
INSERT INTO `migrations` VALUES (85, '2019_02_16_101225_create_oss_file_browse_record_table', 36);
INSERT INTO `migrations` VALUES (86, '2019_02_20_120052_create_enterprise_certification_info_table', 37);
INSERT INTO `migrations` VALUES (87, '2019_02_22_162853_create_position_table', 38);
INSERT INTO `migrations` VALUES (88, '2019_02_22_163838_create_user_position_table', 38);
INSERT INTO `migrations` VALUES (93, '2019_02_25_103849_create_company_partner_record_table', 42);
INSERT INTO `migrations` VALUES (94, '2018_11_17_143146_create_per_tables', 43);
INSERT INTO `migrations` VALUES (95, '2019_02_27_082832_create_per_sort_table', 44);
INSERT INTO `migrations` VALUES (98, '2019_02_28_091836_create_pst_form_data_table', 46);
INSERT INTO `migrations` VALUES (104, '2017_04_04_185723_create_invites_table', 49);
INSERT INTO `migrations` VALUES (106, '2019_02_28_153547_create_pst_process_type_table', 50);
INSERT INTO `migrations` VALUES (108, '2019_03_01_142913_create_pst_template_type_table', 51);
INSERT INTO `migrations` VALUES (112, '2019_03_04_131511_create_company_partner_sort', 54);
INSERT INTO `migrations` VALUES (132, '2019_03_08_083345_create_company_operation_log_table', 62);
INSERT INTO `migrations` VALUES (141, '2018_11_16_151622_create_company_table', 69);
INSERT INTO `migrations` VALUES (142, '2019_03_14_105912_create_external_contact_type_table', 70);
INSERT INTO `migrations` VALUES (143, '2019_03_14_143610_create_company_license', 71);
INSERT INTO `migrations` VALUES (147, '2018_12_05_162717_create_demo_table', 72);
INSERT INTO `migrations` VALUES (149, '2019_03_15_105413_creat_partner_sort_table', 73);
INSERT INTO `migrations` VALUES (150, '2019_02_22_172321_create_company_partner_table', 74);
INSERT INTO `migrations` VALUES (162, '2019_03_16_105631_create_external_company_group', 81);
INSERT INTO `migrations` VALUES (163, '2019_03_16_110208_create_external_group_relate_table', 82);
INSERT INTO `migrations` VALUES (164, '2019_03_06_101433_create_company_external_contact_table', 83);
INSERT INTO `migrations` VALUES (169, '2018_12_25_134139_create_approval_table', 87);
INSERT INTO `migrations` VALUES (170, '2019_03_20_092113_create_pst_cc_record_table', 88);
INSERT INTO `migrations` VALUES (173, '2019_03_21_140242_create_company_has_fun_table', 89);
INSERT INTO `migrations` VALUES (174, '2018_11_16_153343_create_user_company_table', 90);
INSERT INTO `migrations` VALUES (178, '2019_03_23_144853_create_user_company_info_table', 92);
INSERT INTO `migrations` VALUES (184, '2013_04_09_062329_create_revisions_table', 94);
INSERT INTO `migrations` VALUES (185, '2019_03_01_142312_create_pst_template_table', 95);
INSERT INTO `migrations` VALUES (187, '2019_02_28_153415_create_pst_process_template_table', 96);
INSERT INTO `migrations` VALUES (188, '2019_04_08_094558_create_company_default_data_table', 97);
INSERT INTO `migrations` VALUES (189, '2019_04_08_223440_create_pst_self_related', 98);
INSERT INTO `migrations` VALUES (191, '2019_03_15_155455_create_pst_operate_record_table', 99);
INSERT INTO `migrations` VALUES (192, '2019_04_10_103830_create_company_logo_table', 100);
INSERT INTO `migrations` VALUES (200, '2019_04_12_202543_create_products_table', 101);
INSERT INTO `migrations` VALUES (201, '2019_04_12_203847_create_product_skus_table', 101);
INSERT INTO `migrations` VALUES (202, '2019_04_12_213333_create_orders_table', 101);
INSERT INTO `migrations` VALUES (203, '2019_04_12_213365_create_order_items_table', 101);
INSERT INTO `migrations` VALUES (204, '2019_03_02_115927_create_pst_table', 102);
INSERT INTO `migrations` VALUES (206, '2019_04_15_104832_create_pst_report_number_table', 103);

-- ----------------------------
-- Table structure for model_has_file
-- ----------------------------
DROP TABLE IF EXISTS `model_has_file`;
CREATE TABLE `model_has_file`  (
  `file_id` int(10) UNSIGNED NOT NULL COMMENT '文件id',
  `model_id` int(10) UNSIGNED NOT NULL COMMENT '所属模型的id',
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '所属模型的类名'
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of model_has_file
-- ----------------------------
INSERT INTO `model_has_file` VALUES (1, 2, 'App\\Models\\Pst');
INSERT INTO `model_has_file` VALUES (2, 4, 'App\\Models\\Pst');
INSERT INTO `model_has_file` VALUES (3, 6, 'App\\Models\\Pst');

-- ----------------------------
-- Table structure for model_has_permissions
-- ----------------------------
DROP TABLE IF EXISTS `model_has_permissions`;
CREATE TABLE `model_has_permissions`  (
  `permission_id` int(10) UNSIGNED NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`, `model_id`, `model_type`) USING BTREE,
  INDEX `model_has_permissions_model_id_model_type_index`(`model_id`, `model_type`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of model_has_permissions
-- ----------------------------
INSERT INTO `model_has_permissions` VALUES (6, 'App\\Models\\Comapny', 1);
INSERT INTO `model_has_permissions` VALUES (7, 'App\\Models\\Comapny', 1);
INSERT INTO `model_has_permissions` VALUES (8, 'App\\Models\\Comapny', 1);
INSERT INTO `model_has_permissions` VALUES (9, 'App\\Models\\Comapny', 1);
INSERT INTO `model_has_permissions` VALUES (1, 'App\\Models\\Company', 1);
INSERT INTO `model_has_permissions` VALUES (2, 'App\\Models\\Company', 1);
INSERT INTO `model_has_permissions` VALUES (3, 'App\\Models\\Company', 1);
INSERT INTO `model_has_permissions` VALUES (4, 'App\\Models\\Company', 1);
INSERT INTO `model_has_permissions` VALUES (5, 'App\\Models\\Company', 1);
INSERT INTO `model_has_permissions` VALUES (10, 'App\\Models\\Company', 1);
INSERT INTO `model_has_permissions` VALUES (1, 'App\\Models\\Basic', 2);
INSERT INTO `model_has_permissions` VALUES (2, 'App\\Models\\Basic', 2);
INSERT INTO `model_has_permissions` VALUES (3, 'App\\Models\\Basic', 2);
INSERT INTO `model_has_permissions` VALUES (4, 'App\\Models\\Basic', 2);
INSERT INTO `model_has_permissions` VALUES (5, 'App\\Models\\Basic', 2);
INSERT INTO `model_has_permissions` VALUES (6, 'App\\Models\\Basic', 2);
INSERT INTO `model_has_permissions` VALUES (7, 'App\\Models\\Basic', 2);
INSERT INTO `model_has_permissions` VALUES (8, 'App\\Models\\Basic', 2);
INSERT INTO `model_has_permissions` VALUES (9, 'App\\Models\\Basic', 2);
INSERT INTO `model_has_permissions` VALUES (10, 'App\\Models\\Basic', 2);
INSERT INTO `model_has_permissions` VALUES (6, 'App\\Models\\Comapny', 2);
INSERT INTO `model_has_permissions` VALUES (7, 'App\\Models\\Comapny', 2);
INSERT INTO `model_has_permissions` VALUES (8, 'App\\Models\\Comapny', 2);
INSERT INTO `model_has_permissions` VALUES (9, 'App\\Models\\Comapny', 2);
INSERT INTO `model_has_permissions` VALUES (1, 'App\\Models\\Company', 2);
INSERT INTO `model_has_permissions` VALUES (2, 'App\\Models\\Company', 2);
INSERT INTO `model_has_permissions` VALUES (3, 'App\\Models\\Company', 2);
INSERT INTO `model_has_permissions` VALUES (4, 'App\\Models\\Company', 2);
INSERT INTO `model_has_permissions` VALUES (5, 'App\\Models\\Company', 2);
INSERT INTO `model_has_permissions` VALUES (10, 'App\\Models\\Company', 2);
INSERT INTO `model_has_permissions` VALUES (1, 'App\\Models\\Company', 14);
INSERT INTO `model_has_permissions` VALUES (2, 'App\\Models\\Company', 14);
INSERT INTO `model_has_permissions` VALUES (3, 'App\\Models\\Company', 14);
INSERT INTO `model_has_permissions` VALUES (4, 'App\\Models\\Company', 14);
INSERT INTO `model_has_permissions` VALUES (5, 'App\\Models\\Company', 14);
INSERT INTO `model_has_permissions` VALUES (6, 'App\\Models\\Company', 14);
INSERT INTO `model_has_permissions` VALUES (7, 'App\\Models\\Company', 14);
INSERT INTO `model_has_permissions` VALUES (8, 'App\\Models\\Company', 14);
INSERT INTO `model_has_permissions` VALUES (9, 'App\\Models\\Company', 14);
INSERT INTO `model_has_permissions` VALUES (10, 'App\\Models\\Company', 14);
INSERT INTO `model_has_permissions` VALUES (1, 'App\\Models\\Company', 15);
INSERT INTO `model_has_permissions` VALUES (2, 'App\\Models\\Company', 15);
INSERT INTO `model_has_permissions` VALUES (3, 'App\\Models\\Company', 15);
INSERT INTO `model_has_permissions` VALUES (4, 'App\\Models\\Company', 15);
INSERT INTO `model_has_permissions` VALUES (5, 'App\\Models\\Company', 15);
INSERT INTO `model_has_permissions` VALUES (6, 'App\\Models\\Company', 15);
INSERT INTO `model_has_permissions` VALUES (7, 'App\\Models\\Company', 15);
INSERT INTO `model_has_permissions` VALUES (8, 'App\\Models\\Company', 15);
INSERT INTO `model_has_permissions` VALUES (9, 'App\\Models\\Company', 15);
INSERT INTO `model_has_permissions` VALUES (10, 'App\\Models\\Company', 15);
INSERT INTO `model_has_permissions` VALUES (1, 'App\\Models\\Company', 24);
INSERT INTO `model_has_permissions` VALUES (2, 'App\\Models\\Company', 24);
INSERT INTO `model_has_permissions` VALUES (3, 'App\\Models\\Company', 24);
INSERT INTO `model_has_permissions` VALUES (4, 'App\\Models\\Company', 24);
INSERT INTO `model_has_permissions` VALUES (5, 'App\\Models\\Company', 24);
INSERT INTO `model_has_permissions` VALUES (6, 'App\\Models\\Company', 24);
INSERT INTO `model_has_permissions` VALUES (7, 'App\\Models\\Company', 24);
INSERT INTO `model_has_permissions` VALUES (8, 'App\\Models\\Company', 24);
INSERT INTO `model_has_permissions` VALUES (9, 'App\\Models\\Company', 24);
INSERT INTO `model_has_permissions` VALUES (10, 'App\\Models\\Company', 24);
INSERT INTO `model_has_permissions` VALUES (1, 'App\\Models\\Company', 27);
INSERT INTO `model_has_permissions` VALUES (2, 'App\\Models\\Company', 27);
INSERT INTO `model_has_permissions` VALUES (3, 'App\\Models\\Company', 27);
INSERT INTO `model_has_permissions` VALUES (4, 'App\\Models\\Company', 27);
INSERT INTO `model_has_permissions` VALUES (5, 'App\\Models\\Company', 27);
INSERT INTO `model_has_permissions` VALUES (6, 'App\\Models\\Company', 27);
INSERT INTO `model_has_permissions` VALUES (7, 'App\\Models\\Company', 27);
INSERT INTO `model_has_permissions` VALUES (8, 'App\\Models\\Company', 27);
INSERT INTO `model_has_permissions` VALUES (9, 'App\\Models\\Company', 27);
INSERT INTO `model_has_permissions` VALUES (10, 'App\\Models\\Company', 27);
INSERT INTO `model_has_permissions` VALUES (1, 'App\\Models\\Company', 28);
INSERT INTO `model_has_permissions` VALUES (2, 'App\\Models\\Company', 28);
INSERT INTO `model_has_permissions` VALUES (3, 'App\\Models\\Company', 28);
INSERT INTO `model_has_permissions` VALUES (4, 'App\\Models\\Company', 28);
INSERT INTO `model_has_permissions` VALUES (5, 'App\\Models\\Company', 28);
INSERT INTO `model_has_permissions` VALUES (6, 'App\\Models\\Company', 28);
INSERT INTO `model_has_permissions` VALUES (7, 'App\\Models\\Company', 28);
INSERT INTO `model_has_permissions` VALUES (8, 'App\\Models\\Company', 28);
INSERT INTO `model_has_permissions` VALUES (9, 'App\\Models\\Company', 28);
INSERT INTO `model_has_permissions` VALUES (10, 'App\\Models\\Company', 28);

-- ----------------------------
-- Table structure for model_has_role
-- ----------------------------
DROP TABLE IF EXISTS `model_has_role`;
CREATE TABLE `model_has_role`  (
  `role_id` int(10) UNSIGNED NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`role_id`, `model_id`, `model_type`) USING BTREE,
  INDEX `model_has_role_model_id_model_type_index`(`model_id`, `model_type`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of model_has_role
-- ----------------------------
INSERT INTO `model_has_role` VALUES (54, 'App\\Models\\Company', 1);
INSERT INTO `model_has_role` VALUES (66, 'App\\Models\\Company', 1);
INSERT INTO `model_has_role` VALUES (75, 'App\\Models\\Company', 1);
INSERT INTO `model_has_role` VALUES (76, 'App\\Models\\Company', 1);
INSERT INTO `model_has_role` VALUES (1, 'App\\Models\\Basic', 3);
INSERT INTO `model_has_role` VALUES (2, 'App\\Models\\Basic', 3);
INSERT INTO `model_has_role` VALUES (3, 'App\\Models\\Basic', 3);
INSERT INTO `model_has_role` VALUES (4, 'App\\Models\\Basic', 3);
INSERT INTO `model_has_role` VALUES (5, 'App\\Models\\Basic', 3);
INSERT INTO `model_has_role` VALUES (6, 'App\\Models\\Basic', 3);
INSERT INTO `model_has_role` VALUES (101, 'App\\Models\\Company', 14);
INSERT INTO `model_has_role` VALUES (102, 'App\\Models\\Company', 14);
INSERT INTO `model_has_role` VALUES (103, 'App\\Models\\Company', 14);
INSERT INTO `model_has_role` VALUES (104, 'App\\Models\\Company', 14);
INSERT INTO `model_has_role` VALUES (105, 'App\\Models\\Company', 14);
INSERT INTO `model_has_role` VALUES (106, 'App\\Models\\Company', 14);
INSERT INTO `model_has_role` VALUES (107, 'App\\Models\\Company', 15);
INSERT INTO `model_has_role` VALUES (108, 'App\\Models\\Company', 15);
INSERT INTO `model_has_role` VALUES (109, 'App\\Models\\Company', 15);
INSERT INTO `model_has_role` VALUES (110, 'App\\Models\\Company', 15);
INSERT INTO `model_has_role` VALUES (111, 'App\\Models\\Company', 15);
INSERT INTO `model_has_role` VALUES (112, 'App\\Models\\Company', 15);
INSERT INTO `model_has_role` VALUES (113, 'App\\Models\\Company', 15);
INSERT INTO `model_has_role` VALUES (156, 'App\\Models\\Company', 24);
INSERT INTO `model_has_role` VALUES (157, 'App\\Models\\Company', 24);
INSERT INTO `model_has_role` VALUES (158, 'App\\Models\\Company', 24);
INSERT INTO `model_has_role` VALUES (159, 'App\\Models\\Company', 24);
INSERT INTO `model_has_role` VALUES (160, 'App\\Models\\Company', 24);
INSERT INTO `model_has_role` VALUES (161, 'App\\Models\\Company', 24);
INSERT INTO `model_has_role` VALUES (174, 'App\\Models\\Company', 27);
INSERT INTO `model_has_role` VALUES (175, 'App\\Models\\Company', 27);
INSERT INTO `model_has_role` VALUES (176, 'App\\Models\\Company', 27);
INSERT INTO `model_has_role` VALUES (177, 'App\\Models\\Company', 27);
INSERT INTO `model_has_role` VALUES (178, 'App\\Models\\Company', 27);
INSERT INTO `model_has_role` VALUES (179, 'App\\Models\\Company', 27);
INSERT INTO `model_has_role` VALUES (180, 'App\\Models\\Company', 28);
INSERT INTO `model_has_role` VALUES (181, 'App\\Models\\Company', 28);
INSERT INTO `model_has_role` VALUES (182, 'App\\Models\\Company', 28);
INSERT INTO `model_has_role` VALUES (183, 'App\\Models\\Company', 28);
INSERT INTO `model_has_role` VALUES (184, 'App\\Models\\Company', 28);
INSERT INTO `model_has_role` VALUES (185, 'App\\Models\\Company', 28);

-- ----------------------------
-- Table structure for notifications
-- ----------------------------
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '通知用户id',
  `company_id` int(11) NOT NULL DEFAULT 0 COMMENT '所属公司id',
  `model_id` int(11) NOT NULL DEFAULT 0 COMMENT '多态模型id',
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '通知模型类名',
  `type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '通知类型',
  `message` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '通知内容',
  `readed` smallint(6) NOT NULL DEFAULT 0 COMMENT '是否已读',
  `ws_pushed` smallint(6) NOT NULL DEFAULT 0 COMMENT '是否进行过ws实时通知',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 219 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of notifications
-- ----------------------------
INSERT INTO `notifications` VALUES (1, 1, 1, 1, 'App\\Models\\Approval', 'c_approval', 'Pirvate发起一个申请需要您审批', 1, 0, '2019-04-11 14:51:06', '2019-04-15 18:16:07');
INSERT INTO `notifications` VALUES (2, 1, 1, 1, 'App\\Models\\Approval', 'c_approval', '审批已结束', 1, 0, '2019-04-11 14:51:31', '2019-04-15 18:16:07');
INSERT INTO `notifications` VALUES (3, 1, 1, 1, 'App\\Models\\Pst', 'c_pst', '评审通负责人任命通知', 1, 0, '2019-04-11 14:51:32', '2019-04-15 18:16:07');
INSERT INTO `notifications` VALUES (4, 1, 1, 1, 'App\\Models\\Pst', 'c_pst', 'Pirvate您所建的评审通,已经通过审批,处于待接收状态', 1, 0, '2019-04-11 14:51:33', '2019-04-15 18:16:07');
INSERT INTO `notifications` VALUES (5, 4, 1, 2, 'App\\Models\\Pst', 'c_pst', '评审通负责人任命通知', 1, 0, '2019-04-11 15:05:40', '2019-04-16 09:07:30');
INSERT INTO `notifications` VALUES (6, 4, 1, 2, 'App\\Models\\Pst', 'c_pst', '侃大山您所建的评审通,已经通过审批,处于待接收状态', 1, 0, '2019-04-11 15:05:41', '2019-04-16 09:07:30');
INSERT INTO `notifications` VALUES (7, 1, 1, 2, 'App\\Models\\Approval', 'c_approval', 'Pirvate发起一个申请需要您审批', 1, 0, '2019-04-11 15:13:23', '2019-04-15 18:16:07');
INSERT INTO `notifications` VALUES (10, 1, 1, 2, 'App\\Models\\Approval', 'c_approval', '审批已结束', 1, 0, '2019-04-11 15:31:49', '2019-04-15 18:16:07');
INSERT INTO `notifications` VALUES (11, 10, 1, 1, 'App\\Models\\Pst', 'c_pst', '评审通负责人转移通知', 0, 0, '2019-04-11 15:31:51', '2019-04-11 15:31:51');
INSERT INTO `notifications` VALUES (12, 4, 1, 2, 'App\\Models\\Pst', 'c_pst', '评审通负责人转移通知', 1, 0, '2019-04-11 15:42:25', '2019-04-16 09:07:30');
INSERT INTO `notifications` VALUES (13, 1, 1, 4, 'App\\Models\\Approval', 'c_approval', 'Pirvate发起一个申请需要您审批', 1, 0, '2019-04-11 15:52:20', '2019-04-15 18:16:07');
INSERT INTO `notifications` VALUES (14, 1, 1, 3, 'App\\Models\\Approval', 'c_approval', 'Pirvate发起一个申请需要您审批', 1, 0, '2019-04-11 15:52:20', '2019-04-15 18:16:07');
INSERT INTO `notifications` VALUES (15, 1, 1, 3, 'App\\Models\\Approval', 'c_approval', '审批已结束', 1, 0, '2019-04-11 15:57:22', '2019-04-15 18:16:07');
INSERT INTO `notifications` VALUES (16, 1, 1, 3, 'App\\Models\\Pst', 'c_pst', '评审通负责人任命通知', 1, 0, '2019-04-11 15:57:23', '2019-04-15 18:16:07');
INSERT INTO `notifications` VALUES (17, 1, 1, 3, 'App\\Models\\Pst', 'c_pst', 'Pirvate您所建的评审通,已经通过审批,处于待接收状态', 1, 0, '2019-04-11 15:57:24', '2019-04-15 18:16:07');
INSERT INTO `notifications` VALUES (18, 1, 1, 5, 'App\\Models\\Approval', 'c_approval', 'Pirvate发起一个申请需要您审批', 1, 0, '2019-04-11 17:02:21', '2019-04-15 18:16:07');
INSERT INTO `notifications` VALUES (19, 1, 1, 1, 'App\\Models\\Approval', 'c_approval', 'Pirvate发起一个申请需要您审批', 1, 0, '2019-04-11 17:56:09', '2019-04-15 18:16:07');
INSERT INTO `notifications` VALUES (20, 1, 1, 1, 'App\\Models\\Approval', 'c_approval', '审批已结束', 1, 0, '2019-04-11 17:58:21', '2019-04-15 18:16:07');
INSERT INTO `notifications` VALUES (21, 1, 1, 1, 'App\\Models\\Pst', 'c_pst', 'Pirvate您所建的评审通,已经通过审批,处于待接收状态', 1, 0, '2019-04-11 17:58:23', '2019-04-15 18:16:07');
INSERT INTO `notifications` VALUES (22, 1, 1, 1, 'App\\Models\\Approval', 'c_approval', 'Pirvate发起一个申请需要您审批', 1, 0, '2019-04-11 18:09:28', '2019-04-15 18:16:07');
INSERT INTO `notifications` VALUES (24, 1, 1, 1, 'App\\Models\\Approval', 'c_approval', '审批已结束', 1, 0, '2019-04-11 18:14:43', '2019-04-15 18:16:07');
INSERT INTO `notifications` VALUES (25, 4, 1, 1, 'App\\Models\\Pst', 'c_pst', '评审通参与邀请', 1, 0, '2019-04-11 18:14:44', '2019-04-16 09:07:30');
INSERT INTO `notifications` VALUES (26, 1, 1, 1, 'App\\Models\\Pst', 'c_pst', '评审通参抄送通知', 1, 0, '2019-04-11 18:14:45', '2019-04-15 18:16:07');
INSERT INTO `notifications` VALUES (27, 1, 1, 1, 'App\\Models\\Pst', 'c_pst', 'Pirvate您所建的评审通,已经通过审批,处于待接收状态', 1, 0, '2019-04-11 18:14:46', '2019-04-15 18:16:07');
INSERT INTO `notifications` VALUES (28, 4, 1, 4, 'App\\Models\\Approval', 'c_approval', 'Pirvate发起一个申请需要您审批', 1, 0, '2019-04-12 09:34:11', '2019-04-16 09:07:30');
INSERT INTO `notifications` VALUES (29, 1, 1, 4, 'App\\Models\\Approval', 'c_approval', '审批已结束', 1, 0, '2019-04-12 09:37:54', '2019-04-15 18:16:07');
INSERT INTO `notifications` VALUES (30, 14, 1, 4, 'App\\Models\\Pst', 'c_pst', '评审通参抄送通知', 0, 0, '2019-04-12 09:37:55', '2019-04-12 09:37:55');
INSERT INTO `notifications` VALUES (31, 5, 1, 4, 'App\\Models\\Pst', 'c_pst', '评审通参抄送通知', 0, 0, '2019-04-12 09:37:55', '2019-04-12 09:37:55');
INSERT INTO `notifications` VALUES (32, 1, 1, 4, 'App\\Models\\Pst', 'c_pst', '评审通参抄送通知', 1, 0, '2019-04-12 09:37:55', '2019-04-15 18:16:07');
INSERT INTO `notifications` VALUES (33, 10, 1, 4, 'App\\Models\\Pst', 'c_pst', '评审通参抄送通知', 0, 0, '2019-04-12 09:37:55', '2019-04-12 09:37:55');
INSERT INTO `notifications` VALUES (34, 4, 1, 4, 'App\\Models\\Pst', 'c_pst', '评审通参抄送通知', 1, 0, '2019-04-12 09:37:56', '2019-04-16 09:07:30');
INSERT INTO `notifications` VALUES (35, 4, 1, 4, 'App\\Models\\Pst', 'c_pst', '侃大山您所建的评审通,已经通过审批,处于待接收状态', 1, 0, '2019-04-12 09:37:56', '2019-04-16 09:07:30');
INSERT INTO `notifications` VALUES (36, 1, 1, 5, 'App\\Models\\Pst', 'c_pst', '评审通参抄送通知', 1, 0, '2019-04-12 10:26:48', '2019-04-15 18:16:07');
INSERT INTO `notifications` VALUES (37, 5, 1, 5, 'App\\Models\\Pst', 'c_pst', '评审通参抄送通知', 0, 0, '2019-04-12 10:26:48', '2019-04-12 10:26:48');
INSERT INTO `notifications` VALUES (38, 4, 1, 5, 'App\\Models\\Pst', 'c_pst', '侃大山您所建的评审通,已经通过审批,处于待接收状态', 1, 0, '2019-04-12 10:26:48', '2019-04-16 09:07:30');
INSERT INTO `notifications` VALUES (39, 1, 1, 5, 'App\\Models\\Approval', 'c_approval', 'Pirvate发起一个申请需要您审批', 1, 0, '2019-04-12 16:38:05', '2019-04-15 18:16:07');
INSERT INTO `notifications` VALUES (40, 1, 1, 7, 'App\\Models\\Pst', 'c_pst', '评审通参抄送通知', 1, 0, '2019-04-12 17:14:31', '2019-04-15 18:16:07');
INSERT INTO `notifications` VALUES (41, 5, 1, 7, 'App\\Models\\Pst', 'c_pst', '评审通参抄送通知', 0, 0, '2019-04-12 17:14:32', '2019-04-12 17:14:32');
INSERT INTO `notifications` VALUES (42, 10, 1, 7, 'App\\Models\\Pst', 'c_pst', '评审通参抄送通知', 0, 0, '2019-04-12 17:14:32', '2019-04-12 17:14:32');
INSERT INTO `notifications` VALUES (43, 4, 1, 7, 'App\\Models\\Pst', 'c_pst', '评审通参抄送通知', 1, 0, '2019-04-12 17:14:32', '2019-04-16 09:07:30');
INSERT INTO `notifications` VALUES (44, 11, 1, 7, 'App\\Models\\Pst', 'c_pst', '评审通参抄送通知', 0, 0, '2019-04-12 17:14:32', '2019-04-12 17:14:32');
INSERT INTO `notifications` VALUES (45, 18, 1, 7, 'App\\Models\\Pst', 'c_pst', '评审通参抄送通知', 0, 0, '2019-04-12 17:14:32', '2019-04-12 17:14:32');
INSERT INTO `notifications` VALUES (46, 14, 1, 7, 'App\\Models\\Pst', 'c_pst', '评审通参抄送通知', 0, 0, '2019-04-12 17:14:32', '2019-04-12 17:14:32');
INSERT INTO `notifications` VALUES (47, 4, 1, 7, 'App\\Models\\Pst', 'c_pst', '侃大山您所建的评审通,已经通过审批,处于待接收状态', 1, 0, '2019-04-12 17:14:33', '2019-04-16 09:07:30');
INSERT INTO `notifications` VALUES (48, 1, 1, 1, 'App\\Models\\Approval', 'c_approval', 'Pirvate发起一个申请需要您审批', 1, 0, '2019-04-13 09:45:59', '2019-04-15 18:16:07');
INSERT INTO `notifications` VALUES (49, 1, 1, 1, 'App\\Models\\Approval', 'c_approval', '审批已结束', 1, 0, '2019-04-13 09:47:10', '2019-04-15 18:16:07');
INSERT INTO `notifications` VALUES (50, 4, 1, 1, 'App\\Models\\Pst', 'c_pst', '评审通参与邀请', 1, 0, '2019-04-13 09:47:12', '2019-04-16 09:07:30');
INSERT INTO `notifications` VALUES (51, 1, 1, 1, 'App\\Models\\Pst', 'c_pst', '评审通参抄送通知', 1, 0, '2019-04-13 09:47:13', '2019-04-15 18:16:07');
INSERT INTO `notifications` VALUES (52, 1, 1, 1, 'App\\Models\\Pst', 'c_pst', 'Pirvate您所建的评审通,已经通过审批,处于待接收状态', 1, 0, '2019-04-13 09:47:14', '2019-04-15 18:16:07');
INSERT INTO `notifications` VALUES (53, 1, 1, 1, 'App\\Models\\Approval', 'c_approval', 'Pirvate发起一个申请需要您审批', 1, 0, '2019-04-13 11:15:48', '2019-04-15 18:16:07');
INSERT INTO `notifications` VALUES (54, 1, 1, 2, 'App\\Models\\Approval', 'c_approval', 'Pirvate发起一个申请需要您审批', 1, 0, '2019-04-13 11:16:42', '2019-04-15 18:16:07');
INSERT INTO `notifications` VALUES (55, 1, 1, 1, 'App\\Models\\Approval', 'c_approval', 'Pirvate发起一个申请需要您审批', 1, 0, '2019-04-13 11:21:53', '2019-04-15 18:16:07');
INSERT INTO `notifications` VALUES (56, 1, 1, 1, 'App\\Models\\Approval', 'c_approval', '审批已结束', 1, 0, '2019-04-13 11:23:00', '2019-04-15 18:16:07');
INSERT INTO `notifications` VALUES (57, 4, 1, 1, 'App\\Models\\Pst', 'c_pst', '评审通参与邀请', 1, 0, '2019-04-13 11:23:02', '2019-04-16 09:07:30');
INSERT INTO `notifications` VALUES (58, 1, 1, 1, 'App\\Models\\Pst', 'c_pst', '评审通参抄送通知', 1, 0, '2019-04-13 11:23:03', '2019-04-15 18:16:07');
INSERT INTO `notifications` VALUES (59, 1, 1, 1, 'App\\Models\\Pst', 'c_pst', 'Pirvate您所建的评审通,已经通过审批,处于待接收状态', 1, 0, '2019-04-13 11:23:04', '2019-04-15 18:16:07');
INSERT INTO `notifications` VALUES (60, 1, 1, 2, 'App\\Models\\Approval', 'c_approval', 'Pirvate发起一个申请需要您审批', 1, 0, '2019-04-13 11:25:44', '2019-04-15 18:16:07');
INSERT INTO `notifications` VALUES (117, 1, 1, 2, 'App\\Models\\Approval', 'c_approval', '审批已结束', 1, 0, '2019-04-13 11:58:05', '2019-04-15 18:16:07');
INSERT INTO `notifications` VALUES (118, 4, 1, 2, 'App\\Models\\Pst', 'c_pst', '评审通参与邀请', 1, 0, '2019-04-13 11:58:07', '2019-04-16 09:07:30');
INSERT INTO `notifications` VALUES (119, 14, 15, 2, 'App\\Models\\Pst', 'c_pst', '评审通参与邀请', 0, 0, '2019-04-13 11:58:10', '2019-04-13 11:58:10');
INSERT INTO `notifications` VALUES (120, 14, 15, 2, 'App\\Models\\Pst', 'c_pst', '评审通参与邀请', 0, 0, '2019-04-13 11:58:11', '2019-04-13 11:58:11');
INSERT INTO `notifications` VALUES (121, 14, 15, 2, 'App\\Models\\Pst', 'c_pst', '评审通参与邀请', 0, 0, '2019-04-13 11:58:11', '2019-04-13 11:58:11');
INSERT INTO `notifications` VALUES (122, 14, 15, 2, 'App\\Models\\Pst', 'c_pst', '评审通参与邀请', 0, 0, '2019-04-13 11:58:12', '2019-04-13 11:58:12');
INSERT INTO `notifications` VALUES (123, 14, 15, 2, 'App\\Models\\Pst', 'c_pst', '评审通参与邀请', 0, 0, '2019-04-13 11:58:12', '2019-04-13 11:58:12');
INSERT INTO `notifications` VALUES (124, 14, 15, 2, 'App\\Models\\Pst', 'c_pst', '评审通参与邀请', 0, 0, '2019-04-13 11:58:12', '2019-04-13 11:58:12');
INSERT INTO `notifications` VALUES (125, 1, 1, 2, 'App\\Models\\Pst', 'c_pst', '评审通参抄送通知', 1, 0, '2019-04-13 11:58:14', '2019-04-15 18:16:07');
INSERT INTO `notifications` VALUES (126, 1, 1, 2, 'App\\Models\\Pst', 'c_pst', 'Pirvate您所建的评审通,已经通过审批,处于待接收状态', 1, 0, '2019-04-13 11:58:15', '2019-04-15 18:16:07');
INSERT INTO `notifications` VALUES (127, 1, 1, 1, 'App\\Models\\Approval', 'c_approval', 'Pirvate发起一个申请需要您审批', 1, 0, '2019-04-13 15:21:05', '2019-04-15 18:16:07');
INSERT INTO `notifications` VALUES (128, 10, 28, 29, 'App\\Models\\CompanyNotice', 'c_notice', '山西省', 0, 0, '2019-04-13 17:06:27', '2019-04-13 17:06:27');
INSERT INTO `notifications` VALUES (129, 4, 28, 29, 'App\\Models\\CompanyNotice', 'c_notice', '山西省', 1, 0, '2019-04-13 17:06:27', '2019-04-13 17:12:42');
INSERT INTO `notifications` VALUES (130, 11, 28, 29, 'App\\Models\\CompanyNotice', 'c_notice', '山西省', 1, 0, '2019-04-13 17:06:27', '2019-04-13 17:54:44');
INSERT INTO `notifications` VALUES (131, 18, 28, 29, 'App\\Models\\CompanyNotice', 'c_notice', '山西省', 0, 0, '2019-04-13 17:06:27', '2019-04-13 17:06:27');
INSERT INTO `notifications` VALUES (132, 14, 28, 29, 'App\\Models\\CompanyNotice', 'c_notice', '山西省', 0, 0, '2019-04-13 17:06:28', '2019-04-13 17:06:28');
INSERT INTO `notifications` VALUES (133, 21, 28, 29, 'App\\Models\\CompanyNotice', 'c_notice', '山西省', 0, 0, '2019-04-13 17:06:28', '2019-04-13 17:06:28');
INSERT INTO `notifications` VALUES (134, 19, 28, 29, 'App\\Models\\CompanyNotice', 'c_notice', '山西省', 0, 0, '2019-04-13 17:06:28', '2019-04-13 17:06:28');
INSERT INTO `notifications` VALUES (135, 22, 28, 29, 'App\\Models\\CompanyNotice', 'c_notice', '山西省', 0, 0, '2019-04-13 17:06:28', '2019-04-13 17:06:28');
INSERT INTO `notifications` VALUES (136, 1, 28, 29, 'App\\Models\\CompanyNotice', 'c_notice', '山西省', 1, 0, '2019-04-13 17:06:28', '2019-04-13 18:22:11');
INSERT INTO `notifications` VALUES (137, 5, 28, 29, 'App\\Models\\CompanyNotice', 'c_notice', '山西省', 0, 0, '2019-04-13 17:06:28', '2019-04-13 17:06:28');
INSERT INTO `notifications` VALUES (138, 1, 1, 1, 'App\\Models\\Approval', 'c_approval', '审批已结束', 1, 0, '2019-04-13 17:17:59', '2019-04-15 18:16:07');
INSERT INTO `notifications` VALUES (139, 4, 1, 1, 'App\\Models\\Pst', 'c_pst', '评审通参与邀请', 1, 0, '2019-04-13 17:18:01', '2019-04-16 09:07:30');
INSERT INTO `notifications` VALUES (140, 1, 1, 1, 'App\\Models\\Pst', 'c_pst', '评审通参抄送通知', 1, 0, '2019-04-13 17:18:03', '2019-04-15 18:16:07');
INSERT INTO `notifications` VALUES (141, 1, 1, 1, 'App\\Models\\Pst', 'c_pst', 'Pirvate您所建的评审通,已经通过审批,处于待接收状态', 1, 0, '2019-04-13 17:18:04', '2019-04-15 18:16:07');
INSERT INTO `notifications` VALUES (142, 4, 1, 2, 'App\\Models\\Pst', 'c_pst', '评审通负责人任命通知', 1, 0, '2019-04-13 17:57:18', '2019-04-16 09:07:30');
INSERT INTO `notifications` VALUES (143, 19, 1, 2, 'App\\Models\\Pst', 'c_pst', '用户_15100000001您所建的评审通,已经通过审批,处于待接收状态', 1, 0, '2019-04-13 17:57:18', '2019-04-13 18:13:51');
INSERT INTO `notifications` VALUES (144, 1, 1, 36, 'App\\Models\\CompanyPartnerRecord', 'invite_partner', '探知科技 发起成为合作伙伴~', 0, 0, '2019-04-13 18:05:42', '2019-04-13 18:05:42');
INSERT INTO `notifications` VALUES (145, 2, 1, 36, 'App\\Models\\CompanyPartnerRecord', 'invite_partner', '探知科技 发起成为合作伙伴~', 0, 0, '2019-04-13 18:05:42', '2019-04-13 18:05:42');
INSERT INTO `notifications` VALUES (146, 3, 1, 36, 'App\\Models\\CompanyPartnerRecord', 'invite_partner', '探知科技 发起成为合作伙伴~', 0, 0, '2019-04-13 18:05:42', '2019-04-13 18:05:42');
INSERT INTO `notifications` VALUES (147, 4, 1, 3, 'App\\Models\\Pst', 'c_pst', '评审通负责人任命通知', 1, 0, '2019-04-13 18:10:49', '2019-04-16 09:07:30');
INSERT INTO `notifications` VALUES (148, 19, 1, 3, 'App\\Models\\Pst', 'c_pst', '用户_15100000001您所建的评审通,已经通过审批,处于待接收状态', 1, 0, '2019-04-13 18:10:49', '2019-04-13 18:13:51');
INSERT INTO `notifications` VALUES (149, 1, 1, 2, 'App\\Models\\Pst', 'c_pst', '评审通参与邀请', 1, 0, '2019-04-13 18:11:45', '2019-04-15 18:16:07');
INSERT INTO `notifications` VALUES (150, 1, 1, 2, 'App\\Models\\Pst', 'c_pst', '评审通参抄送通知', 1, 0, '2019-04-13 18:11:46', '2019-04-15 18:16:07');
INSERT INTO `notifications` VALUES (151, 5, 1, 2, 'App\\Models\\Pst', 'c_pst', '评审通参抄送通知', 0, 0, '2019-04-13 18:11:46', '2019-04-13 18:11:46');
INSERT INTO `notifications` VALUES (152, 10, 1, 2, 'App\\Models\\Pst', 'c_pst', '评审通参抄送通知', 0, 0, '2019-04-13 18:11:46', '2019-04-13 18:11:46');
INSERT INTO `notifications` VALUES (153, 4, 1, 2, 'App\\Models\\Pst', 'c_pst', '评审通参抄送通知', 1, 0, '2019-04-13 18:11:46', '2019-04-16 09:07:30');
INSERT INTO `notifications` VALUES (154, 11, 1, 2, 'App\\Models\\Pst', 'c_pst', '评审通参抄送通知', 0, 0, '2019-04-13 18:11:47', '2019-04-13 18:11:47');
INSERT INTO `notifications` VALUES (155, 18, 1, 2, 'App\\Models\\Pst', 'c_pst', '评审通参抄送通知', 0, 0, '2019-04-13 18:11:47', '2019-04-13 18:11:47');
INSERT INTO `notifications` VALUES (156, 14, 1, 2, 'App\\Models\\Pst', 'c_pst', '评审通参抄送通知', 0, 0, '2019-04-13 18:11:47', '2019-04-13 18:11:47');
INSERT INTO `notifications` VALUES (157, 4, 3, 3, 'App\\Models\\Pst', 'c_pst', '评审通:参与人接收侃大山,接收了评审通', 0, 0, '2019-04-13 18:11:50', '2019-04-13 18:11:50');
INSERT INTO `notifications` VALUES (158, 10, 1, 30, 'App\\Models\\CompanyNotice', 'c_notice', '23432234', 0, 0, '2019-04-13 18:24:09', '2019-04-13 18:24:09');
INSERT INTO `notifications` VALUES (159, 4, 1, 30, 'App\\Models\\CompanyNotice', 'c_notice', '23432234', 1, 0, '2019-04-13 18:24:09', '2019-04-16 09:07:30');
INSERT INTO `notifications` VALUES (160, 11, 1, 30, 'App\\Models\\CompanyNotice', 'c_notice', '23432234', 0, 0, '2019-04-13 18:24:09', '2019-04-13 18:24:09');
INSERT INTO `notifications` VALUES (161, 18, 1, 30, 'App\\Models\\CompanyNotice', 'c_notice', '23432234', 0, 0, '2019-04-13 18:24:10', '2019-04-13 18:24:10');
INSERT INTO `notifications` VALUES (162, 14, 1, 30, 'App\\Models\\CompanyNotice', 'c_notice', '23432234', 0, 0, '2019-04-13 18:24:10', '2019-04-13 18:24:10');
INSERT INTO `notifications` VALUES (163, 21, 1, 30, 'App\\Models\\CompanyNotice', 'c_notice', '23432234', 0, 0, '2019-04-13 18:24:10', '2019-04-13 18:24:10');
INSERT INTO `notifications` VALUES (164, 19, 1, 30, 'App\\Models\\CompanyNotice', 'c_notice', '23432234', 0, 0, '2019-04-13 18:24:11', '2019-04-13 18:24:11');
INSERT INTO `notifications` VALUES (165, 22, 1, 30, 'App\\Models\\CompanyNotice', 'c_notice', '23432234', 0, 0, '2019-04-13 18:24:11', '2019-04-13 18:24:11');
INSERT INTO `notifications` VALUES (166, 1, 1, 30, 'App\\Models\\CompanyNotice', 'c_notice', '23432234', 1, 0, '2019-04-13 18:24:11', '2019-04-15 18:16:07');
INSERT INTO `notifications` VALUES (167, 5, 1, 30, 'App\\Models\\CompanyNotice', 'c_notice', '23432234', 0, 0, '2019-04-13 18:24:12', '2019-04-13 18:24:12');
INSERT INTO `notifications` VALUES (168, 4, 1, 1, 'App\\Models\\Pst', 'c_pst', '评审通负责人任命通知', 1, 0, '2019-04-13 18:24:56', '2019-04-16 09:07:30');
INSERT INTO `notifications` VALUES (169, 19, 1, 1, 'App\\Models\\Pst', 'c_pst', '用户_15100000001您所建的评审通,已经通过审批,处于待接收状态', 0, 0, '2019-04-13 18:24:56', '2019-04-13 18:24:56');
INSERT INTO `notifications` VALUES (170, 4, 1, 2, 'App\\Models\\Pst', 'c_pst', '评审通参与邀请', 1, 0, '2019-04-13 18:30:06', '2019-04-16 09:07:30');
INSERT INTO `notifications` VALUES (171, 1, 1, 2, 'App\\Models\\Pst', 'c_pst', '评审通参抄送通知', 1, 0, '2019-04-13 18:30:07', '2019-04-15 18:16:07');
INSERT INTO `notifications` VALUES (172, 5, 1, 2, 'App\\Models\\Pst', 'c_pst', '评审通参抄送通知', 0, 0, '2019-04-13 18:30:07', '2019-04-13 18:30:07');
INSERT INTO `notifications` VALUES (173, 10, 1, 2, 'App\\Models\\Pst', 'c_pst', '评审通参抄送通知', 0, 0, '2019-04-13 18:30:08', '2019-04-13 18:30:08');
INSERT INTO `notifications` VALUES (174, 4, 1, 2, 'App\\Models\\Pst', 'c_pst', '评审通参抄送通知', 1, 0, '2019-04-13 18:30:08', '2019-04-16 09:07:30');
INSERT INTO `notifications` VALUES (175, 11, 1, 2, 'App\\Models\\Pst', 'c_pst', '评审通参抄送通知', 0, 0, '2019-04-13 18:30:08', '2019-04-13 18:30:08');
INSERT INTO `notifications` VALUES (176, 18, 1, 2, 'App\\Models\\Pst', 'c_pst', '评审通参抄送通知', 0, 0, '2019-04-13 18:30:09', '2019-04-13 18:30:09');
INSERT INTO `notifications` VALUES (177, 14, 1, 2, 'App\\Models\\Pst', 'c_pst', '评审通参抄送通知', 0, 0, '2019-04-13 18:30:09', '2019-04-13 18:30:09');
INSERT INTO `notifications` VALUES (178, 1, 1, 2, 'App\\Models\\Pst', 'c_pst', 'Pirvate您所建的评审通,已经通过审批,处于待接收状态', 1, 0, '2019-04-13 18:30:11', '2019-04-15 18:16:07');
INSERT INTO `notifications` VALUES (179, 4, 27, 1, 'App\\Models\\Pst', 'c_pst', '评审通参与邀请', 0, 0, '2019-04-13 18:32:03', '2019-04-13 18:32:03');
INSERT INTO `notifications` VALUES (180, 4, 27, 1, 'App\\Models\\Pst', 'c_pst', '评审通参与邀请', 0, 0, '2019-04-13 18:32:04', '2019-04-13 18:32:04');
INSERT INTO `notifications` VALUES (181, 4, 27, 1, 'App\\Models\\Pst', 'c_pst', '评审通参与邀请', 0, 0, '2019-04-13 18:32:04', '2019-04-13 18:32:04');
INSERT INTO `notifications` VALUES (182, 4, 27, 1, 'App\\Models\\Pst', 'c_pst', '评审通参与邀请', 0, 0, '2019-04-13 18:32:04', '2019-04-13 18:32:04');
INSERT INTO `notifications` VALUES (183, 4, 27, 1, 'App\\Models\\Pst', 'c_pst', '评审通参与邀请', 0, 0, '2019-04-13 18:32:04', '2019-04-13 18:32:04');
INSERT INTO `notifications` VALUES (184, 4, 27, 1, 'App\\Models\\Pst', 'c_pst', '评审通参与邀请', 0, 0, '2019-04-13 18:32:04', '2019-04-13 18:32:04');
INSERT INTO `notifications` VALUES (185, 1, 1, 1, 'App\\Models\\Pst', 'c_pst', '评审通参抄送通知', 1, 0, '2019-04-13 18:32:05', '2019-04-15 18:16:07');
INSERT INTO `notifications` VALUES (186, 5, 1, 1, 'App\\Models\\Pst', 'c_pst', '评审通参抄送通知', 0, 0, '2019-04-13 18:32:05', '2019-04-13 18:32:05');
INSERT INTO `notifications` VALUES (187, 10, 1, 1, 'App\\Models\\Pst', 'c_pst', '评审通参抄送通知', 0, 0, '2019-04-13 18:32:05', '2019-04-13 18:32:05');
INSERT INTO `notifications` VALUES (188, 4, 1, 1, 'App\\Models\\Pst', 'c_pst', '评审通参抄送通知', 1, 0, '2019-04-13 18:32:05', '2019-04-16 09:07:30');
INSERT INTO `notifications` VALUES (189, 11, 1, 1, 'App\\Models\\Pst', 'c_pst', '评审通参抄送通知', 0, 0, '2019-04-13 18:32:06', '2019-04-13 18:32:06');
INSERT INTO `notifications` VALUES (190, 18, 1, 1, 'App\\Models\\Pst', 'c_pst', '评审通参抄送通知', 0, 0, '2019-04-13 18:32:06', '2019-04-13 18:32:06');
INSERT INTO `notifications` VALUES (191, 14, 1, 1, 'App\\Models\\Pst', 'c_pst', '评审通参抄送通知', 0, 0, '2019-04-13 18:32:06', '2019-04-13 18:32:06');
INSERT INTO `notifications` VALUES (192, 4, 1, 1, 'App\\Models\\Pst', 'c_pst', '评审通:参与人接收侃大山,接收了评审通', 1, 0, '2019-04-15 10:51:58', '2019-04-16 09:07:30');
INSERT INTO `notifications` VALUES (193, 1, 1, 1, 'App\\Models\\Approval', 'c_approval', 'Pirvate发起一个申请需要您审批', 1, 0, '2019-04-15 18:05:04', '2019-04-15 18:16:07');
INSERT INTO `notifications` VALUES (194, 4, 27, 7, 'App\\Models\\Pst', 'c_pst', '评审通参与邀请', 0, 0, '2019-04-15 18:06:25', '2019-04-15 18:06:25');
INSERT INTO `notifications` VALUES (195, 4, 27, 7, 'App\\Models\\Pst', 'c_pst', '评审通参与邀请', 0, 0, '2019-04-15 18:06:25', '2019-04-15 18:06:25');
INSERT INTO `notifications` VALUES (196, 4, 27, 7, 'App\\Models\\Pst', 'c_pst', '评审通参与邀请', 0, 0, '2019-04-15 18:06:25', '2019-04-15 18:06:25');
INSERT INTO `notifications` VALUES (197, 4, 27, 7, 'App\\Models\\Pst', 'c_pst', '评审通参与邀请', 0, 0, '2019-04-15 18:06:25', '2019-04-15 18:06:25');
INSERT INTO `notifications` VALUES (198, 4, 27, 7, 'App\\Models\\Pst', 'c_pst', '评审通参与邀请', 0, 0, '2019-04-15 18:06:25', '2019-04-15 18:06:25');
INSERT INTO `notifications` VALUES (199, 4, 27, 7, 'App\\Models\\Pst', 'c_pst', '评审通参与邀请', 0, 0, '2019-04-15 18:06:25', '2019-04-15 18:06:25');
INSERT INTO `notifications` VALUES (200, 22, 0, 7, 'App\\Models\\Pst', 'pst_beside', '评审通参与邀请', 0, 0, '2019-04-15 18:06:26', '2019-04-15 18:06:26');
INSERT INTO `notifications` VALUES (201, 1, 1, 7, 'App\\Models\\Pst', 'c_pst', '评审通参抄送通知', 1, 0, '2019-04-15 18:06:27', '2019-04-15 18:16:07');
INSERT INTO `notifications` VALUES (202, 5, 1, 7, 'App\\Models\\Pst', 'c_pst', '评审通参抄送通知', 0, 0, '2019-04-15 18:06:27', '2019-04-15 18:06:27');
INSERT INTO `notifications` VALUES (203, 10, 1, 7, 'App\\Models\\Pst', 'c_pst', '评审通参抄送通知', 0, 0, '2019-04-15 18:06:27', '2019-04-15 18:06:27');
INSERT INTO `notifications` VALUES (204, 4, 1, 7, 'App\\Models\\Pst', 'c_pst', '评审通参抄送通知', 1, 0, '2019-04-15 18:06:27', '2019-04-16 09:07:30');
INSERT INTO `notifications` VALUES (205, 11, 1, 7, 'App\\Models\\Pst', 'c_pst', '评审通参抄送通知', 0, 0, '2019-04-15 18:06:27', '2019-04-15 18:06:27');
INSERT INTO `notifications` VALUES (206, 18, 1, 7, 'App\\Models\\Pst', 'c_pst', '评审通参抄送通知', 0, 0, '2019-04-15 18:06:27', '2019-04-15 18:06:27');
INSERT INTO `notifications` VALUES (207, 14, 1, 7, 'App\\Models\\Pst', 'c_pst', '评审通参抄送通知', 0, 0, '2019-04-15 18:06:27', '2019-04-15 18:06:27');
INSERT INTO `notifications` VALUES (208, 4, 1, 7, 'App\\Models\\Pst', 'c_pst', '侃大山您所建的评审通,已经通过审批,处于待接收状态', 1, 0, '2019-04-15 18:06:28', '2019-04-16 09:07:30');
INSERT INTO `notifications` VALUES (209, 10, 1, 32, 'App\\Models\\CompanyNotice', 'c_notice', '用户管理', 0, 0, '2019-04-15 18:27:09', '2019-04-15 18:27:09');
INSERT INTO `notifications` VALUES (210, 4, 1, 32, 'App\\Models\\CompanyNotice', 'c_notice', '用户管理', 1, 0, '2019-04-15 18:27:10', '2019-04-16 09:07:30');
INSERT INTO `notifications` VALUES (211, 11, 1, 32, 'App\\Models\\CompanyNotice', 'c_notice', '用户管理', 0, 0, '2019-04-15 18:27:10', '2019-04-15 18:27:10');
INSERT INTO `notifications` VALUES (212, 18, 1, 32, 'App\\Models\\CompanyNotice', 'c_notice', '用户管理', 0, 0, '2019-04-15 18:27:10', '2019-04-15 18:27:10');
INSERT INTO `notifications` VALUES (213, 14, 1, 32, 'App\\Models\\CompanyNotice', 'c_notice', '用户管理', 0, 0, '2019-04-15 18:27:11', '2019-04-15 18:27:11');
INSERT INTO `notifications` VALUES (214, 21, 1, 32, 'App\\Models\\CompanyNotice', 'c_notice', '用户管理', 0, 0, '2019-04-15 18:27:11', '2019-04-15 18:27:11');
INSERT INTO `notifications` VALUES (215, 19, 1, 32, 'App\\Models\\CompanyNotice', 'c_notice', '用户管理', 0, 0, '2019-04-15 18:27:12', '2019-04-15 18:27:12');
INSERT INTO `notifications` VALUES (216, 22, 1, 32, 'App\\Models\\CompanyNotice', 'c_notice', '用户管理', 0, 0, '2019-04-15 18:27:12', '2019-04-15 18:27:12');
INSERT INTO `notifications` VALUES (217, 1, 1, 32, 'App\\Models\\CompanyNotice', 'c_notice', '用户管理', 0, 0, '2019-04-15 18:27:12', '2019-04-15 18:27:12');
INSERT INTO `notifications` VALUES (218, 5, 1, 32, 'App\\Models\\CompanyNotice', 'c_notice', '用户管理', 0, 0, '2019-04-15 18:27:13', '2019-04-15 18:27:13');

-- ----------------------------
-- Table structure for oauth_access_tokens
-- ----------------------------
DROP TABLE IF EXISTS `oauth_access_tokens`;
CREATE TABLE `oauth_access_tokens`  (
  `id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(11) NULL DEFAULT NULL,
  `client_id` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `scopes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  `expires_at` datetime(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `oauth_access_tokens_user_id_index`(`user_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of oauth_access_tokens
-- ----------------------------
INSERT INTO `oauth_access_tokens` VALUES ('014716ce1e76c2373c72bb247a44dd262b65e111354b4c6588e6de9929e11efcf9a85d4c8d67bf8c', 2, 2, NULL, '[]', 0, '2019-04-13 11:17:09', '2019-04-13 11:17:09', '2020-04-13 11:17:09');
INSERT INTO `oauth_access_tokens` VALUES ('086c2e7d131e97bfb4f48a337951c198fadeb2359ec3f0a793f535e41d8066b48e02bd25e7673ff7', 4, 2, NULL, '[]', 0, '2019-03-11 09:39:02', '2019-03-11 09:39:02', '2020-03-11 09:39:02');
INSERT INTO `oauth_access_tokens` VALUES ('126390c46ea563dfc933ccef4748beb82394bd44677394ead4818c367f9f189c3e26fc17455b148c', 18, 3, NULL, '[]', 0, '2019-04-15 16:19:19', '2019-04-15 16:19:19', '2020-04-15 16:19:19');
INSERT INTO `oauth_access_tokens` VALUES ('1f5d6ddd4f70de2cce29651fba5508aa9a0f7c548d9a15acc8d2383f22bca2c4f8cbb9834359db15', 1, 3, NULL, '[]', 0, '2019-04-02 09:36:54', '2019-04-02 09:36:54', '2020-04-02 09:36:54');
INSERT INTO `oauth_access_tokens` VALUES ('2bd4848adf6920b849251903bec6938fc4b399e3fa87dbcdb2114c28c3bf58a30127b401c55904e2', 5, 2, NULL, '[]', 0, '2019-01-10 19:02:48', '2019-01-10 19:02:48', '2020-01-10 19:02:48');
INSERT INTO `oauth_access_tokens` VALUES ('5693ddb37fcef43bc59a52cb813eeb5ad658ed87fdaedfb7f67474c53f71fa56e69c7dd5165f3c8b', 12, 3, NULL, '[]', 0, '2019-01-07 11:34:36', '2019-01-07 11:34:36', '2020-01-07 11:34:36');
INSERT INTO `oauth_access_tokens` VALUES ('5d2e562809f0d479020359726f1f654c006d8d641d50d936b10c164595426e7729375bcf09adc6f3', 11, 3, NULL, '[]', 0, '2019-04-13 17:55:53', '2019-04-13 17:55:53', '2020-04-13 17:55:53');
INSERT INTO `oauth_access_tokens` VALUES ('66c0dd025b212bcb413c0fd8e1b7c24deb6d25a8a7ade4b4056ce9bf503638486bb7cb389b2fb452', 5, 3, NULL, '[]', 0, '2019-01-12 14:29:05', '2019-01-12 14:29:05', '2020-01-12 14:29:05');
INSERT INTO `oauth_access_tokens` VALUES ('717ba951652d43b6219c12f66becc68f93932f5ddcf200db96ad1105d312938e81c371fb9e9e19f0', 14, 3, NULL, '[]', 0, '2019-01-07 14:05:26', '2019-01-07 14:05:26', '2020-01-07 14:05:26');
INSERT INTO `oauth_access_tokens` VALUES ('7ad98dfae519c946d3b31e8916939c2cb085e3fe274d43d9b55cd7c4c88323e906242bcab363fadb', 3, 3, NULL, '[]', 0, '2019-02-18 16:32:16', '2019-02-18 16:32:16', '2020-02-18 16:32:16');
INSERT INTO `oauth_access_tokens` VALUES ('7e0f82f1d2033d856df6ebf647f1caa8cfdd277a2a1fbe9f668f6a8f46f4d779a75341299cd725c5', 10, 2, NULL, '[]', 0, '2019-03-22 09:22:31', '2019-03-22 09:22:31', '2020-03-22 09:22:31');
INSERT INTO `oauth_access_tokens` VALUES ('7f779830a6e0799fabba00d88717fd231fe784991ea88cd43d058c09b6961654ec3e6adcce0fadf6', 15, 3, NULL, '[]', 0, '2019-03-23 08:40:38', '2019-03-23 08:40:38', '2020-03-23 08:40:38');
INSERT INTO `oauth_access_tokens` VALUES ('888c970a5daf2a0746bf1d7137d70ee928e537013fb61dd657d16713ea9c5a9cf60e7f489046a120', 2, 3, NULL, '[]', 0, '2019-03-29 17:19:41', '2019-03-29 17:19:41', '2020-03-29 17:19:41');
INSERT INTO `oauth_access_tokens` VALUES ('8e0886421aa194c4e49bd8862546bd79a0d818542a664aa3f666a78c2ff2fd4caa16deffea232f75', 1, 2, NULL, '[]', 0, '2019-03-22 09:38:13', '2019-03-22 09:38:13', '2020-03-22 09:38:13');
INSERT INTO `oauth_access_tokens` VALUES ('b3d821d6124ddfc8b45a7fa644587ba3802bc32b994db298b17f96ea43109f772a36415ab97bab0b', 4, 3, NULL, '[]', 0, '2019-04-02 09:37:00', '2019-04-02 09:37:00', '2020-04-02 09:37:00');
INSERT INTO `oauth_access_tokens` VALUES ('c3d66411728b286edda4ec0e1c494c4e50c27c98d8681549b456755fee355fdfc31c6fa9a6e385f8', 19, 3, NULL, '[]', 0, '2019-04-11 16:45:09', '2019-04-11 16:45:09', '2020-04-11 16:45:09');
INSERT INTO `oauth_access_tokens` VALUES ('f367583c6ec2d50ec871c2d0421587d73a0f3884a44d507d7c6b19797e643520b010a9bcafe8d6c5', 10, 3, NULL, '[]', 0, '2019-03-15 16:44:48', '2019-03-15 16:44:48', '2020-03-15 16:44:48');
INSERT INTO `oauth_access_tokens` VALUES ('ff8364ebba1c16d4768e444d3d7baa6943f4b07379d4a38ed309e9b761cc2aa93a6749f6de049830', 14, 2, NULL, '[]', 0, '2019-04-02 07:51:35', '2019-04-02 07:51:35', '2020-04-02 07:51:35');

-- ----------------------------
-- Table structure for oauth_auth_codes
-- ----------------------------
DROP TABLE IF EXISTS `oauth_auth_codes`;
CREATE TABLE `oauth_auth_codes`  (
  `id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `scopes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for oauth_clients
-- ----------------------------
DROP TABLE IF EXISTS `oauth_clients`;
CREATE TABLE `oauth_clients`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NULL DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `secret` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `redirect` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `personal_access_client` tinyint(1) NOT NULL,
  `password_client` tinyint(1) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `oauth_clients_user_id_index`(`user_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of oauth_clients
-- ----------------------------
INSERT INTO `oauth_clients` VALUES (1, NULL, '工作通 Personal Access Client', 'nrKAHLO7jniQVNG11w5DyTlo2u8GtjH7HTE11zhl', 'http://localhost', 1, 0, 0, '2018-11-22 13:37:41', '2018-11-22 13:37:41');
INSERT INTO `oauth_clients` VALUES (2, NULL, 'app', 'ARbCMOP0osqaQOxHQgjK6NavwH2QahKY0xvvplq7', 'http://localhost', 0, 1, 0, '2018-11-22 13:37:41', '2018-11-22 13:37:41');
INSERT INTO `oauth_clients` VALUES (3, NULL, 'web', 'Zxvx48D7CWRNMMpIIgRGlUtljGonKISTKNitCCKc', 'http://localhost', 0, 1, 0, '2018-11-22 13:38:34', '2018-11-22 13:38:34');

-- ----------------------------
-- Table structure for oauth_personal_access_clients
-- ----------------------------
DROP TABLE IF EXISTS `oauth_personal_access_clients`;
CREATE TABLE `oauth_personal_access_clients`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `oauth_personal_access_clients_client_id_index`(`client_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of oauth_personal_access_clients
-- ----------------------------
INSERT INTO `oauth_personal_access_clients` VALUES (1, 1, '2018-11-22 13:37:41', '2018-11-22 13:37:41');
INSERT INTO `oauth_personal_access_clients` VALUES (2, 4, '2019-01-03 16:14:27', '2019-01-03 16:14:27');
INSERT INTO `oauth_personal_access_clients` VALUES (3, 6, '2019-03-06 08:35:48', '2019-03-06 08:35:48');

-- ----------------------------
-- Table structure for oauth_refresh_tokens
-- ----------------------------
DROP TABLE IF EXISTS `oauth_refresh_tokens`;
CREATE TABLE `oauth_refresh_tokens`  (
  `id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_token_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `oauth_refresh_tokens_access_token_id_index`(`access_token_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of oauth_refresh_tokens
-- ----------------------------
INSERT INTO `oauth_refresh_tokens` VALUES ('0c856848f19a0aa15d42db62da791f3cf18631b0aa1339c10d3eb91c25dff7c4500e646de5023d57', 'ff8364ebba1c16d4768e444d3d7baa6943f4b07379d4a38ed309e9b761cc2aa93a6749f6de049830', 0, '2020-04-02 07:51:36');
INSERT INTO `oauth_refresh_tokens` VALUES ('26b09f1933e63aeff03c133b70064a9cbb536117b03fc576996b2c544dfe1a0c8f1c4ac272cec1b9', '014716ce1e76c2373c72bb247a44dd262b65e111354b4c6588e6de9929e11efcf9a85d4c8d67bf8c', 0, '2020-04-13 11:17:09');
INSERT INTO `oauth_refresh_tokens` VALUES ('3793436c725d85c3e98d688ad2e03797ff91254f19d789bc1c7058b0c38dded9ec704a70d854408b', '7f779830a6e0799fabba00d88717fd231fe784991ea88cd43d058c09b6961654ec3e6adcce0fadf6', 0, '2020-03-23 08:40:38');
INSERT INTO `oauth_refresh_tokens` VALUES ('3eb7943bdffa34576763ee1c1bc40d1a75661c950d166d8a916a0096a1beb8e9da2138aee8ee75b8', '717ba951652d43b6219c12f66becc68f93932f5ddcf200db96ad1105d312938e81c371fb9e9e19f0', 0, '2020-01-07 14:05:26');
INSERT INTO `oauth_refresh_tokens` VALUES ('5bbace77b5e84f081350cc4d246c6e0c849271e9f0e2a8492f9cde31deb3cd065e3db411d255bab0', '1f5d6ddd4f70de2cce29651fba5508aa9a0f7c548d9a15acc8d2383f22bca2c4f8cbb9834359db15', 0, '2020-04-02 09:36:54');
INSERT INTO `oauth_refresh_tokens` VALUES ('73a16f5124a1fa48c9f97316c87b5bd8c9898afb8517bbaeecabdbe84bebd4238da1e32cea790051', '888c970a5daf2a0746bf1d7137d70ee928e537013fb61dd657d16713ea9c5a9cf60e7f489046a120', 0, '2020-03-29 17:19:41');
INSERT INTO `oauth_refresh_tokens` VALUES ('7b7c97c096fe7fe60e072de83037330120550c3957bf46b7a5affdacdc20eb55211d8c9e4930e962', '126390c46ea563dfc933ccef4748beb82394bd44677394ead4818c367f9f189c3e26fc17455b148c', 0, '2020-04-15 16:19:19');
INSERT INTO `oauth_refresh_tokens` VALUES ('865e44b243918b275c4c860aca2a8e11e164a2c11eb768ea28a70ee9ac6acf95929e5143db9d048b', 'f367583c6ec2d50ec871c2d0421587d73a0f3884a44d507d7c6b19797e643520b010a9bcafe8d6c5', 0, '2020-03-15 16:44:48');
INSERT INTO `oauth_refresh_tokens` VALUES ('94b5eb3f6add47815f19b7a0b37c529940df9840cbc0601d2f909e99bfe9b224e4a325542e1ebb52', '2bd4848adf6920b849251903bec6938fc4b399e3fa87dbcdb2114c28c3bf58a30127b401c55904e2', 0, '2020-01-10 19:02:48');
INSERT INTO `oauth_refresh_tokens` VALUES ('9d0e7f698d605cea04675a3ea59e6b05004f11e5664448b6ac06974085c6cf909a4606015afc0cd4', '7e0f82f1d2033d856df6ebf647f1caa8cfdd277a2a1fbe9f668f6a8f46f4d779a75341299cd725c5', 0, '2020-03-22 09:22:31');
INSERT INTO `oauth_refresh_tokens` VALUES ('c32cdb92d8d64e1968222272bdb0c8aa65ef257044190c9f7df67aa7d6c1bbc1eaacb01529e67ef5', '66c0dd025b212bcb413c0fd8e1b7c24deb6d25a8a7ade4b4056ce9bf503638486bb7cb389b2fb452', 0, '2020-01-12 14:29:06');
INSERT INTO `oauth_refresh_tokens` VALUES ('d15515a5b9b25e9aa47bbad37c45aa692ca51719028621b287a5f2525b1804f8890936764d78dcff', 'b3d821d6124ddfc8b45a7fa644587ba3802bc32b994db298b17f96ea43109f772a36415ab97bab0b', 0, '2020-04-02 09:37:00');
INSERT INTO `oauth_refresh_tokens` VALUES ('da18d2a0cd09cb2676a0c9c381ec22ed553a010d99316bb7d9ba8b023338bd0c455516bbfd3b617a', '5d2e562809f0d479020359726f1f654c006d8d641d50d936b10c164595426e7729375bcf09adc6f3', 0, '2020-04-13 17:55:54');
INSERT INTO `oauth_refresh_tokens` VALUES ('e7e8432cd0e02e8cd2778c440819be20fcc142a9d38e7076d0e49a8ff924439b6ea3ee4c98395cfa', '8e0886421aa194c4e49bd8862546bd79a0d818542a664aa3f666a78c2ff2fd4caa16deffea232f75', 0, '2020-03-22 09:38:13');
INSERT INTO `oauth_refresh_tokens` VALUES ('e8e1a4b92e4b8a6806748721d5840cfd20d439bd4b6193a396d5011cde9011650067234d344abdcf', 'c3d66411728b286edda4ec0e1c494c4e50c27c98d8681549b456755fee355fdfc31c6fa9a6e385f8', 0, '2020-04-11 16:45:09');
INSERT INTO `oauth_refresh_tokens` VALUES ('f1dcdb8e7a4f6a16010c104e7734aeeb19fe439ddfd29b1fe4eb89ae933b7dc08686e345ce7cb785', '086c2e7d131e97bfb4f48a337951c198fadeb2359ec3f0a793f535e41d8066b48e02bd25e7673ff7', 0, '2020-03-11 09:39:02');
INSERT INTO `oauth_refresh_tokens` VALUES ('f42a89fab9a549cac6d36fe79df217e651953b3ab87029d40cc77d4b42c029c6717fe70a2d71d48b', '5693ddb37fcef43bc59a52cb813eeb5ad658ed87fdaedfb7f67474c53f71fa56e69c7dd5165f3c8b', 0, '2020-01-07 11:34:36');
INSERT INTO `oauth_refresh_tokens` VALUES ('fd3874c7191ed70380aa935a85c5985c794035bb34ccc1228690164c87688cafe368c81abc6b1560', '7ad98dfae519c946d3b31e8916939c2cb085e3fe274d43d9b55cd7c4c88323e906242bcab363fadb', 0, '2020-02-18 16:32:16');

-- ----------------------------
-- Table structure for order_items
-- ----------------------------
DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `product_sku_id` int(10) UNSIGNED NOT NULL,
  `amount` int(10) UNSIGNED NOT NULL,
  `price` decimal(10, 2) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `order_items_order_id_foreign`(`order_id`) USING BTREE,
  INDEX `order_items_product_id_foreign`(`product_id`) USING BTREE,
  INDEX `order_items_product_sku_id_foreign`(`product_sku_id`) USING BTREE,
  CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `order_items_product_sku_id_foreign` FOREIGN KEY (`product_sku_id`) REFERENCES `product_skus` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 24 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of order_items
-- ----------------------------
INSERT INTO `order_items` VALUES (1, 2, 1, 1, 50, 299.00);
INSERT INTO `order_items` VALUES (2, 3, 1, 1, 50, 299.00);
INSERT INTO `order_items` VALUES (3, 4, 1, 1, 50, 299.00);
INSERT INTO `order_items` VALUES (4, 5, 1, 1, 50, 299.00);
INSERT INTO `order_items` VALUES (5, 6, 1, 2, 50, 259.00);
INSERT INTO `order_items` VALUES (6, 7, 1, 2, 50, 259.00);
INSERT INTO `order_items` VALUES (7, 8, 1, 2, 50, 259.00);
INSERT INTO `order_items` VALUES (8, 9, 1, 2, 50, 259.00);
INSERT INTO `order_items` VALUES (9, 10, 2, 8, 50, 0.09);
INSERT INTO `order_items` VALUES (10, 11, 2, 8, 50, 0.09);
INSERT INTO `order_items` VALUES (11, 12, 2, 8, 50, 0.09);
INSERT INTO `order_items` VALUES (12, 13, 2, 8, 50, 0.09);
INSERT INTO `order_items` VALUES (13, 14, 2, 8, 50, 0.09);
INSERT INTO `order_items` VALUES (14, 15, 2, 8, 50, 0.09);
INSERT INTO `order_items` VALUES (15, 16, 2, 8, 50, 0.09);
INSERT INTO `order_items` VALUES (16, 17, 2, 8, 50, 0.09);
INSERT INTO `order_items` VALUES (17, 18, 2, 8, 50, 0.09);
INSERT INTO `order_items` VALUES (18, 19, 2, 8, 50, 0.09);
INSERT INTO `order_items` VALUES (19, 20, 2, 8, 50, 0.09);
INSERT INTO `order_items` VALUES (20, 21, 2, 8, 50, 0.09);
INSERT INTO `order_items` VALUES (21, 22, 2, 8, 50, 0.09);
INSERT INTO `order_items` VALUES (22, 23, 2, 8, 50, 0.09);
INSERT INTO `order_items` VALUES (23, 24, 2, 8, 50, 0.09);

-- ----------------------------
-- Table structure for orders
-- ----------------------------
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `company_id` int(10) UNSIGNED NOT NULL,
  `total_amount` decimal(10, 2) NOT NULL,
  `remark` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `paid_at` datetime(0) NULL DEFAULT NULL,
  `payment_method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `payment_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `refund_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `refund_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `closed` tinyint(1) NOT NULL DEFAULT 0,
  `extra` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `orders_no_unique`(`no`) USING BTREE,
  UNIQUE INDEX `orders_refund_no_unique`(`refund_no`) USING BTREE,
  INDEX `orders_user_id_foreign`(`user_id`) USING BTREE,
  INDEX `orders_company_id_foreign`(`company_id`) USING BTREE,
  CONSTRAINT `orders_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 25 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of orders
-- ----------------------------
INSERT INTO `orders` VALUES (2, '20190413184129322062', 11, 1, 14950.00, '备注', '2019-04-13 23:01:13', 'alipay', '2019041322001465321000002337', 'pending', NULL, 0, NULL, '2019-04-13 18:41:29', '2019-04-13 23:01:13');
INSERT INTO `orders` VALUES (3, '20190413193746041968', 11, 1, 14950.00, '备注', NULL, NULL, NULL, 'pending', NULL, 1, NULL, '2019-04-13 19:37:46', '2019-04-13 23:18:24');
INSERT INTO `orders` VALUES (4, '20190414215708429105', 11, 1, 14950.00, '备注', NULL, NULL, NULL, 'pending', NULL, 1, NULL, '2019-04-14 21:57:08', '2019-04-14 22:32:49');
INSERT INTO `orders` VALUES (5, '20190414215716921846', 11, 1, 14950.00, '备注', NULL, NULL, NULL, 'pending', NULL, 1, NULL, '2019-04-14 21:57:16', '2019-04-14 22:32:49');
INSERT INTO `orders` VALUES (6, '20190414215913917379', 11, 1, 12950.00, '备注', NULL, NULL, NULL, 'pending', NULL, 1, NULL, '2019-04-14 21:59:13', '2019-04-14 22:32:50');
INSERT INTO `orders` VALUES (7, '20190414215913909935', 11, 1, 12950.00, '备注', NULL, NULL, NULL, 'pending', NULL, 1, NULL, '2019-04-14 21:59:13', '2019-04-14 22:32:50');
INSERT INTO `orders` VALUES (8, '20190414215914263349', 11, 1, 12950.00, '备注', NULL, NULL, NULL, 'pending', NULL, 1, NULL, '2019-04-14 21:59:14', '2019-04-14 22:32:50');
INSERT INTO `orders` VALUES (9, '20190414220220327486', 11, 1, 12950.00, '备注', NULL, NULL, NULL, 'pending', NULL, 1, NULL, '2019-04-14 22:02:21', '2019-04-14 22:32:51');
INSERT INTO `orders` VALUES (10, '20190414220329483421', 11, 1, 4.50, '备注', '2019-04-14 22:25:53', 'alipay', '2019041422001465321000001002', 'pending', NULL, 0, NULL, '2019-04-14 22:03:29', '2019-04-14 22:25:53');
INSERT INTO `orders` VALUES (11, '20190414220332739765', 11, 1, 4.50, '备注', NULL, NULL, NULL, 'pending', NULL, 1, NULL, '2019-04-14 22:03:32', '2019-04-14 22:32:52');
INSERT INTO `orders` VALUES (12, '20190414220335024846', 11, 1, 4.50, '备注', '2019-04-14 22:11:22', 'alipay', '2019041422001465321000003584', 'pending', NULL, 0, NULL, '2019-04-14 22:03:36', '2019-04-14 22:11:22');
INSERT INTO `orders` VALUES (13, '20190414222847994154', 11, 1, 4.50, '备注', NULL, NULL, NULL, 'pending', NULL, 1, NULL, '2019-04-14 22:28:47', '2019-04-14 22:32:53');
INSERT INTO `orders` VALUES (14, '20190414222850589408', 11, 1, 4.50, '备注', NULL, NULL, NULL, 'pending', NULL, 1, NULL, '2019-04-14 22:28:50', '2019-04-14 22:32:53');
INSERT INTO `orders` VALUES (15, '20190414222850966464', 11, 1, 4.50, '备注', NULL, NULL, NULL, 'pending', NULL, 1, NULL, '2019-04-14 22:28:50', '2019-04-14 22:32:54');
INSERT INTO `orders` VALUES (16, '20190414222851967463', 11, 1, 4.50, '备注', NULL, NULL, NULL, 'pending', NULL, 1, NULL, '2019-04-14 22:28:51', '2019-04-14 22:32:54');
INSERT INTO `orders` VALUES (17, '20190414222852377545', 11, 1, 4.50, '备注', NULL, NULL, NULL, 'pending', NULL, 1, NULL, '2019-04-14 22:28:52', '2019-04-14 22:32:54');
INSERT INTO `orders` VALUES (18, '20190414222852950957', 11, 1, 4.50, '备注', NULL, NULL, NULL, 'pending', NULL, 1, NULL, '2019-04-14 22:28:52', '2019-04-14 22:32:55');
INSERT INTO `orders` VALUES (19, '20190414223536557035', 11, 1, 4.50, '备注', NULL, NULL, NULL, 'pending', NULL, 1, NULL, '2019-04-14 22:35:36', '2019-04-14 22:36:08');
INSERT INTO `orders` VALUES (20, '20190414223537906627', 11, 1, 4.50, '备注', NULL, NULL, NULL, 'pending', NULL, 1, NULL, '2019-04-14 22:35:37', '2019-04-14 22:36:08');
INSERT INTO `orders` VALUES (21, '20190414223537672553', 11, 1, 4.50, '备注', NULL, NULL, NULL, 'pending', NULL, 1, NULL, '2019-04-14 22:35:37', '2019-04-14 22:36:09');
INSERT INTO `orders` VALUES (22, '20190414223539877935', 11, 1, 4.50, '备注', NULL, NULL, NULL, 'pending', NULL, 0, NULL, '2019-04-14 22:35:39', '2019-04-14 22:35:39');
INSERT INTO `orders` VALUES (23, '20190414223539251640', 11, 1, 4.50, '备注', '2019-04-14 22:47:26', 'alipay', '2019041422001465321000002339', 'pending', NULL, 0, NULL, '2019-04-14 22:35:39', '2019-04-14 22:47:26');
INSERT INTO `orders` VALUES (24, '20190414223541424586', 11, 1, 4.50, '备注', '2019-04-14 22:38:23', 'alipay', '2019041422001465321000002338', 'pending', NULL, 0, NULL, '2019-04-14 22:35:41', '2019-04-14 22:38:23');

-- ----------------------------
-- Table structure for oss_file
-- ----------------------------
DROP TABLE IF EXISTS `oss_file`;
CREATE TABLE `oss_file`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uploader_id` int(11) NOT NULL COMMENT '上传者id',
  `company_id` int(11) NOT NULL COMMENT '所属企业的id',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '项目中文件名显示(原名)',
  `size` double(8, 2) NOT NULL COMMENT '文件大小/kb',
  `oss_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '对应的阿里云路径',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of oss_file
-- ----------------------------
INSERT INTO `oss_file` VALUES (1, 1, 1, '短信验证.png', 141.73, 'company/company1/评审通附件/gaYyva21072GGsUbLcg.png', '2019-04-13 18:30:05', '2019-04-13 18:30:05');
INSERT INTO `oss_file` VALUES (2, 4, 1, 'u=3805949291,1300857286&fm=200&gp=0.jpg', 15.01, 'company/company1/评审通附件/uYXych401263BQoWZkc.jpg', '2019-04-15 17:56:38', '2019-04-15 17:56:38');
INSERT INTO `oss_file` VALUES (3, 1, 1, '短信验证.png', 141.73, 'company/company1/评审通附件/7J578E59180aZHLKouQ.png', '2019-04-15 18:05:03', '2019-04-15 18:05:03');

-- ----------------------------
-- Table structure for oss_file_browse_record
-- ----------------------------
DROP TABLE IF EXISTS `oss_file_browse_record`;
CREATE TABLE `oss_file_browse_record`  (
  `user_id` int(10) UNSIGNED NOT NULL COMMENT '浏览用户id',
  `file_id` int(10) UNSIGNED NOT NULL COMMENT '浏览的文件id',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '浏览用户的名字',
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '文件操作标识',
  `time` timestamp(0) NOT NULL COMMENT '浏览的时间'
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for partner_sort
-- ----------------------------
DROP TABLE IF EXISTS `partner_sort`;
CREATE TABLE `partner_sort`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `partner_id` int(11) NOT NULL,
  `sort_id` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 25 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of partner_sort
-- ----------------------------
INSERT INTO `partner_sort` VALUES (19, 15, 1);
INSERT INTO `partner_sort` VALUES (20, 16, 1);
INSERT INTO `partner_sort` VALUES (21, 17, 27);
INSERT INTO `partner_sort` VALUES (23, 17, 1);
INSERT INTO `partner_sort` VALUES (24, 18, -1);

-- ----------------------------
-- Table structure for password_resets
-- ----------------------------
DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets`  (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  INDEX `password_resets_email_index`(`email`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for per_sort
-- ----------------------------
DROP TABLE IF EXISTS `per_sort`;
CREATE TABLE `per_sort`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of per_sort
-- ----------------------------
INSERT INTO `per_sort` VALUES (1, '系统管理', '123');
INSERT INTO `per_sort` VALUES (2, '主线评审', '123');
INSERT INTO `per_sort` VALUES (3, '企业伙伴', '123');
INSERT INTO `per_sort` VALUES (4, '公告管理', '123');

-- ----------------------------
-- Table structure for permissions
-- ----------------------------
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_personal` smallint(6) NOT NULL DEFAULT 0 COMMENT '标识是否为单个用户提供的增值服务',
  `per_sort_id` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `per_name_unique`(`name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 22 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of permissions
-- ----------------------------
INSERT INTO `permissions` VALUES (1, 'c_notice_editor_per', '公告编辑权(能够 编辑|撤回| 自己发布的公告)', 'gzt', 0, 4);
INSERT INTO `permissions` VALUES (2, 'c_notice_manager_per', '公告管理权(可以管理任何人发布的公告)', 'gzt', 0, 4);
INSERT INTO `permissions` VALUES (3, 'c_oss_editor_per', '企业oss文件管理权限', 'gzt', 0, 2);
INSERT INTO `permissions` VALUES (4, 'c_oss_upload_per', '企业oss上传文件权限', 'gzt', 0, 2);
INSERT INTO `permissions` VALUES (5, 'c_super_manage_per', '企业超级管理员权限', 'gzt', 0, 1);
INSERT INTO `permissions` VALUES (6, 'c_recive_project_per', '接收企业合作伙伴项目的权限', 'gzt', 0, 3);
INSERT INTO `permissions` VALUES (7, 'c_normal_mange_per', '企业普通管理员权限', 'gzt', 0, 3);
INSERT INTO `permissions` VALUES (8, 'c_staff_per', '企业员工标识', 'gzt', 0, 3);
INSERT INTO `permissions` VALUES (9, 'c_pst_manage_per', '评审通管理权', 'gzt', 0, 2);
INSERT INTO `permissions` VALUES (10, 'c_pst_recive_per', '评审通数据接收权', 'gzt', 0, 0);
INSERT INTO `permissions` VALUES (11, '1asd', '企业基础权限预留', 'gzt', 0, 0);
INSERT INTO `permissions` VALUES (12, 'aas', '企业基础权限预留', 'gzt', 0, 0);
INSERT INTO `permissions` VALUES (13, 'aasasd', '企业基础权限预留', 'gzt', 0, 0);
INSERT INTO `permissions` VALUES (14, 'xcv', '企业基础权限预留', 'gzt', 0, 0);
INSERT INTO `permissions` VALUES (15, '45g', '企业基础权限预留', 'gzt', 0, 0);
INSERT INTO `permissions` VALUES (16, '45gdfg', '企业基础权限预留', 'gzt', 0, 0);
INSERT INTO `permissions` VALUES (17, 'hjh', '个人用户基础权限预留', 'gzt', 1, 0);
INSERT INTO `permissions` VALUES (18, 'hjhghjng', '个人用户基础权限预留', 'gzt', 1, 0);
INSERT INTO `permissions` VALUES (19, '897987', '个人用户基础权限预留', 'gzt', 1, 0);
INSERT INTO `permissions` VALUES (20, '567iojio', '个人用户基础权限预留', 'gzt', 1, 0);
INSERT INTO `permissions` VALUES (21, 'poiop', '个人用户基础权限预留', 'gzt', 1, 0);

-- ----------------------------
-- Table structure for product_skus
-- ----------------------------
DROP TABLE IF EXISTS `product_skus`;
CREATE TABLE `product_skus`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(10, 2) NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `product_skus_product_id_foreign`(`product_id`) USING BTREE,
  CONSTRAINT `product_skus_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 13 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of product_skus
-- ----------------------------
INSERT INTO `product_skus` VALUES (1, '100人以下', '100人以下', 299.00, 1, '2019-04-12 21:42:40', '2019-04-12 21:42:40');
INSERT INTO `product_skus` VALUES (2, '200人以下', '200人以下', 259.00, 1, '2019-04-12 21:42:43', '2019-04-12 21:42:43');
INSERT INTO `product_skus` VALUES (3, '300人以下', '300人以下', 219.00, 1, '2019-04-12 21:42:43', '2019-04-12 21:42:43');
INSERT INTO `product_skus` VALUES (4, '400人以下', '400人以下', 179.00, 1, '2019-04-12 21:42:43', '2019-04-12 21:42:43');
INSERT INTO `product_skus` VALUES (5, '500人以下', '500人以下', 139.00, 1, '2019-04-12 21:42:44', '2019-04-12 21:42:44');
INSERT INTO `product_skus` VALUES (6, '5000条', '5000条', 0.10, 2, '2019-04-14 11:12:25', '2019-04-14 11:30:43');
INSERT INTO `product_skus` VALUES (7, '1万条', '1万条', 0.09, 2, '2019-04-14 11:12:26', '2019-04-14 11:30:43');
INSERT INTO `product_skus` VALUES (8, '2万条', '2万条', 0.09, 2, '2019-04-14 11:12:26', '2019-04-14 11:30:44');
INSERT INTO `product_skus` VALUES (9, '200GB', '200GB', 2.50, 3, '2019-04-14 11:14:29', '2019-04-14 11:37:44');
INSERT INTO `product_skus` VALUES (10, '400GB', '400GB', 2.00, 3, '2019-04-14 11:14:29', '2019-04-14 11:37:44');
INSERT INTO `product_skus` VALUES (11, '800GB', '800GB', 1.90, 3, '2019-04-14 11:14:29', '2019-04-14 11:37:45');
INSERT INTO `product_skus` VALUES (12, '1TB', '1TB', 1.80, 3, '2019-04-14 11:14:30', '2019-04-14 11:37:45');

-- ----------------------------
-- Table structure for products
-- ----------------------------
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `on_sale` tinyint(1) NOT NULL DEFAULT 1,
  `sold_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `price` decimal(10, 2) NOT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of products
-- ----------------------------
INSERT INTO `products` VALUES (1, '组织人数', '<p>人数版</p>', 1, 0, 139.00, '2019-04-12 21:42:40', '2019-04-14 21:23:24');
INSERT INTO `products` VALUES (2, '短信服务', '<p>短信服务</p>', 1, 200, 0.09, '2019-04-14 11:12:25', '2019-04-14 22:48:01');
INSERT INTO `products` VALUES (3, '网盘存储', '<p>网盘存储</p>', 1, 0, 1.80, '2019-04-14 11:14:29', '2019-04-14 11:37:44');

-- ----------------------------
-- Table structure for pst
-- ----------------------------
DROP TABLE IF EXISTS `pst`;
CREATE TABLE `pst`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `last_pst_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '上级评审通id',
  `template_id` int(10) UNSIGNED NOT NULL COMMENT '采用的评审通模板id',
  `publish_user_id` int(10) UNSIGNED NOT NULL COMMENT '发起人id',
  `company_id` int(10) UNSIGNED NOT NULL COMMENT '所属企业id',
  `outside_user_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '外部联系人id',
  `state` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '评审状态',
  `need_approval` smallint(5) UNSIGNED NOT NULL COMMENT '相关操作是否需要审批标识',
  `removed` smallint(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否删除',
  `form_template` json NULL COMMENT '发起评审的表单数据',
  `form_values` json NULL COMMENT '所需要的表单目标k-v',
  `process_template` json NULL COMMENT '所需人员的数据',
  `approval_method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '流程类型:自由流程/固定流程',
  `origin_data` json NULL COMMENT '上级来源数据',
  `join_user_data` json NULL COMMENT '参与人员的数据',
  `join_pst_form_data` json NULL COMMENT '内部参与人提交的表单数据信息',
  `transfer_join_data` json NULL COMMENT '参与人转移数据json',
  `cc_user_data` json NULL COMMENT '抄送人员的数据',
  `duty_user_data` json NULL COMMENT '负责人信息json',
  `transfer_duty_data` json NULL COMMENT '转移负责人json',
  `last_duty_user_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '上一环负责人id',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `pst_state_index`(`state`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pst
-- ----------------------------
INSERT INTO `pst` VALUES (1, 0, 5, 19, 1, 0, '评审中', 0, 0, '[{\"type\": \"SELECT\", \"field\": {\"name\": \"category\", \"label\": \"分类\", \"required\": false}, \"value\": \"预算\"}, {\"type\": \"SELECT\", \"field\": {\"name\": \"project_category\", \"label\": \"工程分类\", \"required\": false}, \"value\": \"土木工程\"}, {\"type\": \"INPUT\", \"field\": {\"name\": \"project_name\", \"label\": \"项目名称\", \"required\": false}, \"value\": \"葛天源n期\"}, {\"type\": \"INPUT\", \"field\": {\"name\": \"amount_of_review\", \"label\": \"送审金额\", \"required\": false}, \"value\": \"5435435434534\"}, {\"type\": \"SELECT\", \"field\": {\"name\": \"action_label\", \"label\": \"标签\", \"required\": false}, \"value\": \"火速处理\"}]', '{\"category\": \"预算\", \"action_label\": \"火速处理\", \"project_name\": \"葛天源n期\", \"amount_of_review\": \"5435435434534\", \"project_category\": \"土木工程\"}', '[]', '自由流程', '[]', '{\"join_user_ids\": {\"inside_user_ids\": [4], \"outside_user_ids\": [], \"company_partner_ids\": [27], \"inside_receive_state\": {\"state_4\": \"评审中\", \"state_19\": \"已接收\"}}, \"join_form_data\": {\"checkedIds\": {\"partner\": [\"8EByMG259247\"], \"organizational\": [\"ZkmXpK40126\"], \"externalContact\": []}, \"checkedKeys\": {\"partner\": [\"8EByMG259247\"], \"organizational\": [\"ZkmXpK40126\"]}, \"checkedPersonnels\": {\"partner\": [{\"key\": \"8EByMG259247\", \"type\": \"partner\", \"title\": \"智障集中营\", \"linKey\": [{\"key\": \"partners\", \"title\": \"合作伙伴\"}, {\"key\": \"8EByMG259247\", \"type\": \"partner\", \"title\": \"智障集中营\"}]}], \"organizational\": [{\"key\": \"ZkmXpK40126\", \"type\": \"personnel\", \"title\": \"侃大山\", \"linKey\": [{\"key\": \"caAAYg11545\", \"title\": \"探知科技\"}, {\"key\": \"ZkmXpK40126\", \"type\": \"personnel\", \"title\": \"侃大山\"}]}], \"externalContact\": []}}}', NULL, NULL, '{\"cc_users\": {\"checkedKeys\": [\"caAAYg11545\", \"xVQtP021072\", \"Tu673A30599\", \"W9W3yA87761\", \"SGRamE97288\", \"UE7ha5154450\", \"sEDa7F11545\", \"wnRWc740126\", \"wWItJk163977\", \"OQDuqT49653\", \"82lYQA173504\", \"SXCQCZ59180\", \"k8XUlE49653\", \"uOjXhl68707\", \"8V0zna78234\", \"piGRjs106815\", \"MyFoH2116342\", \"xAT08w125869\", \"Y8Z4Pl135396\", \"XW0xPj144923\", \"reMiDT97288\", \"ZkmXpK40126\", \"vDonno106815\", \"gcafR0173504\", \"E8GPcE135396\"], \"checkedPersonnels\": [{\"key\": \"sEDa7F11545\", \"type\": \"personnel\", \"title\": \"Pirvate\", \"linKey\": [{\"key\": \"caAAYg11545\", \"title\": \"探知科技\"}, {\"key\": \"xVQtP021072\", \"type\": \"department\", \"title\": \"人事部\"}, {\"key\": \"sEDa7F11545\", \"type\": \"personnel\", \"title\": \"Pirvate\"}]}, {\"key\": \"k8XUlE49653\", \"type\": \"personnel\", \"title\": \"Soul\", \"linKey\": [{\"key\": \"caAAYg11545\", \"title\": \"探知科技\"}, {\"key\": \"SXCQCZ59180\", \"type\": \"department\", \"title\": \"总总总总总经理部\"}, {\"key\": \"k8XUlE49653\", \"type\": \"personnel\", \"title\": \"Soul\"}]}, {\"key\": \"reMiDT97288\", \"type\": \"personnel\", \"title\": \"Pirvate2\", \"linKey\": [{\"key\": \"caAAYg11545\", \"title\": \"探知科技\"}, {\"key\": \"reMiDT97288\", \"type\": \"personnel\", \"title\": \"Pirvate2\"}]}, {\"key\": \"ZkmXpK40126\", \"type\": \"personnel\", \"title\": \"侃大山\", \"linKey\": [{\"key\": \"caAAYg11545\", \"title\": \"探知科技\"}, {\"key\": \"ZkmXpK40126\", \"type\": \"personnel\", \"title\": \"侃大山\"}]}, {\"key\": \"vDonno106815\", \"type\": \"personnel\", \"title\": \"用户_15939965336\", \"linKey\": [{\"key\": \"caAAYg11545\", \"title\": \"探知科技\"}, {\"key\": \"vDonno106815\", \"type\": \"personnel\", \"title\": \"用户_15939965336\"}]}, {\"key\": \"gcafR0173504\", \"type\": \"personnel\", \"title\": \"用户_17634766666\", \"linKey\": [{\"key\": \"caAAYg11545\", \"title\": \"探知科技\"}, {\"key\": \"gcafR0173504\", \"type\": \"personnel\", \"title\": \"用户_17634766666\"}]}, {\"key\": \"E8GPcE135396\", \"type\": \"personnel\", \"title\": \"132\", \"linKey\": [{\"key\": \"caAAYg11545\", \"title\": \"探知科技\"}, {\"key\": \"E8GPcE135396\", \"type\": \"personnel\", \"title\": \"132\"}]}]}, \"cc_user_ids\": [1, 5, 10, 4, 11, 18, 14]}', '{\"duty_user_id\": 4, \"duty_form_data\": {\"key\": \"ZkmXpK40126\", \"title\": \"侃大山\"}, \"duty_receive_state\": \"已接收\"}', NULL, 0, '2019-04-13 18:24:56', '2019-04-15 10:51:57');
INSERT INTO `pst` VALUES (2, 0, 5, 1, 1, 0, '评审中', 0, 0, '[{\"type\": \"SELECT\", \"field\": {\"name\": \"category\", \"label\": \"分类\", \"required\": false}, \"value\": \"预算\"}, {\"type\": \"SELECT\", \"field\": {\"name\": \"project_category\", \"label\": \"工程分类\", \"required\": false}, \"value\": \"土木工程\"}, {\"type\": \"INPUT\", \"field\": {\"name\": \"project_name\", \"label\": \"项目名称\", \"required\": false}, \"value\": \"测试专用\"}, {\"type\": \"INPUT\", \"field\": {\"name\": \"amount_of_review\", \"label\": \"送审金额\", \"required\": false}, \"value\": \"800\"}, {\"type\": \"SELECT\", \"field\": {\"name\": \"action_label\", \"label\": \"标签\", \"required\": false}, \"value\": \"火速处理\"}]', '{\"category\": \"预算\", \"action_label\": \"火速处理\", \"project_name\": \"测试专用\", \"amount_of_review\": \"800\", \"project_category\": \"土木工程\"}', '[]', '自由流程', '[]', '{\"join_user_ids\": {\"inside_user_ids\": [1, 4], \"outside_user_ids\": [], \"company_partner_ids\": [], \"inside_receive_state\": {\"state_1\": \"已接收\", \"state_4\": \"待接收\"}}, \"join_form_data\": {\"checkedIds\": {\"partner\": [], \"organizational\": [\"qXp47111545\", \"rzw9Rv40126\"], \"externalContact\": []}, \"checkedKeys\": {\"organizational\": [\"K1khJZ21072\", \"TWiIC130599\", \"mwaELL87761\", \"RgJIcT97288\", \"NZxV8G154450\", \"qXp47111545\", \"rzw9Rv40126\"]}, \"checkedPersonnels\": {\"partner\": [], \"organizational\": [{\"key\": \"qXp47111545\", \"type\": \"personnel\", \"title\": \"Pirvate\", \"linKey\": [{\"key\": \"Vr8gct11545\", \"title\": \"探知科技\"}, {\"key\": \"K1khJZ21072\", \"type\": \"department\", \"title\": \"人事部\"}, {\"key\": \"qXp47111545\", \"type\": \"personnel\", \"title\": \"Pirvate\"}]}, {\"key\": \"rzw9Rv40126\", \"type\": \"personnel\", \"title\": \"侃大山\", \"linKey\": [{\"key\": \"Vr8gct11545\", \"title\": \"探知科技\"}, {\"key\": \"rzw9Rv40126\", \"type\": \"personnel\", \"title\": \"侃大山\"}]}], \"externalContact\": []}}}', NULL, NULL, '{\"cc_users\": {\"checkedKeys\": [\"caAAYg11545\", \"xVQtP021072\", \"Tu673A30599\", \"W9W3yA87761\", \"SGRamE97288\", \"UE7ha5154450\", \"sEDa7F11545\", \"wnRWc740126\", \"wWItJk163977\", \"OQDuqT49653\", \"82lYQA173504\", \"SXCQCZ59180\", \"k8XUlE49653\", \"uOjXhl68707\", \"8V0zna78234\", \"piGRjs106815\", \"MyFoH2116342\", \"xAT08w125869\", \"Y8Z4Pl135396\", \"XW0xPj144923\", \"reMiDT97288\", \"ZkmXpK40126\", \"vDonno106815\", \"gcafR0173504\", \"E8GPcE135396\"], \"checkedPersonnels\": [{\"key\": \"sEDa7F11545\", \"type\": \"personnel\", \"title\": \"Pirvate\", \"linKey\": [{\"key\": \"caAAYg11545\", \"title\": \"探知科技\"}, {\"key\": \"xVQtP021072\", \"type\": \"department\", \"title\": \"人事部\"}, {\"key\": \"sEDa7F11545\", \"type\": \"personnel\", \"title\": \"Pirvate\"}]}, {\"key\": \"k8XUlE49653\", \"type\": \"personnel\", \"title\": \"Soul\", \"linKey\": [{\"key\": \"caAAYg11545\", \"title\": \"探知科技\"}, {\"key\": \"SXCQCZ59180\", \"type\": \"department\", \"title\": \"总总总总总经理部\"}, {\"key\": \"k8XUlE49653\", \"type\": \"personnel\", \"title\": \"Soul\"}]}, {\"key\": \"reMiDT97288\", \"type\": \"personnel\", \"title\": \"Pirvate2\", \"linKey\": [{\"key\": \"caAAYg11545\", \"title\": \"探知科技\"}, {\"key\": \"reMiDT97288\", \"type\": \"personnel\", \"title\": \"Pirvate2\"}]}, {\"key\": \"ZkmXpK40126\", \"type\": \"personnel\", \"title\": \"侃大山\", \"linKey\": [{\"key\": \"caAAYg11545\", \"title\": \"探知科技\"}, {\"key\": \"ZkmXpK40126\", \"type\": \"personnel\", \"title\": \"侃大山\"}]}, {\"key\": \"vDonno106815\", \"type\": \"personnel\", \"title\": \"用户_15939965336\", \"linKey\": [{\"key\": \"caAAYg11545\", \"title\": \"探知科技\"}, {\"key\": \"vDonno106815\", \"type\": \"personnel\", \"title\": \"用户_15939965336\"}]}, {\"key\": \"gcafR0173504\", \"type\": \"personnel\", \"title\": \"用户_17634766666\", \"linKey\": [{\"key\": \"caAAYg11545\", \"title\": \"探知科技\"}, {\"key\": \"gcafR0173504\", \"type\": \"personnel\", \"title\": \"用户_17634766666\"}]}, {\"key\": \"E8GPcE135396\", \"type\": \"personnel\", \"title\": \"132\", \"linKey\": [{\"key\": \"caAAYg11545\", \"title\": \"探知科技\"}, {\"key\": \"E8GPcE135396\", \"type\": \"personnel\", \"title\": \"132\"}]}]}, \"cc_user_ids\": [1, 5, 10, 4, 11, 18, 14]}', '{\"duty_user_id\": 1, \"duty_form_data\": {\"key\": \"qXp47111545\", \"title\": \"Pirvate\"}, \"duty_receive_state\": \"已接收\"}', NULL, 0, '2019-04-13 18:29:58', '2019-04-13 18:30:06');
INSERT INTO `pst` VALUES (3, 1, 0, 0, 27, 0, '待接收', 1, 0, '[{\"type\": \"SELECT\", \"field\": {\"name\": \"category\", \"label\": \"分类\", \"required\": false}, \"value\": \"预算\"}, {\"type\": \"SELECT\", \"field\": {\"name\": \"project_category\", \"label\": \"工程分类\", \"required\": false}, \"value\": \"土木工程\"}, {\"type\": \"INPUT\", \"field\": {\"name\": \"project_name\", \"label\": \"项目名称\", \"required\": false}, \"value\": \"葛天源n期\"}, {\"type\": \"INPUT\", \"field\": {\"name\": \"amount_of_review\", \"label\": \"送审金额\", \"required\": false}, \"value\": \"5435435434534\"}, {\"type\": \"SELECT\", \"field\": {\"name\": \"action_label\", \"label\": \"标签\", \"required\": false}, \"value\": \"火速处理\"}]', '{\"category\": \"预算\", \"action_label\": \"火速处理\", \"project_name\": \"葛天源n期\", \"amount_of_review\": \"5435435434534\", \"project_category\": \"土木工程\"}', '[]', NULL, '[]', NULL, NULL, NULL, NULL, NULL, NULL, 0, '2019-04-13 18:32:03', '2019-04-13 18:32:03');
INSERT INTO `pst` VALUES (4, 0, 5, 4, 1, 0, '评审中', 0, 0, '[{\"type\": \"SELECT\", \"field\": {\"name\": \"category\", \"label\": \"分类\", \"required\": false}, \"value\": \"概算\"}, {\"type\": \"SELECT\", \"field\": {\"name\": \"project_category\", \"label\": \"工程分类\", \"required\": false}, \"value\": \"园林\"}, {\"type\": \"INPUT\", \"field\": {\"name\": \"project_name\", \"label\": \"项目名称\", \"required\": false}, \"value\": \"葛天源k期\"}, {\"type\": \"INPUT\", \"field\": {\"name\": \"amount_of_review\", \"label\": \"送审金额\", \"required\": false}, \"value\": \"5435435434534\"}, {\"type\": \"SELECT\", \"field\": {\"name\": \"action_label\", \"label\": \"标签\", \"required\": false}, \"value\": \"12222\"}]', '{\"category\": \"概算\", \"action_label\": \"12222\", \"project_name\": \"葛天源k期\", \"amount_of_review\": \"5435435434534\", \"project_category\": \"园林\"}', '[]', '自由流程', '[]', '{\"join_user_ids\": {\"inside_user_ids\": [4], \"outside_user_ids\": [22], \"company_partner_ids\": [27], \"inside_receive_state\": {\"state_4\": \"已接收\"}}, \"join_form_data\": {\"checkedIds\": {\"partner\": [\"fn7e1s259247\"], \"organizational\": [\"rzw9Rv40126\"], \"externalContact\": [\"SwfTpj211612\"]}, \"checkedKeys\": {\"partner\": [\"fn7e1s259247\"], \"organizational\": [\"rzw9Rv40126\"], \"externalContact\": [\"SwfTpj211612\"]}, \"checkedPersonnels\": {\"partner\": [{\"key\": \"fn7e1s259247\", \"type\": \"partner\", \"title\": \"智障集中营\", \"linKey\": [{\"key\": \"partners\", \"title\": \"合作伙伴\"}, {\"key\": \"fn7e1s259247\", \"type\": \"partner\", \"title\": \"智障集中营\"}]}], \"organizational\": [{\"key\": \"rzw9Rv40126\", \"type\": \"personnel\", \"title\": \"侃大山\", \"linKey\": [{\"key\": \"Vr8gct11545\", \"title\": \"探知科技\"}, {\"key\": \"rzw9Rv40126\", \"type\": \"personnel\", \"title\": \"侃大山\"}]}], \"externalContact\": [{\"key\": \"SwfTpj211612\", \"type\": \"externalContact\", \"title\": \"用户_15100000003\", \"linKey\": [{\"key\": \"externalContacts\", \"title\": \"外部联系人\"}, {\"key\": \"SwfTpj211612\", \"type\": \"externalContact\", \"title\": \"用户_15100000003\"}]}]}}}', NULL, NULL, '{\"cc_users\": {\"checkedKeys\": [\"caAAYg11545\", \"xVQtP021072\", \"Tu673A30599\", \"W9W3yA87761\", \"SGRamE97288\", \"UE7ha5154450\", \"sEDa7F11545\", \"wnRWc740126\", \"wWItJk163977\", \"OQDuqT49653\", \"82lYQA173504\", \"SXCQCZ59180\", \"k8XUlE49653\", \"uOjXhl68707\", \"8V0zna78234\", \"piGRjs106815\", \"MyFoH2116342\", \"xAT08w125869\", \"Y8Z4Pl135396\", \"XW0xPj144923\", \"reMiDT97288\", \"ZkmXpK40126\", \"vDonno106815\", \"gcafR0173504\", \"E8GPcE135396\"], \"checkedPersonnels\": [{\"key\": \"sEDa7F11545\", \"type\": \"personnel\", \"title\": \"Pirvate\", \"linKey\": [{\"key\": \"caAAYg11545\", \"title\": \"探知科技\"}, {\"key\": \"xVQtP021072\", \"type\": \"department\", \"title\": \"人事部\"}, {\"key\": \"sEDa7F11545\", \"type\": \"personnel\", \"title\": \"Pirvate\"}]}, {\"key\": \"k8XUlE49653\", \"type\": \"personnel\", \"title\": \"Soul\", \"linKey\": [{\"key\": \"caAAYg11545\", \"title\": \"探知科技\"}, {\"key\": \"SXCQCZ59180\", \"type\": \"department\", \"title\": \"总总总总总经理部\"}, {\"key\": \"k8XUlE49653\", \"type\": \"personnel\", \"title\": \"Soul\"}]}, {\"key\": \"reMiDT97288\", \"type\": \"personnel\", \"title\": \"Pirvate2\", \"linKey\": [{\"key\": \"caAAYg11545\", \"title\": \"探知科技\"}, {\"key\": \"reMiDT97288\", \"type\": \"personnel\", \"title\": \"Pirvate2\"}]}, {\"key\": \"ZkmXpK40126\", \"type\": \"personnel\", \"title\": \"侃大山\", \"linKey\": [{\"key\": \"caAAYg11545\", \"title\": \"探知科技\"}, {\"key\": \"ZkmXpK40126\", \"type\": \"personnel\", \"title\": \"侃大山\"}]}, {\"key\": \"vDonno106815\", \"type\": \"personnel\", \"title\": \"用户_15939965336\", \"linKey\": [{\"key\": \"caAAYg11545\", \"title\": \"探知科技\"}, {\"key\": \"vDonno106815\", \"type\": \"personnel\", \"title\": \"用户_15939965336\"}]}, {\"key\": \"gcafR0173504\", \"type\": \"personnel\", \"title\": \"用户_17634766666\", \"linKey\": [{\"key\": \"caAAYg11545\", \"title\": \"探知科技\"}, {\"key\": \"gcafR0173504\", \"type\": \"personnel\", \"title\": \"用户_17634766666\"}]}, {\"key\": \"E8GPcE135396\", \"type\": \"personnel\", \"title\": \"132\", \"linKey\": [{\"key\": \"caAAYg11545\", \"title\": \"探知科技\"}, {\"key\": \"E8GPcE135396\", \"type\": \"personnel\", \"title\": \"132\"}]}]}, \"cc_user_ids\": [1, 5, 10, 4, 11, 18, 14]}', '{\"duty_user_id\": 4, \"duty_form_data\": {\"key\": \"rzw9Rv40126\", \"title\": \"侃大山\"}, \"duty_receive_state\": \"已接收\"}', NULL, 0, '2019-04-15 17:56:32', '2019-04-15 17:56:38');
INSERT INTO `pst` VALUES (5, 4, 0, 0, 27, 0, '待接收', 1, 0, '[{\"type\": \"SELECT\", \"field\": {\"name\": \"category\", \"label\": \"分类\", \"required\": false}, \"value\": \"概算\"}, {\"type\": \"SELECT\", \"field\": {\"name\": \"project_category\", \"label\": \"工程分类\", \"required\": false}, \"value\": \"园林\"}, {\"type\": \"INPUT\", \"field\": {\"name\": \"project_name\", \"label\": \"项目名称\", \"required\": false}, \"value\": \"葛天源k期\"}, {\"type\": \"INPUT\", \"field\": {\"name\": \"amount_of_review\", \"label\": \"送审金额\", \"required\": false}, \"value\": \"5435435434534\"}, {\"type\": \"SELECT\", \"field\": {\"name\": \"action_label\", \"label\": \"标签\", \"required\": false}, \"value\": \"12222\"}]', '{\"category\": \"概算\", \"action_label\": \"12222\", \"project_name\": \"葛天源k期\", \"amount_of_review\": \"5435435434534\", \"project_category\": \"园林\"}', '[]', NULL, '[]', NULL, NULL, NULL, NULL, NULL, NULL, 0, '2019-04-15 17:56:38', '2019-04-15 17:56:38');
INSERT INTO `pst` VALUES (6, 0, 6, 1, 1, 0, '待审核', 1, 0, '[{\"type\": \"SELECT\", \"field\": {\"name\": \"category\", \"label\": \"分类\", \"required\": false}, \"value\": \"预算\"}, {\"type\": \"INPUT\", \"field\": {\"name\": \"amount_of_review\", \"label\": \"送审金额\", \"required\": false}, \"value\": \"12312\"}]', '{\"category\": \"预算\", \"amount_of_review\": \"12312\"}', '[{\"type\": \"normal\", \"checkedInfo\": {\"checkedKeys\": [\"xVQtP021072\", \"Tu673A30599\", \"W9W3yA87761\", \"SGRamE97288\", \"UE7ha5154450\", \"sEDa7F11545\"], \"checkedPersonnels\": [{\"key\": \"sEDa7F11545\", \"type\": \"personnel\", \"title\": \"Pirvate\", \"linKey\": [{\"key\": \"caAAYg11545\", \"title\": \"探知科技\"}, {\"key\": \"xVQtP021072\", \"type\": \"department\", \"title\": \"人事部\"}, {\"key\": \"sEDa7F11545\", \"type\": \"personnel\", \"title\": \"Pirvate\"}]}]}}]', '固定流程', '[]', '{\"join_user_ids\": {\"inside_user_ids\": [1, 4], \"outside_user_ids\": [], \"company_partner_ids\": [], \"inside_receive_state\": {\"state_1\": \"待接收\", \"state_4\": \"待接收\"}}, \"join_form_data\": {\"checkedIds\": {\"partner\": [], \"organizational\": [\"qXp47111545\", \"rzw9Rv40126\"], \"externalContact\": []}, \"checkedKeys\": {\"organizational\": [\"K1khJZ21072\", \"TWiIC130599\", \"mwaELL87761\", \"RgJIcT97288\", \"NZxV8G154450\", \"qXp47111545\", \"rzw9Rv40126\"]}, \"checkedPersonnels\": {\"partner\": [], \"organizational\": [{\"key\": \"qXp47111545\", \"type\": \"personnel\", \"title\": \"Pirvate\", \"linKey\": [{\"key\": \"Vr8gct11545\", \"title\": \"探知科技\"}, {\"key\": \"K1khJZ21072\", \"type\": \"department\", \"title\": \"人事部\"}, {\"key\": \"qXp47111545\", \"type\": \"personnel\", \"title\": \"Pirvate\"}]}, {\"key\": \"rzw9Rv40126\", \"type\": \"personnel\", \"title\": \"侃大山\", \"linKey\": [{\"key\": \"Vr8gct11545\", \"title\": \"探知科技\"}, {\"key\": \"rzw9Rv40126\", \"type\": \"personnel\", \"title\": \"侃大山\"}]}], \"externalContact\": []}}}', NULL, NULL, '{\"cc_users\": {\"checkedKeys\": [\"sEDa7F11545\"], \"checkedPersonnels\": [{\"key\": \"sEDa7F11545\", \"type\": \"personnel\", \"title\": \"Pirvate\", \"linKey\": [{\"key\": \"caAAYg11545\", \"title\": \"探知科技\"}, {\"key\": \"xVQtP021072\", \"type\": \"department\", \"title\": \"人事部\"}, {\"key\": \"sEDa7F11545\", \"type\": \"personnel\", \"title\": \"Pirvate\"}]}]}, \"cc_user_ids\": [1]}', '{\"duty_user_id\": 1, \"duty_form_data\": {\"key\": \"qXp47111545\", \"title\": \"Pirvate\"}, \"duty_receive_state\": \"待接收\"}', NULL, 0, '2019-04-15 18:04:56', '2019-04-15 18:04:56');
INSERT INTO `pst` VALUES (7, 0, 5, 4, 1, 0, '评审中', 0, 0, '[{\"type\": \"SELECT\", \"field\": {\"name\": \"category\", \"label\": \"分类\", \"required\": false}, \"value\": \"概算\"}, {\"type\": \"SELECT\", \"field\": {\"name\": \"project_category\", \"label\": \"工程分类\", \"required\": false}, \"value\": \"园林\"}, {\"type\": \"INPUT\", \"field\": {\"name\": \"project_name\", \"label\": \"项目名称\", \"required\": false}, \"value\": \"葛天源k期\"}, {\"type\": \"INPUT\", \"field\": {\"name\": \"amount_of_review\", \"label\": \"送审金额\", \"required\": false}, \"value\": \"5435435434534\"}, {\"type\": \"SELECT\", \"field\": {\"name\": \"action_label\", \"label\": \"标签\", \"required\": false}, \"value\": \"12222\"}]', '{\"category\": \"概算\", \"action_label\": \"12222\", \"project_name\": \"葛天源k期\", \"amount_of_review\": \"5435435434534\", \"project_category\": \"园林\"}', '[]', '自由流程', '[]', '{\"join_user_ids\": {\"inside_user_ids\": [4], \"outside_user_ids\": [22], \"company_partner_ids\": [27], \"inside_receive_state\": {\"state_4\": \"已接收\"}}, \"join_form_data\": {\"checkedIds\": {\"partner\": [\"fn7e1s259247\"], \"organizational\": [\"rzw9Rv40126\"], \"externalContact\": [\"SwfTpj211612\"]}, \"checkedKeys\": {\"partner\": [\"fn7e1s259247\"], \"organizational\": [\"rzw9Rv40126\"], \"externalContact\": [\"SwfTpj211612\"]}, \"checkedPersonnels\": {\"partner\": [{\"key\": \"fn7e1s259247\", \"type\": \"partner\", \"title\": \"智障集中营\", \"linKey\": [{\"key\": \"partners\", \"title\": \"合作伙伴\"}, {\"key\": \"fn7e1s259247\", \"type\": \"partner\", \"title\": \"智障集中营\"}]}], \"organizational\": [{\"key\": \"rzw9Rv40126\", \"type\": \"personnel\", \"title\": \"侃大山\", \"linKey\": [{\"key\": \"Vr8gct11545\", \"title\": \"探知科技\"}, {\"key\": \"rzw9Rv40126\", \"type\": \"personnel\", \"title\": \"侃大山\"}]}], \"externalContact\": [{\"key\": \"SwfTpj211612\", \"type\": \"externalContact\", \"title\": \"用户_15100000003\", \"linKey\": [{\"key\": \"externalContacts\", \"title\": \"外部联系人\"}, {\"key\": \"SwfTpj211612\", \"type\": \"externalContact\", \"title\": \"用户_15100000003\"}]}]}}}', NULL, NULL, '{\"cc_users\": {\"checkedKeys\": [\"caAAYg11545\", \"xVQtP021072\", \"Tu673A30599\", \"W9W3yA87761\", \"SGRamE97288\", \"UE7ha5154450\", \"sEDa7F11545\", \"wnRWc740126\", \"wWItJk163977\", \"OQDuqT49653\", \"82lYQA173504\", \"SXCQCZ59180\", \"k8XUlE49653\", \"uOjXhl68707\", \"8V0zna78234\", \"piGRjs106815\", \"MyFoH2116342\", \"xAT08w125869\", \"Y8Z4Pl135396\", \"XW0xPj144923\", \"reMiDT97288\", \"ZkmXpK40126\", \"vDonno106815\", \"gcafR0173504\", \"E8GPcE135396\"], \"checkedPersonnels\": [{\"key\": \"sEDa7F11545\", \"type\": \"personnel\", \"title\": \"Pirvate\", \"linKey\": [{\"key\": \"caAAYg11545\", \"title\": \"探知科技\"}, {\"key\": \"xVQtP021072\", \"type\": \"department\", \"title\": \"人事部\"}, {\"key\": \"sEDa7F11545\", \"type\": \"personnel\", \"title\": \"Pirvate\"}]}, {\"key\": \"k8XUlE49653\", \"type\": \"personnel\", \"title\": \"Soul\", \"linKey\": [{\"key\": \"caAAYg11545\", \"title\": \"探知科技\"}, {\"key\": \"SXCQCZ59180\", \"type\": \"department\", \"title\": \"总总总总总经理部\"}, {\"key\": \"k8XUlE49653\", \"type\": \"personnel\", \"title\": \"Soul\"}]}, {\"key\": \"reMiDT97288\", \"type\": \"personnel\", \"title\": \"Pirvate2\", \"linKey\": [{\"key\": \"caAAYg11545\", \"title\": \"探知科技\"}, {\"key\": \"reMiDT97288\", \"type\": \"personnel\", \"title\": \"Pirvate2\"}]}, {\"key\": \"ZkmXpK40126\", \"type\": \"personnel\", \"title\": \"侃大山\", \"linKey\": [{\"key\": \"caAAYg11545\", \"title\": \"探知科技\"}, {\"key\": \"ZkmXpK40126\", \"type\": \"personnel\", \"title\": \"侃大山\"}]}, {\"key\": \"vDonno106815\", \"type\": \"personnel\", \"title\": \"用户_15939965336\", \"linKey\": [{\"key\": \"caAAYg11545\", \"title\": \"探知科技\"}, {\"key\": \"vDonno106815\", \"type\": \"personnel\", \"title\": \"用户_15939965336\"}]}, {\"key\": \"gcafR0173504\", \"type\": \"personnel\", \"title\": \"用户_17634766666\", \"linKey\": [{\"key\": \"caAAYg11545\", \"title\": \"探知科技\"}, {\"key\": \"gcafR0173504\", \"type\": \"personnel\", \"title\": \"用户_17634766666\"}]}, {\"key\": \"E8GPcE135396\", \"type\": \"personnel\", \"title\": \"132\", \"linKey\": [{\"key\": \"caAAYg11545\", \"title\": \"探知科技\"}, {\"key\": \"E8GPcE135396\", \"type\": \"personnel\", \"title\": \"132\"}]}]}, \"cc_user_ids\": [1, 5, 10, 4, 11, 18, 14]}', '{\"duty_user_id\": 4, \"duty_form_data\": {\"key\": \"rzw9Rv40126\", \"title\": \"侃大山\"}, \"duty_receive_state\": \"已接收\"}', NULL, 0, '2019-04-15 18:06:24', '2019-04-15 18:06:24');
INSERT INTO `pst` VALUES (8, 7, 0, 0, 27, 0, '待接收', 1, 0, '[{\"type\": \"SELECT\", \"field\": {\"name\": \"category\", \"label\": \"分类\", \"required\": false}, \"value\": \"概算\"}, {\"type\": \"SELECT\", \"field\": {\"name\": \"project_category\", \"label\": \"工程分类\", \"required\": false}, \"value\": \"园林\"}, {\"type\": \"INPUT\", \"field\": {\"name\": \"project_name\", \"label\": \"项目名称\", \"required\": false}, \"value\": \"葛天源k期\"}, {\"type\": \"INPUT\", \"field\": {\"name\": \"amount_of_review\", \"label\": \"送审金额\", \"required\": false}, \"value\": \"5435435434534\"}, {\"type\": \"SELECT\", \"field\": {\"name\": \"action_label\", \"label\": \"标签\", \"required\": false}, \"value\": \"12222\"}]', '{\"category\": \"概算\", \"action_label\": \"12222\", \"project_name\": \"葛天源k期\", \"amount_of_review\": \"5435435434534\", \"project_category\": \"园林\"}', '[]', NULL, '[]', NULL, NULL, NULL, NULL, NULL, NULL, 0, '2019-04-15 18:06:24', '2019-04-15 18:06:24');
INSERT INTO `pst` VALUES (9, 7, 0, 0, 0, 22, '待接收', 1, 0, '[{\"type\": \"SELECT\", \"field\": {\"name\": \"category\", \"label\": \"分类\", \"required\": false}, \"value\": \"概算\"}, {\"type\": \"SELECT\", \"field\": {\"name\": \"project_category\", \"label\": \"工程分类\", \"required\": false}, \"value\": \"园林\"}, {\"type\": \"INPUT\", \"field\": {\"name\": \"project_name\", \"label\": \"项目名称\", \"required\": false}, \"value\": \"葛天源k期\"}, {\"type\": \"INPUT\", \"field\": {\"name\": \"amount_of_review\", \"label\": \"送审金额\", \"required\": false}, \"value\": \"5435435434534\"}, {\"type\": \"SELECT\", \"field\": {\"name\": \"action_label\", \"label\": \"标签\", \"required\": false}, \"value\": \"12222\"}]', '{\"category\": \"概算\", \"action_label\": \"12222\", \"project_name\": \"葛天源k期\", \"amount_of_review\": \"5435435434534\", \"project_category\": \"园林\"}', '[]', NULL, '[]', NULL, NULL, NULL, NULL, NULL, NULL, 0, '2019-04-15 18:06:26', '2019-04-15 18:06:26');

-- ----------------------------
-- Table structure for pst_cc_record
-- ----------------------------
DROP TABLE IF EXISTS `pst_cc_record`;
CREATE TABLE `pst_cc_record`  (
  `pst_id` int(10) UNSIGNED NOT NULL COMMENT '评审通id',
  `user_id` int(10) UNSIGNED NOT NULL COMMENT '抄送用户的',
  `company_id` int(10) UNSIGNED NOT NULL COMMENT '哪个公司抄送的'
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pst_cc_record
-- ----------------------------
INSERT INTO `pst_cc_record` VALUES (1, 1, 1);
INSERT INTO `pst_cc_record` VALUES (4, 14, 1);
INSERT INTO `pst_cc_record` VALUES (4, 5, 1);
INSERT INTO `pst_cc_record` VALUES (4, 1, 1);
INSERT INTO `pst_cc_record` VALUES (4, 10, 1);
INSERT INTO `pst_cc_record` VALUES (4, 4, 1);
INSERT INTO `pst_cc_record` VALUES (5, 1, 1);
INSERT INTO `pst_cc_record` VALUES (5, 5, 1);
INSERT INTO `pst_cc_record` VALUES (7, 1, 1);
INSERT INTO `pst_cc_record` VALUES (7, 5, 1);
INSERT INTO `pst_cc_record` VALUES (7, 10, 1);
INSERT INTO `pst_cc_record` VALUES (7, 4, 1);
INSERT INTO `pst_cc_record` VALUES (7, 11, 1);
INSERT INTO `pst_cc_record` VALUES (7, 18, 1);
INSERT INTO `pst_cc_record` VALUES (7, 14, 1);
INSERT INTO `pst_cc_record` VALUES (1, 1, 1);
INSERT INTO `pst_cc_record` VALUES (1, 1, 1);
INSERT INTO `pst_cc_record` VALUES (2, 1, 1);
INSERT INTO `pst_cc_record` VALUES (1, 1, 1);
INSERT INTO `pst_cc_record` VALUES (2, 1, 1);
INSERT INTO `pst_cc_record` VALUES (2, 5, 1);
INSERT INTO `pst_cc_record` VALUES (2, 10, 1);
INSERT INTO `pst_cc_record` VALUES (2, 4, 1);
INSERT INTO `pst_cc_record` VALUES (2, 11, 1);
INSERT INTO `pst_cc_record` VALUES (2, 18, 1);
INSERT INTO `pst_cc_record` VALUES (2, 14, 1);
INSERT INTO `pst_cc_record` VALUES (2, 1, 1);
INSERT INTO `pst_cc_record` VALUES (2, 5, 1);
INSERT INTO `pst_cc_record` VALUES (2, 10, 1);
INSERT INTO `pst_cc_record` VALUES (2, 4, 1);
INSERT INTO `pst_cc_record` VALUES (2, 11, 1);
INSERT INTO `pst_cc_record` VALUES (2, 18, 1);
INSERT INTO `pst_cc_record` VALUES (2, 14, 1);
INSERT INTO `pst_cc_record` VALUES (1, 1, 1);
INSERT INTO `pst_cc_record` VALUES (1, 5, 1);
INSERT INTO `pst_cc_record` VALUES (1, 10, 1);
INSERT INTO `pst_cc_record` VALUES (1, 4, 1);
INSERT INTO `pst_cc_record` VALUES (1, 11, 1);
INSERT INTO `pst_cc_record` VALUES (1, 18, 1);
INSERT INTO `pst_cc_record` VALUES (1, 14, 1);
INSERT INTO `pst_cc_record` VALUES (7, 1, 1);
INSERT INTO `pst_cc_record` VALUES (7, 5, 1);
INSERT INTO `pst_cc_record` VALUES (7, 10, 1);
INSERT INTO `pst_cc_record` VALUES (7, 4, 1);
INSERT INTO `pst_cc_record` VALUES (7, 11, 1);
INSERT INTO `pst_cc_record` VALUES (7, 18, 1);
INSERT INTO `pst_cc_record` VALUES (7, 14, 1);

-- ----------------------------
-- Table structure for pst_form_data
-- ----------------------------
DROP TABLE IF EXISTS `pst_form_data`;
CREATE TABLE `pst_form_data`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` int(10) UNSIGNED NOT NULL COMMENT '所属企业id',
  `service_department` json NOT NULL COMMENT '送审业务负责科室标签json',
  `action_label` json NOT NULL COMMENT '行为标签json数据',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pst_form_data
-- ----------------------------
INSERT INTO `pst_form_data` VALUES (1, 1, '[\"66666666666\", \"...66666dasd\"]', '[\"火速处理\", \"dsadsadas\", \"12222\"]');
INSERT INTO `pst_form_data` VALUES (2, 24, '[\"概算一科\", \"二科\", \"三科\", \"四科\"]', '[\"尽情发挥\", \"紧急\", \"十万火急\"]');
INSERT INTO `pst_form_data` VALUES (4, 27, '[\"概算一科\", \"二科\", \"三科\", \"四科\"]', '[\"尽情发挥\", \"紧急\", \"十万火急\"]');
INSERT INTO `pst_form_data` VALUES (5, 28, '[\"概算一科\", \"二科\", \"三科\", \"四科\"]', '[\"尽情发挥\", \"紧急\", \"十万火急\"]');

-- ----------------------------
-- Table structure for pst_operate_record
-- ----------------------------
DROP TABLE IF EXISTS `pst_operate_record`;
CREATE TABLE `pst_operate_record`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pst_id` int(10) UNSIGNED NOT NULL COMMENT '评审通id',
  `company_id` int(10) UNSIGNED NOT NULL COMMENT '所属企业的id',
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '操作标识',
  `operate_user_id` int(10) UNSIGNED NOT NULL COMMENT '操作用户id',
  `info` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '操作详情信息',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 15 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pst_operate_record
-- ----------------------------
INSERT INTO `pst_operate_record` VALUES (1, 1, 1, '发起评审', 19, '用户_15100000001,发起评审', '2019-04-13 18:24:56', '2019-04-13 18:24:56');
INSERT INTO `pst_operate_record` VALUES (2, 1, 1, '通过发起评审审核', 19, '评审发起审批通过', '2019-04-13 18:24:56', '2019-04-13 18:24:56');
INSERT INTO `pst_operate_record` VALUES (3, 2, 1, '发起评审', 1, 'Pirvate,发起评审', '2019-04-13 18:29:58', '2019-04-13 18:29:58');
INSERT INTO `pst_operate_record` VALUES (4, 2, 1, '接收负责人', 1, 'Pirvate,接收了负责人', '2019-04-13 18:30:05', '2019-04-13 18:30:05');
INSERT INTO `pst_operate_record` VALUES (5, 2, 1, '通过发起评审审核', 1, '评审发起审批通过', '2019-04-13 18:30:11', '2019-04-13 18:30:11');
INSERT INTO `pst_operate_record` VALUES (6, 1, 1, '接收负责人', 4, '侃大山,接收了负责人', '2019-04-13 18:32:03', '2019-04-13 18:32:03');
INSERT INTO `pst_operate_record` VALUES (7, 1, 1, '接收参与人', 4, '侃大山,接收了参与人', '2019-04-15 10:51:57', '2019-04-15 10:51:57');
INSERT INTO `pst_operate_record` VALUES (8, 4, 1, '发起评审', 4, '侃大山,发起评审', '2019-04-15 17:56:32', '2019-04-15 17:56:32');
INSERT INTO `pst_operate_record` VALUES (9, 4, 1, '接收负责人', 4, '侃大山,接收了负责人', '2019-04-15 17:56:38', '2019-04-15 17:56:38');
INSERT INTO `pst_operate_record` VALUES (10, 6, 1, '发起评审', 1, 'Pirvate,发起评审', '2019-04-15 18:04:56', '2019-04-15 18:04:56');
INSERT INTO `pst_operate_record` VALUES (11, 6, 1, '创建审批', 1, 'Pirvate,发起审批', '2019-04-15 18:05:03', '2019-04-15 18:05:03');
INSERT INTO `pst_operate_record` VALUES (12, 7, 1, '发起评审', 4, '侃大山,发起评审', '2019-04-15 18:06:24', '2019-04-15 18:06:24');
INSERT INTO `pst_operate_record` VALUES (13, 7, 1, '接收负责人', 4, '侃大山,接收了负责人', '2019-04-15 18:06:24', '2019-04-15 18:06:24');
INSERT INTO `pst_operate_record` VALUES (14, 7, 1, '通过发起评审审核', 4, '评审发起审批通过', '2019-04-15 18:06:28', '2019-04-15 18:06:28');

-- ----------------------------
-- Table structure for pst_process_template
-- ----------------------------
DROP TABLE IF EXISTS `pst_process_template`;
CREATE TABLE `pst_process_template`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` int(10) UNSIGNED NOT NULL COMMENT '所属企业的id',
  `process_type_id` int(10) UNSIGNED NOT NULL COMMENT '流程所属类型id',
  `is_show` smallint(5) UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否启用',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '流程模板名称',
  `process_template` json NOT NULL COMMENT '流程模板数据',
  `per` json NOT NULL COMMENT '可见人相关信息',
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '流程描述',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pst_process_template
-- ----------------------------
INSERT INTO `pst_process_template` VALUES (1, 0, 3, 1, '44444', '[{\"type\": \"countersign\", \"checkedInfo\": {\"checkedKeys\": [\"Ly5fEh287828\", \"qrxdXi306882\", \"7BdmJp30599\", \"7BdmJp21072\"], \"checkedPersonnels\": [{\"key\": \"7BdmJp30599\", \"type\": \"personnel\", \"title\": \"狗蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"Ly5fEh287828\", \"type\": \"department\", \"title\": \"财务部\"}, {\"key\": \"qrxdXi306882\", \"type\": \"department\", \"title\": \"小财务部\"}, {\"key\": \"7BdmJp30599\", \"type\": \"personnel\", \"title\": \"狗蛋\"}]}, {\"key\": \"7BdmJp21072\", \"type\": \"personnel\", \"title\": \"狗蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"Ly5fEh287828\", \"type\": \"department\", \"title\": \"财务部\"}, {\"key\": \"7BdmJp21072\", \"type\": \"personnel\", \"title\": \"狗蛋\"}]}]}}, {\"type\": \"normal\", \"checkedInfo\": {\"checkedKeys\": [\"7BdmJp106815\"], \"checkedPersonnels\": [{\"key\": \"7BdmJp106815\", \"type\": \"personnel\", \"title\": \"xxx\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"7BdmJp106815\", \"type\": \"personnel\", \"title\": \"xxx\"}]}]}}]', '{\"staffId\": [14, 5, 1, 10, 4], \"departmentId\": [\"UNv8kB11545\", \"bRYxTq21072\", \"WS7M4l30599\", \"bIgsII87761\", \"7abYUd97288\", \"KcLxyk40126\", \"Pps90u49653\", \"QK8C1u59180\", \"P8353K68707\", \"pyi1au78234\"], \"selectedPersonnelInfo\": {\"checkedKeys\": [\"UNv8kB11545\", \"bRYxTq21072\", \"WS7M4l30599\", \"bIgsII87761\", \"7abYUd97288\", \"tMUMgY135396\", \"Ry31qw49653\", \"MYz62Q11545\", \"KcLxyk40126\", \"Pps90u49653\", \"QK8C1u59180\", \"P8353K68707\", \"pyi1au78234\", \"ZGP6L297288\", \"0p8ADB40126\"], \"checkedPersonnels\": [{\"key\": \"tMUMgY135396\", \"type\": \"personnel\", \"title\": \"132\", \"linKey\": [{\"key\": \"UNv8kB11545\", \"title\": \"探知科技\"}, {\"key\": \"bRYxTq21072\", \"type\": \"department\", \"title\": \"人事部\"}, {\"key\": \"tMUMgY135396\", \"type\": \"personnel\", \"title\": \"132\"}]}, {\"key\": \"Ry31qw49653\", \"type\": \"personnel\", \"title\": \"Soul\", \"linKey\": [{\"key\": \"UNv8kB11545\", \"title\": \"探知科技\"}, {\"key\": \"bRYxTq21072\", \"type\": \"department\", \"title\": \"人事部\"}, {\"key\": \"Ry31qw49653\", \"type\": \"personnel\", \"title\": \"Soul\"}]}, {\"key\": \"MYz62Q11545\", \"type\": \"personnel\", \"title\": \"Pirvate\", \"linKey\": [{\"key\": \"UNv8kB11545\", \"title\": \"探知科技\"}, {\"key\": \"bRYxTq21072\", \"type\": \"department\", \"title\": \"人事部\"}, {\"key\": \"MYz62Q11545\", \"type\": \"personnel\", \"title\": \"Pirvate\"}]}, {\"key\": \"ZGP6L297288\", \"type\": \"personnel\", \"title\": \"Pirvate2\", \"linKey\": [{\"key\": \"UNv8kB11545\", \"title\": \"探知科技\"}, {\"key\": \"ZGP6L297288\", \"type\": \"personnel\", \"title\": \"Pirvate2\"}]}, {\"key\": \"0p8ADB40126\", \"type\": \"personnel\", \"title\": \"侃大山\", \"linKey\": [{\"key\": \"UNv8kB11545\", \"title\": \"探知科技\"}, {\"key\": \"0p8ADB40126\", \"type\": \"personnel\", \"title\": \"侃大山\"}]}]}}', '劳动安全卫生评审费计算器', NULL, '2019-03-09 14:04:06');
INSERT INTO `pst_process_template` VALUES (2, 0, 5, 1, 'dasdas', '[{\"type\": \"countersign\", \"checkedInfo\": {\"checkedKeys\": [\"35796314\", \"Ly5fEh287828\", \"qrxdXi306882\", \"7BdmJp30599\", \"7BdmJp21072\", \"8CoSqT297355\", \"VJbKqK316409\", \"ebhBuf325936\", \"vkoGt5335463\", \"7BdmJp40126\", \"7BdmJp11545\", \"7BdmJp106815\"], \"checkedPersonnels\": [{\"key\": \"7BdmJp30599\", \"type\": \"personnel\", \"title\": \"狗蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"Ly5fEh287828\", \"type\": \"department\", \"title\": \"财务部\"}, {\"key\": \"qrxdXi306882\", \"type\": \"department\", \"title\": \"小财务部\"}, {\"key\": \"7BdmJp30599\", \"type\": \"personnel\", \"title\": \"狗蛋\"}]}, {\"key\": \"7BdmJp21072\", \"type\": \"personnel\", \"title\": \"狗蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"Ly5fEh287828\", \"type\": \"department\", \"title\": \"财务部\"}, {\"key\": \"7BdmJp21072\", \"type\": \"personnel\", \"title\": \"狗蛋\"}]}, {\"key\": \"7BdmJp40126\", \"type\": \"personnel\", \"title\": \"大锤\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"8CoSqT297355\", \"type\": \"department\", \"title\": \"人事部\"}, {\"key\": \"7BdmJp40126\", \"type\": \"personnel\", \"title\": \"大锤\"}]}, {\"key\": \"7BdmJp11545\", \"type\": \"personnel\", \"title\": \"黑蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"7BdmJp11545\", \"type\": \"personnel\", \"title\": \"黑蛋\"}]}, {\"key\": \"7BdmJp106815\", \"type\": \"personnel\", \"title\": \"xxx\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"7BdmJp106815\", \"type\": \"personnel\", \"title\": \"xxx\"}]}]}}, {\"type\": \"normal\", \"checkedInfo\": {\"checkedKeys\": [\"7BdmJp11545\"], \"checkedPersonnels\": [{\"key\": \"7BdmJp11545\", \"type\": \"personnel\", \"title\": \"黑蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"7BdmJp11545\", \"type\": \"personnel\", \"title\": \"黑蛋\"}]}]}}, {\"type\": \"orSign\", \"checkedInfo\": {\"checkedKeys\": [\"35796314\", \"Ly5fEh287828\", \"qrxdXi306882\", \"7BdmJp30599\", \"7BdmJp21072\", \"8CoSqT297355\", \"VJbKqK316409\", \"ebhBuf325936\", \"vkoGt5335463\", \"7BdmJp40126\", \"7BdmJp11545\", \"7BdmJp106815\"], \"checkedPersonnels\": [{\"key\": \"7BdmJp30599\", \"type\": \"personnel\", \"title\": \"狗蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"Ly5fEh287828\", \"type\": \"department\", \"title\": \"财务部\"}, {\"key\": \"qrxdXi306882\", \"type\": \"department\", \"title\": \"小财务部\"}, {\"key\": \"7BdmJp30599\", \"type\": \"personnel\", \"title\": \"狗蛋\"}]}, {\"key\": \"7BdmJp21072\", \"type\": \"personnel\", \"title\": \"狗蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"Ly5fEh287828\", \"type\": \"department\", \"title\": \"财务部\"}, {\"key\": \"7BdmJp21072\", \"type\": \"personnel\", \"title\": \"狗蛋\"}]}, {\"key\": \"7BdmJp40126\", \"type\": \"personnel\", \"title\": \"大锤\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"8CoSqT297355\", \"type\": \"department\", \"title\": \"人事部\"}, {\"key\": \"7BdmJp40126\", \"type\": \"personnel\", \"title\": \"大锤\"}]}, {\"key\": \"7BdmJp11545\", \"type\": \"personnel\", \"title\": \"黑蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"7BdmJp11545\", \"type\": \"personnel\", \"title\": \"黑蛋\"}]}, {\"key\": \"7BdmJp106815\", \"type\": \"personnel\", \"title\": \"xxx\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"7BdmJp106815\", \"type\": \"personnel\", \"title\": \"xxx\"}]}]}}]', '\"all\"', 'dsadasdsa', '2019-03-11 10:34:05', '2019-03-15 09:09:49');
INSERT INTO `pst_process_template` VALUES (3, 1, 5, 0, '又一个流程', '[{\"type\": \"countersign\", \"checkedInfo\": {\"checkedKeys\": [\"Ly5fEh287828\", \"qrxdXi306882\", \"7BdmJp30599\", \"7BdmJp21072\"], \"checkedPersonnels\": [{\"key\": \"7BdmJp30599\", \"type\": \"personnel\", \"title\": \"狗蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"Ly5fEh287828\", \"type\": \"department\", \"title\": \"财务部\"}, {\"key\": \"qrxdXi306882\", \"type\": \"department\", \"title\": \"小财务部\"}, {\"key\": \"7BdmJp30599\", \"type\": \"personnel\", \"title\": \"狗蛋\"}]}, {\"key\": \"7BdmJp21072\", \"type\": \"personnel\", \"title\": \"狗蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"Ly5fEh287828\", \"type\": \"department\", \"title\": \"财务部\"}, {\"key\": \"7BdmJp21072\", \"type\": \"personnel\", \"title\": \"狗蛋\"}]}]}}]', '\"all\"', '山豆根山豆根给', '2019-03-11 10:34:05', '2019-03-15 09:44:05');
INSERT INTO `pst_process_template` VALUES (4, 1, 8, 1, '我的模板', '[{\"type\": \"countersign\", \"checkedInfo\": {\"checkedKeys\": [\"Ly5fEh287828\", \"qrxdXi306882\", \"7BdmJp30599\", \"7BdmJp21072\"], \"checkedPersonnels\": [{\"key\": \"7BdmJp30599\", \"type\": \"personnel\", \"title\": \"狗蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"Ly5fEh287828\", \"type\": \"department\", \"title\": \"财务部\"}, {\"key\": \"qrxdXi306882\", \"type\": \"department\", \"title\": \"小财务部\"}, {\"key\": \"7BdmJp30599\", \"type\": \"personnel\", \"title\": \"狗蛋\"}]}, {\"key\": \"7BdmJp21072\", \"type\": \"personnel\", \"title\": \"狗蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"Ly5fEh287828\", \"type\": \"department\", \"title\": \"财务部\"}, {\"key\": \"7BdmJp21072\", \"type\": \"personnel\", \"title\": \"狗蛋\"}]}]}}]', '{\"staffId\": [14, 5, 1, 10, 4], \"departmentId\": [\"UNv8kB11545\", \"bRYxTq21072\", \"WS7M4l30599\", \"bIgsII87761\", \"7abYUd97288\", \"KcLxyk40126\", \"Pps90u49653\", \"QK8C1u59180\", \"P8353K68707\", \"pyi1au78234\"], \"selectedPersonnelInfo\": {\"checkedKeys\": [\"UNv8kB11545\", \"bRYxTq21072\", \"WS7M4l30599\", \"bIgsII87761\", \"7abYUd97288\", \"tMUMgY135396\", \"Ry31qw49653\", \"MYz62Q11545\", \"KcLxyk40126\", \"Pps90u49653\", \"QK8C1u59180\", \"P8353K68707\", \"pyi1au78234\", \"ZGP6L297288\", \"0p8ADB40126\"], \"checkedPersonnels\": [{\"key\": \"tMUMgY135396\", \"type\": \"personnel\", \"title\": \"132\", \"linKey\": [{\"key\": \"UNv8kB11545\", \"title\": \"探知科技\"}, {\"key\": \"bRYxTq21072\", \"type\": \"department\", \"title\": \"人事部\"}, {\"key\": \"tMUMgY135396\", \"type\": \"personnel\", \"title\": \"132\"}]}, {\"key\": \"Ry31qw49653\", \"type\": \"personnel\", \"title\": \"Soul\", \"linKey\": [{\"key\": \"UNv8kB11545\", \"title\": \"探知科技\"}, {\"key\": \"bRYxTq21072\", \"type\": \"department\", \"title\": \"人事部\"}, {\"key\": \"Ry31qw49653\", \"type\": \"personnel\", \"title\": \"Soul\"}]}, {\"key\": \"MYz62Q11545\", \"type\": \"personnel\", \"title\": \"Pirvate\", \"linKey\": [{\"key\": \"UNv8kB11545\", \"title\": \"探知科技\"}, {\"key\": \"bRYxTq21072\", \"type\": \"department\", \"title\": \"人事部\"}, {\"key\": \"MYz62Q11545\", \"type\": \"personnel\", \"title\": \"Pirvate\"}]}, {\"key\": \"ZGP6L297288\", \"type\": \"personnel\", \"title\": \"Pirvate2\", \"linKey\": [{\"key\": \"UNv8kB11545\", \"title\": \"探知科技\"}, {\"key\": \"ZGP6L297288\", \"type\": \"personnel\", \"title\": \"Pirvate2\"}]}, {\"key\": \"0p8ADB40126\", \"type\": \"personnel\", \"title\": \"侃大山\", \"linKey\": [{\"key\": \"UNv8kB11545\", \"title\": \"探知科技\"}, {\"key\": \"0p8ADB40126\", \"type\": \"personnel\", \"title\": \"侃大山\"}]}]}}', '劳动安全卫生评审费计算器', '2019-03-11 10:43:13', '2019-03-22 10:22:56');
INSERT INTO `pst_process_template` VALUES (5, 1, 0, 1, '造价咨询费', '[{\"type\": \"countersign\", \"checkedInfo\": {\"checkedKeys\": [\"P7hXvN49653\", \"1Gqc13135396\"], \"checkedPersonnels\": [{\"key\": \"P7hXvN49653\", \"type\": \"personnel\", \"title\": \"Soul\", \"linKey\": [{\"key\": \"joVTK711545\", \"title\": \"探知科技\"}, {\"key\": \"P7hXvN49653\", \"type\": \"personnel\", \"title\": \"Soul\"}]}, {\"key\": \"1Gqc13135396\", \"type\": \"personnel\", \"title\": \"132\", \"linKey\": [{\"key\": \"joVTK711545\", \"title\": \"探知科技\"}, {\"key\": \"1Gqc13135396\", \"type\": \"personnel\", \"title\": \"132\"}]}]}}]', '{\"staffId\": [14, 5, 1, 10, 4], \"departmentId\": [\"UNv8kB11545\", \"bRYxTq21072\", \"WS7M4l30599\", \"bIgsII87761\", \"7abYUd97288\", \"KcLxyk40126\", \"Pps90u49653\", \"QK8C1u59180\", \"P8353K68707\", \"pyi1au78234\"], \"selectedPersonnelInfo\": {\"checkedKeys\": [\"UNv8kB11545\", \"bRYxTq21072\", \"WS7M4l30599\", \"bIgsII87761\", \"7abYUd97288\", \"tMUMgY135396\", \"Ry31qw49653\", \"MYz62Q11545\", \"KcLxyk40126\", \"Pps90u49653\", \"QK8C1u59180\", \"P8353K68707\", \"pyi1au78234\", \"ZGP6L297288\", \"0p8ADB40126\"], \"checkedPersonnels\": [{\"key\": \"tMUMgY135396\", \"type\": \"personnel\", \"title\": \"132\", \"linKey\": [{\"key\": \"UNv8kB11545\", \"title\": \"探知科技\"}, {\"key\": \"bRYxTq21072\", \"type\": \"department\", \"title\": \"人事部\"}, {\"key\": \"tMUMgY135396\", \"type\": \"personnel\", \"title\": \"132\"}]}, {\"key\": \"Ry31qw49653\", \"type\": \"personnel\", \"title\": \"Soul\", \"linKey\": [{\"key\": \"UNv8kB11545\", \"title\": \"探知科技\"}, {\"key\": \"bRYxTq21072\", \"type\": \"department\", \"title\": \"人事部\"}, {\"key\": \"Ry31qw49653\", \"type\": \"personnel\", \"title\": \"Soul\"}]}, {\"key\": \"MYz62Q11545\", \"type\": \"personnel\", \"title\": \"Pirvate\", \"linKey\": [{\"key\": \"UNv8kB11545\", \"title\": \"探知科技\"}, {\"key\": \"bRYxTq21072\", \"type\": \"department\", \"title\": \"人事部\"}, {\"key\": \"MYz62Q11545\", \"type\": \"personnel\", \"title\": \"Pirvate\"}]}, {\"key\": \"ZGP6L297288\", \"type\": \"personnel\", \"title\": \"Pirvate2\", \"linKey\": [{\"key\": \"UNv8kB11545\", \"title\": \"探知科技\"}, {\"key\": \"ZGP6L297288\", \"type\": \"personnel\", \"title\": \"Pirvate2\"}]}, {\"key\": \"0p8ADB40126\", \"type\": \"personnel\", \"title\": \"侃大山\", \"linKey\": [{\"key\": \"UNv8kB11545\", \"title\": \"探知科技\"}, {\"key\": \"0p8ADB40126\", \"type\": \"personnel\", \"title\": \"侃大山\"}]}]}}', '', '2019-03-22 10:23:23', '2019-03-22 10:23:23');
INSERT INTO `pst_process_template` VALUES (6, 1, 3, 1, '我的模板', '[{\"type\": \"countersign\", \"checkedInfo\": {\"checkedKeys\": [\"bRYxTq21072\", \"WS7M4l30599\", \"bIgsII87761\", \"7abYUd97288\", \"tMUMgY135396\", \"Ry31qw49653\", \"MYz62Q11545\", \"UNv8kB11545\", \"KcLxyk40126\", \"Pps90u49653\", \"QK8C1u59180\", \"P8353K68707\", \"pyi1au78234\", \"ZGP6L297288\", \"0p8ADB40126\"], \"checkedPersonnels\": [{\"key\": \"tMUMgY135396\", \"type\": \"personnel\", \"title\": \"132\", \"linKey\": [{\"key\": \"UNv8kB11545\", \"title\": \"探知科技\"}, {\"key\": \"bRYxTq21072\", \"type\": \"department\", \"title\": \"人事部\"}, {\"key\": \"tMUMgY135396\", \"type\": \"personnel\", \"title\": \"132\"}]}, {\"key\": \"Ry31qw49653\", \"type\": \"personnel\", \"title\": \"Soul\", \"linKey\": [{\"key\": \"UNv8kB11545\", \"title\": \"探知科技\"}, {\"key\": \"bRYxTq21072\", \"type\": \"department\", \"title\": \"人事部\"}, {\"key\": \"Ry31qw49653\", \"type\": \"personnel\", \"title\": \"Soul\"}]}, {\"key\": \"MYz62Q11545\", \"type\": \"personnel\", \"title\": \"Pirvate\", \"linKey\": [{\"key\": \"UNv8kB11545\", \"title\": \"探知科技\"}, {\"key\": \"bRYxTq21072\", \"type\": \"department\", \"title\": \"人事部\"}, {\"key\": \"MYz62Q11545\", \"type\": \"personnel\", \"title\": \"Pirvate\"}]}, {\"key\": \"ZGP6L297288\", \"type\": \"personnel\", \"title\": \"Pirvate2\", \"linKey\": [{\"key\": \"UNv8kB11545\", \"title\": \"探知科技\"}, {\"key\": \"ZGP6L297288\", \"type\": \"personnel\", \"title\": \"Pirvate2\"}]}, {\"key\": \"0p8ADB40126\", \"type\": \"personnel\", \"title\": \"侃大山\", \"linKey\": [{\"key\": \"UNv8kB11545\", \"title\": \"探知科技\"}, {\"key\": \"0p8ADB40126\", \"type\": \"personnel\", \"title\": \"侃大山\"}]}]}}]', '{\"staffId\": [14, 5, 1, 10, 4], \"departmentId\": [\"UNv8kB11545\", \"bRYxTq21072\", \"WS7M4l30599\", \"bIgsII87761\", \"7abYUd97288\", \"KcLxyk40126\", \"Pps90u49653\", \"QK8C1u59180\", \"P8353K68707\", \"pyi1au78234\"], \"selectedPersonnelInfo\": {\"checkedKeys\": [\"UNv8kB11545\", \"bRYxTq21072\", \"WS7M4l30599\", \"bIgsII87761\", \"7abYUd97288\", \"tMUMgY135396\", \"Ry31qw49653\", \"MYz62Q11545\", \"KcLxyk40126\", \"Pps90u49653\", \"QK8C1u59180\", \"P8353K68707\", \"pyi1au78234\", \"ZGP6L297288\", \"0p8ADB40126\"], \"checkedPersonnels\": [{\"key\": \"tMUMgY135396\", \"type\": \"personnel\", \"title\": \"132\", \"linKey\": [{\"key\": \"UNv8kB11545\", \"title\": \"探知科技\"}, {\"key\": \"bRYxTq21072\", \"type\": \"department\", \"title\": \"人事部\"}, {\"key\": \"tMUMgY135396\", \"type\": \"personnel\", \"title\": \"132\"}]}, {\"key\": \"Ry31qw49653\", \"type\": \"personnel\", \"title\": \"Soul\", \"linKey\": [{\"key\": \"UNv8kB11545\", \"title\": \"探知科技\"}, {\"key\": \"bRYxTq21072\", \"type\": \"department\", \"title\": \"人事部\"}, {\"key\": \"Ry31qw49653\", \"type\": \"personnel\", \"title\": \"Soul\"}]}, {\"key\": \"MYz62Q11545\", \"type\": \"personnel\", \"title\": \"Pirvate\", \"linKey\": [{\"key\": \"UNv8kB11545\", \"title\": \"探知科技\"}, {\"key\": \"bRYxTq21072\", \"type\": \"department\", \"title\": \"人事部\"}, {\"key\": \"MYz62Q11545\", \"type\": \"personnel\", \"title\": \"Pirvate\"}]}, {\"key\": \"ZGP6L297288\", \"type\": \"personnel\", \"title\": \"Pirvate2\", \"linKey\": [{\"key\": \"UNv8kB11545\", \"title\": \"探知科技\"}, {\"key\": \"ZGP6L297288\", \"type\": \"personnel\", \"title\": \"Pirvate2\"}]}, {\"key\": \"0p8ADB40126\", \"type\": \"personnel\", \"title\": \"侃大山\", \"linKey\": [{\"key\": \"UNv8kB11545\", \"title\": \"探知科技\"}, {\"key\": \"0p8ADB40126\", \"type\": \"personnel\", \"title\": \"侃大山\"}]}]}}', '手动阀手动阀手动阀', '2019-04-04 17:29:14', '2019-04-04 17:29:14');

-- ----------------------------
-- Table structure for pst_process_type
-- ----------------------------
DROP TABLE IF EXISTS `pst_process_type`;
CREATE TABLE `pst_process_type`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '类型名称',
  `company_id` int(10) UNSIGNED NOT NULL COMMENT '所属公司id',
  `sequence` int(11) NOT NULL DEFAULT 0 COMMENT '排序,根据此字段查找排序,针对于用户拖拽后的排序更新',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 11 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pst_process_type
-- ----------------------------
INSERT INTO `pst_process_type` VALUES (2, '流程第一排打算', 1, 0, '2019-03-01 11:57:48', '2019-03-08 15:16:49');
INSERT INTO `pst_process_type` VALUES (3, '流程模板分类', 1, 2, '2019-03-04 15:29:23', '2019-03-06 16:03:05');
INSERT INTO `pst_process_type` VALUES (5, '好的啊好的啊', 1, 0, '2019-03-08 14:46:18', '2019-03-08 16:32:09');
INSERT INTO `pst_process_type` VALUES (7, 'dsadsadas', 1, 0, '2019-03-22 10:16:38', '2019-03-22 10:16:38');
INSERT INTO `pst_process_type` VALUES (8, '999999999', 1, 0, '2019-03-22 10:22:22', '2019-03-22 10:22:22');
INSERT INTO `pst_process_type` VALUES (9, '34534', 1, 0, '2019-03-22 10:23:21', '2019-03-22 10:23:21');

-- ----------------------------
-- Table structure for pst_report_number
-- ----------------------------
DROP TABLE IF EXISTS `pst_report_number`;
CREATE TABLE `pst_report_number`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `company_id` int(10) UNSIGNED NOT NULL COMMENT '所属企业id',
  `rule_data` json NOT NULL COMMENT '所属企业id',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pst_report_number
-- ----------------------------
INSERT INTO `pst_report_number` VALUES (1, 1, '{\"join_char\": \"-\", \"rule_data\": [{\"type\": \"Label\", \"value\": \"TZ\"}, {\"type\": \"Data\", \"value\": \"Ymd\"}, {\"type\": \"Label\", \"value\": \"BQ\"}, {\"step\": 2, \"type\": \"Increasement\", \"length\": 6, \"begin_number\": 15}, {\"step\": 2, \"type\": \"Increasement\", \"length\": 6, \"begin_number\": 15}]}');

-- ----------------------------
-- Table structure for pst_self_related
-- ----------------------------
DROP TABLE IF EXISTS `pst_self_related`;
CREATE TABLE `pst_self_related`  (
  `target_pst_id` int(10) UNSIGNED NOT NULL COMMENT '目标评审通id',
  `related_pst_id` int(10) UNSIGNED NOT NULL COMMENT '所关联的评审通id'
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for pst_template
-- ----------------------------
DROP TABLE IF EXISTS `pst_template`;
CREATE TABLE `pst_template`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '评审通模板名称',
  `type_id` int(10) UNSIGNED NOT NULL COMMENT '对应的评审通模板类型id',
  `company_id` int(10) UNSIGNED NOT NULL COMMENT '所属公司id',
  `is_show` smallint(5) UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否启用',
  `need_approval` smallint(5) UNSIGNED NOT NULL COMMENT '相关操作是否需要审批',
  `form_template` json NOT NULL COMMENT '该模板的表单数据',
  `form_values` json NULL COMMENT '该模板所需要的数据 k-v数组',
  `process_template` json NULL COMMENT '审批人员流程数据',
  `approval_method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '流程类型:自由流程/固定流程',
  `cc_users` json NOT NULL COMMENT '抄送人员源数据',
  `per` json NOT NULL COMMENT '可见人员源数据',
  `users_info` json NOT NULL COMMENT '抄送，可见，....相关人员json数据',
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '描述信息',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pst_template
-- ----------------------------
INSERT INTO `pst_template` VALUES (1, '测试模板', 1, 0, 1, 1, '[{\"type\": \"INPUT\", \"field\": {\"name\": \"project_name\", \"label\": \"项目名称\", \"required\": false}}, {\"type\": \"SELECT\", \"field\": {\"name\": \"category\", \"label\": \"分类\", \"required\": false}}, {\"type\": \"SELECT\", \"field\": {\"name\": \"project_category\", \"label\": \"工程分类\", \"required\": false}}, {\"type\": \"INPUT\", \"field\": {\"name\": \"amount_of_review\", \"label\": \"送审金额\", \"required\": false}}, {\"type\": \"DATEPICKER\", \"field\": {\"name\": \"submit_time\", \"label\": \"送审时间\", \"required\": false}}, {\"type\": \"INPUT\", \"field\": {\"name\": \"approved_amount\", \"label\": \"审定金额\", \"required\": false}}, {\"type\": \"SELECT\", \"field\": {\"name\": \"service_department\", \"label\": \"送审业务负责科室\", \"required\": false}}, {\"type\": \"RADIO\", \"field\": {\"name\": \"limit_time\", \"label\": \"完成时间\", \"required\": false}}, {\"type\": \"SELECT\", \"field\": {\"name\": \"action_label\", \"label\": \"标签\", \"required\": false}}]', '[\"project_name\", \"category\", \"project_category\", \"amount_of_review\", \"submit_time\", \"approved_amount\", \"service_department\", \"limit_time\", \"action_label\"]', '[{\"type\": \"normal\", \"checkedInfo\": {\"checkedKeys\": [\"7BdmJp11545\"], \"checkedPersonnels\": [{\"key\": \"7BdmJp11545\", \"type\": \"personnel\", \"title\": \"黑蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"7BdmJp11545\", \"type\": \"personnel\", \"title\": \"黑蛋\"}]}]}}]', '固定流程', '{\"checkedKeys\": [\"7BdmJp11545\"], \"checkedPersonnels\": [{\"key\": \"7BdmJp11545\", \"type\": \"personnel\", \"title\": \"黑蛋\", \"linKey\": [{\"key\": \"35796314\", \"title\": \"探知科技\"}, {\"key\": \"7BdmJp11545\", \"type\": \"personnel\", \"title\": \"黑蛋\"}]}]}', '\"all\"', '[]', '一个测试模板', '2019-03-16 08:37:15', '2019-03-16 08:41:56');
INSERT INTO `pst_template` VALUES (2, '不需要评审', 2, 0, 1, 0, '[{\"type\": \"SELECT\", \"field\": {\"name\": \"project_category\", \"label\": \"工程分类\", \"required\": false}}, {\"type\": \"SELECT\", \"field\": {\"name\": \"category\", \"label\": \"分类\", \"required\": false}}]', '[\"project_category\", \"category\"]', '[]', '自由流程', '{\"checkedKeys\": [], \"checkedPersonnels\": []}', '{\"staffId\": [\"tMUMgY135396\", \"Ry31qw49653\", \"MYz62Q11545\", \"ZGP6L297288\", \"0p8ADB40126\"], \"departmentId\": [\"UNv8kB11545\", \"bRYxTq21072\", \"WS7M4l30599\", \"bIgsII87761\", \"7abYUd97288\", \"KcLxyk40126\", \"Pps90u49653\", \"QK8C1u59180\", \"P8353K68707\", \"pyi1au78234\"], \"selectedPersonnelInfo\": {\"checkedKeys\": [\"UNv8kB11545\", \"bRYxTq21072\", \"WS7M4l30599\", \"bIgsII87761\", \"7abYUd97288\", \"tMUMgY135396\", \"Ry31qw49653\", \"MYz62Q11545\", \"KcLxyk40126\", \"Pps90u49653\", \"QK8C1u59180\", \"P8353K68707\", \"pyi1au78234\", \"ZGP6L297288\", \"0p8ADB40126\"], \"checkedPersonnels\": [{\"key\": \"tMUMgY135396\", \"type\": \"personnel\", \"title\": \"132\", \"linKey\": [{\"key\": \"UNv8kB11545\", \"title\": \"探知科技\"}, {\"key\": \"bRYxTq21072\", \"type\": \"department\", \"title\": \"人事部\"}, {\"key\": \"tMUMgY135396\", \"type\": \"personnel\", \"title\": \"132\"}]}, {\"key\": \"Ry31qw49653\", \"type\": \"personnel\", \"title\": \"Soul\", \"linKey\": [{\"key\": \"UNv8kB11545\", \"title\": \"探知科技\"}, {\"key\": \"bRYxTq21072\", \"type\": \"department\", \"title\": \"人事部\"}, {\"key\": \"Ry31qw49653\", \"type\": \"personnel\", \"title\": \"Soul\"}]}, {\"key\": \"MYz62Q11545\", \"type\": \"personnel\", \"title\": \"Pirvate\", \"linKey\": [{\"key\": \"UNv8kB11545\", \"title\": \"探知科技\"}, {\"key\": \"bRYxTq21072\", \"type\": \"department\", \"title\": \"人事部\"}, {\"key\": \"MYz62Q11545\", \"type\": \"personnel\", \"title\": \"Pirvate\"}]}, {\"key\": \"ZGP6L297288\", \"type\": \"personnel\", \"title\": \"Pirvate2\", \"linKey\": [{\"key\": \"UNv8kB11545\", \"title\": \"探知科技\"}, {\"key\": \"ZGP6L297288\", \"type\": \"personnel\", \"title\": \"Pirvate2\"}]}, {\"key\": \"0p8ADB40126\", \"type\": \"personnel\", \"title\": \"侃大山\", \"linKey\": [{\"key\": \"UNv8kB11545\", \"title\": \"探知科技\"}, {\"key\": \"0p8ADB40126\", \"type\": \"personnel\", \"title\": \"侃大山\"}]}]}}', '[]', '一个不需要评审的模板', '2019-03-20 14:44:31', '2019-04-03 17:22:53');
INSERT INTO `pst_template` VALUES (3, '哈哈哈哈哈哈哈', 3, 0, 1, 1, '[{\"type\": \"INPUT\", \"field\": {\"name\": \"approved_amount\", \"label\": \"审定金额\", \"required\": false}}]', '[\"approved_amount\"]', '[{\"type\": \"normal\", \"checkedInfo\": {\"checkedKeys\": [\"JfpEhz11545\"], \"checkedPersonnels\": [{\"key\": \"JfpEhz11545\", \"type\": \"personnel\", \"title\": \"Pirvate\", \"linKey\": [{\"key\": \"7rhRRz11545\", \"title\": \"探知科技\"}, {\"key\": \"ZGDe1b21072\", \"type\": \"department\", \"title\": \"人事部\"}, {\"key\": \"JfpEhz11545\", \"type\": \"personnel\", \"title\": \"Pirvate\"}]}]}}]', '固定流程', '{\"checkedKeys\": [\"JfpEhz11545\", \"COYxOj49653\"], \"checkedPersonnels\": [{\"key\": \"JfpEhz11545\", \"type\": \"personnel\", \"title\": \"Pirvate\", \"linKey\": [{\"key\": \"7rhRRz11545\", \"title\": \"探知科技\"}, {\"key\": \"ZGDe1b21072\", \"type\": \"department\", \"title\": \"人事部\"}, {\"key\": \"JfpEhz11545\", \"type\": \"personnel\", \"title\": \"Pirvate\"}]}, {\"key\": \"COYxOj49653\", \"type\": \"personnel\", \"title\": \"Soul\", \"linKey\": [{\"key\": \"7rhRRz11545\", \"title\": \"探知科技\"}, {\"key\": \"ZGDe1b21072\", \"type\": \"department\", \"title\": \"人事部\"}, {\"key\": \"COYxOj49653\", \"type\": \"personnel\", \"title\": \"Soul\"}]}]}', '{\"staffId\": [1, 5, 10, 4, 11, 18, 14], \"departmentId\": [\"caAAYg11545\", \"xVQtP021072\", \"Tu673A30599\", \"W9W3yA87761\", \"SGRamE97288\", \"UE7ha5154450\", \"wnRWc740126\", \"wWItJk163977\", \"OQDuqT49653\", \"82lYQA173504\", \"SXCQCZ59180\", \"uOjXhl68707\", \"8V0zna78234\", \"piGRjs106815\", \"MyFoH2116342\", \"xAT08w125869\", \"Y8Z4Pl135396\", \"XW0xPj144923\"], \"selectedPersonnelInfo\": {\"checkedKeys\": [\"caAAYg11545\", \"xVQtP021072\", \"Tu673A30599\", \"W9W3yA87761\", \"SGRamE97288\", \"UE7ha5154450\", \"sEDa7F11545\", \"wnRWc740126\", \"wWItJk163977\", \"OQDuqT49653\", \"82lYQA173504\", \"SXCQCZ59180\", \"k8XUlE49653\", \"uOjXhl68707\", \"8V0zna78234\", \"piGRjs106815\", \"MyFoH2116342\", \"xAT08w125869\", \"Y8Z4Pl135396\", \"XW0xPj144923\", \"reMiDT97288\", \"ZkmXpK40126\", \"vDonno106815\", \"gcafR0173504\", \"E8GPcE135396\"], \"checkedPersonnels\": [{\"key\": \"sEDa7F11545\", \"type\": \"personnel\", \"title\": \"Pirvate\", \"linKey\": [{\"key\": \"caAAYg11545\", \"title\": \"探知科技\"}, {\"key\": \"xVQtP021072\", \"type\": \"department\", \"title\": \"人事部\"}, {\"key\": \"sEDa7F11545\", \"type\": \"personnel\", \"title\": \"Pirvate\"}]}, {\"key\": \"k8XUlE49653\", \"type\": \"personnel\", \"title\": \"Soul\", \"linKey\": [{\"key\": \"caAAYg11545\", \"title\": \"探知科技\"}, {\"key\": \"SXCQCZ59180\", \"type\": \"department\", \"title\": \"总总总总总经理部\"}, {\"key\": \"k8XUlE49653\", \"type\": \"personnel\", \"title\": \"Soul\"}]}, {\"key\": \"reMiDT97288\", \"type\": \"personnel\", \"title\": \"Pirvate2\", \"linKey\": [{\"key\": \"caAAYg11545\", \"title\": \"探知科技\"}, {\"key\": \"reMiDT97288\", \"type\": \"personnel\", \"title\": \"Pirvate2\"}]}, {\"key\": \"ZkmXpK40126\", \"type\": \"personnel\", \"title\": \"侃大山\", \"linKey\": [{\"key\": \"caAAYg11545\", \"title\": \"探知科技\"}, {\"key\": \"ZkmXpK40126\", \"type\": \"personnel\", \"title\": \"侃大山\"}]}, {\"key\": \"vDonno106815\", \"type\": \"personnel\", \"title\": \"用户_15939965336\", \"linKey\": [{\"key\": \"caAAYg11545\", \"title\": \"探知科技\"}, {\"key\": \"vDonno106815\", \"type\": \"personnel\", \"title\": \"用户_15939965336\"}]}, {\"key\": \"gcafR0173504\", \"type\": \"personnel\", \"title\": \"用户_17634766666\", \"linKey\": [{\"key\": \"caAAYg11545\", \"title\": \"探知科技\"}, {\"key\": \"gcafR0173504\", \"type\": \"personnel\", \"title\": \"用户_17634766666\"}]}, {\"key\": \"E8GPcE135396\", \"type\": \"personnel\", \"title\": \"132\", \"linKey\": [{\"key\": \"caAAYg11545\", \"title\": \"探知科技\"}, {\"key\": \"E8GPcE135396\", \"type\": \"personnel\", \"title\": \"132\"}]}]}}', '[]', '规划环境客户见面', '2019-03-29 10:07:51', '2019-04-10 16:54:25');
INSERT INTO `pst_template` VALUES (4, '33333333333', 13, 1, 1, 1, '[{\"type\": \"INPUT\", \"field\": {\"name\": \"approved_amount\", \"label\": \"审定金额\", \"required\": false}}]', '[\"approved_amount\"]', '[]', '自由流程', '{\"checkedKeys\": [\"UNv8kB11545\", \"bRYxTq21072\", \"WS7M4l30599\", \"bIgsII87761\", \"7abYUd97288\", \"tMUMgY135396\", \"Ry31qw49653\", \"MYz62Q11545\", \"KcLxyk40126\", \"Pps90u49653\", \"QK8C1u59180\", \"P8353K68707\", \"pyi1au78234\", \"ZGP6L297288\", \"0p8ADB40126\"], \"checkedPersonnels\": [{\"key\": \"tMUMgY135396\", \"type\": \"personnel\", \"title\": \"132\", \"linKey\": [{\"key\": \"UNv8kB11545\", \"title\": \"探知科技\"}, {\"key\": \"bRYxTq21072\", \"type\": \"department\", \"title\": \"人事部\"}, {\"key\": \"tMUMgY135396\", \"type\": \"personnel\", \"title\": \"132\"}]}, {\"key\": \"Ry31qw49653\", \"type\": \"personnel\", \"title\": \"Soul\", \"linKey\": [{\"key\": \"UNv8kB11545\", \"title\": \"探知科技\"}, {\"key\": \"bRYxTq21072\", \"type\": \"department\", \"title\": \"人事部\"}, {\"key\": \"Ry31qw49653\", \"type\": \"personnel\", \"title\": \"Soul\"}]}, {\"key\": \"MYz62Q11545\", \"type\": \"personnel\", \"title\": \"Pirvate\", \"linKey\": [{\"key\": \"UNv8kB11545\", \"title\": \"探知科技\"}, {\"key\": \"bRYxTq21072\", \"type\": \"department\", \"title\": \"人事部\"}, {\"key\": \"MYz62Q11545\", \"type\": \"personnel\", \"title\": \"Pirvate\"}]}, {\"key\": \"ZGP6L297288\", \"type\": \"personnel\", \"title\": \"Pirvate2\", \"linKey\": [{\"key\": \"UNv8kB11545\", \"title\": \"探知科技\"}, {\"key\": \"ZGP6L297288\", \"type\": \"personnel\", \"title\": \"Pirvate2\"}]}, {\"key\": \"0p8ADB40126\", \"type\": \"personnel\", \"title\": \"侃大山\", \"linKey\": [{\"key\": \"UNv8kB11545\", \"title\": \"探知科技\"}, {\"key\": \"0p8ADB40126\", \"type\": \"personnel\", \"title\": \"侃大山\"}]}]}', '\"all\"', '[]', '自由流程', '2019-04-03 15:45:32', '2019-04-03 17:16:37');
INSERT INTO `pst_template` VALUES (5, '2222222222222222', 13, 1, 1, 0, '[{\"type\": \"SELECT\", \"field\": {\"name\": \"category\", \"label\": \"分类\", \"required\": false}}, {\"type\": \"SELECT\", \"field\": {\"name\": \"project_category\", \"label\": \"工程分类\", \"required\": false}}, {\"type\": \"INPUT\", \"field\": {\"name\": \"project_name\", \"label\": \"项目名称\", \"required\": false}}, {\"type\": \"INPUT\", \"field\": {\"name\": \"amount_of_review\", \"label\": \"送审金额\", \"required\": false}}, {\"type\": \"SELECT\", \"field\": {\"name\": \"action_label\", \"label\": \"标签\", \"required\": false}}]', '[\"category\", \"project_category\", \"project_name\", \"amount_of_review\", \"action_label\"]', '[]', '自由流程', '{\"checkedKeys\": [\"caAAYg11545\", \"xVQtP021072\", \"Tu673A30599\", \"W9W3yA87761\", \"SGRamE97288\", \"UE7ha5154450\", \"sEDa7F11545\", \"wnRWc740126\", \"wWItJk163977\", \"OQDuqT49653\", \"82lYQA173504\", \"SXCQCZ59180\", \"k8XUlE49653\", \"uOjXhl68707\", \"8V0zna78234\", \"piGRjs106815\", \"MyFoH2116342\", \"xAT08w125869\", \"Y8Z4Pl135396\", \"XW0xPj144923\", \"reMiDT97288\", \"ZkmXpK40126\", \"vDonno106815\", \"gcafR0173504\", \"E8GPcE135396\"], \"checkedPersonnels\": [{\"key\": \"sEDa7F11545\", \"type\": \"personnel\", \"title\": \"Pirvate\", \"linKey\": [{\"key\": \"caAAYg11545\", \"title\": \"探知科技\"}, {\"key\": \"xVQtP021072\", \"type\": \"department\", \"title\": \"人事部\"}, {\"key\": \"sEDa7F11545\", \"type\": \"personnel\", \"title\": \"Pirvate\"}]}, {\"key\": \"k8XUlE49653\", \"type\": \"personnel\", \"title\": \"Soul\", \"linKey\": [{\"key\": \"caAAYg11545\", \"title\": \"探知科技\"}, {\"key\": \"SXCQCZ59180\", \"type\": \"department\", \"title\": \"总总总总总经理部\"}, {\"key\": \"k8XUlE49653\", \"type\": \"personnel\", \"title\": \"Soul\"}]}, {\"key\": \"reMiDT97288\", \"type\": \"personnel\", \"title\": \"Pirvate2\", \"linKey\": [{\"key\": \"caAAYg11545\", \"title\": \"探知科技\"}, {\"key\": \"reMiDT97288\", \"type\": \"personnel\", \"title\": \"Pirvate2\"}]}, {\"key\": \"ZkmXpK40126\", \"type\": \"personnel\", \"title\": \"侃大山\", \"linKey\": [{\"key\": \"caAAYg11545\", \"title\": \"探知科技\"}, {\"key\": \"ZkmXpK40126\", \"type\": \"personnel\", \"title\": \"侃大山\"}]}, {\"key\": \"vDonno106815\", \"type\": \"personnel\", \"title\": \"用户_15939965336\", \"linKey\": [{\"key\": \"caAAYg11545\", \"title\": \"探知科技\"}, {\"key\": \"vDonno106815\", \"type\": \"personnel\", \"title\": \"用户_15939965336\"}]}, {\"key\": \"gcafR0173504\", \"type\": \"personnel\", \"title\": \"用户_17634766666\", \"linKey\": [{\"key\": \"caAAYg11545\", \"title\": \"探知科技\"}, {\"key\": \"gcafR0173504\", \"type\": \"personnel\", \"title\": \"用户_17634766666\"}]}, {\"key\": \"E8GPcE135396\", \"type\": \"personnel\", \"title\": \"132\", \"linKey\": [{\"key\": \"caAAYg11545\", \"title\": \"探知科技\"}, {\"key\": \"E8GPcE135396\", \"type\": \"personnel\", \"title\": \"132\"}]}]}', '\"all\"', '[]', '测试不需要审批模板', '2019-04-03 17:21:51', '2019-04-10 16:34:20');
INSERT INTO `pst_template` VALUES (6, '我的一个模板', 13, 1, 1, 1, '[{\"type\": \"SELECT\", \"field\": {\"name\": \"category\", \"label\": \"分类\", \"required\": false}}, {\"type\": \"INPUT\", \"field\": {\"name\": \"amount_of_review\", \"label\": \"送审金额\", \"required\": false}}]', NULL, '[{\"type\": \"normal\", \"checkedInfo\": {\"checkedKeys\": [\"xVQtP021072\", \"Tu673A30599\", \"W9W3yA87761\", \"SGRamE97288\", \"UE7ha5154450\", \"sEDa7F11545\"], \"checkedPersonnels\": [{\"key\": \"sEDa7F11545\", \"type\": \"personnel\", \"title\": \"Pirvate\", \"linKey\": [{\"key\": \"caAAYg11545\", \"title\": \"探知科技\"}, {\"key\": \"xVQtP021072\", \"type\": \"department\", \"title\": \"人事部\"}, {\"key\": \"sEDa7F11545\", \"type\": \"personnel\", \"title\": \"Pirvate\"}]}]}}]', '固定流程', '{\"checkedKeys\": [\"sEDa7F11545\"], \"checkedPersonnels\": [{\"key\": \"sEDa7F11545\", \"type\": \"personnel\", \"title\": \"Pirvate\", \"linKey\": [{\"key\": \"caAAYg11545\", \"title\": \"探知科技\"}, {\"key\": \"xVQtP021072\", \"type\": \"department\", \"title\": \"人事部\"}, {\"key\": \"sEDa7F11545\", \"type\": \"personnel\", \"title\": \"Pirvate\"}]}]}', '{\"staffId\": [1], \"departmentId\": [\"xVQtP021072\", \"Tu673A30599\", \"W9W3yA87761\", \"SGRamE97288\", \"UE7ha5154450\"], \"selectedPersonnelInfo\": {\"checkedKeys\": [\"xVQtP021072\", \"Tu673A30599\", \"W9W3yA87761\", \"SGRamE97288\", \"UE7ha5154450\", \"sEDa7F11545\"], \"checkedPersonnels\": [{\"key\": \"sEDa7F11545\", \"type\": \"personnel\", \"title\": \"Pirvate\", \"linKey\": [{\"key\": \"caAAYg11545\", \"title\": \"探知科技\"}, {\"key\": \"xVQtP021072\", \"type\": \"department\", \"title\": \"人事部\"}, {\"key\": \"sEDa7F11545\", \"type\": \"personnel\", \"title\": \"Pirvate\"}]}]}}', '\"{\\\"staffId\\\":[1],\\\"departmentId\\\":[\\\"xVQtP021072\\\",\\\"Tu673A30599\\\",\\\"W9W3yA87761\\\",\\\"SGRamE97288\\\",\\\"UE7ha5154450\\\"],\\\"selectedPersonnelInfo\\\":{\\\"checkedKeys\\\":[\\\"xVQtP021072\\\",\\\"Tu673A30599\\\",\\\"W9W3yA87761\\\",\\\"SGRamE97288\\\",\\\"UE7ha5154450\\\",\\\"sEDa7F11545\\\"],\\\"checkedPersonnels\\\":[{\\\"type\\\":\\\"personnel\\\",\\\"key\\\":\\\"sEDa7F11545\\\",\\\"title\\\":\\\"Pirvate\\\",\\\"linKey\\\":[{\\\"key\\\":\\\"caAAYg11545\\\",\\\"title\\\":\\\"\\\\u63a2\\\\u77e5\\\\u79d1\\\\u6280\\\"},{\\\"type\\\":\\\"department\\\",\\\"key\\\":\\\"xVQtP021072\\\",\\\"title\\\":\\\"\\\\u4eba\\\\u4e8b\\\\u90e8\\\"},{\\\"type\\\":\\\"personnel\\\",\\\"key\\\":\\\"sEDa7F11545\\\",\\\"title\\\":\\\"Pirvate\\\"}]}]}}\"', '这是一个模板', '2019-04-08 17:04:05', '2019-04-08 17:04:05');

-- ----------------------------
-- Table structure for pst_template_type
-- ----------------------------
DROP TABLE IF EXISTS `pst_template_type`;
CREATE TABLE `pst_template_type`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '类型名称',
  `company_id` int(10) UNSIGNED NOT NULL COMMENT '所属公司id',
  `sequence` int(11) NOT NULL DEFAULT 0 COMMENT '排序,根据此字段查找排序,针对于用户拖拽后的排序更新',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 14 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pst_template_type
-- ----------------------------
INSERT INTO `pst_template_type` VALUES (1, '经典类型1', 0, 1, '2019-03-01 15:46:53', '2019-03-01 15:46:53');
INSERT INTO `pst_template_type` VALUES (2, '流程第一排', 0, 2, '2019-03-01 15:47:08', '2019-03-08 14:33:58');
INSERT INTO `pst_template_type` VALUES (3, '经典类型3', 0, 3, '2019-03-04 15:35:45', '2019-03-07 17:02:16');
INSERT INTO `pst_template_type` VALUES (4, '经典类型4', 0, 4, '2019-03-06 09:05:53', '2019-03-07 17:02:16');
INSERT INTO `pst_template_type` VALUES (5, '经典类型5', 0, 5, '2019-03-06 10:43:09', '2019-03-07 17:02:16');
INSERT INTO `pst_template_type` VALUES (8, '9999', 1, 1, '2019-03-06 11:10:59', '2019-03-14 17:43:57');
INSERT INTO `pst_template_type` VALUES (11, '模板测试', 1, 0, '2019-03-06 11:25:21', '2019-03-16 08:13:35');
INSERT INTO `pst_template_type` VALUES (13, '又一个分组', 1, 0, '2019-03-22 09:57:59', '2019-03-22 09:57:59');

-- ----------------------------
-- Table structure for revisions
-- ----------------------------
DROP TABLE IF EXISTS `revisions`;
CREATE TABLE `revisions`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `revisionable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `revisionable_id` int(11) NOT NULL,
  `user_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `user_id` int(11) NULL DEFAULT NULL,
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `old_value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `new_value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `company_id` int(11) NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `revisions_revisionable_id_revisionable_type_index`(`revisionable_id`, `revisionable_type`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 117 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of revisions
-- ----------------------------
INSERT INTO `revisions` VALUES (3, 'App\\Models\\Role', 76, '132', 14, 'create_model', NULL, '的哈健康', 1, '2019-04-02 09:59:33', '2019-04-02 09:59:33', NULL);
INSERT INTO `revisions` VALUES (4, 'App\\Models\\Role', 74, '132', 14, '职务/角色名称-', '111111-', '威威-', 1, '2019-04-02 10:03:14', '2019-04-02 10:03:14', NULL);
INSERT INTO `revisions` VALUES (5, 'App\\Models\\Role', 74, NULL, 14, 'name', '威威', 'aa', NULL, '2019-04-02 10:10:29', '2019-04-02 10:10:29', NULL);
INSERT INTO `revisions` VALUES (6, 'App\\Models\\Role', 74, '132', 14, '职务/角色名称-', 'aa-', '安安-', 1, '2019-04-02 10:11:57', '2019-04-02 10:11:57', '职务/角色');
INSERT INTO `revisions` VALUES (7, 'App\\Models\\ApprovalTemplate', 67, NULL, 1, 'created_at', NULL, '2019-04-03 17:35:19', NULL, '2019-04-03 17:35:19', '2019-04-03 17:35:19', NULL);
INSERT INTO `revisions` VALUES (32, 'App\\Models\\Role', 101, '132', 14, 'create_model', NULL, '超级管理员', 14, '2019-04-08 11:11:07', '2019-04-08 11:11:07', NULL);
INSERT INTO `revisions` VALUES (33, 'App\\Models\\Role', 102, '132', 14, 'create_model', NULL, '管理员', 14, '2019-04-08 11:11:07', '2019-04-08 11:11:07', NULL);
INSERT INTO `revisions` VALUES (34, 'App\\Models\\Role', 103, '132', 14, 'create_model', NULL, '经理', 14, '2019-04-08 11:11:08', '2019-04-08 11:11:08', NULL);
INSERT INTO `revisions` VALUES (35, 'App\\Models\\Role', 104, '132', 14, 'create_model', NULL, '员工', 14, '2019-04-08 11:11:09', '2019-04-08 11:11:09', NULL);
INSERT INTO `revisions` VALUES (36, 'App\\Models\\Role', 105, '132', 14, 'create_model', NULL, 'HR', 14, '2019-04-08 11:11:09', '2019-04-08 11:11:09', NULL);
INSERT INTO `revisions` VALUES (37, 'App\\Models\\Role', 106, '132', 14, 'create_model', NULL, '组长', 14, '2019-04-08 11:11:10', '2019-04-08 11:11:10', NULL);
INSERT INTO `revisions` VALUES (38, 'App\\Models\\Role', 107, '132', 14, 'create_model', NULL, '超级管理员', 15, '2019-04-08 11:59:45', '2019-04-08 11:59:45', NULL);
INSERT INTO `revisions` VALUES (39, 'App\\Models\\Role', 108, '132', 14, 'create_model', NULL, '管理员', 15, '2019-04-08 11:59:45', '2019-04-08 11:59:45', NULL);
INSERT INTO `revisions` VALUES (40, 'App\\Models\\Role', 109, '132', 14, 'create_model', NULL, '经理', 15, '2019-04-08 11:59:46', '2019-04-08 11:59:46', NULL);
INSERT INTO `revisions` VALUES (41, 'App\\Models\\Role', 110, '132', 14, 'create_model', NULL, '员工', 15, '2019-04-08 11:59:47', '2019-04-08 11:59:47', NULL);
INSERT INTO `revisions` VALUES (42, 'App\\Models\\Role', 111, '132', 14, 'create_model', NULL, 'HR', 15, '2019-04-08 11:59:47', '2019-04-08 11:59:47', NULL);
INSERT INTO `revisions` VALUES (43, 'App\\Models\\Role', 112, '132', 14, 'create_model', NULL, '组长', 15, '2019-04-08 11:59:48', '2019-04-08 11:59:48', NULL);
INSERT INTO `revisions` VALUES (44, 'App\\Models\\Role', 113, '132', 14, 'create_model', NULL, 'ppbl', 15, '2019-04-08 16:39:21', '2019-04-08 16:39:21', NULL);
INSERT INTO `revisions` VALUES (87, 'App\\Models\\Role', 156, 'Pirvate', 1, 'create_model', NULL, '超级管理员', 24, '2019-04-13 09:55:58', '2019-04-13 09:55:58', NULL);
INSERT INTO `revisions` VALUES (88, 'App\\Models\\Role', 157, 'Pirvate', 1, 'create_model', NULL, '管理员', 24, '2019-04-13 09:56:00', '2019-04-13 09:56:00', NULL);
INSERT INTO `revisions` VALUES (89, 'App\\Models\\Role', 158, 'Pirvate', 1, 'create_model', NULL, '经理', 24, '2019-04-13 09:56:01', '2019-04-13 09:56:01', NULL);
INSERT INTO `revisions` VALUES (90, 'App\\Models\\Role', 159, 'Pirvate', 1, 'create_model', NULL, '员工', 24, '2019-04-13 09:56:03', '2019-04-13 09:56:03', NULL);
INSERT INTO `revisions` VALUES (91, 'App\\Models\\Role', 160, 'Pirvate', 1, 'create_model', NULL, 'HR', 24, '2019-04-13 09:56:05', '2019-04-13 09:56:05', NULL);
INSERT INTO `revisions` VALUES (92, 'App\\Models\\Role', 161, 'Pirvate', 1, 'create_model', NULL, '组长', 24, '2019-04-13 09:56:06', '2019-04-13 09:56:06', NULL);
INSERT INTO `revisions` VALUES (105, 'App\\Models\\Role', 174, '侃大山', 4, 'create_model', NULL, '超级管理员', 27, '2019-04-13 09:58:56', '2019-04-13 09:58:56', NULL);
INSERT INTO `revisions` VALUES (106, 'App\\Models\\Role', 175, '侃大山', 4, 'create_model', NULL, '管理员', 27, '2019-04-13 09:58:57', '2019-04-13 09:58:57', NULL);
INSERT INTO `revisions` VALUES (107, 'App\\Models\\Role', 176, '侃大山', 4, 'create_model', NULL, '经理', 27, '2019-04-13 09:58:57', '2019-04-13 09:58:57', NULL);
INSERT INTO `revisions` VALUES (108, 'App\\Models\\Role', 177, '侃大山', 4, 'create_model', NULL, '员工', 27, '2019-04-13 09:58:58', '2019-04-13 09:58:58', NULL);
INSERT INTO `revisions` VALUES (109, 'App\\Models\\Role', 178, '侃大山', 4, 'create_model', NULL, 'HR', 27, '2019-04-13 09:58:59', '2019-04-13 09:58:59', NULL);
INSERT INTO `revisions` VALUES (110, 'App\\Models\\Role', 179, '侃大山', 4, 'create_model', NULL, '组长', 27, '2019-04-13 09:59:00', '2019-04-13 09:59:00', NULL);
INSERT INTO `revisions` VALUES (111, 'App\\Models\\Role', 180, '侃大山', 4, 'create_model', NULL, '超级管理员', 28, '2019-04-13 14:11:47', '2019-04-13 14:11:47', NULL);
INSERT INTO `revisions` VALUES (112, 'App\\Models\\Role', 181, '侃大山', 4, 'create_model', NULL, '管理员', 28, '2019-04-13 14:11:48', '2019-04-13 14:11:48', NULL);
INSERT INTO `revisions` VALUES (113, 'App\\Models\\Role', 182, '侃大山', 4, 'create_model', NULL, '经理', 28, '2019-04-13 14:11:49', '2019-04-13 14:11:49', NULL);
INSERT INTO `revisions` VALUES (114, 'App\\Models\\Role', 183, '侃大山', 4, 'create_model', NULL, '员工', 28, '2019-04-13 14:11:49', '2019-04-13 14:11:49', NULL);
INSERT INTO `revisions` VALUES (115, 'App\\Models\\Role', 184, '侃大山', 4, 'create_model', NULL, 'HR', 28, '2019-04-13 14:11:50', '2019-04-13 14:11:50', NULL);
INSERT INTO `revisions` VALUES (116, 'App\\Models\\Role', 185, '侃大山', 4, 'create_model', NULL, '组长', 28, '2019-04-13 14:11:50', '2019-04-13 14:11:50', NULL);

-- ----------------------------
-- Table structure for role_per
-- ----------------------------
DROP TABLE IF EXISTS `role_per`;
CREATE TABLE `role_per`  (
  `permission_id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`, `role_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of role_per
-- ----------------------------
INSERT INTO `role_per` VALUES (1, 1);
INSERT INTO `role_per` VALUES (1, 2);
INSERT INTO `role_per` VALUES (1, 3);
INSERT INTO `role_per` VALUES (1, 4);
INSERT INTO `role_per` VALUES (1, 5);
INSERT INTO `role_per` VALUES (1, 6);
INSERT INTO `role_per` VALUES (1, 54);
INSERT INTO `role_per` VALUES (1, 56);
INSERT INTO `role_per` VALUES (1, 101);
INSERT INTO `role_per` VALUES (1, 102);
INSERT INTO `role_per` VALUES (1, 103);
INSERT INTO `role_per` VALUES (1, 104);
INSERT INTO `role_per` VALUES (1, 105);
INSERT INTO `role_per` VALUES (1, 106);
INSERT INTO `role_per` VALUES (1, 107);
INSERT INTO `role_per` VALUES (1, 108);
INSERT INTO `role_per` VALUES (1, 109);
INSERT INTO `role_per` VALUES (1, 110);
INSERT INTO `role_per` VALUES (1, 111);
INSERT INTO `role_per` VALUES (1, 112);
INSERT INTO `role_per` VALUES (1, 156);
INSERT INTO `role_per` VALUES (1, 157);
INSERT INTO `role_per` VALUES (1, 158);
INSERT INTO `role_per` VALUES (1, 159);
INSERT INTO `role_per` VALUES (1, 160);
INSERT INTO `role_per` VALUES (1, 161);
INSERT INTO `role_per` VALUES (1, 174);
INSERT INTO `role_per` VALUES (1, 175);
INSERT INTO `role_per` VALUES (1, 176);
INSERT INTO `role_per` VALUES (1, 177);
INSERT INTO `role_per` VALUES (1, 178);
INSERT INTO `role_per` VALUES (1, 179);
INSERT INTO `role_per` VALUES (1, 180);
INSERT INTO `role_per` VALUES (1, 181);
INSERT INTO `role_per` VALUES (1, 182);
INSERT INTO `role_per` VALUES (1, 183);
INSERT INTO `role_per` VALUES (1, 184);
INSERT INTO `role_per` VALUES (1, 185);
INSERT INTO `role_per` VALUES (2, 1);
INSERT INTO `role_per` VALUES (2, 2);
INSERT INTO `role_per` VALUES (2, 3);
INSERT INTO `role_per` VALUES (2, 4);
INSERT INTO `role_per` VALUES (2, 5);
INSERT INTO `role_per` VALUES (2, 6);
INSERT INTO `role_per` VALUES (2, 54);
INSERT INTO `role_per` VALUES (2, 56);
INSERT INTO `role_per` VALUES (2, 101);
INSERT INTO `role_per` VALUES (2, 102);
INSERT INTO `role_per` VALUES (2, 103);
INSERT INTO `role_per` VALUES (2, 104);
INSERT INTO `role_per` VALUES (2, 105);
INSERT INTO `role_per` VALUES (2, 106);
INSERT INTO `role_per` VALUES (2, 107);
INSERT INTO `role_per` VALUES (2, 108);
INSERT INTO `role_per` VALUES (2, 109);
INSERT INTO `role_per` VALUES (2, 110);
INSERT INTO `role_per` VALUES (2, 111);
INSERT INTO `role_per` VALUES (2, 112);
INSERT INTO `role_per` VALUES (2, 156);
INSERT INTO `role_per` VALUES (2, 157);
INSERT INTO `role_per` VALUES (2, 158);
INSERT INTO `role_per` VALUES (2, 159);
INSERT INTO `role_per` VALUES (2, 160);
INSERT INTO `role_per` VALUES (2, 161);
INSERT INTO `role_per` VALUES (2, 174);
INSERT INTO `role_per` VALUES (2, 175);
INSERT INTO `role_per` VALUES (2, 176);
INSERT INTO `role_per` VALUES (2, 177);
INSERT INTO `role_per` VALUES (2, 178);
INSERT INTO `role_per` VALUES (2, 179);
INSERT INTO `role_per` VALUES (2, 180);
INSERT INTO `role_per` VALUES (2, 181);
INSERT INTO `role_per` VALUES (2, 182);
INSERT INTO `role_per` VALUES (2, 183);
INSERT INTO `role_per` VALUES (2, 184);
INSERT INTO `role_per` VALUES (2, 185);
INSERT INTO `role_per` VALUES (3, 1);
INSERT INTO `role_per` VALUES (3, 2);
INSERT INTO `role_per` VALUES (3, 3);
INSERT INTO `role_per` VALUES (3, 54);
INSERT INTO `role_per` VALUES (3, 56);
INSERT INTO `role_per` VALUES (3, 75);
INSERT INTO `role_per` VALUES (3, 76);
INSERT INTO `role_per` VALUES (3, 101);
INSERT INTO `role_per` VALUES (3, 102);
INSERT INTO `role_per` VALUES (3, 103);
INSERT INTO `role_per` VALUES (3, 107);
INSERT INTO `role_per` VALUES (3, 108);
INSERT INTO `role_per` VALUES (3, 109);
INSERT INTO `role_per` VALUES (3, 113);
INSERT INTO `role_per` VALUES (3, 156);
INSERT INTO `role_per` VALUES (3, 157);
INSERT INTO `role_per` VALUES (3, 158);
INSERT INTO `role_per` VALUES (3, 174);
INSERT INTO `role_per` VALUES (3, 175);
INSERT INTO `role_per` VALUES (3, 176);
INSERT INTO `role_per` VALUES (3, 180);
INSERT INTO `role_per` VALUES (3, 181);
INSERT INTO `role_per` VALUES (3, 182);
INSERT INTO `role_per` VALUES (4, 1);
INSERT INTO `role_per` VALUES (4, 2);
INSERT INTO `role_per` VALUES (4, 3);
INSERT INTO `role_per` VALUES (4, 54);
INSERT INTO `role_per` VALUES (4, 56);
INSERT INTO `role_per` VALUES (4, 76);
INSERT INTO `role_per` VALUES (4, 101);
INSERT INTO `role_per` VALUES (4, 102);
INSERT INTO `role_per` VALUES (4, 103);
INSERT INTO `role_per` VALUES (4, 107);
INSERT INTO `role_per` VALUES (4, 108);
INSERT INTO `role_per` VALUES (4, 109);
INSERT INTO `role_per` VALUES (4, 113);
INSERT INTO `role_per` VALUES (4, 156);
INSERT INTO `role_per` VALUES (4, 157);
INSERT INTO `role_per` VALUES (4, 158);
INSERT INTO `role_per` VALUES (4, 174);
INSERT INTO `role_per` VALUES (4, 175);
INSERT INTO `role_per` VALUES (4, 176);
INSERT INTO `role_per` VALUES (4, 180);
INSERT INTO `role_per` VALUES (4, 181);
INSERT INTO `role_per` VALUES (4, 182);
INSERT INTO `role_per` VALUES (5, 1);
INSERT INTO `role_per` VALUES (5, 2);
INSERT INTO `role_per` VALUES (5, 3);
INSERT INTO `role_per` VALUES (5, 4);
INSERT INTO `role_per` VALUES (5, 5);
INSERT INTO `role_per` VALUES (5, 6);
INSERT INTO `role_per` VALUES (5, 54);
INSERT INTO `role_per` VALUES (5, 56);
INSERT INTO `role_per` VALUES (5, 75);
INSERT INTO `role_per` VALUES (5, 101);
INSERT INTO `role_per` VALUES (5, 102);
INSERT INTO `role_per` VALUES (5, 103);
INSERT INTO `role_per` VALUES (5, 104);
INSERT INTO `role_per` VALUES (5, 105);
INSERT INTO `role_per` VALUES (5, 106);
INSERT INTO `role_per` VALUES (5, 107);
INSERT INTO `role_per` VALUES (5, 108);
INSERT INTO `role_per` VALUES (5, 109);
INSERT INTO `role_per` VALUES (5, 110);
INSERT INTO `role_per` VALUES (5, 111);
INSERT INTO `role_per` VALUES (5, 112);
INSERT INTO `role_per` VALUES (5, 156);
INSERT INTO `role_per` VALUES (5, 157);
INSERT INTO `role_per` VALUES (5, 158);
INSERT INTO `role_per` VALUES (5, 159);
INSERT INTO `role_per` VALUES (5, 160);
INSERT INTO `role_per` VALUES (5, 161);
INSERT INTO `role_per` VALUES (5, 174);
INSERT INTO `role_per` VALUES (5, 175);
INSERT INTO `role_per` VALUES (5, 176);
INSERT INTO `role_per` VALUES (5, 177);
INSERT INTO `role_per` VALUES (5, 178);
INSERT INTO `role_per` VALUES (5, 179);
INSERT INTO `role_per` VALUES (5, 180);
INSERT INTO `role_per` VALUES (5, 181);
INSERT INTO `role_per` VALUES (5, 182);
INSERT INTO `role_per` VALUES (5, 183);
INSERT INTO `role_per` VALUES (5, 184);
INSERT INTO `role_per` VALUES (5, 185);
INSERT INTO `role_per` VALUES (6, 1);
INSERT INTO `role_per` VALUES (6, 2);
INSERT INTO `role_per` VALUES (6, 3);
INSERT INTO `role_per` VALUES (6, 4);
INSERT INTO `role_per` VALUES (6, 5);
INSERT INTO `role_per` VALUES (6, 6);
INSERT INTO `role_per` VALUES (6, 54);
INSERT INTO `role_per` VALUES (6, 56);
INSERT INTO `role_per` VALUES (6, 101);
INSERT INTO `role_per` VALUES (6, 102);
INSERT INTO `role_per` VALUES (6, 103);
INSERT INTO `role_per` VALUES (6, 104);
INSERT INTO `role_per` VALUES (6, 105);
INSERT INTO `role_per` VALUES (6, 106);
INSERT INTO `role_per` VALUES (6, 107);
INSERT INTO `role_per` VALUES (6, 108);
INSERT INTO `role_per` VALUES (6, 109);
INSERT INTO `role_per` VALUES (6, 110);
INSERT INTO `role_per` VALUES (6, 111);
INSERT INTO `role_per` VALUES (6, 112);
INSERT INTO `role_per` VALUES (6, 156);
INSERT INTO `role_per` VALUES (6, 157);
INSERT INTO `role_per` VALUES (6, 158);
INSERT INTO `role_per` VALUES (6, 159);
INSERT INTO `role_per` VALUES (6, 160);
INSERT INTO `role_per` VALUES (6, 161);
INSERT INTO `role_per` VALUES (6, 174);
INSERT INTO `role_per` VALUES (6, 175);
INSERT INTO `role_per` VALUES (6, 176);
INSERT INTO `role_per` VALUES (6, 177);
INSERT INTO `role_per` VALUES (6, 178);
INSERT INTO `role_per` VALUES (6, 179);
INSERT INTO `role_per` VALUES (6, 180);
INSERT INTO `role_per` VALUES (6, 181);
INSERT INTO `role_per` VALUES (6, 182);
INSERT INTO `role_per` VALUES (6, 183);
INSERT INTO `role_per` VALUES (6, 184);
INSERT INTO `role_per` VALUES (6, 185);
INSERT INTO `role_per` VALUES (8, 1);
INSERT INTO `role_per` VALUES (8, 54);
INSERT INTO `role_per` VALUES (8, 101);
INSERT INTO `role_per` VALUES (8, 107);
INSERT INTO `role_per` VALUES (8, 156);
INSERT INTO `role_per` VALUES (8, 174);
INSERT INTO `role_per` VALUES (8, 180);
INSERT INTO `role_per` VALUES (9, 1);
INSERT INTO `role_per` VALUES (9, 2);
INSERT INTO `role_per` VALUES (9, 3);
INSERT INTO `role_per` VALUES (9, 54);
INSERT INTO `role_per` VALUES (9, 56);
INSERT INTO `role_per` VALUES (9, 101);
INSERT INTO `role_per` VALUES (9, 102);
INSERT INTO `role_per` VALUES (9, 103);
INSERT INTO `role_per` VALUES (9, 107);
INSERT INTO `role_per` VALUES (9, 108);
INSERT INTO `role_per` VALUES (9, 109);
INSERT INTO `role_per` VALUES (9, 113);
INSERT INTO `role_per` VALUES (9, 156);
INSERT INTO `role_per` VALUES (9, 157);
INSERT INTO `role_per` VALUES (9, 158);
INSERT INTO `role_per` VALUES (9, 174);
INSERT INTO `role_per` VALUES (9, 175);
INSERT INTO `role_per` VALUES (9, 176);
INSERT INTO `role_per` VALUES (9, 180);
INSERT INTO `role_per` VALUES (9, 181);
INSERT INTO `role_per` VALUES (9, 182);
INSERT INTO `role_per` VALUES (10, 54);

-- ----------------------------
-- Table structure for roles
-- ----------------------------
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '角色名称',
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '角色描述',
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_personal` smallint(6) NOT NULL DEFAULT 0,
  `sort` int(255) NULL DEFAULT 0,
  `deleted_at` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 186 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of roles
-- ----------------------------
INSERT INTO `roles` VALUES (1, '超级管理员-企业基础', '测试', 'gzt', 0, 0, NULL);
INSERT INTO `roles` VALUES (2, '管理员-企业基础', '测试', 'gzt', 0, 0, NULL);
INSERT INTO `roles` VALUES (3, '经理-企业基础', '测试', 'gzt', 0, 0, NULL);
INSERT INTO `roles` VALUES (4, '员工-企业基础', '测试', 'gzt', 0, 0, NULL);
INSERT INTO `roles` VALUES (5, 'HR-企业基础', '部长测试', 'gzt', 0, 0, NULL);
INSERT INTO `roles` VALUES (6, '组长-企业基础', '组长测试', 'gzt', 0, 0, NULL);
INSERT INTO `roles` VALUES (9, '企业基础待定', '企业基础待定', 'gzt', 0, 0, NULL);
INSERT INTO `roles` VALUES (10, '企业基础待定', '企业基础待定', 'gzt', 0, 0, NULL);
INSERT INTO `roles` VALUES (11, '企业基础待定', '企业基础待定', 'gzt', 0, 0, NULL);
INSERT INTO `roles` VALUES (12, '企业基础待定', '企业基础待定', 'gzt', 0, 0, NULL);
INSERT INTO `roles` VALUES (13, '企业基础待定', '企业基础待定', 'gzt', 0, 0, NULL);
INSERT INTO `roles` VALUES (14, '单用户增值角色(预留)', '描述', 'gzt', 1, 0, NULL);
INSERT INTO `roles` VALUES (15, '单用户增值角色(预留)', '描述', 'gzt', 1, 0, NULL);
INSERT INTO `roles` VALUES (16, '单用户增值角色(预留)', '描述', 'gzt', 1, 0, NULL);
INSERT INTO `roles` VALUES (17, '单用户增值角色(预留)', '描述', 'gzt', 1, 0, NULL);
INSERT INTO `roles` VALUES (18, '单用户增值角色(预留)', '描述', 'gzt', 1, 0, NULL);
INSERT INTO `roles` VALUES (19, '单用户增值角色(预留)', '描述', 'gzt', 1, 0, NULL);
INSERT INTO `roles` VALUES (20, '单用户增值角色(预留)', '描述', 'gzt', 1, 0, NULL);
INSERT INTO `roles` VALUES (21, '单用户增值角色(预留)', '描述', 'gzt', 1, 0, NULL);
INSERT INTO `roles` VALUES (22, '单用户增值角色(预留)', '描述', 'gzt', 1, 0, NULL);
INSERT INTO `roles` VALUES (23, '单用户增值角色(预留)', '描述', 'gzt', 1, 0, NULL);
INSERT INTO `roles` VALUES (54, '超级管理员', '测试', 'gzt', 0, 1, NULL);
INSERT INTO `roles` VALUES (56, '经理', '测试', 'gzt', 0, 0, NULL);
INSERT INTO `roles` VALUES (66, 'aaa', NULL, 'gzt', 0, 0, NULL);
INSERT INTO `roles` VALUES (73, '132', NULL, 'gzt', 0, 0, '2019-03-23 13:40:48');
INSERT INTO `roles` VALUES (74, '安安', NULL, 'gzt', 0, 0, '2019-04-02 10:14:11');
INSERT INTO `roles` VALUES (75, 'eeee', NULL, 'gzt', 0, 0, NULL);
INSERT INTO `roles` VALUES (76, '的哈健康', NULL, 'gzt', 0, 0, NULL);
INSERT INTO `roles` VALUES (101, '超级管理员', '测试', 'gzt', 0, 0, NULL);
INSERT INTO `roles` VALUES (102, '管理员', '测试', 'gzt', 0, 0, NULL);
INSERT INTO `roles` VALUES (103, '经理', '测试', 'gzt', 0, 0, NULL);
INSERT INTO `roles` VALUES (104, '员工', '测试', 'gzt', 0, 0, NULL);
INSERT INTO `roles` VALUES (105, 'HR', '部长测试', 'gzt', 0, 0, NULL);
INSERT INTO `roles` VALUES (106, '组长', '组长测试', 'gzt', 0, 0, NULL);
INSERT INTO `roles` VALUES (107, '超级管理员', '测试', 'gzt', 0, 0, NULL);
INSERT INTO `roles` VALUES (108, '管理员', '测试', 'gzt', 0, 0, NULL);
INSERT INTO `roles` VALUES (109, '经理', '测试', 'gzt', 0, 0, NULL);
INSERT INTO `roles` VALUES (110, '员工', '测试', 'gzt', 0, 0, NULL);
INSERT INTO `roles` VALUES (111, 'HR', '部长测试', 'gzt', 0, 0, NULL);
INSERT INTO `roles` VALUES (112, '组长', '组长测试', 'gzt', 0, 0, NULL);
INSERT INTO `roles` VALUES (113, 'ppbl', NULL, 'gzt', 0, 0, NULL);
INSERT INTO `roles` VALUES (156, '超级管理员', '测试', 'gzt', 0, 0, NULL);
INSERT INTO `roles` VALUES (157, '管理员', '测试', 'gzt', 0, 0, NULL);
INSERT INTO `roles` VALUES (158, '经理', '测试', 'gzt', 0, 0, NULL);
INSERT INTO `roles` VALUES (159, '员工', '测试', 'gzt', 0, 0, NULL);
INSERT INTO `roles` VALUES (160, 'HR', '部长测试', 'gzt', 0, 0, NULL);
INSERT INTO `roles` VALUES (161, '组长', '组长测试', 'gzt', 0, 0, NULL);
INSERT INTO `roles` VALUES (174, '超级管理员', '测试', 'gzt', 0, 0, NULL);
INSERT INTO `roles` VALUES (175, '管理员', '测试', 'gzt', 0, 0, NULL);
INSERT INTO `roles` VALUES (176, '经理', '测试', 'gzt', 0, 0, NULL);
INSERT INTO `roles` VALUES (177, '员工', '测试', 'gzt', 0, 0, NULL);
INSERT INTO `roles` VALUES (178, 'HR', '部长测试', 'gzt', 0, 0, NULL);
INSERT INTO `roles` VALUES (179, '组长', '组长测试', 'gzt', 0, 0, NULL);
INSERT INTO `roles` VALUES (180, '超级管理员', '测试', 'gzt', 0, 0, NULL);
INSERT INTO `roles` VALUES (181, '管理员', '测试', 'gzt', 0, 0, NULL);
INSERT INTO `roles` VALUES (182, '经理', '测试', 'gzt', 0, 0, NULL);
INSERT INTO `roles` VALUES (183, '员工', '测试', 'gzt', 0, 0, NULL);
INSERT INTO `roles` VALUES (184, 'HR', '部长测试', 'gzt', 0, 0, NULL);
INSERT INTO `roles` VALUES (185, '组长', '组长测试', 'gzt', 0, 0, NULL);

-- ----------------------------
-- Table structure for spreads
-- ----------------------------
DROP TABLE IF EXISTS `spreads`;
CREATE TABLE `spreads`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '对应用户',
  `path` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '邀请链接',
  `num` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for user_company
-- ----------------------------
DROP TABLE IF EXISTS `user_company`;
CREATE TABLE `user_company`  (
  `user_id` bigint(20) NOT NULL COMMENT '用户id',
  `company_id` int(11) NOT NULL COMMENT '公司id',
  `is_enable` tinyint(4) NOT NULL DEFAULT 1,
  `activation` tinyint(4) NULL DEFAULT 1 COMMENT '0为未激活状态,1是激活状态',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of user_company
-- ----------------------------
INSERT INTO `user_company` VALUES (18700000000, 1, 1, 0, NULL, NULL);
INSERT INTO `user_company` VALUES (5, 1, 1, 1, NULL, NULL);
INSERT INTO `user_company` VALUES (10, 1, 1, 1, NULL, NULL);
INSERT INTO `user_company` VALUES (4, 1, 1, 1, NULL, NULL);
INSERT INTO `user_company` VALUES (1, 1, 1, 1, NULL, NULL);
INSERT INTO `user_company` VALUES (11, 1, 1, 1, NULL, NULL);
INSERT INTO `user_company` VALUES (18, 1, 1, 1, NULL, NULL);
INSERT INTO `user_company` VALUES (15100000069, 1, 1, 0, NULL, NULL);
INSERT INTO `user_company` VALUES (14, 14, 1, 1, NULL, NULL);
INSERT INTO `user_company` VALUES (14, 15, 1, 1, NULL, NULL);
INSERT INTO `user_company` VALUES (21, 1, 1, 1, NULL, NULL);
INSERT INTO `user_company` VALUES (19, 1, 1, 1, NULL, NULL);
INSERT INTO `user_company` VALUES (22, 14, 1, 1, NULL, NULL);
INSERT INTO `user_company` VALUES (1, 24, 1, 1, NULL, NULL);
INSERT INTO `user_company` VALUES (4, 27, 1, 1, NULL, NULL);
INSERT INTO `user_company` VALUES (4, 28, 1, 1, NULL, NULL);

-- ----------------------------
-- Table structure for user_company_info
-- ----------------------------
DROP TABLE IF EXISTS `user_company_info`;
CREATE TABLE `user_company_info`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `company_id` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sex` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `tel` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `role_ids` json NULL,
  `remarks` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `roomNumber` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  `department_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `activation` tinyint(4) NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 25 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of user_company_info
-- ----------------------------
INSERT INTO `user_company_info` VALUES (1, 18700000000, 1, 'ttt33', '男', '18700000000', '18749817085@qq.com', NULL, '4567896', '长葛市政委', '100', NULL, NULL, '-0.21181904062139184', 0);
INSERT INTO `user_company_info` VALUES (2, 14, 1, '刘学', '女', '18749817085', '18749817085@qq.com', NULL, NULL, 'da\'d\'sa发发', '123', NULL, NULL, 'cccdcc11545', 0);
INSERT INTO `user_company_info` VALUES (3, 5, 1, 'soul3', '男', '13733607139', '13733607139@qq.com', NULL, NULL, NULL, NULL, NULL, NULL, 'dbdbbb59180', 0);
INSERT INTO `user_company_info` VALUES (4, 4, 1, 'ppbl', 'nv', '15100000002', '15100000002@163.com', NULL, '13456', '长葛市政委', '100', NULL, NULL, NULL, 0);
INSERT INTO `user_company_info` VALUES (5, 10, 1, 'ttt', '女', '13333333333', '13333333333@163.com', NULL, NULL, NULL, NULL, NULL, NULL, '59180', 0);
INSERT INTO `user_company_info` VALUES (6, 11, 1, '李生', '男', '15939965336', 'litongleo9@126.com', NULL, NULL, NULL, NULL, NULL, NULL, '-0.21181904062139184', 0);
INSERT INTO `user_company_info` VALUES (7, 18, 1, '小鹏哥', '男', '17634766666', 'dulifei@163.com', '[54]', NULL, NULL, '601', NULL, NULL, NULL, 0);
INSERT INTO `user_company_info` VALUES (8, 15100000069, 1, '245', NULL, '15100000069', '158964782@qq.com', NULL, NULL, NULL, NULL, NULL, NULL, '-0.21181904062139184', 0);
INSERT INTO `user_company_info` VALUES (9, 14, 15, '132', '男', '18749817085', '13333333333@163.com', '[\"FQpzuP1021407\", \"yHcqhy1030934\", \"yN5bzU1040461\", \"6bfxm11049988\", \"9RcQ2k1059515\", \"833Qpp1069042\"]', '123', '455', '100', NULL, NULL, 'ddaada516476', 0);
INSERT INTO `user_company_info` VALUES (10, 21, 1, '用户_18749817055', NULL, '18749817055', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1);
INSERT INTO `user_company_info` VALUES (11, 19, 1, '用户_15100000001', NULL, '15100000001', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1);
INSERT INTO `user_company_info` VALUES (12, 22, 1, '用户_15100000003', NULL, '15100000003', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1);
INSERT INTO `user_company_info` VALUES (20, 1, 24, 'Pirvate', NULL, '16638638285', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1);
INSERT INTO `user_company_info` VALUES (23, 4, 27, '侃大山', NULL, '15100000002', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1);
INSERT INTO `user_company_info` VALUES (24, 4, 28, '侃大山', NULL, '15100000002', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1);

-- ----------------------------
-- Table structure for user_department
-- ----------------------------
DROP TABLE IF EXISTS `user_department`;
CREATE TABLE `user_department`  (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `department_id` bigint(20) UNSIGNED NOT NULL,
  `is_main` smallint(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否是主部门'
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of user_department
-- ----------------------------
INSERT INTO `user_department` VALUES (18700000000, 0, 0);
INSERT INTO `user_department` VALUES (5, 6, 0);
INSERT INTO `user_department` VALUES (1, 2, 0);
INSERT INTO `user_department` VALUES (10, 1, 0);
INSERT INTO `user_department` VALUES (4, 1, 0);
INSERT INTO `user_department` VALUES (11, 1, 0);
INSERT INTO `user_department` VALUES (18, 1, 0);
INSERT INTO `user_department` VALUES (15100000069, 0, 0);
INSERT INTO `user_department` VALUES (14, 1, 0);
INSERT INTO `user_department` VALUES (14, 54, 0);
INSERT INTO `user_department` VALUES (21, 1, 0);
INSERT INTO `user_department` VALUES (19, 1, 0);
INSERT INTO `user_department` VALUES (22, 1, 0);
INSERT INTO `user_department` VALUES (1, 92, 0);
INSERT INTO `user_department` VALUES (4, 107, 0);
INSERT INTO `user_department` VALUES (4, 112, 0);

-- ----------------------------
-- Table structure for user_notice_follow
-- ----------------------------
DROP TABLE IF EXISTS `user_notice_follow`;
CREATE TABLE `user_notice_follow`  (
  `user_id` int(10) UNSIGNED NOT NULL COMMENT '关注用户的id',
  `notice_id` int(10) UNSIGNED NOT NULL COMMENT '所关注的notice_id'
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of user_notice_follow
-- ----------------------------
INSERT INTO `user_notice_follow` VALUES (4, 12);
INSERT INTO `user_notice_follow` VALUES (1, 30);

-- ----------------------------
-- Table structure for user_oss
-- ----------------------------
DROP TABLE IF EXISTS `user_oss`;
CREATE TABLE `user_oss`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` smallint(5) UNSIGNED NOT NULL,
  `name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '个人网盘' COMMENT '个人云存储',
  `root_path` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '个人云存储根路径',
  `now_size` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '个人云存储已使用空间/kb',
  `all_size` bigint(20) UNSIGNED NOT NULL DEFAULT 2097152 COMMENT '个人云存储总空间/kb',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 18 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of user_oss
-- ----------------------------
INSERT INTO `user_oss` VALUES (1, 1, '个人网盘', 'user/user1/', 136153, 2097152, '2018-12-07 15:58:47', '2018-12-08 11:31:35');
INSERT INTO `user_oss` VALUES (2, 6, '个人网盘', 'user/user6/', 0, 2097152, '2018-12-08 08:46:34', '2018-12-08 08:46:34');
INSERT INTO `user_oss` VALUES (3, 7, '个人网盘', 'user/user7/', 0, 2097152, '2018-12-08 08:49:46', '2018-12-08 08:49:46');
INSERT INTO `user_oss` VALUES (4, 8, '个人网盘', 'user/user8/', 0, 2097152, '2018-12-08 09:02:56', '2018-12-08 09:02:56');
INSERT INTO `user_oss` VALUES (5, 9, '个人网盘', 'user/user9/', 0, 2097152, '2018-12-08 09:04:18', '2018-12-08 09:04:18');
INSERT INTO `user_oss` VALUES (6, 10, '个人网盘', 'user/user10/', 0, 2097152, '2018-12-12 10:55:21', '2018-12-12 10:55:21');
INSERT INTO `user_oss` VALUES (7, 11, '个人网盘', 'user/user11/', 0, 2097152, '2019-01-02 15:16:57', '2019-01-02 15:16:57');
INSERT INTO `user_oss` VALUES (8, 12, '个人网盘', 'user/user12/', 0, 2097152, '2019-01-07 11:26:37', '2019-01-07 11:26:37');
INSERT INTO `user_oss` VALUES (9, 13, '个人网盘', 'user/user13/', 0, 2097152, '2019-01-07 13:57:17', '2019-01-07 13:57:17');
INSERT INTO `user_oss` VALUES (10, 14, '个人网盘', 'user/user14/', 0, 2097152, '2019-01-07 14:05:04', '2019-01-07 14:05:04');
INSERT INTO `user_oss` VALUES (11, 15, '个人网盘', 'user/user15/', 0, 2097152, '2019-01-12 14:16:45', '2019-01-12 14:16:45');
INSERT INTO `user_oss` VALUES (12, 17, '个人网盘', 'user/user17/', 0, 2097152, '2019-01-28 14:58:08', '2019-01-28 14:58:08');
INSERT INTO `user_oss` VALUES (13, 18, '个人网盘', 'user/user18/', 0, 2097152, '2019-03-19 16:01:08', '2019-03-19 16:01:08');
INSERT INTO `user_oss` VALUES (14, 19, '个人网盘', 'user/user19/', 0, 2097152, '2019-04-11 16:44:41', '2019-04-11 16:44:41');
INSERT INTO `user_oss` VALUES (16, 21, '个人网盘', 'user/user21/', 0, 2097152, '2019-04-12 14:38:55', '2019-04-12 14:38:55');
INSERT INTO `user_oss` VALUES (17, 22, '个人网盘', 'user/user22/', 0, 2097152, '2019-04-12 16:38:13', '2019-04-12 16:38:13');

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `email_verified` smallint(6) NOT NULL DEFAULT 0,
  `tel` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tel_verified` smallint(6) NOT NULL DEFAULT 0,
  `email_token` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  `current_company_id` smallint(6) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `users_tel_unique`(`tel`) USING BTREE,
  UNIQUE INDEX `users_email_token_unique`(`email_token`) USING BTREE,
  UNIQUE INDEX `users_email_unique`(`email`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 23 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES (1, 'Pirvate', '$2y$10$MhEwldgDkiHDlN1wtIZKoe/Z20ERnEwOzbudWH6xIzNVgg2ubv4xa', '704356116@qq.com', 0, '16638638285', 1, 'GCbxWSmOdTjlUDRoQGVmoOBKdbb0OhYeGHIf7QDz', '2018-11-17 16:14:21', '2019-04-13 09:55:57', 1);
INSERT INTO `users` VALUES (2, '梦彧', '$2y$10$MhEwldgDkiHDlN1wtIZKoe/Z20ERnEwOzbudWH6xIzNVgg2ubv4xa', NULL, 0, '15237358571', 1, 'l6GIL0NAMUfhiEhASEbQwNhlECR15IDfDzdTRRc1', '2018-11-19 15:19:30', '2018-11-19 15:19:30', 1);
INSERT INTO `users` VALUES (3, 'mz', '$2y$10$MhEwldgDkiHDlN1wtIZKoe/Z20ERnEwOzbudWH6xIzNVgg2ubv4xa', NULL, 0, '15237367812', 1, '4PzCsz8QGOPDJOT73pHc0szar3VCaHQVRrwGZhxO', '2018-12-01 16:59:06', '2018-12-01 16:59:09', 1);
INSERT INTO `users` VALUES (4, '侃大山', '$2y$10$MhEwldgDkiHDlN1wtIZKoe/Z20ERnEwOzbudWH6xIzNVgg2ubv4xa', NULL, 0, '15100000002', 1, 'asffcbfdbdfbdb', '2018-12-01 16:59:28', '2019-04-15 17:46:15', 1);
INSERT INTO `users` VALUES (5, 'Soul', '$2y$10$MhEwldgDkiHDlN1wtIZKoe/Z20ERnEwOzbudWH6xIzNVgg2ubv4xa', NULL, 0, '13733607139', 1, 'Yr56F3cumdBIYs4nnMnyFe8j0oD1VkJNgftMfPNC', '2018-12-08 08:43:50', '2018-12-08 08:43:50', 1);
INSERT INTO `users` VALUES (6, '用户_13733607138', '$2y$10$up9p1yppqrq5E0eCAYrVVu0BD9qeyUIV0v2E.FO12AKtlMSkWJsw2', NULL, 0, '13733607138', 1, 'Rx7XoLCNQRmIBrYF1jHFdJC3AdqfuYT000jVWYKV', '2018-12-08 08:46:29', '2018-12-08 08:46:29', 1);
INSERT INTO `users` VALUES (7, '用户_13733607137', '$2y$10$cIeiZnxSihgZXT1yInqpB.NfBQOqE7Pgb/VaDguJJRPWDpCxwBuYC', NULL, 0, '13733607137', 1, 'fYBMtWJYPyjjNAXZ96hxZeI7mHbkrlkAaPbyuiut', '2018-12-08 08:49:46', '2018-12-08 08:49:46', 1);
INSERT INTO `users` VALUES (8, '用户_13733607135', '$2y$10$oZfIDDkMqqKSQHfGTXD5c.dzose2BAO4vMG3E5aYL3WjAvNzqgyqm', NULL, 0, '13733607135', 1, 'VBH6VLX9wtMienscMCp1u2835DSCB5yEnuwi6Df9', '2018-12-08 09:02:56', '2018-12-08 09:02:56', 1);
INSERT INTO `users` VALUES (9, '用户_13733607134', '$2y$10$ibWV9uuDSSV5wyHWTviGCeJauh4Nzo8BBRaZ4tp9zEXw3G5EdAq0C', NULL, 0, '13733607134', 1, 'y0buz2dUfehSAs5JYbV8iim1pgGs8nI1e03hqTOa', '2018-12-08 09:04:12', '2018-12-08 09:04:12', 1);
INSERT INTO `users` VALUES (10, 'Pirvate2', '$2y$10$MhEwldgDkiHDlN1wtIZKoe/Z20ERnEwOzbudWH6xIzNVgg2ubv4xa', NULL, 0, '13333333333', 1, 'rmF8MrwA5OLaigWvVhfqUDhsiLyh9resHsEtmCai', '2018-12-12 10:55:16', '2018-12-12 10:55:16', 1);
INSERT INTO `users` VALUES (11, '用户_15939965336', '$2y$10$TEcDe1JCRfderaFUOMabXevDUxAVZldEBj1BF7sO7gB3Cn6Uxt/C2', NULL, 0, '15939965336', 1, 'fE3LGEU9GuHwBmNWyXY4jsS27IREO6AojXLoE3lc', '2019-01-02 15:16:56', '2019-01-02 15:16:56', 1);
INSERT INTO `users` VALUES (12, '用户_14567845678', '$2y$10$Oc25qHxkveXuTT2L4i3xJ.ytyq1rfThCEldX2dSy1imljuTeWjzLq', NULL, 0, '14567845678', 1, '6QG6aebuESl6Ty6YrRzKyWpN7s8c9lcwhyLdAU8g', '2019-01-07 11:26:31', '2019-01-07 11:26:31', 0);
INSERT INTO `users` VALUES (13, '用户_17834567898', '$2y$10$A0HCMIhWmOsGtbVfi9KkJ.h.UTPf0pMAzqUyyGHjzQJWA50n6qko6', NULL, 0, '17834567898', 1, 'QDzloAqaGDkM4ZdPqfytGEFrSDKYBIbzLODzNk8n', '2019-01-07 13:57:11', '2019-01-07 13:57:11', 0);
INSERT INTO `users` VALUES (14, '132', '$2y$10$MhEwldgDkiHDlN1wtIZKoe/Z20ERnEwOzbudWH6xIzNVgg2ubv4xa', NULL, 0, '18749817085', 0, 'CvfzzcWs2hfNs6RsTI3Eb6rG4oFa9OvEgAobRNe5', '2019-01-07 14:05:04', '2019-04-15 14:25:05', 1);
INSERT INTO `users` VALUES (15, '用户_13164377353', '$2y$10$ZVeukgw9iUgXx2IO.KO4LetuwCc8NOImNq2Qx4UIyag4ggdhBRMrO', NULL, 0, '13164377353', 1, 'PZE82tN8y0B6R1OYt6bTWabTK644fsWN1n8bw3DP', '2019-01-12 14:16:45', '2019-01-12 14:16:45', 1);
INSERT INTO `users` VALUES (16, 'yonghu_123', '$2y$10$rzy4OiSFbxwN2aG54goFGOuQXCVKVIhqpMqSPzFjRilXnn90PXiWm', NULL, 0, '18749817080', 0, 'P0IiiV2kbBpSjleIJUJ1RBFfqa7Bp9HBIKzwdncz', '2019-01-12 14:16:45', '2019-01-12 14:16:45', 1);
INSERT INTO `users` VALUES (17, '用户_16637125678', '$2y$10$YKPUBhT9Op00/fScUjQYluXM82d.42IyzqqSRzMlsCoFeqx.gvD4y', NULL, 0, '16637125678', 1, 'AVtJt0FStI0WuA5yvwI5r9HF9gq4w7paEQGBOuqp', '2019-01-28 14:58:07', '2019-01-28 14:58:07', 0);
INSERT INTO `users` VALUES (18, '用户_17634766666', '$2y$10$aBAMm7vsLpXl8GN5Qko1K.zhr8TFtA5X6kNQD/LTeUcuhFjb0qZBS', NULL, 0, '17634766666', 1, 'lqveXOMioJftJYZORyJcE2jEEtAYymurFxH6GHY7', '2019-03-19 16:01:08', '2019-03-19 16:01:08', 1);
INSERT INTO `users` VALUES (19, '用户_15100000001', '$2y$10$4yGMnjh75hbxzKTCoqzYLuORjQ3anyr7gfdw5btP5DGgqggKnO3he', NULL, 0, '15100000001', 1, 'FmL2FxVSviiTh4cagNvvE3a2nBz7rd4CaAyS41Zc', '2019-04-11 16:44:41', '2019-04-13 17:56:08', 1);
INSERT INTO `users` VALUES (21, '用户_18749817055', '$2y$10$i3IbgASa4N1u61CycrmlReTgsM0FOjQ/blVEvt/6g7Di81TrCcxtq', NULL, 0, '18749817055', 1, 'URrCJTpmrjdAFgk4F1a0P284dG0w1oYAUgyCjZ11', '2019-04-12 14:38:55', '2019-04-12 14:38:55', 0);
INSERT INTO `users` VALUES (22, '用户_15100000003', '$2y$10$2Pp7/13RNedg7UODJbRu/ORDwKNIKKzu9HRCUzMEeXwqYCdCeTJK.', NULL, 0, '15100000003', 1, 'bhAU1U4AetlvjWhW76LTiSJPWB8h15vnfZbfZ6K5', '2019-04-12 16:38:07', '2019-04-12 16:38:07', 0);

SET FOREIGN_KEY_CHECKS = 1;
