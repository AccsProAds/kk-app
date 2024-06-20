<div>
<div id='calendar'></div>
<script>
        document.addEventListener('livewire:init', function () {
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                themeSystem: 'bootstrap5',
                initialView: 'dayGridMonth',
                //initialView: 'multiMonthYear',
                events: []
            });

            calendar.render();

            const serviceColors = {
                fitnessxr: 'blue',
                usadc: 'orange',
                uprev: 'purple'
            };

            console.log("livewire inited");

            Livewire.on('leadsCalculated', event => {

               
                console.log("leads calculated");
                console.log(event);

                let events = [];
                for (const [date, item] of Object.entries(event[0].leads)) {
                    events.push({
                        title: item.no_declined + ' No-Declined',
                        start: date,
                        color: 'green'
                    } , {
                        title: item.declined + ' Declined',
                        start: date,
                        color: 'red'
                    },  {
                        title: item.total + ' Total',
                        start: date,
                        color: 'grey'
                    });
                    /*events.push({
                        title: item.declined + ' Declined',
                        start: date,
                        color: 'grey'
                    });
                    events.push({
                        title: item.total + ' Leads',
                        start: date,
                        color: 'grey'
                    });*/
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
