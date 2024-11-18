<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Validasi & Tabel Data</title>

    <script src="https://cdn.jsdelivr.net/npm/just-validate@3.5.0/dist/just-validate.production.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        * {
            box-sizing:border-box;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 50px;
            background-color: #f0f0f0;
        }

        form {
            margin-bottom: 20px;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        input {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            margin-bottom: 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .is-invalid {
            border-color: red;
        }

        .error {
            color: red;
            font-size: 12px;
            margin-top: -10px;
            margin-bottom: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #281796;
            color: white;
        }

        .loader {
            width: 48px;
            height: 48px;
            border: 5px solid #FFF;
            border-bottom-color: #FF3D00;
            border-radius: 50%;
            display: block;
            margin: 16px auto;
            box-sizing: border-box;
            animation: rotation 1s linear infinite;
        }

        @keyframes rotation {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .pagination button {
            padding: 8px 16px;
            margin: 0 5px;
            border: none;
            background-color: #281796;
            color: white;
            cursor: pointer;
            border-radius: 4px;
        }

        .pagination button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
    </style>
</head>
<body>

    <form id="dataForm">
        <div>
            <input type="text" name="nik" id="nik" placeholder="Masukkan NIK">
        </div>
        <div>
            <input type="text" name="name" id="name" placeholder="Masukkan Nama">
        </div>
        <button type="submit">Simpan</button>
    </form>

    <label for="limitSelect">Items per page:</label>
    <select id="limitSelect">
        <option value="5">5</option>
        <option value="10" selected>10</option>
        <option value="15">15</option>
    </select>

    <table border="1">
        <thead>
            <tr>
                <th>No</th>
                <th>NIK</th>
                <th>Nama</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <div class="pagination">
        <button id="prevBtn" disabled>Previous</button>
        <button id="nextBtn">Next</button>
    </div>

    <script>
        let currentPage = 1;
        let itemsPerPage = 10;

        function renderTable(data, total, page) {
            const tableBody = document.querySelector('table tbody');
            tableBody.innerHTML = '';
            data.forEach((student, index) => {
                const row = `
                    <tr>
                        <td>${(page - 1) * itemsPerPage + index + 1}</td>
                        <td>${student.nik}</td>
                        <td>${student.nama}</td>
                    </tr>
                `;
                tableBody.innerHTML += row;
            });

            document.getElementById('prevBtn').disabled = page === 1;
            document.getElementById('nextBtn').disabled = page * itemsPerPage >= total;
        }

        function fetchDataAndRender(page = 1) {
            axios.get(`get-students.php?limit=${itemsPerPage}&page=${page}`)
                .then(response => {
                    if (response.data.status) {
                        renderTable(response.data.students, response.data.total, page);
                    } else {
                        alert('Error fetching data: ' + response.data.error);
                    }
                })
                .catch(error => {
                    console.error(error);
                });
        }

        document.getElementById('dataForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const nik = document.getElementById('nik').value;
            const name = document.getElementById('name').value;

            axios.post('save-students.php', { nik, name }, {
                headers: { 'Content-Type': 'application/json' }
            }).then(response => {
                if (response.data.status) {
                    fetchDataAndRender(currentPage);
                    document.getElementById('dataForm').reset();
                } else {
                    alert('Error saving data: ' + response.data.error);
                }
            }).catch(error => {
                console.error(error);
            });
        });

        document.getElementById('prevBtn').addEventListener('click', function() {
            if (currentPage > 1) {
                currentPage--;
                fetchDataAndRender(currentPage);
            }
        });

        document.getElementById('nextBtn').addEventListener('click', function() {
            currentPage++;
            fetchDataAndRender(currentPage);
        });

        document.getElementById('limitSelect').addEventListener('change', function() {
            itemsPerPage = parseInt(this.value);
            currentPage = 1;
            fetchDataAndRender(currentPage);
        });

        fetchDataAndRender(currentPage);
    </script>

</body>
</html>
