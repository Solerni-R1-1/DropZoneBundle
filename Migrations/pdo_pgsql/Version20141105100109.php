<?php

namespace Icap\DropzoneBundle\Migrations\pdo_pgsql;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated migration based on mapping information: modify it with caution
 *
 * Generation date: 2014/11/05 10:01:11
 */
class Version20141105100109 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("
            ALTER TABLE icap__dropzonebundle_dropzone 
            ADD forumCategory_id INT DEFAULT NULL
        ");
        $this->addSql("
            ALTER TABLE icap__dropzonebundle_dropzone 
            ADD CONSTRAINT FK_6782FC2375A75F08 FOREIGN KEY (forumCategory_id) 
            REFERENCES claro_forum_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        ");
        $this->addSql("
            CREATE INDEX IDX_6782FC2375A75F08 ON icap__dropzonebundle_dropzone (forumCategory_id)
        ");
    }

    public function down(Schema $schema)
    {
        $this->addSql("
            ALTER TABLE icap__dropzonebundle_dropzone 
            DROP CONSTRAINT FK_6782FC2375A75F08
        ");
        $this->addSql("
            DROP INDEX IDX_6782FC2375A75F08
        ");
        $this->addSql("
            ALTER TABLE icap__dropzonebundle_dropzone 
            DROP forumCategory_id
        ");
    }
}