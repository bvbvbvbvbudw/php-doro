<?php
require '../Db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    try {
        $pdo = Db::getConnection();
        $data = json_decode(file_get_contents('php://input'), true);
        $files = $data['files'] ?? null;
        $singleFile = isset($_POST['oldFileName'], $_POST['newFileName'], $_POST['filePath']) ? $_POST : null;

        if ($files && is_array($files)) {
            foreach ($files as $file) {
                $oldFileName = $file['oldFileName'];
                $newFileName = $file['newFileName'];
                $filePath = $file['filePath'];

                renameFile($pdo, $oldFileName, $newFileName, $filePath);
            }

            echo json_encode(['status' => 'success', 'message' => 'Files renamed successfully']);
        }
        elseif ($singleFile) {
            $oldFileName = $singleFile['oldFileName'];
            $newFileName = $singleFile['newFileName'];
            $filePath = $singleFile['filePath'];

            renameFile($pdo, $oldFileName, $newFileName, $filePath);

            echo json_encode(['status' => 'success', 'message' => 'File renamed successfully']);
        }
        else {
            echo json_encode(['status' => 'error', 'message' => 'No files selected for renaming']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

function renameFile($pdo, $oldFileName, $newFileName, $filePath) {
    $newFilePath = dirname($filePath) . '/' . $newFileName;

    if (file_exists($filePath)) {
        if (rename($filePath, $newFilePath)) {
            $stmt = $pdo->prepare("UPDATE files SET file_name = ?, file_path = ? WHERE file_path = ?");
            if (!$stmt->execute([$newFileName, $newFilePath, $filePath])) {
                rename($newFilePath, $filePath);
                throw new Exception("Error updating file info in database for file: {$oldFileName}");
            }
        } else {
            throw new Exception("Error renaming file: {$oldFileName}");
        }
    } else {
        throw new Exception("File not found: {$filePath}");
    }
}
?>