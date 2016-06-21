DROP DATABASE IF EXISTS gladkovdb;
CREATE DATABASE gladkovdb;
USE gladkovdb;
CREATE TABLE countries(
				id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
				slug VARCHAR(20) NOT NULL UNIQUE,
				name VARCHAR(40) NOT NULL UNIQUE
				);
CREATE TABLE users(
				id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
				slug VARCHAR(20) NOT NULL UNIQUE,
				name VARCHAR(80) NULL,
				mail VARCHAR(40) NOT NULL UNIQUE,
				alt_mail VARCHAR(40) NULL,
				gender BOOLEAN NOT NULL DEFAULT false,
				mobile VARCHAR(30) NOT NULL,
				tel VARCHAR(30) NULL,
				fax VARCHAR(30) NULL,
				zip VARCHAR(20),
				street VARCHAR(100),
				city VARCHAR(80),
				country INT NULL,
				job_title VARCHAR(80) NULL,
				FOREIGN KEY(country) REFERENCES countries(id)
				);
CREATE TABLE salers(
				id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
				slug VARCHAR(20) NOT NULL UNIQUE,
				title VARCHAR(80) NOT NULL,
				logo VARCHAR(40) NULL UNIQUE,
				respons_person INT NOT NULL,
				FOREIGN KEY(respons_person) REFERENCES users(id)
				);
CREATE TABLE subjects(
				id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
				slug VARCHAR(20) NOT NULL UNIQUE,
				name VARCHAR(40) NOT NULL UNIQUE
				);
CREATE TABLE permitions(
				id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
				slug VARCHAR(20) NOT NULL UNIQUE,
				code TINYINT(1) NOT NULL DEFAULT 4,
				user_id INT NOT NULL,
				subject_id INT NOT NULL,
				FOREIGN KEY(user_id) REFERENCES users(id),
				FOREIGN KEY(subject_id) REFERENCES subjects(id)
				);
INSERT INTO countries(id,slug,name)
	VALUES
		(1,'d_country_0001','Espana'),
		(2,'d_country_0002','Russia'),
		(3,'d_country_0003','USA')
		;
INSERT INTO users(id,slug,name,mail,mobile,zip,street,city,country)
	VALUES
		(1,'d_user_0000','John Smith','john@smith.loc','+8 888 888 88 88','400088','Happy st.','NY',2),
		(2,'d_user_0001','Вася Пупкин','v@pk.loc','+7-34-988-888-88-00','400089','переулок Богатый дом 888 корпус 7-А кватрира 8','Длиннющееназваниекакаготонеизвестногогорода',1),
		(3,'d_user_0002',NULL,'anonim@user.loc',NULL,NULL,NULL,NULL,NULL),
		(4,'d_user_0004','guest','',NULL,NULL,NULL,NULL,NULL)
		;
INSERT INTO salers(id,slug,title,logo,respons_person)
	VALUES
		(1,'d_saler_01','International Textile Inc.','logo_filename.png',1),
		(2,'d_saler_02','ООО Классные ткани лимитеддд',NULL,2),
		(3,'d_saler_03','',NULL,3)
		;
INSERT INTO subjects(id,slug,name)
	VALUES
		(1,'d_subj_1','AdminController'),
		(2,'d_subj_2','DefaultController')
		;
INSERT INTO permitions(id,slug,user_id,subject_id,code)
	VALUES
		(1,'d_permit_1',1,1,6),
		(2,'d_permit_2',1,2,3),
		(3,'d_permit_3',2,1,5),
		(4,'d_permit_4',4,2,4)
		;