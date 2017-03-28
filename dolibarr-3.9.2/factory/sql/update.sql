ALTER TABLE  `llx_factorydet`	  	ADD  `globalqty` 			INTEGER NOT NULL DEFAULT '0';		-- La quantité est à prendre au détail ou au global
ALTER TABLE  `llx_factorydet`		ADD  `description`	text	;
ALTER TABLE  `llx_product_factory`	ADD  `globalqty` 			INTEGER NOT NULL DEFAULT '0';		-- La quantité est à prendre au détail ou au global
ALTER TABLE  `llx_product_factory`	ADD  `description`	text	;
ALTER TABLE  `llx_factory`			ADD  `entity`		Integer 		NOT NULL	DEFAULT 1;
ALTER TABLE  `llx_factory`			ADD  `no_serie`		text	;