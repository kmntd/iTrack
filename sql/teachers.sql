CREATE TABLE teachers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    middle_name VARCHAR(50),           -- Optional
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,  -- Store hashed passwords
    dob DATE NOT NULL,
    subject_id INT NOT NULL,          -- Foreign key to subjects
    phone_number VARCHAR(15),         -- Optional
    address VARCHAR(255),             -- Optional
    hire_date DATE,                   -- Optional
    status ENUM('active', 'inactive') DEFAULT 'active', -- Optional
    FOREIGN KEY (subject_id) REFERENCES subjects(id)
);

