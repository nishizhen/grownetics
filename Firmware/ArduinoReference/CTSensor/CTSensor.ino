#include "EmonLib.h" // 
EnergyMonitor emon1;             // Create an instance 
void setup() 
 {
 Serial.begin(9600);   
 emon1.current(0, 29); // Current: input pin, calibration. Cur Const= Ratio/BurdenR. 1800/62 = 29. 
 } 
void loop() {   

emon1.current(0, 29);
double Irms = emon1.calcIrms(1480); // Calculate Irms only   
Serial.println(Irms); // Irms   
emon1.current(1, 29);
Irms = emon1.calcIrms(1480); // Calculate Irms only   
Serial.println(Irms); // Irms   
emon1.current(2, 29);
Irms = emon1.calcIrms(1480); // Calculate Irms only   
Serial.println(Irms); // Irms   
emon1.current(3, 29);
Irms = emon1.calcIrms(1480); // Calculate Irms only   
Serial.println(Irms); // Irms   
emon1.current(8, 29);
Irms = emon1.calcIrms(1480); // Calculate Irms only   
Serial.println(Irms); // Irms   
emon1.current(9, 29);
Irms = emon1.calcIrms(1480); // Calculate Irms only   
Serial.println(Irms); // Irms   
emon1.current(10, 29);
Irms = emon1.calcIrms(1480); // Calculate Irms only   
Serial.println(Irms); // Irms   
emon1.current(11, 29);
Irms = emon1.calcIrms(1480); // Calculate Irms only   
Serial.println(Irms); // Irms   
emon1.current(12, 29);
Irms = emon1.calcIrms(1480); // Calculate Irms only   
Serial.println(Irms); // Irms   
emon1.current(13, 29);
Irms = emon1.calcIrms(1480); // Calculate Irms only   
Serial.println(Irms); // Irms   
emon1.current(14, 29);
Irms = emon1.calcIrms(1480); // Calculate Irms only   
Serial.println(Irms); // Irms   
emon1.current(15, 29);
Irms = emon1.calcIrms(1480); // Calculate Irms only   
Serial.println(Irms); // Irms  
Serial.println();Serial.println(); 

delay(2000);
}