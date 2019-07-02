  


//This code has intentionally has been written to be overly lengthy and includes unnecessary steps. 
//Many parts of this code can be truncated. This code was written to be easy to understand.
//Code efficiency was not considered. Modify this code as you see fit.    
//This code will output data to the Arduino serial monitor. Type commands into the Arduino serial monitor to control the EC circuit.


//As of 11/6/14 the default baud rate has changed to 9600.
//The old defaul baud rate was 38400.



#include <SoftwareSerial.h>      //we have to include the SoftwareSerial library, or else we can't use it.  
#define rx 2                     //define what pin rx is going to be.
#define tx 3                     //define what pin Tx is going to be.

SoftwareSerial myserial(rx, tx); //define how the soft serial port is going to work. 
SoftwareSerial myserial1(0, 1);


char EC_data[48];                  //we make a 20 byte character array to hold incoming data from the EC. 
char computerdata[20];             //we make a 20 byte character array to hold incoming data from a pc/mac/other. 
byte received_from_computer=0;     //we need to know how many characters have been received.                                 
byte received_from_sensor=0;       //we need to know how many characters have been received.
byte string_received=0;            //used to identify when we have received a string from the EC circuit.


float EC_float=0;                  //used to hold a floating point number that is the EC.
float TDS_float ;                  //used to hold a floating point number that is the TDS.
float SAL__float;                  //used to hold a floating point number that is the Salinity.
float GRAV__float;                 //used to hold a floating point number that is the Specific gravity.


char *EC;                          //char pointer used in string parsing 
char *TDS;                         //char pointer used in string parsing
char *SAL;                         //char pointer used in string parsing
char *GRAV;                        //char pointer used in string parsing



// change this to whatever pin you&#39;ve moved the jumper to
int ph_pin = 5;
//int for the averaged reading
int reading;
//int for conversion to millivolts
int millivolts;
//float for the ph value
float ph_value;
int i;
// highly recommended that you hook everything up and check the arduino&#39;s voltage with a
//multimeter.
// It doesn&#39;t make that much of a difference, but
// if you want it to be highly accurate than do this step
#define ARDUINO_VOLTAGE 5.0
// PH_GAIN is (4000mv / (59.2 * 7)) // 4000mv is max output and 59.2 * 7 is the maximum range (in
//millivolts) for the ph probe.
#define PH_GAIN 9.6525


void setup(){
     Serial.begin(9600);          //enable the hardware serial port
     myserial.begin(9600);        //enable the software serial port
      }
  
 
 
 void serialEvent(){               //this interrupt will trigger when the data coming from the serial monitor(pc/mac/other) is received.    
           received_from_computer=Serial.readBytesUntil(13,computerdata,20); //we read the data sent from the serial monitor(pc/mac/other) until we see a <CR>. We also count how many characters have been received.      
           computerdata[received_from_computer]=0; //we add a 0 to the spot in the array just after the last character we received. This will stop us from transmitting incorrect data that may have been left in the buffer. 
           myserial.print(computerdata);           //we transmit the data received from the serial monitor(pc/mac/other) through the soft serial port to the EC Circuit. 
           myserial.print('\r');                   //all data sent to the EC Circuit must end with a <CR>.  
          }    
        
 
 
  

void loop(){ 
    
  // if(myserial.available() > 0){        //if we see that the EC Circuit has sent a character.
  //    received_from_sensor=myserial.readBytesUntil(13,EC_data,48); //we read the data sent from EC Circuit until we see a <CR>. We also count how many character have been received.  
  //    EC_data[received_from_sensor]=0;  //we add a 0 to the spot in the array just after the last character we received. This will stop us from transmitting incorrect data that may have been left in the buffer. 
     
  //    if((EC_data[0] >= 48) && (EC_data[0] <=57)){   //if ec_data[0] is a digit and not a letter
  //       pars_data();
  //       }
  //    else
  //      Serial.println(EC_data);            //if the data from the EC circuit does not start with a number transmit that data to the serial monitor.
  // }    
  if(myserial1.available() > 0){        //if we see that the EC Circuit has sent a character.
     received_from_sensor=myserial1.readBytesUntil(13,EC_data,48); //we read the data sent from EC Circuit until we see a <CR>. We also count how many character have been received.  
     EC_data[received_from_sensor]=0;  //we add a 0 to the spot in the array just after the last character we received. This will stop us from transmitting incorrect data that may have been left in the buffer. 
     
     if((EC_data[0] >= 48) && (EC_data[0] <=57)){   //if ec_data[0] is a digit and not a letter
        pars_data();
        }
     else
       Serial.println(EC_data);            //if the data from the EC circuit does not start with a number transmit that data to the serial monitor.
  }    
 //take a sample of 50 readings
 reading = 0;
 for(i = 1; i < 50; i++) {
 reading += analogRead(ph_pin);
 delay(10);
 }
 //average it out
 reading /= i;
 //convert to millivolts. remember for higher accuracy measure your arduino&#39;s
 //voltage with a multimeter and change ARDUINO_VOLTAGE
 millivolts = ((reading * ARDUINO_VOLTAGE) / 1024) * 1000;
 ph_value = ((millivolts / PH_GAIN) / 59.2) + 7;
 Serial.print("pH= ");
 Serial.println(ph_value);
 delay(500);

}      
 
  void pars_data(){

        EC=strtok(EC_data, ",");                //let's pars the string at each comma.
        TDS=strtok(NULL, ",");                  //let's pars the string at each comma.
        SAL=strtok(NULL, ",");                  //let's pars the string at each comma.
        GRAV=strtok(NULL, ",");                 //let's pars the string at each comma.
        

        Serial.print("EC:");                //We now print each value we parsed sepratly. 
        Serial.println(EC);                 //this is the EC value. 
     
        Serial.print("TDS:");               //We now print each value we parsed sepratly. 
        Serial.println(TDS);                //this is the TDS value.
     
        Serial.print("SAL:");               //We now print each value we parsed sepratly. 
        Serial.println(SAL);                //this is the salinity value.
        
        Serial.print("GRAV:");              //We now print each value we parsed sepratly. 
        Serial.println(GRAV);               //this is the Specific gravity.
        Serial.println();                   //this just makes the output easyer to read. 
        }



//here are some functions you might find useful
//these functions are not enabled

/*

void ECFactoryDefault(){           //factory defaults the EC circuit
  myserial.print("X\r");}          //send the "X" command to factory reset the device 


void read_info(){                  //get device info
   myserial.print("I\r");}        //send the "I" command to query the information
     

void sleep(){
  myserial.print("sleep\r");}        //send the "sleep" command to put the EC circuit in a low power state  


void ECSetLEDs(byte enabled)      //turn the LEDs on or off
{
  if(enabled)                     //if enabled is > 0 
     myserial.print("L,1\r");      //the LED's will turn ON 
  else                            //if enabled is 0        
    myserial.print("L,0\r");      //the LED's will turn OFF
}
*/





   


  

