# Influessence Academy

Plataforma de cursos en línea de **Influessence Academy**, dirigida al mercado hispano de creadoras de contenido. Es un monolito: el backend, el panel de administración y el sitio público para las visitantes viven en el mismo proyecto.

## 1. Qué es

El sitio ofrece distintos tipos de curso:

- **Cursos grabados**: contenido en video que la alumna ve a su ritmo.
- **Programas en vivo**: con sesiones en vivo agendadas por calendario.
- **Cursos híbridos**: contenido grabado más una asesoría individual que se agenda aparte.
- **Guías**: material descargable mostrado como páginas de imagen dentro de la cuenta de la alumna (sin descarga de archivo).

Incluye un carrito de compras, pago con tarjeta, creación automática de cuenta al comprar, un área privada ("Mi cuenta") donde la alumna ve sus cursos, y un panel de administración donde el equipo gestiona cursos, precios, accesos y reservas sin tocar código.

## 2. Tecnologías

| Componente | Versión |
|---|---|
| PHP | ^8.4 |
| Laravel | 13.30 |
| Filament (panel de administración) | 5.8 |
| Livewire | 4.4 |
| Flux (componentes de UI) | 2.19 |
| Laravel Fortify (autenticación) | 1.39 |
| Tailwind CSS | 4.3 |
| Vite | 8.x (vía `vite-plus`) |
| Stripe PHP SDK | 21.3 |
| Resend (correo transaccional) | 1.5 |

Servicios externos integrados: **Stripe** (pagos, Checkout alojado), **Resend** (envío de correos), **Calendly** (agenda de clases y asesorías) y **Vimeo** (alojamiento de los videos de los cursos, con privacidad restringida por dominio).

## 3. Requisitos

- **PHP 8.4** o superior.
- Extensiones de PHP: `ctype`, `curl`, `dom`, `fileinfo`, `filter`, `hash`, `iconv`, `intl`, `json`, `libxml`, `mbstring`, `openssl`, `pcre`, `pdo_mysql`, `session`, `tokenizer`, `xmlreader`, `zip`.
- **Composer** 2.x.
- **Node.js** 20.19 o superior (o 22.12+), con npm.
- **MySQL** o **MariaDB**. El proyecto no usa SQLite en ningún ambiente.

## 4. Instalación en local

```bash
# 1. Clonar el repositorio y entrar a la carpeta
git clone <url-del-repositorio>
cd influessence-academy

# 2. Instalar dependencias de PHP
composer install

# 3. Copiar el archivo de entorno y generar la clave de la aplicación
cp .env.example .env
php artisan key:generate

# 4. Completar en .env al menos la conexión a la base de datos
#    (ver la tabla de variables de entorno más abajo)

# 5. Crear la base de datos vacía en MySQL/MariaDB, y luego migrar
php artisan migrate

# 6. Instalar dependencias de JavaScript y compilar los estilos/scripts
npm install
npm run build

# 7. Levantar el servidor de desarrollo
php artisan serve
```

Para desarrollo día a día, `npm run dev` deja Vite recompilando en caliente mientras se edita.

## 5. Variables de entorno

Todas las variables viven en `.env.example`, sin ningún valor real cargado — cada quien completa su propio `.env` local o de servidor. Las marcadas como **obligatoria en producción** deben revisarse siempre antes de desplegar; dejarlas con el valor de desarrollo puede exponer información interna o dejar funcionalidad a medias.

