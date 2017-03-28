-- --------------------------------------------------------

--
-- Structure de la table `llx_equipement_factory`
--
-- Contient le lien entre les equipements et factory

CREATE TABLE IF NOT EXISTS `llx_equipement_factory` (
  `fk_equipement` int(11) NOT NULL DEFAULT '0',
  `fk_factory` 	int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `uk_factory_equipement` (`fk_equipement`,`fk_factory`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ;
