<?php
include 'db.php';
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php");
    exit;
}

// Fetch user events
$stmt = $conn->prepare("SELECT * FROM citas WHERE usuario_id = :user_id");
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$citas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Schedule</title>
    <link rel="stylesheet" href="styles.css">
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/locales-all.min.js'></script>
</head>
<body>

<!-- Header with User Menu -->
<div class="header">
    <div class="user-menu">
        <img src="login.png" alt="User Icon" class="user-icon">
        <div class="user-dropdown">
            <a href="logout.php">Log out</a>
        </div>
    </div>
</div>

<!-- Main Container -->
<div class="main-container">
    <!-- Calendar Container -->
    <div class="calendar-container">
        <h1>Welcome to your schedule</h1>
        <div id="calendar"></div>
    </div>

    <!-- Sidebar for Adding Events -->
    <div class="sidebar">
        <h2>Add Event</h2>
        <form id="addEventForm">
            <input type="text" id="eventTitle" placeholder="Event Title" required>
            <input type="date" id="eventDate" required>
            <input type="time" id="eventTime" required>
            <button type="submit">Add Event</button>
        </form>
    </div>
</div>

<!-- Add Event Modal -->
<div id="addEventModal" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Add Event</h2>
        <form id="addEventFormModal">
            <input type="text" id="eventTitleModal" placeholder="Event Title" required>
            <input type="date" id="eventDateModal" required>
            <input type="time" id="eventTimeModal" required>
            <button type="submit">Add Event</button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        events: [
            <?php foreach($citas as $cita): ?>,
            {
                title: '<?php echo addslashes($cita['titulo']); ?>',
                start: '<?php echo $cita['fecha']; ?>T<?php echo $cita['hora']; ?>'
            },
            <?php endforeach; ?>
        ],
        dateClick: function(info) {
            document.getElementById('addEventModal').style.display = 'block';
            document.getElementById('eventDateModal').value = info.dateStr;
        }
    });
    calendar.render();

    document.getElementById('addEventForm').addEventListener('submit', function(e) {
        e.preventDefault();

        var title = document.getElementById('eventTitle').value;
        var date = document.getElementById('eventDate').value;
        var time = document.getElementById('eventTime').value;

        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'add_event.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200) {
                calendar.addEvent({
                    title: title,
                    start: date + 'T' + time
                });

                document.getElementById('addEventModal').style.display = 'none';
                document.getElementById('addEventForm').reset();
            }
        };
        xhr.send('title=' + encodeURIComponent(title) + '&date=' + encodeURIComponent(date) + '&time=' + encodeURIComponent(time));
    });

    document.getElementById('addEventFormModal').addEventListener('submit', function(e) {
        e.preventDefault();

        var title = document.getElementById('eventTitleModal').value;
        var date = document.getElementById('eventDateModal').value;
        var time = document.getElementById('eventTimeModal').value;

        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'add_event.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200) {
                calendar.addEvent({
                    title: title,
                    start: date + 'T' + time
                });

                document.getElementById('addEventModal').style.display = 'none';
                document.getElementById('addEventFormModal').reset();
            }
        };
        xhr.send('title=' + encodeURIComponent(title) + '&date=' + encodeURIComponent(date) + '&time=' + encodeURIComponent(time));
    });

    var span = document.getElementsByClassName('close')[0];
    span.onclick = function() {
        document.getElementById('addEventModal').style.display = 'none';
    }

    window.onclick = function(event) {
        if (event.target == document.getElementById('addEventModal')) {
            document.getElementById('addEventModal').style.display = 'none';
        }
    }
});
</script>
</body>
</html>