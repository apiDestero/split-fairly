# Split fairly
Split the bill evenly among your friends

![Alt text](/screenshot.jpg?raw=true "Screenshot")

### Create user table

```
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL
);
```

### Start server

`php -S localhost:8000 -t public/ -d extension=mysqli`
or
`php -S 0.0.0.0:8000 -t public/ -d extension=mysqli`

### Database config

in public/index.php

```
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', 'root');
define('DB_DATABASE', 'test');
```
