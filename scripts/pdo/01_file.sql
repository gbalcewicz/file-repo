DROP TABLE IF EXISTS gb_file;
CREATE TABLE gb_file (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    file_id VARCHAR(36) NOT NULL,
    location VARCHAR(255),
    mime_type VARCHAR(255),
    base_name VARCHAR(80) NOT NULL,
    extension VARCHAR(10) NOT NULL,
    path VARCHAR(255) NOT NULL,
    created_at DATETIME(6) NOT NULL,
    checksum VARCHAR(40),
    size BIGINT UNSIGNED NOT NULL,
    original_name VARCHAR(255),
    storage_id VARCHAR (255),
    PRIMARY KEY (id)
);
CREATE UNIQUE INDEX udx_file_id ON gb_file(file_id);