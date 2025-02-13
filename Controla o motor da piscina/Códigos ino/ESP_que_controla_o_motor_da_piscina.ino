//////////  CÓDIGO PRINCIPAL, QUE ESTÁ NA MEMÓRIA DO ESP
//////////  ROTEIRO
//
//
// importa as bibliotecas
// define as redes wifi
// define o link do servidor
//
// inicia as variaveis
//
// void setup {
//    inicia os relé desligados
//    inicia o espaço da memoria EPROM
// }
//
// void loop {
//    se não conectado à internet:
//        conecta-se a internet
//    prepara a url com o link do server e a temperatura e o status do relé
//    envia ao server
//    se recebeu uma resposta (está conectado à internet):
//        verifica se há erros na resposta
//        usa a resposta do servidor pra obter os valores de inputs, hora, minuto e segundos, do horário atuallizado
//        mostra no serial
//        monta um novo objeto JSON com os valores de inputs, hora, minuto e segundos
//        mostra ele no serial
//        armazena esse objeto na memória EPROM
//        com base nos inputs e na hora atual, controlar o estado do relé
//
//    se não conectado a internet:
//        lê a memória EPROM
//        verifica se há erros no objeto lido
//        usa o objeto lido pra obter os valores de inputs, hora, minuto e segundos
//        mostra no serial
//        incrementa 42 segundos na sua hora
//        monta um novo objeto JSON com os valores de inputs, hora, minuto e segundos
//        mostra ele no serial
//        armazena ela na memoria EPROM, sem alterar os inputs, somente a hora, minuto e segundos
//        com base nos inputs e na hora atual, controlar o estado do relé
//
//  aguarda 10 segundos
// }
//
//



#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <Arduino.h>
#include <EEPROM.h>
#include <ArduinoJson.h>


const char* ssid_1 = "VALMOR ANGELIM_EXT";  // Rede Wifi 1
const char* password_1 = "Sorriso@2021";

const char* ssid_2 = "VALMOR ANGELIM";  // Rede Wifi 2 substituta
const char* password_2 = "Sorriso@2021";

const char* ssid_3 = "Daniel m62";  // Rede Wifi 3 substituta
const char* password_3 = "jczjczjcz";

WiFiClient client;

const char* serverUrl = "http://monitoramento.x10.bz/rede_de_monitoramento_esp/controlador_motor_da_piscina/comunicacao_com_esp.php";   // Valor da URL
const char* keyValue = "1478621p";                                                                                                      // Valor da senha







float temperatura = 0.1;
JsonArray inputs;
int hora[3] = { 0, 0, 0 };
int _hora[3];
String horario;
String aviso;
String url;
String jsonString;
String jsonString_montada;
DynamicJsonDocument doc(512);
DeserializationError error;
bool estado_rele;
int cont = 0;







void setup() {
  Serial.begin(9600);
  EEPROM.begin(512);
  pinMode(D2, OUTPUT);
  pinMode(LED_BUILTIN, OUTPUT);
  digitalWrite(LED_BUILTIN, HIGH);  // desliga o led
}




void loop() {
  if (WiFi.status() != WL_CONNECTED) {
    conecta_ao_wifi();
  }

  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    http.end();                                   // Finaliza qualquer conexão anterior

    url = prepara_url();

    http.begin(client, url);                      // Usando o novo formato com WiFiClient

    int httpResponseCode = http.GET();            // Faz a requisição GET

    if (httpResponseCode > 0) {
      String response = http.getString();
      aviso = "Tudo certo.";
      conectado_a_internet(response);

    } else {
      Serial.print("Erro ao enviar requisição: ");
      Serial.println(httpResponseCode);
      desconectado_da_internet();

      switch (httpResponseCode) {
        case -1:
          aviso = "Falha de conexao WiFi ou servidor inalcancavel";
          ESP.restart();
          break;
        case -2:
          aviso = "Tempo limite na conexao";
          break;
        case -3:
          aviso = "Erro de memoria insuficiente";
          break;
        case -4:
          aviso = "Erro ao se conectar ao servidor";
          break;
        case -5:
          aviso = "Falha na escrita da solicitacao HTTP";
          break;
        default:
          aviso = "Erro desconhecido";
          break;
      }
      Serial.println(aviso);
    }

    http.end();  // Finaliza a conexão

  } else {
    aviso = "Desconectado do WiFi";
    Serial.println(aviso);
    Serial.println();
    desconectado_da_internet();
  }

  Serial.println();
  Serial.println();
  Serial.println();
  Serial.println("-------------------------------------------------------------------------------");
  Serial.println();
  Serial.println();
  delay(10000);  // Aguarda 10 segundos antes de enviar novamente
}





