<p align="center">
    <h1>Authentication Service</h1>
</p>

## About Service

This service handles all the different kind of authentication into the entire system. Below are list of the authentication mode implemented.

- JWT authentication using passport
- API key/secret

## How to start the service

To successfully start the service, follow the steps below:
**NB: Ensure you have docker and docker compose installed in your environment**

- Clone the [repository](https://gitlab.com/technology38/authentication-service) in gitlab
- Run ```docker-compose build``` to build the image
- Run ```docker-compose up``` to start the container, you can pass the ```-d``` flag to run it in the background
- Run ```docker ps``` to see the list of containers running. 
- Run ```docker-compose exec <container_name> bash``` to access the running containers