| Variable | Para qué sirve | Obligatoria en producción |
|---|---|---|
| `APP_NAME` | Nombre de la aplicación, usado en el `<title>` y en correos. | No |
| `APP_ENV` | Entorno de ejecución (`local`, `production`). Cambia comportamientos internos (contraseñas más estrictas, bloqueo de comandos destructivos). | **Sí** — debe ser `production` |
| `APP_KEY` | Clave de cifrado de la aplicación (sesiones, datos cifrados). Se genera con `php artisan key:generate`. | Sí |
| `APP_DEBUG` | Si es `true`, los errores muestran detalles internos (rutas, consultas) a quien visite el sitio. | **Sí** — debe ser `false` |
| `APP_URL` | URL pública del sitio, usada para generar enlaces en correos y en Stripe. | **Sí** — debe ser la URL real con `https://` |
| `APP_LOCALE` | Idioma de la aplicación. | No |
| `APP_FALLBACK_LOCALE` | Idioma de respaldo si falta una traducción. | No |
| `APP_FAKER_LOCALE` | Idioma usado por los generadores de datos de prueba (`factories`). | No |
| `APP_MAINTENANCE_DRIVER` | Cómo se guarda el estado de "modo mantenimiento". | No |
| `BCRYPT_ROUNDS` | Costo del algoritmo de cifrado de contraseñas. | No |
| `LOG_CHANNEL` / `LOG_STACK` / `LOG_DEPRECATIONS_CHANNEL` / `LOG_LEVEL` | A dónde y con cuánto detalle se escriben los registros (`logs`). | Recomendado bajar el nivel de detalle en producción |
| `DB_CONNECTION` | Motor de base de datos (`mysql`). | Sí |
| `DB_HOST` / `DB_PORT` / `DB_DATABASE` / `DB_USERNAME` / `DB_PASSWORD` | Datos de conexión a la base de datos. | Sí |
| `SESSION_DRIVER` | Dónde se guardan las sesiones de usuarias (`database`). | No |
| `SESSION_LIFETIME` | Minutos de inactividad antes de cerrar sesión. | No |
| `SESSION_ENCRYPT` | Si la cookie de sesión se cifra. | No |
| `SESSION_PATH` / `SESSION_DOMAIN` | Alcance de la cookie de sesión. | No |
| `SESSION_SECURE_COOKIE` | Obliga a que la cookie de sesión solo viaje por HTTPS. | **Sí** — debe ser `true` |
| `BROADCAST_CONNECTION` | Driver de eventos en tiempo real (no se usa activamente hoy). | No |
| `FILESYSTEM_DISK` | Disco de almacenamiento de archivos por defecto. | No |
| `QUEUE_CONNECTION` | Cómo se procesan los trabajos en segundo plano. | **Sí** — debe ser `sync` (no hay un worker de colas corriendo en el servidor) |
| `CACHE_STORE` | Dónde se guarda la caché de la aplicación. | No |
| `CACHE_PREFIX` | Prefijo para las claves de caché. | No |
| `MEMCACHED_HOST` / `REDIS_*` | Configuración de Memcached/Redis (no se usan en este proyecto; quedan con su valor por defecto). | No |
| `MAIL_MAILER` | Driver de envío de correo. | **Sí** — debe ser `resend` |
| `MAIL_SCHEME` / `MAIL_HOST` / `MAIL_PORT` / `MAIL_USERNAME` / `MAIL_PASSWORD` | Configuración de un servidor SMTP alterno (no se usa con Resend). | No |
| `MAIL_FROM_ADDRESS` / `MAIL_FROM_NAME` | Remitente de los correos transaccionales. | Sí |
| `RESEND_KEY` | Clave de API de Resend para el envío real de correos. | **Sí** |
| `RESEND_ROUTES` | Si se registran las rutas internas del paquete de Resend. | No |
| `AWS_*` | Credenciales para un disco de almacenamiento en S3 (no se usa hoy; el proyecto solo usa disco local). | No |
| `STRIPE_KEY` | Clave pública de Stripe. | Sí |
| `STRIPE_SECRET` | Clave secreta de Stripe, usada para crear las sesiones de pago desde el servidor. | **Sí** |
| `STRIPE_WEBHOOK_SECRET` | Secreto para verificar la firma del webhook de Stripe. Sin esto, el webhook rechaza todos los eventos. | **Sí** |
| `CALENDLY_TOKEN` | Token de la API de Calendly para confirmar reservas. | **Sí** |
| `CALENDLY_SCHEDULING_URL` | URL del calendario de Calendly mostrado a las estudiantes. | **Sí** |
| `GTM_CONTAINER_ID` | ID del contenedor de Google Tag Manager. Si queda vacío, simplemente no se carga ningún script de analítica. | No (opcional) |
| `VITE_APP_NAME` | Nombre de la aplicación expuesto al build de Vite. | No |

## 6. Cómo correr las pruebas

