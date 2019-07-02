#include <Arduino.h>
#include <MemoryFree.h>
#include <ArduinoJson.h>
#include <SPI.h>
#include <OneWire.h>
#include <Wire.h>
#include <EmonLib.h>
#include <ctype.h>
#include <Adafruit_SleepyDog.h>

// ======================================
//            Sensor Device
// ======================================
// Installation Steps
// 1. Test PoE Cables
// 2. Test Cat5 Cable
// 3. Add Mac Address, IP address, and ID
// ======================================
//          Configuration Here
// ======================================

<<<<<<< HEAD
int deviceId = 4;
=======
int deviceId = 6;
>>>>>>> 5198f46b6d7e3997b960edfe762fc28bbccea4e4
bool debug = false; // Must be set to false when in Production.
char server[] = "onsite.grownetics.co";
int serverPort = 81;

// ======================================
// DO NOT CHANGE ANYTHING BELOW THIS LINE
// ======================================

char version[10] = "M-1.1.6";

byte macs[] = { 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x10,
0x11, 0x12, 0x13, 0x14, 0x15, 0x16, 0x17, 0x18, 0x19, 0x20,
0x21, 0x22, 0x23, 0x24, 0x25, 0x26, 0x27, 0x28, 0x29, 0x30,
0x31, 0x32, 0x33, 0x34, 0x35, 0x36, 0x37, 0x38, 0x39, 0x40,
0x41, 0x42, 0x43, 0x44, 0x45, 0x46, 0x47, 0x48, 0x49, 0x50 };

int deviceTier = (int) (deviceId / 50);
int deviceMod = deviceId % 50;
byte mac[] = { 0x90, 0xA2, 0xDA, 0x0F, macs[deviceTier], macs[deviceMod] };

int status = 0;
int ipSkipSize = 20;
int ipBase = 0;

unsigned long lastConnectionTime = millis();
unsigned long lastReadTime = 0;
boolean lastConnected = false;
boolean readingJson = false;
int refreshRate = 10000;
char inData[500];
int stringPos = 0;
unsigned char inputCount = 0;
EnergyMonitor emon1;

byte humidityData;

char outputData[400] = "";

int pinsType1[10];
int pinsType2[20];
int pinsType4[20];
int pinsType5[10];
int pinsType6[10];
int pinsType7[10];
int pinsType8[40];
int pinsType8Calibrations[40];
int pinsType9[10];
int pinsType11[15];
// Atlas Scientific RTD (Temperature)
int pinsType12[10];
int pinsType13[20]; //SM 

int SMpwer[16] = {30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45};

int outs[50];

//Mux control pins
int s0 = 6;
int s1 = 7;
int s2 = 8;
int s3 = 9;

int controlPin[] = {s0, s1, s2, s3};

int muxChannel[16][4]={
  {0,0,0,0}, //channel 0
  {1,0,0,0}, //channel 1
  {0,1,0,0}, //channel 2
  {1,1,0,0}, //channel 3
  {0,0,1,0}, //channel 4
  {1,0,1,0}, //channel 5
  {0,1,1,0}, //channel 6
  {1,1,1,0}, //channel 7
  {0,0,0,1}, //channel 8
  {1,0,0,1}, //channel 9
  {0,1,0,1}, //channel 10
  {1,1,0,1}, //channel 11
  {0,0,1,1}, //channel 12
  {1,0,1,1}, //channel 13
  {0,1,1,1}, //channel 14
  {1,1,1,1}  //channel 15
};

char sensordata[30];                     //A 30 byte character array to hold incoming data from the sensors
byte sensor_bytes_received = 0;          //We need to know how many characters bytes have been received
int channel;                             //INT pointer for channel switching - 0-7 serial, 8-127 I2C addresses
byte i2c_response_code = 0;              //used to hold the I2C response code.
byte in_char = 0;                    //used as a 1 byte buffer to store in bound bytes from an I2C stamp.

char dataIn[500]; 

