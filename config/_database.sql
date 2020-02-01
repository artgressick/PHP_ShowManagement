CREATE TABLE audit (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	reverted TINYINT(1) NOT NULL DEFAULT 0,
	audittype_id TINYINT(2) UNSIGNED NOT NULL,
	record_id INT UNSIGNED NOT NULL,
	user_id INT UNSIGNED NOT NULL,
	created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	table_name VARCHAR(100),
	column_name VARCHAR(100),
	old_value TEXT,
	new_value TEXT,
	exec_query TEXT,
	revert_query TEXT,
	index(audittype_id),
	index(record_id),
	index(user_id)
) ENGINE=MyISAM, CHARACTER SET utf8;

CREATE TABLE audittypes (
	id TINYINT(2) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	audittype_name VARCHAR(40) NOT NULL
) ENGINE=MyISAM, CHARACTER SET utf8;
INSERT INTO audittypes (audittype_name) VALUES ('Add Record'),('Edit Record'),('Delete Record'),('Permanent Delete Record');

CREATE TABLE user_groups (
	id SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	rkey VARCHAR(40) NOT NULL,
	deleted TINYINT(1) NOT NULL DEFAULT 0,
	group_name VARCHAR(150) NOT NULL
) ENGINE=MyISAM, CHARACTER SET utf8;