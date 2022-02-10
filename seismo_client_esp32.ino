#include <WiFi.h>
#include <HTTPClient.h>
#include <Arduino_JSON.h>
int Buzzer = 18;
int webdata;
const char* ssid = "WIFI-NET2";
const char* password = "584370911war";

unsigned long last_time = 0;
unsigned long timer_delay = 10000;

String json_array;

void setup() {
  //Пищим что включились
  pinMode (Buzzer, OUTPUT);
  digitalWrite (Buzzer, HIGH);
  delay(100);
  digitalWrite (Buzzer, LOW);
  delay(100);
  
  Serial.begin(115200);
  WiFi.begin(ssid, password);
  Serial.println("Connecting to WIFI...");
  while(WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("");
  Serial.print("IP Address: ");
  Serial.println(WiFi.localIP());
  //Пищим что подключились к сети
  digitalWrite (Buzzer, HIGH);
  delay(100);
  digitalWrite (Buzzer, LOW);
  delay(100);
  digitalWrite (Buzzer, HIGH);
  delay(100);
  digitalWrite (Buzzer, LOW);
  delay(100);
  
  Serial.println("First set of readings will appear after 10 seconds");
}

void loop() {
   
    if(WiFi.status()== WL_CONNECTED){
      String server = "https://sbs.h1n.ru/seism/json.php";
      
      json_array = GET_Request(server.c_str());
      JSONVar my_obj = JSON.parse(json_array);
  
      if (JSON.typeof(my_obj) == "undefined") {
        Serial.println("Parsing input failed!");
        return;
      }
      webdata = (my_obj["EAQ"]);
      Serial.println(webdata);
      
    }
    else {
      Serial.println("WiFi Disconnected");
      //Писк бубубу
    }
    last_time = millis();


    if (webdata == 1){
    Serial.println("Warning!!!");
    //Пищим что трясет
      digitalWrite (Buzzer, HIGH);
      delay(300);
      digitalWrite (Buzzer, LOW);
      delay(300);
      digitalWrite (Buzzer, HIGH);
      delay(300);
      digitalWrite (Buzzer, LOW);
      delay(300);
      digitalWrite (Buzzer, HIGH);
      delay(300);
      digitalWrite (Buzzer, LOW);
      delay(300);
      digitalWrite (Buzzer, HIGH);
      delay(300);
      digitalWrite (Buzzer, LOW);
      delay(300);
    }else{
    Serial.println("All OK");
    }
    
    delay(500);

}

String GET_Request(const char* server) {
  HTTPClient http;    
  http.begin(server);
  int httpResponseCode = http.GET();
 
  String payload = "{}"; 
 
  if (httpResponseCode>0) {
    Serial.print("HTTP Response code: ");
    Serial.println(httpResponseCode);
    payload = http.getString();
  }
  else {
    Serial.print("Error code: ");
    Serial.println(httpResponseCode);
  }

  http.end();

  return payload;
}
