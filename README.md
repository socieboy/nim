#nim
##Laravel Network Interfaces Manager Application for Linux

The idea of this application is creating a web based application to read and write the network interfaces in Linux systems.

+ Linux
+ Laravel
+ VueJS
+ SASS

Execute the following command to give permissions to www-data user.
```
sudo chown www-data:www-data /etc/network/interfaces.d
www-data ALL=(root) NOPASSWD: service/networking/restart
sudo chown www-data:www-data /sbin/ifconfig
```