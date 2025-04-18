# Documentación Técnica - Mars Rover Mission

## Language / Idioma / Idioma
- [English](/TECHNICAL_DOCUMENTATION.md)
- [Español (actual)](#documentación-técnica---mars-rover-mission)
- [Català](/DOCUMENTACIO_TECNICA.md)

## Documentación Visual

Una demostración en video del proyecto está disponible en YouTube, que muestra la navegación del rover, la detección de obstáculos y las interacciones de la interfaz de usuario en tiempo real. Esta documentación visual complementa la documentación técnica al proporcionar una vista práctica de cómo funciona el sistema.

[![Demostración de Mars Rover Mission](https://img.youtube.com/vi/c4FEZmLOL2w/0.jpg)](https://youtu.be/c4FEZmLOL2w)

La documentación visual es crucial para proyectos interactivos complejos como este porque:

1. Demuestra el flujo de la interfaz de usuario y las interacciones
2. Muestra cómo se mueve y rota el rover en tiempo real
3. Ilustra la detección de obstáculos y el manejo de límites del mapa
4. Proporciona a los interesados una comprensión clara de la funcionalidad del proyecto

## Componentes Vue.js

### Mars.vue

El componente principal que representa la simulación del rover en Marte. Este componente maneja la lógica de visualización y control del rover.

#### Props
- `initialX` (Number): Coordenada X inicial del rover.
- `initialY` (Number): Coordenada Y inicial del rover.
- `initialDirection` (String): Dirección inicial del rover ('N', 'E', 'S', 'W').

#### Data
- `size` (Number): Tamaño total del mapa (200x200).
- `viewportSize` (Number): Tamaño de la ventana de visualización (11x11).
- `rover` (Object): Objeto que contiene la posición actual y dirección del rover.
  - `x` (Number): Coordenada X actual.
  - `y` (Number): Coordenada Y actual.
  - `direction` (String): Dirección actual ('N', 'E', 'S', 'W').
- `commandSequence` (String): Secuencia de comandos ingresada por el usuario.
- `errorMessage` (String): Mensaje de error para mostrar al usuario.
- `grid` (Array): Matriz bidimensional que representa la superficie de Marte con obstáculos.
- `directions` (Array): Array de posibles direcciones ['N', 'E', 'S', 'W'].
- `rotationDegrees` (Number): Grados de rotación actual para la imagen del rover.
- `isAnimating` (Boolean): Indica si una animación está en progreso.
- `savedSuccessMessage` (String): Mensaje de éxito al guardar la posición.
- `saveTimeout` (Timer): Temporizador para controlar el tiempo que se muestra el mensaje de éxito.
- `showCellCoords` (Boolean): Controla si se muestran las coordenadas dentro de las celdas.

#### Métodos

##### Visualización del Mapa
- `getVisibleRows()`: Calcula las filas (coordenadas Y) visibles en la ventana actual.
- `getVisibleCols()`: Calcula las columnas (coordenadas X) visibles en la ventana actual.
- `isOutOfBounds(x, y)`: Verifica si unas coordenadas están fuera de los límites del mapa.
- `updateRotationFromDirection()`: Actualiza los grados de rotación basados en la dirección actual.
- `generateGrid()`: Genera la cuadrícula con obstáculos aleatorios.
- `isObstacle(x, y)`: Verifica si hay un obstáculo en las coordenadas especificadas.
- `isRover(x, y)`: Verifica si el rover está en las coordenadas especificadas.

##### Control del Rover
- `moveRover()`: Procesa la secuencia de comandos ingresada por el usuario.
- `executeCommandsSequentially(commands, index)`: Ejecuta comandos secuencialmente con animaciones.
- `moveForward()`: Mueve el rover una unidad hacia adelante en la dirección actual.
- `turnLeft()`: Gira el rover 90 grados en sentido antihorario.
- `turnRight()`: Gira el rover 90 grados en sentido horario.
- `savePosition()`: Guarda la posición actual del rover a través de la API.

#### Computed Properties
- `roverRotationStyle`: Calcula el estilo CSS para la rotación del rover.

#### Lifecycle Hooks
- `created()`: Genera el mapa y establece la rotación inicial.

## Integración con Laravel

### API Endpoints

#### POST /api/rover/save-position
Guarda la posición y dirección actual del rover en la base de datos.

**Payload:**
```json
{
  "x": 10,
  "y": 15,
  "direction": "N"
}
```

**Respuesta exitosa:**
```json
{
  "success": true
}
```

## Algoritmos Implementados

### Algoritmo de Generación de Obstáculos
```javascript
generateGrid() {
    this.grid = [];
    for (let i = 1; i <= this.size; i++) {
        const row = [];
        for (let j = 1; j <= this.size; j++) {
            // No colocar obstáculo en la posición inicial del rover
            if (j === this.rover.x && i === this.rover.y) {
                row.push(false);
            } else {
                // 15% de probabilidad de obstáculo
                row.push(Math.random() < 0.15);
            }
        }
        this.grid.push(row);
    }
}
```

### Algoritmo de Cálculo de Ventana Visible
```javascript
getVisibleRows() {
    const halfViewport = Math.floor(this.viewportSize / 2);
    const minRow = Math.max(1, this.rover.y - halfViewport);
    const rows = [];
    
    for (let i = 0; i < this.viewportSize; i++) {
        const row = minRow + i;
        if (row <= this.size) {
            rows.push(row);
        }
    }
    
    return rows;
}

getVisibleCols() {
    const halfViewport = Math.floor(this.viewportSize / 2);
    const minCol = Math.max(1, this.rover.x - halfViewport);
    const cols = [];
    
    for (let i = 0; i < this.viewportSize; i++) {
        const col = minCol + i;
        if (col <= this.size) {
            cols.push(col);
        }
    }
    
    return cols;
}
```

### Algoritmo de Movimiento del Rover
```javascript
moveForward() {
    const { direction } = this.rover;
    
    // Calcular nueva posición basada en la dirección
    let newX = this.rover.x;
    let newY = this.rover.y;
    
    // Posición sin restricciones (para detectar si sale del mapa)
    let rawNewX = this.rover.x;
    let rawNewY = this.rover.y;
    
    // X es columna (horizontal), Y es fila (vertical)
    // En un tablero, Y aumenta hacia abajo
    switch (direction) {
        case 'N': // Norte (arriba) - Disminuye Y (fila)
            rawNewY = this.rover.y - 1;
            newY = Math.max(1, rawNewY);
            break;
        case 'S': // Sur (abajo) - Aumenta Y (fila)
            rawNewY = this.rover.y + 1;
            newY = Math.min(this.size, rawNewY);
            break;
        case 'E': // Este (derecha) - Aumenta X (columna)
            rawNewX = this.rover.x + 1;
            newX = Math.min(this.size, rawNewX);
            break;
        case 'W': // Oeste (izquierda) - Disminuye X (columna)
            rawNewX = this.rover.x - 1;
            newX = Math.max(1, rawNewX);
            break;
    }
    
    // Verificar si intenta moverse fuera del mapa
    if (rawNewX < 1 || rawNewX > this.size || rawNewY < 1 || rawNewY > this.size) {
        this.errorMessage = "WARNING! We are on a really weird planet that is square. We are not able to move out of the planet.";
        return false;
    }
    
    // Verificar si hay un obstáculo
    if (!this.isObstacle(newX, newY)) {
        this.rover.x = newX;
        this.rover.y = newY;
        
        // Guardar posición después de moverse
        this.savePosition();
        
        return true;
    } else {
        this.errorMessage = 'Obstacle detected at coordinates (' + newX + ', ' + newY + '). Cannot move in that direction.';
        return false;                   
    }
}
```

## Patrones de Diseño Utilizados

### Patrón Observador
Vue.js implementa internamente el patrón observador para la reactividad. Cuando las propiedades del componente cambian (como la posición del rover), la interfaz se actualiza automáticamente.

### Patrón Command
El sistema de comandos del rover (F, L, R) implementa una versión simplificada del patrón Command, donde cada comando encapsula una acción específica.

## Optimizaciones de Rendimiento

### Renderizado Condicional
Solo se renderizan las celdas visibles en la ventana actual (11x11), en lugar de intentar renderizar todo el mapa de 200x200, lo que mejoraría significativamente el rendimiento.

```vue
<div
    v-for="i in getVisibleRows()"
    :key="i"
    class="row"
>
    <div
        v-for="j in getVisibleCols()"
        :key="j"
        class="cell"
        :class="{...}"
    >
        <!-- Contenido de la celda -->
    </div>
</div>
```

### Animaciones Eficientes
Las animaciones utilizan CSS para la rotación, aprovechando la aceleración por hardware cuando está disponible:

```javascript
roverRotationStyle() {
    return {
        transform: `rotate(${this.rotationDegrees}deg)`,
        transition: 'transform 0.3s ease'
    };
}
```

## Características de Accesibilidad

- Contraste de color para facilitar la lectura
- Elementos visuales que complementan las indicaciones textuales
- Mensajes de error claros y visibles
- Interfaces responsivas para diferentes tamaños de pantalla

## Manejo de Estados Asíncronos

### Guardado de Posición
```javascript
savePosition() {
    const position = {
        x: this.rover.x,
        y: this.rover.y,
        direction: this.rover.direction
    };
    
    axios.post('/api/rover/save-position', position)
        .then(response => {
            if (response.data.success) {
                this.savedSuccessMessage = 'Position saved successfully';
                
                // Borrar mensaje después de 3 segundos
                if (this.saveTimeout) {
                    clearTimeout(this.saveTimeout);
                }
                
                this.saveTimeout = setTimeout(() => {
                    this.savedSuccessMessage = '';
                }, 3000);
            }
        })
        .catch(error => {
            this.errorMessage = 'Error saving position: ' + error.message;
        });
}
```