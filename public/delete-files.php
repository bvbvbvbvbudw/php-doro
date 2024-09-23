<?php
require '../Db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    try {
        $pdo = Db::getConnection();

        $filesToDelete = $_POST['files'] ?? [];

        if (empty($filesToDelete)) {
            throw new Exception('The file list is empty.');
        }

        foreach ($filesToDelete as $filePath) {
            if (file_exists($filePath)) {
                if (!unlink($filePath)) {
                    throw new Exception("Failed to delete file: $filePath");
                }
                $stmt = $pdo->prepare("DELETE FROM files WHERE file_path = ?");
                $stmt->execute([$filePath]);
            } else {
                throw new Exception("File not found: $filePath");
            }
        }

        echo json_encode(['status' => 'success']);

    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
}
?>
