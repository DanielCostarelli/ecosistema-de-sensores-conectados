// PARA CONECTAR AO ESP 8266:
// NodeMCU 1.0 (ESP-12E Module)
// 
// 
// 



#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <Arduino.h>

// const char* ssid = "Costarelli";      // Substitua pelo nome da sua rede WiFi
const char* ssid = "Konoha";      // Substitua pelo nome da sua rede WiFi
// const char* password = "tupperware";  // Substitua pela senha da sua rede WiFi
const char* password = "yppah15t7c";  // Substitua pela senha da sua rede WiFi
const char* serverUrl = "http://monitoramento.x10.bz/Medidor_PZEM/Modelo_da_Tati/responde_o_esp.php";
const char* keyValue = "admin124";  // Valor da chave que deseja enviar

WiFiClient client;  // Criando um objeto WiFiClient





#include <SoftwareSerial.h>
#include <PZEM004Tv30.h>
// Definir pinos para SoftwareSerial
#define RX_PIN D5  // GPIO14
#define TX_PIN D6  // GPIO12

SoftwareSerial pzemSerial(RX_PIN, TX_PIN);  // Inicializar SoftwareSerial nos pinos D5 e D6
PZEM004Tv30 pzem(pzemSerial);               // Passar o objeto SoftwareSerial para PZEM004Tv30




String dataHora;

float voltage;
float current;
float power;
float energy;
float frequency;
float pf;

String url = String(serverUrl) + "?key=";


void setup() {
  Serial.begin(9600);
  conecta_ao_wifi();
  pzemSerial.begin(9600);  // Inicializar a comunicação com o PZEM
  Serial.println("Monitoramento PZEM iniciado");
}





void loop() {
  if (WiFi.status() != WL_CONNECTED) {
    conecta_ao_wifi();
  }
  
  
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    http.end(); // Finaliza qualquer conexão anterior

    String url = todo_o_corpo_do_codigo_e_retorna_URL();

    http.begin(client, url);  // Usando o novo formato com WiFiClient

    int httpResponseCode = http.GET();  // Faz a requisição GET

    if (httpResponseCode > 0) {
      String response = http.getString();
      Serial.println("Resposta do servidor:");
      Serial.println(response);  // Imprime a resposta do servidor

    } else {
      Serial.print("Erro ao enviar requisição: ");
      Serial.println(httpResponseCode);

      switch (httpResponseCode) {
        case -1:
            Serial.println("Falha de conexão WiFi ou servidor inalcançável");
            ESP.restart();
            // conecta_ao_wifi();
            break;
        case -2:
            Serial.println("Tempo limite na conexão");
            break;
        case -3:
            Serial.println("Erro de memória insuficiente");
            break;
        case -4:
            Serial.println("Erro ao se conectar ao servidor");
            break;
        case -5:
            Serial.println("Falha na escrita da solicitação HTTP");
            break;
        default:
            Serial.println("Erro desconhecido");
            break;
      }
    }

    http.end();  // Finaliza a conexão

  } else {
    Serial.println("Não está conectado ao WiFi");
  }

  delay(10000);  // Aguarda 10 segundos antes de enviar novamente
}





String todo_o_corpo_do_codigo_e_retorna_URL() {


  ////////// PZEM
  float voltage = pzem.voltage();
  float current = pzem.current();
  float power = pzem.power();
  float energy = pzem.energy();
  float frequency = pzem.frequency();
  float pf = pzem.pf();


  if (!isnan(voltage) && !isnan(current) && !isnan(power) && !isnan(energy) && !isnan(frequency) && !isnan(pf)) {  ///////////// CODIGO DE QUANDO DÁ TUDO CERTO:
    imprime_os_valores();

    // enviar:   valores medidos (tensao, corrente, potencia, energia, frequencia e fp),    apenas. o backend faz todo o resto



    ///////////// FORMATA A URL            [voltage, current, power, energy, frequency, pf]

    String dados = "[\"" + String(voltage) + "\",\"" + String(current) + "\",\"" + String(power) + "\",\"" + String(energy) + "\",\"" + String(frequency) + "\",\"" + String(pf) + "\"]";

    url = String(serverUrl) + "?key=" + dados + "//" + keyValue;
    Serial.println(url);
    return url;





  } else {
    imprime_alerta();
  }


  Serial.println();
  return url;
}







