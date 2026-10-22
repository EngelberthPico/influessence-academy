# Revisión pre-producción — Influessence Academy

Fecha de la revisión: 2026-10-20. Revisión de solo lectura: no se modificó código, migraciones ni configuración. Se corrió la suite de tests (`php artisan test`) y comandos de lectura (`git ls-files`, `grep`, lectura de archivos).

**Actualización 2026-10-20:** Se corrigieron los 5 hallazgos marcados como 🟡/🟢 abajo (ver el estado al inicio de cada uno). La suite pasó de 234 a 252 tests, todos en verde. Nada de esto se commiteó — los cambios quedaron pendientes de revisión manual.

## 1. Resumen

**Sí, con arreglos.** La base de seguridad y de pagos está sólida: el webhook de Stripe verifica firma y es idempotente (con test explícito para entregas duplicadas), el precio siempre sale del servidor, las imágenes de las guías viven en disco privado y están protegidas por acceso autenticado, y los 234 tests de la suite pasan. No encontré inyección SQL, redirecciones abiertas ni fugas de secretos.

Lo que falta antes de salir a producción no es inseguridad del código, sino **piezas de operación y de producto**: no hay manejo de errores si Stripe falla justo al pagar, no hay SEO básico (meta description, Open Graph, analítica) en ninguna página, y el despliegue tiene varios pasos manuales que si se saltan rompen cosas en silencio (el correo de notificación de compra, el comando que vence asesorías, crear el primer usuario admin). Ninguno de estos hallazgos es un hueco de seguridad explotable; son cosas a resolver o a tener en el checklist antes de abrir al público.

## 2. Hallazgos

### ✅ Resuelto — No hay manejo de errores si la API de Stripe falla al crear la sesión de pago
**Archivo:** `app/Http/Controllers/CartController.php:89`
**Qué pasa:** `StripeCheckoutSession::create($params)` no está envuelto en try/catch. Si Stripe está caído, hay un problema de red o la clave es inválida, la excepción sube sin capturar.
**Por qué importa:** La estudiante, justo en el momento de pagar, vería la página de error genérica de Laravel (fea, sin contexto) en vez de un mensaje como "no pudimos iniciar tu pago, intenta de nuevo". Es el punto más sensible de todo el flujo de compra.
**Cómo se arreglaría:** Envolver la llamada en try/catch, loguear el error y redirigir al carrito con un mensaje de error amigable (como ya se hace en otros puntos del controlador con `->with('error', ...)`).
**Qué se hizo:** `CartController::pay()` ahora captura `Stripe\Exception\ApiErrorException`, registra el error en el log (usuario y cursos del carrito, nunca claves) y redirige al carrito con "No pudimos iniciar tu pago. Intenta de nuevo en un momento." Test nuevo simulando la falla de Stripe, confirmando el mensaje y que no se crea ninguna `Order` ni `CourseAccess`.

### ✅ Resuelto (parcial) — No hay SEO básico (meta description, Open Graph, analítica) en ninguna página
**Archivo:** `resources/views/partials/head.blade.php` (completo, 15 líneas)
**Qué pasa:** El `<head>` compartido por todo el sitio solo trae `charset`, `viewport`, `<title>` y favicons. No hay `<meta name="description">`, ni `og:title`/`og:description`/`og:image`, ni canonical, ni ningún snippet de Google Tag Manager o GA4 (confirmé con una búsqueda en todo `resources/` que no existe ninguna referencia a `GTM-`, `gtag(` ni `googletagmanager`).
**Por qué importa:** El propio archivo de reglas del proyecto (`CLAUDE.md`) ya anticipa una imagen de Open Graph en JPG ("varios crawlers de redes sociales no leen WebP de forma confiable"), pero esa imagen no existe en el proyecto ni está referenciada en ningún lado. Hoy, si alguien comparte el link del sitio en WhatsApp o Facebook, no se ve ninguna tarjeta de vista previa — mala primera impresión para una landing de ventas. Y sin GTM/GA4, no hay forma de medir qué tan bien convierte la página.
**Cómo se arreglaría:** Agregar meta description + Open Graph (title, description, image, url) al partial de `<head>`, generar la imagen og-image en JPG siguiendo la convención ya definida, y decidir con el negocio si se instala GTM (recomendado sobre GA4 directo, como ya quedó decidido en las reglas del proyecto).
**Qué se hizo:** El `<head>` compartido ahora arma título, descripción, Open Graph y canonical por página (home, cursos, detalle de curso con copy dinámico del propio curso, y las 3 páginas legales), con `noindex` automático en todas las páginas privadas y en `/admin`. Se agregó el snippet de Google Tag Manager, pero solo se imprime si hay un ID configurado (`GTM_CONTAINER_ID`). `robots.txt` ahora bloquea `/admin`, `/mi-cuenta`, `/settings` y `/reservar-clase`. **Queda pendiente** (no es código, ver sección 6): la foto para `og:image` no existe todavía — el mecanismo está listo, solo falta que el equipo elija/genere esa imagen en JPG.

