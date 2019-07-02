	int ii = 7;
	void setup () {
		  pinMode(0, OUTPUT);
		  pinMode(1, OUTPUT);
		  pinMode(2, OUTPUT);
		  pinMode(3, OUTPUT);
		  pinMode(4, OUTPUT);
		  pinMode(5, OUTPUT);
		  pinMode(6, OUTPUT);
		  pinMode(7, OUTPUT);
		  pinMode(8, OUTPUT);
		  pinMode(9, OUTPUT);
		  pinMode(10, OUTPUT);
		  pinMode(11, OUTPUT);
		  pinMode(12, OUTPUT);
		  pinMode(13, OUTPUT);
		  pinMode(14, OUTPUT);
		  pinMode(15, OUTPUT);

	}
	void loop() {
	  delay(45000);
	   digitalWrite(ii, LOW);
	   ii++;
	   digitalWrite(ii, HIGH);
	   if (ii > 15) {
	        digitalWrite(ii, LOW);
	     ii = 8;
	   }
	}