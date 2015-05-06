- install basic ubuntu server
	ssh server
	LAMP server

- sudo apt-get remove samba 
- sudo apt-get autoremove
 
- set static address 
	edit /etc/network/interfaces

	example:

		auto eth0
		iface eth0 inet static
		address 192.168.0.240
		netmask 255.255.255.0
		gateway 192.168.0.1


- set DNS servers

	edit /etc/resolvconf/resolv.conf.d/head

	example:

		domain dummy.net
		nameserver 192.168.0.201
		nameserver 192.168.0.202

- enable connection to mysql from all hosts

	edit /etc/mysql/my.conf, comment the line "bind-address            = 127.0.0.1"
		service mysql restart

- install freeradius

		sudo apt-get install freeradius freeradius-mysql 

- install PHP helper

		sudo apt-get install libphp-phpmailer 

- configure freeradius

		 mysql -uroot -p
 			CREATE DATABASE radius;
		 	exit
 	
		 mysql -u root -p radius < /etc/freeradius/sql/mysql/schema.sql
		 mysql -u root -p radius < /etc/freeradius/sql/mysql/nas.sql

		 mysql -uroot -p
 			GRANT ALL ON radius.* TO radius@localhost IDENTIFIED BY "radiuspassword";
		 	GRANT ALL ON radius.* to radius@'192.168.0.%' IDENTIFIED BY "radiuspassword";
		 	USE radius
		 	INSERT INTO  nas VALUES (NULL ,  '192.168.0.250',  'radiusClient',  'other', NULL ,  'radiusSecret', NULL , NULL ,  'RADIUS Client');
		 	INSERT INTO  nas VALUES (NULL ,  '127.0.0.1',  'voucherBox',  'other', NULL ,  'radiusSecret', NULL , NULL ,  'RADIUS Client');
		    FLUSH PRIVILEGES;
		    exit

	uncomment $INCLUDE sql.conf in /etc/freeradius/radiusd.conf
  
	edit /etc/freeradius/sql.conf, set password = "radiuspassword", uncomment "readclients = yes"
 
		mv clients.conf clients.conf.ori
		touch clients.conf
 
		nano /etc/freeradius/sites-available/wifiVouchers (copy file content)
		cd /etc/freeradius/sites-enabled
		ln -s ../sites-available/wifiVouchers
		service freeradius restart

- configure apache

		copy voucherBox.conf on /etc/apache2/sites-available
		cd /etc/apache2/sites-enabled
		rm 000-
		ln -s ../sites-available/voucherBox.conf
		sudo service apache2 restart

	copy www on /var/www

- populate voucher table

	CLI:

		__createVouchers.php -n 10 -v 1
		__createVouchers.php -n 10 -v 7
		__createVouchers.php -n 10 -v 31

	GUI:
		 (TODO)

- test vouchers

	get vouchers from DB:

		__getVoucher.php -v 1 -u test1@dummy.net -> <test1voucher>
		__getVoucher.php -v 7 -u test7@dummy.net -> <test7voucher>
		__getVoucher.php -v 31 -u test31@dummy.net -> <test31voucher>

	check vouchers:
	
		radtest test1voucher dummypass localhost 1812 FradiusSecret
			check that Session-Timeout = 86400

		radtest test7voucher dummypass localhost 1812 radiusSecret
			check that Session-Timeout = 604800

		radtest test31voucher dummypass localhost 1812 radiusSecret
			check that Session-Timeout = 2674800


