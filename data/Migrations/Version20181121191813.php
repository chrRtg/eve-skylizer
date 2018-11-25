<?php declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181121191813 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('SET FOREIGN_KEY_CHECKS = 0');
        $this->addSql('ALTER TABLE permission RENAME INDEX name_idx TO UNIQ_E04992AA5E237E06');
        $this->addSql('ALTER TABLE at_moon CHANGE eve_mapdenormalize_itemid eve_mapdenormalize_itemid INT DEFAULT NULL, CHANGE eve_invtypes_typeid eve_invtypes_typeid INT DEFAULT NULL');
        $this->addSql('ALTER TABLE at_moongoo CHANGE moon_id moon_id INT DEFAULT NULL, CHANGE eve_invtypes_typeid eve_invtypes_typeid INT DEFAULT NULL, CHANGE goo_amount goo_amount DOUBLE PRECISION NOT NULL');
        $this->addSql('DROP INDEX idx_st_invitem ON at_structure');
        $this->addSql('ALTER TABLE at_structure ADD group_id BIGINT DEFAULT NULL, ADD signature VARCHAR(10) DEFAULT NULL COMMENT \'scan signature\', ADD scan_quality INT DEFAULT NULL, ADD scan_type VARCHAR(10) DEFAULT NULL COMMENT \'scan signature\', ADD solar_system_id INT DEFAULT NULL COMMENT \'maps to mapdenormalize.itemID to indicate the solarsystem\', ADD celestial_id INT DEFAULT NULL COMMENT \'maps to mapdenormalize. Indicates the nearest celstial\', ADD celestial_distance BIGINT DEFAULT NULL COMMENT \'in KM, how far is the structure away from the celestial\', ADD at_cosmic_detail_id INT DEFAULT NULL COMMENT \'maps to at_cosmic_detail if entity is a site\', ADD target_system_id INT DEFAULT NULL COMMENT \'to link this structure to another solarsystem - wh or gate\'');
        // Model-Data Migration
        $this->addSql('UPDATE at_structure set celestial_id = item_id;');
        $this->addSql('UPDATE at_structure set group_id = 1406, scan_type = \'STRUCT\', celestial_distance = 5000 where type_id in (35835, 35836);');
        // further regular migration
        $this->addSql('ALTER TABLE at_structure DROP item_id');
        $this->addSql('CREATE INDEX idx_targetsystem_id ON at_structure (target_system_id)');
        $this->addSql('CREATE INDEX idx_cosmic_detail_id ON at_structure (at_cosmic_detail_id)');
        $this->addSql('CREATE INDEX idx_lastseen_data ON at_structure (lastseen_date)');
        $this->addSql('CREATE INDEX idx_create_date ON at_structure (create_date)');
        $this->addSql('SET FOREIGN_KEY_CHECKS = 1');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('SET FOREIGN_KEY_CHECKS = 0');
        $this->addSql('ALTER TABLE at_moon CHANGE eve_mapdenormalize_itemid eve_mapdenormalize_itemid INT DEFAULT 0 NOT NULL, CHANGE eve_invtypes_typeid eve_invtypes_typeid INT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE at_moongoo CHANGE eve_invtypes_typeid eve_invtypes_typeid INT DEFAULT 0 NOT NULL, CHANGE moon_id moon_id INT DEFAULT 0 NOT NULL, CHANGE goo_amount goo_amount DOUBLE PRECISION DEFAULT \'0\' NOT NULL');
        $this->addSql('DROP INDEX idx_targetsystem_id ON at_structure');
        $this->addSql('DROP INDEX idx_cosmic_detail_id ON at_structure');
        $this->addSql('DROP INDEX idx_lastseen_data ON at_structure');
        $this->addSql('DROP INDEX idx_create_date ON at_structure');
        $this->addSql('ALTER TABLE at_structure ADD item_id BIGINT DEFAULT NULL COMMENT \'invNames -> mapDenormalize in case of celestials\', DROP group_id, DROP signature, DROP scan_quality, DROP scan_type, DROP solar_system_id, DROP celestial_id, DROP celestial_distance, DROP at_cosmic_detail_id, DROP target_system_id');
        $this->addSql('CREATE INDEX idx_st_invitem ON at_structure (item_id)');
        $this->addSql('ALTER TABLE permission RENAME INDEX uniq_e04992aa5e237e06 TO name_idx');
        $this->addSql('SET FOREIGN_KEY_CHECKS = 1');
    }
}
