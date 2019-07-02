
# Onsite Server Configuration Guide

###  BIOS Settings

We need the servers to always turn on whenever they receive power.

* Start the machine, and at the first GNU GRUB boot menu, hit the down arrow to select 'System setup' hit Enter.
* Hit the right arrow to select 'Advanced' and down to select 'ACPI Settings' hit Enter.
* Down arrow to 'PWRON After PWR-Fail' set it to 'Always On'
* Hit F4 to Save & Reset, save configuration.
* Test that the configuration worked by pulling power to the server, and reconnecting it. It should turn on immediately.


# Configuring IPMI/iDRAC

Plug ethernet into iDRAC moving
Hit F2 to enter System Setup
Select `iDRAC Settings`
Select `Network`
Scroll down to `IPV4 Settings`
Change `Static IP Address` to something in the correct IP range. e.g. 192.168.50.100
Change `Static Gateway` to the correct range. e.g. 192.168.50.1
Scroll down to `IPMI Settings`
Set `Enable IPMI Over LAN` to `Enabled`
Hit `ESC`
Scroll down to and Select `User Configuration`
Enter a password into `Change Password` field
Hit `ESC`
Select `Yes` to save the changes.
Hit `OK`
Hit `ESC`
Select `Yes` to Confirm Exit
Open a browser and navigate to the `Static IP Address` that you entered above. e.g. http://192.168.50.101

## Ubuntu Install

* Create a Linux Server USB key if one isn't available.


Language: English
Location: United States
Detect Keyboard Layout: No
Country of origin for keyboard: English (US)
Keyboard Layout: English (US)
Network Interfaces: First One

Hostname: dr-onsite (note: this will often come from the DHCP server)
Username: grownetics
Create a unique password

Encrypt Home Directory: No

HWE = false

### Software RAID5 Setup with 3 disks

note: never do this on a server with dedicated raid card/hardware. This is for that BS FakeRaid that came on the logic supply ASRock mobos.

Choose manual partitioning
Make sure disks for software RAID are empty and have no partitions
Choose Configure Software RAID
Make new MD Device
Choose 3 (autofills) disks (also the minimum for RAID 5)
Choose 0 backup disks
Use space bar to 'select' the 3 disks to put in the SW RAID vol
Enter
Should end back at Configure Software Raid screen, continue.

Partioning method: Guided - use entire disk and set up LVM
Select disk to partition: first RAID5 Device
Write changes to disk: Yes
Amount of volume group to use for guided partioning: Just hit enter
Write changes to disk: Yes

HTTP Proxy: Just hit enter

How do you want to manage upgrades on this system?: Install Security Updates Automatically

Software Selection
	standard system utilities
	OpenSSH server

Login to the system, and execute

```
sudo su
(enter password)
echo "grownetics ALL=(ALL) NOPASSWD:ALL" >> /etc/sudoers
apt update
apt upgrade -y
exit
```
