/*
Navicat MySQL Data Transfer

Source Server         : sii
Source Server Version : 50624
Source Host           : localhost:3306
Source Database       : dolibarr392

Target Server Type    : MYSQL
Target Server Version : 50624
File Encoding         : 65001

Date: 2017-03-02 12:03:32
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for llx_factory_details
-- ----------------------------
DROP TABLE IF EXISTS `llx_factory_details`;
CREATE TABLE `llx_factory_details` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_propal` int(11) DEFAULT NULL,
  `dateFactoryPlanned` date DEFAULT NULL,
  `dateFactoryReal` date DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `fk_entrepot` int(11) DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
