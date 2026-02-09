# Guía de Instalación de Ollama en VPS (Contabo/Ubuntu)

Esta guía te ayudará a instalar Ollama en tu servidor VPS y configurarlo para que nuestra aplicación Laravel pueda conectarse a él.

## 1. Instalación de Ollama

Conéctate a tu VPS mediante SSH y ejecuta el siguiente comando:

```bash
curl -fsSL https://ollama.com/install.sh | sh
```

Esto descargará e instalará Ollama automáticamente.

## 2. Iniciar el Servicio

Normalmente, el script de instalación configura Ollama como un servicio de sistema (`systemd`). Verifica que esté corriendo:

```bash
systemctl status ollama
```

Si dice "**active (running)**", todo está bien.

## 3. Configurar para Acceso Remoto (IMPORTANTE)

Por defecto, Ollama solo escucha en `localhost` (127.0.0.1). Para que tu aplicación Laravel pueda conectarse desde fuera, debemos permitir conexiones externas.

### Paso 3.1: Configuración Robusta (Recomendado)

A veces los editores de texto en terminal pueden ser confusos o no guardar bien. La forma más segura de configurar esto es ejecutando este bloque de comandos **todo junto**:

```bash
mkdir -p /etc/systemd/system/ollama.service.d && \
echo '[Service]
Environment="OLLAMA_HOST=0.0.0.0"
Environment="OLLAMA_ORIGINS=*"' > /etc/systemd/system/ollama.service.d/override.conf && \
systemctl daemon-reload && \
systemctl restart ollama
```

### Paso 3.2: Verificar que está escuchando

Una vez ejecutado lo anterior, verifica que funcione con:

```bash
netstat -tuln | grep 11434
```
**Éxito:** Debe decir `:::11434` o `0.0.0.0:11434`.
**Error:** Si dice `127.0.0.1:11434`, repite el paso anterior.

## 4. Configurar Firewall (UFW)

Tu servidor probablemente tiene un firewall (muro de fuego) llamado UFW. Por defecto bloquea todo lo que no sea esencial. Necesitamos abrir la "puerta" 11434.

```bash
ufw allow 11434/tcp
```
Si te dice "Rules updated", funcionó.
Si te dice "ufw: command not found", es que quizás no tienes firewall activo o usas otro, en cuyo caso podrías saltar este paso.

## 5. Descargar el Modelo (Llama 3.2)

Ahora vamos a bajar el "cerebro" de la IA. Este modelo (3GB) es el que usaremos.

```bash
ollama pull llama3.2
```
Verás una barra de progreso. Espera a que llegue al 100%.

## 6. Probar conexión remota

**Desde tu computadora (NO desde el VPS)**, abre una terminal (CMD o PowerShell) e intenta esto, reemplazando la IP por la de tu VPS:

```powershell
curl http://123.123.123.123:11434/api/tags
```
