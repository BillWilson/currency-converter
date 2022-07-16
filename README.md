# currency-converter

## Start the API server

### Copy env file
```bash
cp api/.env.example api/.env
```

### Build docker image
```bash
docker build . -t cc -f Dockerfile
```

### Run docker container
```bash
docker run -d -p 8081:8080 cc
```
----

## API document

### Currency converter

Return the result of calculator for converting one currency to another

* **URL**

  `/api/currency/convert`

* **Method:**

  `GET`

* **URL Params**
  
  None
* **Query String Params**

  - **from**: `string` *(Required)*, original currency. Use `TWD`, `USD` and `JPY`

  - **to**: `string` *(Required)*, target currency. Use `TWD`, `USD` and `JPY`

  - **amount**: `integer` *(Required)*, any number larger than `0`

* **Success Response:**

    * **Code:** 200 <br />
      **Content:** `{"result" : "1,234.56"}`

* **Error Response:**

    * **Code:** 422 Unprocessable Entity <br />
      **Content:** `{"error": "User doesn't exist"}`
* **Example**

    * **URL**: `http://localhost:8081/api/currency/convert?from=USD&to=TWD&amount=123456`
    * **Response**: `{"result":"3,758,494.46"}`

## Testing
`cd api && vendor/bin/pest --colors=always`
