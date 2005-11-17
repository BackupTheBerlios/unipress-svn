# Version 0.1
# 2005/11/07 3-11pm

CREATE TABLE entry (
  id INT NOT NULL AUTO_INCREMENT,
  sources_id_fk INTEGER UNSIGNED NOT NULL,
  title VARCHAR(100) NULL,
  filename VARCHAR(200) NULL,
  link VARCHAR(100) NULL,
  date DATE NULL,
  PRIMARY KEY(id, sources_id_fk),
  FULLTEXT INDEX entries_title(title),
  INDEX entries_FKIndex1(sources_id_fk)
);

CREATE TABLE entry_has_keywords (
  entry_id_fk INT NOT NULL,
  keywords_id_fk INT NOT NULL,
  PRIMARY KEY(entry_id_fk, keywords_id_fk),
  INDEX entries_has_keywords_FKIndex1(entry_id_fk),
  INDEX entries_has_keywords_FKIndex2(keywords_id_fk)
);

CREATE TABLE entry_has_sites (
  entry_id_fk INT NOT NULL,
  sites_id_fk INT NOT NULL,
  PRIMARY KEY(entry_id_fk, sites_id_fk),
  INDEX entries_has_sites_FKIndex1(entry_id_fk),
  INDEX entries_has_sites_FKIndex2(sites_id_fk)
);

CREATE TABLE keywords (
  id INT NOT NULL AUTO_INCREMENT,
  keyword VARCHAR(30) NULL,
  PRIMARY KEY(id),
  INDEX keywords_fulltext(keyword),
  UNIQUE INDEX keywords_uniq(keyword)
);

CREATE TABLE sites (
  id INT NOT NULL AUTO_INCREMENT,
  name VARCHAR(100) NULL,
  kuerzel VARCHAR(5) NULL,
  head VARCHAR(250) NULL,
  foot VARCHAR(250) NULL,
  PRIMARY KEY(id),
  INDEX sites_nunique(name),
  INDEX sites_kunique(kuerzel)
);

CREATE TABLE sources (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  PRIMARY KEY(id),
  INDEX sources_nunique(name)
);
