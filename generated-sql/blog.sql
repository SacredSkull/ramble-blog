
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- article
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `article`;

CREATE TABLE `article`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `bodyHTML` TEXT,
    `body` TEXT NOT NULL,
    `tags` VARCHAR(255) NOT NULL,
    `positive_votes` INTEGER DEFAULT 0,
    `negative_votes` INTEGER DEFAULT 0,
    `theme_id` INTEGER DEFAULT 0 NOT NULL,
    `image` VARCHAR(255) DEFAULT 'default/post_img.png',
    `draft` TINYINT(1) DEFAULT 0,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    `slug` VARCHAR(255),
    PRIMARY KEY (`id`),
    UNIQUE INDEX `article_slug` (`slug`(255)),
    INDEX `article_fi_e23503` (`theme_id`),
    CONSTRAINT `article_fk_e23503`
        FOREIGN KEY (`theme_id`)
        REFERENCES `theme` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- theme
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `theme`;

CREATE TABLE `theme`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(60) NOT NULL,
    `root` VARCHAR(128) NOT NULL,
    `colour` VARCHAR(10) DEFAULT 'blue',
    `slug` VARCHAR(255),
    PRIMARY KEY (`id`),
    UNIQUE INDEX `theme_slug` (`slug`(255))
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- view
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `view`;

CREATE TABLE `view`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `ip_address` VARCHAR(30) NOT NULL,
    `time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `article_id` INTEGER DEFAULT 0 NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `view_fi_3610e9` (`article_id`),
    CONSTRAINT `view_fk_3610e9`
        FOREIGN KEY (`article_id`)
        REFERENCES `article` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
