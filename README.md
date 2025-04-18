# Mars Rover Mission

## Language / Idioma / Idioma
- [English (current)](#mars-rover-mission)
- [Español](/README_ES.md)
- [Català](/README_CA.md)

## Project Description

This project implements an interactive simulation of a Mars rover, allowing users to control a rover on a Martian surface represented as a grid. The project is built using Laravel for the backend and Vue.js for the frontend, with an interactive graphical interface that displays the position and direction of the rover.

## Demo

[![Mars Rover Mission Demo](https://img.youtube.com/vi/c4FEZmLOL2w/0.jpg)](https://youtu.be/c4FEZmLOL2w)

Click on the image above to watch a demonstration of the Mars Rover Mission in action.

## Main Features

- Visualization of a Martian surface as a grid with randomly generated obstacles
- Rover control through simple commands (F: Forward, L: Turn Left, R: Turn Right)
- Obstacle and map boundary detection
- Real-time tracking and visualization of rover position and orientation
- Data persistence through automatic saving of rover position
- Responsive and user-friendly interface

## Project Structure

### Frontend (Vue.js)

The main component `Mars.vue` handles the visualization and user interaction logic:

- **Map Visualization**: A grid showing the Martian surface with direction indicators (N, S, E, W) and coordinates on the margins.
- **Rover Representation**: Image of a rover with dynamic rotation according to its direction.
- **Control Panel**: Allows the user to input and execute commands.
- **Information Panel**: Displays the current coordinates and direction of the rover.

### Backend (Laravel)

The backend provides APIs for:
- Saving the rover's position (`/api/rover/save-position`)
- Retrieving the saved position of the rover
- Managing the logic of obstacles and map boundaries

## Rover Movement Algorithm

The rover movement algorithm implements the following rules:

1. **Move Forward (F)**: The rover moves one unit in the current direction.
   - If there is an obstacle in the destination cell, the movement is canceled and an error message is displayed.
   - If the movement would take the rover outside the map boundaries, a warning message is displayed.

2. **Turn Left (L)**: The rover rotates 90 degrees counterclockwise.
   - N → W → S → E → N

3. **Turn Right (R)**: The rover rotates 90 degrees clockwise.
   - N → E → S → W → N

Each movement or turn is visually animated to provide feedback to the user.

## Technical Features

### Map Generation

- The map is a 200x200 cell grid.
- Obstacles are randomly generated with a 15% probability per cell.
- A 11x11 cell viewport centered on the rover is used to optimize performance.

### Animations

- Smooth rover rotation when turning (using CSS transitions).
- Visual feedback for valid and invalid commands.

### Data Persistence

- The rover's position is automatically saved after each movement through API calls.
- A success notification is displayed when saving.

## Optimization and Performance

- Conditional rendering of visible cells to improve performance.
- Dynamic calculation of visible coordinates based on rover position.
- Coordinate indicators on the map margins for easier orientation without overloading the interface.

## UX/UI Considerations

- Clear direction indicators (N, S, E, W) for easier orientation.
- Intuitive interface with visual feedback for each action.
- Rover with rotation that visually indicates its direction (the robotic arm points in the direction of movement).
- Responsive design that adapts to different screen sizes.
- Clear error messages and consistent visual formatting.

## Technologies Used

- **Frontend**: Vue.js, CSS3, HTML5
- **Backend**: Laravel, PHP
- **Communication**: Axios for REST API calls
- **Styling**: Custom CSS with variables for color consistency

## Architecture

The project follows a client-server architecture:

1. **Client** (Vue.js):
   - Handles user interaction and visualization
   - Implements rover movement logic
   - Communicates with the server for data persistence

2. **Server** (Laravel):
   - Manages data persistence
   - Provides APIs for rover operations

## Installation and Configuration

### Prerequisites

- PHP 8.0 or higher
- Composer
- Node.js and npm
- MySQL or compatible database

### Installation Steps

1. Clone the repository:
   ```
   git clone https://github.com/your-username/mars-rover-mission.git
   cd mars-rover-mission
   ```

2. Install PHP dependencies:
   ```
   composer install
   ```

3. Install JavaScript dependencies:
   ```
   npm install
   ```

4. Configure the environment:
   - Copy `.env.example` to `.env`
   - Configure the database connection in `.env`

5. Generate application key:
   ```
   php artisan key:generate
   ```

6. Run migrations:
   ```
   php artisan migrate
   ```

7. Compile frontend resources:
   ```
   npm run dev
   ```

8. Start the server:
   ```
   php artisan serve
   ```

## Implementation Decisions

### Design Approach

A clean and functional interface has been prioritized, with clear visual feedback on the rover's state and its environment. The coordinate indicators on the margins allow users to orient themselves without visually overloading the map.

### Performance Optimization

To efficiently handle a large map (200x200), only an 11x11 viewport around the rover is rendered. This allows for a fluid experience even on less powerful devices.

### Error Management

Robust error handling is implemented with clear messages for:
- Detected obstacles
- Attempts to exit the map
- Invalid commands
- Server communication errors

## Future Improvements

- Implementation of different terrain types with effects on movement
- Multiplayer mode with multiple rovers
- Mission and objective system
- Movement and efficiency statistics
- Touch device support with gesture controls

## Conclusion

This project demonstrates skills in:
- Frontend development with Vue.js
- Responsive interface design
- Implementation of complex business logic
- Client-server communication
- State and transition handling
- Performance optimization

The Mars Rover simulation represents an interesting technical challenge that combines algorithms, interface design, and object-oriented programming in a practical and visually attractive context.

