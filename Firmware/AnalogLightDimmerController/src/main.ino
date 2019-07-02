#include <ArduinoJson.h>
#include <Ethernet2.h>
#include <OneWire.h>
#include <Wire.h>
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

int deviceId = 1;
bool debug = false; // Must be set to false when in Production.

// ======================================
// DO NOT CHANGE ANYTHING BELOW THIS LINE
// ======================================

char server[] = "onsite.grownetics.co";
int serverPort = 80;
char version[10] = "ALDC-1.0";

byte macs[] = { 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x10,
                0x11, 0x12, 0x13, 0x14, 0x15, 0x16, 0x17, 0x18, 0x19, 0x20,
                0x21, 0x22, 0x23, 0x24, 0x25, 0x26, 0x27, 0x28, 0x29, 0x30,
                0x31, 0x32, 0x33, 0x34, 0x35, 0x36, 0x37, 0x38, 0x39, 0x40,
                0x41, 0x42, 0x43, 0x44, 0x45, 0x46, 0x47, 0x48, 0x49, 0x50
              };

byte ip[] = { 10, 5, 1, deviceId };

int deviceTier = (int) (deviceId / 50);
int deviceMod = deviceId % 50;
byte mac[] = { 0x90, 0xA2, 0xDA, 0x0F, macs[deviceTier], macs[deviceMod] };

EthernetClient client;
int status = 0;

unsigned long lastConnectionTime = millis();
unsigned long lastReadTime = 0;
boolean lastConnected = false;
boolean readingJson = false;
int refreshRate = 1000;
char inData[500];
int stringPos = 0;
unsigned char inputCount = 0;

byte humidityData;

char outputData[400] = "";

int pinsType2[20];
int pinsType4[20];
int pinsType11[15];

int outs[50];

//Mux control pins
int s0 = 6;
int s1 = 7;
int s2 = 8;
int s3 = 9;

int controlPin[] = {s0, s1, s2, s3};

int muxChannel[16][4] = {
  {0, 0, 0, 0}, //channel 0
  {1, 0, 0, 0}, //channel 1
  {0, 1, 0, 0}, //channel 2
  {1, 1, 0, 0}, //channel 3
  {0, 0, 1, 0}, //channel 4
  {1, 0, 1, 0}, //channel 5
  {0, 1, 1, 0}, //channel 6
  {1, 1, 1, 0}, //channel 7
  {0, 0, 0, 1}, //channel 8
  {1, 0, 0, 1}, //channel 9
  {0, 1, 0, 1}, //channel 10
  {1, 1, 0, 1}, //channel 11
  {0, 0, 1, 1}, //channel 12
  {1, 0, 1, 1}, //channel 13
  {0, 1, 1, 1}, //channel 14
  {1, 1, 1, 1} //channel 15
};

char sensordata[30];                     //A 30 byte character array to hold incoming data from the sensors
byte sensor_bytes_received = 0;          //We need to know how many characters bytes have been received
int channel;                             //INT pointer for channel switching - 0-7 serial, 8-127 I2C addresses
byte i2c_response_code = 0;              //used to hold the I2C response code.
byte in_char = 0;                    //used as a 1 byte buffer to store in bound bytes from an I2C stamp.

// Float to array. Pass it a char array to put the float conversion into.
char *ftoa(char *a, double f, int precision)
{
  long p[] = {0, 10, 100, 1000, 10000, 100000, 1000000, 10000000, 100000000};

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
  ftoa(buffer,analogRead(pinNumber),2);
  strcat(data,buffer);
  strcat(data,"]");
}

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

  // There's no rush. We just started, and may be recovering from a power outage.
  // Give the network time to come back online.
  delay(500);
  srPrintln("We're live! Try and connect.");
  if (debug == false) {
    Ethernet.begin(mac, ip);
  }

  pinsType2[0] = -1;
  pinsType4[0] = -1;
  pinsType11[0] = -1;
}