### ⏳ Pendiente (no es código, es un paso de despliegue) — `OrderPaidAdminNotification` se encola; si `QUEUE_CONNECTION` queda mal en producción, el correo nunca sale
**Archivo:** `app/Mail/OrderPaidAdminNotification.php:15`
**Qué es "ShouldQueue":** Es una interfaz de Laravel que le dice al framework "no envíes este correo en el momento, ponlo en una fila (cola) para procesarlo aparte". Sirve para no hacer esperar al usuario mientras se envía un correo.
**Qué pasa:** Esta clase implementa `ShouldQueue`, así que aunque el código la manda con `Mail::send()`, Laravel la encola igual. Confirmé con un test (`tests/Feature/Orders/StripeWebhookTest.php:137`, usa `Mail::assertQueued`) que efectivamente se encola.
**Por qué importa:** Con `QUEUE_CONNECTION=sync` (lo que está planeado para producción, sin worker), encolar un trabajo lo ejecuta inmediatamente en el mismo proceso — no hace falta un worker y todo funciona bien. Pero si por error el `.env` de producción queda con el valor que usa local (`QUEUE_CONNECTION=database`), el correo de "nueva compra" simplemente se queda guardado en la tabla `jobs` y **nunca se envía**, porque no hay ningún worker corriendo en Hostinger para procesarlo. Es un fallo silencioso: la compra se procesa bien, la clienta recibe su acceso, pero el equipo nunca se entera de la venta por correo.
**Cómo se arreglaría:** No es un cambio de código — es verificar explícitamente, después de desplegar, que `QUEUE_CONNECTION=sync` quedó así en el `.env` de producción, y hacer una compra de prueba real para confirmar que el correo a `info@influessenceacademy.com` llega.

### ✅ Resuelto — No hay clases `Policy` de Laravel, aunque las reglas del proyecto las piden
**Qué es una "Policy":** Es la forma estándar de Laravel para centralizar las reglas de "¿puede este usuario hacer esta acción sobre este registro?" en una sola clase por modelo, en vez de repetir el chequeo a mano en cada controlador.
**Qué pasa:** `CLAUDE.md` dice textualmente "Autorización: Laravel Policies para Course, Order, Booking. El frontend nunca es la capa de control de acceso real." No existe la carpeta `app/Policies` ni se usa `Gate::` / `->authorize()` / `->can()` en ningún lado del código (confirmé con `grep` en todo `app/`). En su lugar, la autorización se hace a mano: `$user->hasAccessTo($course)` (método en `app/Models/User.php:76`) llamado explícitamente en cada controlador (`MyLearningController.php:27,50,62`, `app/Actions/Bookings/ConfirmCalendlyBookingAction.php:25`).
**Por qué importa:** Funciona hoy y está bien testeado (lo verifiqué ruta por ruta), pero es más fácil que a futuro alguien agregue una ruta nueva y se le olvide poner el `abort_unless(...)` a mano, que olvidar una Policy que Laravel aplica de forma más consistente. Es una desviación del estándar que el propio proyecto se puso, no un hueco de seguridad activo ahora mismo.
**Cómo se arreglaría:** Decisión de equipo: o se actualiza `CLAUDE.md` para reflejar que la autorización se hace con chequeos explícitos (lo cual es válido si se documenta y se mantiene disciplina), o se migra `hasAccessTo()` y los chequeos de `Booking`/`Order` a Policies reales. No urge antes de lanzar, pero sí antes de que el equipo crezca.
**Qué se hizo:** Se creó `app/Policies/CoursePolicy.php` con un método `access()` (nombre elegido a propósito para no chocar con los nombres de acción que Filament vigila automáticamente — `view`, `update`, `delete`, etc. — y así no afectar la gestión de cursos en `/admin`). Los 3 chequeos de `MyLearningController` (ver curso, video, página de guía) y el de `ConfirmCalendlyBookingAction` (agendar clase) ahora pasan por esta Policy en vez de llamar `hasAccessTo()` directo. No se tocaron los otros 2 usos de `hasAccessTo()` (en `CourseController` y `CartController`): son lógica de negocio de la tienda ("ya tienes este curso"), no control de acceso. Tampoco se crearon Policies de `Order` ni `Booking`: hoy no hay ningún chequeo manual sobre esos modelos fuera de `/admin` (ya protegido), y crear una Policy vacía ahí sí habría activado la revisión automática de Filament sobre acciones reales del panel, con riesgo de bloquear algo sin necesidad. Tests nuevos: alumna con acceso, alumna sin acceso, invitada, y admin sin haber comprado el curso (los cuatro casos pedidos).

