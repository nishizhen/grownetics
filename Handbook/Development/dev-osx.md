
## Getting going

### Warning: require(/var/www/html/vendor/autoload.php): failed to open stream: No such file or directory

Run `docker exec -it growserver_growdash_1 composer install`

### Fatal error: Uncaught PDOException: SQLSTATE[HY000] [14] unable to open database file

Run `docker exec -it growserver_growdash_1 mkdir tmp`

### SQLSTATE[42S02]: Base table or view not found: 1146 Table 'grownetics.roles' doesn't exist

Run `docker exec -it growserver_growdash_1 bin/cake migrations migrate`

Then `docker exec -it growserver_growdash_1 bin/cake migrations seed`

### Could not locate ../js/bower_components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css for all.css in any configured path.

Run `docker exec -it growserver_growdash_1 apt update && apt install -y git && bower install`

## Testing with PHPUnit

Add a Run/Debug Configuration under PHPUnit called All Tests. Set the Directory to `/Users/$YOURUSERNAMEHERE$/Code/Grownetics/Grownetics/Server/html/tests`

Set the following as ENV Variables `DEV=0;DB_PASS=grownetics;CERES=0;DB_HOSTNAME=localhost`

Click Save, Click the Green 'Run' Button at the top of the PHPStorm Interface.

To run a specific test, right click just that test in the test result window and click the 'Run Test Name' option.

## DB Access

* Click View -> Tool Windows -> Database
* Click the wrench to edit the data source
* User: grownetics Password: get this from your Ansible/hosts file.
* Click SSH/SSL Tab
 * Use SSH Tunnel
 * Proxy host: web1.growserver.dev
 * Proxy user: ubuntu
 * Auth type: Key pair (OpenSSH)
 * Private Key File: ~/.ssh/id_rsa

## View Tweaks

For .ctp file syntax highlighting, open your preferences, go to Editor -> File Types and add `*.ctp` as a PHP file.

I personally like Darcula under Prefs->Appearance, and Blackboard under Prefs->Editor -> Colors & Fonts.

Enable all the Toggles under the View menu, 'Toolbar', 'Tool Buttons', 'Status Bar', and 'Navigation Bar'.

### Markdown

Install `Markdown Navigator` plugin to be able to click relative links inside the PHPStorm Markdown Preview pane.

## Search Anywhere

Un-map Command-T from 'Update Project' in Prefs -> Keymap, and map it instead to Search Everywhere.

Now typing '<Command-T>devi/add<Enter>' will open the devices/add.ctp file for example.

## PHPUnit

Setup PHP 7 on your vagrant machine as a remote interpreter. Setup your enviroment variables. ....

# General OS X Tips

Sharpen your axe.

## Sublime Text Package Manager
Install this in the Sublime Text Console: https://packagecontrol.io/installation.

Open command palette (Cmd + Shift + P) and install package CakePHP (2013 version).

## Passwordless sudo setup for vagrant-hostsupdater

https://github.com/cogitatio/vagrant-hostsupdater#passwordless-sudo

## MySQL Workbench
Download and install MySQL Workbench from here: http://dev.mysql.com/downloads/workbench/.

## Keyboard Layouts

* Try out Dvorak or Colemak keyboard layouts.
* Check out http://www.keybr.com/ for practice
* Learn VIM.
 * Trust me. Just do it. It's so powerful.