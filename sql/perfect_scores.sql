CREATE TABLE perfect_scores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    assignment_id INT NOT NULL,
    perfect_score INT NOT NULL,
    FOREIGN KEY (assignment_id) REFERENCES assignments(id)
);