### ✅ Resuelto — `role` es un campo "fillable" en el modelo `User`
**Archivo:** `app/Models/User.php:33`
**Qué es "fillable":** Es la lista de campos que un modelo permite llenar de forma masiva (por ejemplo con `User::create([...])` pasando un arreglo). Si un campo sensible está en esa lista y en algún punto del código se le pasa directo el input del usuario, esa persona podría cambiar ese campo sin que el código lo haya previsto explícitamente.
**Qué pasa:** `role` está en el `#[Fillable(...)]` de `User`. Revisé los dos únicos lugares donde se crea un `User` (`app/Actions/Fortify/CreateNewUser.php:27` y `app/Actions/Orders/CreateOrderFromStripeCheckoutSessionAction.php:50`) y el único lugar donde se actualiza desde un formulario (`app/Livewire/Settings/Profile.php:40`) — los tres arman el arreglo a mano o lo validan contra una lista fija de campos, nunca pasan el input completo. Hoy no hay forma de explotar esto.
**Por qué importa:** Es una trampa para el futuro: si alguien agrega después un endpoint que haga `User::create($request->all())` o `$user->update($request->validated())` sin pensarlo, cualquiera podría mandar `role=admin` en el request y autoasignarse acceso de administrador.
**Cómo se arreglaría:** Sacar `'role'` del `#[Fillable(...)]` de `User` y asignarlo solo internamente (por ejemplo con `forceFill` o `$user->role = ...` directo), nunca vía mass-assignment.
**Qué se hizo:** Se sacó `'role'` del `#[Fillable(...)]`. El único lugar donde esto importaba de verdad —el selector de "Rol" en el panel de Filament— se ajustó para asignarlo explícitamente al guardar, así que sigue funcionando igual. Tests nuevos: `User::create([...'role'=>'admin'])` ya no eleva el rol, un admin sí puede seguir cambiando el rol de otra persona desde Filament, y mandar `role: 'admin'` en el formulario público de registro no hace nada.

### ✅ Resuelto — El seeder por defecto crea una cuenta de prueba con contraseña conocida
**Archivo:** `database/seeders/DatabaseSeeder.php:17-20`
**Qué pasa:** `DatabaseSeeder` crea un usuario `test@example.com` con la contraseña por defecto de las factories, que es literalmente la palabra `"password"` (`database/factories/UserFactory.php:31`).
**Por qué importa:** Esto es normal y esperado en desarrollo. El riesgo es solo si alguien corre `php artisan db:seed` (o `migrate --seed`) en producción por costumbre — quedaría una cuenta real con credenciales públicas y conocidas.
**Cómo se arreglaría:** No tocar el código; solo asegurarse de que el proceso de despliegue use `php artisan migrate --force` y nunca `db:seed` en producción (ya lo incluí en el checklist abajo).
**Qué se hizo:** Igual se agregó una validación en el propio seeder (`if (app()->isProduction()) { return; }`) como respaldo, por si alguien corre `db:seed` en producción por error. Test nuevo que lo confirma.

### ✅ Resuelto (parcial) — `robots.txt` no bloquea las rutas privadas ni declara un sitemap
**Archivo:** `public/robots.txt`
**Qué pasa:** El archivo dice `Disallow:` vacío, es decir, permite rastrear todo el sitio, incluyendo `/mi-cuenta` y `/admin` (aunque esas rutas de todas formas exigen login y no muestran contenido real a un rastreador). No hay línea `Sitemap:` porque tampoco existe un sitemap generado.
**Por qué importa:** Impacto bajo porque las rutas privadas no exponen contenido sin sesión, pero es una buena práctica estándar de SEO que hoy falta.
**Cómo se arreglaría:** Agregar `Disallow: /mi-cuenta` y `Disallow: /admin` al robots.txt, y si se decide generar un sitemap (por ejemplo con el paquete `spatie/laravel-sitemap`), referenciarlo ahí.
**Qué se hizo:** `robots.txt` ahora bloquea `/admin`, `/mi-cuenta`, `/settings` y `/reservar-clase` (se revisaron todas las rutas con `route:list`). No se agregó línea de `Sitemap:` porque todavía no existe ningún sitemap generado — **queda pendiente** si el equipo decide crear uno.

