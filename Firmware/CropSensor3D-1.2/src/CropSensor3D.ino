#include <ArduinoJson.h>
#include <Ethernet.h>
#include <OneWire.h>
#include <Wire.h>

#define SS 10U  //D10----- SS
#define RST 11U //D11----- Reset

#include "BlueDot_BME280.h"
BlueDot_BME280 bme280_1;

#include "SparkFun_SCD30_Arduino_Library.h" //Click here to get the library: http://librarymanager/All#SparkFun_SCD30
SCD30 airSensor;

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

int deviceId = 6;

// ======================================
// DO NOT CHANGE ANYTHING BELOW THIS LINE
// ======================================

char server[] = "onsite.grownetics.co";
int serverPort = 80;
char version[9] = "3D-1.2";

byte macs[] = {0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x10,
               0x11, 0x12, 0x13, 0x14, 0x15, 0x16, 0x17, 0x18, 0x19, 0x20,
               0x21, 0x22, 0x23, 0x24, 0x25, 0x26, 0x27, 0x28, 0x29, 0x30,
               0x31, 0x32, 0x33, 0x34, 0x35, 0x36, 0x37, 0x38, 0x39, 0x40,
               0x41, 0x42, 0x43, 0x44, 0x45, 0x46, 0x47, 0x48, 0x49, 0x50};

byte ip[] = {10, 5, 1, deviceId};

int deviceTier = (int)(deviceId / 50);
int deviceMod = deviceId % 50;
byte mac[] = {0x90, 0xA2, 0xDA, 0x0F, macs[deviceTier], macs[deviceMod]};

EthernetClient client;
int status = 0;

unsigned long lastConnectionTime = millis();
unsigned long lastReadTime = 0;
boolean lastConnected = false;
boolean readingJson = false;
int refreshRate = 1000;
char inData[400];
int stringPos = 0;
unsigned char inputCount = 0;

byte humidityData;

int outs[50];

char sensordata[30];            //A 30 byte character array to hold incoming data from the sensors
byte sensor_bytes_received = 0; //We need to know how many characters bytes have been received
int channel;                    //INT pointer for channel switching - 0-7 serial, 8-127 I2C addresses
byte i2c_response_code = 0;     //used to hold the I2C response code.
byte in_char = 0;               //used as a 1 byte buffer to store in bound bytes from an I2C stamp.

// Float to array. Pass it a char array to put the float conversion into.
char *ftoa(char *a, double f, int precision)
{
  long p[] = {0, 10, 100, 1000, 10000, 100000, 1000000, 10000000, 100000000};

  char *ret = a;
  long heiltal = (long)f;
  itoa(heiltal, a, 10);
  while (*a != '\0')
    a++;
  *a++ = '.';
  long desimal = abs((long)((f - heiltal) * p[precision]));
  itoa(desimal, a, 10);
  return ret;
}

#define TCAADDR 0x70
void tcaselect(uint8_t i)
{
  if (i > 7)
    return;

  Wire.beginTransmission(TCAADDR);
  Wire.write(1 << i);
  Wire.endTransmission();
}

void readAnalogPin(char *data, int pinNumber)
{
  char pinNumberBuf[4];
  itoa(pinNumber, pinNumberBuf, 10);

  strcat(data, "[A");
  strcat(data, pinNumberBuf);
  strcat(data, ":");
  char buffer[7];
  ftoa(buffer, analogRead(pinNumber), 2);
  strcat(data, buffer);
  strcat(data, "]");
}

