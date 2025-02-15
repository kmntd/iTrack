CREATE TABLE assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_id INT NOT NULL,
    teacher_id INT NOT NULL,
    assignment_title VARCHAR(100) NOT NULL,
    due_date DATE,
    type ENUM('file_upload', 'quiz') NOT NULL, -- New column to define assignment type
    FOREIGN KEY (subject_id) REFERENCES subjects(id),
    FOREIGN KEY (teacher_id) REFERENCES teachers(id)
);
