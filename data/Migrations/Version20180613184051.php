<?php declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180613184051 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE eve_corporation CHANGE alliance_id alliance_id INT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE eve_corporation RENAME INDEX k_ticker TO ix_c_ticker');
        $this->addSql('ALTER TABLE eve_corporation RENAME INDEX k_name TO ix_c_name');
        $this->addSql('ALTER TABLE eve_corporation RENAME INDEX k_alliance TO ix_c_alliance');
        $this->addSql('ALTER TABLE invCategories RENAME INDEX idx_cat_name TO ix_ic_catname');
        $this->addSql('ALTER TABLE invGroups DROP INDEX ix_invGroups_categoryID, ADD INDEX ix_ig_cid (categoryID)');
        $this->addSql('ALTER TABLE invGroups DROP INDEX idx_ig_cid, ADD INDEX ix_ig_categoryid (categoryID)');
        $this->addSql('DROP INDEX idx_ig_gname ON invGroups');
        $this->addSql('ALTER TABLE invGroups ADD CONSTRAINT FK_1A13E92CA7592BB9 FOREIGN KEY (categoryID) REFERENCES invCategories (categoryID)');
        $this->addSql('ALTER TABLE invItems RENAME INDEX idx_invitems_lid TO ix_ii_locationid');
        $this->addSql('ALTER TABLE invItems RENAME INDEX idx_invitems_oid_lid TO ix_ii_ownerid_locationid');
        $this->addSql('ALTER TABLE invTypeMaterials RENAME INDEX k_typeid TO ix_tm_typeid');
        $this->addSql('ALTER TABLE invTypeMaterials RENAME INDEX k_mtypeid TO ix_tm_mtypeid');
        $this->addSql('ALTER TABLE invTypes DROP INDEX ix_invTypes_groupID, ADD INDEX ix_it_groupid (groupID)');
        $this->addSql('DROP INDEX idx_it_group ON invTypes');
        $this->addSql('ALTER TABLE invTypes ADD CONSTRAINT FK_F2A7ECB4D6EFA878 FOREIGN KEY (groupID) REFERENCES invGroups (groupID)');
        $this->addSql('ALTER TABLE invTypes ADD CONSTRAINT FK_F2A7ECB4C0C5DD6B FOREIGN KEY (marketGroupID) REFERENCES invMarketGroups (marketGroupID)');
        $this->addSql('ALTER TABLE invTypes RENAME INDEX idx_it_mgroup TO ix_it_mgroup');
        $this->addSql('ALTER TABLE invTypes RENAME INDEX idx_it_name TO ix_it_name');
        $this->addSql('ALTER TABLE mapDenormalize DROP INDEX ix_mapDenormalize_typeID, ADD INDEX ix_md_typeid (typeID)');
        $this->addSql('DROP INDEX mapDenormalize_IX_groupSystem ON mapDenormalize');
        $this->addSql('DROP INDEX md_IX_name ON mapDenormalize');
        $this->addSql('DROP INDEX md_IX_typeid ON mapDenormalize');
        $this->addSql('ALTER TABLE mapDenormalize ADD CONSTRAINT FK_64B77626A20C70A2 FOREIGN KEY (regionID) REFERENCES mapLocationWormholeClasses (locationID)');
        $this->addSql('ALTER TABLE mapDenormalize RENAME INDEX mapdenormalize_ix_groupconstellation TO ix_md_groupconstellation');
        $this->addSql('ALTER TABLE mapDenormalize RENAME INDEX mapdenormalize_ix_groupregion TO ix_md_groupregion');
        $this->addSql('ALTER TABLE mapDenormalize RENAME INDEX ix_mapdenormalize_regionid TO ix_md_regionid');
        $this->addSql('ALTER TABLE mapDenormalize RENAME INDEX ix_mapdenormalize_solarsystemid TO ix_md_solarSystemid');
        $this->addSql('ALTER TABLE mapDenormalize RENAME INDEX ix_mapdenormalize_constellationid TO ix_md_constellationid');
        $this->addSql('ALTER TABLE mapDenormalize RENAME INDEX ix_mapdenormalize_orbitid TO ix_md_orbitid');
        $this->addSql('ALTER TABLE mapDenormalize RENAME INDEX md_ix_groupid TO ix_md_groupsystem');
        $this->addSql('ALTER TABLE mapSolarSystemJumps RENAME INDEX idx_fromsolar TO ix_mssj_fromsolar');
        $this->addSql('ALTER TABLE mapSolarSystemJumps RENAME INDEX idx_tosolar TO ix_mssj_tosolar');
        $this->addSql('ALTER TABLE at_cosmic_detail RENAME INDEX idx_cosmic_main_id TO idx_cd_main_id');
        $this->addSql('ALTER TABLE at_cosmic_detail RENAME INDEX idx_name_en TO idx_cd_name_en');
        $this->addSql('ALTER TABLE at_cosmic_detail RENAME INDEX idx_name_de TO idx_cd_name_de');
        $this->addSql('ALTER TABLE at_cosmic_detail RENAME INDEX idx_type TO idx_cd_type');
        $this->addSql('ALTER TABLE at_cosmic_detail RENAME INDEX idx_class TO idx_cd_class');
        $this->addSql('DROP INDEX idx_group ON at_structure');
        $this->addSql('ALTER TABLE at_structure DROP group_id');
        $this->addSql('ALTER TABLE at_structure RENAME INDEX idx_invitem TO idx_st_invitem');
        $this->addSql('ALTER TABLE at_structure RENAME INDEX idx_invtype TO idx_st_invtype');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE at_cosmic_detail RENAME INDEX idx_cd_main_id TO idx_cosmic_main_id');
        $this->addSql('ALTER TABLE at_cosmic_detail RENAME INDEX idx_cd_name_en TO idx_name_en');
        $this->addSql('ALTER TABLE at_cosmic_detail RENAME INDEX idx_cd_name_de TO idx_name_de');
        $this->addSql('ALTER TABLE at_cosmic_detail RENAME INDEX idx_cd_type TO idx_type');
        $this->addSql('ALTER TABLE at_cosmic_detail RENAME INDEX idx_cd_class TO idx_class');
        $this->addSql('ALTER TABLE at_structure ADD group_id INT DEFAULT NULL');
        $this->addSql('CREATE INDEX idx_group ON at_structure (group_id)');
        $this->addSql('ALTER TABLE at_structure RENAME INDEX idx_st_invitem TO idx_invitem');
        $this->addSql('ALTER TABLE at_structure RENAME INDEX idx_st_invtype TO idx_invtype');
        $this->addSql('ALTER TABLE eve_corporation CHANGE alliance_id alliance_id INT DEFAULT 0');
        $this->addSql('ALTER TABLE eve_corporation RENAME INDEX ix_c_ticker TO k_ticker');
        $this->addSql('ALTER TABLE eve_corporation RENAME INDEX ix_c_name TO k_name');
        $this->addSql('ALTER TABLE eve_corporation RENAME INDEX ix_c_alliance TO k_alliance');
        $this->addSql('ALTER TABLE invCategories RENAME INDEX ix_ic_catname TO idx_cat_name');
        $this->addSql('ALTER TABLE invGroups DROP INDEX ix_ig_cid, ADD INDEX ix_invGroups_categoryID (categoryID)');
        $this->addSql('ALTER TABLE invGroups DROP INDEX ix_ig_categoryid, ADD INDEX idx_ig_cid (categoryID)');
        $this->addSql('ALTER TABLE invGroups DROP FOREIGN KEY FK_1A13E92CA7592BB9');
        $this->addSql('CREATE INDEX idx_ig_gname ON invGroups (groupName)');
        $this->addSql('ALTER TABLE invItems RENAME INDEX ix_ii_locationid TO IDX_invItems_LID');
        $this->addSql('ALTER TABLE invItems RENAME INDEX ix_ii_ownerid_locationid TO IDX_invItems_OID_LID');
        $this->addSql('ALTER TABLE invTypeMaterials RENAME INDEX ix_tm_typeid TO k_typeid');
        $this->addSql('ALTER TABLE invTypeMaterials RENAME INDEX ix_tm_mtypeid TO k_mtypeid');
        $this->addSql('ALTER TABLE invTypes DROP FOREIGN KEY FK_F2A7ECB4D6EFA878');
        $this->addSql('ALTER TABLE invTypes DROP FOREIGN KEY FK_F2A7ECB4C0C5DD6B');
        $this->addSql('CREATE INDEX idx_it_group ON invTypes (groupID)');
        $this->addSql('ALTER TABLE invTypes RENAME INDEX ix_it_groupid TO ix_invTypes_groupID');
        $this->addSql('ALTER TABLE invTypes RENAME INDEX ix_it_name TO idx_it_name');
        $this->addSql('ALTER TABLE invTypes RENAME INDEX ix_it_mgroup TO idx_it_mgroup');
        $this->addSql('ALTER TABLE mapDenormalize DROP FOREIGN KEY FK_64B77626A20C70A2');
        $this->addSql('CREATE INDEX mapDenormalize_IX_groupSystem ON mapDenormalize (groupID, solarSystemID)');
        $this->addSql('CREATE INDEX md_IX_name ON mapDenormalize (itemName)');
        $this->addSql('CREATE INDEX md_IX_typeid ON mapDenormalize (typeID)');
        $this->addSql('ALTER TABLE mapDenormalize RENAME INDEX ix_md_groupconstellation TO mapDenormalize_IX_groupConstellation');
        $this->addSql('ALTER TABLE mapDenormalize RENAME INDEX ix_md_groupregion TO mapDenormalize_IX_groupRegion');
        $this->addSql('ALTER TABLE mapDenormalize RENAME INDEX ix_md_typeid TO ix_mapDenormalize_typeID');
        $this->addSql('ALTER TABLE mapDenormalize RENAME INDEX ix_md_regionid TO ix_mapDenormalize_regionID');
        $this->addSql('ALTER TABLE mapDenormalize RENAME INDEX ix_md_solarsystemid TO ix_mapDenormalize_solarSystemID');
        $this->addSql('ALTER TABLE mapDenormalize RENAME INDEX ix_md_orbitid TO ix_mapDenormalize_orbitID');
        $this->addSql('ALTER TABLE mapDenormalize RENAME INDEX ix_md_constellationid TO ix_mapDenormalize_constellationID');
        $this->addSql('ALTER TABLE mapDenormalize RENAME INDEX ix_md_groupsystem TO md_IX_groupid');
        $this->addSql('ALTER TABLE mapSolarSystemJumps RENAME INDEX ix_mssj_fromsolar TO IDX_fromSolar');
        $this->addSql('ALTER TABLE mapSolarSystemJumps RENAME INDEX ix_mssj_tosolar TO IDX_toSolar');
    }
}
