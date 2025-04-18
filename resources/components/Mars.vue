<template>
    <div class="mars">
        <div class="mars-container">
            <div class="direction-indicator north">N</div>
            <div class="grid-wrapper">
                <div class="direction-indicator west">W</div>
                <div class="row-indicators">
                    <div
                        v-for="i in getVisibleRows()"
                        :key="i"
                        class="row-indicator"
                    >
                        {{ i }}
                    </div>
                </div>
                <div class="grid-container">
                    <div
                        v-for="i in getVisibleRows()"
                        :key="i"
                        class="row"
                    >
                        <div
                            v-for="j in getVisibleCols()"
                            :key="j"
                            class="cell"
                            :class="{
                                'obstacle': isObstacle(j, i),
                                'rover': isRover(j, i),
                                'out-of-bounds': isOutOfBounds(j, i)
                            }"
                        >
                            <div
                                v-if="isRover(j, i)"
                                class="rover"
                            ><img 
                                src="/public/img/rover.png" 
                                title="rover" 
                                class="rover-img"
                                :style="roverRotationStyle"
                            ></div>
                            <div
                                v-if="isObstacle(j, i) && !isOutOfBounds(j, i)"
                                class="obstacle"
                            ><img src="/public/img/obstacle.png" title="obstacle" class="obstacle-img"></div>
                            <span class="coords" :class="{'small-coords': j > 99 || i > 99}" v-if="showCellCoords">{{ j }},{{ i }}</span>
                        </div>
                    </div>
                </div>
                <div class="direction-indicator east">E</div>
            </div>
            <div class="col-indicators-container">
                <div class="col-indicators-offset"></div>
                <div class="col-indicators">
                    <div
                        v-for="j in getVisibleCols()"
                        :key="j"
                        class="col-indicator"
                    >
                        {{ j }}
                    </div>
                </div>
            </div>
            <div class="direction-indicator south">S</div>
        </div>
        <div class="direction-guide">
            <span class="direction-tip">Note: The rover will move in the direction its robotic arm is pointing</span>
        </div>
    </div>
    <div v-if="errorMessage" class="error-message">{{ errorMessage }}</div>
    <div class="rover-info">
        <div class="coords-info">
            <span class="info-label">Current coordinates:</span>
            <span class="info-value">X: {{ rover.x }}, Y: {{ rover.y }}</span>
        </div>
        <div class="direction-info">
            <span class="info-label">Current direction:</span>
            <span class="info-value">{{ rover.direction }}</span>
        </div>
        <div class="viewport-info">
            <span class="info-label">Viewport window:</span>
            <span class="info-value">11x11 around the rover ({{ size }}x{{ size }} total)</span>
        </div>
        <div v-if="savedSuccessMessage" class="save-success-message">
            {{ savedSuccessMessage }}
        </div>
    </div>
    <div class="controls">
        <div class="input-group">
            <input 
                type="text" 
                placeholder="Enter commands (F: Forward, L: Left, R: Right)" 
                v-model="commandSequence"
                class="command-input"
            >
            <div class="buttons-group">
                <button @click="moveRover" class="move-btn">Execute</button>
                <button @click="$emit('reset')" class="reset-btn">Reset mission</button>
                <button @click="savePosition" class="save-btn">Save position</button>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';

    export default {
        name: 'Mars',
        props: {
            initialX: {
                type: Number,
                required: true
            },
            initialY: {
                type: Number,
                required: true
            },
            initialDirection: {
                type: String,
                required: true,
                validator: (value) => ['N', 'E', 'S', 'W'].includes(value)
            }
        },
        data() {
            return {
                size: 200,
                viewportSize: 11, // Viewport window size
                rover: {
                    x: this.initialX,
                    y: this.initialY,
                    direction: this.initialDirection
                },
                commandSequence: '',
                errorMessage: '',
                grid: [],
                directions: ['N', 'E', 'S', 'W'],
                rotationDegrees: 0, // Current rotation in degrees
                isAnimating: false,
                savedSuccessMessage: '', // Success message for saving
                saveTimeout: null, // To control message timing
                showCellCoords: false, // Toggle for showing coordinates in cells
            }
        },
        created() {
            // Generate the board with obstacles once at startup
            this.generateGrid();
            // Set initial rotation based on direction
            this.updateRotationFromDirection();
        },
        computed: {
            roverRotationStyle() {
                return {
                    transform: `rotate(${this.rotationDegrees}deg)`,
                    transition: 'transform 0.3s ease'
                };
            }
        },
        methods: {
            // Calculate visible rows in the current window
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
            },
            
            // Calculate visible columns in the current window
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
            },
            
            // Check if a coordinate is out of Mars boundaries
            isOutOfBounds(x, y) {
                return x < 1 || x > this.size || y < 1 || y > this.size;
            },
            
            // Update rotation degrees based on current direction
            updateRotationFromDirection() {
                const directionToRotation = {
                    'E': 0,
                    'S': 90,
                    'W': 180,
                    'N': 270
                };
                this.rotationDegrees = directionToRotation[this.rover.direction];
            },
            
            generateGrid() {
                this.grid = [];
                for (let i = 1; i <= this.size; i++) { // i represents row (Y)
                    const row = [];
                    for (let j = 1; j <= this.size; j++) { // j represents column (X)
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
            },
            isObstacle(x, y) {
                // x is column, y is row
                // Check if out of bounds
                if (this.isOutOfBounds(x, y)) {
                    return false;
                }
                
                // Adjust for 0-based array index
                return this.grid[y-1][x-1];
            },
            isRover(x, y) {
                return x === this.rover.x && y === this.rover.y;
            },
            moveRover() {
                // Clear previous error message
                this.errorMessage = '';
                
                const commands = this.commandSequence.toUpperCase().split('');
                
                // Validate that all commands are valid (F, L, R)
                const invalidCommands = commands.filter(cmd => !['F', 'L', 'R'].includes(cmd));
                
                if (invalidCommands.length > 0) {
                    this.errorMessage = `Invalid commands detected: ${invalidCommands.join(', ')}. Only F, L and R are allowed.`;
                    return;
                }

                // Execute commands sequentially with delay
                this.executeCommandsSequentially(commands, 0);
            },
            
            executeCommandsSequentially(commands, index) {
                // If we've finished all commands, exit
                if (index >= commands.length) {
                    return;
                }
                
                // If animating, wait
                if (this.isAnimating) {
                    setTimeout(() => {
                        this.executeCommandsSequentially(commands, index);
                    }, 100);
                    return;
                }
                
                // Execute current command
                const command = commands[index];
                switch (command) {
                    case 'F':
                        if (!this.moveForward()) {
                            return;
                        }
                        break;
                    case 'L':
                        this.turnLeft();
                        break;
                    case 'R':
                        this.turnRight();
                        break;
                }
                
                // Schedule next command with delay
                setTimeout(() => {
                    this.executeCommandsSequentially(commands, index + 1);
                }, 500); // 500ms delay between commands
            },
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
            },
            turnLeft() {
                const directionMap = {
                    'N': 'W',
                    'E': 'N',
                    'S': 'E',
                    'W': 'S'
                };
                
                // Mark that we're animating
                this.isAnimating = true;
                
                // Apply counter-clockwise rotation (-90 degrees)
                this.rotationDegrees -= 90;
                
                // Update direction after a brief delay
                setTimeout(() => {
                    this.rover.direction = directionMap[this.rover.direction];
                    this.isAnimating = false;
                    
                    // Save position after turning
                    this.savePosition();
                }, 300);
            },
            turnRight() {
                const directionMap = {
                    'N': 'E',
                    'E': 'S',
                    'S': 'W',
                    'W': 'N'
                };
                
                // Mark that we're animating
                this.isAnimating = true;
                
                // Apply clockwise rotation (+90 degrees)
                this.rotationDegrees += 90;
                
                // Update direction after a brief delay
                setTimeout(() => {
                    this.rover.direction = directionMap[this.rover.direction];
                    this.isAnimating = false;
                    
                    // Save position after turning
                    this.savePosition();
                }, 300);
            },
            // Save current position to database
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
        }
    }
