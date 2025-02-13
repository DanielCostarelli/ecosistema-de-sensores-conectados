// ESSE CÓDIGO CONTÉM UM EXEMPLO QUE tenta se conectar a pelo menos 3 redes wifi diferentes (tem a rede principal e 2 redes wifi reserva)






#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <Arduino.h>

const char* ssid_1 = "VALMOR ANGELIM";           // Rede Wifi 1
const char* password_1 = "Sorriso@2021";

const char* ssid_2 = "VALMOR ANGELIM_EXT";       // Rede Wifi 2 substituta
const char* password_2 = "Sorriso@2021";

const char* ssid_3 = "Daniel m62";               // Rede Wifi 3 substituta
const char* password_3 = "jczjczjcz";

WiFiClient client;  // Criando um objeto WiFiClient





void setup() {
  Serial.begin(9600);
  conecta_ao_wifi();
}





void loop() {
  if (WiFi.status() != WL_CONNECTED) {
    conecta_ao_wifi();
  }

  // Código principal do loop
  delay(10000);
}




void conecta_ao_wifi() {
  WiFi.disconnect(true);           // Forçar a desconexão
  delay(1000);                     // Aguardar antes de tentar reconectar



  Serial.println("Tentando conectar ao WiFi 1...");
  WiFi.begin(ssid_1, password_1);

  int tentativas = 0;
  while (WiFi.status() != WL_CONNECTED && tentativas < 10) {
    delay(1000);
    Serial.print(".");
    tentativas++;
  }

  if (WiFi.status() == WL_CONNECTED) {
    Serial.println("\nConectado ao WiFi 1");
    return;                        // Sai da função se conectado
  }



  // Tentar conectar ao WiFi 2
  Serial.println("\nFalha ao conectar ao WiFi 1. Tentando conectar ao WiFi 2...");
  WiFi.begin(ssid_2, password_2);

  tentativas = 0;
  while (WiFi.status() != WL_CONNECTED && tentativas < 10) {
    delay(1000);
    Serial.print(".");
    tentativas++;
  }

  if (WiFi.status() == WL_CONNECTED) {
    Serial.println("\nConectado ao WiFi 2");
    return;                        // Sai da função se conectado
  }



  // Tentar conectar ao WiFi 3
  Serial.println("\nFalha ao conectar ao WiFi 2. Tentando conectar ao WiFi 3...");
  WiFi.begin(ssid_3, password_3);

  tentativas = 0;
  while (WiFi.status() != WL_CONNECTED && tentativas < 10) {
    delay(1000);
    Serial.print(".");
    tentativas++;
  }

  if (WiFi.status() == WL_CONNECTED) {
    Serial.println("\nConectado ao WiFi 3");
  } else {
    Serial.println("\nFalha ao conectar a qualquer rede WiFi!");
  }
}

