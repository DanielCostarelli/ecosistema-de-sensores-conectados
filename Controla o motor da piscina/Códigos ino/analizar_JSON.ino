// ESSE CÓDIGO CONTÉM UM EXEMPLO QUE recebe uma variável JSON e dá o valor de suas chaves a variaveis




#include <ArduinoJson.h>

void setup() {
  Serial.begin(9600);
}

void loop() {
  // Código principal
  Serial.println();
  Serial.println();


  // Simulação de uma string JSON
  const char* jsonString = R"({
    "hora": 13,
    "minuto": 32,
    "segundo": 15,
    "inputs": [0,0,0,0,1,1,1,1,0,0,0,0,0,0,0,0,1,1,1,1,0,0,0,0,0,0,0,0,1,1,1,1,0,0,0,0,0,0,0,0,1,1,1,1,0,0,0,0,0,0,0,0,1,1,1,1,0,0,0,0,0,0,0,0,1,1,1,1,0,0,0,0,0,0,0,0,1,1,1,1,0,0,0,0,0,0,0,0,1,1,1,1,0,0,0,0]
  })";

  DynamicJsonDocument doc(1024);

  // Desserializar a string JSON
  DeserializationError error = deserializeJson(doc, jsonString);

  // Verificar se houve erro na desserialização
  if (error) {
    Serial.print(F("Erro na desserialização: "));
    Serial.println(error.c_str());
    return;
  }

  int hora = doc["hora"];
  int minuto = doc["minuto"];
  int segundo = doc["segundo"];
  Serial.print("Hora: ");
  Serial.println(hora);
  Serial.print("Minuto: ");
  Serial.println(minuto);
  Serial.print("Segundo: ");
  Serial.println(segundo);

  // Acessar o array "inputs"
  JsonArray inputs = doc["inputs"];
  Serial.print("Inputs: ");
  for (int value : inputs) {
    Serial.print(value);
  }
  Serial.println();







  String jsonString_montada = "{\n\"hora\": " + String(hora) + ",\n" + "\"minuto\": " + String(minuto) + ",\n" + "\"segundo\": " + String(segundo) + ",\n" + "\"inputs\": [";
  for (int i = 0; i < 96; i++) {  // Adiciona os 96 elementos do array inputs ao JSON
    jsonString_montada += String(inputs[i]);
    if (i < 95) {  // Adiciona vírgula para separar os elementos, exceto no último
      jsonString_montada += ",";
    }
  }
  jsonString_montada += "]\n}";


  Serial.println("String: ");
  Serial.println(jsonString_montada);


  delay(10000);
}