void setup()
{
  pinMode(s0, OUTPUT);
  pinMode(s1, OUTPUT);
  pinMode(s2, OUTPUT);
  pinMode(s3, OUTPUT);
  digitalWrite(s0, LOW);
  digitalWrite(s1, LOW);
  digitalWrite(s2, LOW);
  digitalWrite(s3, LOW);
  Wire.begin();
  Serial.begin(9600);
  Serial1.begin(9600);
  Serial3.begin(9600);

  // There's no rush. We just started, and may be recovering from a power outage.
  // Give the network time to come back online
  delay(500)  ;
  
<<<<<<< HEAD
  srPrintln("We're live! Connecting to ESP8266!");
=======
  srPrintln("We're live! Try and connect.");
>>>>>>> 5198f46b6d7e3997b960edfe762fc28bbccea4e4
  /*
  if (debug == false) {
    if (Serial3.available() == 0) {
      // Serial Connection failed
      srPrintln("serial3.available ==0");
      reboot();
    } else {
      //srPrintln("Communication to ESP established!!");   
      }

      Serial.println();
    }*/
  
  pinsType1[0] = -1;
  pinsType2[0] = -1;
  pinsType4[0] = -1;
  pinsType5[0] = -1;
  pinsType6[0] = -1;
  pinsType7[0] = -1;
  pinsType8[0] = -1;
  pinsType8Calibrations[0] = -1;
  pinsType9[0] = -1;
  pinsType11[0] = -1;
  pinsType12[0] = -1;
  pinsType13[0] = -1; //SM pintype

  if (debug == true) {

    //outs[0] = 3;
    //outs[1] = 8;
    status = 1;
    StaticJsonBuffer<500> jsonBuffer;

    JsonObject& root = jsonBuffer.createObject();
    /*
    JsonArray& data = root.createNestedArray("i13");
    data.add("1");
    data.add("2");
   
    memset( &pinsType13, 0, 20 );
    setPins(data,pinsType13);
    //srPrintln(JsonArray& data);
    */

   JsonArray& data = root.createNestedArray("i2");
   //data.add("M1");
   data.add("M2");
       
   memset( &pinsType2, 0, 20 );
   setPins(data,pinsType2);

    JsonArray& data2 = root.createNestedArray("i4");
    data2.add("M3");
    setPins(data2,pinsType4);



  }
}

void loop()
{
  
  readDataFromServer();
  

  EspReaderDebug();
/*
  if (debug)
    {
      toggleOutputs(); 
    }
 */         
  sendDataToServer();
  
  // If we're not in 'Server Maintenance Mode', then check for timeouts and reboot as needed
  if (status < 2) {
    if (millis() - lastConnectionTime > refreshRate * 5) {
      srPrintln("Try and wake up..");
      transmitData("");
    }

    if (millis() - lastConnectionTime > refreshRate * 50) {
      srPrintln("Timed Out!");
      delay(100);
      reboot();
    }
  }
}

void toggleOutputs(){
  int ii = 0;
  // Check all arduino ports
  while(ii < 54)
  {
    bool found = FALSE;
    int iii = 0;
    if (outs) {
      //srPrintln(outs[iii]);
      if (outs[iii]<1) {
        //srPrint("Turn off: ");
        //srPrintln(ii);
        //dsdigitalWrite(ii, LOW);
      } else {
        while(outs[iii]>0 && found == FALSE) {
          if (ii == outs[iii]) {
            //srPrint("Turn on: ");
            //srPrintln(ii);
            //srPrint("iii: ");
            //srPrintln(iii);
            pinMode(ii, OUTPUT);
            digitalWrite(ii, HIGH);
            found = TRUE;
          }
          iii++;
        }
        if (found == FALSE) {
          //srPrintln(ii);
          digitalWrite(ii, LOW);
        }
      }
    } else {
        while(ii < 54){
          digitalWrite(ii, LOW);
          ii++;
      }
    }
    ii++;
  }
}


void srPrint(char const* obj) {
  Serial.print(obj);
  Serial1.print(obj);
}

void srPrint(int obj) {
  Serial.print(obj);
  Serial1.print(obj);
}

void srPrintln(char const* obj) {
  Serial.println(obj);
  Serial1.println(obj);
}

void srPrintln(int obj) {
  Serial.println(obj);
  Serial1.println(obj);
}

void transmitData(char *data) {
  srPrint("Got data to send: ");
  srPrintln(data);
  if (strlen(data)<1) {
    data="\"x\":[]";
  }
  char jsonOut[500];
  sprintf(
    jsonOut,
    "{\"id\":%i,\"v\":\"%s\",\"st\":%i,\"m\":%i,%s}",
    deviceId,
    version,
    status,
    getFreeMemory(),
    data
  );
  if (debug == true) {
    return;
  }
  srPrint("Sending to ");
  srPrint(server);
  srPrint(":");
  srPrintln(serverPort);

  if (Serial3.available() > 0) {
    //srPrintln("Connected to esp!");
    lastConnectionTime = millis();
  } else {
    srPrintln("Not connected, try to reconnect.");
     }
  srPrintln("Transmitting Data...");
  Serial.println(jsonOut);
  Serial3.println("(data from mega...)");
  Serial3.println(jsonOut);
}


void reboot() {
  srPrintln("** Rebooting.");
  delay(500);
  asm volatile ("  jmp 0");
}

