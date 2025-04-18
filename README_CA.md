# Mars Rover Mission

## Language / Idioma / Idioma
- [English](/README.md)
- [Español](/README_ES.md)
- [Català (actual)](#mars-rover-mission)

## Descripció del Projecte

Aquest projecte implementa una simulació interactiva del rover marcià, permetent als usuaris controlar un rover en una superfície marciana representada com una quadrícula. El projecte està construït utilitzant Laravel per al backend i Vue.js per al frontend, amb una interfície gràfica interactiva que mostra la posició i direcció del rover.

## Demostració

[![Demostració de Mars Rover Mission](https://img.youtube.com/vi/c4FEZmLOL2w/0.jpg)](https://youtu.be/c4FEZmLOL2w)

Fes clic a la imatge de dalt per veure una demostració del funcionament de Mars Rover Mission.

## Funcionalitats Principals

- Visualització d'una superfície marciana com a quadrícula amb obstacles generats aleatòriament
- Control del rover mitjançant comandes simples (F: Avançar, L: Girar Esquerra, R: Girar Dreta)
- Detecció d'obstacles i límits del mapa
- Seguiment i visualització en temps real de la posició i orientació del rover
- Persistència de dades mitjançant el desat automàtic de la posició del rover
- Interfície d'usuari responsiva i amigable

## Estructura del Projecte

### Frontend (Vue.js)

El component principal `Mars.vue` s'encarrega de la visualització i la lògica d'interacció amb l'usuari:

- **Visualització del Mapa**: Una quadrícula que mostra la superfície marciana amb indicadors de direcció (N, S, E, W) i coordenades als marges.
- **Representació del Rover**: Imatge d'un rover amb rotació dinàmica segons la seva direcció.
- **Panell de Control**: Permet a l'usuari introduir comandes i executar-les.
- **Panell d'Informació**: Mostra les coordenades actuals i la direcció del rover.

### Backend (Laravel)

El backend proporciona APIs per a:
- Desar la posició del rover (`/api/rover/save-position`)
- Recuperar la posició desada del rover
- Gestionar la lògica d'obstacles i límits del mapa

## Algoritme de Moviment del Rover

L'algoritme de moviment del rover implementa les següents regles:

1. **Avançar (F)**: El rover es mou una unitat en la direcció actual.
   - Si hi ha un obstacle a la casella de destí, el moviment es cancel·la i es mostra un missatge d'error.
   - Si el moviment portaria al rover fora dels límits del mapa, es mostra un missatge d'advertència.

2. **Girar Esquerra (L)**: El rover rota 90 graus en sentit antihorari.
   - N → W → S → E → N

3. **Girar Dreta (R)**: El rover rota 90 graus en sentit horari.
   - N → E → S → W → N

Cada moviment o gir s'anima visualment per donar retroalimentació a l'usuari.

## Característiques Tècniques

### Generació del Mapa

- El mapa és una quadrícula de 200x200 cel·les.
- Els obstacles es generen aleatòriament amb una probabilitat del 15% per cel·la.
- S'utilitza una finestra de visualització d'11x11 cel·les centrada en el rover per optimitzar el rendiment.

### Animacions

- Rotació suau del rover en girar (usant transicions CSS).
- Feedback visual per a comandes vàlides i invàlides.

### Persistència de Dades

- La posició del rover es desa automàticament després de cada moviment mitjançant crides a l'API.
- Es mostra una notificació d'èxit en desar.

## Optimització i Rendiment

- Renderitzat condicional de cel·les visibles per millorar el rendiment.
- Càlcul dinàmic de coordenades visibles en funció de la posició del rover.
- Indicadors de coordenades als marges del mapa per facilitar l'orientació sense sobrecarregar la interfície.

## Consideracions d'UX/UI

- Indicadors clars de direcció (N, S, E, W) per facilitar l'orientació.
- Interfície intuïtiva amb retroalimentació visual per a cada acció.
- Rover amb rotació que indica visualment la seva direcció (el braç robòtic apunta en la direcció d'avanç).
- Disseny responsiu que s'adapta a diferents mides de pantalla.
- Missatges d'error clars i formatació visual consistent.

## Tecnologies Utilitzades

- **Frontend**: Vue.js, CSS3, HTML5
- **Backend**: Laravel, PHP
- **Comunicació**: Axios per a crides API REST
- **Estil**: CSS personalitzat amb variables per a consistència de colors

## Arquitectura

El projecte segueix una arquitectura client-servidor:

1. **Client** (Vue.js):
   - Gestiona la interacció de l'usuari i la visualització
   - Implementa la lògica de moviment del rover
   - Es comunica amb el servidor per a persistència de dades

2. **Servidor** (Laravel):
   - Gestiona la persistència de dades
   - Proporciona APIs per a les operacions del rover

## Instal·lació i Configuració

### Requisits previs

- PHP 8.0 o superior
- Composer
- Node.js i npm
- Base de dades MySQL o compatible

### Passos d'instal·lació

1. Clonar el repositori:
   ```
   git clone https://github.com/el-teu-usuari/mars-rover-mission.git
   cd mars-rover-mission
   ```

2. Instal·lar dependències de PHP:
   ```
   composer install
   ```

3. Instal·lar dependències de JavaScript:
   ```
   npm install
   ```

4. Configurar l'entorn:
   - Copiar `.env.example` a `.env`
   - Configurar la connexió a la base de dades a `.env`

5. Generar clau d'aplicació:
   ```
   php artisan key:generate
   ```

6. Executar migracions:
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

## Decisions d'Implementació

### Enfocament de Disseny

S'ha prioritzat una interfície neta i funcional, amb retroalimentació visual clara sobre l'estat del rover i el seu entorn. Els indicadors de coordenades als marges permeten als usuaris orientar-se sense sobrecarregar visualment el mapa.

### Optimització de Rendiment

Per gestionar un mapa gran (200x200) de manera eficient, només es renderitza una finestra de visualització d'11x11 al voltant del rover. Això permet una experiència fluida fins i tot en dispositius menys potents.

### Gestió d'Errors

S'implementa una gestió d'errors robusta amb missatges clars per a:
- Obstacles detectats
- Intents de sortida del mapa
- Comandes invàlides
- Errors de comunicació amb el servidor

## Futures Millores

- Implementació de diferents tipus de terreny amb efectes sobre el moviment
- Mode multijugador amb diversos rovers
- Sistema de missions i objectius
- Estadístiques de moviment i eficiència
- Suport per a dispositius tàctils amb controls gestuals

## Conclusió

Aquest projecte demostra habilitats en:
- Desenvolupament frontend amb Vue.js
- Disseny d'interfícies responsives
- Implementació de lògica de negoci complexa
- Comunicació client-servidor
- Gestió d'estats i transicions
- Optimització de rendiment

La simulació del Mars Rover representa un desafiament tècnic interessant que combina algorísmia, disseny d'interfície i programació orientada a objectes en un context pràctic i visualment atractiu. 