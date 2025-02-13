// ESSE CÓDIGO CONTÉM UM EXEMPLO QUE grava uma string na memória EPROM




#include <EEPROM.h>

void setup() {
  Serial.begin(9600);

  EEPROM.begin(512);                                // Inicializa o EEPROM com um tamanho específico (512 bytes é um bom tamanho para ESP8266)

  String dataToWrite = "Minha String de Teste";     // String para gravar na memória flash

  for (int i = 0; i < dataToWrite.length(); i++) {  // Gravar a string na EEPROM
    EEPROM.write(i, dataToWrite[i]);
  }

  EEPROM.write(dataToWrite.length(), '\0');         // Adiciona um terminador nulo para indicar o final da string

  EEPROM.commit();                                  // Salva os dados no EEPROM

  Serial.println("String gravada na memória flash!");
}


void loop() {
}
