<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1440279244.
 * Generated on 2015-08-22 22:34:04 by sacredskull
 */
class PropelMigration_1440279244
{
    public $comment = '';

    public function preUp($manager)
    {
        // add the pre-migration code here
    }

    public function postUp($manager)
    {
        // add the post-migration code here
    }

    public function preDown($manager)
    {
        // add the pre-migration code here
    }

    public function postDown($manager)
    {
        // add the post-migration code here
    }

    /**
     * Get the SQL statements for the Up migration
     *
     * @return array list of the SQL strings to execute for the Up migration
     *               the keys being the datasources
     */
    public function getUpSQL()
    {
        return array (
  'blog' => '
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `theme`;

DROP INDEX `article_slug` ON `article`;

CREATE UNIQUE INDEX `article_slug` ON `article` (`slug`(255));

DROP INDEX `category_slug` ON `category`;

CREATE UNIQUE INDEX `category_slug` ON `category` (`slug`(255));

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
    }

    /**
     * Get the SQL statements for the Down migration
     *
     * @return array list of the SQL strings to execute for the Down migration
     *               the keys being the datasources
     */
    public function getDownSQL()
    {
        return array (
  'blog' => '
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

DROP INDEX `article_slug` ON `article`;

CREATE UNIQUE INDEX `article_slug` ON `article` (`slug`);

DROP INDEX `category_slug` ON `category`;

CREATE UNIQUE INDEX `category_slug` ON `category` (`slug`);

CREATE TABLE `theme`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(30) NOT NULL,
    `root` VARCHAR(128) NOT NULL,
    `colour` VARCHAR(10) DEFAULT \'blue\',
    `slug` VARCHAR(255),
    PRIMARY KEY (`id`),
    UNIQUE INDEX `theme_slug` (`slug`)
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
    }

}