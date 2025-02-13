// ESSE CÓDIGO CONTÉM UM EXEMPLO QUE lê a memória EPROM e dá o valor a uma variavel




#include <EEPROM.h>

void setup() {
  Serial.begin(9600);

  EEPROM.begin(512);                  // Inicializa o EEPROM com o mesmo tamanho usado para gravar
  
  char dataFromEEPROM[512];           // Buffer para armazenar a string lida
  
  int i = 0;
  while (true) {                      // Ler a string da EEPROM
    char c = EEPROM.read(i);
    if (c == '\0') break;             // Parar ao encontrar o terminador nulo
    dataFromEEPROM[i] = c;
    i++;
  }
  dataFromEEPROM[i] = '\0';           // Garante que a string termine corretamente

  Serial.print("String lida da memória flash: ");
  Serial.println(dataFromEEPROM);
}

void loop() {
}
