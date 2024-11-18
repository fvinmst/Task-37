<?php
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=task37', 'root', ''); // Sesuaikan koneksi

        // Parameter pagination
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10; // Default limit 10
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Default page 1
        $offset = ($page - 1) * $limit;

        // Query data
        $query = $pdo->prepare("SELECT * FROM students LIMIT :limit OFFSET :offset");
        $query->bindValue(':limit', $limit, PDO::PARAM_INT);
        $query->bindValue(':offset', $offset, PDO::PARAM_INT);
        $query->execute();

        $students = $query->fetchAll(PDO::FETCH_ASSOC);

        // Total data untuk pagination
        $countQuery = $pdo->query("SELECT COUNT(*) AS total FROM students");
        $totalCount = $countQuery->fetch(PDO::FETCH_ASSOC)['total'];

        echo json_encode([
            'status' => true,
            'students' => $students,
            'total' => (int)$totalCount,
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'status' => false,
            'error' => $e->getMessage(),
        ]);
    }
?>
