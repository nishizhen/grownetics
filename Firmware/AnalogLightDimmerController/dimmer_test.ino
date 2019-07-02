int dimmerPin = 9;    // LED connected to digital pin 9

void setup() {
  // nothing happens in setup
}

void loop() {
  // fade in from min to max in increments of 5 points:
  for (int fadeValue = 0; fadeValue <= 255; fadeValue += 1) {
    // sets the value (range   from 0 to 255):
    analogWrite(dimmerPin, fadeValue);
    // wait for 30 milliseconds to see the dimming effect
    delay(10);
  }



  // fade out from max to min in increments of 5 points:
  for (int fadeValue = 255 ; fadeValue >= 0; fadeValue -= 1) {
    // sets the value (range from 0 to 255):
    analogWrite(dimmerPin, fadeValue);
    // wait for 30 milliseconds to see the dimming effect
    delay(10);
 }
 delay(5000);
}