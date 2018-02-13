# Network Interfaces Manager
### Laravel Network Interfaces Manager Application for Ubuntu Linux

#### Description
The idea of this application is creating a web based application to read and write the network interfaces in Linux systems.

#### Installation
Copy the .env.example to .env and set the interface pattern value of your interface name device.

Example:
```
Interface name: enp1s0
Regular Expresion: enp[0-9]s[0-9]
INTERFACES_PATTERN=enp[0-9]s[0-9]
```

Install Network Manager for ubuntu
```
sudo apt-get install network-manager
```

Execute the following commands to give permissions to www-data user.
```
sudo chown www-data:www-data /etc/network/interfaces.d
sudo chown www-data:www-data /sbin/ifconfig
sudo chown www-data:www-data /etc/resolv.conf
```

Add the following lines to the /etc/sudoers file. (use visudo)
```
www-data ALL=(root) NOPASSWD: /sbin/ifconfig
www-data ALL=(root) NOPASSWD: /usr/sbin/service networking restart
```

Screenshot:
![alt text](https://raw.githubusercontent.com/socieboy/nim/master/public/images/screenshot.png)
