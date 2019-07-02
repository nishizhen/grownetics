int heater = 2;
int sense = A0;

void setup() {
  Serial.begin(9600);
  pinMode(heater, OUTPUT);
  pinMode(sense, INPUT);
  digitalWrite(heater, HIGH);        // Heater on
  Serial.println("Purge started...");
  for(int i = 60; i > 0; i--) {         // Purge for 60 seconds
    Serial.println(i);
    delay(1000);
  }
  Serial.println("Purge completed...");

  Serial.println("Sensing...");

  for(int i = 0; i <= 90; i++) {     // Read for 90 seconds
    Serial.println(i, DEC);                   //  This code may be repeated
    Serial.println(digitalRead(sense));
    delay(1000);
  }
  Serial.println("Sense complete...");
}

void loop() {
  // Empty loop  
}