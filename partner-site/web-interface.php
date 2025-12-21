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
        * { 
            box-sizing: border-box; 
        }
        
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
            color: #333;
        }
        
        .container { 
            max-width: 1200px; 
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        h1 {
            color: #2c3e50;
            border-bottom: 3px solid #3498db;
            padding-bottom: 15px;
            margin-top: 0;
        }
        
        h2 {
            color: #34495e;
            margin-top: 30px;
        }
        
        h3 {
            color: #555;
        }
        
        .message { 
            background: #d4edda; 
            color: #155724; 
            padding: 15px; 
            border-radius: 5px; 
            margin: 15px 0;
            border-left: 4px solid #28a745;
        }
        
        .error { 
            background: #f8d7da; 
            color: #721c24; 
            padding: 15px; 
            border-radius: 5px; 
            margin: 15px 0;
            border-left: 4px solid #dc3545;
        }
        
        .section { 
            margin: 25px 0; 
            padding: 20px; 
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            background: #fafafa;
        }
        
        .form-group { 
            margin: 15px 0; 
        }
        
        label { 
            display: block; 
            margin-bottom: 8px; 
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }
        
        input, select, textarea { 
            width: 100%; 
            padding: 10px; 
            border: 1px solid #ddd; 
            border-radius: 4px;
            font-size: 14px;
            font-family: inherit;
            transition: border-color 0.3s ease;
        }
        
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.3);
        }
        
        button { 
            background: #3498db; 
            color: white; 
            border: none; 
            padding: 12px 24px; 
            border-radius: 4px; 
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: background 0.3s ease;
        }
        
        button:hover { 
            background: #2980b9;
        }
        
        button:active {
            transform: translateY(1px);
        }
        
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 15px 0;
            background: white;
            border-radius: 4px;
            overflow: hidden;
        }
        
        th, td { 
            border: 1px solid #e0e0e0;
            padding: 12px;
            text-align: left;
            font-size: 14px;
        }
        
        th { 
            background: #3498db;
            color: white;
            font-weight: 600;
        }
        
        tr:hover {
            background: #f9f9f9;
        }
        
        .stats { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
            gap: 20px;
            margin: 20px 0;
        }
        
        .stat-card { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 25px; 
            border-radius: 8px; 
            text-align: center;
            color: white;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-value { 
            font-size: 32px; 
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .tabs { 
            display: flex; 
            border-bottom: 2px solid #e0e0e0; 
            margin-bottom: 25px;
            gap: 5px;
        }
        
        .tab { 
            padding: 12px 24px; 
            cursor: pointer; 
            border: 2px solid transparent;
            background: transparent;
            color: #666;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            border-radius: 4px 4px 0 0;
        }
        
        .tab:hover {
            background: #f0f0f0;
        }
        
        .tab.active { 
            border-bottom: 3px solid #3498db;
            color: #3498db;
            background: #f0f7ff;
        }
        
        .tab-content { 
            display: none; 
        }
        
        .tab-content.active { 
            display: block;
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        
        .quick-actions button {
            background: #27ae60;
            padding: 15px;
            text-align: center;
            font-size: 13px;
        }
        
        .quick-actions button:hover {
            background: #229954;
        }
        
        .checkbox-label {
            display: flex;
            align-items: center;
            font-weight: normal;
        }
        
        .checkbox-label input[type="checkbox"] {
            width: auto;
            margin-right: 8px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📊 Админ-панель "Комфорт-отдых"</h1>
        
        <?php if ($message): ?>
            <div class="message">✅ <?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="error">❌ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <!-- Вкладки -->
        <div class="tabs">
            <div class="tab active" data-tab="dashboard">📈 Дашборд</div>
            <div class="tab" data-tab="countries">🌍 Страны</div>
            <div class="tab" data-tab="clients">👥 Клиенты</div>
            <div class="tab" data-tab="tours">✈️ Туры</div>
            <div class="tab" data-tab="bookings">📅 Бронирования</div>
        </div>
        
        <!-- Дашборд -->
        <div id="dashboard" class="tab-content active">
            <h2>📈 Общая статистика</h2>
            <div class="stats">
                <div class="stat-card">
                    <div class="stat-value"><?php echo count($countries['data'] ?? []); ?></div>
                    <div>Стран в базе</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo count($clients['data'] ?? []); ?></div>
                    <div>Клиентов зарегистрировано</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo count($tours['data'] ?? []); ?></div>
                    <div>Доступных туров</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo count($bookings['data'] ?? []); ?></div>
                    <div>Всего бронирований</div>
                </div>
            </div>
            
            <h2>Быстрые действия</h2>
            <div class="quick-actions">
                <button onclick="showTab('countries')">➕ Добавить страну</button>
                <button onclick="showTab('clients')">➕ Добавить клиента</button>
                <button onclick="showTab('tours')">➕ Добавить тур</button>
                <button onclick="showTab('bookings')">➕ Добавить бронирование</button>
            </div>
        </div>
        
        <!-- Страны -->
        <div id="countries" class="tab-content">
            <h2>🌍 Управление странами</h2>
            
            <!-- Форма добавления страны -->
            <div class="section">
                <h3>Добавить новую страну</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="create_country">
                    <div class="form-group">
                        <label>Название страны:</label>
                        <input type="text" name="name" required placeholder="Например: Испания">
                    </div>
                    <div class="form-group">
                        <label>Код страны (ISO 2):</label>
                        <input type="text" name="code" maxlength="2" required placeholder="ES">
                    </div>
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="visa_required" value="1">
                            Требуется виза для граждан РФ
                        </label>
                    </div>
                    <button type="submit">✅ Добавить страну</button>
                </form>
            </div>
            
            <!-- Список стран -->
            <div class="section">
                <h3>Список всех стран (<?php echo count($countries['data'] ?? []); ?>)</h3>
                <?php if (isset($countries['data']) && count($countries['data']) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Название</th>
                                <th>Код</th>
                                <th>Виза требуется</th>
                                <th>Дата создания</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($countries['data'] as $country): ?>
                                <tr>
                                    <td><?php echo $country['id']; ?></td>
                                    <td><strong><?php echo htmlspecialchars($country['name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($country['code']); ?></td>
                                    <td><?php echo $country['visa_required'] ? '✅ Да' : '❌ Нет'; ?></td>
                                    <td><?php echo $country['created_at']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="text-align: center; color: #999; padding: 20px;">ℹ️ Нет добавленных стран</p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Клиенты -->
        <div id="clients" class="tab-content">
            <h2>👥 Управление клиентами</h2>
            
            <!-- Форма добавления клиента -->
            <div class="section">
                <h3>Добавить нового клиента</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="create_client">
                    <div class="form-group">
                        <label>ФИО:</label>
                        <input type="text" name="full_name" required placeholder="Иван Иванов">
                    </div>
                    <div class="form-group">
                        <label>Номер паспорта:</label>
                        <input type="text" name="passport_number" required placeholder="1234567890">
                    </div>
                    <div class="form-group">
                        <label>Номер телефона:</label>
                        <input type="tel" name="phone" required placeholder="+7 (999) 123-45-67">
                    </div>
                    <div class="form-group">
                        <label>Email:</label>
                        <input type="email" name="email" required placeholder="client@example.com">
                    </div>
                    <div class="form-group">
                        <label>Дата рождения:</label>
                        <input type="date" name="birth_date" required>
                    </div>
                    <button type="submit">✅ Добавить клиента</button>
                </form>
            </div>
            
            <!-- Список клиентов -->
            <div class="section">
                <h3>Список всех клиентов (<?php echo count($clients['data'] ?? []); ?>)</h3>
                <?php if (isset($clients['data']) && count($clients['data']) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>ФИО</th>
                                <th>Паспорт</th>
                                <th>Телефон</th>
                                <th>Email</th>
                                <th>Дата рождения</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clients['data'] as $client): ?>
                                <tr>
                                    <td><?php echo $client['id']; ?></td>
                                    <td><strong><?php echo htmlspecialchars($client['full_name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($client['passport_number']); ?></td>
                                    <td><?php echo htmlspecialchars($client['phone']); ?></td>
                                    <td><?php echo htmlspecialchars($client['email']); ?></td>
                                    <td><?php echo $client['birth_date']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="text-align: center; color: #999; padding: 20px;">ℹ️ Нет добавленных клиентов</p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Туры -->
        <div id="tours" class="tab-content">
            <h2>✈️ Управление турами</h2>
            
            <!-- Форма добавления тура -->
            <div class="section">
                <h3>Добавить новый тур</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="create_tour">
                    <div class="form-group">
                        <label>Страна:</label>
                        <select name="country_id" required>
                            <option value="">-- Выберите страну --</option>
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
                        <input type="text" name="name" required placeholder="Пляжный отдых в Испании">
                    </div>
                    <div class="form-group">
                        <label>Описание:</label>
                        <textarea name="description" rows="4" placeholder="Подробное описание тура..."></textarea>
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
                        <label>Цена за человека (руб):</label>
                        <input type="number" name="price" step="0.01" min="0" required placeholder="50000">
                    </div>
                    <div class="form-group">
                        <label>Максимум участников:</label>
                        <input type="number" name="max_people" min="1" required placeholder="25">
                    </div>
                    <button type="submit">✅ Добавить тур</button>
                </form>
            </div>
            
            <!-- Список туров -->
            <div class="section">
                <h3>Список всех туров (<?php echo count($tours['data'] ?? []); ?>)</h3>
                <?php if (isset($tours['data']) && count($tours['data']) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Страна</th>
                                <th>Название</th>
                                <th>Период</th>
                                <th>Цена</th>
                                <th>Макс. чел.</th>
                                <th>Создан</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tours['data'] as $tour): ?>
                                <tr>
                                    <td><?php echo $tour['id']; ?></td>
                                    <td><?php echo htmlspecialchars($tour['country_name'] ?? 'N/A'); ?></td>
                                    <td><strong><?php echo htmlspecialchars($tour['name']); ?></strong></td>
                                    <td><?php echo $tour['start_date'] . ' — ' . $tour['end_date']; ?></td>
                                    <td><strong><?php echo number_format($tour['price'], 0, '.', ' '); ?> ₽</strong></td>
                                    <td><?php echo $tour['max_people']; ?></td>
                                    <td><?php echo $tour['created_at']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="text-align: center; color: #999; padding: 20px;">ℹ️ Нет добавленных туров</p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Бронирования -->
        <div id="bookings" class="tab-content">
            <h2>📅 Управление бронированиями</h2>
            
            <!-- Форма создания бронирования -->
            <div class="section">
                <h3>Создать бронирование</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="create_booking">
                    <div class="form-group">
                        <label>Клиент:</label>
                        <select name="client_id" required>
                            <option value="">-- Выберите клиента --</option>
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
                            <option value="">-- Выберите тур --</option>
                            <?php if (isset($tours['data'])): ?>
                                <?php foreach ($tours['data'] as $tour): ?>
                                    <option value="<?php echo $tour['id']; ?>">
                                        <?php echo htmlspecialchars($tour['name'] . ' (' . $tour['start_date'] . ' - ' . $tour['end_date'] . ')'); ?>
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
                        <label>Общая цена (руб):</label>
                        <input type="number" name="total_price" step="0.01" min="0" required placeholder="100000">
                    </div>
                    <div class="form-group">
                        <label>Примечания:</label>
                        <textarea name="notes" rows="3" placeholder="Дополнительная информация о бронировании..."></textarea>
                    </div>
                    <button type="submit">✅ Создать бронирование</button>
                </form>
            </div>
            
            <!-- Список бронирований -->
            <div class="section">
                <h3>Список всех бронирований (<?php echo count($bookings['data'] ?? []); ?>)</h3>
                <?php if (isset($bookings['data']) && count($bookings['data']) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Клиент</th>
                                <th>Тур</th>
                                <th>Дата бронирования</th>
                                <th>Сумма</th>
                                <th>Примечания</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bookings['data'] as $booking): ?>
                                <tr>
                                    <td><?php echo $booking['id']; ?></td>
                                    <td><strong><?php echo htmlspecialchars($booking['client_name'] ?? 'N/A'); ?></strong></td>
                                    <td><?php echo htmlspecialchars($booking['tour_name'] ?? 'N/A'); ?></td>
                                    <td><?php echo $booking['booking_date']; ?></td>
                                    <td><strong><?php echo number_format($booking['total_price'], 0, '.', ' '); ?> ₽</strong></td>
                                    <td><?php echo htmlspecialchars(substr($booking['notes'] ?? '', 0, 50)); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="text-align: center; color: #999; padding: 20px;">ℹ️ Нет созданных бронирований</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

        <script>
        // Флаг для проверки готовности DOM
        let isReady = false;
        
        // Функция переключения вкладок - ГЛОБАЛЬНАЯ
        window.showTab = function(tabName) {
            if (!tabName) return;
            
            try {
                // Проверка существования элементов
                const targetContent = document.getElementById(tabName);
                if (!targetContent) {
                    console.error('Tab content not found:', tabName);
                    return;
                }
                
                // Скрыть все содержимое вкладок
                const allContents = document.querySelectorAll('.tab-content');
                if (allContents && allContents.length > 0) {
                    allContents.forEach(function(el) {
                        if (el && el.classList) {
                            el.classList.remove('active');
                        }
                    });
                }
                
                // Убрать активный класс у всех табов
                const allTabs = document.querySelectorAll('.tab[data-tab]');
                if (allTabs && allTabs.length > 0) {
                    allTabs.forEach(function(el) {
                        if (el && el.classList) {
                            el.classList.remove('active');
                        }
                    });
                }
                
                // Активировать нужный контент
                if (targetContent && targetContent.classList) {
                    targetContent.classList.add('active');
                }
                
                // Активировать нужный таб
                const activeTab = document.querySelector('[data-tab="' + tabName + '"]');
                if (activeTab && activeTab.classList) {
                    activeTab.classList.add('active');
                }
                
            } catch (e) {
                console.error('Error in showTab:', e);
            }
        };
        
        // Инициализация при полной загрузке DOM
        function initializeTabs() {
            if (isReady) return;
            isReady = true;
            
            try {
                // Добавляем обработчики кликов ко всем табам
                const tabs = document.querySelectorAll('.tab[data-tab]');
                if (tabs && tabs.length > 0) {
                    tabs.forEach(function(tab) {
                        tab.addEventListener('click', function(e) {
                            e.preventDefault();
                            const tabName = this.getAttribute('data-tab');
                            if (tabName) {
                                showTab(tabName);
                            }
                        });
                    });
                }
                
                // Показать дашборд по умолчанию
                showTab('dashboard');
                
            } catch (e) {
                console.error('Error initializing tabs:', e);
            }
        }
        
        // Несколько способов инициализации для надежности
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializeTabs);
        } else {
            initializeTabs();
        }
        
        // Также инициализировать когда окно загрузится полностью
        window.addEventListener('load', initializeTabs);
    </script>

</body>
</html>
