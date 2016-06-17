DROP DATABASE IF EXISTS gladkovdb;
CREATE DATABASE gladkovdb;
USE gladkovdb;
CREATE TABLE t (name VARCHAR(40));
INSERT INTO t(name) VALUES('John Smith'),('Вася Пупкин');
CREATE TABLE d (name VARCHAR(40));