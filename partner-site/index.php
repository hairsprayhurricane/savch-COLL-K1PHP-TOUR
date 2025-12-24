<?php
require_once 'api-client.php';


$api = new ComfortTravelApiClient();
$message = '';
$error = '';
// Обработка форм
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'create_country':
                    $api->createCountry(
                        $_POST['name'],
                        $_POST['code'],
                        isset($_POST['visa_required'])
                    );
                    $message = 'Страна успешно создана!';
                    break;
                    
                case 'create_client':
                    $api->createClient(
                        $_POST['full_name'],
                        $_POST['passport_number'],
                        $_POST['phone'],
                        $_POST['email'],
                        $_POST['birth_date']
                    );
                    $message = 'Клиент успешно создан!';
                    break;
                    
                case 'create_tour':
                    $api->createTour(
                        $_POST['country_id'],
                        $_POST['name'],
                        $_POST['description'],
                        $_POST['start_date'],
                        $_POST['end_date'],
                        $_POST['price'],
                        $_POST['max_people']
                    );
                    $message = 'Тур успешно создан!';
                    break;
                    
                case 'create_booking':
                    $api->createBooking(
                        $_POST['client_id'],
                        $_POST['tour_id'],
                        $_POST['booking_date'],
                        $_POST['total_price'],
                        $_POST['notes']
                    );
                    $message = 'Бронирование успешно создано!';
                    break;
            }
        }
    } catch (Exception $e) {
        $error = 'Ошибка: ' . $e->getMessage();
    }
}