byte I2C_call() {           //function to parse and call I2C commands.
  sensor_bytes_received = 0;                            // reset data counter
  memset(sensordata, 0, sizeof(sensordata));            // clear sensordata array;

  Wire.beginTransmission(channel);                  //call the circuit by its ID number.
  Wire.write("r");                      //transmit the command that was sent through the serial port.
  Wire.endTransmission();                           //end the I2C data transmission.

  i2c_response_code = 254;
  while (i2c_response_code == 254) {      // in case the stamp takes longer to process than we expected

    delay(1000);                        // reading command takes about a second

    Wire.requestFrom(channel, 48, 1);     //call the circuit and request 48 bytes (this is more then we need).
    i2c_response_code = Wire.read();      //the first byte is the response code, we read this separately.

    while (Wire.available()) {            //are there bytes to receive.
      in_char = Wire.read();              //receive a byte.

      if (in_char == 0) {                 //if we see that we have been sent a null command.
        Wire.endTransmission();           //end the I2C data transmission.
        break;                            //exit the while loop.
      }
      else {
        sensordata[sensor_bytes_received] = in_char;        //load this byte into our array.
        sensor_bytes_received++;
      }
    }

    switch (i2c_response_code) {         //switch case based on what the response code is.
      case 1:                          //decimal 1.
        srPrintln( "< ack");     //means the command was successful.
        break;                           //exits the switch case.

      case 2:                          //decimal 2.
        srPrintln( "< command failed");     //means the command has failed.
        break;                           //exits the switch case.

      case 254:                        //decimal 254.
        srPrintln( "< command pending");    //means the command has not yet been finished calculating.
        break;                           //exits the switch case.

      case 255:                        //decimal 255.
        srPrintln( "No Data");    //means there is no further data to send.
        break;                           //exits the switch case.
    }
  }
}

// Float to array. Pass it a char array to put the float conversion into.
char *ftoa(char *a, double f, int precision)
{
  long p[] = {0,10,100,1000,10000,100000,1000000,10000000,100000000};

  char *ret = a;
  long heiltal = (long)f;
  itoa(heiltal, a, 10);
  while (*a != '\0') a++;
  *a++ = '.';
  long desimal = abs((long)((f - heiltal) * p[precision]));
  itoa(desimal, a, 10);
  return ret;
}

void readAnalogPin(char *data, int pinNumber) {
          char pinNumberBuf[4];
          itoa(pinNumber,pinNumberBuf,10);

          strcat(data,"[A");
          strcat(data,pinNumberBuf);
          strcat(data,":");
          char buffer[7];
	  srPrint("Reading analog pin: ");
    srPrintln(pinNumber);
          srPrintln(analogRead(pinNumber));
          ftoa(buffer,analogRead(pinNumber),2);
          strcat(data,buffer);
          strcat(data,"]");

}

char *getWaterTemp(char *b, int pin){
  byte data[12];
  byte addr[8];
  OneWire ds(pin);
  srPrint("Get water temp from pin: ");
  srPrintln(pin);
  if ( !ds.search(addr)) {
   //no more sensors on chain, reset search
    ds.reset_search();
    return "Err:0";
  }

  if ( OneWire::crc8( addr, 7) != addr[7]) {
    srPrintln("CRC is not valid!");
    return "Err:1";
  }

  if ( addr[0] != 0x10 && addr[0] != 0x28) {
    srPrint("Device is not recognized");
    return "Err:2";
  }

  ds.reset();
  ds.select(addr);
  ds.write(0x44,1);

  byte present = ds.reset();
  ds.select(addr);  
  ds.write(0xBE);

  for (int i = 0; i < 9; i++) {
    data[i] = ds.read();
  }

  ds.reset_search();

  byte MSB = data[1];
  byte LSB = data[0];

  float tempRead = ((MSB << 8) | LSB);
  float TemperatureSum = tempRead / 16;
  char *buffer = b;
  ftoa(buffer,TemperatureSum,2);
  return buffer;
}

boolean fetch_humidity_temperature(unsigned int *p_H_dat, unsigned int *p_T_dat, int pin)
{

  byte address, Hum_H, Hum_L, Temp_H, Temp_L, _status;
  unsigned int H_dat, T_dat;
  address = 0x27;
  delay(100);
  Wire.beginTransmission(address); 
  delay(100);
  if (Wire.endTransmission() == 0) {

    Wire.requestFrom((int)address, 0x03, (int) 4);
    Hum_H = Wire.read();
    Hum_L = Wire.read();
    Temp_H = Wire.read();
    Temp_L = Wire.read();
    Wire.endTransmission();
    
    _status = (Hum_H >> 6) & 0x03;
    Hum_H = Hum_H & 0x3f;
    H_dat = (((unsigned int)Hum_H) << 8) | Hum_L;
    T_dat = (((unsigned int)Temp_H) << 8) | Temp_L;
    T_dat = T_dat / 4;
    *p_H_dat = H_dat;
    *p_T_dat = T_dat;

    humidityData = _status;
    return(TRUE);
  } else {
    srPrintln("Fault wire.endTransmission");
    return(FALSE);
  }
}   