void loop()
{
  readDataFromServer();

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


void readDataFromServer() {
  
  Ethernet.maintain();

  if (client.available() && (millis() - lastReadTime > refreshRate)) {

    char inChar = client.read();
    // srPrint(&inChar);
    if (inChar == '{') {
      stringPos = 0;
      memset( &inData, 0, 500 );
      readingJson = true;
    }

    if (readingJson) {
      if (stringPos < 500) {
        inData[stringPos] = inChar; // Store it
        stringPos++;
      } else {
        // More than 500 characters? Nope, reboot.
        reboot();
      }
      if (inChar == '<') {
        // We got bad data from the server, reboot, and wait until it's happy again.
        reboot();
      }
      if (inChar == '}') {
        readingJson = false;
        srPrint("Got JSON: ");
        srPrintln(inData);

        StaticJsonBuffer<500> jsonBuffer;
        JsonObject& root = jsonBuffer.parseObject(inData);

        if (root.success()) {
          lastReadTime = millis();
        } else {
          srPrintln("JSON parse failed.");
          delay(500);
          reboot();
        }

        if (root.containsKey("pins")) {
          srPrintln("Got pins, set 'em!");

          int pins[10];
          int pinCount = root["pins"].as<JsonArray>().copyTo(pins);

          int pinValues[10];
          int pinValuesCount = root["values"].as<JsonArray>().copyTo(pinValues);
          int ii;
          for (ii = 0; ii < pinCount; ii = ii + 1) {
            srPrint("Got pin: ");
            srPrint(pins[ii]);
            srPrint(" With value: ");
            srPrintln(pinValues[ii]);
            analogWrite(pins[ii], pinValues[ii]);
          }
        } // Done setting pins

        client.stop();
      } // Finished reading JSON
    } // Reading JSON

  } // Client available

  // if there's no net connection, but there was one last time
  // through the loop, then stop the client:
  if (!client.connected() && lastConnected) {
    client.stop();
  }
}

void sendDataToServer() {
  // if you're not connected, and ten seconds have passed since
  // your last connection, then connect again and send data:
  if (!client.connected() && (millis() - lastConnectionTime > refreshRate)) {
    srPrint("Device Status: ");
    srPrintln(status);

    switch (status) {
      case 0:
        {
          transmitData("\"b\":0");
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
  } else {
    lastConnected = client.connected();
  }
}

void transmitData(char *data) {
  srPrint("Got data to send: ");
  srPrintln(data);
  if (strlen(data) < 1) {
    data = "\"x\":[]";
  }
  char jsonOut[500];
  sprintf(
    jsonOut,
    "{\"id\":%i,\"v\":\"%s\",\"st\":%i,\"m\":%i,%s}",
    deviceId,
    version,
    status,
    0,
    data
  );
  if (debug == true) {
    return;
  }
  srPrint("Sending to ");
  srPrint(server);
  srPrint(":");
  srPrintln(serverPort);

  if (client.connect(server, serverPort)) {
    srPrintln("Connected!");
    client.print("GET /api/aldc/");
    client.print(deviceId);
    client.println(" HTTP/1.1");
    client.print("Host: ");
    client.println(server);
    client.println("Connection: close");
    client.println();
    lastConnectionTime = millis();
  } else {
    Watchdog.enable(8000);
    srPrintln("Not connected, try to reconnect.");
    client.stop();
    Ethernet.begin(mac);
    Watchdog.disable();
  }
  srPrintln(jsonOut);
}

void reboot() {
  srPrintln("** Rebooting.");
  delay(500);
  asm volatile ("  jmp 0");
}

int isNumeric (const char * s)
{
  if (s == NULL || *s == '\0' || isspace(*s))
    return 0;
  char * p;
  strtod (s, &p);
  return *p == '\0';
}

int setPins(JsonArray& array, int pins[])
{
  int ii = 0;
  boolean ended = false;
  while (ended == false and ii < 50) {
    if (strlen(array[ii]) > 0) {
      const char* sensor = array[ii].asString();
      const char *sensorP = sensor;
      // Drop the A / D marker by moving the pointer forward one slot.
      if (!isDigit(sensorP[0])) {
        sensorP++;
        srPrint("Got a sensor!!!: ");
        srPrintln(sensorP);
        pins[ii] = atoi(sensorP);
      }
      ii++;
    } else {
      ended = true;
    }
  }
  pins[ii] = -1;
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
