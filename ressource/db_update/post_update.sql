use skylizer;

#
# Update some indices
#

ALTER TABLE `invCategories` 
ADD INDEX `idx_cat_name` (`categoryName` ASC);

ALTER TABLE `invGroups` 
ADD INDEX `idx_ig_cid` (`categoryID` ASC),
ADD INDEX `idx_ig_gname` (`groupName` ASC);

ALTER TABLE `invMarketGroups` 
ADD INDEX `idx_mg_parent` (`parentGroupID` ASC),
ADD INDEX `idx_mg_name` (`marketGroupName` ASC);

ALTER TABLE `invTypes` 
ADD INDEX `idx_it_group` (`groupID` ASC),
ADD INDEX `idx_it_name` (`typeName` ASC),
ADD INDEX `idx_it_mgroup` (`marketGroupID` ASC);

ALTER TABLE `invTypeMaterials` 
ADD INDEX `k_typeid` (`typeID` ASC)  COMMENT '',
ADD INDEX `k_mtypeid` (`materialTypeID` ASC)  COMMENT '';

ALTER TABLE `trnTranslations` 
ADD INDEX `idx_tt_tcid` (`tcID` ASC),
ADD INDEX `idx_tt_kid` (`keyID` ASC),
ADD INDEX `idx_tt_text` (`text`(20) ASC),
ADD INDEX `idx_tt_lang` (`languageID` ASC);

ALTER TABLE `mapDenormalize` 
ADD INDEX `md_IX_name` (`itemName`(15) ASC),
ADD INDEX `md_IX_groupid` (`groupID` ASC),
ADD INDEX `md_IX_typeid` (`typeID` ASC);

ALTER TABLE `mapSolarSystemJumps` 
ADD INDEX `IDX_fromSolar` (`fromSolarSystemID` ASC),
ADD INDEX `IDX_toSolar` (`toSolarSystemID` ASC);