String prepara_url() {
  // Converte o estado_rele booleano para "true" ou "false" (não 0 ou 1)
  String estado_releStr = estado_rele ? "true" : "false";

  // Monta a string dos dados para enviar
  String dados = "[" + String(temperatura) + "," + estado_releStr + "]";

  String url_ = String(serverUrl) + "?key=" + dados + "//" + keyValue;
  Serial.println(url_);

  return url_;
}




void conectado_a_internet(String resposta) {
  // a resposta será nesse formato: Conteudo adicionado//{"hora": 13, "minuto": 32, "segundo": 15, "inputs": [0,0,0,0,0,1,1,1,1,0,0,0,0,0,0,0,0,0,0,0,1,1,1,1,0,0,0,0,...]} sendo 4 input por hora, cada input 15 minutos
  
  //////////////////////// recebe e filtra a resposta do server, para apenas o objeto desejado
  int pos = resposta.indexOf("//");
  String jsonStr = resposta.substring(pos + 2);



  //////////////////////// verifica se há erros nas chaves do objeto
  error = deserializeJson(doc, jsonStr);  // Desserializar a string JSON
  if (error) {                               // Verificar se houve erro na desserialização
    Serial.print(F("Erro na desserialização: "));
    Serial.println(error.c_str());
    desconectado_da_internet();  // se der erro.. vai com a lógica offline
  }

  if (doc.containsKey("hora") && doc.containsKey("minuto") && doc.containsKey("segundo") && doc.containsKey("inputs")) {  // Verificar se as chaves existem no objeto JSON
    JsonArray inputsArray = doc["inputs"].as<JsonArray>();
    if (inputsArray.size() == 96) {     // Verificar se o array 'inputs' tem 96 elementos



      //////////////////////// Se todas as condições forem verdadeiras, atribui os valores
      hora[0] = doc["hora"];
      hora[1] = doc["minuto"];
      hora[2] = doc["segundo"];
      inputs = inputsArray;


      //////////////////////// imprime no serial:
      Serial.println("Valores recebidos do servidor:");
      Serial.print("Hora: ");
      Serial.println(hora[0]);
      Serial.print("Minuto: ");
      Serial.println(hora[1]);
      Serial.print("Segundo: ");
      Serial.println(hora[2]);
      Serial.print("Inputs: ");
      for (int value : inputs) {
        Serial.print(value);
      }
      Serial.println();
      Serial.println();






      //////////////////////// monta a string novamente com os valores recebidos:
      jsonString_montada = "{\n\"hora\": " + String(hora[0]) + ",\n" + "\"minuto\": " + String(hora[1]) + ",\n" + "\"segundo\": " + String(hora[2]) + ",\n" + "\"inputs\": [";
      for (int i = 0; i < 96; i++) {      // Adiciona os 96 elementos do array inputs ao JSON
        jsonString_montada += String(inputs[i]);
        if (i < 95) {                     // Adiciona vírgula para separar os elementos, exceto no último
          jsonString_montada += ",";
        }
      }
      jsonString_montada += "]\n}";

      Serial.println("String que será armazenada:");
      Serial.println(jsonString_montada);
      Serial.println();


      //////////////////////// armazena elas na memória:
      for (int i = 0; i < jsonString_montada.length(); i++) {   // Gravar a string na EEPROM
        EEPROM.write(i, jsonString_montada[i]);
      }
      EEPROM.write(jsonString_montada.length(), '\0');          // Adiciona um terminador nulo para indicar o final da string
      EEPROM.commit();                                          // Salva os dados no EEPROM





      //////////////////////// controla o relé:
      int posicao = (hora[0] * 4) + (hora[1] / 15);     // Calculando a posição no array de 96 elementos
      controla_rele(inputs[posicao]);







    } else {    // Se o array 'inputs' não tiver 96 elementos, alerta no serial
      Serial.println("Erro: o array 'inputs' não tem 96 elementos.");
      desconectado_da_internet();  // se der erro.. vai com a lógica offline
    }

  } else {      // Se alguma chave não existir, alerta no serial
    Serial.println("Erro: falta uma ou mais chaves (hora, minuto, segundo, inputs).");
    desconectado_da_internet();  // se der erro.. vai com a lógica offline
  }
}




