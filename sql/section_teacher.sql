CREATE TABLE section_teacher (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_id INT NOT NULL,
    teacher_id INT NOT NULL,
    FOREIGN KEY (section_id) REFERENCES sections(id),
    FOREIGN KEY (teacher_id) REFERENCES teachers(id)
);
