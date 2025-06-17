
## Prueba tecnica Aiwifi


Requerimiento: Api para registrar y autentificar clientes, y subir lista de contactos en  archivo csv para cada cliente

el archivo debe estar separado por comas y la primer fila debe ser los headers las columnas del csv deben ser 

name, email.


Se valida edad, nacionalidad y genero en base al nombre, y se valida que el email sea un email valido, los datos del email se almacentan a formato json para estadisticas


### Caracteristicas 


- Laravel 12
- PHP 8.
- Authentificacion sanctum
- base de datos mysql
- Capa de servicios para logica de negocio
- Variable de entorno nueva
    - MAILBOXLAYER_API_KEY : Api key del api https://mailboxlayer.com/.  valor de testeo. 
- Testeo con php unit



### Instalacion

- Clonar repositorio
- Correr composer install
- correr migraciones
- run php artisan queue:work
- runt php artisan serve
- Coleccion de postman en PostmanCollection




## Authors

- Jorge Cortes Lopez [https://www.linkedin.com/in/jorhcl/]
