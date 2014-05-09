 /*
  * Progetto Meteo Arduino
  *
  * Autore: Alfieri Mauro
  * Twitter: @mauroalfieri
  *
  * Tutorial su: http://www.mauroalfieri.it/elettronica/centralina-meteo-arduino-yun.html
  *
  */

#include <FileIO.h>
#include <Wire.h>
#include <dht11.h>
#include <SHT1x.h>

#define DHT11_PIN 10
#define SHT_dataPin 12
#define SHT_clockPin 11

dht11 DHT;
SHT1x sht1x(SHT_dataPin, SHT_clockPin);

int  DHT_h;
int  DHT_t;
char SHT_t[10];
char SHT_h[10];

String cur = "";
String old = "";

void setup() {
  Bridge.begin();
  FileSystem.begin();
}

void loop () {
  readSensor();
  int chk = DHT.read(DHT11_PIN);
  
  String dataString;
  dataString += getTimeStamp();
  dataString += " ";
  dataString += String( DHT_h );
  dataString += " ";
  dataString += String( DHT_t );
  dataString += " ";
  dataString += String( SHT_h );
  dataString += " ";
  dataString += String( SHT_t );

  cur = getInterval("+%M");
  
  if ( cur != old ) {
    old = cur;
    File dataFile = FileSystem.open("/mnt/sd/arduino/www/dati.log", FILE_APPEND);
    if (dataFile) {
      dataFile.println(dataString);
      dataFile.close();
    }
  } 
  delay(30000);
}

String getTimeStamp() {
  String result;
  Process time;
  time.begin("date");
  time.addParameter("+%d/%m/%Y %T");
  time.run();
  while(time.available()>0) {
    char c = time.read();
    if(c != '\n') result += c;
  }
  
  return result;
}

String getInterval( char* string ) {
  String result;
  Process time;
  time.begin("date");
  time.addParameter(string);
  time.run();
  while(time.available()>0) {
    char c = time.read();
    if(c != '\n') result += c;
  }
  
  return result;
}

void readSensor() {
   int chk = DHT.read(DHT11_PIN);
   if ( chk != DHTLIB_OK ) {
     switch (chk){
         case DHTLIB_OK:
           DHT_h=0;
           DHT_t=0;
           break;
         case DHTLIB_ERROR_CHECKSUM: 
           DHT_h=997;
           DHT_t=997;
           break;
         case DHTLIB_ERROR_TIMEOUT: 
           DHT_h=998;
           DHT_t=998;
           break;
         default: 
           DHT_h=999;
           DHT_t=999;
           break;
      }
    }
    DHT_h=DHT.humidity;
    DHT_t=DHT.temperature;
    dtostrf(sht1x.readHumidity(), 5, 2, SHT_h);
    dtostrf(sht1x.readTemperatureC(), 5, 2, SHT_t);
}

