# Documentació Tècnica - Mars Rover Mission

## Language / Idioma / Idioma
- [English](/TECHNICAL_DOCUMENTATION.md)
- [Español](/DOCUMENTACION_TECNICA.md)
- [Català (actual)](#documentació-tècnica---mars-rover-mission)

## Documentació Visual

Una demostració en vídeo del projecte està disponible a YouTube, que mostra la navegació del rover, la detecció d'obstacles i les interaccions de la interfície d'usuari en temps real. Aquesta documentació visual complementa la documentació tècnica proporcionant una visió pràctica de com funciona el sistema.

[![Demostració de Mars Rover Mission](https://img.youtube.com/vi/c4FEZmLOL2w/0.jpg)](https://youtu.be/c4FEZmLOL2w)

La documentació visual és crucial per a projectes interactius complexos com aquest perquè:

1. Demostra el flux de la interfície d'usuari i les interaccions
2. Mostra com es mou i rota el rover en temps real
3. Il·lustra la detecció d'obstacles i la gestió de límits del mapa
4. Proporciona als interessats una comprensió clara de la funcionalitat del projecte

## Components Vue.js

### Mars.vue

El component principal que representa la simulació del rover a Mart. Aquest component gestiona la lògica de visualització i control del rover.

#### Props
- `initialX` (Number): Coordenada X inicial del rover.
- `initialY` (Number): Coordenada Y inicial del rover.
- `initialDirection` (String): Direcció inicial del rover ('N', 'E', 'S', 'W').

#### Data
- `size` (Number): Mida total del mapa (200x200).
- `viewportSize` (Number): Mida de la finestra de visualització (11x11).
- `rover` (Object): Objecte que conté la posició actual i direcció del rover.
  - `x` (Number): Coordenada X actual.
  - `y` (Number): Coordenada Y actual.
  - `direction` (String): Direcció actual ('N', 'E', 'S', 'W').
- `commandSequence` (String): Seqüència de comandes introduïda per l'usuari.
- `errorMessage` (String): Missatge d'error per mostrar a l'usuari.
- `grid` (Array): Matriu bidimensional que representa la superfície de Mart amb obstacles.
- `directions` (Array): Array de possibles direccions ['N', 'E', 'S', 'W'].
- `rotationDegrees` (Number): Graus de rotació actual per a la imatge del rover.
- `isAnimating` (Boolean): Indica si una animació està en progrés.
- `savedSuccessMessage` (String): Missatge d'èxit en desar la posició.
- `saveTimeout` (Timer): Temporitzador per controlar el temps que es mostra el missatge d'èxit.
- `showCellCoords` (Boolean): Controla si es mostren les coordenades dins de les cel·les.

#### Mètodes

##### Visualització del Mapa
- `getVisibleRows()`: Calcula les files (coordenades Y) visibles a la finestra actual.
- `getVisibleCols()`: Calcula les columnes (coordenades X) visibles a la finestra actual.
- `isOutOfBounds(x, y)`: Verifica si unes coordenades estan fora dels límits del mapa.
- `updateRotationFromDirection()`: Actualitza els graus de rotació basats en la direcció actual.
- `generateGrid()`: Genera la quadrícula amb obstacles aleatoris.
- `isObstacle(x, y)`: Verifica si hi ha un obstacle a les coordenades especificades.
- `isRover(x, y)`: Verifica si el rover està a les coordenades especificades.

##### Control del Rover
- `moveRover()`: Processa la seqüència de comandes introduïda per l'usuari.
- `executeCommandsSequentially(commands, index)`: Executa comandes seqüencialment amb animacions.
- `moveForward()`: Mou el rover una unitat endavant en la direcció actual.
- `turnLeft()`: Gira el rover 90 graus en sentit antihorari.
- `turnRight()`: Gira el rover 90 graus en sentit horari.
- `savePosition()`: Desa la posició actual del rover a través de l'API.

#### Computed Properties
- `roverRotationStyle`: Calcula l'estil CSS per a la rotació del rover.

#### Lifecycle Hooks
- `created()`: Genera el mapa i estableix la rotació inicial.

## Integració amb Laravel

### API Endpoints

#### POST /api/rover/save-position
Desa la posició i direcció actual del rover a la base de dades.

**Payload:**
```json
{
  "x": 10,
  "y": 15,
  "direction": "N"
}
```

**Resposta exitosa:**
```json
{
  "success": true
}
```

## Algoritmes Implementats

### Algoritme de Generació d'Obstacles
```javascript
generateGrid() {
    this.grid = [];
    for (let i = 1; i <= this.size; i++) {
        const row = [];
        for (let j = 1; j <= this.size; j++) {
            // No col·locar obstacle a la posició inicial del rover
            if (j === this.rover.x && i === this.rover.y) {
                row.push(false);
            } else {
                // 15% de probabilitat d'obstacle
                row.push(Math.random() < 0.15);
            }
        }
        this.grid.push(row);
    }
}
```

### Algoritme de Càlcul de Finestra Visible
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

### Algoritme de Moviment del Rover
```javascript
moveForward() {
    const { direction } = this.rover;
    
    // Calcular nova posició basada en la direcció
    let newX = this.rover.x;
    let newY = this.rover.y;
    
    // Posició sense restriccions (per detectar si surt del mapa)
    let rawNewX = this.rover.x;
    let rawNewY = this.rover.y;
    
    // X és columna (horitzontal), Y és fila (vertical)
    // En un tauler, Y augmenta cap avall
    switch (direction) {
        case 'N': // Nord (amunt) - Disminueix Y (fila)
            rawNewY = this.rover.y - 1;
            newY = Math.max(1, rawNewY);
            break;
        case 'S': // Sud (avall) - Augmenta Y (fila)
            rawNewY = this.rover.y + 1;
            newY = Math.min(this.size, rawNewY);
            break;
        case 'E': // Est (dreta) - Augmenta X (columna)
            rawNewX = this.rover.x + 1;
            newX = Math.min(this.size, rawNewX);
            break;
        case 'W': // Oest (esquerra) - Disminueix X (columna)
            rawNewX = this.rover.x - 1;
            newX = Math.max(1, rawNewX);
            break;
    }
    
    // Verificar si intenta moure's fora del mapa
    if (rawNewX < 1 || rawNewX > this.size || rawNewY < 1 || rawNewY > this.size) {
        this.errorMessage = "WARNING! We are on a really weird planet that is square. We are not able to move out of the planet.";
        return false;
    }
    
    // Verificar si hi ha un obstacle
    if (!this.isObstacle(newX, newY)) {
        this.rover.x = newX;
        this.rover.y = newY;
        
        // Desar posició després de moure's
        this.savePosition();
        
        return true;
    } else {
        this.errorMessage = 'Obstacle detected at coordinates (' + newX + ', ' + newY + '). Cannot move in that direction.';
        return false;                   
    }
}
```

## Patrons de Disseny Utilitzats

### Patró Observador
Vue.js implementa internament el patró observador per a la reactivitat. Quan les propietats del component canvien (com la posició del rover), la interfície s'actualitza automàticament.

### Patró Command
El sistema de comandes del rover (F, L, R) implementa una versió simplificada del patró Command, on cada comanda encapsula una acció específica.

## Optimitzacions de Rendiment

### Renderitzat Condicional
Només es renderitzen les cel·les visibles a la finestra actual (11x11), en lloc d'intentar renderitzar tot el mapa de 200x200, el que milloraria significativament el rendiment.

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
        <!-- Contingut de la cel·la -->
    </div>
</div>
```

### Animacions Eficients
Les animacions utilitzen CSS per a la rotació,充分利用硬件加速，当可用时：

```javascript
roverRotationStyle() {
    return {
        transform: `rotate(${this.rotationDegrees}deg)`,
        transition: 'transform 0.3s ease'
    };
}
```

## Característiques d'Accessibilitat

- Contrast de color per facilitar la lectura
- Elements visuals que complementen les indicacions textuals
- Missatges d'error clars i visibles
- Interfícies responsives per a diferents mides de pantalla

## Gestió d'Estats Asíncrons

### Desat de Posició
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
                
                // Esborrar missatge després de 3 segons
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
