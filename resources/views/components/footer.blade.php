<footer class="bg-white mt-12 border-t border-gray-200">
    <div class="max-w-7xl mx-auto py-12 px-4 overflow-hidden sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="col-span-1 md:col-span-2">
                <h3 class="footer-heading">ArtGallery</h3>
                <p class="footer-text">
                    Художественная галерея, где талантливые художники со всего мира делятся своими произведениями искусства. 
                    Откройте для себя удивительный мир творчества и вдохновения.
                </p>
                <div class="social-links">
                    <a href="#" class="social-link" aria-label="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="social-link" aria-label="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="social-link" aria-label="Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="social-link" aria-label="Pinterest">
                        <i class="fab fa-pinterest"></i>
                    </a>
                </div>
            </div>

            <!-- Навигация -->
            <div>
                <h3 class="footer-heading">Навигация</h3>
                <ul class="footer-links">
                    <li><a href="{{ route('home') }}" class="footer-link">Главная</a></li>
                    <li><a href="{{ route('gallery.all') }}" class="footer-link">Галерея</a></li>
                    <li><a href="#" class="footer-link">Категории</a></li>
                    <li><a href="#" class="footer-link">О нас</a></li>
                </ul>
            </div>

            <!-- Поддержка -->
            <div>
                <h3 class="footer-heading">Поддержка</h3>
                <ul class="footer-links">
                    <li><a href="#" class="footer-link">Помощь</a></li>
                    <li><a href="#" class="footer-link">Контакты</a></li>
                    <li><a href="#" class="footer-link">Политика конфиденциальности</a></li>
                    <li><a href="#" class="footer-link">Условия использования</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p class="footer-copyright">
                &copy; {{ date('Y') }} ArtGallery. Все права защищены.
            </p>
            <p class="footer-made-with">
                Создано с <i class="fas fa-heart text-red-500"></i> для любителей искусства
            </p>
        </div>
    </div>
</footer>
