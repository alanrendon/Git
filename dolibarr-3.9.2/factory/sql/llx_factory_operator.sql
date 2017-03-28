CREATE TABLE IF NOT EXISTS  `llx_factory_operator` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `ref` varchar(20) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  PRIMARY KEY (`rowid`)
);
