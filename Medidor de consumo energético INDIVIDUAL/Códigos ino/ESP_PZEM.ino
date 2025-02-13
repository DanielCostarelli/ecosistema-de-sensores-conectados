#include <SoftwareSerial.h>
#include <PZEM004Tv30.h>

// Definir pinos para SoftwareSerial
#define RX_PIN D5  // GPIO14
#define TX_PIN D6  // GPIO12

SoftwareSerial pzemSerial(RX_PIN, TX_PIN);  // Inicializar SoftwareSerial nos pinos D5 e D6
PZEM004Tv30 pzem(pzemSerial);               // Passar o objeto SoftwareSerial para PZEM004Tv30



void setup() {
  Serial.begin(9600);
  pzemSerial.begin(9600);  // Inicializar a comunicação com o PZEM
  Serial.println("Monitoramento PZEM iniciado");
}



void loop() {
  float voltage = pzem.voltage();
  float current = pzem.current();
  float power = pzem.power();
  float energy = pzem.energy();
  float frequency = pzem.frequency();
  float pf = pzem.pf();


  if (!isnan(voltage) && !isnan(current) && !isnan(power) && !isnan(energy) && !isnan(frequency) && !isnan(pf)) {
    imprime_os_valores();
  } else {
    imprime_alerta();
  }


  Serial.println();
  delay(1000);
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
