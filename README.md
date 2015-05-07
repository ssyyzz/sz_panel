Для начала потребуется установленный CentOS 6 (разработка проверялась на версии 6.5)

Устанавливаем и запускаем сервер MySQL

```
yum install mysql mysql-server
chkconfig --levels 235 mysqld on
service mysqld start
```

Настраиваем сервер MySQL, задаем root пароль

```
mysql_secure_installation
```

Устанавливаем и запускаем Apache

```
yum install httpd
chkconfig --levels 235 httpd on
service httpd start
```

Настраиваем фаервол (открываем порты для веба и фтп)

```
nano /etc/sysconfig/iptables
```

```
*filter
:INPUT ACCEPT [0:0]
:FORWARD ACCEPT [0:0]
:OUTPUT ACCEPT [0:0]
-A INPUT -m state --state ESTABLISHED,RELATED -j ACCEPT
-A INPUT -p icmp -j ACCEPT
-A INPUT -i lo -j ACCEPT
-A INPUT -m state --state NEW -m tcp -p tcp --dport 22 -j ACCEPT
-A INPUT -m state --state NEW -m tcp -p tcp --dport 80 -j ACCEPT
-A INPUT -m state --state NEW -m tcp -p tcp --dport 21 -j ACCEPT
-A INPUT -m state --state NEW -m tcp -p tcp --dport 20 -j ACCEPT
-A INPUT -m state --state NEW -m tcp -p tcp --dport 443 -j ACCEPT
-A INPUT -m state --state NEW -m tcp -p tcp --dport 44000:44100 -j ACCEPT
-A INPUT -j REJECT --reject-with icmp-host-prohibited
-A FORWARD -j REJECT --reject-with icmp-host-prohibited
COMMIT
```

Перезапускаем фаервол

```
service iptables restart
```

Устанавливаем PHP

```
yum install php
yum install php-mysql php-gd php-imap php-ldap php-mbstring php-odbc php-pear php-xml php-xmlrpc
service httpd restart
```

Комментируем все строки в файле конфигурации welcome.conf

```
nano /etc/httpd/conf.d/welcome.conf
```

Создаем свой файл для виртуальных хостов Apache (не забудьте, что нужно указать свое доменное имя и IP-адрес)

```
nano /etc/httpd/conf.d/vhosts.conf
```

```
<VirtualHost 185.22.172.118:80>
        ServerAdmin admin@primez.tk
        ServerName primez.tk
        ServerAlias *.primez.tk

        <Directory />
                Options FollowSymLinks
                AllowOverride None
        </Directory>
        <Directory /var/www/html/vhosts/*>
                Options Indexes FollowSymLinks MultiViews
                AllowOverride All
                Order allow,deny
                allow from all
        </Directory>

        ErrorLog /var/www/log/dev-error.log

        LogLevel warn
        CustomLog /var/www/log/dev-access.log combined

        UseCanonicalName Off
        VirtualDocumentRoot /var/www/html/vhosts/%-3/
</VirtualHost>
```

Создаем папки для виртуальных хостов

```
mkdir /var/www/log
mkdir /var/www/html/vhosts
mkdir /var/www/html/vhosts/_
service httpd restart
```

Копируем репозиторий в папку /var/www/html/vhosts/_
Для удобства настройки можно использовать mysql-adminer доступные по адресу <a href="http://primez.tk/myadmin/">http://primez.tk/myadmin/</a>

Устанавливаем FTP-сервер с поддержкой MySQL

```
yum install epel-release
yum install -y pam_mysql vsftpd
```

Для FTP-сервера потребуется создать базу и пользователя. В базе должна быть следующая таблица:

```
CREATE TABLE `accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(30) NOT NULL,
  `pass` char(40) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user` (`user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
```

Настраиваем FTP-сервер (не забудьте поменять настройки доступа к базе в конфигурационном файле на свои)

```
mv /etc/pam.d/vsftpd /etc/pam.d/vsftpd.original
nano /etc/pam.d/vsftpd
```

```
#%PAM-1.0
session     optional     pam_keyinit.so     force revoke
auth required pam_mysql.so user=vsftp passwd=mypassword host=127.0.0.1 db=vsftp table=accounts usercolumn=user passwdcolumn=pass crypt=3
account required pam_mysql.so user=vsftp passwd=mypassword host=127.0.0.1 db=vsftp table=accounts usercolumn=user passwdcolumn=pass crypt=3
```

```
mv /etc/vsftpd/vsftpd.conf /etc/vsftpd/vsftpd.conf.original
nano /etc/vsftpd/vsftpd.conf
```

```
listen=YES
connect_from_port_20=YES
pam_service_name=vsftpd
tcp_wrappers=YES

pasv_enable=YES
pasv_min_port=44000
pasv_max_port=44100

hide_ids=YES

anonymous_enable=NO
local_enable=YES
write_enable=YES
local_umask=022

chroot_local_user=YES
chroot_list_enable=YES

userlist_enable=YES
user_config_dir=/etc/vsftpd/vsftpd_user_conf

guest_enable=YES
guest_username=root
local_root=/var/ftp/$USER
user_sub_token=$USER
virtual_use_local_privs=YES
```

```
mkdir /etc/vsftpd/vsftpd_user_conf
echo '# Contains a list of users with root access' > /etc/vsftpd/chroot_list
```

Запускаем FTP-сервер

```
chkconfig vsftpd on
service vsftpd start
```

Добавим конфигурационный файл для FTP-аккаунта администратора

```
nano /etc/vsftpd/vsftpd_user_conf/ftpadmin
```

```
local_root=/var/www
```

Добавим пользователя в базу vsftp в таблицу accounts

```
INSERT INTO `vsftp`.`accounts` (`user`,`pass`) VALUES ('ftpadmin', MD5('adminpassword'));
```

Отключим защиту SELinux

```
setenforce 0
service vsftpd restart
```

В конфигурационном файле PHP нужно установить часовой пояс

```
nano /etc/php.ini
```

```
date.timezone = Europe/Moscow
```

```
service httpd restart
```

Для самой панели нужно будет создать пользователя в MySQL и базу данных со следующей структурой:

```
SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `domains`;
CREATE TABLE `domains` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `user` int(11) NOT NULL,
  `ftp` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `domains` (`id`, `name`, `user`, `ftp`) VALUES
(1,	'ftpadmin',	0,	1);

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(100) NOT NULL,
  `password` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
```

Последний шаг - настройка head_domain в /var/www/html/vhosts/_/application/config/config.php и параметров БД в database.php