#include <ArduinoJson.h>
#include <Ethernet.h>
#include <SPI.h>
#include <OneWire.h>

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

int id = 2;

byte mac[] = { 0x90, 0xA2, 0xDA, 0x0F, 0xB5, 0x88 };

// ======================================
// DO NOT CHANGE ANYTHING BELOW THIS LINE
// ======================================

EthernetClient client;
int status = 1;
IPAddress deviceIp(192,168,0,176+id);
IPAddress serverIp(192,168,0,13);
unsigned long lastConnectionTime = 0;
boolean lastConnected = false;
boolean readingJson = false;
int refreshRate = 5000;
char inData[32];
char inData2[32];
int stringPos = 0;
unsigned char inputCount = 0;

char* type1pins = "2,4";

int t1[10];

void setup() { 
  Serial.begin(9600);
  delay(1000);
  Ethernet.begin(mac,deviceIp);
  delay(1000);
}

void loop(){
  if (client.available()) {
    
    char inChar = client.read();
    
    if(inChar == '{') {
        stringPos = 0;
        memset( &inData, 0, 32 );
        memset( &inData2, 0, 32 );
        readingJson = true;
    }

    if(readingJson) {
         if(stringPos < 254) 
          {
            inData[stringPos] = inChar; // Store it
            stringPos++; 
          }
          if(inChar == '}') {
            readingJson = false;
            // inData[stringPos] = '\0';

            StaticJsonBuffer<200> jsonBuffer;
            JsonObject& root = jsonBuffer.parseObject(inData);
            
            if (root["boot"]==1) {
              // We're booting, set up inputs and outputs
              status = 1;
              inputCount = root["ins"][0];
              // inputCount = 9;


              // char data[100];
              // sprintf(data,"\"type\":\"%s\"", ins->type);
              // foreach (sensors as sensor) {
              //   data[] = sensor.read();
              // }
              // dataFromSerevr = sendData(data);
              // foreach (dataFromServer.outputs as output) {
              //   arduino.pin(output.pinNumber,output.pinValue);
              // }
            }

            
          }
      }
  }

  // if there's no net connection, but there was one last time
  // through the loop, then stop the client:
  if (!client.connected() && lastConnected) {
    client.stop();
  }

  // if you're not connected, and ten seconds have passed since
  // your last connection, then connect again and send data:
  if(!client.connected() && (millis() - lastConnectionTime > refreshRate)) {
    switch (status) {
      case 0:
      {
        // Try and connect to the server
        sendData("\"booted\":0");
        break;
      }
      case 1:
      {
        char pinData[42] = "";
        int inputCount = 0;
        // for (int point = 0; inputArray[point] > -1; point++) {
        //   int inputPin = inputArray[point];
        //   long inputValue = analogRead(0);
        //   sprintf(pinData,"%s{%d:%f},",pinData,inputPin,inputValue);
        // }

        // char inputValue1[10];
        // getWaterTemp(inputValue1,2);
        // int inputValue2 = analogRead(1);
        // int inputValue3 = analogRead(2);
        // int inputValue4 = analogRead(3);
        // int inputValue5 = analogRead(4);
        char data[40] = "";

        char *pin;
        char type1pinsBuf[50];
        strcpy(type1pinsBuf,type1pins);
        pin = strtok (type1pinsBuf,",");
        while (pin != NULL)
        {
          char temp[10];
          getWaterTemp(temp,pin);
          Serial.print(pin);
          Serial.print(" - ");
          Serial.print(temp);
          if (strlen(data)!=0) {
            strcat(data,",");
          }
          strcat(data,temp);
          pin = strtok (NULL, ",");
        }

        sprintf(pinData,"\"data\":[%s]",
          data
        );
        
        sendData(pinData);
        break;
      }
    }
  }
  // store the state of the connection for next time through the loop:
  lastConnected = client.connected();
  delay(1000);
}

void sendData(char *data) {
  char jsonOut[100];
  sprintf(
    jsonOut,
    "{\"id\":%i,\"st\":%i,%s}",
    id,
    status,
    data
  );
  if (client.connect(serverIp, 80)) {
    client.print("GET /api/raw?q=");
    client.print(jsonOut);
    client.println(" HTTP/1.1");
    client.print("Host: ");
    client.println(serverIp);
    client.println("Connection: close");
    client.println();
    lastConnectionTime = millis();
  } else {
    client.stop();
  }
  Serial.println(jsonOut);
}

char *getWaterTemp(char *b, char *pin){
 //returns the temperature from one DS18S20 in DEG Celsius
 Serial.print("Getting temp of pin: ");
 Serial.println(pin);
 byte data[12];
 byte addr[8];
 OneWire ds(atoi(pin));
 if ( !ds.search(addr)) {
   //no more sensors on chain, reset search
   ds.reset_search();
   return 0;
 }

 if ( OneWire::crc8( addr, 7) != addr[7]) {
   Serial.println("CRC is not valid!");
   return 0;
 }

 if ( addr[0] != 0x10 && addr[0] != 0x28) {
   Serial.print("Device is not recognized");
   return 0;
 }

 ds.reset();
 ds.select(addr);
 ds.write(0x44,1); // start conversion, with parasite power on at the end

 byte present = ds.reset();
 ds.select(addr);  
 ds.write(0xBE); // Read Scratchpad

 
 for (int i = 0; i < 9; i++) { // we need 9 bytes
  data[i] = ds.read();
 }
 
 ds.reset_search();
 
 byte MSB = data[1];
 byte LSB = data[0];

 float tempRead = ((MSB << 8) | LSB); //using two's compliment
 float TemperatureSum = tempRead / 16;
 Serial.println(TemperatureSum);
 char *buffer = b;
 ftoa(buffer,TemperatureSum,2);
 return buffer;
 
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
