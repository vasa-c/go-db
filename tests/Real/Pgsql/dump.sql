CREATE TEMP TABLE "godbtest" (
    "id" SERIAL PRIMARY KEY,
    "num" integer NOT NULL,
    "desc" integer NOT NULL,
    "val" varchar NULL DEFAULT NULL
);

INSERT INTO "godbtest" ("num", "desc", "val") VALUES
    (1, 10, 'one'),
    (3, 6, 'two'),
    (3, 6, 'three'),
    (7, 3, NULL),
    (7, 2, 'five');

