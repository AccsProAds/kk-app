<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Processor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Include Flatpickr CSS -->
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">


    @livewireStyles
</head>
<body>
    <div class="container mt-5">
        <h1>File Processor</h1>
        @livewire('log-file-processor')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
     


    <!-- Include Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function () {
            flatpickr("#start_date", {
                enableTime: false,
                dateFormat: "Y-m-d",
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
        });
    </script>

    @livewireScripts
    @flashifyScripts
</body>
</html>
