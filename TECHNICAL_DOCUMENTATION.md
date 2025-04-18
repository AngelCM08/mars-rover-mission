# Technical Documentation - Mars Rover Mission

## Language / Idioma / Idioma
- [English (current)](#technical-documentation---mars-rover-mission)
- [Español](/DOCUMENTACION_TECNICA.md)
- [Català](/DOCUMENTACIO_TECNICA.md)

## Visual Documentation

A video demonstration of the project is available on YouTube, which shows the rover navigation, obstacle detection, and user interface interactions in real-time. This visual documentation complements the technical documentation by providing a practical view of how the system works.

[![Mars Rover Mission Demo](https://img.youtube.com/vi/c4FEZmLOL2w/0.jpg)](https://youtu.be/c4FEZmLOL2w)

Visual documentation is crucial for complex interactive projects like this one because:

1. It demonstrates the user interface flow and interactions
2. It shows how the rover moves and rotates in real-time
3. It illustrates obstacle detection and boundary handling
4. It gives stakeholders a clear understanding of the project functionality

## Vue.js Components

### Mars.vue

The main component that represents the rover simulation on Mars. This component handles the logic for visualization and rover control.

#### Props
- `initialX` (Number): Initial X coordinate of the rover.
- `initialY` (Number): Initial Y coordinate of the rover.
- `initialDirection` (String): Initial direction of the rover ('N', 'E', 'S', 'W').

#### Data
- `size` (Number): Total map size (200x200).
- `viewportSize` (Number): Size of the visualization window (11x11).
- `rover` (Object): Object containing the current position and direction of the rover.
  - `x` (Number): Current X coordinate.
  - `y` (Number): Current Y coordinate.
  - `direction` (String): Current direction ('N', 'E', 'S', 'W').
- `commandSequence` (String): Command sequence entered by the user.
- `errorMessage` (String): Error message to display to the user.
- `grid` (Array): Two-dimensional array representing the Mars surface with obstacles.
- `directions` (Array): Array of possible directions ['N', 'E', 'S', 'W'].
- `rotationDegrees` (Number): Current rotation degrees for the rover image.
- `isAnimating` (Boolean): Indicates if an animation is in progress.
- `savedSuccessMessage` (String): Success message when saving position.
- `saveTimeout` (Timer): Timer to control how long the success message is displayed.
- `showCellCoords` (Boolean): Controls whether coordinates are shown within cells.

#### Methods

##### Map Visualization
- `getVisibleRows()`: Calculates the rows (Y coordinates) visible in the current window.
- `getVisibleCols()`: Calculates the columns (X coordinates) visible in the current window.
- `isOutOfBounds(x, y)`: Checks if coordinates are outside the map boundaries.
- `updateRotationFromDirection()`: Updates rotation degrees based on the current direction.
- `generateGrid()`: Generates the grid with random obstacles.
- `isObstacle(x, y)`: Checks if there is an obstacle at the specified coordinates.
- `isRover(x, y)`: Checks if the rover is at the specified coordinates.

##### Rover Control
- `moveRover()`: Processes the command sequence entered by the user.
- `executeCommandsSequentially(commands, index)`: Executes commands sequentially with animations.
- `moveForward()`: Moves the rover one unit forward in the current direction.
- `turnLeft()`: Rotates the rover 90 degrees counterclockwise.
- `turnRight()`: Rotates the rover 90 degrees clockwise.
- `savePosition()`: Saves the current rover position through the API.

#### Computed Properties
- `roverRotationStyle`: Calculates the CSS style for rover rotation.

#### Lifecycle Hooks
- `created()`: Generates the map and sets the initial rotation.

## Laravel Integration

### API Endpoints

#### POST /api/rover/save-position
Saves the current position and direction of the rover in the database.

**Payload:**
```json
{
  "x": 10,
  "y": 15,
  "direction": "N"
}
```

**Successful response:**
```json
{
  "success": true
}
```

## Implemented Algorithms

### Obstacle Generation Algorithm
```javascript
generateGrid() {
    this.grid = [];
    for (let i = 1; i <= this.size; i++) {
        const row = [];
        for (let j = 1; j <= this.size; j++) {
            // Don't place obstacle at rover's initial position
            if (j === this.rover.x && i === this.rover.y) {
                row.push(false);
            } else {
                // 15% chance of obstacle
                row.push(Math.random() < 0.15);
            }
        }
        this.grid.push(row);
    }
}
```

### Visible Window Calculation Algorithm
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

### Rover Movement Algorithm
```javascript
moveForward() {
    const { direction } = this.rover;
    
    // Calculate new position based on direction
    let newX = this.rover.x;
    let newY = this.rover.y;
    
    // Unrestricted position (to detect if going off map)
    let rawNewX = this.rover.x;
    let rawNewY = this.rover.y;
    
    // X is column (horizontal), Y is row (vertical)
    // On a board, Y increases downward
    switch (direction) {
        case 'N': // North (up) - Decreases Y (row)
            rawNewY = this.rover.y - 1;
            newY = Math.max(1, rawNewY);
            break;
        case 'S': // South (down) - Increases Y (row)
            rawNewY = this.rover.y + 1;
            newY = Math.min(this.size, rawNewY);
            break;
        case 'E': // East (right) - Increases X (column)
            rawNewX = this.rover.x + 1;
            newX = Math.min(this.size, rawNewX);
            break;
        case 'W': // West (left) - Decreases X (column)
            rawNewX = this.rover.x - 1;
            newX = Math.max(1, rawNewX);
            break;
    }
    
    // Check if trying to move off the map
    if (rawNewX < 1 || rawNewX > this.size || rawNewY < 1 || rawNewY > this.size) {
        this.errorMessage = "WARNING! We are on a really weird planet that is square. We are not able to move out of the planet.";
        return false;
    }
    
    // Check if there's an obstacle
    if (!this.isObstacle(newX, newY)) {
        this.rover.x = newX;
        this.rover.y = newY;
        
        // Save position after moving
        this.savePosition();
        
        return true;
    } else {
        this.errorMessage = 'Obstacle detected at coordinates (' + newX + ', ' + newY + '). Cannot move in that direction.';
        return false;                   
    }
}
```

## Design Patterns Used

### Observer Pattern
Vue.js internally implements the observer pattern for reactivity. When component properties change (such as the rover's position), the interface updates automatically.

### Command Pattern
The rover command system (F, L, R) implements a simplified version of the Command pattern, where each command encapsulates a specific action.

## Performance Optimizations

### Conditional Rendering
Only cells visible in the current window (11x11) are rendered, instead of trying to render the entire 200x200 map, which significantly improves performance.

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
        <!-- Cell content -->
    </div>
</div>
```

### Efficient Animations
The animations use CSS for rotation, taking advantage of hardware acceleration when available:

```javascript
roverRotationStyle() {
    return {
        transform: `rotate(${this.rotationDegrees}deg)`,
        transition: 'transform 0.3s ease'
    };
}
```

## Accessibility Features

- Color contrast for easy reading
- Visual elements that complement text indications
- Clear and visible error messages
- Responsive interfaces for different screen sizes

## Asynchronous State Management

### Position Saving
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
                
                // Clear message after 3 seconds
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