// Получение данных для отображения
try {
    $countries = $api->getCountries();
    $clients = $api->getClients();
    $tours = $api->getTours();
    $bookings = $api->getBookings();
} catch (Exception $e) {
    $error = 'Ошибка загрузки данных: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель "Комфорт-отдых"</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .message { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .section { margin: 30px 0; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .form-group { margin: 10px 0; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select, textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #007bff; color: white; border: none; padding: 10px 15px; border-radius: 4px; cursor: pointer; }
        button:hover { background: #0056b3; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f8f9fa; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; }
        .stat-card { background: #f8f9fa; padding: 20px; border-radius: 5px; text-align: center; }
        .stat-value { font-size: 24px; font-weight: bold; color: #007bff; }
        .tabs { display: flex; border-bottom: 1px solid #ddd; margin-bottom: 20px; }
        .tab { padding: 10px 20px; cursor: pointer; border: 1px solid transparent; }
        .tab.active { border: 1px solid #ddd; border-bottom-color: white; margin-bottom: -1px; background: white; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📊 Админ-панель "Комфорт-отдых"</h1>
        
        <?php if ($message): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <!-- Вкладки -->
        <div class="tabs">
            <div class="tab active" onclick="showTab('dashboard')">Дашборд</div>
            <div class="tab" onclick="showTab('countries')">Страны</div>
            <div class="tab" onclick="showTab('clients')">Клиенты</div>
            <div class="tab" onclick="showTab('tours')">Туры</div>
            <div class="tab" onclick="showTab('bookings')">Бронирования</div>
        </div>
        
        <!-- Дашборд -->
        <div id="dashboard" class="tab-content active">
            <h2>📈 Статистика</h2>
            <div class="stats">
                <div class="stat-card">
                    <div class="stat-value"><?php echo count($countries['data'] ?? []); ?></div>
                    <div>Стран</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo count($clients['data'] ?? []); ?></div>
                    <div>Клиентов</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo count($tours['data'] ?? []); ?></div>
                    <div>Туров</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo count($bookings['data'] ?? []); ?></div>
                    <div>Бронирований</div>
                </div>
            </div>
            
            <h2>Быстрые действия</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;">
                <button onclick="showTab('countries')">➕ Добавить страну</button>
                <button onclick="showTab('clients')">➕ Добавить клиента</button>
                <button onclick="showTab('tours')">➕ Добавить тур</button>
                <button onclick="showTab('bookings')">➕ Добавить бронирование</button>
            </div>
        </div>
        
        <!-- Страны -->
        <div id="countries" class="tab-content">
            <h2>🌍 Страны</h2>
            
            <!-- Форма добавления страны -->
            <div class="section">
                <h3>Добавить новую страну</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="create_country">
                    <div class="form-group">
                        <label>Название страны:</label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Код страны (2 буквы):</label>
                        <input type="text" name="code" maxlength="2" required>
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="visa_required" value="1">
                            Требуется виза
                        </label>
                    </div>
                    <button type="submit">Добавить страну</button>
                </form>
            </div>
            
            <!-- Список стран -->
            <div class="section">
                <h3>Список стран</h3>
                <?php if (isset($countries['data']) && count($countries['data']) > 0): ?>
                    <table>
                        <tr>
                            <th>ID</th>
                            <th>Название</th>
                            <th>Код</th>
                            <th>Виза</th>
                            <th>Дата создания</th>
                        </tr>
                        <?php foreach ($countries['data'] as $country): ?>
                            <tr>
                                <td><?php echo $country['id']; ?></td>
                                <td><?php echo htmlspecialchars($country['name']); ?></td>
                                <td><?php echo htmlspecialchars($country['code']); ?></td>
                                <td><?php echo $country['visa_required'] ? '✅ Требуется' : '❌ Не требуется'; ?></td>
                                <td><?php echo $country['created_at']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php else: ?>
                    <p>Страны не найдены</p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Клиенты -->
        <div id="clients" class="tab-content">
            <h2>👥 Клиенты</h2>
            
            <!-- Форма добавления клиента -->
            <div class="section">
                <h3>Добавить нового клиента</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="create_client">
                    <div class="form-group">
                        <label>ФИО:</label>
                        <input type="text" name="full_name" required>
                    </div>
                    <div class="form-group">
                        <label>Номер паспорта:</label>
                        <input type="text" name="passport_number" required>
                    </div>
                    <div class="form-group">
                        <label>Телефон:</label>
                        <input type="tel" name="phone" required>
                    </div>
                    <div class="form-group">
                        <label>Email:</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Дата рождения:</label>
                        <input type="date" name="birth_date" required>
                    </div>
                    <button type="submit">Добавить клиента</button>
                </form>
            </div>
            
            <!-- Список клиентов -->
            <div class="section">
                <h3>Список клиентов</h3>
                <?php if (isset($clients['data']) && count($clients['data']) > 0): ?>
                    <table>
                        <tr>
                            <th>ID</th>
                            <th>ФИО</th>
                            <th>Паспорт</th>
                            <th>Телефон</th>
                            <th>Email</th>
                            <th>Дата рождения</th>
                        </tr>
                        <?php foreach ($clients['data'] as $client): ?>
                            <tr>
                                <td><?php echo $client['id']; ?></td>
                                <td><?php echo htmlspecialchars($client['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($client['passport_number']); ?></td>
                                <td><?php echo htmlspecialchars($client['phone']); ?></td>
                                <td><?php echo htmlspecialchars($client['email']); ?></td>
                                <td><?php echo $client['birth_date']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php else: ?>
                    <p>Клиенты не найдены</p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Туры -->
        <div id="tours" class="tab-content">
            <h2>✈️ Туры</h2>
            
            <!-- Форма добавления тура -->
            <div class="section">
                <h3>Добавить новый тур</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="create_tour">
                    <div class="form-group">
                        <label>Страна:</label>
                        <select name="country_id" required>
                            <option value="">Выберите страну</option>
                            <?php if (isset($countries['data'])): ?>
                                <?php foreach ($countries['data'] as $country): ?>
                                    <option value="<?php echo $country['id']; ?>">
                                        <?php echo htmlspecialchars($country['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Название тура:</label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Описание:</label>
                        <textarea name="description" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Дата начала:</label>
                        <input type="date" name="start_date" required>
                    </div>
                    <div class="form-group">
                        <label>Дата окончания:</label>
                        <input type="date" name="end_date" required>
                    </div>
                    <div class="form-group">
                        <label>Цена (руб):</label>
                        <input type="number" name="price" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Максимум человек:</label>
                        <input type="number" name="max_people" required>
                    </div>
                    <button type="submit">Добавить тур</button>
                </form>
            </div>
            
            <!-- Список туров -->
            <div class="section">
                <h3>Список туров</h3>
                <?php if (isset($tours['data']) && count($tours['data']) > 0): ?>
                    <table>
                        <tr>
                            <th>ID</th>
                            <th>Название</th>
                            <th>Страна</th>
                            <th>Дата начала</th>
                            <th>Дата окончания</th>
                            <th>Цена</th>
                            <th>Макс. людей</th>
                        </tr>
                        <?php foreach ($tours['data'] as $tour): ?>
                            <tr>
                                <td><?php echo $tour['id']; ?></td>
                                <td><?php echo htmlspecialchars($tour['name']); ?></td>
                                <td><?php echo htmlspecialchars($tour['country_id']); ?></td>
                                <td><?php echo $tour['start_date']; ?></td>
                                <td><?php echo $tour['end_date']; ?></td>
                                <td><?php echo number_format($tour['price'], 2, ',', ' '); ?> ₽</td>
                                <td><?php echo $tour['max_people']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php else: ?>
                    <p>Туры не найдены</p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Бронирования -->
        <div id="bookings" class="tab-content">
            <h2>📅 Бронирования</h2>
            
            <!-- Форма добавления бронирования -->
            <div class="section">
                <h3>Добавить новое бронирование</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="create_booking">
                    <div class="form-group">
                        <label>Клиент:</label>
                        <select name="client_id" required>
                            <option value="">Выберите клиента</option>
                            <?php if (isset($clients['data'])): ?>
                                <?php foreach ($clients['data'] as $client): ?>
                                    <option value="<?php echo $client['id']; ?>">
                                        <?php echo htmlspecialchars($client['full_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Тур:</label>
                        <select name="tour_id" required>
                            <option value="">Выберите тур</option>
                            <?php if (isset($tours['data'])): ?>
                                <?php foreach ($tours['data'] as $tour): ?>
                                    <option value="<?php echo $tour['id']; ?>">
                                        <?php echo htmlspecialchars($tour['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Дата бронирования:</label>
                        <input type="date" name="booking_date" required>
                    </div>
                    <div class="form-group">
                        <label>Общая стоимость (руб):</label>
                        <input type="number" name="total_price" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Заметки:</label>
                        <textarea name="notes" rows="3"></textarea>
                    </div>
                    <button type="submit">Добавить бронирование</button>
                </form>
            </div>
            
            <!-- Список бронирований -->
            <div class="section">
                <h3>Список бронирований</h3>
                <?php if (isset($bookings['data']) && count($bookings['data']) > 0): ?>
                    <table>
                        <tr>
                            <th>ID</th>
                            <th>Клиент</th>
                            <th>Тур</th>
                            <th>Дата бронирования</th>
                            <th>Сумма</th>
                        </tr>
                        <?php foreach ($bookings['data'] as $booking): ?>
                            <tr>
                                <td><?php echo $booking['id']; ?></td>
                                <td><?php echo htmlspecialchars($booking['client_id']); ?></td>
                                <td><?php echo htmlspecialchars($booking['tour_id']); ?></td>
                                <td><?php echo $booking['booking_date']; ?></td>
                                <td><?php echo number_format($booking['total_price'], 2, ',', ' '); ?> ₽</td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php else: ?>
                    <p>Бронирования не найдены</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script>
        // Функция для переключения вкладок
        function showTab(tabName) {
            // Скрыть все вкладки
            const tabContents = document.querySelectorAll('.tab-content');
            if (tabContents) {
                tabContents.forEach(tab => {
                    if (tab) tab.classList.remove('active');
                });
            }
            
            // Убрать активный класс у всех табов
            const tabs = document.querySelectorAll('.tab');
            if (tabs) {
                tabs.forEach(tab => {
                    if (tab) tab.classList.remove('active');
                });
            }
            
            // Показать выбранную вкладку
            const activeTab = document.getElementById(tabName);
            if (activeTab) activeTab.classList.add('active');
            
            // Активировать соответствующий таб
            document.querySelectorAll('.tab').forEach(tab => {
                if (tab && tab.textContent && tab.textContent.includes(getTabLabel(tabName))) {
                    tab.classList.add('active');
                }
            });
        }
        
        // Вспомогательная функция для получения метки таба
        function getTabLabel(tabName) {
            const labels = {
                'dashboard': 'Дашборд',
                'countries': 'Страны',
                'clients': 'Клиенты',
                'tours': 'Туры',
                'bookings': 'Бронирования'
            };
            return labels[tabName] || tabName;
        }
        
        // Initialize tabs
        function initTabs() {
            // Add click handlers to all tabs
            document.querySelectorAll('.tab').forEach(tab => {
                tab.addEventListener('click', function() {
                    const tabText = this.textContent.trim().toLowerCase();
                    const tabMap = {
                        'дашборд': 'dashboard',
                        'страны': 'countries',
                        'клиенты': 'clients',
                        'туры': 'tours',
                        'бронирования': 'bookings'
                    };
                    
                    if (tabMap[tabText]) {
                        showTab(tabMap[tabText]);
                        localStorage.setItem('lastActiveTab', tabText);
                    }
                });
            });
            
            // Show default tab
            showTab('dashboard');
        }
        
        // Initialize the application when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tabs
            initTabs();
            
            // Restore last active tab from localStorage
            const lastActiveTab = localStorage.getItem('lastActiveTab');
            if (lastActiveTab) {
                const tabMap = {
                    'дашборд': 'dashboard',
                    'страны': 'countries',
                    'клиенты': 'clients',
                    'туры': 'tours',
                    'бронирования': 'bookings'
                };
                
                if (tabMap[lastActiveTab]) {
                    showTab(tabMap[lastActiveTab]);
                }
            }
        });
        
        // Автоматическое обновление данных каждые 30 секунд
        setInterval(() => {
            console.log('Автообновление данных...');
        }, 30000);
        
        // Функция для поиска бронирований
        function searchBookings(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            const params = new URLSearchParams();
            
            if (formData.get('client_id')) params.append('client_id', formData.get('client_id'));
            if (formData.get('tour_id')) params.append('tour_id', formData.get('tour_id'));

            fetch(`api-client.php?action=getBookings&${params.toString()}`)
                .then(response => response.json())
                .then(data => {
                    const bookingsList = document.getElementById('bookingsList');
                    if (data.error) {
                        bookingsList.innerHTML = `<div class="error">${data.error}</div>`;
                        return;
                    }
                    
                    if (data.length === 0) {
                        bookingsList.innerHTML = '<p>Бронирований не найдено</p>';
                        return;
                    }

                    let html = '<table><tr><th>ID</th><th>Клиент</th><th>Тур</th><th>Дата бронирования</th><th>Статус</th></tr>';
                    data.forEach(booking => {
                        html += `
                            <tr>
                                <td>${booking.id}</td>
                                <td>${booking.client_name || 'Неизвестно'}</td>
                                <td>${booking.tour_name || 'Неизвестно'}</td>
                                <td>${new Date(booking.booking_date).toLocaleDateString()}</td>
                                <td>${booking.status || 'Ожидает подтверждения'}</td>
                            </tr>
                        `;
                    });
                    html += '</table>';
                    bookingsList.innerHTML = html;
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('bookingsList').innerHTML = '<div class="error">Ошибка при загрузке бронирований</div>';
                });
        }
    </script>
</body>
</html>