int isNumeric (const char * s)
{
  if (s == NULL || *s == '\0' || isspace(*s))
    return 0;
  char * p;
  strtod (s, &p);
  return *p == '\0';
}

///////////////////////////////////////////////////////////////////
// Function : int readCO2()
// Returns : CO2 Value upon success, 0 upon checksum failure
// Assumes : - Wire library has been imported successfully.
// - LED is connected to IO pin 13
// - CO2 sensor address is defined in co2_addr
///////////////////////////////////////////////////////////////////
int readCO2()
{
  int co2_value = 0;
  Wire.beginTransmission(0x68);
  Wire.write(0x22);
  Wire.write(0x00);
  Wire.write(0x08);
  Wire.write(0x2A);

  if (Wire.endTransmission() == 0) {
    delay(10);
    Wire.requestFrom(0x68, 4);
    byte ii = 0;
    byte buffer[4] = {0, 0, 0, 0};
    delay(10);
    while(Wire.available() and ii < 500)
    {
      buffer[ii] = Wire.read();
      ii++;
    }

    co2_value = 0;
    co2_value |= buffer[1] & 0xFF;
    co2_value = co2_value << 8;
    co2_value |= buffer[2] & 0xFF;

    byte sum = 0;
    sum = buffer[0] + buffer[1] + buffer[2];

    if(sum == buffer[3])
    {
      return co2_value;
    }
    else
    {
      return 0;
    }
  } else {
    srPrintln("Fault wire transmission Co2");
    return(FALSE);
  }
}

int setPins(JsonArray& array, int pins[])
{
  //srPrintln(array.size());
  int ii = 0;
  boolean ended = false;
  while (ended == false and ii < 50) {
    
    //srPrint("Strlen: ");
    //srPrintln(strlen(array[ii]));
    
    const char* sensor = array[ii].asString();
    const char *sensorP = sensor;
      //srPrint("Sensor: ");
      //srPrintln(sensor);
      // Drop the A / D marker by moving the pointer forward one slot.
    if (!isDigit(sensorP[0])) {
      sensorP++;
    }
    if (strlen(sensorP)>0) {
      //srPrint("Sensor length: ");
      //srPrintln(strlen(sensorP));
      //srPrint("Got a sensor!!!: ");
      //srPrintln(sensorP);
      pins[ii] = atoi(sensorP);
      ii++;
    } else {
      ended = true;
    }
  }
  pins[ii] = -1;
}

int switchMux(int channel){
  channel--;
  //loop through the 4 sig
  for(int aa = 0; aa < 4; aa++){
    digitalWrite(controlPin[aa], muxChannel[channel][aa]);
  }
  delay(50);
}

