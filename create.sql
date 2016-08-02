DROP DATABASE IF EXISTS gladkovdb;
CREATE DATABASE gladkovdb;
USE gladkovdb;
CREATE TABLE forms(
				id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
				name VARCHAR(40) NOT NULL UNIQUE,
				title VARCHAR(80) NULL
				);
CREATE TABLE fields(
				id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
				form INT NOT NULL,
				type VARCHAR(20) NOT NULL,
				name VARCHAR(40) NOT NULL,
				title VARCHAR(80) NULL,
				required BOOLEAN NOT NULL DEFAULT true,
				FOREIGN KEY(form) REFERENCES forms(id)
				);
CREATE TABLE options(
				id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
				name VARCHAR(40) NOT NULL,
				field INT NOT NULL,
				FOREIGN KEY(field) REFERENCES fields(id)
				);
INSERT INTO forms(id,name,title)
	VALUES
		(1,'AddGoodForm','Форма добавления товара')
		;
INSERT INTO fields(id,form,type,name,title,required)
	VALUES
		(1,1,'text','name','Название',true),
		(2,1,'text','price','Цена',true),
		(3,1,'select','manuf','Производитель',true),
		(4,1,'text','consist','Состав',true),
		(5,1,'text','width','Ширина',true),
		(6,1,'text','descr','Описание',false),
		(7,1,'img','foto','Изображение',true)
		;
INSERT INTO options(id,field,name)
	VALUES
		(1,3,'Италия'),
		(2,3,'Китай'),
		(3,3,'Россия')
		;


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
CREATE TABLE owner_forms(
				id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
				name VARCHAR(80) NOT NULL,
				descr VARCHAR(200) NULL
				);
CREATE TABLE payments(
				id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
				name VARCHAR(80) NOT NULL,
				descr VARCHAR(400) NULL
				);
CREATE TABLE shipings(
				id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
				name VARCHAR(80) NOT NULL,
				descr VARCHAR(400) NULL
				);
CREATE TABLE shops(
				id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
				slug VARCHAR(20) NOT NULL UNIQUE,
				create_time INT NOT NULL,
				open_time INT NULL,
				respons_person INT NOT NULL UNIQUE,
				title VARCHAR(80) NOT NULL UNIQUE,
				logo VARCHAR(40) NULL UNIQUE,
				owner_form INT NOT NULL,
				descr VARCHAR(800) NULL,
				pub_phone VARCHAR(30) NOT NULL,
				pub_address VARCHAR(400) NOT NULL,
				addition_info VARCHAR(800) NULL,
				FOREIGN KEY(respons_person) REFERENCES users(id),
				FOREIGN KEY(owner_form) REFERENCES owner_forms(id)
				);
CREATE TABLE payments_of_shops(
				id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
				shop_id INT NOT NULL,
				payment_id INT NOT NULL,
				FOREIGN KEY(shop_id) REFERENCES shops(id) ON DELETE CASCADE,
				FOREIGN KEY(payment_id) REFERENCES payments(id)
				);
CREATE TABLE shipings_of_shops(
				shop_id INT NOT NULL,
				shiping_id INT NOT NULL,
				price INT NULL,
				PRIMARY KEY (shop_id,shiping_id),
				FOREIGN KEY(shop_id) REFERENCES shops(id) ON DELETE CASCADE,
				FOREIGN KEY(shiping_id) REFERENCES shipings(id)
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
				user_id INT NOT NULL UNIQUE,
				reg_time INT NOT NULL,
				add_payment VARCHAR(400) NULL,
				add_shiping VARCHAR(400) NULL,
				reject_reason VARCHAR(400) NULL,
				FOREIGN KEY(user_id) REFERENCES users(id)
				);

INSERT INTO owner_forms(id,name,descr)
	VALUES
		(1,'Физ. лицо','не является предпринимателем, продажи осуществляются в частном порядке'),
		(2,'ПБЮЛ','предприниматель без образования юридического лица'),
		(3,'ИП','юридическое лицо с единственным учредителем'),
		(4,'ООО','общество с ограниченной ответсвенностью, юридическое лицо'),
		(5,'ЗАО','закрытое акционерное общество, юридическое лицо'),
		(6,'Иностранная фирма','продажи осуществяет зарубежная фирма или предприниматель')
		;
INSERT INTO payments(id,name,descr)
	VALUES
		(1,'В магазине наличными','при покупке'),
		(2,'Курьеру наличными','при доставке'),
		(3,'Перевод на карту Сбербанка','через банкомат, интернет банкинг или другим способом')
		;
INSERT INTO shipings(id,name,descr)
	VALUES
		(1,'Самовывоз из магазина',''),
		(2,'Почта России','бандероль, обычное отправление'),
		(3,'ТК Деловые Линии.','доставка до терминала компании в Вашем городе')
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
INSERT INTO shops(id,slug,create_time,open_time,respons_person,title,logo,owner_form,descr,pub_phone,pub_address,addition_info)
	VALUES
		(1,'d_shop_01',1468492577,1468493577,1,'International Textile Inc.',NULL,3,'Супер классный магазин.','+7 987 654 32 10','г.Мосвка проспект Ленина 1','У нас всё хорошее.')
		;
INSERT INTO admins(user_id)
	VALUES(1)
		;
INSERT INTO subjects(id,slug,name,code)
	VALUES
		(1,'d_subj_1','AdminController',4),
		(2,'d_subj_2','CabinetController',0),
		(3,'d_subj_3','SalerController',2),
		(4,'d_subj_4','DefaultController',0),		
		(5,'d_subj_5','CabinetController/reg_shopMethod',1),		
		(6,'d_subj_6','CabinetController/shopMethod',1),		
		(7,'d_subj_7','CabinetController/shopsMethod',1),
		(8,'d_subj_8','GoodsController',2)
		;


CREATE TABLE fotos(
			id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
			file VARCHAR(80) NOT NULL UNIQUE
			);
CREATE TABLE groups(
			id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
			name VARCHAR(80) NOT NULL UNIQUE,
			foto_id INT NULL,
			FOREIGN KEY(foto_id) REFERENCES fotos(id)
			);
CREATE TABLE caths(
			id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
			group_id INT NULL,
			name VARCHAR(80) NOT NULL UNIQUE,
			foto_id INT NULL,
			FOREIGN KEY(foto_id) REFERENCES fotos(id),
			FOREIGN KEY(group_id) REFERENCES groups(id)
			);
CREATE TABLE goods(
			id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
			slug VARCHAR(20) NOT NULL UNIQUE,
			shop_id INT NOT NULL,
			d_date INT NULL,
			name VARCHAR(80) NOT NULL,
			price INT NOT NULL,
			descr VARCHAR(400) NULL,
			manuf INT NOT NULL,
			consist VARCHAR(80) NOT NULL,
			width INT NOT NULL,
			main_foto_id INT NOT NULL,
			FOREIGN KEY(shop_id) REFERENCES shops(id),			
			FOREIGN KEY(manuf) REFERENCES options(id),
			FOREIGN KEY(main_foto_id) REFERENCES fotos(id)
			);

INSERT INTO fotos(id,file)
	VALUES	(1,'IMG_2406.JPG');
INSERT INTO groups(id,name,foto_id)
	VALUES	(1,'jins',1);	
INSERT INTO caths(id,name,foto_id)
	VALUES	(1,'jins printed',1),
			(2,'jins colored',1);
INSERT INTO goods(id,slug,shop_id,d_date,name,price,descr,manuf,consist,width,main_foto_id)
	VALUES	(1,'g_001',1,1468496877,'jins #1',888,'Описание джинса номер один.',2,'хлопок 100%',140,1);