### ⏳ Pendiente (riesgo bajo, aceptado por ahora) — Resumen de compra accesible por `session_id` en la URL
**Archivo:** `app/Actions/Cart/GetCheckoutSessionSummaryAction.php`, usado desde `app/Http/Controllers/CartController.php:98`
**Qué pasa:** La página "gracias por tu compra" (`/carrito/gracias?session_id=...`) usa el `session_id` de la URL para pedirle a Stripe el resumen (cursos comprados, total, correo) y mostrarlo. Ese `session_id` es generado por Stripe, es largo y prácticamente imposible de adivinar, así que es el mismo patrón que la propia documentación de Stripe recomienda.
**Por qué importa:** Si ese `session_id` se llegara a filtrar (por ejemplo, alguien comparte una captura de pantalla de la URL completa, o queda en un log de un proxy intermedio), quien lo tenga podría ver el resumen de esa compra ajena. Es un riesgo bajo y aceptado por el propio ecosistema de Stripe, pero vale la pena que lo sepas.
**Cómo se arreglaría:** No requiere cambio ahora. Si en algún momento quieres cerrarlo del todo, se podría exigir que el usuario esté logueado y sea dueño de la orden para ver esa página, en vez de confiar solo en el `session_id`.

### ✅ Verificado sin hallazgos — Pagos
- Todo el dinero se maneja en centavos enteros de principio a fin: Stripe recibe `unit_amount` directamente desde `Course::price_cents` (servidor), nunca desde el navegador (`app/Http/Controllers/CartController.php:70-79`).
- El webhook verifica la firma de Stripe (`StripeWebhookController.php:17-24`) y es idempotente: si Stripe reintenta la misma entrega, no se duplica la orden ni el acceso (`CreateOrderFromStripeCheckoutSessionAction.php:26-28`, cubierto por el test `tests/Feature/Orders/StripeWebhookTest.php:211`).
- `CourseAccess` se otorga una sola vez por curso comprado, dentro de una transacción (`CreateOrderFromStripeCheckoutSessionAction.php:41-86`).
- Si el correo del comprador invitado ya tiene cuenta, la compra se asocia a esa cuenta en vez de crear una duplicada — comportamiento esperado y testeado, no un bug.

### ✅ Verificado sin hallazgos — Acceso a guías y video
- Las páginas de las guías se guardan en el disco `local` (`storage/app/private/guides/...`), que no tiene URL pública ni symlink — no es adivinable desde fuera (confirmé que `storage:link` no aplica aquí porque nada en el proyecto usa el disco `public`).
- Cada página se sirve solo tras `abort_unless($request->user()->hasAccessTo($course), 403)` (`MyLearningController.php:50`), con cache privado (`Cache-Control: private, no-store`). Cubierto por `tests/Feature/Guides/GuidePageAccessTest.php` (invitado → login, sin acceso → 403, acceso revocado → 403, acceso activo → 200).
- Los videos de Vimeo (de cursos y de lecciones) se resuelven solo después del mismo chequeo de acceso, y `ResolveVimeoEmbedUrlAction` valida que la URL venga de un dominio de Vimeo permitido antes de construir el embed.
- La reserva de asesorías con Calendly valida que las URLs de evento/invitado sean del dominio `api.calendly.com` (evita que alguien mande una URL arbitraria al servidor), valida que el correo del invitado coincida con el de la cuenta, y verifica acceso y sesiones restantes antes de confirmar.

### ✅ Verificado sin hallazgos — Seguridad general
- Sin inyección SQL: no hay `DB::raw`, `whereRaw`, `DB::statement` ni concatenaciones en consultas.
- Sin redirecciones abiertas: todos los `redirect()` usan rutas fijas o URLs generadas por el propio servidor (Stripe), nunca un parámetro del request.
- CSRF activo por defecto en todo el sitio, con la única excepción necesaria (`stripe/webhook`, declarada explícitamente en `bootstrap/app.php:15`).
- Rate limiting de login: 5 intentos por minuto por combinación correo+IP (`app/Providers/FortifyServiceProvider.php:66-70`), el valor por defecto de Laravel/Fortify.
- `/admin` solo es accesible para usuarios con `role=admin` (`app/Models/User.php:71-74`).
- Todos los modelos usan `#[Fillable]` con listas explícitas (ningún `$guarded = []`), y no encontré ningún `::create($request->all())` ni similar.
- Nada de `dd()`, `dump()`, `var_dump()`, `console.log` ni comentarios `TODO`/`FIXME` olvidados en el código de la aplicación.

