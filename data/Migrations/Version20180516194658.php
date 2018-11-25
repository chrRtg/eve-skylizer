<?php

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180516194658 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('SET FOREIGN_KEY_CHECKS = 0');
        $this->addSql('CREATE TABLE at_structure (id INT AUTO_INCREMENT NOT NULL, type_id INT DEFAULT NULL COMMENT \'invTypes in case of structures\', item_id BIGINT DEFAULT NULL COMMENT \'invNames -> mapDenormalize in case of celestials\', group_id INT NOT NULL, corporation_id INT DEFAULT NULL, structure_name VARCHAR(255) DEFAULT NULL COMMENT \'player give name\', created_by INT NOT NULL, create_date DATETIME NOT NULL, lastseen_by INT NOT NULL, lastseen_date DATETIME NOT NULL, INDEX idx_invitem (item_id), INDEX idx_group (group_id), INDEX idx_invtype (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE invGroups ADD CONSTRAINT FK_1A13E92CA7592BB9 FOREIGN KEY (categoryID) REFERENCES invCategories (categoryID)');
        $this->addSql('CREATE INDEX k_typeid ON invTypeMaterials (typeID)');
        $this->addSql('CREATE INDEX k_mtypeid ON invTypeMaterials (materialTypeID)');
        $this->addSql('ALTER TABLE invTypes ADD CONSTRAINT FK_F2A7ECB4D6EFA878 FOREIGN KEY (groupID) REFERENCES invGroups (groupID)');
        $this->addSql('ALTER TABLE invTypes ADD CONSTRAINT FK_F2A7ECB4C0C5DD6B FOREIGN KEY (marketGroupID) REFERENCES invMarketGroups (marketGroupID)');
        $this->addSql('CREATE INDEX marketGroupID_idx ON invTypes (marketGroupID)');
        $this->addSql('DROP INDEX ix_mapDenormalize_solarSystemID ON mapDenormalize');
        $this->addSql('DROP INDEX md_IX_name ON mapDenormalize');
        $this->addSql('DROP INDEX md_IX_groupid ON mapDenormalize');
        $this->addSql('DROP INDEX md_IX_typeid ON mapDenormalize');
        $this->addSql('DROP INDEX mapDenormalize_IX_groupSystem ON mapDenormalize');
        $this->addSql('ALTER TABLE mapDenormalize ADD CONSTRAINT FK_64B77626A20C70A2 FOREIGN KEY (regionID) REFERENCES mapLocationWormholeClasses (locationID)');
        $this->addSql('CREATE INDEX mapDenormalize_IX_groupSystem ON mapDenormalize (groupID)');
        $this->addSql('ALTER TABLE permission RENAME INDEX name_idx TO UNIQ_E04992AA5E237E06');
        $this->addSql('ALTER TABLE at_cosmic_detail CHANGE cosmic_detail_id cosmic_detail_id INT AUTO_INCREMENT NOT NULL, CHANGE cosmic_main_id cosmic_main_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE at_cosmic_main CHANGE cosmic_main_id cosmic_main_id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE at_moon CHANGE eve_mapdenormalize_itemid eve_mapdenormalize_itemid INT DEFAULT NULL, CHANGE eve_invtypes_typeid eve_invtypes_typeid INT DEFAULT NULL');
        $this->addSql('ALTER TABLE at_moongoo CHANGE eve_invtypes_typeid eve_invtypes_typeid INT DEFAULT NULL, CHANGE moon_id moon_id INT DEFAULT NULL, CHANGE goo_amount goo_amount DOUBLE PRECISION NOT NULL');
        $this->addSql('SET FOREIGN_KEY_CHECKS = 1');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('SET FOREIGN_KEY_CHECKS = 0');
        $this->addSql('DROP TABLE at_structure');
        $this->addSql('ALTER TABLE at_cosmic_detail CHANGE cosmic_detail_id cosmic_detail_id INT NOT NULL, CHANGE cosmic_main_id cosmic_main_id INT NOT NULL');
        $this->addSql('ALTER TABLE at_cosmic_main CHANGE cosmic_main_id cosmic_main_id INT NOT NULL');
        $this->addSql('ALTER TABLE at_moon CHANGE eve_mapdenormalize_itemid eve_mapdenormalize_itemid INT DEFAULT 0 NOT NULL, CHANGE eve_invtypes_typeid eve_invtypes_typeid INT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE at_moongoo CHANGE eve_invtypes_typeid eve_invtypes_typeid INT DEFAULT 0 NOT NULL, CHANGE moon_id moon_id INT DEFAULT 0 NOT NULL, CHANGE goo_amount goo_amount DOUBLE PRECISION DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE invGroups DROP FOREIGN KEY FK_1A13E92CA7592BB9');
        $this->addSql('DROP INDEX k_typeid ON invTypeMaterials');
        $this->addSql('DROP INDEX k_mtypeid ON invTypeMaterials');
        $this->addSql('ALTER TABLE invTypes DROP FOREIGN KEY FK_F2A7ECB4D6EFA878');
        $this->addSql('ALTER TABLE invTypes DROP FOREIGN KEY FK_F2A7ECB4C0C5DD6B');
        $this->addSql('DROP INDEX marketGroupID_idx ON invTypes');
        $this->addSql('ALTER TABLE mapDenormalize DROP FOREIGN KEY FK_64B77626A20C70A2');
        $this->addSql('DROP INDEX mapDenormalize_IX_groupSystem ON mapDenormalize');
        $this->addSql('CREATE INDEX ix_mapDenormalize_solarSystemID ON mapDenormalize (solarSystemID)');
        $this->addSql('CREATE INDEX md_IX_name ON mapDenormalize (itemName)');
        $this->addSql('CREATE INDEX md_IX_groupid ON mapDenormalize (groupID)');
        $this->addSql('CREATE INDEX md_IX_typeid ON mapDenormalize (typeID)');
        $this->addSql('CREATE INDEX mapDenormalize_IX_groupSystem ON mapDenormalize (groupID, solarSystemID)');
        $this->addSql('ALTER TABLE permission RENAME INDEX uniq_e04992aa5e237e06 TO name_idx');
        $this->addSql('SET FOREIGN_KEY_CHECKS = 1');
    }
}
