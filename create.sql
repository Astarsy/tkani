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
				zip VARCHAR(20) NULL,
				street VARCHAR(100) NULL,
				city VARCHAR(80) NULL,
				country INT NULL,
				job_title VARCHAR(80) NULL,
				active BOOLEAN NOT NULL DEFAULT false,
				FOREIGN KEY(country) REFERENCES countries(id)
				);
CREATE TABLE payments(
				id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
				name VARCHAR(80) NOT NULL UNIQUE,
				descr VARCHAR(400) NULL
				);
CREATE TABLE shipings(
				id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
				name VARCHAR(80) NOT NULL UNIQUE,
				descr VARCHAR(400) NULL,
				price INT NULL
				);
CREATE TABLE payments_of_shops(
				id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
				shop_id INT NOT NULL,
				payment_id INT NOT NULL
				);
CREATE TABLE shipings_of_shops(
				id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
				shop_id INT NOT NULL,
				shiping_id INT NOT NULL
				);
CREATE TABLE shops(
				id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
				slug VARCHAR(20) NOT NULL UNIQUE,
				open_time INT NULL,
				respons_person INT NOT NULL,
				title VARCHAR(80) NOT NULL UNIQUE,
				logo VARCHAR(40) NULL UNIQUE,
				owner_form VARCHAR(20) NOT NULL,
				descr VARCHAR(800) NULL,
				pub_phone VARCHAR(30) NOT NULL,
				pub_address VARCHAR(400) NOT NULL,
				payments INT NOT NULL,				
				shipings INT NOT NULL,
				addition_info VARCHAR(800) NULL,
				FOREIGN KEY(respons_person) REFERENCES users(id),
				FOREIGN KEY(payments) REFERENCES payments_of_shops(shop_id),
				FOREIGN KEY(shipings) REFERENCES shipings_of_shops(shop_id)
				);
CREATE TABLE admins(
				id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
				user_id INT NOT NULL UNIQUE,
				FOREIGN KEY(user_id) REFERENCES users(id)
				);
CREATE TABLE subjects(
				id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
				slug VARCHAR(20) NOT NULL UNIQUE,
				name VARCHAR(40) NOT NULL UNIQUE,
				code TINYINT(1) NOT NULL
				);
CREATE TABLE reg_heshes(
				id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
				hesh CHAR(40) NOT NULL UNIQUE,
				user_slug VARCHAR(20) NOT NULL UNIQUE,
				reg_time INT NOT NULL UNIQUE,
				FOREIGN KEY(user_slug) REFERENCES users(slug)
				);
CREATE TABLE saler_requests(
				id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
				user_id INT NOT NULL,
				reg_time INT NOT NULL,
				FOREIGN KEY(user_id) REFERENCES users(id)
				);

INSERT INTO payments(id,name,descr)
	VALUES
		(1,'Наличными.','В магазине.'),
		(2,'Наличными.','Курьеру при получении.'),
		(3,'Перевод на карту.','Перевод любым способом на корту Сбербанка.')
		;
INSERT INTO shipings(id,name,descr,price)
	VALUES
		(1,'Самовывоз из магазина.','В магазине.'),
		(2,'Почта России.','Бандероль, обычное отправление.',200),
		(3,'ТК Деловые Линии.','Обычное отправление',400)
		;
INSERT INTO countries(id,slug,name)
	VALUES
		(1,'d_country_0001','Espana'),
		(2,'d_country_0002','Russia'),
		(3,'d_country_0003','USA')
		;
INSERT INTO users(id,slug,name,mail,mobile,zip,street,city,country,active)
	VALUES
		(1,'d_user_0000','John Smith','john@smith.loc','+8 888 888 88 88','400088','Happy st.','NY',3,true),
		(2,'d_user_0001','Вася Пупкин','v@pk.loc','+7-34-988-888-88-00','400089','переулок Богатый дом 888 корпус 7-А кватрира 8','Длиннющееназваниекакаготонеизвестногогорода',2,true),
		(3,'d_user_0002',NULL,'anonim@user.loc',NULL,NULL,NULL,NULL,NULL,false),
		(4,'d_user_0004','guest','',NULL,NULL,NULL,NULL,2,true)
		;
INSERT INTO shops(id,slug,title,logo,respons_person,reg_time)
	VALUES
		(1,'d_shop_01','International Textile Inc.','logo_filename.png',2,1467396208),
		(2,'d_shop_02','ООО Классные ткани лимитеддд',NULL,2,1467396308),
		(3,'d_shop_03','',NULL,3,1467376208)
		;
INSERT INTO admins(user_id)
	VALUES(1)
		;
INSERT INTO subjects(id,slug,name,code)
	VALUES
		(1,'d_subj_1','AdminController',4),
		(2,'d_subj_2','CabinetController',0),
		(3,'d_subj_3','SalerController',2),
		(4,'d_subj_4','DefaultController',0)
		;