void readDataFromServer() {
  // srPrint("client.available(): ");
  // srPrintln(client.available());
  // srPrintln(lastReadTime);
  // srPrintln(millis() - lastReadTime);

  
  if (Serial3.available() > 0 && (millis() - lastReadTime > refreshRate) ) {
    
    char inChar = Serial3.read();
//    srPrint(&inChar);
    if(inChar == '{') {
        stringPos = 0;
        memset( &inData, 0, 500 );
        readingJson = true;
    }

    if(readingJson) {
      if(stringPos < 500) {
        inData[stringPos] = inChar; // Store it
        stringPos++; 
      } else {
        // More than 500 characters? Nope, reboot.
        srPrintln("more than 500 chars");
        reboot();
      }
      if(inChar == '<') {
        // We got bad data from the server, reboot, and wait until it's happy again.
        srPrintln("bad character");
        reboot();
      }
      if(inChar == '}') {
        readingJson = false;

        srPrint("Got JSON: ");
        srPrintln(inData);
        StaticJsonBuffer<500> jsonBuffer;
        JsonObject& root = jsonBuffer.parseObject(inData);

        // Test if parsing succeeds.
        if (root.success()) {
          lastReadTime = millis();
        } else {
          srPrintln("root.success = false ");
          reboot();
        }
        if (root.containsKey("boot")) {
          //srPrintln("Booting!");
          status = 1;
          // We're booting, set up inputs and outputs
          memset( &pinsType1, 0, 2 );
          memset( &pinsType2, 0, 10 );
          memset( &pinsType4, 0, 2 );
          memset( &pinsType5, 0, 2 );
          memset( &pinsType6, 0, 2 );
          memset( &pinsType7, 0, 2 );
          memset( &pinsType8, 0, 10 );
          memset( &pinsType8Calibrations, 0, 10 );
          memset( &pinsType9, 0, 10 );
          memset( &pinsType11, 0, 10 );
          memset( &pinsType12, 0, 10 );
          memset( &pinsType13, 0, 10 ); //SM

          setPins(root["i1"],pinsType1);
          setPins(root["i2"],pinsType2);
          setPins(root["i4"],pinsType4);
          setPins(root["i5"],pinsType5);
          setPins(root["i6"],pinsType6);
          setPins(root["i7"],pinsType7);
          setPins(root["i8"],pinsType8);
          setPins(root["i8c"],pinsType8Calibrations);
          setPins(root["i9"],pinsType9);
          setPins(root["i11"],pinsType11);
          //srPrintln("===Set RTD==");
          setPins(root["i12"],pinsType12);
          setPins(root["i13"],pinsType13);
        }

        if (root.containsKey("refresh")) {
          srPrint("Setting refresh rate at: ");
          refreshRate = root["refresh"];
          srPrintln(refreshRate);
        }

        if (root.containsKey("maintenance")) {
          int maintenance = root["maintenance"];
          if (maintenance == 1) {
            // Enter maintenance mode so the device doesn't reboot due to network timeout
            status = 2;
          } else {
            status = 1;
          }
        }

        int ii = 0;
        if (root.containsKey("outs")) {
          memset( &outs, 0, 54 );
          setPins(root["outs"],outs);
<<<<<<< HEAD
    
=======
>>>>>>> 5198f46b6d7e3997b960edfe762fc28bbccea4e4
          
          // Check all arduino ports
          while(ii < 54)
          {
            bool found = FALSE;
            int iii = 0;
            if (outs) {
              //srPrintln(outs[iii]);
              if (outs[iii]<1) {
                //srPrint("Turn off: ");
                //srPrintln(ii);
                digitalWrite(ii, LOW);
              } else {
                while(outs[iii]>0 && found == FALSE) {
                  if (ii == outs[iii]) {
                    //srPrint("Turn on: ");
                    //srPrintln(ii);
                    //srPrint("iii: ");
                    //srPrintln(iii);
                    pinMode(ii, OUTPUT);
                    digitalWrite(ii, HIGH);
                    found = TRUE;
                  }
                  iii++;
                }
                if (found == FALSE) {
                  //srPrintln(ii);
                  digitalWrite(ii, LOW);
                }
              }
            }
            ii++;
          }
        } else {
          while(ii < 54)
          {
            digitalWrite(ii, LOW);
            ii++;
          }
        }
      } // Finished reading JSON
    } // Reading JSON

  } // Client available

  // if there's no net connection, but there was one last time
  // through the loop, then stop the client:
  //if (!client.connected() && lastConnected) {
    //client.stop();
  //}
}

