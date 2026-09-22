# inti-test
## Setup inicial

### Requisitos

El proyecto utiliza:

* PHP
* Composer
* Node.js
* Git
* Homebrew (macOS)

### Instalación del entorno

Verificar las herramientas instaladas:

```bash
php -v
composer -V
node -v
git --version
brew --version
```

Instalar PHP mediante Homebrew:

```bash
brew install php
```

Instalar Composer mediante Homebrew:

```bash
brew install composer
```

### Crear el proyecto Laravel

```bash
composer create-project laravel/laravel laravel-ai-gateway
```

Ingresar al proyecto:

```bash
cd laravel-ai-gateway
```

Verificar la versión de Laravel:

```bash
php artisan --version
```

Levantar el servidor local:

```bash
php artisan serve
```

La aplicación queda disponible en:

```text
http://127.0.0.1:8000
```

### Configuración de API

Instalar la configuración de rutas API de Laravel:

```bash
php artisan install:api
```

### Verificar las rutas

```bash
php artisan route:list
```



![alt text](image.png)


