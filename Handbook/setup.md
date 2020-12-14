# Setting up a new Grownetics Installation

## Create a Server

### Requirements
* Ubuntu 20.04
* 8GB+ RAM

## DNS

Set up a wildcard DNS A record pointing `*.YOURDOMAIN.com` to the IP of your new server.

## Install HomelabOS

SSH into your new server and run `bash <(curl -s https://gitlab.com/NickBusey/HomelabOS/-/raw/master/install_homelabos.sh)`

## Enable HomelabOS + TICK

After the initial HomelabOS setup has completed, run the following

`cd /var/homelabos/install`

Enable Grownetics

`make set grownetics.enable true`

Enable TICK

`make set tick.enable true`

Secure the TICK service (this isn't needed for the Grownetics service as it has it's own authentication layer).

`make set tick.auth true`

## Deploy

Now deploy all the services with

`make`

You can monitor the deployment progress with `systemctl status grownetics`.

Once everything is running it should look like this:

```
root@demo-2020-12-14:/var/homelabos/install# systemctl status grownetics
● grownetics.service - HomelabOS grownetics Service
     Loaded: loaded (/etc/systemd/system/grownetics.service; enabled; vendor preset: enabled)
     Active: active (running) since Mon 2020-12-14 19:08:50 UTC; 3min 20s ago
```

## Access

Once the services are all up and running, and your DNS settings above are correct, you can access Grownetics at http(s)://grownetics.YOURDOMAIN.com/

You can login to Grownetics with admin@grownetics.co/GrowBetter

You can also access Chronograf/TICK at http(s)://tick.YOURDOMAIN.com/

You can login to TICK with the default username and password you set up with HomelabOS.
