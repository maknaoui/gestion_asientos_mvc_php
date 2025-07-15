# 🎟️ ButacaYa — Sistema de reserva de asientos

ButacaYa es una aplicación web que permite gestionar eventos y reservar asientos de manera visual e intuitiva.  
Fue desarrollada como prueba técnica, pero está pensada para ser escalable, profesional y fácil de mantener.

---

## ✨ Características

✅ CRUD de Eventos, Asientos, Tipos de Eventos y Reservas  
✅ Interfaz administrativa para gestionar asientos y ver capacidad ocupada  
✅ Selección visual de asientos para usuarios (con mapa interactivo)  
✅ Lógica para asegurar que no se sobrepase la capacidad máxima  
✅ Validación completa de los datos y del RUT (para Chile)  
✅ Subida de imágenes para los eventos con validación de formato y peso  
✅ Soporte para múltiples tipos de evento con layouts personalizados  
✅ Arquitectura MVC clara, lista para migrar a API REST

---

## 🔷 Tecnologías usadas

### Versión básica:
- PHP 8
- MySQL
- jQuery + jTable
- UIkit (CSS Framework)

### Versión Framework (en desarrollo):
- Laravel (backend API REST)
- Vue.js o React (frontend SPA)
- MySQL con migraciones y seeders
- Autenticación y protección con Laravel Sanctum

---

## 📂 Estructura del proyecto
.
├── app/
│ ├── controllers/
│ ├── models/
│ ├── views/
│ ├── utils/
├── uploads/
├── assets/
├── schema.sql
├── README.md
└── .env.example

---

## ⚙️ Instalación

1️⃣ Clona este repositorio:
```bash
git clone https://github.com/maknaoui/gestion_asientos_mvc_php.git

2️⃣ Configura la base de datos:

Crea una base de datos en MySQL

Importa el archivo schema.sql

3️⃣ Configura las variables de entorno en .env:
DB_HOST=localhost
DB_NAME=butacaya
DB_USER=butacaya
DB_PASS=
DB_CHARSET=utf8mb4

4️⃣ Coloca el proyecto en tu servidor web (Apache/Nginx) y listo.

👨‍💻 Futuro y mejoras
🛠️ Implementar API REST completa con Laravel
🛠️ Frontend SPA con Vue/React
🛠️ Pruebas unitarias y de integración
🛠️ CI/CD con GitHub Actions
🛠️ Contenedorización con Docker

📧 Contacto
Si quieres saber más sobre este proyecto o estás interesado en trabajar conmigo:

📨 yassine.maknaoui@gmail.com
💼 LinkedIn : yassinemaknaoui

.