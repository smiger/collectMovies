ALTER DATABASE `collectMovies` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
ALTER TABLE `mac_type` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `mac_vod` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;


INSERT INTO `mac_type` VALUES ('1', '电影', 'dianying', '1', '0', '1');
INSERT INTO `mac_type` VALUES ('2', '连续剧', 'lianxuju', '2', '0', '1');
INSERT INTO `mac_type` VALUES ('3', '综艺', 'zongyi', '3', '0', '1');
INSERT INTO `mac_type` VALUES ('4', '动漫', 'dongman', '4', '0', '1');
INSERT INTO `mac_type` VALUES ('5', '其他', 'qita', '5', '0', '0');

INSERT INTO `mac_type` VALUES ('101', '动作片', 'dongzuopian', '1', '1', '1');
INSERT INTO `mac_type` VALUES ('102', '喜剧片', 'xijupian', '2', '1', '1');
INSERT INTO `mac_type` VALUES ('103', '爱情片', 'aiqingpian', '3', '1', '1');
INSERT INTO `mac_type` VALUES ('104', '科幻片', 'kehuanpian', '4', '1', '1');
INSERT INTO `mac_type` VALUES ('105', '恐怖片', 'kongbupian', '5', '1', '1');
INSERT INTO `mac_type` VALUES ('106', '剧情片', 'juqingpian', '6', '1', '1');
INSERT INTO `mac_type` VALUES ('107', '战争片', 'zhanzhengpian', '7', '1', '1');
INSERT INTO `mac_type` VALUES ('108', '伦理片', 'lunli', '8', '1', '1');
INSERT INTO `mac_type` VALUES ('199', '其他', 'qita', '9', '1', '1');

INSERT INTO `mac_type` VALUES ('201', '国产剧', 'guochanju', '1', '2', '1');
INSERT INTO `mac_type` VALUES ('202', '港台剧', 'gangtaiju', '2', '2', '1');
INSERT INTO `mac_type` VALUES ('203', '日韩剧', 'rihanju', '3', '2', '1');
INSERT INTO `mac_type` VALUES ('204', '欧美剧', 'oumeiju', '4', '2', '1');
INSERT INTO `mac_type` VALUES ('205', '泰国剧', 'taiju', '5', '2', '1');
INSERT INTO `mac_type` VALUES ('206', '纪录片', 'jilu', '6', '2', '1');
INSERT INTO `mac_type` VALUES ('299', '其他剧', 'qita', '99', '2', '1');

INSERT INTO `mac_type` VALUES ('301', '国产综艺', 'guochan', '1', '3', '1');
INSERT INTO `mac_type` VALUES ('302', '日韩综艺', 'rihan', '2', '3', '1');
INSERT INTO `mac_type` VALUES ('303', '港台综艺', 'gangtai', '3', '3', '1');
INSERT INTO `mac_type` VALUES ('304', '欧美综艺', 'oumei', '4', '3', '1');
INSERT INTO `mac_type` VALUES ('399', '其他综艺', 'qita', '99', '3', '1');

INSERT INTO `mac_type` VALUES ('401', '国产动漫', 'guochan', '1', '4', '1');
INSERT INTO `mac_type` VALUES ('402', '日韩动漫', 'rihan', '2', '4', '1');
INSERT INTO `mac_type` VALUES ('403', '港台动漫', 'gangtai', '3', '4', '1');
INSERT INTO `mac_type` VALUES ('404', '欧美动漫', 'oumei', '4', '4', '1');
INSERT INTO `mac_type` VALUES ('499', '其他动漫', 'qita', '99', '4', '1');
