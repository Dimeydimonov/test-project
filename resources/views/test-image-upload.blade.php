<!DOCTYPE html>
<html>
<head>
    <title>Тест загрузки изображений</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .result { margin-top: 20px; padding: 10px; border: 1px solid #ccc; }
        .success { background-color: #d4edda; border-color: #c3e6cb; }
        .error { background-color: #f8d7da; border-color: #f5c6cb; }
    </style>
</head>
<body>
    <h1>Тест загрузки изображений Laravel</h1>
    
    <form id="upload-form" enctype="multipart/form-data">
        <div>
            <label for="test_image">Выберите изображение:</label>
            <input type="file" id="test_image" name="test_image" accept="image/*" required>
        </div>
        <br>
        <button type="submit">Загрузить и протестировать</button>
    </form>
    
    <div id="result"></div>
    
    <script>
        document.getElementById('upload-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            const fileInput = document.getElementById('test_image');
            const file = fileInput.files[0];
            
            if (!file) {
                showResult('Выберите файл для загрузки', 'error');
                return;
            }
            
            formData.append('test_image', file);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            
            showResult('Загрузка...', 'info');
            
            fetch('/test-image-upload', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showResult(`
                        <h3>✅ Успешно загружено!</h3>
                        <p><strong>Путь:</strong> ${data.path}</p>
                        <p><strong>Полный путь:</strong> ${data.full_path}</p>
                        <p><strong>Файл существует:</strong> ${data.exists ? 'Да' : 'Нет'}</p>
                        <p><strong>Размер на диске:</strong> ${data.size_on_disk} байт</p>
                    `, 'success');
                } else {
                    showResult(`❌ Ошибка: ${data.error}`, 'error');
                }
            })
            .catch(error => {
                showResult(`❌ Ошибка сети: ${error.message}`, 'error');
            });
        });
        
        function showResult(message, type) {
            const resultDiv = document.getElementById('result');
            resultDiv.innerHTML = message;
            resultDiv.className = 'result ' + type;
        }
    </script>
</body>
</html>
