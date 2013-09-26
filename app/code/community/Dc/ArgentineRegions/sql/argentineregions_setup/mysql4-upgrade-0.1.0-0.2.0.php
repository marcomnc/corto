<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DELETE FROM {$installer->getTable('directory_country_region')} WHERE country_id = 'AR';

INSERT INTO {$installer->getTable('directory_country_region')} (country_id,code,default_name)
VALUES(
'AR','Buenos Aires','Buenos Aires');

INSERT INTO {$installer->getTable('directory_country_region')} (country_id,code,default_name)
VALUES(
'AR','Capital Federal','Capital Federal');

INSERT INTO {$installer->getTable('directory_country_region')} (country_id,code,default_name)
VALUES(
'AR','Catamarca','Catamarca');

INSERT INTO {$installer->getTable('directory_country_region')} (country_id,code,default_name)
VALUES(
'AR','Chaco','Chaco');

INSERT INTO {$installer->getTable('directory_country_region')} (country_id,code,default_name)
VALUES(
'AR','Chubut','Chubut');

INSERT INTO {$installer->getTable('directory_country_region')} (country_id,code,default_name)
VALUES(
'AR','Cordoba','Córdoba');

INSERT INTO {$installer->getTable('directory_country_region')} (country_id,code,default_name)
VALUES(
'AR','Corrientes','Corrientes');

INSERT INTO {$installer->getTable('directory_country_region')} (country_id,code,default_name)
VALUES(
'AR','Entre Rios','Entre Ríos');

INSERT INTO {$installer->getTable('directory_country_region')} (country_id,code,default_name)
VALUES(
'AR','Formosa','Formosa');

INSERT INTO {$installer->getTable('directory_country_region')} (country_id,code,default_name)
VALUES(
'AR','Jujuy','Jujuy');

INSERT INTO {$installer->getTable('directory_country_region')} (country_id,code,default_name)
VALUES(
'AR','La Pampa','La Pampa');

INSERT INTO {$installer->getTable('directory_country_region')} (country_id,code,default_name)
VALUES(
'AR','La Rioja','La Rioja');

INSERT INTO {$installer->getTable('directory_country_region')} (country_id,code,default_name)
VALUES(
'AR','Mendoza','Mendoza');

INSERT INTO {$installer->getTable('directory_country_region')} (country_id,code,default_name)
VALUES(
'AR','Misiones','Misiones');

INSERT INTO {$installer->getTable('directory_country_region')} (country_id,code,default_name)
VALUES(
'AR','Neuquen','Neuquén');

INSERT INTO {$installer->getTable('directory_country_region')} (country_id,code,default_name)
VALUES(
'AR','Rio Negro','Río Negro');

INSERT INTO {$installer->getTable('directory_country_region')} (country_id,code,default_name)
VALUES(
'AR','Salta','Salta');

INSERT INTO {$installer->getTable('directory_country_region')} (country_id,code,default_name)
VALUES(
'AR','San Juan','San Juan');

INSERT INTO {$installer->getTable('directory_country_region')} (country_id,code,default_name)
VALUES(
'AR','San Luis','San Luis');

INSERT INTO {$installer->getTable('directory_country_region')} (country_id,code,default_name)
VALUES(
'AR','Santa Cruz','Santa Cruz');

INSERT INTO {$installer->getTable('directory_country_region')} (country_id,code,default_name)
VALUES(
'AR','Santa Fe','Santa Fe');

INSERT INTO {$installer->getTable('directory_country_region')} (country_id,code,default_name)
VALUES(
'AR','Santiago del Estero','Santiago del Estero');

INSERT INTO {$installer->getTable('directory_country_region')} (country_id,code,default_name)
VALUES(
'AR','Tierra del Fuego','Tierra del Fuego, Antártida e Islas del Atlántico Sur');

INSERT INTO {$installer->getTable('directory_country_region')} (country_id,code,default_name)
VALUES(
'AR','Tucuman','Tucumán');

    ");

$installer->endSetup(); 