void sendDataToServer() {
  // if you're not connected, and ten seconds have passed since
  // your last connection, then connect again and send data:
  if((millis() - lastConnectionTime > refreshRate)) {
    srPrint("Device Status: ");
    srPrintln(status);

    switch (status) {
      case 0:
      {
        transmitData("\"b\":0");
        break;
      }
      case 1:
      case 2:
      {
        char data[400] = "";

        if (getFreeMemory() < 40) {
          srPrintln("*Memory gone!");
          asm volatile ("  jmp 0");
        }
        int countdownMS = Watchdog.enable(2000);
        // Water temp
        int ii = 0;
        while(pinsType1[ii] > -1)
        {
          if (strlen(data)!=0) {
            strcat(data,",");
          }
          char temp[10];
          int pinNumber = pinsType1[ii];
          getWaterTemp(temp,pinNumber);
          char pinNumberBuf[4];
          itoa(pinNumber,pinNumberBuf,10);
          strcat(data,"[D");
          strcat(data,pinNumberBuf);
          strcat(data,":");
          strcat(data,temp);
          strcat(data,"]");
          ii++;
        }
         Watchdog.disable();

        // Humidity and air temp
        ii = 0;
        Watchdog.enable(2000);
        while(pinsType2[ii] > -1)
        {
          srPrintln("Get humidity");
          // srPrint("pinsType2 ii: ");
          // srPrintln(ii);
          if (strlen(data)!=0) {
            strcat(data,",");
          }

          int pinNumber = pinsType2[ii];

          // srPrint("pinsType2 pin: ");
          // srPrintln(pinNumber);

          switchMux(pinNumber);

          unsigned int H_dat, T_dat;
          float RH, T_C;
          boolean gotHumidity = fetch_humidity_temperature(&H_dat, &T_dat, pinNumber);
          char pinNumberBuf[4];
          itoa(pinNumber,pinNumberBuf,10);

          if (gotHumidity) {
            srPrintln("Got data!");
            RH = (float) H_dat * 6.10e-3;
            T_C = (float) T_dat * 1.007e-2 - 40.0;
            strcat(data,"[M");
            strcat(data,pinNumberBuf);
            strcat(data,":");
            char buffer[7];
            ftoa(buffer,RH,2);
            strcat(data,buffer);
            strcat(data,"-");
            ftoa(buffer, T_C, 2);
            strcat(data,buffer);
            strcat(data,"]");
          } else {
            // We didn't get a valid reading, send back 0
            strcat(data,"[M");
            strcat(data,pinNumberBuf);
            strcat(data,":0-0]");
          }
          ii++;
        }
        Watchdog.disable();

        Watchdog.enable(2000);
        // Co2
        ii = 0;
        while(pinsType4[ii] > -1)
        {
          // srPrintln("Get CO2");
          // srPrint("pinsType4 ii: ");
          // srPrintln(ii);
          if (strlen(data)!=0) {
            strcat(data,",");
          }
          char temp[10];
          int pinNumber = pinsType4[ii];
          switchMux(pinNumber);
          // srPrint("pinsType4 pin: ");
          // srPrintln(pinNumber);
          // delay(100);
          int co2Value = readCO2();
          char pinNumberBuf[4];
          itoa(pinNumber,pinNumberBuf,10);
          char co2ValueBuf[4];
          itoa(co2Value,co2ValueBuf,10);
          strcat(data,"[M");
          strcat(data,pinNumberBuf);
          strcat(data,":");
          strcat(data,co2ValueBuf);
          strcat(data,"]");
          ii++;
        }
        Watchdog.disable();


        // '5' => 'pH Sensor',
        ii = 0;
        Watchdog.enable(2000);
        if (pinsType5[ii] > -1) {
            channel = 99;
            I2C_call();  // send i2c command and wait for answer
            if (sensor_bytes_received > 0) {
              srPrint("< ");
              srPrintln(sensordata);       //print the data.
              if (strlen(data)!=0) {
                strcat(data,",");
              }
              char pinNumberBuf[4];
              itoa(pinsType5[ii],pinNumberBuf,10);
              strcat(data,"[");
              strcat(data,pinNumberBuf);
              strcat(data,":");
              strcat(data,sensordata);
              strcat(data,"]");
              ii++;
            }
        }
        Watchdog.disable();

        // DO
        ii = 0;
        Watchdog.enable(2000);
        if (pinsType6[ii] > -1) {
            channel = 97;
            I2C_call();  // send i2c command and wait for answer
            if (sensor_bytes_received > 0) {
              srPrint("< ");
              srPrintln(sensordata);       //print the data.
              if (strlen(data)!=0) {
                strcat(data,",");
              }
              char pinNumberBuf[4];
              itoa(pinsType6[ii],pinNumberBuf,10);
              strcat(data,"[");
              strcat(data,pinNumberBuf);
              strcat(data,":");
              strcat(data,sensordata);
              strcat(data,"]");
              ii++;
            }
        }
        Watchdog.disable();

        // EC
        ii = 0;
        Watchdog.enable(2000);
        if (pinsType7[ii] > -1) {
            channel = 100;
            I2C_call();  // send i2c command and wait for answer
            if (sensor_bytes_received > 0) {
              srPrint("< ");
              srPrintln(sensordata);       //print the data.
              if (strlen(data)!=0) {
                strcat(data,",");
              }
              char pinNumberBuf[4];
              itoa(pinsType7[ii],pinNumberBuf,10);
              strcat(data,"[");
              strcat(data,pinNumberBuf);
              strcat(data,":");
              strcat(data,sensordata);
              strcat(data,"]");
              ii++;
            }
        }
        Watchdog.disable();

        // Current Transformer (CT Sensor)
        ii = 0;
 
        while(pinsType8[ii] > -1)
        {
          srPrint("Pins type 8[0]: ");
          srPrintln(pinsType8[0]);
          if (strlen(data)!=0) {
            strcat(data,",");
          }
          int pinNumber = pinsType8[ii];
          // pinNumber--;
          srPrint("Read current for pin: ");
          srPrint(pinNumber);
          char pinNumberBuf[4];
          itoa(pinNumber,pinNumberBuf,10);

          strcat(data,"[A");
          strcat(data,pinNumberBuf);
          strcat(data,":");

          // http://openenergymonitor.org/emon/buildingblocks/calibration
          emon1.current(pinsType8[ii], 100);
          double Irms = emon1.calcIrms(1480);
          char buffer[7];
          ftoa(buffer,Irms,2);
          strcat(data,buffer);
          strcat(data,"]");
          delay(100);
          ii++;
        }
        
	      // Fill Level
        ii = 0;
        Watchdog.enable(2000);
        while(pinsType9[ii] > -1)
        {
          if (strlen(data)!=0) {
            strcat(data,",");
          }

          int pinNumber = pinsType9[ii];
          srPrint("Got pin number: ");
          srPrintln(pinNumber);
	        readAnalogPin(data,pinNumber);
          ii++;
        }
        Watchdog.disable();


        // PAR Sensor
        ii = 0;
        Watchdog.enable(2000);
        while(pinsType11[ii] > -1)
        {
          if (strlen(data)!=0) {
            strcat(data,",");
          }

          int pinNumber = pinsType11[ii];
          srPrint("Got pin number: ");
          srPrintln(pinNumber);

          readAnalogPin(data,pinNumber);

          ii++;
        }
        Watchdog.disable();


        // Atlas Scientific RTD (Temperature)
        ii = 0;
        if (pinsType12[ii] > -1) {
            srPrintln("Get RTD");
            channel = 102;
            I2C_call();  // send i2c command and wait for answer
            if (sensor_bytes_received > 0) {
              srPrint("< ");
              srPrintln(sensordata);       //print the data.
              if (strlen(data)!=0) {
                strcat(data,",");
              }
              char pinNumberBuf[4];
              itoa(pinsType12[ii],pinNumberBuf,10);
              strcat(data,"[");
              strcat(data,pinNumberBuf);
              strcat(data,":");
              strcat(data,sensordata);
              strcat(data,"]");
              ii++;
            }
        }
        // Soil Moisture 
        ii = 0;
        Watchdog.enable(16500); //need this to be at least 16s + 10msx16 for max I.O. of 16
        while(pinsType13[ii] > -1)
        {
          if (strlen(data)!=0) {
            strcat(data,",");
          }
          srPrintln("Starting Pintype13 method");
          pinMode(SMpwer[ii], OUTPUT);

          int pinNumber = pinsType13[ii];
          srPrint("Setting ");
          srPrint(SMpwer[ii]);
          srPrintln(" to HIGH");
          digitalWrite(SMpwer[ii], HIGH);
          delay(1000); //needs to be powered on for at least 40ms for Vegetronix sensors

	        readAnalogPin(data,pinNumber);
          delay(10);
          digitalWrite(SMpwer[ii], LOW);
          ii++;
        }
        Watchdog.disable();

        sprintf(outputData,"\"d\":\"%s\"",
          data
        );
        transmitData(outputData);

        // free(pinsBuf);
        break;
      }
    }
    if (debug == TRUE) {
      lastConnectionTime = millis();
    }
  }
  // store the state of the connection for next time through the loop:
  if (debug == TRUE) {
    lastConnected = millis();
  } 
}
void EspReaderDebug(void)
{
if (Serial3.available() > 0 && (millis() - lastReadTime > refreshRate && debug) ) 
{
    
    char inChar = Serial3.read();
//    srPrint(&inChar);
    if(inChar == '{') 
    {
        stringPos = 0;
        memset( &inData, 0, 500 );
        readingJson = true;
    }

    if(readingJson) 
    {
      if(stringPos < 500) 
      {
        inData[stringPos] = inChar; // Store it
        stringPos++; 
      } 
      else 
      {
        // More than 500 characters? Nope, reboot.
        srPrintln("More than 500 chars");
        reboot();
      }
      if(inChar == '<') 
      {
        // We got bad data from the server, reboot, and wait until it's happy again.
        srPrintln("bad char");
        reboot();
      }
      if(inChar == '}') 
      {
        readingJson = false;

        srPrint("Got JSON: ");
        srPrintln(inData);
         StaticJsonBuffer<500> jsonBuffer;
        JsonObject& root = jsonBuffer.parseObject(inData);

        // Test if parsing succeeds.
        if (root.success()) {
          lastReadTime = millis();
        } else {
          srPrintln("root.failed");
          reboot();
        }
        if (root.containsKey("boot")) {
          srPrintln("Booting!");
          status = 1;
          // We're booting, set up inputs and outputs
          memset( &pinsType1, 0, 2 );
          memset( &pinsType2, 0, 10 );
          memset( &pinsType4, 0, 2 );
          memset( &pinsType5, 0, 2 );
          memset( &pinsType6, 0, 2 );
          memset( &pinsType7, 0, 2 );
          memset( &pinsType8, 0, 10 );
          memset( &pinsType8Calibrations, 0, 10 );
          memset( &pinsType9, 0, 10 );
          memset( &pinsType11, 0, 10 );
          memset( &pinsType12, 0, 10 );

          setPins(root["i1"],pinsType1);
          setPins(root["i2"],pinsType2);
          setPins(root["i4"],pinsType4);
          setPins(root["i5"],pinsType5);
          setPins(root["i6"],pinsType6);
          setPins(root["i7"],pinsType7);
          setPins(root["i8"],pinsType8);
          setPins(root["i8c"],pinsType8Calibrations);
          setPins(root["i9"],pinsType9);
          setPins(root["i11"],pinsType11);
          //srPrintln("===Set RTD==");
          setPins(root["i12"],pinsType12);
        }

        if (root.containsKey("refresh")) {
          srPrint("Setting refresh rate at: ");
          refreshRate = root["refresh"];
          srPrintln(refreshRate);
        }

        if (root.containsKey("maintenance")) {
          int maintenance = root["maintenance"];
          if (maintenance == 1) {
            // Enter maintenance mode so the device doesn't reboot due to network timeout
            status = 2;
          } else {
            status = 1;
          }
        }

        int ii = 0;
        if (root.containsKey("outs")) {
          memset( &outs, 0, 54 );
          setPins(root["outs"],outs);

          // Check all arduino ports
          while(ii < 54)
          {
            bool found = FALSE;
            int iii = 0;
            if (outs) {
              //srPrintln(outs[iii]);
              if (outs[iii]<1) {
                //srPrint("Turn off: ");
                //srPrintln(ii);
                digitalWrite(ii, LOW);
              } else {
                while(outs[iii]>0 && found == FALSE) {
                  if (ii == outs[iii]) {
                    srPrint("Turn on: ");
                    srPrintln(ii);
                    srPrint("iii: ");
                    srPrintln(iii);
                    pinMode(ii, OUTPUT);
                    digitalWrite(ii, HIGH);
                    found = TRUE;
                  }
                  iii++;
                }
                if (found == FALSE) {
                  //srPrintln(ii);
                  digitalWrite(ii, LOW);
                }
              }
            }
            ii++;
          }
        } else {
          while(ii < 54)
          {
            digitalWrite(ii, LOW);
            ii++;
          }
        }
      } // Finished reading JSON
    } // Reading JSON

  } // Client available

  // if there's no net connection, but there was one last time
  // through the loop, then stop the client:
  //if (!client.connected() && lastConnected) {
    //client.stop();
  //}
        

}      
/*
/*void parseJson(void) {

  /* need to parse in the following format
    outs[0] = 3;
    outs[1] = 8;
    status = 1;
    StaticJsonBuffer<500> jsonBuffer;
    //JsonObject& root = jsonBuffer.parseObject(inData);

    JsonObject& root = jsonBuffer.createObject();
    JsonArray& data = root.createNestedArray("i9");
    data.add("1");
    data.add("2");
  
   
    memset( &pinsType9, 0, 20 );
    setPins(data,pinsType9);
    //srPrintln(JsonArray& data);
    
  */
