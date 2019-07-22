# XDebug

Install the PHP Debug extension for VSCode. Click the Debug icon, click the
green start button. Uncheck `Everything` in the breakpoints panel.
Set a breakpoint in the code somewhere, hit a page that loads that code,
enjoy your bugs.

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