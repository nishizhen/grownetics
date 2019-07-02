//Creator: Alex Rosa
//Date: 09/5/2018
//Company: Grownetics
//Last Modified 09/18/18

/* 
Description:  The purpose of this code is to convert existing ethernet Devices into wireless devices using the 
Arduino Mega boards with integrated esp8266 chips

Function: This is the esp8266 portion of code for middleware devices This code reads JSON objects outputted 
to 'serial3'on the arduino mega's.  Once the json object has been retrieved, it then sends it as a GET request
to the respective server.  The responce (payload) is then sent back to the arduino Mega
*/

#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>


//USER VARS - CHANGE ACCORDING TO SYSTEM
bool debug = false;
const char* ssid=      "Grownetics";
const char* password =  "GrowBetter16";
const char* Server = "http://proxmox1.onsite.cropcircle.io/api/raw?q=";


//DO NOT CHANGE ANYTHING BELOW THIS LINE!!!

//variables for GET request method
char buffer[500];
String response;

//timing variables
unsigned long previousMillis = 0;
const long interval = 5000;
const long refreshRate = 30000;

//variables for getData() 
char inData[500]; 
char outData[500];
char inChar;
int stringPos = 0;
bool readingJson;
int status = 0;

//Used for debugging
//char json1[] = "{\"boot\":1,\"outs\":[\"3\",\"2\", \"4\"],\"i9\":[\"A0\",\"A1\",\"A2\"]}";
char json1[] = "{\"boot\":1,\"i13\":[\"A0\",\"A1\",\"A2\",\"A3\",\"A4\",\"A5\",\"A6\",\"A7\"]}";

char json2[] = "{\"boot\":1,\"i2\":[\"M2\"],\"i4\":[\"M3\"]}";

void setup() {

  Serial.begin(9600); 

  pinMode(0, OUTPUT);
  digitalWrite(0,LOW);

  WiFi.begin(ssid,password);

  delay(1000);
  
  if(debug){

    //use for soil Moisture testing, untill i13 is added to the server
    Serial.println(json1);

  }
}

void loop() {
    // use this to keep track of time 
    // loop condition -> (currentMillis - previousMillis >= interval)
    unsigned long currentMillis = millis();

    //skip connecttion to server if in debug
    if (!debug){

        while (WiFi.status() != WL_CONNECTED) {

            delay(1000);

            Serial.println("Connecting..");
        }
    }    

    getData(); //parse JSON objects from Mega (bit by bit) 
}

void GETRequest(const char* theServer, const char* theObject){

    if (!debug){

        //Create object of class HTTPClient
        HTTPClient http;

        int n;

        n = sprintf(buffer,"%s%s",theServer,theObject);

        //Serial.println(buffer);

        //call the 'begin' method and pass through the URL
        http.begin(buffer);
        int httpCode = http.GET(); 

        String thePayload = http.getString();

        //comment this line out for soil moisture testing
        Serial.println(thePayload);

            http.end();

    }

    else{
        
        Serial.println("{}");
    }
}

void getData(){

  if (Serial.available() > 0){ 

    char inChar = Serial.read();

    if(inChar == '{') {

      Serial.println("incomming data...");

      digitalWrite(0, HIGH);

      stringPos = 0;
      memset( &inData, 0, 500 );
      readingJson = true;
      }

    if(readingJson) {
        
      if(stringPos < 500) {

        inData[stringPos] = inChar; // Store it
        stringPos++;
      } 

      if(inChar == '}') {

        readingJson = false;
        digitalWrite(0,LOW);

        Serial.println("Got JSON: ");
    
        GETRequest(Server,inData);
        }
      }
    } 
  }

