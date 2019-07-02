# Tor Access

To access remote servers, when all other connections methods have failed,
we have a backup method of a Tor Hidden Service, which is setup by our Ansible
scripts.

To be able to access the Tor hidden services you have to do some config first.

To your `.ssh/config` file, add the following lines
```
Host *.onion
    ProxyCommand /usr/bin/nc -xlocalhost:9150 -X5 %h %p
```

Now download the Tor Browser, start it up, let it connect to Tor. Once it's
connected, you can run the Tor SSH access command for any given server, or
access it's HTTP(S) services view the http service. To find these commands,
refer to a specific client's reference document.