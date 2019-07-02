# PHPStorm / XDebug Remote Debugging Setup ##

Xdebug should be installed in your vagrant box already.

The Xdebug remote properties should also already be setup by ansible if you are in a (dev == 1) environment.
Verify that `/etc/php/7.0/apache2/conf.d/20-xdebug.ini` contains the following lines:

```
xdebug.remote_enable = 1
xdebug.remote_host = 192.168.33.1
xdebug.remote_port = 9000
``` 

Make sure to restart your apache service if you made any changes `sudo service apache2 restart`

Setup roughly follows the guidelines from Jetbrains here:

https://confluence.jetbrains.com/display/PhpStorm/Zero-configuration+Web+Application+Debugging+with+Xdebug+and+PhpStorm

Except that for whatever reason in that tutorial they do things in a weird order, so here's the order I did it in.

### 1. Setup Path Mappings ###

Open Settings... navigate to Languages & Frameworks > PHP > Servers and click the + plus sign to add a new server.  Set the Host to `web1.growserver.dev` and then check the box labeled  "Use Path Mappings."

In the Path Mapping box, add a mapping from your local project's `html/` folder, e.g. `/Users/Conan/Grownetics/Server/html/`  to  `/var/www/html/current` on the growserver.

### 2. Setup default configuration file
Open preferences > Languages & Frameworks > PHP > PHPUnit.
Select your remote interpreter, under Test Runner change the default configuration file path to:
`/var/www/html/current/phpunit.xml.dist`

### 3. Install a browser toolbar or bookmarklet ###

See https://confluence.jetbrains.com/display/PhpStorm/Browser+Debugging+Extensions and choose the extension for your favorite browser.  I can confirm that the XDebug Helper for Chrome works.

### 4. Start a debugging session ###

1. In PHPStorm, go to Run > Start Listening for PHP Debug Connections.

2. Set a breakpoint somewhere.

3. Activate debug mode with your favorite browser extension.

4. Reload the page where you set the breakpoint.

## Profiling with XDebug ##

Create the output folder `mkdir -p /var/www/html/current/xdebug_profiler_output`

In order to enable profiling with XDebug, you will need a few more options set in php.ini:

```
xdebug.profiler_enable = 1
xdebug.profiler_remote_trigger = 1
xdebug.profiler_output_dir = /var/www/html/current/xdebug_profiler_output
```

You can set the value of `xdebug.profiler_output_dir`  to wherever you want, however for ease of importing into PHPStorm, you probably want this to be somewhere in the shared path of vagrant and your machine.

Edit /etc/apache2/sites-enabled/000-default.conf, set DEV to 0, run `sudo service apache2 restart` to disable DebugKit and get more useful and speedy Profiling information.

If you now click on the 'Profile' option of the XDebug toolbar or extension you installed, you should now see that a file named `cachegrind.out.[some_ID]` is created.  You can then import this file into PHPStorm by using "Tools > Analyze Xdebug Profiler Snapshot..." menu option.

## Debugging PHPUnit Tests ##
If you have already set up PHPUnit to run from PHPStorm, you can debug a PHPUnit test by creating a Run/Debug configuration and start in debug mode with the "Debug As..." option.  See https://confluence.jetbrains.com/display/PhpStorm/Debugging+and+Profiling+PHPUnit+and+Behat+Tests+with+PhpStorm