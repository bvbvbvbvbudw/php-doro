<?php

require 'Db.php';

$pdo = Db::getConnection();

$pdo->exec("INSERT INTO courses (title) VALUES ('КУРС 1'), ('КУРС 2'), ('КУРС 3')");

$pdo->exec("
    INSERT INTO lessons (course_id, name) VALUES 
    (1, 'Заняття 1'),
    (1, 'Заняття 2'),
    (1, 'Фандрейзинг для громадських та релігійних організацій'),
    (1, 'Заняття 4'),
    (2, 'Заняття 1'),
    (2, 'Заняття 2'),
    (3, 'Заняття 1'),
    (3, 'Заняття 2')
");

function createDirectoriesAndFiles($courseId, $lessonId, $pdo) {
    $uploadDir = "uploads/course-{$courseId}/lesson-{$lessonId}/";

    if (!is_dir($uploadDir)) {
        mkdir('public/' . $uploadDir, 0777, true);
    }

    $testFileName = "test_lesson_{$lessonId}.txt";
    $testFilePath =  $uploadDir . $testFileName;
    file_put_contents('public/' . $testFilePath, "Test file for {$lessonId}. course: {$courseId}.");

    $pdo->exec("
        INSERT INTO files (lesson_id, file_name, file_path) VALUES 
        ({$lessonId}, '{$testFileName}', '{$testFilePath}')
    ");
}

$courses = $pdo->query("SELECT id FROM courses")->fetchAll(PDO::FETCH_ASSOC);
foreach ($courses as $course) {
    $courseId = $course['id'];
    $lessons = $pdo->query("SELECT id FROM lessons WHERE course_id = {$courseId}")->fetchAll(PDO::FETCH_ASSOC);

    foreach ($lessons as $lesson) {
        $lessonId = $lesson['id'];
        createDirectoriesAndFiles($courseId, $lessonId, $pdo);
    }
}
?>