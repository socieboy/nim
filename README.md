#nim
##Laravel Network Interfaces Manager Application for Linux

The idea of this application is creating a web based application to read and write the network interfaces in Linux systems.

+ Linux
+ Laravel
+ VueJS
+ SASS

Execute the following command to give permissions to www-data user to execute the commands
```
sudo chown www-data:www-data /etc/network/interfaces.d
sudo chown www-data:www-data /sbin/ifconfig
```

Add the following lines to the /etc/sudoers file. (use visudo)
```
www-data ALL=(root) NOPASSWD: /sbin/ifconfig
www-data ALL=(root) NOPASSWD: /usr/sbin/service networking restart
```