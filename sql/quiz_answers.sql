CREATE TABLE quiz_answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    submission_id INT NOT NULL,
    question_id INT NOT NULL,
    selected_answer_id INT NOT NULL,
    FOREIGN KEY (submission_id) REFERENCES quiz_submissions(id),
    FOREIGN KEY (question_id) REFERENCES quiz_questions(id),
    FOREIGN KEY (selected_answer_id) REFERENCES quiz_options(id)
);
