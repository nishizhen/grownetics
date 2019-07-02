
void setup() {
Serial.begin(9600);
}

void loop()
{
  char* buf;
  buf = strdup("[1:2],[2:2],[3:10]");
  int i;
  char *p;
  char *pp;
  char *array[50];
  i = 0;

  while ((p = strsep(&buf, ",")) != NULL)
  {
    array[i++] = p;
    Serial.println(p);


    while ((pp = strsep(&p, "[:]")) != NULL)
    {
      Serial.print("Pin: ");
      Serial.println(pp);
    }


  }


  // while (p != NULL)
  // {
  //   array[i++] = p;
  //    strcpy(pp,p);
  //    Serial.print("pp: ");
  //    Serial.println(pp);
  //     // Serial.println(strtok(pp,"[:]"));
  //     // Serial.println(strtok(NULL,"[:]"));
  //   p = strtok (NULL, ",");
  // }
  Serial.println();
    Serial.println("Array:");
  for (i=0;i<3; ++i) 
    Serial.println(array[i]);

  Serial.println();
  delay(2000); 
}