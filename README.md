# isunKi - Gestión de Multas para Equipos Deportivos

## Descripción
Aplicación web para gestionar multas en equipos deportivos. Permite crear multas, gestionar pagos y enviar quejas.

## Tecnologías
- PHP 8.0
- MySQL 5.7
- HTML5, CSS3, JavaScript

## Requisitos
- XAMPP (Apache, MySQL, PHP)
- Navegador web actualizado

## Instalación

1. Clona el repositorio en `C:\xampp\htdocs\`
2. Inicia XAMPP (Apache y MySQL)
3. Importa la base de datos desde `db/isunki_db.sql`
4. Configura la conexión en `modelo/conexion.php`
5. Accede desde `http://localhost/isunki`

## Accesos por defecto

| Rol | Correo | Contraseña |
|-----|--------|-------------|
| Admin | admin@isunki.com | 1312 |
| Multero | multero@ejemplo.com | multero |
| Jugador | jugador@ejemplo.com | jugador |

## Estructura

- `controlador/` - Lógica PHP
- `vista/` - Pantallas
- `modelo/` - Conexión BD
- `css/` - Estilos
- `img/` - Imágenes

## Autor
Aner Monzon