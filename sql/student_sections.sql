CREATE TABLE student_sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    section_id INT NOT NULL,
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (section_id) REFERENCES sections(id),
    UNIQUE (student_id, section_id)  -- Ensures no duplicate enrollments in sections
);