<?php
require '../Db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    try {
        $pdo = Db::getConnection();
        $lessonId = (int)$_POST['lessonId'];
        $stmt = $pdo->prepare("SELECT course_id FROM lessons WHERE id = ?");
        $stmt->execute([$lessonId]);
        $courseId = $stmt->fetchColumn();

        if (!$courseId) {
            throw new Exception("Invalid lesson or course ID");
        }
        $uploadDir = "uploads/course-{$courseId}/lesson-{$lessonId}/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $file = $_FILES['file'];
        $fileName = basename($file['name']);
        $filePath = $uploadDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            $stmt = $pdo->prepare("INSERT INTO files (lesson_id, file_name, file_path) VALUES (?, ?, ?)");
            if ($stmt->execute([$lessonId, $fileName, $filePath])) {
                echo json_encode(['status' => 'success', 'message' => 'File uploaded successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to save file info to the database']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error uploading file']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}