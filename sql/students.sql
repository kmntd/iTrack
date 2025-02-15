CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lrn VARCHAR(15) NOT NULL UNIQUE,  -- LRN is unique for each student
    first_name VARCHAR(50) NOT NULL,
    middle_name VARCHAR(50),           -- Optional
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE, -- Ensure each email is unique
    password VARCHAR(255) NOT NULL,
    dob DATE NOT NULL,
    grade_level INT DEFAULT NULL,      -- Set as NULL initially for future assignment
    section_id INT DEFAULT NULL,       -- Set as NULL initially for future assignment
    FOREIGN KEY (section_id) REFERENCES sections(id) ON DELETE SET NULL
);