</script>

<style>
    .mars{
        width: 100%;
        max-width: 600px;
        display: flex;
        flex-direction: column;
        margin: 0 auto;
        padding: 15px;
        background-color: rgba(232, 193, 160, 0.2);
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    
    .mars-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    
    .grid-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .row-indicators {
        display: flex;
        flex-direction: column;
        margin-right: 5px;
    }
    
    .row-indicator {
        height: 3.5vh;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: bold;
        color: var(--dark-red);
        min-width: 20px;
    }
    
    .col-indicators-container {
        display: flex;
        margin-top: 5px;
    }
    
    .col-indicators-offset {
        width: 25px; /* Match width of row indicators */
    }
    
    .col-indicators {
        display: flex;
    }
    
    .col-indicator {
        width: 3.5vh;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: bold;
        color: var(--dark-red);
    }
    
    .grid-container {
        display: flex;
        flex-direction: column;
        border: 2px solid var(--border-color);
        border-radius: 4px;
        overflow: hidden;
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
    }
    
    .direction-indicator {
        font-size: 18px;
        font-weight: bold;
        color: var(--dark-red);
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: rgba(232, 193, 160, 0.5);
        border-radius: 50%;
        margin: 5px;
    }
    
    .direction-indicator.north {
        margin-bottom: 5px;
    }
    
    .direction-indicator.south {
        margin-top: 5px;
    }
    
    .direction-guide {
        margin-top: 10px;
        text-align: center;
    }
    
    .direction-tip {
        font-size: 14px;
        color: var(--dark-red);
        font-style: italic;
    }
    
    .row{
        display: flex;
    }
    
    .cell{
        width: 3.5vh;
        height: 3.5vh;
        border: 0.5px solid rgba(192, 106, 84, 0.5);
        position: relative;
        background-color: #f8d7c4;
    }
    
    .rover{
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: rgba(232, 144, 144);
    }
    
    .obstacle{
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: rgba(158, 38, 38, 0.1);
    }
    
    .out-of-bounds {
        background-color: #222;
        opacity: 0.5;
    }
    
    .rover-img{
        width: 2.5vh;
        height: 2.5vh;
        transform-origin: center;
    }
    
    .obstacle-img {
        width: 2.5vh;
        height: 2.5vh;
    }
    
    .coords {
        position: absolute;
        font-size: 8px;
        bottom: 1px;
        right: 1px;
        color: rgba(75, 38, 38, 0.6);
    }
    
    .small-coords {
        font-size: 6px;
    }
    
    .rover-info {
        margin: 20px 0;
        padding: 12px;
        border: 2px solid var(--border-color);
        border-radius: 8px;
        background-color: rgba(232, 193, 160, 0.3);
        max-width: 600px;
        width: 100%;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .direction-info, .viewport-info, .coords-info {
        margin: 8px 0;
    }
    
    .info-label {
        font-weight: bold;
        margin-right: 8px;
        color: var(--dark-red);
    }
    
    .info-value {
        color: var(--text-color);
        font-weight: 500;
    }
    
    .controls {
        margin-bottom: 20px;
        width: 100%;
        max-width: 600px;
    }
    
    .input-group {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    
    .command-input {
        padding: 12px;
        border: 2px solid var(--border-color);
        border-radius: 6px;
        font-size: 1rem;
        background-color: #fff;
        color: var(--text-color);
        width: 100%;
    }
    
    .command-input:focus {
        outline: none;
        border-color: var(--primary-red);
        box-shadow: 0 0 0 2px rgba(200, 50, 50, 0.2);
    }
    
    .buttons-group {
        display: flex;
        gap: 10px;
    }
    
    .move-btn {
        padding: 10px 20px;
        background-color: var(--primary-red);
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-weight: bold;
        flex: 1;
        transition: background-color 0.2s;
    }
    
    .move-btn:hover {
        background-color: var(--dark-red);
    }
    
    .reset-btn {
        padding: 10px 20px;
        background-color: #8c3a3a;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-weight: bold;
        flex: 1;
        transition: background-color 0.2s;
    }
    
    .reset-btn:hover {
        background-color: #6a2a2a;
    }
    
    .error-message {
        color: var(--primary-red);
        margin-top: 12px;
        font-weight: bold;
        padding: 8px 12px;
        background-color: rgba(200, 50, 50, 0.1);
        border-radius: 4px;
        border-left: 3px solid var(--primary-red);
    }
    
    .save-btn {
        padding: 10px 20px;
        background-color: var(--primary-red);
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-weight: bold;
        flex: 1;
        transition: background-color 0.2s;
    }
    
    .save-btn:hover {
        background-color: var(--dark-red);
    }
    
    .save-success-message {
        margin-top: 10px;
        padding: 8px;
        background-color: rgba(76, 175, 80, 0.1);
        color: #4CAF50;
        border-radius: 4px;
        font-weight: bold;
        border-left: 3px solid #4CAF50;
    }
    
    /* Responsive */
    @media screen and (max-width: 600px) {
        .mars, .rover-info, .controls {
            max-width: 100%;
        }
        
        .cell {
            width: 3vh;
            height: 3vh;
        }
        
        .rover-img, .obstacle-img {
            width: 2.2vh;
            height: 2.2vh;
        }
        
        .buttons-group {
            flex-direction: column;
        }
        
        .direction-indicator {
            font-size: 14px;
            width: 20px;
            height: 20px;
        }
        
        .small-coords {
            font-size: 5px;
        }
        
        .direction-tip {
            font-size: 12px;
        }
        
        .row-indicator, .col-indicator {
            font-size: 10px;
        }
        
        .row-indicator {
            height: 3vh;
            min-width: 16px;
        }
        
        .col-indicators-offset {
            width: 21px;
        }
        
        .col-indicator {
            width: 3vh;
        }
    }
</style>