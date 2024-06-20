<div>
<div id='calendar'></div>
<script>
        document.addEventListener('livewire:init', function () {
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                themeSystem: 'bootstrap5',
                initialView: 'dayGridMonth',
                events: []
            });

            calendar.render();

            const serviceColors = {
                fitnessxr: 'blue',
                usadc: 'green',
                uprev: 'purple'
            };

            console.log("livewire inited");

            Livewire.on('leadsCalculated', event => {

               
                console.log("leads calculated");
                console.log(event);

                let events = [];
                for (const [date, count] of Object.entries(event[0].leads)) {
                    events.push({
                        title: count + ' Leads',
                        start: date,
                        color: 'grey'
                    });
                }
                for (const [date, services] of Object.entries(event[0].scheduledLeads)) {
                    for (const [service, count] of Object.entries(services)) {
                        events.push({
                            title: `${count} Leads (${service})`,
                            start: date,
                            color: serviceColors[service] || 'red' // Different color for each service
                        });
                    }
                }
                calendar.removeAllEvents(); // Remove previous events
                calendar.addEventSource(events); // Add new events
            });
        });
    </script>
</div>
