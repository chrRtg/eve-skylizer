
## to be imported

invCategories
invGroups
invMarketGroups
invTypes
invTypeMaterials
mapDenormalize
mapLocationWormholeClasses
mapSolarSystemJumps
trnTranslations

## download them from Steve Ronuken
wget https://www.fuzzwork.co.uk/dump/latest/invCategories.sql.bz2
wget https://www.fuzzwork.co.uk/dump/latest/invGroups.sql.bz2
wget https://www.fuzzwork.co.uk/dump/latest/invMarketGroups.sql.bz2
wget https://www.fuzzwork.co.uk/dump/latest/invTypes.sql.bz2
wget https://www.fuzzwork.co.uk/dump/latest/invTypeMaterials.sql.bz2
wget https://www.fuzzwork.co.uk/dump/latest/mapDenormalize.sql.bz2
wget https://www.fuzzwork.co.uk/dump/latest/mapLocationWormholeClasses.sql.bz2
wget https://www.fuzzwork.co.uk/dump/latest/mapSolarSystemJumps.sql.bz2
wget https://www.fuzzwork.co.uk/dump/latest/trnTranslations.sql.bz2

## then unzip and import to your DB
bzcat *.bz2 | mysql <your db> -u root -p 

## afterwards update the indices with post_update.sql
SET FOREIGN_KEY_CHECKS = 0;
ALTER TABLE invCategories ADD INDEX idx_cat_name (categoryName ASC);
ALTER TABLE invGroups ADD INDEX idx_ig_cid (categoryID ASC), ADD INDEX idx_ig_gname (groupName ASC);
ALTER TABLE invMarketGroups ADD INDEX idx_mg_parent (parentGroupID ASC), ADD INDEX idx_mg_name (marketGroupName ASC);
ALTER TABLE invTypes ADD INDEX idx_it_group (groupID ASC), ADD INDEX idx_it_name (typeName ASC),ADD INDEX idx_it_mgroup (marketGroupID ASC);
ALTER TABLE invTypeMaterials ADD INDEX idx_typeid (typeID ASC), ADD INDEX idx_mtypeid (materialTypeID ASC);
ALTER TABLE trnTranslations ADD INDEX idx_tt_tcid (tcID ASC), ADD INDEX idx_tt_kid (keyID ASC), ADD INDEX idx_tt_text (text(20) ASC), ADD INDEX idx_tt_lang (languageID ASC);
ALTER TABLE mapDenormalize  ADD INDEX md_IX_name (itemName(15) ASC), ADD INDEX md_IX_groupid (groupID ASC), ADD INDEX md_IX_typeid (typeID ASC);
ALTER TABLE mapSolarSystemJumps ADD INDEX IDX_fromSolar (fromSolarSystemID ASC), ADD INDEX IDX_toSolar (toSolarSystemID ASC);
SET FOREIGN_KEY_CHECKS = 1;
