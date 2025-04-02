# Avengers Web - Gestión de Personajes

¡Bienvenido a la aplicación web para gestionar personajes de tus películas, series, animes o libros favoritos! 🎬📚

## 🚀 ¿Qué hace esta aplicación?

Esta aplicación te permite crear, leer, actualizar, eliminar y generar un PDF con la información de los personajes de una producción de tu elección. ¡Todo con un diseño que refleja la estética de esa serie, anime o película!

## 🔧 Requisitos

- **PHP** 7.4 o superior
- **Base de datos**: MySQL (puedes usar XAMPP o cualquier otro servidor local)
- **Librería**: TCPDF o DomPDF para la generación de PDFs

## 🛠️ Configuración

### 1. Configuración de la Base de Datos

- Si es la primera vez que usas la aplicación, accede a `install.php`.
- Ingresa los detalles de conexión de la base de datos (servidor, usuario, contraseña y nombre de la base de datos).
- La aplicación creará la base de datos `serie_db` y la tabla `personajes` automáticamente si no existen.

### 2. Conexión a la Base de Datos

La conexión a la base de datos está configurada en el archivo `db_config.php`. Si necesitas cambiar los datos de conexión, actualiza este archivo.

## ⚙️ Funciones

### CRUD de Personajes en `index.php`

Desde la página principal (`index.php`), podrás gestionar todos los personajes con las siguientes acciones:

- **Agregar Personaje**: Botón para añadir un nuevo personaje con nombre, color, tipo, nivel y foto.
- **Editar Personaje**: Botón para modificar la información de los personajes ya registrados.
- **Eliminar Personaje**: Botón para borrar personajes de la base de datos.
- **Ver y Descargar PDF**: Opción para generar un perfil en PDF con los detalles del personaje seleccionado, incluyendo su foto.

## 🎨 Diseño

- El diseño está inspirado en la producción que elijas. Puedes personalizar los colores, fuentes y estilos para hacerlo más acorde con tu tema.
- Utiliza **Bootstrap** o **Tailwind CSS** para la estructura visual.

## 🚀 Instrucciones de Uso

1. Accede a `install.php` para configurar la base de datos.
2. Después de la instalación, ve a `index.php` donde podrás ver la lista de personajes.
3. Usa los botones para **agregar, editar, eliminar** o **descargar en PDF** los perfiles de los personajes.
4. ¡Disfruta de gestionar tus personajes favoritos!

## 📄 Generación de PDF

Cada personaje tiene una opción para generar un PDF con su perfil completo, que incluye:
- Foto del personaje
- Nombre, color, tipo y nivel
- Estilo personalizado acorde al tema

---

¡Gracias por usar esta aplicación! Si tienes alguna pregunta o sugerencia, no dudes en ponerte en contacto.