El proyecto usa [Pest](https://pestphp.com/) sobre PHPUnit. Hoy la suite tiene **273 pruebas**.

```bash
# Toda la suite
php artisan test

# Un archivo o carpeta específica
php artisan test tests/Feature/Cart

# Un test puntual por nombre
php artisan test --filter=nombre_del_test
```

También se usa [Laravel Pint](https://laravel.com/docs/pint) para el formato de código (`vendor/bin/pint`) y [Larastan](https://github.com/larastan/larastan) para análisis estático (`vendor/bin/phpstan analyse`).

## 7. Cómo funciona la compra

1. La visitante agrega uno o más cursos al carrito (sin necesidad de tener cuenta).
2. Al pagar, se crea una sesión de **Stripe Checkout alojado** — el formulario de pago vive en los servidores de Stripe, nunca en este sitio.
3. Stripe notifica el pago exitoso mediante un webhook: `POST /stripe/webhook`, escuchando el evento `checkout.session.completed`. La firma de ese webhook se verifica antes de procesar nada.
4. Si quien compró no tenía cuenta, se crea automáticamente con su correo, y recibe un enlace para definir su contraseña. Si ya tenía cuenta con ese correo, la compra se asocia a esa cuenta existente.
5. Se otorga acceso al curso comprado y queda registrada la orden.
6. La misma cuenta de Stripe también puede recibir eventos de otro proyecto de la agencia (ventas que no son de esta plataforma). El webhook identifica esas compras porque no traen el dato `course_ids` en los metadatos del pago, y las ignora sin crear nada ni generar un error.

Todos los montos de dinero se manejan internamente en **centavos como números enteros**, nunca en decimales, para evitar errores de redondeo.

## 8. Panel de administración

Disponible en la ruta **`/admin`**. Solo pueden entrar las cuentas marcadas internamente con el rol de administrador — cualquier otra cuenta recibe un error de acceso denegado, incluso si conoce la URL.

Desde ahí el equipo gestiona, sin tocar código:

- **Cursos**: contenido, módulos, lecciones, precios, tipo de curso y si está publicado.
- **Accesos**: qué alumna tiene acceso a qué curso, y permite otorgar accesos manualmente.
- **Órdenes**: historial de compras realizadas.
- **Reservas**: sesiones agendadas por las alumnas.
- **Usuarios**: cuentas registradas en la plataforma.

## 9. Tarea programada

El proyecto tiene un único comando programado: `courses:expire-advisories`, que debe ejecutarse **una vez al día** (vía el programador de tareas de Laravel). Revisa los cursos híbridos cuya ventana para agendar la asesoría ya venció sin que la alumna haya reservado, y marca esa asesoría como vencida — esto es solo informativo para el panel de administración; no retira el acceso ya otorgado al curso.

## 10. Despliegue

En términos generales, cada despliegue a producción sigue este orden:

1. `git pull` para traer el código más reciente.
2. `composer install --no-dev --optimize-autoloader` (con PHP 8.4).
3. Compilar los assets del frontend (`npm run build`) y subir la carpeta `public/build` resultante — **no viaja dentro del repositorio**, hay que generarla o subirla aparte en cada despliegue.
4. Correr migraciones pendientes: `php artisan migrate --force`.
5. Limpiar y regenerar cachés: `php artisan optimize:clear` seguido de `php artisan config:cache`, `php artisan route:cache` y `php artisan view:cache`.
6. Confirmar que el webhook de Stripe apunta a la URL pública de producción.

No se documentan aquí rutas de servidor, credenciales ni datos de infraestructura — esa información vive fuera del repositorio.

## 11. Estructura de carpetas

Además de la estructura estándar de Laravel, las carpetas más relevantes para entender el negocio son:

```
app/
├── Actions/            # Un caso de uso por clase (crear orden, otorgar acceso, agregar al carrito, etc.)
│   ├── Bookings/
│   ├── Cart/
│   ├── Courses/
│   ├── Fortify/
│   └── Orders/
├── Http/Controllers/    # Controladores HTTP (uno por flujo: carrito, cursos, reservas, webhook de Stripe...)
├── Filament/Resources/  # Recursos del panel de administración (Cursos, Accesos, Órdenes, Reservas, Usuarios)
├── Models/               # Modelos Eloquent (Course, Order, Booking, CourseAccess, User, etc.)
└── Policies/             # Reglas de autorización (quién puede ver el contenido de qué curso)

resources/views/
├── welcome.blade.php     # Página de inicio
├── courses/              # Catálogo y detalle de curso
├── cart/                 # Carrito y página de agradecimiento tras la compra
├── learning/             # Área privada "Mi cuenta" (cursos, lecciones, guías)
├── bookings/             # Agenda de clases (Calendly)
├── legal/                # Políticas de privacidad, términos y reembolsos
└── components/           # Componentes reutilizables (navegación, carrusel, layouts)

tests/Feature/            # Pruebas de los flujos críticos: compra, webhook, acceso a cursos, reservas, etc.
```

## 12. Decisiones y límites conocidos

- Los montos de dinero se guardan y calculan siempre en **centavos, como números enteros** — nunca en decimales.
- **No hay reembolsos automáticos.** Cualquier reembolso se gestiona manualmente desde el dashboard de Stripe; el sitio no tiene un flujo propio para esto.
- Las **guías** se muestran como páginas de imagen dentro de la cuenta de la alumna; no existe un botón de descarga de archivo.
- **Cancelar una reserva desde el panel de administración no cancela el evento en Calendly.** Son dos sistemas independientes — si se cancela una clase, hay que cancelarla también en Calendly para que ese horario vuelva a quedar disponible.
- El checkout de pago es siempre el formulario alojado de Stripe; este sitio nunca procesa ni almacena datos de tarjetas directamente.
