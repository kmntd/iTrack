CREATE TABLE submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    assignment_id INT NOT NULL,
    student_id INT NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    submission_time DATETIME NOT NULL,
    FOREIGN KEY (assignment_id) REFERENCES assignments(id)
);
