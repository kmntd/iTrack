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
    status ENUM('active', 'inactive') DEFAULT 'active',  -- Status field for students
    FOREIGN KEY (section_id) REFERENCES sections(id) ON DELETE SET NULL
);


CREATE TABLE subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_code VARCHAR(50) NOT NULL,  -- No UNIQUE constraint
    subject_name VARCHAR(100) NOT NULL
);

CREATE TABLE sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_name VARCHAR(10) NOT NULL  -- e.g., "A", "B", "C"
);

CREATE TABLE teachers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    middle_name VARCHAR(50),           -- Optional
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,    -- Store hashed passwords
    dob DATE NOT NULL,
    phone_number VARCHAR(15),           -- Optional
    address VARCHAR(255),               -- Optional
    hire_date DATE,                     -- Optional
    status ENUM('active', 'inactive') DEFAULT 'active' -- Optional
);

CREATE TABLE assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    assignment_title VARCHAR(100) NOT NULL,
    description TEXT DEFAULT NULL,
    section_id INT NOT NULL,
    subject_id INT NOT NULL,
    teacher_id INT NOT NULL,
    due_date DATETIME DEFAULT NULL,
    type ENUM('file_upload', 'quiz') NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    perfect_score INT DEFAULT 100,
    
    -- Foreign key constraints
    FOREIGN KEY (section_id) REFERENCES sections(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE CASCADE,
    
    -- Unique constraint to avoid duplicate assignments for the same section, subject, and teacher
    UNIQUE (section_id, subject_id, teacher_id, assignment_title)
);


CREATE TABLE quizzes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    subject_id INT NOT NULL,
    section_id INT NOT NULL,
    teacher_id INT NOT NULL,
    due_date DATETIME DEFAULT NULL,
    perfect_score INT NOT NULL,
    FOREIGN KEY (subject_id) REFERENCES subjects(id),
    FOREIGN KEY (section_id) REFERENCES sections(id),
    FOREIGN KEY (teacher_id) REFERENCES teachers(id)
);
