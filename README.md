# Web Security Website

This is a simple online webshop, created to implement, execute and defend against common attacks against websites.

The project contains pages written in php with some javascript for functionality, postgresql as a database and a flask backend which handles payments and creation of wallets.

## Setting up the project
To setup the project, a postgres database should be created, and the query in `sql.txt` should be run to create the proper tables. Then the credentials for connecting to the database needs to be added to the `pg_connect` in `login.php`. 

The development server can be started by navigating to the project directory and running
```
php -S 127.0.0.1:8000
```
and to view the pages navigate to localhost:8000/login.php and the login and registration page should show up.
To register a user, that user first needs to create a SimpleCoin wallet. The easiest way is to go to the miner folder and starting the `miner.py` script which starts a flask server, and then making a curl request to the proper endpoint with
```
curl 127.0.0.1:5000/wallet
```
If the server is started then the response will contain a public key and a private key pair for the SimpleCoin wallet. Write down the private key and then the public key can be used in the registration. After logging in, adding some items to the cart and going to checkout, the website will provide a curl command for the user to be able to generate a signature. Simply replacing your_private_key_value with the actual private key and sending the request will be enough to get back a signature which can be entered into the input field. If everything was done correctly, a receipt of the transaction should be shown, otherwise an error message will show up.
