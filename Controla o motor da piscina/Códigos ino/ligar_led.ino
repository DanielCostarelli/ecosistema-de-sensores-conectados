// ESSE CÓDIGO CONTÉM UM EXEMPLO QUE liga e desliga o led integrado do esp e um relé conectado no D2 do esp (o relé está com aquele circuito com transistor)




void setup() {
  Serial.begin(9600);
  pinMode(D2, OUTPUT);
  pinMode(LED_BUILTIN, OUTPUT);
}

void loop() {
  Serial.println("Liga o relé");
  digitalWrite(D2, HIGH);
  digitalWrite(LED_BUILTIN, LOW);
  delay(10000);
  Serial.println("Desliga o relé");
  digitalWrite(D2, LOW);
  digitalWrite(LED_BUILTIN, HIGH);
  delay(10000);
}
