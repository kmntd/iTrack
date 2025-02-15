CREATE VIEW quiz_grades AS
SELECT 
    qs.student_id,
    SUM(qs.score) AS total_quiz_score,
    SUM(q.perfect_score) AS total_quiz_possible_score
FROM 
    quiz_submissions qs
JOIN 
    quizzes q ON qs.quiz_id = q.id
GROUP BY 
    qs.student_id;
