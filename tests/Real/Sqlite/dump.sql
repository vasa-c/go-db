CREATE TABLE `godbtest` (
    `id` INTEGER PRIMARY KEY,
    `num` INTEGER NOT NULL,
    `desc` INTEGER NOT NULL,
    `val` VARCHAR(10) NULL DEFAULT NULL
);

INSERT INTO `godbtest` VALUES (1, 1, 10, "one");
INSERT INTO `godbtest` VALUES (2, 3, 6, "two");
INSERT INTO `godbtest` VALUES (3, 3, 6, "three");
INSERT INTO `godbtest` VALUES (4, 7, 3, NULL);
INSERT INTO `godbtest` VALUES (5, 7, 2, "five");
