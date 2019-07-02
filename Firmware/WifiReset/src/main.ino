#include <Arduino.h>
#include <ESP8266WiFi.h>
#include <ESP8266WiFiMulti.h>
#include <ESP8266HTTPClient.h>

#define USE_SERIAL Serial

ESP8266WiFiMulti WiFiMulti;

int id = 1;  // This needs to match the Arduino's deviceId.
int ledPin = 5;
String server = "http://onsite.grownetics.co:8500/v1/kv/devices/";

void setup() {
  delay(2000);
  pinMode(2, OUTPUT);
  pinMode(ledPin, OUTPUT);
  digitalWrite(2, HIGH);
  digitalWrite(ledPin, HIGH);
  USE_SERIAL.begin(115200);

  USE_SERIAL.println();
  USE_SERIAL.println();
  USE_SERIAL.println();

  for(uint8_t t = 4; t > 0; t--) {
      USE_SERIAL.printf("[SETUP] WAIT %d...\n", t);
      USE_SERIAL.flush();
      delay(1000);
  }

  WiFiMulti.addAP("GrownInternal", "GrowBetter16"); // This needs to match the AP crendtials connected to the server.
}

void loop() {
    // wait for WiFi connection
    if (WiFiMulti.run() == WL_CONNECTED) {

        HTTPClient http;
        http.setReuse(true);
        USE_SERIAL.print("[HTTP] begin...\n");
        String rebootURL = server + id + "/reboot?raw";
        http.begin(rebootURL); // HTTP
        USE_SERIAL.print("[HTTP] GET...\n");
        // start connection and send HTTP header
        int httpCode = http.GET();
        
        // httpCode will be negative on error
        if (httpCode > 0) {
            // HTTP header has been send and Server response header has been handled
            USE_SERIAL.printf("[HTTP] GET... code: %d\n", httpCode);

            // file found at server
            if (httpCode == HTTP_CODE_OK) {
                String payload = http.getString();
                http.end();
                USE_SERIAL.println(payload);
                if (payload.equals("1")) {
                  USE_SERIAL.println("Reboot!");
                  digitalWrite(2, LOW);
                  digitalWrite(ledPin, LOW);
                  delay(1000);
                  digitalWrite(2, HIGH);
                  digitalWrite(ledPin, HIGH);
                }
            }
        } else {
          http.end();
          USE_SERIAL.printf("[HTTP] GET... failed, error: %s\n", http.errorToString(httpCode).c_str());
          ESP.restart();
        }

    } else {
      USE_SERIAL.println("Not connected");
    }
    USE_SERIAL.println("Done checking");
    delay(3000); //Every 30 seconds check whether the device's status = Rebooting.
    USE_SERIAL.println("Loop");
}
