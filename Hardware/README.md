[< Documentation](../README.md)
# Hardware
### [Production](hardware-production.md)

### Arduino Software Setup
1. Download [VSCode](https://code.visualstudio.com/Download)  
2. Open the Extensions menu and install PlatformIO IDE (http://docs.platformio.org/en/latest/ide/vscode.html#installation)  
3. After platform.io is downloaded in vscode restart vscode, it will initiate the install, upon completion a bar comes on up top to restart vscode. You're in!  

### Git the Grownetics Repo   
1. Download and Install [git](http://git-scm.com/download/mac) (already installed on linux)  
2. 'cd' to the folder you want the Grownetics Repo to be in  
3. Check out the code (open Terminal and copy-paste)
`git clone git@code.cropcircle.io:Grownetics/Grownetics.git`
if you are unable to authenticate login to gitlab via code.cropcircle.io and add your .ssh public key to your profile settings. Then try again.  
4. Back in platform.io open the firmware folder you want to work with.  (i.e. the Middleware folder)
5. Navigate to the src folder once the project folder is open, click the Middleware.ino file
6. Build the file by clicking the tiny check mark at the bottom of the vscode window. 
7. You may have a missing library, this is where platform.io is awesome! Click the PIO Home tab
8. Click Libraries in the PIO left menu, search for the missing library (case and spaces matter!)
9. Click install, and try your build again, should be gtg!
5. Click the little right hand arrow at bottom of vscode to Upload it to the board.

### Arduino/Wifi-Reset Software Testing
1. Make sure you have the latest code by running `git pull` within the project folder.
2. Turn on Server/Pepwave, make sure TP-Links are synced, and make sure the Arduino Ethernet Shield has an ethernet cord out to the POE device connected to one of the TP-Links.
3. Verify/Upload the 'Middleware.x.x.x.ino' code to the Arduino.
4. Open the serial monitor (Cmd/Ctrl+Shift+M) and make sure data is accurately coming out AND that the server is sending a response back to the Arduino. 
  * ***Uploading code to the Arduino while the WiFi-Reset is ON can sometimes cause problems. Try uploading with the WiFi-Reset OFF.***  
  * ***The serial monitor has specific baud (data rate in bits per second) values that it receives output from. Make sure your serial monitor is set to the same baud value as in the code to see the correct output.***  

5. Verify/Upload the 'ESP8266_WiFi_Reset.x.x.x.ino' code directly to the WiFi-Reset module. 
6. Open the serial monitor and make sure it's getting a status code of 200 from the Server. If not, make sure the Access Point (Pepwave) login credentials match with the credentials in the wifi reset code.

7. The Arduino and WiFi Reset devices have been successfully tested, to be ready for production: Set debug = false, and assign an ID to the device (starting at 1).

## 3D Crop Sensor

3D Crop Device: onsite.grownetics.co port 81
Reset Device: onsite.grownetics.co port 82

Assembly: Devices should have already passed their individual QC test

Components required (Multiplexer shield, Ethernet shield, Arduino Mega)

*  Before assembly check that all pins are straight and undamaged
*  Match Pins on Arduino Mega with pins on Ethernet shield and connect devices
*  Attach multiplexer shield to the Ethernet shield 

QC Assesment (Device in NEMA box)

*  Make sure device has rubber studs or Velcro to secure it to the base of the NEMA box
*  Insert POE power cable into Arduino mega 
*  Insert other POE cable into Ethernet shield

## Suppliers

### Rapid PCB Suppliers
1. http://www.pcbway.com/f
2. http://www.pentalogix.com/pcb-manufacturing.php?gclid=CLmT5YOP_csCFQmSaQodgkoIUw
3. https://www.itead.cc/
4. http://www.customcircuitboards.com/
5. http://www.pcb-solutions.com/
6. http://www.pcbunlimited.com/
7. http://www.4pcb.com/
8. http://www.goldphoenixpcb.com/
9. http://www.seeedstudio.com/service/index.php?r=pcb

#### Pepwave Max BR-1
 * Support Contact:  taylor@frontierus.com, jgrote@frontierus.com, tel:866-226-6344
 * Default Wifi Password: Last 8 digits of LAN MAC.
 * Default admin login: admin/admin

* SIM Cards
 * Verizon Business Support: 

