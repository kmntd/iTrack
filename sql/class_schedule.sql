CREATE TABLE class_schedule (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_id INT NOT NULL,
    subject_id INT NOT NULL,
    teacher_id INT NOT NULL,
    schedule_day VARCHAR(20) NOT NULL,  -- e.g., "Monday", "Tuesday"
    schedule_time TIME NOT NULL,        -- e.g., "10:00:00" (24-hour format)
    FOREIGN KEY (section_id) REFERENCES sections(id),
    FOREIGN KEY (subject_id) REFERENCES subjects(id),
    FOREIGN KEY (teacher_id) REFERENCES teachers(id)
);
