
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
    `title` VARCHAR(255) DEFAULT 'Untitled',
    `bodyHTML` TEXT,
    `body` TEXT,
    `category_id` INTEGER DEFAULT 0 NOT NULL,
    `image` VARCHAR(255) DEFAULT 'default/post_img.png',
    `draft` TINYINT(1) DEFAULT 0,
    `poll_question` VARCHAR(255) DEFAULT 'false',
    `created_at` DATETIME,
    `updated_at` DATETIME,
    `slug` VARCHAR(255),
    PRIMARY KEY (`id`),
    UNIQUE INDEX `article_slug` (`slug`(255)),
    INDEX `article_fi_904832` (`category_id`),
    CONSTRAINT `article_fk_904832`
        FOREIGN KEY (`category_id`)
        REFERENCES `category` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- tag
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `tag`;

CREATE TABLE `tag`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(60) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- article_tag
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `article_tag`;

CREATE TABLE `article_tag`
(
    `articleID` INTEGER NOT NULL,
    `tagID` INTEGER NOT NULL,
    PRIMARY KEY (`articleID`,`tagID`),
    INDEX `article_tag_fi_180726` (`tagID`),
    CONSTRAINT `article_tag_fk_5868da`
        FOREIGN KEY (`articleID`)
        REFERENCES `article` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `article_tag_fk_180726`
        FOREIGN KEY (`tagID`)
        REFERENCES `tag` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- category
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `category`;

CREATE TABLE `category`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(60) NOT NULL,
    `root` VARCHAR(128) NOT NULL,
    `colour` VARCHAR(10) DEFAULT 'blue',
    `slug` VARCHAR(255),
    PRIMARY KEY (`id`),
    UNIQUE INDEX `category_slug` (`slug`(255))
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- view
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `view`;

CREATE TABLE `view`
(
    `article_id` INTEGER NOT NULL,
    `ip_address` VARCHAR(20) NOT NULL,
    `time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`article_id`),
    INDEX `i_referenced_vote_fk_f8adeb_1` (`ip_address`),
    CONSTRAINT `view_fk_3610e9`
        FOREIGN KEY (`article_id`)
        REFERENCES `article` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- vote
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `vote`;

CREATE TABLE `vote`
(
    `articleID` INTEGER NOT NULL,
    `ip` VARCHAR(20) NOT NULL,
    `vote` INTEGER NOT NULL,
    PRIMARY KEY (`articleID`,`ip`),
    INDEX `vote_fi_f8adeb` (`ip`),
    CONSTRAINT `vote_fk_5868da`
        FOREIGN KEY (`articleID`)
        REFERENCES `article` (`id`),
    CONSTRAINT `vote_fk_f8adeb`
        FOREIGN KEY (`ip`)
        REFERENCES `view` (`ip_address`)
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
