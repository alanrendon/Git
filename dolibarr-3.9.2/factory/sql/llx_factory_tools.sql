

CREATE TABLE IF NOT EXISTS  `llx_factory_tools` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_operator` int(11) DEFAULT NULL,
  `fk_product` int(11) DEFAULT NULL,
  `qty` float DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `comment` longtext,
  `dateCreation` date DEFAULT NULL,
  `dateDeliver` date DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
