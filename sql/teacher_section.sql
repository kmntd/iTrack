CREATE TABLE teacher_section (
    id INT AUTO_INCREMENT PRIMARY KEY,
    teacher_id INT NOT NULL,
    section_id INT NOT NULL,
    year_level VARCHAR(20),
    deleted_at DATETIME DEFAULT NULL,
    FOREIGN KEY (teacher_id) REFERENCES teachers(id),
    FOREIGN KEY (section_id) REFERENCES sections(id)
);
