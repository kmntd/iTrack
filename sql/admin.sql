CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,  -- New username column
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,   -- Keep for password recovery
    password VARCHAR(255) NOT NULL        -- Store hashed passwords
);