String* parseJsonArray(const String& jsonString) {
  // Remove os colchetes do início e do fim
  String cleanedString = jsonString.substring(1, jsonString.length() - 1);

  // Cria um array dinâmico para armazenar os valores
  // Estimando um número máximo de elementos a partir do comprimento da cleanedString
  int count = 0;
  for (int i = 0; i < cleanedString.length(); i++) {
    if (cleanedString.charAt(i) == ',') {
      count++;
    }
  }
  count++;  // Adiciona um para o último elemento

  String* valores = new String[count];  // Cria um array dinâmico

  // Separa os valores com base na vírgula
  int startIndex = 0;
  int index = 0;

  for (int i = 0; i < cleanedString.length(); i++) {
    if (cleanedString.charAt(i) == ',') {
      valores[index++] = cleanedString.substring(startIndex, i);
      startIndex = i + 1;
    }
  }
  // Adiciona o último elemento
  valores[index] = cleanedString.substring(startIndex);

  // Tira as " " extras da string
  for (int i = 0; i < 7; i++) {
    valores[i] = valores[i].substring(1, valores[i].length() - 1);
  }

  return valores;  // Retorna o array
}





bool isNumericString(const String& str) {
  // Verifica se a string tem exatamente 2 caracteres
  if (str.length() != 2) {
    return false;
  }

  // Verifica se ambos os caracteres são dígitos
  if (!isDigit(str[0]) || !isDigit(str[1])) {
    return false;
  }

  // Converte a string para um número inteiro
  int valor = str.toInt();

  // Verifica se o valor está no intervalo de 00 a 99
  return (valor >= 0 && valor <= 99);
}

bool isNumericStringANO(const String& str) {
  // Verifica se a string tem exatamente 2 caracteres
  if (str.length() != 4) {
    return false;
  }

  // Verifica se ambos os caracteres são dígitos
  if (!isDigit(str[0]) || !isDigit(str[1]) || !isDigit(str[2]) || !isDigit(str[3])) {
    return false;
  }

  // Converte a string para um número inteiro
  int valor = str.toInt();

  // Verifica se o valor está no intervalo de 00 a 9999
  return (valor >= 0 && valor <= 9999);
}

bool verificaDiaS(String diaS) {

  // Verifica se a string tem mais de 5 caracteres
  if (diaS.length() <= 5) {
    return false;
  }

  // Verifica se a primeira letra é maiúscula
  if (!isUpperCase(diaS[0])) {
    return false;
  }

  // Verifica se a segunda, terceira e quarta letras são minúsculas
  if (!isLowerCase(diaS[1]) || !isLowerCase(diaS[2]) || !isLowerCase(diaS[3])) {
    return false;
  }

  return true;
}

bool isUpperCase(char c) {
  return (c >= 'A' && c <= 'Z');
}

bool isLowerCase(char c) {
  return (c >= 'a' && c <= 'z');
}




void conecta_ao_wifi() {
  WiFi.disconnect(true);  // Forçar a desconexão
  delay(5000);  // Aguardar antes de tentar reconectar
  WiFi.begin(ssid, password);

  // Conexão com o WiFi
  Serial.print("Conectando-se ao WiFi");
  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.print(".");
  }
  Serial.println("\nConectado ao WiFi");
}



void imprime_os_valores() {
  float voltage = pzem.voltage();
  float current = pzem.current();
  float power = pzem.power();
  float energy = pzem.energy();
  float frequency = pzem.frequency();
  float pf = pzem.pf();

  Serial.print("Tensao: ");
  Serial.print(voltage);
  Serial.println(" V");

  Serial.print("Corrente: ");
  Serial.print(current);
  Serial.println(" A");

  Serial.print("Potencia: ");
  Serial.print(power);
  Serial.println(" W");

  Serial.print("Energia: ");
  Serial.print(energy, 3);
  Serial.println(" kWh");

  Serial.print("Frequencia: ");
  Serial.print(frequency, 1);
  Serial.println(" Hz");

  Serial.print("Fator de Potencia: ");
  Serial.println(pf);
}





void imprime_alerta() {
  float voltage = pzem.voltage();
  float current = pzem.current();
  float power = pzem.power();
  float energy = pzem.energy();
  float frequency = pzem.frequency();
  float pf = pzem.pf();

  if (isnan(voltage)) { Serial.println("Erro ao ler a tensao"); }

  if (!isnan(current)) { Serial.println("Erro ao ler a corrente"); }

  if (!isnan(power)) { Serial.println("Erro ao ler a potencia"); }

  if (!isnan(energy)) { Serial.println("Erro ao ler a energia"); }

  if (!isnan(frequency)) { Serial.println("Erro ao ler a frequencia"); }

  if (!isnan(pf)) { Serial.println("Erro ao ler o fator de potencia"); }
}
