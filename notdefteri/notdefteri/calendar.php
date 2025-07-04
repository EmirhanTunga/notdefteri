<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Takvim - Not Defterim</title>
    <link rel="stylesheet" href="style.css">
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
    <style>
        #calendar { max-width: 900px; margin: 40px auto; background: #fffbe6; border-radius: 18px; box-shadow: 0 2px 8px #fda08522; padding: 18px; }
    </style>
</head>
<body>
    <a href="index.php" class="back-home-btn" style="position: absolute; top: 18px; left: 18px; z-index: 10; margin: 0;">🏠 Ana Sayfa</a>
    <div id='calendar'></div>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
          initialView: 'dayGridMonth',
          locale: 'tr',
          headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
          },
          events: 'calendar_events.php'
        });
        calendar.render();
      });
    </script>
</body>
</html> 