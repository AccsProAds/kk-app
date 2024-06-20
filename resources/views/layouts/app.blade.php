<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Processor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Include Flatpickr CSS -->
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
    <!-- ... other meta tags ... -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.14/index.global.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.14/index.global.min.js'></script>
   

    @livewireStyles
</head>
<body>
    <div class="container mt-5">
        <h1>File Processor</h1>
        {{ $slot }}
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
     


    <!-- Include Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function () {
            flatpickr("#start_date", {
                enableTime: false,
                dateFormat: "Y-m-d",
                minDate: "today",
                onChange: function(selectedDates, dateStr, instance) {
                    document.getElementById('end_date')._flatpickr.set('minDate', dateStr);
                }
            });
            flatpickr("#end_date", {
                enableTime: false,
                dateFormat: "Y-m-d",
                onChange: function(selectedDates, dateStr, instance) {
                    document.getElementById('start_date')._flatpickr.set('maxDate', dateStr);
                }
            });

            flatpickr("#lead_time_start", {
                enableTime: false,
                dateFormat: "Y-m-d",
                onChange: function(selectedDates, dateStr, instance) {
                    document.getElementById('lead_time_end')._flatpickr.set('minDate', dateStr);
                }
            });
            flatpickr("#lead_time_end", {
                enableTime: false,
                dateFormat: "Y-m-d",
                onChange: function(selectedDates, dateStr, instance) {
                    document.getElementById('lead_time_start')._flatpickr.set('maxDate', dateStr);
                }
            });

        });
    </script>


    @flashifyScripts


    @stack('scripts')
    @livewireScripts
</body>
</html>
