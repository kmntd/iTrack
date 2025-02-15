CREATE VIEW overall_grades AS
SELECT 
    ag.student_id,
    COALESCE(ag.total_assignment_score, 0) AS total_assignment_score,
    COALESCE(ag.total_assignment_possible_score, 0) AS total_assignment_possible_score,
    COALESCE(qg.total_quiz_score, 0) AS total_quiz_score,
    COALESCE(qg.total_quiz_possible_score, 0) AS total_quiz_possible_score
FROM 
    assignment_grades ag
LEFT JOIN 
    quiz_grades qg ON ag.student_id = qg.student_id;
