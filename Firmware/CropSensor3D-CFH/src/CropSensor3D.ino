#include <Ethernet.h>
#include <OneWire.h>
#include <Wire.h>

#define SS 10U  //D10----- SS
#define RST 11U //D11----- Reset

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

// ======================================
// DO NOT CHANGE ANYTHING BELOW THIS LINE
// ======================================

char server[] = "192.168.50.22";
int serverPort = 81;
char version[9] = "3D-1.1.7";
char outputData[400] = "";

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
int refreshRate = 1000;

byte humidityData;

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
    {1, 1, 1, 1}  //channel 15
};

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
  pinMode(s0, OUTPUT);
  pinMode(s1, OUTPUT);
  pinMode(s2, OUTPUT);
  pinMode(s3, OUTPUT);
  digitalWrite(s0, LOW);
  digitalWrite(s1, LOW);
  digitalWrite(s2, LOW);
  digitalWrite(s3, LOW);
  Wire.begin();

  // There's no rush. We just started, and may be recovering from a power outage.
  // Give the network time to come back online.
  delay(500);

  Serial.begin(9600);

  delay(500);

  status = 1;

  memset(&pinsType2, 0, 10);
  memset(&pinsType4, 0, 2);
  memset(&pinsType11, 0, 10);

  pinsType2[0] = 1;
  pinsType2[1] = 2;
  pinsType2[2] = -1;

  pinsType4[0] = 3;
  pinsType4[1] = -1;

  pinsType11[0] = 0;
  pinsType11[1] = 1;
  pinsType11[2] = 2;
  pinsType11[3] = 3;
  pinsType11[4] = -1;
}

void loop()
{
  readDataFromServer();
  sendDataToServer();
}

void readDataFromServer()
{
  Ethernet.maintain();
  if (client.available() && (millis() - lastReadTime > refreshRate))
  {
    char inChar = client.read();
  } // Client available

  // if there's no net connection, but there was one last time
  // through the loop, then stop the client:
  if (!client.connected() && lastConnected)
  {
    client.stop();
  }
}

void sendDataToServer()
{
  // if you're not connected, and ten seconds have passed since
  // your last connection, then connect again and send data:
  if (!client.connected() && (millis() - lastConnectionTime > refreshRate))
  {

    char data[400] = "";
    Serial.println("Get data");
    // Humidity and air temp
    int ii = 0;
    while (pinsType2[ii] > -1)
    {
      if (strlen(data) != 0)
      {
        strcat(data, ",");
      }

      int pinNumber = pinsType2[ii];

      switchMux(pinNumber);

      unsigned int H_dat, T_dat;
      float RH, T_C;
      boolean gotHumidity = fetch_humidity_temperature(&H_dat, &T_dat, pinNumber);
      char pinNumberBuf[4];
      itoa(pinNumber, pinNumberBuf, 10);

      if (gotHumidity)
      {
        RH = (float)H_dat * 6.10e-3;
        T_C = (float)T_dat * 1.007e-2 - 40.0;
        strcat(data, "[M");
        strcat(data, pinNumberBuf);
        strcat(data, ":");
        char buffer[7];
        ftoa(buffer, RH, 2);
        strcat(data, buffer);
        strcat(data, "-");
        ftoa(buffer, T_C, 2);
        strcat(data, buffer);
        strcat(data, "]");
      }
      else
      {
        // We didn't get a valid reading, send back 0
        strcat(data, "[M");
        strcat(data, pinNumberBuf);
        strcat(data, ":0-0]");
      }
      ii++;
    }

    // Co2
    ii = 0;
    while (pinsType4[ii] > -1)
    {
      if (strlen(data) != 0)
      {
        strcat(data, ",");
      }
      int pinNumber = pinsType4[ii];
      switchMux(pinNumber);
      int co2Value = readCO2();
      char pinNumberBuf[4];
      itoa(pinNumber, pinNumberBuf, 10);
      char co2ValueBuf[4];
      itoa(co2Value, co2ValueBuf, 10);
      strcat(data, "[M");
      strcat(data, pinNumberBuf);
      strcat(data, ":");
      strcat(data, co2ValueBuf);
      strcat(data, "]");
      ii++;
    }

    // PAR
    ii = 0;
    while (pinsType11[ii] > -1)
    {
      if (strlen(data) != 0)
      {
        strcat(data, ",");
      }
      int pinNumber = pinsType11[ii];
      readAnalogPin(data, pinNumber);
      ii++;
    }

    Serial.println(data);

    sprintf(outputData, "\"d\":\"%s\"",
            data);
    transmitData(outputData);
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
  // char jsonOut[300];

  if (client.connect(server, serverPort))
  {
    Serial.println("Connected");
    client.print("GET /api/raw?q=");
    client.print(data);
    client.println(" HTTP/1.1");
    client.print("Host: ");
    client.println(server);
    client.println("Connection: close");
    client.println();
    lastConnectionTime = millis();
  }
  else
  {
    Serial.println("Couldn't connect.");
    client.stop();
    Ethernet.begin(mac);
  }
}

boolean fetch_humidity_temperature(unsigned int *p_H_dat, unsigned int *p_T_dat, int pin)
{

  byte address, Hum_H, Hum_L, Temp_H, Temp_L, _status;
  unsigned int H_dat, T_dat;
  address = 0x27;
  delay(100);
  Wire.beginTransmission(address);
  delay(100);
  if (Wire.endTransmission() == 0)
  {

    Wire.requestFrom((int)address, 0x03, (int)4);
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
    return (TRUE);
  } else {
    return (FALSE);
  }
}

int isNumeric(const char *s)
{
  if (s == NULL || *s == '\0' || isspace(*s))
    return 0;
  char *p;
  strtod(s, &p);
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

  if (Wire.endTransmission() == 0)
  {
    delay(10);
    Wire.requestFrom(0x68, 4);
    byte ii = 0;
    byte buffer[4] = {0, 0, 0, 0};
    delay(10);
    while (Wire.available() and ii < 500)
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

    if (sum == buffer[3])
    {
      return co2_value;
    }
    else
    {
      return 0;
    }
  }
}

int switchMux(int channel)
{
  channel--;
  //loop through the 4 sig
  for (int aa = 0; aa < 4; aa++)
  {
    digitalWrite(controlPin[aa], muxChannel[channel][aa]);
  }
  delay(50);
}
