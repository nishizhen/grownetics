#include <SoftwareSerial.h>      //we have to include the SoftwareSerial library, or else we can't use it.  

// EC IS working.
// pH is NOT working
// DO IS working

// EC
char EC_data[48];                  //we make a 20 byte character array to hold incoming data from the EC. 
char computerdata[20];             //we make a 20 byte character array to hold incoming data from a pc/mac/other. 
byte received_from_computer=0;     //we need to know how many characters have been received.                                 
byte received_from_sensor=0;       //we need to know how many characters have been received.
byte string_received=0;            //used to identify when we have received a string from the EC circuit.
float EC_float=0;                  //used to hold a floating point number that is the EC.
char *EC;                          //char pointer used in string parsing 

// DO
char DO_data[20];                  //we make a 20 byte character array to hold incoming data from the D.O. 
char docomputerdata[20];             //we make a 20 byte character array to hold incoming data from a pc/mac/other. 
byte doreceived_from_computer=0;     //we need to know how many characters have been received.                                 
byte doreceived_from_sensor=0;       //we need to know how many characters have been received.
byte arduino_only=1;               //if you would like to operate the D.O. Circuit with the Arduino only and not use the serial monitor to send it commands set this to 1. The data will still come out on the serial monitor, so you can see it working.  
byte startup=0;                    //used to make sure the Arduino takes over control of the D.O. Circuit properly.
byte dostring_received=0;            //used to identify when we have received a string from the D.O. circuit.
float DO_float=0;                  //used to hold a floating point number that is the D.O. 
float sat_float=0;                 //used to hold a floating point number that is the percent saturation.
char *DO;                          //char pointer used in string parsing 
char *sat;                         //char pointer used in string parsing

// pH
char ph_data[20];                  //we make a 20 byte character array to hold incoming data from the pH. 
char phcomputerdata[20];             //we make a 20 byte character array to hold incoming data from a pc/mac/other. 
byte phreceived_from_computer=0;     //we need to know how many characters have been received.                                 
byte phreceived_from_sensor=0;       //we need to know how many characters have been received.
byte pharduino_only=0;               //if you would like to operate the pH Circuit with the Arduino only and not use the serial monitor to send it commands set this to 1. The data will still come out on the serial monitor, so you can see it working.  
byte phstartup=0;                    //used to make sure the Arduino takes over control of the pH Circuit properly.
float ph=0;                        //used to hold a floating point number that is the pH. 
byte phstring_received=0;            //used to identify when we have received a string from the pH circuit.
String sensorstring = "";
boolean sensor_stringcomplete = false; //have we received all the data from the Atlas 

void setup(){
  sensorstring.reserve(30);
  Serial.begin(9600);          //enable the hardware serial port
  Serial1.begin(38400);        //enable the software serial port
  Serial2.begin(9600);
  Serial3.begin(9600);

}

void serialEvent1(){ //if the hardware serial port_3 receives a char
  char inchar = (char)Serial1.read(); //get the char we just received
  sensorstring += inchar; //add it to the inputString
  if(inchar == '\r') {sensor_stringcomplete = true;} //if the incoming character is a , set the flag
}

  
void loop(){ 
  if(Serial3.available() > 0){        //if we see that the EC Circuit has sent a character.
     received_from_sensor=Serial3.readBytesUntil(13,EC_data,48); //we read the data sent from EC Circuit until we see a <CR>. We also count how many character have been received.  
     EC_data[received_from_sensor]=0;  //we add a 0 to the spot in the array just after the last character we received. This will stop us from transmitting incorrect data that may have been left in the buffer. 
     
     if((EC_data[0] >= 48) && (EC_data[0] <=57)){   //if ec_data[0] is a digit and not a letter
        pars_data();
        }
     else
       Serial.println(EC_data);            //if the data from the EC circuit does not start with a number transmit that data to the serial monitor.
  }    

  if(Serial2.available() > 0){         //if we see that the D.O. Circuit has sent a character.
     doreceived_from_sensor=Serial2.readBytesUntil(13,DO_data,20); //we read the data sent from D.O. Circuit until we see a <CR>. We also count how many character have been received.  
     DO_data[doreceived_from_sensor]=0;  //we add a 0 to the spot in the array just after the last character we received. This will stop us from transmitting incorrect data that may have been left in the buffer. 
     dostring_received=1;                //a flag used when the Arduino is controlling the D.O. Circuit to let us know that a complete string has been received.
     
     if((DO_data[0] >= 48) && (DO_data[0] <=57)){   //if DO_data[0] is a digit and not a letter
        dopars_data();
        }
     else
       Serial.println(DO_data);            //if the data from the D.O. circuit does not start with a number transmit that data to the serial monitor.
   }     

   if (sensor_stringcomplete){ //if a string from the Atlas Scientific product has been recived in its entierty
      Serial.print("ph: ");
      Serial.println(sensorstring); //send that string to to the PC's serial monitor
      sensorstring = ""; //clear the string:
      sensor_stringcomplete = false; //reset the flage used to tell if we have recived a completed string from the Atlas Scientific product
    }
}


 
void pars_data(){
  EC=strtok(EC_data, ",");                //let's pars the string at each comma.
  Serial.print("EC:");                //We now print each value we parsed sepratly. 
  Serial.println(EC);                 //this is the EC value. 
  Serial.println();                   //this just makes the output easyer to read. 
}



 void dopars_data()
{
  
  byte i;
  byte pars_flag=0;
  
  for(i=0;i<=received_from_sensor;i++)
      {
        if(DO_data[i]==','){pars_flag=1;}
      }
  

  
  if(pars_flag){ 
  
  DO=strtok(DO_data, ",");           //let's pars the string at each comma.
  sat=strtok(NULL, ",");             //let's pars the string at each comma.
  
 
  Serial.print("DO:");                //We now print each value we parsed sepratly. 
  Serial.println(DO);                 //this is the DO value. 
  //DO_float=atoi(DO);                //Uncomment this line to turn the string into to floating pint value. 
  
  
     
  Serial.print("%sat:");               //We now print each value we parsed sepratly. 
  Serial.println(sat);                 //this is the TDS value. 
  //sat_float=atoi(sat);               //Uncomment this line to turn the string into to floating pint value.
  }

  else                                //if the output is ony DO and not DO + % sat  
  {
   Serial.print("DO:");              //print out "DO:"  
   Serial.println(DO_data);          //printout that DO in Mg/L 
  }


} 

