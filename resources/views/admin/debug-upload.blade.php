<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Диагностика загрузки изображений</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>🔍 Диагностика загрузки изображений</h4>
                        <p class="mb-0 text-muted">Проверяем, почему изображения не загружаются через админ-панель</p>
                    </div>
                    <div class="card-body">
                        <form id="debug-form" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="title" class="form-label">Название произведения</label>
                                <input type="text" class="form-control" id="title" name="title" value="Тест диагностики" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Описание</label>
                                <textarea class="form-control" id="description" name="description" rows="3">Тестируем загрузку изображений</textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="price" class="form-label">Цена ($)</label>
                                <input type="number" class="form-control" id="price" name="price" value="100" step="0.01">
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="width" class="form-label">Ширина (см)</label>
                                        <input type="number" class="form-control" id="width" name="width" value="50">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="height" class="form-label">Высота (см)</label>
                                        <input type="number" class="form-control" id="height" name="height" value="70">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="images" class="form-label">Изображения</label>
                                <input type="file" class="form-control" id="images" name="images[]" multiple accept="image/*">
                                <div class="form-text">Выберите одно или несколько изображений для тестирования</div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-primary" onclick="testDebugUpload()">
                                    🔍 Тест диагностики загрузки
                                </button>
                                <button type="button" class="btn btn-success" onclick="testRealUpload()">
                                    ✅ Тест реальной загрузки
                                </button>
                            </div>
                        </form>
                        
                        <div id="result" class="mt-4" style="display: none;">
                            <div class="card">
                                <div class="card-header">
                                    <h6>Результат тестирования</h6>
                                </div>
                                <div class="card-body">
                                    <pre id="result-content"></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function testDebugUpload() {
            const form = document.getElementById('debug-form');
            const formData = new FormData(form);
            console.log('=== ДИАГНОСТИКА ФОРМЫ ===');
            console.log('Form data entries:');
            for (let [key, value] of formData.entries()) {
                if (key === 'images[]') {
                    console.log(`${key}: ${value.name} (${value.size} bytes)`);
                } else {
                    console.log(`${key}: ${value}`);
                }
            }
            
            fetch('/admin/debug/artwork-upload', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                                   document.querySelector('input[name="_token"]').value
                }
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('result').style.display = 'block';
                document.getElementById('result-content').textContent = JSON.stringify(data, null, 2);
                console.log('Debug response:', data);
            })
            .catch(error => {
                document.getElementById('result').style.display = 'block';
                document.getElementById('result-content').textContent = 'Ошибка: ' + error.message;
                console.error('Error:', error);
            });
        }
        
        function testRealUpload() {
            const form = document.getElementById('debug-form');
            const formData = new FormData(form);
            
            formData.append('is_published', '1');
            
            console.log('=== РЕАЛЬНАЯ ЗАГРУЗКА ===');
            console.log('Отправляем на /admin/artworks');
            
            fetch('/admin/artworks', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                                   document.querySelector('input[name="_token"]').value
                }
            })
            .then(response => {
                if (response.redirected) {
                    document.getElementById('result').style.display = 'block';
                    document.getElementById('result-content').textContent = 'Успех! Перенаправлен на: ' + response.url;
                    console.log('Success! Redirected to:', response.url);
                } else {
                    return response.text();
                }
            })
            .then(text => {
                if (text) {
                    document.getElementById('result').style.display = 'block';
                    document.getElementById('result-content').textContent = 'Response: ' + text.substring(0, 500) + '...';
                }
            })
            .catch(error => {
                document.getElementById('result').style.display = 'block';
                document.getElementById('result-content').textContent = 'Ошибка: ' + error.message;
                console.error('Error:', error);
            });
        }
    </script>
</body>
</html>
