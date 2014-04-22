CREATE TEMPORARY TABLE `godbtest` (
    `id` INT UNSIGNED AUTO_INCREMENT,
    `num` INT UNSIGNED NOT NULL,
    `desc` INT UNSIGNED NOT NULL,
    `val` VARCHAR(10) NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
);

INSERT INTO `godbtest` VALUES
    (1, 1, 10, "one"),
    (2, 3, 6, "two"),
    (3, 3, 6, "three"),
    (4, 7, 3, NULL),
    (5, 7, 2, "five");

