## Electra

1. Docker setup
    
    ```
    cd laradock
    ```
        
    Before first launch copy .env.local into .env:
    ```
    cp .env.local .env
    ```
    ```
    cp .env.dev .env - on dev server
    ```
    
    Set env variables in ./laradock/.env file: 
    ```
    COMPOSE_FILE=docker-compose.local.yml - for local server
    ``` 
    ```
    COMPOSE_FILE=docker-compose.dev.yml - for dev server
    ```
   
    ```
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

    Add link to storage
    ```
   php artisan storage:link
   ```

    Run command from 'workspace' container:
    ```
    composer install
    ```
    
    Copy .env.example into .env:
    ```
    cp .env.local.example .env
    ```
    ```
    cp .env.dev.example .env - on dev server
    ```
   
   Apply migrations:
   ```
   php artisan migrate
   ```
   
   Generate the encryption keys Passport needs in order to generate access token:
   ```
   php artisan passport:keys
   ```
   
   Add Passport client:
   ```
   php artisan passport:client --password --name=IOS --no-interaction
   ```