## 3. Checklist de despliegue

En orden:

1. **Confirmar en hPanel que PHP 8.3 o superior está disponible** (el `composer.json` exige `^8.3`; usar 8.4 si Hostinger ya lo soporta, si no, 8.3) y que las extensiones estándar de Laravel están activas (mbstring, openssl, pdo_mysql, bcmath, ctype, curl, fileinfo, tokenizer, xml).
2. Subir el código al servidor (git clone/pull vía SSH, que Hostinger Business sí soporta).
3. Copiar `.env.example` a `.env` y completar las variables de producción (lista en la sección 4).
   ```
   cp .env.example .env
   ```
4. Instalar dependencias de PHP **sin** paquetes de desarrollo:
   ```
   composer install --no-dev --optimize-autoloader
   ```
5. Generar la clave de la app si `APP_KEY` quedó vacío:
   ```
   php artisan key:generate --force
   ```
6. Correr las migraciones — **nunca** `--seed` en producción (ver hallazgo del seeder):
   ```
   php artisan migrate --force
   ```
7. Compilar los assets del frontend. `public/build` está en `.gitignore` (no se sube al repo), así que hay que compilarlo en el servidor o subir esa carpeta ya compilada manualmente:
   ```
   npm install
   npm run build
   ```
8. Cachear configuración, rutas y vistas para producción:
   ```
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
9. Crear el primer usuario administrador. No existe ningún seeder ni comando para esto — el rol por defecto de cualquier cuenta nueva es `student` (`database/migrations/..._create_users_table.php:15`). Hay que registrarse normalmente en el sitio y luego promoverse a admin a mano. **Importante:** ya no sirve `->update(['role' => 'admin'])` — `role` se sacó de los campos asignables en masa (ver hallazgo resuelto en la sección 2) para que nadie pueda autoasignarse el rol por accidente. Hay que asignarlo directo a la propiedad, así:
   ```
   php artisan tinker
   >>> $u = App\Models\User::where('email', 'tu-correo@influessenceacademy.com')->first();
   >>> $u->role = App\Enums\UserRole::Admin;
   >>> $u->save();
   ```
10. Configurar un cron job en hPanel (necesario para que corra `courses:expire-advisories`, el único comando programado del proyecto — `routes/console.php:10`):
    ```
    * * * * * php /ruta/al/proyecto/artisan schedule:run >> /dev/null 2>&1
    ```
11. En el dashboard de Stripe, crear el endpoint de webhook apuntando a `https://tu-dominio.com/stripe/webhook`, copiar el "signing secret" que genera y ponerlo en `STRIPE_WEBHOOK_SECRET` en el `.env` de producción.
12. Verificar que el dominio tenga SSL activo (HTTPS) en Hostinger y que `APP_URL` en `.env` use `https://`.
13. Hacer una compra de prueba real de principio a fin (con una tarjeta de prueba de Stripe si ya se configuraron claves live con modo test, o con un monto mínimo) y confirmar: que llega el correo a `info@influessenceacademy.com`, que la clienta recibe su acceso, y que el correo de verificación/reset de contraseña llega vía Resend.

## 4. Variables de entorno para producción

