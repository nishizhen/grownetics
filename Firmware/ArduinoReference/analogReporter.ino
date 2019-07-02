// Led Pin, this is the onboard Arduino LED that flashes
int ledPin = 13;
// Light Pin, this is the light we're going to turn on when light is low
int lightPin = 12;
// How often the board should update
int delayTime = 1000;

String id = "0";

byte handshakeCompleted = 0;

// Setup the serial reading variables
String readMessage; // Allocate some space for the string
byte index = 0; // Index into array; where to store the character

void setup()
{
  Serial.begin(9600);

  pinMode(ledPin, OUTPUT);
  pinMode(lightPin, OUTPUT);

}
 
void loop()
{
  String readMessage = "";
  char character;

  while(Serial.available()) {
      character = Serial.read();
      readMessage.concat(character);
      delay(10);
  }

  if (handshakeCompleted == 1) {
    // Get the data
    float data0 = getVoltage(A0);
    float data1 = getVoltage(A1);
    float data2 = getVoltage(A2);
    // Convert to degrees
    data2 = (data2 - .5) * 100;

    // Send Data
    Serial.println(
      id+":"+
      getDataString("LS-1",data0)+","+
      getDataString("LT-1",data1)+","+
      getDataString("TM-1",data2)
    );
    
    // Flash the led flasher.
    delay(delayTime);
    digitalWrite(ledPin, HIGH);
    delay(delayTime);
    digitalWrite(ledPin, LOW);

    // Translate incoming messages
    if (readMessage.indexOf("l1 on") == 0) {
      digitalWrite(lightPin, HIGH);
    }
    if (readMessage.indexOf("l1 off") == 0) {
      digitalWrite(lightPin, LOW);
    }
  } else {
    // Perform Hanshake
    if (readMessage.indexOf("AR-") == 0) {
      Serial.println("OK");
      handshakeCompleted = 1;
      id = readMessage;
    } else if (readMessage.length()>0) {
      Serial.println("Err We Got "+readMessage);
    } else {
      Serial.println("?");
    }
    delay(delayTime);
  }
}

// Converts float to srting, and concatenates them into data format.
String getDataString(String name, float value) {
  char buffer[10];
  String valueString = name;
  dtostrf(value, 1, 2, buffer);
  valueString += ":";
  valueString += buffer;
  return valueString;
}

float getVoltage(int pin){
  int reading = 0;
  int sampleCount = 10;  // use lower number for more speed.  higher number for more filtering

  analogRead(pin); // this one gets thrown away
  for(int i = 0; i < sampleCount ; i++) {
    reading += analogRead(pin); // accumulate readings
    delay(1);
  }
  reading = reading / sampleCount ;  // calculate the average
  //converting from a 0 to 1023 digital range
  // to 0 to 5 volts (each 1 reading equals ~ 5 millivolts
  return (reading * .004882814);
}

