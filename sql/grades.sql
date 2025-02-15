CREATE TABLE grades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    subject_id INT NOT NULL,
    grade DECIMAL(5, 2),  -- Assuming grades are numeric (e.g., 95.50)
    term VARCHAR(20),  -- For example, "Term 1", "Term 2"
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (subject_id) REFERENCES subjects(id),
    UNIQUE (student_id, subject_id, term)  -- Ensure one grade per subject per term
);
