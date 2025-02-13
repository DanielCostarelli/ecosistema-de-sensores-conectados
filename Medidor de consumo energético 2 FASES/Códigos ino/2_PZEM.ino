
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



void setup() {
  Serial.begin(9600);
  pzemSerial_A.begin(9600);  // Inicializar a comunicação com o PZEM A
  pzemSerial_B.begin(9600);  // Inicializar a comunicação com o PZEM B
  Serial.println("Monitoramento PZEM iniciado");

  pinMode(D2, OUTPUT);  // Configura o pino como saída
  pinMode(D3, OUTPUT);  // Configura o relé como saída
  digitalWrite(D3, LOW);  // Define ele desligado  // sem o relé, ele buga, imagino que ele liga o PZEM antes do esp e buga, por isso deve ser ligado depois.
}




void loop() {

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
  } else {
    imprime_alerta();
  }


  delay(450);  // Aguarda


  analogWrite(D2, 255);
  delay(450);
  analogWrite(D2, 0);
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

  Serial.println();
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


  if (isnan(voltage_A)) {
    Serial.println("Erro ao ler a tensao (A)");
  } else {
    Serial.print("Tensão: ");
    Serial.print(voltage_A);
    Serial.println(" V");
  }
  if (!isnan(current_A)) {
    Serial.println("Erro ao ler a corrente (A)");
  } else {
    Serial.print("Corrente: ");
    Serial.print(current_A);
    Serial.println(" A");
  }
  if (!isnan(power_A)) {
    Serial.println("Erro ao ler a potencia (A)");
  } else {
    Serial.print("Potência: ");
    Serial.print(power_A);
    Serial.println(" W");
  }
  if (!isnan(energy_A)) {
    Serial.println("Erro ao ler a energia (A)");
  } else {
    Serial.print("Energia: ");
    Serial.print(energy_A);
    Serial.println(" kWh");
  }
  if (!isnan(frequency_A)) {
    Serial.println("Erro ao ler a frequencia (A)");
  } else {
    Serial.print("Frequencia: ");
    Serial.print(frequency_A);
    Serial.println(" hz");
  }
  if (!isnan(pf_A)) {
    Serial.println("Erro ao ler o fator de potencia (A)");
  } else {
    Serial.print("FP: ");
    Serial.print(pf_A);
    Serial.println("");
  }

  if (isnan(voltage_B)) {
    Serial.println("Erro ao ler a tensao (B)");
  } else {
    Serial.print("Tensão: ");
    Serial.print(voltage_B);
    Serial.println(" V");
  }
  if (!isnan(current_B)) {
    Serial.println("Erro ao ler a corrente (B)");
  } else {
    Serial.print("Corrente: ");
    Serial.print(current_B);
    Serial.println(" A");
  }
  if (!isnan(power_B)) {
    Serial.println("Erro ao ler a potencia (B)");
  } else {
    Serial.print("Potência: ");
    Serial.print(power_B);
    Serial.println(" W");
  }
  if (!isnan(energy_B)) {
    Serial.println("Erro ao ler a energia (B)");
  } else {
    Serial.print("Energia: ");
    Serial.print(energy_B);
    Serial.println(" kWh");
  }
  if (!isnan(frequency_B)) {
    Serial.println("Erro ao ler a frequencia (B)");
  } else {
    Serial.print("Frequencia: ");
    Serial.print(frequency_B);
    Serial.println(" hz");
  }
  if (!isnan(pf_B)) {
    Serial.println("Erro ao ler o fator de potencia (B)");
  } else {
    Serial.print("FP: ");
    Serial.print(pf_B);
    Serial.println("");
  }
  Serial.println("");
}
