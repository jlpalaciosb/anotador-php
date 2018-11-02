-- PostgreSQL 10.5

-- DROP DATABASE diary;

CREATE DATABASE diary;

\c diary;

CREATE TABLE users (
	name VARCHAR(10),
	password CHARACTER(32) NOT NULL,
	CONSTRAINT pk_users PRIMARY KEY(name)
);

CREATE TABLE entries (
	owner VARCHAR(10),
	date CHARACTER(10),
	content TEXT,
	CONSTRAINT pk_entries PRIMARY KEY(owner, date),
	CONSTRAINT fk_entry_user FOREIGN KEY(owner) REFERENCES users(name)
);
