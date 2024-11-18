<?php
    try {
        // Mendapatkan data dari request body
        $body = file_get_contents('php://input');
        $request = json_decode($body, true);

        // Validasi input
        if (!isset($request['nik']) || !isset($request['name'])) {
            throw new Exception('NIK dan Nama wajib diisi.');
        }

        $nik = $request['nik'];
        $name = $request['name'];

        // Koneksi ke database
        $pdo = new PDO('mysql:host=localhost;dbname=task37', 'root', ''); // Sesuaikan username dan password
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Query untuk menyimpan data
        $query = $pdo->prepare("INSERT INTO students (nik, nama) VALUES (:nik, :name)");
        $query->bindValue(':nik', $nik, PDO::PARAM_STR);
        $query->bindValue(':name', $name, PDO::PARAM_STR);

        // Eksekusi query
        if ($query->execute()) {
            // Ambil data yang baru saja disimpan
            $id = $pdo->lastInsertId();
            $selectQuery = $pdo->prepare("SELECT * FROM students WHERE id = :id");
            $selectQuery->bindValue(':id', $id, PDO::PARAM_INT);
            $selectQuery->execute();

            $newStudent = $selectQuery->fetch(PDO::FETCH_ASSOC);

            // Kirim respons sukses
            echo json_encode([
                'status' => true,
                'student' => $newStudent,
                'message' => 'Data berhasil disimpan.'
            ]);
        } else {
            throw new Exception('Gagal menyimpan data.');
        }
    } catch (Exception $e) {
        // Kirim respons gagal
        echo json_encode([
            'status' => false,
            'error' => $e->getMessage()
        ]);
    }
?>
