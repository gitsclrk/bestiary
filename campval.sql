BEGIN TRANSACTION;
CREATE TABLE IF NOT EXISTS "creatures" (
	"crtr_id"	TEXT,
	"crtr_name"	TEXT NOT NULL,
	"crtr_size"	TEXT NOT NULL,
	"crtr_progeny"	TEXT NOT NULL,
	"crtr_type"	TEXT NOT NULL,
	"crtr_environment"	TEXT NOT NULL,
	"crtr_description"	TEXT,
	"crtr_image"	TEXT NOT NULL,
	"created_at"	TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	"user_id"	INTEGER,
	PRIMARY KEY("crtr_id")
);
CREATE TABLE IF NOT EXISTS "users" (
	"user_id"	INTEGER,
	"user_fName"	TEXT NOT NULL,
	"user_lName"	TEXT NOT NULL,
	"user_username"	TEXT NOT NULL UNIQUE,
	"user_password"	TEXT NOT NULL,
	"user_desig"	TEXT NOT NULL,
	"user_last_login"	TIMESTAMP,
	"created_at"	TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY("user_id" AUTOINCREMENT)
);
CREATE INDEX "id_crtr_name" ON "creatures" (
	"crtr_name"
);
CREATE INDEX "idx_crtr_environment" ON "creatures" (
	"crtr_environment"
);
CREATE INDEX "idx_crtr_type" ON "creatures" (
	"crtr_type"
);
CREATE INDEX "idx_user_desig" ON "users" (
	"user_desig"
);
CREATE INDEX "idx_user_last_login" ON "users" (
	"user_last_login"
);
CREATE INDEX "idx_user_username" ON "users" (
	"user_username"
);
CREATE TRIGGER set_creatures_updated_at
AFTER UPDATE ON creatures
FOR EACH ROW
BEGIN
  UPDATE creatures
  SET updated_at = CURRENT_TIMESTAMP
  WHERE crtr_id = OLD.crtr_id;
END;
COMMIT;
