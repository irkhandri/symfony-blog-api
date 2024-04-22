<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240403081411 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE blog (id INT AUTO_INCREMENT NOT NULL, profile_id INT NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(2222) DEFAULT NULL, image_url VARCHAR(1111) DEFAULT NULL, INDEX IDX_C0155143CCFA12B8 (profile_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE blog_tag (blog_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_6EC3989DAE07E97 (blog_id), INDEX IDX_6EC3989BAD26311 (tag_id), PRIMARY KEY(blog_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, profile_id INT NOT NULL, blog_id INT NOT NULL, created DATETIME NOT NULL, description VARCHAR(2047) DEFAULT NULL, rate VARCHAR(255) DEFAULT NULL, INDEX IDX_9474526CCCFA12B8 (profile_id), INDEX IDX_9474526CDAE07E97 (blog_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE interest (id INT AUTO_INCREMENT NOT NULL, profile_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description VARCHAR(2047) DEFAULT NULL, INDEX IDX_6C3E1A67CCFA12B8 (profile_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, sender_id INT DEFAULT NULL, recipient_id INT DEFAULT NULL, subject VARCHAR(255) DEFAULT NULL, text VARCHAR(2047) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, is_read TINYINT(1) DEFAULT NULL, created DATETIME DEFAULT NULL, INDEX IDX_B6BD307FF624B39D (sender_id), INDEX IDX_B6BD307FE92F8F78 (recipient_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE profile (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, name VARCHAR(255) DEFAULT NULL, username VARCHAR(255) DEFAULT NULL, location VARCHAR(255) DEFAULT NULL, number VARCHAR(255) DEFAULT NULL, soc_facebook VARCHAR(255) DEFAULT NULL, soc_linkedin VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, image_url VARCHAR(255) DEFAULT NULL, intro VARCHAR(255) DEFAULT NULL, bio VARCHAR(2222) DEFAULT NULL, UNIQUE INDEX UNIQ_8157AA0FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE blog ADD CONSTRAINT FK_C0155143CCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id)');
        $this->addSql('ALTER TABLE blog_tag ADD CONSTRAINT FK_6EC3989DAE07E97 FOREIGN KEY (blog_id) REFERENCES blog (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE blog_tag ADD CONSTRAINT FK_6EC3989BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CCCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CDAE07E97 FOREIGN KEY (blog_id) REFERENCES blog (id)');
        $this->addSql('ALTER TABLE interest ADD CONSTRAINT FK_6C3E1A67CCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FF624B39D FOREIGN KEY (sender_id) REFERENCES profile (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FE92F8F78 FOREIGN KEY (recipient_id) REFERENCES profile (id)');
        $this->addSql('ALTER TABLE profile ADD CONSTRAINT FK_8157AA0FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE blog DROP FOREIGN KEY FK_C0155143CCFA12B8');
        $this->addSql('ALTER TABLE blog_tag DROP FOREIGN KEY FK_6EC3989DAE07E97');
        $this->addSql('ALTER TABLE blog_tag DROP FOREIGN KEY FK_6EC3989BAD26311');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CCCFA12B8');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CDAE07E97');
        $this->addSql('ALTER TABLE interest DROP FOREIGN KEY FK_6C3E1A67CCFA12B8');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FF624B39D');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FE92F8F78');
        $this->addSql('ALTER TABLE profile DROP FOREIGN KEY FK_8157AA0FA76ED395');
        $this->addSql('DROP TABLE blog');
        $this->addSql('DROP TABLE blog_tag');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE interest');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE profile');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE user');
    }
}
