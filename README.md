# APP Gestión de Tareas

Ya está lista para ejecutar. La página principal es una bienvenida simple que muestra las opciones disponibles: **Enlistar Tareas** y **Crear Nueva Tarea**.

## Enlistar Tareas
Al entrar a *Enlistar Tareas*, inmediatamente se listan todas las tareas empezando por la más reciente, mostrando el **título**, la **descripción** y las **keywords** de cada tarea.

## Crear Nueva Tarea
Como campo obligatorio está el **título** de la tarea. La tarea creada aparecerá de primera en el listado.

## Buenas prácticas

### Ruteo
Se dividen las rutas para especificar responsabilidades:
- `tareasRoutes` se encarga solo de lo referente a **tareas**.

### Componentes Vue
Aunque en las instrucciones se pedía un solo componente `TaskList.vue` para enlistar y crear, se dividió en componentes separados para **reutilización de código** y **escalabilidad**.

### DataSeeder
La mayoría de los datos se generan automáticamente para contar con una base de datos de prueba desde el principio.

## Notas

### Relación muchos a muchos
Una tarea puede tener varias **keywords** y una **keyword** puede pertenecer a varias tareas. Por eso se implementó una relación **N:N** con una tabla pivote.

## Cómo ejecutar

- **Backend:** `php artisan serve`
- **Frontend:** `npm run dev`
