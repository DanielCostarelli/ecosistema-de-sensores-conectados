#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <Arduino.h>

const char* ssid = "Costarelli";      // Substitua pelo nome da sua rede WiFi
const char* password = "tupperware";  // Substitua pela senha da sua rede WiFi

const char* serverUrl = "http://monitoramento.x10.bz/Medidor_PZEM/Modelo_Daniel/responde_o_esp.php";
const char* keyValue = "admin1248";  // Valor da chave que deseja enviar

WiFiClient client;  // Criando um objeto WiFiClient





#include <SoftwareSerial.h>
#include <PZEM004Tv30.h>
SoftwareSerial pzemSerial_A(D6, D5);
SoftwareSerial pzemSerial_B(D7, D8);
PZEM004Tv30 pzem_A(pzemSerial_A);
PZEM004Tv30 pzem_B(pzemSerial_B);




float voltage_A;
float current_A;
float power_A;
float energy_A;
float frequency_A;
float pf_A;

float voltage_B;
float current_B;
float power_B;
float energy_B;
float frequency_B;
float pf_B;

String url = String(serverUrl) + "?key=";


void setup() {
  Serial.begin(9600);
  conecta_ao_wifi();
  pzemSerial_A.begin(9600);  // Inicializar a comunicação com o PZEM A
  pzemSerial_B.begin(9600);  // Inicializar a comunicação com o PZEM B
  Serial.println("Monitoramento PZEM iniciado");
}





void loop() {
  if (WiFi.status() != WL_CONNECTED) {
    conecta_ao_wifi();
  }
  
  
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    // http.end(); // Finaliza qualquer conexão anterior

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
            // conecta_ao_wifi();
            ESP.restart();
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


  ////////// PZEM A
  float voltage_A = pzem_A.voltage();
  float current_A = pzem_A.current();
  float power_A = pzem_A.power();
  float energy_A = pzem_A.energy();
  float frequency_A = pzem_A.frequency();
  float pf_A = pzem_A.pf();
  
  ////////// PZEM B
  float voltage_B = pzem_B.voltage();
  float current_B = pzem_B.current();
  float power_B = pzem_B.power();
  float energy_B = pzem_B.energy();
  float frequency_B = pzem_B.frequency();
  float pf_B = pzem_B.pf();



  if (!isnan(voltage_A) && !isnan(current_A) && !isnan(power_A) && !isnan(energy_A) && !isnan(frequency_A) && !isnan(pf_A) && !isnan(voltage_B) && !isnan(current_B) && !isnan(power_B) && !isnan(energy_B) && !isnan(frequency_B) && !isnan(pf_B)) {  ///////////// CODIGO DE QUANDO DÁ TUDO CERTO:
    imprime_os_valores();


    ///////////// FORMATA A URL            [voltage_A, current_A, power_A, energy_A, frequency_A, pf_A, voltage_B, current_B, power_B, energy_B, frequency_B, pf_B]

    String dados = "[\"" + String(voltage_A) + "\",\"" + String(current_A) + "\",\"" + String(power_A) + "\",\"" + String(energy_A) + "\",\"" + String(frequency_A) + "\",\"" + String(pf_A) + "\",\"" + String(voltage_B) + "\",\"" + String(current_B) + "\",\"" + String(power_B) + "\",\"" + String(energy_B) + "\",\"" + String(frequency_B) + "\",\"" + String(pf_B) + "\"]";

    url = String(serverUrl) + "?key=" + dados + "//" + keyValue;
    Serial.println(url);
    return url;





  } else {
    imprime_alerta();
  }


  Serial.println();
  return url;
}









// String* parseJsonArray(const String& jsonString) {
//   // Remove os colchetes do início e do fim
//   String cleanedString = jsonString.substring(1, jsonString.length() - 1);

