# CHANGELOG

Todos los cambios notables del proyecto se documentarán en este archivo.

---

## [1.0.0] - 2025-06-16 (Fecha de inicio o primera versión funcional)

### Añadido
- Configuración inicial del entorno de desarrollo con Docker Compose.
  - Servicio de aplicación Laravel (PHP 8.2-FPM con extensiones comunes).
  - Servidor web Nginx.
  - Base de datos MariaDB (v10.6).
  - Servidor de caché y colas Redis.
- Automatización de instalación de dependencias PHP (Composer) y JS/CSS (npm) dentro del flujo de Docker.
- Gestión de permisos para volumenes montados en el contenedor de aplicación.

### Cambiado
- Migración de dependencias directas en el sistema operativo a un entorno completamente Dockerizado.