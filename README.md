## Electra

1. Docker setup
    
    ```
    cd laradock
    ```
        
    Before first launch copy .env.local into .env:
    ```
    cp .env.local .env
    ```
    
    Set env variables in ./laradock/.env file: 
    ```
    COMPOSE_FILE=docker-compose.local.yml - for local development
   
    DATA_PATH_HOST=~/.laradock/data/electra - path to project data directory
    ```
    
    On project first launch run: 
    ```
    sudo docker-compose build
    ```
    
    To run docker containers:
    
    ```
    sudo docker-compose up -d
    ```
    
    Run 'bash' inside the 'workspace' container:
    
    ```
    sudo docker-compose exec -u laradock workspace bash
    ```

2. Laravel setup

    Run command from 'workspace' container:
    ```
    composer install
    ```    
    
    Copy .env.example into .env:
    ```
    cp .env.local.example .env