//   // Cria um array dinâmico para armazenar os valores
//   // Estimando um número máximo de elementos a partir do comprimento da cleanedString
//   int count = 0;
//   for (int i = 0; i < cleanedString.length(); i++) {
//     if (cleanedString.charAt(i) == ',') {
//       count++;
//     }
//   }
//   count++;  // Adiciona um para o último elemento

//   String* valores = new String[count];  // Cria um array dinâmico

//   // Separa os valores com base na vírgula
//   int startIndex = 0;
//   int index = 0;

//   for (int i = 0; i < cleanedString.length(); i++) {
//     if (cleanedString.charAt(i) == ',') {
//       valores[index++] = cleanedString.substring(startIndex, i);
//       startIndex = i + 1;
//     }
//   }
//   // Adiciona o último elemento
//   valores[index] = cleanedString.substring(startIndex);

//   // Tira as " " extras da string
//   for (int i = 0; i < 7; i++) {
//     valores[i] = valores[i].substring(1, valores[i].length() - 1);
//   }

//   return valores;  // Retorna o array
// }


// bool isNumericString(const String& str) {
//   // Verifica se a string tem exatamente 2 caracteres
//   if (str.length() != 2) {
//     return false;
//   }

//   // Verifica se ambos os caracteres são dígitos
//   if (!isDigit(str[0]) || !isDigit(str[1])) {
//     return false;
//   }

//   // Converte a string para um número inteiro
//   int valor = str.toInt();

//   // Verifica se o valor está no intervalo de 00 a 99
//   return (valor >= 0 && valor <= 99);
// }

// bool isNumericStringANO(const String& str) {
//   // Verifica se a string tem exatamente 2 caracteres
//   if (str.length() != 4) {
//     return false;
//   }

//   // Verifica se ambos os caracteres são dígitos
//   if (!isDigit(str[0]) || !isDigit(str[1]) || !isDigit(str[2]) || !isDigit(str[3])) {
//     return false;
//   }

//   // Converte a string para um número inteiro
//   int valor = str.toInt();

//   // Verifica se o valor está no intervalo de 00 a 9999
//   return (valor >= 0 && valor <= 9999);
// }

// bool verificaDiaS(String diaS) {

//   // Verifica se a string tem mais de 5 caracteres
//   if (diaS.length() <= 5) {
//     return false;
//   }

//   // Verifica se a primeira letra é maiúscula
//   if (!isUpperCase(diaS[0])) {
//     return false;
//   }

//   // Verifica se a segunda, terceira e quarta letras são minúsculas
//   if (!isLowerCase(diaS[1]) || !isLowerCase(diaS[2]) || !isLowerCase(diaS[3])) {
//     return false;
//   }

//   return true;
// }

// bool isUpperCase(char c) {
//   return (c >= 'A' && c <= 'Z');
// }

// bool isLowerCase(char c) {
//   return (c >= 'a' && c <= 'z');
// }




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
  ////////// PZEM A
  float voltage_A = pzem_A.voltage();
  float current_A = pzem_A.current();
  float power_A = pzem_A.power();
  float energy_A = pzem_A.energy();
  float frequency_A = pzem_A.frequency();
  float pf_A = pzem_A.pf();
  
  ////////// PZEM B
  float voltage_B = pzem_B.voltage();
  float current_B = pzem_B.current();
  float power_B = pzem_B.power();
  float energy_B = pzem_B.energy();
  float frequency_B = pzem_B.frequency();
  float pf_B = pzem_B.pf();

  Serial.println();
  Serial.println();
  
  Serial.print("  PZEM_A  |  ");

  Serial.print("Tensão: ");
  Serial.print(voltage_A);
  Serial.print(" V  |  ");

  Serial.print("Corrente: ");
  Serial.print(current_A);
  Serial.print(" A  |  ");

  Serial.print("Potência: ");
  Serial.print(power_A);
  Serial.print(" W  |  ");

  Serial.print("Energia: ");
  Serial.print(energy_A, 3);
  Serial.print(" kWh  |  ");

  Serial.print("Frequencia: ");
  Serial.print(frequency_A, 1);
  Serial.print(" Hz  |  ");

  Serial.print("FP: ");
  Serial.println(pf_A);




  Serial.print("  PZEM_B  |  ");

  Serial.print("Tensão: ");
  Serial.print(voltage_B);
  Serial.print(" V  |  ");

  Serial.print("Corrente: ");
  Serial.print(current_B);
  Serial.print(" A  |  ");

  Serial.print("Potência: ");
  Serial.print(power_B);
  Serial.print(" W  |  ");

  Serial.print("Energia: ");
  Serial.print(energy_B, 3);
  Serial.print(" kWh  |  ");

  Serial.print("Frequencia: ");
  Serial.print(frequency_B, 1);
  Serial.print(" Hz  |  ");

  Serial.print("FP: ");
  Serial.println(pf_B);

  
}




