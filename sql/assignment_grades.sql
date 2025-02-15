CREATE VIEW assignment_grades AS
SELECT 
    student_id,
    SUM(score) AS total_assignment_score,
    SUM(perfect_score) AS total_assignment_possible_score
FROM 
    submissions
GROUP BY 
    student_id;
