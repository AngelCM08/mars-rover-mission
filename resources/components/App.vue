<template>
    <div class="app-container">
      <div v-if="loading" class="loading-overlay">
        <div class="loading-spinner"></div>
        <p>Loading...</p>
      </div>
      
      <h1 class="app-title">Mars Rover Mission</h1>
      <div v-if="!missionStarted" class="landing-selection">
        <h2 class="section-title">Select landing position</h2>
        
        <div v-if="hasStoredPosition" class="stored-position-notice">
          <p>A saved position has been found:</p>
          <p>X: {{ landingX }}, Y: {{ landingY }}, Direction: {{ selectedDirection }}</p>
          <div class="position-actions">
            <button @click="useLastPosition" class="use-stored-btn">Use saved position</button>
            <button @click="hasStoredPosition = false" class="new-position-btn">Use new position</button>
          </div>
        </div>
        
        <div v-else class="landing-form">
            <div class="form-group">
                <label for="coordX">X Coordinate:</label>
                <input 
                    type="number" 
                    id="coordX" 
                    v-model.number="landingX" 
                    min="1" 
                    max="200" 
                    placeholder="1-200"
                >
            </div>
            <div class="form-group">
                <label for="coordY">Y Coordinate:</label>
                <input 
                    type="number" 
                    id="coordY" 
                    v-model.number="landingY" 
                    min="1" 
                    max="200" 
                    placeholder="1-200"
                >
            </div>
            <div class="form-group">
                <label for="direction">Initial direction:</label>
                <select id="direction" v-model="selectedDirection">
                    <option value="N">North (N)</option>
                    <option value="E">East (E)</option>
                    <option value="S">South (S)</option>
                    <option value="W">West (W)</option>
                </select>
            </div>
            <button 
                @click="startWithNewPosition" 
                :disabled="!isValidLandingPosition"
                class="landing-btn"
            >
                Start mission
            </button>
            <p class="coordinates-info">The board has a size of 200x200</p>
            <div v-if="errorMessage" class="error-message">{{ errorMessage }}</div>
        </div>
      </div>
      <Mars 
        v-else 
        :initial-x="landingX" 
        :initial-y="landingY" 
        :initial-direction="selectedDirection"
        @reset="resetMission"
      />
    </div>
</template>

<script>
    import Mars from './Mars.vue';
    import axios from 'axios';

    export default {  
        name: 'App',
        components: {
            Mars
        },
        data() {
            return {
                missionStarted: false,
                landingX: 100,
                landingY: 100,
                selectedDirection: 'E',
                errorMessage: '',
                loading: false, // Loading indicator
                hasStoredPosition: false, // If there's a saved position
            }
        },
        created() {
            // Check if there's a saved position when loading the application
            this.checkForSavedPosition();
        },
        computed: {
            isValidLandingPosition() {
                return this.landingX >= 1 && 
                       this.landingX <= 200 && 
                       this.landingY >= 1 && 
                       this.landingY <= 200;
            }
        },
        methods: {
            checkForSavedPosition() {
                this.loading = true;
                
                axios.get('/api/rover/get-position')
                    .then(response => {
                        if (response.data.success && response.data.position) {
                            // If there's a saved position, load it
                            this.landingX = response.data.position.x;
                            this.landingY = response.data.position.y;
                            this.selectedDirection = response.data.position.direction;
                            this.hasStoredPosition = true;
                        }
                    })
                    .catch(error => {
                        console.error('Error loading saved position:', error);
                    })
                    .finally(() => {
                        this.loading = false;
                    });
            },
            
            startMission() {
                if (!this.isValidLandingPosition) {
                    this.errorMessage = 'Please enter valid coordinates (1-200).';
                    return;
                }
                
                this.errorMessage = '';
                this.missionStarted = true;
            },
            
            resetMission() {
                this.missionStarted = false;
                // Retrieve the last saved position when resetting
                this.checkForSavedPosition();
            },
            
            useLastPosition() {
                // Start mission with the saved position
                this.startMission();
            },
            
            startWithNewPosition() {
                // Use manually entered coordinates
                this.startMission();
            }
        }
    }
</script>

