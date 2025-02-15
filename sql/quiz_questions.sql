CREATE TABLE quiz_questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quiz_id INT NOT NULL,  -- Change from assignment_id to quiz_id
    question_text TEXT NOT NULL,
    correct_answer_id INT NOT NULL,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id),
    FOREIGN KEY (correct_answer_id) REFERENCES quiz_options(id)
);
