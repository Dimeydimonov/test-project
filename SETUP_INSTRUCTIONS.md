# Инструкции по установке и запуску Katrin Gallery

## Требования к системе

- PHP 8.3 или выше
- Composer
- Node.js и npm
- SQLite или MySQL (рекомендуется SQLite для разработки)

## Установка

### 1. Установка зависимостей PHP

```bash
composer install
```

### 2. Установка зависимостей Node.js

```bash
npm install
```

### 3. Настройка окружения

Скопируйте файл окружения:
```bash
cp .env.example .env
```

### 4. Генерация ключа приложения

```bash
php artisan key:generate
```

### 5. Настройка базы данных

#### Вариант A: SQLite (рекомендуется для разработки)

Создайте файл базы данных:
```bash
touch database/database.sqlite
```

В файле `.env` установите:
```
DB_CONNECTION=sqlite
DB_DATABASE=/полный/путь/к/проекту/database/database.sqlite
```

#### Вариант B: MySQL

В файле `.env` установите:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=katrin_gallery
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 6. Установка драйвера SQLite (если используете SQLite)

#### Ubuntu/Debian:
```bash
sudo apt-get install php-sqlite3
```

#### CentOS/RHEL:
```bash
sudo yum install php-sqlite3
```

#### macOS (через Homebrew):
```bash
brew install php
```

### 7. Запуск миграций

```bash
php artisan migrate
```

### 8. Заполнение базы данных тестовыми данными (опционально)

```bash
php artisan db:seed
```

### 9. Создание символической ссылки для storage

```bash
php artisan storage:link
```

### 10. Сборка фронтенда

```bash
npm run build
```

Или для разработки:
```bash
npm run dev
```

## Запуск проекта

### Запуск сервера разработки

```bash
php artisan serve
```

Сайт будет доступен по адресу: http://localhost:8000

### Создание администратора

Для доступа к админ-панели создайте пользователя с ролью admin:

```bash
php artisan tinker
```

В консоли tinker выполните:
```php
$user = new App\Models\User();
$user->name = 'Admin';
$user->email = 'admin@gallery.com';
$user->password = bcrypt('password');
$user->role = 'admin';
$user->save();
```

Теперь вы можете войти в админ-панель по адресу: http://localhost:8000/admin

## Структура проекта

### Основные компоненты:

- **Модели**: `app/Models/` - User, Artwork, Category, Comment, Like
- **Контроллеры**: `app/Http/Controllers/` - основные контроллеры
- **Админ-контроллеры**: `app/Http/Controllers/Admin/` - контроллеры админ-панели
- **Сервисы**: `app/Services/` - бизнес-логика
- **Представления**: `resources/views/` - Blade шаблоны
- **Стили**: `resources/css/gallery.css` - основные стили
- **Миграции**: `database/migrations/` - структура базы данных

### Ключевые возможности:

1. **Галерея произведений искусства** с категориями
2. **Система комментариев и лайков**
3. **Админ-панель** с полным управлением контентом
4. **Ролевая система доступа** (admin/user)
5. **Загрузка и управление изображениями**
6. **Адаптивный дизайн** с Bootstrap 5

## Решение проблем

### Проблема с правами доступа к storage

Если возникают проблемы с правами доступа, выполните:
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### Проблема с драйвером базы данных

Убедитесь, что установлен нужный драйвер PHP:
- Для SQLite: `php-sqlite3`
- Для MySQL: `php-mysql`

### Очистка кеша

Если что-то работает некорректно, очистите кеш:
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

## Разработка

### Запуск в режиме разработки

```bash
# Терминал 1: Laravel сервер
php artisan serve

# Терминал 2: Vite для фронтенда
npm run dev
```

### Создание нового произведения через админ-панель

1. Войдите в админ-панель: `/admin`
2. Перейдите в "Произведения" → "Добавить новое"
3. Заполните форму и загрузите изображение
4. Сохраните произведение

Проект готов к использованию!
