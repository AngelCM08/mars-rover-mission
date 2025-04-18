# Mars Rover Mission

## Language / Idioma / Idioma
- [English](/README.md)
- [Español (actual)](#mars-rover-mission)
- [Català](/README_CA.md)

## Descripción del Proyecto

Este proyecto implementa una simulación interactiva del rover marciano, permitiendo a los usuarios controlar un rover en una superficie marciana representada como una cuadrícula. El proyecto está construido utilizando Laravel para el backend y Vue.js para el frontend, con una interfaz gráfica interactiva que muestra la posición y dirección del rover.

## Demostración

[![Demostración de Mars Rover Mission](https://img.youtube.com/vi/c4FEZmLOL2w/0.jpg)](https://youtu.be/c4FEZmLOL2w)

Haz clic en la imagen de arriba para ver una demostración del funcionamiento de Mars Rover Mission.

## Funcionalidades Principales

- Visualización de una superficie marciana como cuadrícula con obstáculos generados aleatoriamente
- Control del rover mediante comandos simples (F: Avanzar, L: Girar Izquierda, R: Girar Derecha)
- Detección de obstáculos y límites del mapa
- Seguimiento y visualización en tiempo real de la posición y orientación del rover
- Persistencia de datos mediante guardado automático de la posición del rover
- Interfaz de usuario responsiva y amigable

## Estructura del Proyecto

### Frontend (Vue.js)

El componente principal `Mars.vue` se encarga de la visualización y la lógica de interacción con el usuario:

- **Visualización del Mapa**: Una cuadrícula que muestra la superficie marciana con indicadores de dirección (N, S, E, W) y coordenadas en los márgenes.
- **Representación del Rover**: Imagen de un rover con rotación dinámica según su dirección.
- **Panel de Control**: Permite al usuario introducir comandos y ejecutarlos.
- **Panel de Información**: Muestra las coordenadas actuales y la dirección del rover.

### Backend (Laravel)

El backend proporciona APIs para:
- Guardar la posición del rover (`/api/rover/save-position`)
- Recuperar la posición guardada del rover
- Gestionar la lógica de obstáculos y límites del mapa

## Algoritmo de Movimiento del Rover

El algoritmo de movimiento del rover implementa las siguientes reglas:

1. **Avanzar (F)**: El rover se mueve una unidad en la dirección actual.
   - Si hay un obstáculo en la casilla de destino, el movimiento se cancela y se muestra un mensaje de error.
   - Si el movimiento llevaría al rover fuera de los límites del mapa, se muestra un mensaje de advertencia.

2. **Girar Izquierda (L)**: El rover rota 90 grados en sentido antihorario.
   - N → W → S → E → N

3. **Girar Derecha (R)**: El rover rota 90 grados en sentido horario.
   - N → E → S → W → N

Cada movimiento o giro se anima visualmente para dar retroalimentación al usuario.

## Características Técnicas

### Generación del Mapa

- El mapa es una cuadrícula de 200x200 celdas.
- Los obstáculos se generan aleatoriamente con una probabilidad del 15% por celda.
- Se utiliza una ventana de visualización de 11x11 celdas centrada en el rover para optimizar el rendimiento.

### Animaciones

- Rotación suave del rover al girar (usando transiciones CSS).
- Feedback visual para comandos válidos e inválidos.

### Persistencia de Datos

- La posición del rover se guarda automáticamente después de cada movimiento mediante llamadas a la API.
- Se muestra una notificación de éxito al guardar.

## Optimización y Rendimiento

- Renderizado condicional de celdas visible para mejorar el rendimiento.
- Cálculo dinámico de coordenadas visibles en función de la posición del rover.
- Indicadores de coordenadas en los márgenes del mapa para facilitar la orientación sin sobrecargar la interfaz.

## Consideraciones de UX/UI

- Indicadores claros de dirección (N, S, E, W) para facilitar la orientación.
- Interfaz intuitiva con retroalimentación visual para cada acción.
- Rover con rotación que indica visualmente su dirección (el brazo robótico apunta en la dirección de avance).
- Diseño responsivo que se adapta a diferentes tamaños de pantalla.
- Mensajes de error claros y formateo visual consistente.

## Tecnologías Utilizadas

- **Frontend**: Vue.js, CSS3, HTML5
- **Backend**: Laravel, PHP
- **Comunicación**: Axios para llamadas API REST
- **Estilo**: CSS personalizado con variables para consistencia de colores

## Arquitectura

El proyecto sigue una arquitectura cliente-servidor:

1. **Cliente** (Vue.js):
   - Maneja la interacción del usuario y la visualización
   - Implementa la lógica de movimiento del rover
   - Se comunica con el servidor para persistencia de datos

2. **Servidor** (Laravel):
   - Gestiona la persistencia de datos
   - Proporciona APIs para las operaciones del rover

## Instalación y Configuración

### Requisitos previos

- PHP 8.0 o superior
- Composer
- Node.js y npm
- Base de datos MySQL o compatible

### Pasos de instalación

1. Clonar el repositorio:
   ```
   git clone https://github.com/tu-usuario/mars-rover-mission.git
   cd mars-rover-mission
   ```

2. Instalar dependencias de PHP:
   ```
   composer install
   ```

3. Instalar dependencias de JavaScript:
   ```
   npm install
   ```

4. Configurar el entorno:
   - Copiar `.env.example` a `.env`
   - Configurar la conexión a la base de datos en `.env`

5. Generar clave de aplicación:
   ```
   php artisan key:generate
   ```

6. Ejecutar migraciones:
   ```
   php artisan migrate
   ```

7. Compilar recursos frontend:
   ```
   npm run dev
   ```

8. Iniciar el servidor:
   ```
   php artisan serve
   ```

## Decisiones de Implementación

### Enfoque de Diseño

Se ha priorizado una interfaz limpia y funcional, con retroalimentación visual clara sobre el estado del rover y su entorno. Los indicadores de coordenadas en los márgenes permiten a los usuarios orientarse sin sobrecargar visualmente el mapa.

### Optimización de Rendimiento

Para manejar un mapa grande (200x200) de manera eficiente, solo se renderiza una ventana de visualización de 11x11 alrededor del rover. Esto permite una experiencia fluida incluso en dispositivos menos potentes.

### Gestión de Errores

Se implementa una gestión de errores robusta con mensajes claros para:
- Obstáculos detectados
- Intentos de salida del mapa
- Comandos inválidos
- Errores de comunicación con el servidor

## Futuras Mejoras

- Implementación de diferentes tipos de terreno con efectos sobre el movimiento
- Modo multijugador con varios rovers
- Sistema de misiones y objetivos
- Estadísticas de movimiento y eficiencia
- Soporte para dispositivos táctiles con controles gestuales

## Conclusión

Este proyecto demuestra habilidades en:
- Desarrollo frontend con Vue.js
- Diseño de interfaces responsivas
- Implementación de lógica de negocio compleja
- Comunicación cliente-servidor
- Manejo de estados y transiciones
- Optimización de rendimiento

La simulación del Mars Rover representa un desafío técnico interesante que combina algoritmia, diseño de interfaz y programación orientada a objetos en un contexto práctico y visualmente atractivo. 