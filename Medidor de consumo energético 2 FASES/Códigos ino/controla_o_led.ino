const int pinLED = D2; // Pino onde o LED está conectado

void setup() {
  pinMode(pinLED, OUTPUT); // Configura o pino como saída
  analogWrite(pinLED, 255); // Define o PWM em valor máximo (brilho máximo)
}

void loop() {
  analogWrite(pinLED, 255);
  delay(300);
  analogWrite(pinLED, 40);
  delay(300);
  analogWrite(pinLED, 0);
  delay(300);
}


// bom
