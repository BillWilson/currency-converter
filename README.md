# currency-converter

## Prepare for API server

### Copy env file
`cp api/.env.example api/.env`

### Build docker image
`docker build . -t cc -f Dockerfile`

### Run docker container
`docker run -d -p 8081:8080 cc`

### Check endpoint
Usage:
`http://localhost:8081/api/currency/convert?from=USD&to=USD&amount=123456`