void imprime_alerta() {
  ////////// PZEM A
  float voltage_A = pzem_A.voltage();
  float current_A = pzem_A.current();
  float power_A = pzem_A.power();
  float energy_A = pzem_A.energy();
  float frequency_A = pzem_A.frequency();
  float pf_A = pzem_A.pf();
  
  ////////// PZEM B
  float voltage_B = pzem_B.voltage();
  float current_B = pzem_B.current();
  float power_B = pzem_B.power();
  float energy_B = pzem_B.energy();
  float frequency_B = pzem_B.frequency();
  float pf_B = pzem_B.pf();


  if (isnan(voltage_A)) { Serial.println("Erro ao ler a tensao (A)"); } else { Serial.print("Tensão: "); Serial.print(voltage_A); Serial.println(" V"); }
  if (!isnan(current_A)) { Serial.println("Erro ao ler a corrente (A)"); } else { Serial.print("Corrente: "); Serial.print(current_A); Serial.println(" A"); }
  if (!isnan(power_A)) { Serial.println("Erro ao ler a potencia (A)"); } else { Serial.print("Potência: "); Serial.print(power_A); Serial.println(" W"); }
  if (!isnan(energy_A)) { Serial.println("Erro ao ler a energia (A)"); } else { Serial.print("Energia: "); Serial.print(energy_A); Serial.println(" kWh"); }
  if (!isnan(frequency_A)) { Serial.println("Erro ao ler a frequencia (A)"); } else { Serial.print("Frequencia: "); Serial.print(frequency_A); Serial.println(" hz"); }
  if (!isnan(pf_A)) { Serial.println("Erro ao ler o fator de potencia (A)"); } else { Serial.print("FP: "); Serial.print(pf_A); Serial.println(""); }

  if (isnan(voltage_B)) { Serial.println("Erro ao ler a tensao (B)"); } else { Serial.print("Tensão: "); Serial.print(voltage_B); Serial.println(" V"); }
  if (!isnan(current_B)) { Serial.println("Erro ao ler a corrente (B)"); } else { Serial.print("Corrente: "); Serial.print(current_B); Serial.println(" A"); }
  if (!isnan(power_B)) { Serial.println("Erro ao ler a potencia (B)"); } else { Serial.print("Potência: "); Serial.print(power_B); Serial.println(" W"); }
  if (!isnan(energy_B)) { Serial.println("Erro ao ler a energia (B)"); } else { Serial.print("Energia: "); Serial.print(energy_B); Serial.println(" kWh"); }
  if (!isnan(frequency_B)) { Serial.println("Erro ao ler a frequencia (B)"); } else { Serial.print("Frequencia: "); Serial.print(frequency_B); Serial.println(" hz"); }
  if (!isnan(pf_B)) { Serial.println("Erro ao ler o fator de potencia (B)"); } else { Serial.print("FP: "); Serial.print(pf_B); Serial.println(""); }
  Serial.println("");
}