void desconectado_da_internet() {
  //////////////////////// lê a memória eprom:
  char dados_lidos[512];
  int i = 0;
  while (true) {            // Ler a string da EEPROM
    char c = EEPROM.read(i);
    if (c == '\0') break;   // Parar ao encontrar o terminador nulo
    dados_lidos[i] = c;
    i++;
  }
  dados_lidos[i] = '\0';    // Garante que a string termine corretamente




  //////////////////////// atribui a string recebida às variaveis e..:
  jsonString = dados_lidos;

  //////////////////////// verifica se há erros nas chaves do objeto
  error = deserializeJson(doc, jsonString);   // Desserializar a string JSON
  if (error) {                                // Verificar se houve erro na desserialização
    Serial.print(F("Erro na desserialização: "));
    Serial.println(error.c_str());
  }
  if (doc.containsKey("hora") && doc.containsKey("minuto") && doc.containsKey("segundo") && doc.containsKey("inputs")) {  // Verificar se as chaves existem no objeto JSON
    JsonArray inputsArray = doc["inputs"].as<JsonArray>();
    if (inputsArray.size() == 96) {           // Verificar se o array 'inputs' tem 96 elementos



      //////////////////////// Se todas as condições forem verdadeiras, atribui os valores
      hora[0] = doc["hora"];
      hora[1] = doc["minuto"];
      hora[2] = doc["segundo"];
      inputs = inputsArray;


      //////////////////////// imprime no serial:
      Serial.println("Dados lidos da memória:");
      Serial.print("Hora: ");
      Serial.println(hora[0]);
      Serial.print("Minuto: ");
      Serial.println(hora[1]);
      Serial.print("Segundo: ");
      Serial.println(hora[2]);
      Serial.print("Inputs: ");
      for (int value : inputs) {
        Serial.print(value);
      }
      Serial.println();
      Serial.println();





      //////////////////////// aumenta o tempo perdido esperando e tentando conectar ao wifi:
      incrementar_segundos(hora, 41, _hora);
      cont ++;
      if (cont >= 6) {  // 5 a cada 6 vezes ele aumenta 42, e 1 a cada 6 ele aumenta 41.  basicamente:  +42  +42  +42  +42  +42  +41,    ou,    1 a cada 6 vezes subtrai 1
        incrementar_segundos(hora, 5, _hora);
        cont = 0;
      }





      //////////////////////// monta a string novamente com os valores recebidos:
      jsonString_montada = "{\n\"hora\": " + String(_hora[0]) + ",\n" + "\"minuto\": " + String(_hora[1]) + ",\n" + "\"segundo\": " + String(_hora[2]) + ",\n" + "\"inputs\": [";
      for (int i = 0; i < 96; i++) {      // Adiciona os 96 elementos do array inputs ao JSON
        jsonString_montada += String(inputs[i]);
        if (i < 95) {                     // Adiciona vírgula para separar os elementos, exceto no último
          jsonString_montada += ",";
        }
      }
      jsonString_montada += "]\n}";

      Serial.println("String remontada que será armazenada (+42 segundos):");
      Serial.println(jsonString_montada);
      Serial.println();


      //////////////////////// armazena elas na memória:
      for (int i = 0; i < jsonString_montada.length(); i++) {   // Gravar a string na EEPROM
        EEPROM.write(i, jsonString_montada[i]);
      }
      EEPROM.write(jsonString_montada.length(), '\0');          // Adiciona um terminador nulo para indicar o final da string
      EEPROM.commit();                                          // Salva os dados no EEPROM




      //////////////////////// controla o relé:
      int posicao = (_hora[0] * 4) + (_hora[1] / 15);     // Calculando a posição no array de 96 elementos
      controla_rele(inputs[posicao]);





    } else {    // Se o array 'inputs' não tiver 96 elementos, alerta no serial
      Serial.println("Erro: o array 'inputs' não tem 96 elementos.");
    }

  } else {      // Se alguma chave não existir, alerta no serial
    Serial.println("Erro: falta uma ou mais chaves (hora, minuto, segundo, inputs).");
  }
}










void controla_rele(int acao) {
  if (acao == 0) {
    Serial.println("Desliga o relé");
    digitalWrite(D2, LOW);
    digitalWrite(LED_BUILTIN, HIGH);
    estado_rele = false;
  }
  if (acao == 1) {
    Serial.println("Liga o relé");
    digitalWrite(D2, HIGH);
    digitalWrite(LED_BUILTIN, LOW);
    estado_rele = true;
  }
}






void incrementar_segundos(int _horario[3], int segundosIncremento, int _hora[3]) {
  int __hora = _horario[0];
  int __minuto = _horario[1];
  int __segundo = _horario[2];

  // Incrementando os segundos
  __segundo += segundosIncremento;

  // Ajuste de minutos e segundos
  while (__segundo >= 60) {
    __segundo -= 60;
    __minuto += 1;
  }

  // Ajuste de horas e minutos
  while (__minuto >= 60) {
    __minuto -= 60;
    __hora += 1;
  }

  // Ajuste para o ciclo de 24 horas
  if (__hora >= 24) {
    __hora %= 24;
  }

  _hora[0] = __hora;
  _hora[1] = __minuto;
  _hora[2] = __segundo;
}





void conecta_ao_wifi() {
  WiFi.disconnect(true);  // Forçar a desconexão
  delay(1000);            // Aguardar antes de tentar reconectar



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
    return;  // Sai da função se conectado
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
    return;  // Sai da função se conectado
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