| Variable | Para qué sirve |
|---|---|
| `APP_NAME` | Nombre que se muestra en el `<title>` y en los correos. |
| `APP_ENV` | Debe ser `production` — activa comportamientos más estrictos (contraseñas más largas, bloqueo de comandos destructivos). |
| `APP_KEY` | Clave de cifrado de la app (sesiones, datos encriptados). Se genera con `artisan key:generate`. |
| `APP_DEBUG` | Debe ser `false` — si queda en `true`, cualquier error muestra detalles internos (rutas de archivos, consultas) a quien visite el sitio. |
| `APP_URL` | URL pública del sitio (`https://influessenceacademy.com`), usada para generar links en correos y en Stripe. |
| `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | Conexión a la base de datos MySQL real de Hostinger. |
| `SESSION_DRIVER` | Dónde se guardan las sesiones de los usuarios logueados (`database`, como en local). |
| `SESSION_SECURE_COOKIE` | Poner `true` explícitamente — obliga a que la cookie de sesión solo viaje por HTTPS. |
| `QUEUE_CONNECTION` | Debe quedar en `sync` según lo decidido (sin worker) — confirmar que no quede en `database` por error, o el correo de nueva compra nunca se enviará (ver hallazgo arriba). |
| `CACHE_STORE` | Dónde se guarda la caché de la app (`database`, como en local). |
| `MAIL_MAILER` | Debe ser `resend` en producción (en local está en `log`, que solo escribe los correos en un archivo). |
| `RESEND_KEY` | Clave de API de Resend para el envío real de correos. |
| `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME` | Remitente que verán las usuarias en los correos transaccionales. |
| `STRIPE_KEY` | Clave pública de Stripe (hoy no se usa en el frontend porque el checkout es hospedado, pero Laravel la espera configurada). |
| `STRIPE_SECRET` | Clave secreta de Stripe — crea las sesiones de pago desde el servidor. |
| `STRIPE_WEBHOOK_SECRET` | Secreto del webhook de Stripe — sin esto, el webhook rechaza todos los eventos por firma inválida. |
| `CALENDLY_TOKEN` | Token de la API de Calendly para confirmar reservas. |
| `CALENDLY_SCHEDULING_URL` | URL del calendario de Calendly que se muestra a las estudiantes. |
| `LOG_LEVEL` | Recomendado `error` o `warning` en producción (en local está en `debug`, que es muy detallado). |

## 5. Pendientes que no son de código

Esto no lo puedo resolver yo — queda en tus manos o depende de alguien fuera del código:

- **Variables de `.env` de producción**: la tabla completa está en la sección 4. Las más fáciles de olvidar: `QUEUE_CONNECTION=sync` (si no, el correo de "nueva compra" se queda sin enviar en silencio — ver hallazgo arriba) y correr `php artisan config:clear` después de cualquier cambio al `.env` del servidor si ya habías corrido `config:cache` antes (si no, Laravel sigue leyendo los valores viejos cacheados).
- **ID del contenedor de Google Tag Manager**: cuando Eli y Fabiola tengan el contenedor (creado con el correo oficial de la agencia), esa es la variable `GTM_CONTAINER_ID` en el `.env` de producción. En cuanto se llena, el script empieza a aparecer solo — no hace falta tocar código.
- **La foto para compartir en redes (`og:image`)**: no existe todavía. El mecanismo ya está listo (la etiqueta se imprime en cuanto cualquier página le pase una URL de imagen), pero hace falta que el equipo elija o genere esa foto en JPG (según la convención que ya está en `CLAUDE.md`) y me digan a qué página(s) va.
- **Textos para validar con Eli y Fabiola**: solo hay uno realmente nuevo — la descripción genérica de respaldo en `resources/views/partials/head.blade.php` ("Influessence Academy es la academia online de Fabiola Colmenares: cursos para crear contenido, construir tu marca personal y monetizar en redes sociales."). Casi nunca se va a mostrar (todas las páginas que se tocaron ya tienen su propia descripción), pero la marco porque es texto que yo escribí, no texto que ya existiera en el sitio.
- **Decisión de equipo sobre Policies de `Order` y `Booking`**: se resolvió lo de `Course`. Si más adelante quieren Policies también para esos dos modelos, es una decisión aparte (hoy no hay ningún comportamiento que migrar ahí).

## 6. Lo que no pude verificar

- **Comportamiento real en Hostinger**: no tengo forma de confirmar desde aquí si el plan Business realmente soporta cron jobs, qué versión exacta de PHP y qué extensiones vienen activas por defecto, ni si hay algún proxy/balanceador delante de la app que afecte la detección de HTTPS. Hay que probarlo en vivo.
- **Envío real de correos vía Resend**: en este entorno local `MAIL_MAILER=log`, así que no pude probar que el envío real funcione con una clave de Resend válida.
- **Stripe en modo live**: no pude probar el webhook con claves y eventos reales de producción, solo con las pruebas automatizadas que simulan la respuesta de Stripe.
- **Carga/rendimiento real**: la suite de tests corre contra una base de datos de prueba y no simula tráfico ni concurrencia real.
- **Si Hostinger permite correr `npm run build` vía SSH** en el mismo servidor, o si hace falta compilar en otro lado y subir la carpeta `public/build` manualmente — depende del plan contratado y no lo pude confirmar desde el código.
