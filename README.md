#nim
##Laravel Network Interfaces Manager Application for Linux

The idea of this application is creating a web based application to read and write the network interfaces in Linux systems.

+ Linux
+ Laravel
+ VueJS
+ SASS

Execute the following command in order to write the interfaces.d folder.
```
sudo chown www-data:www-data /etc/network/interfaces.d
```

Give permission to the web app to reboot the system
```
www-data ALL=(root) NOPASSWD: /sbin/reboot
```