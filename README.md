# GOA Proxy (google-oauth2-proxy)

Google Oauth2 Proxy (PHP) for internal servers to get Google Authentication

### Synopsis

```
                    Browser Redirection
    Client ------------>  GOA_Proxy   ----->   Google Servers
                Get User details  <-------->
           <-----------               
           user details
```

### API Details (Client to GOA Proxy)

* `redirect` -- Redirect URL back to the Client (could even be localhost)
* `api_key`  -- API Key to authenticate
* `signout`  -- A true value to request for a signout

* The GOA proxy will authenticate with Google. After authenticating, it will redirect the browser along with passing the following parameters:

* `user`: Base64 encoded JSON encoded string
    - `name`: Name of the user
    - `email`: Email of the user
* `error`: Sent if there are any errors

### API To get Token

* `redirect` -- Redirect URL back to the Client (could even be localhost)
* `api_key`  -- API Key to authenticate
* `getToken` -- A true value to get only the Token

The proxy will simply return the JSON string of the token


