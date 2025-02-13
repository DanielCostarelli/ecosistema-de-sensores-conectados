// criar um codigo para realizar teste e calibrar o sensor de temperatura LM35, comparando com o termometro da Tati


#define pinLM35 0
float temperatura = 0;

void setup() {
  Serial.begin(9600);
}

void loop() {
  temperatura = analogAvg(pinLM35);
  // temperatura = map(temperatura, 0, 1023, 0, 100);

  Serial.print("Temperatura: ");
  Serial.print(temperatura);
  Serial.println(" °C");

  delay(50); // demora mais 50 ms pra totalizar 4 por segundo
}


int analogAvg(int sensorPin) { //função demora 200ms
  int total = 0;
  int amostras = 20;
  for (int n = 0; n < amostras; n++) {
    total += analogRead(sensorPin);
    delay(10);
  }
  return total / amostras;
}