void setup()
{
  delay(2000);
  Serial.begin(9600);
  delay(2000);
  Wire.begin();

  airSensor.begin();

  delay(2000);
  tcaselect(4);
  bme280_1.parameter.communication = 0;
  bme280_1.parameter.I2CAddress = 0x77;         //Choose I2C Address
  bme280_1.parameter.sensorMode = 0b11;         //Choose sensor mode
  bme280_1.parameter.IIRfilter = 0b100;         //Setup for IIR Filter
  bme280_1.parameter.humidOversampling = 0b101; //Setup Humidity Oversampling
  bme280_1.parameter.tempOversampling = 0b101;  //Setup Temperature Ovesampling
  bme280_1.parameter.pressOversampling = 0b101; //Setup Pressure Oversampling
  if (bme280_1.init() != 0x60)
  {
    Serial.print(F("BME280 Nr.1 detected?\t"));
    Serial.println(F("No"));
  }
  else
  {
    Serial.print(F("BME280 Nr.1 detected?\t"));
    Serial.println(F("Yes"));
  }
}

void loop()
{
  sendDataToServer();
  delay(1000);
}

void sendDataToServer()
{
  // if you're not connected, and ten seconds have passed since
  // your last connection, then connect again and send data:
  if (!client.connected() && (millis() - lastConnectionTime > refreshRate))
  {
    char data[400] = "";
    Serial.println("Get data");

    tcaselect(4);

    // Temp
    strcat(data, "[THT:");
    char buffer[7];
    ftoa(buffer, bme280_1.readTempC(), 2);
    strcat(data, buffer);

    // Humidity
    strcat(data, "],[THH:");
    ftoa(buffer, bme280_1.readHumidity(), 2);
    strcat(data, buffer);

    // Pressure
    strcat(data, "],[THP:");
    ftoa(buffer, bme280_1.readPressure(), 2);
    strcat(data, buffer);

    strcat(data, "]");

    tcaselect(2);
    delay(500);

    // #https://github.com/sparkfun/SparkFun_SCD30_Arduino_Library/blob/master/examples/Example2_SetOptions/Example2_SetOptions.ino
    airSensor.setAltitudeCompensation(30);
    airSensor.setAmbientPressure(bme280_1.readPressure());

    if (airSensor.dataAvailable())
    {
      if (strlen(data) != 0)
      {
        strcat(data, ",");
      }

      strcat(data, "[CC:");
      ftoa(buffer, airSensor.getCO2(), 2);
      strcat(data, buffer);

      strcat(data, "],[CT:");
      ftoa(buffer, airSensor.getTemperature(), 2);
      strcat(data, buffer);

      strcat(data, "],[CH:");
      ftoa(buffer, airSensor.getHumidity(), 2);
      strcat(data, buffer);

      strcat(data, "]");

      Serial.println();
    }

    // PAR
    if (strlen(data) != 0)
    {
      strcat(data, ",");
    }
    int pinNumber = 0;
    readAnalogPin(data, pinNumber);

    if (strlen(data) != 0)
    {
      strcat(data, ",");
    }
    pinNumber = 1;
    readAnalogPin(data, pinNumber);

    if (strlen(data) != 0)
    {
      strcat(data, ",");
    }
    pinNumber = 2;
    readAnalogPin(data, pinNumber);

    if (strlen(data) != 0)
    {
      strcat(data, ",");
    }
    pinNumber = 3;
    readAnalogPin(data, pinNumber);

    Serial.println(data);
    transmitData(data);
  }
  // store the state of the connection for next time through the loop:
  lastConnected = client.connected();
}

void transmitData(char *data)
{
  if (strlen(data) < 1)
  {
    data = "\"x\":[]";
  }
  char jsonOut[300];
  sprintf(
      jsonOut,
      "{\"id\":%i,\"v\":\"%s\",\"st\":%i,\"d\":\"%s\"}",
      deviceId,
      version,
      status,
      data);
  Serial.println(jsonOut);
  if (client.connect(server, serverPort))
  {
    client.print("GET /api/raw?q=");
    client.print(jsonOut);
    client.println(" HTTP/1.1");
    client.print("Host: ");
    client.println(server);
    client.println("Connection: close");
    client.println();
    lastConnectionTime = millis();
    Serial.println("Sent");
  }
  else
  {
    Serial.println("Failed.");
  }
  delay(1000);
  client.stop();
  Ethernet.begin(mac);
}