/*
 void loop() {
 
  Serial.println("—————— -");
  char JSONMessage[] = " {\"SensorType\": \"Temperature\", \"Value\": [20, 21, 23]}";
  Serial.print("Message to parse: ");
  Serial.println(JSONMessage);
 
  StaticJsonBuffer<300> JSONBuffer; //Memory pool
  JsonObject& parsed = JSONBuffer.parseObject(JSONMessage);   //Parse message
 
  if (!parsed.success()) {      //Check for errors in parsing
 
    Serial.println("Parsing failed");
    delay(5000);
    return;
 
  }
 
  const char * sensorType = parsed["SensorType"]; //Get sensor type value
  Serial.print("Sensor type: ");
  Serial.println(sensorType);
 
  int arraySize = parsed["Value"].size();   //get size of JSON Array
  Serial.print("\nSize of value array: ");
  Serial.println(arraySize);
 
  Serial.println("\nArray values without explicit casting");
  for (int i = 0; i < arraySize; i++) { //Iterate through results
 
    int sensorValue = parsed["Value"][i];  //Implicit cast
    Serial.println(sensorValue);
 
  }
 
  Serial.println("\nArray values with explicit casting");
  for (int i = 0; i < arraySize; i++) {  //Iterate through results
 
    Serial.println(parsed["Value"][i].as<int>());//Explicit cast
 
  }
 
  Serial.println();
  delay(5000);
 
}
 
}
*/