<style>
    /* General styles */
    :root {
        --primary-red: #c83232;
        --dark-red: #9e2626;
        --light-red: #e89090;
        --sand-color: #e8c1a0;
        --background-color: #feedde;
        --text-color: #4b2626;
        --border-color: #c06a54;
    }
    
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }
    
    body {
        font-family: Verdana, sans-serif;
        background-color: var(--background-color);
        color: var(--text-color);
        line-height: 1.6;
    }
    
    .app-container {
        max-width: 1100px;
        margin: 0 auto;
        padding: 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    
    .app-title {
        color: var(--primary-red);
        text-align: center;
        margin: 20px 0;
        font-size: 2.5rem;
        text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.2);
    }
    
    .section-title {
        color: var(--dark-red);
        margin-bottom: 15px;
        font-size: 1.5rem;
    }

    /* Styles for landing selection */
    .landing-selection {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
        max-width: 600px;
        margin: 0 auto 20px;
    }
    
    .landing-form {
        display: flex;
        flex-direction: column;
        gap: 15px;
        max-width: 350px;
        width: 100%;
        padding: 25px;
        border: 2px solid var(--border-color);
        border-radius: 8px;
        background-color: rgba(232, 193, 160, 0.3);
        margin-top: 15px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    
    .form-group {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .form-group label {
        font-weight: bold;
        flex: 1;
        color: var(--text-color);
    }
    
    .form-group input,
    .form-group select {
        flex: 2;
        padding: 10px;
        border: 1px solid var(--border-color);
        border-radius: 4px;
        background-color: #fff;
        color: var(--text-color);
        font-size: 0.9rem;
    }
    
    .form-group input:focus,
    .form-group select:focus {
        outline: none;
        border-color: var(--primary-red);
        box-shadow: 0 0 0 2px rgba(200, 50, 50, 0.2);
    }
    
    .landing-btn {
        padding: 12px;
        background-color: var(--primary-red);
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-weight: bold;
        margin-top: 10px;
        transition: background-color 0.2s;
        font-size: 1rem;
    }
    
    .landing-btn:hover {
        background-color: var(--dark-red);
    }
    
    .landing-btn:disabled {
        background-color: #cccccc;
        cursor: not-allowed;
    }
    
    .coordinates-info {
        font-size: 0.8rem;
        color: var(--text-color);
        text-align: center;
        margin: 10px 0;
    }
    
    .error-message {
        color: var(--primary-red);
        margin-top: 10px;
        font-weight: bold;
    }
    
    /* Responsive */
    @media screen and (max-width: 480px) {
        .app-title {
            font-size: 2rem;
        }
        
        .section-title {
            font-size: 1.3rem;
        }
        
        .landing-form {
            padding: 15px;
        }
        
        .form-group {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .form-group label {
            margin-bottom: 5px;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
        }
    }
    
    /* Additional styles for loading indicator and saved position notification */
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        z-index: 1000;
        color: white;
    }
    
    .loading-spinner {
        border: 4px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top: 4px solid var(--primary-red);
        width: 40px;
        height: 40px;
        animation: spin 1s linear infinite;
        margin-bottom: 10px;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .stored-position-notice {
        background-color: rgba(232, 193, 160, 0.3);
        border: 2px solid var(--border-color);
        border-radius: 8px;
        padding: 20px;
        margin-top: 15px;
        text-align: center;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        max-width: 350px;
        width: 100%;
    }
    
    .position-actions {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-top: 15px;
    }
    
    .use-stored-btn {
        padding: 12px;
        background-color: var(--primary-red);
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-weight: bold;
        transition: background-color 0.2s;
    }
    
    .use-stored-btn:hover {
        background-color: var(--dark-red);
    }
    
    .new-position-btn {
        padding: 12px;
        background-color: #8c3a3a;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-weight: bold;
        transition: background-color 0.2s;
    }
    
    .new-position-btn:hover {
        background-color: #6a2a2a;
    }
    
    /* Responsive for saved position notification */
    @media screen and (max-width: 480px) {
        .stored-position-notice {
            padding: 15px;
            font-size: 0.9rem;
        }
        
        .use-stored-btn,
        .new-position-btn {
            padding: 10px;
        }
    }
</style>
  