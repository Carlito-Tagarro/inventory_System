// --- Hamburger Menu ---
document.addEventListener('DOMContentLoaded', () => {
    const hamburgerBtn = document.getElementById('hamburgerBtn');
    const navLinks = document.getElementById('navLinks');

    if (hamburgerBtn && navLinks) {
        hamburgerBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            navLinks.classList.toggle('show');
            const expanded = navLinks.classList.contains('show');
            hamburgerBtn.setAttribute('aria-expanded', expanded ? 'true' : 'false');
        });

        // Close nav when clicking outside (mobile)
        document.addEventListener('click', function(e) {
            if (
                navLinks.classList.contains('show') &&
                !navLinks.contains(e.target) &&
                e.target !== hamburgerBtn
            ) {
                navLinks.classList.remove('show');
                hamburgerBtn.setAttribute('aria-expanded', 'false');
            }
        });

        // Optional: Close menu when clicking a nav link (mobile)
        navLinks.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                if (navLinks.classList.contains('show')) {
                    navLinks.classList.remove('show');
                    hamburgerBtn.setAttribute('aria-expanded', 'false');
                }
            });
        });
    }
});


// Modal JS
document.addEventListener('DOMContentLoaded', function() {
    var select = document.getElementById('provide_materials');
    var modal = document.getElementById('materialsModal');
    var closeBtn = document.getElementById('closeModal');
    var categoryFilter = document.getElementById('categoryFilter');
    var nameSearch = document.getElementById('nameSearch');
    var materialsTable = document.getElementById('materialsTable');
    select.addEventListener('change', function() {
        if (select.value === 'Yes') {
            modal.style.display = 'block';
        } else {
            modal.style.display = 'none';
            // Clear selected materials if "No" is chosen
            var selectedPreview = document.getElementById('selectedMaterialsPreview');
            var selectedList = document.getElementById('selectedMaterialsList');
            selectedPreview.style.display = 'none';
            selectedList.innerHTML = '';
            // Also uncheck all checkboxes and clear quantities in modal
            var rows = document.querySelectorAll('#materialsTable tbody tr');
            rows.forEach(function(row) {
                var checkbox = row.querySelector('input[type="checkbox"]');
                var qtyInput = row.querySelector('input[type="number"]');
                if (checkbox) checkbox.checked = false;
                if (qtyInput) {
                    qtyInput.value = '';
                    qtyInput.disabled = true;
                }
            });
        }
    });
    closeBtn.onclick = function() {
        modal.style.display = 'none';
        select.value = '';
    };
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    };
    // Filter logic
    function filterTable() {
        var categoryValue = categoryFilter.value;
        var searchValue = nameSearch.value.toLowerCase();
        var rows = materialsTable.querySelectorAll('tbody tr');
        rows.forEach(function(row) {
            var matchesCategory = (categoryValue === 'All' || row.getAttribute('data-category') === categoryValue);
            var nameCell = row.querySelector('td:nth-child(3)');
            var matchesSearch = nameCell && nameCell.textContent.toLowerCase().includes(searchValue);
            if (matchesCategory && matchesSearch) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
    categoryFilter.addEventListener('change', filterTable);
    nameSearch.addEventListener('input', filterTable);

    // Enable quantity input only if checkbox is checked
    var table = document.getElementById('materialsTable');
    table.addEventListener('change', function(e) {
        if (e.target.type === 'checkbox') {
            var row = e.target.closest('tr');
            var qtyInput = row.querySelector('input[type="number"]');
            if (qtyInput) {
                qtyInput.disabled = !e.target.checked;
                if (!e.target.checked) qtyInput.value = '';
            }
        }
        // Quantity input validation
        if (e.target.type === 'number') {
            var row = e.target.closest('tr');
            var availableCell = row.querySelector('td:nth-child(4)');
            var available = parseInt(availableCell.textContent, 10);
            var val = parseInt(e.target.value, 10);
            if (val > available) {
                e.target.value = available;
            } else if (val < 1 && e.target.value !== '') {
                e.target.value = 1;
            }
        }
    });
    // On page load, disable all quantity inputs
    var qtyInputs = table.querySelectorAll('input[type="number"]');
    qtyInputs.forEach(function(input) {
        input.disabled = true;
        // Prevent manual input above available
        input.addEventListener('input', function(e) {
            var row = input.closest('tr');
            var availableCell = row.querySelector('td:nth-child(4)');
            var available = parseInt(availableCell.textContent, 10);
            var val = parseInt(input.value, 10);
            if (val > available) {
                input.value = available;
            } else if (val < 1 && input.value !== '') {
                input.value = 1;
            }
        });
    });
    // Show selected materials outside modal
    var materialsForm = document.getElementById('materialsForm');
    var selectedPreview = document.getElementById('selectedMaterialsPreview');
    var selectedList = document.getElementById('selectedMaterialsList');
    var submitMaterialsBtn = document.getElementById('submitMaterialsBtn');
    var selectedMaterialsInput = document.getElementById('selected_materials_input');
    materialsForm.addEventListener('submit', function(e) {
        e.preventDefault();
        var selected = [];
        var rows = materialsTable.querySelectorAll('tbody tr');
        rows.forEach(function(row) {
            var checkbox = row.querySelector('input[type="checkbox"]');
            var qtyInput = row.querySelector('input[type="number"]');
            if (checkbox && checkbox.checked && qtyInput && qtyInput.value) {
                var name = row.querySelector('td:nth-child(3)').textContent;
                var qty = qtyInput.value;
                var type = row.querySelector('td:nth-child(2)').textContent;
                selected.push({name: name, qty: qty, type: type});
            }
        });
        // Store selected materials as JSON in hidden input
        selectedMaterialsInput.value = JSON.stringify(selected);

        // Display selected materials as table
        var selectedPreview = document.getElementById('selectedMaterialsPreview');
        var selectedList = document.getElementById('selectedMaterialsList');
        if (selected.length > 0) {
            selectedPreview.style.display = 'block';
            var html = '';
            selected.forEach(function(item) {
                html += '<tr><td style="padding:8px;">' + item.name + '</td><td style="padding:8px;">' + item.qty + '</td></tr>';
            });
            selectedList.innerHTML = html;
        } else {
            selectedPreview.style.display = 'none';
            selectedList.innerHTML = '';
        }
        modal.style.display = 'none';
        select.value = 'Yes'; // keep selection
    });

    // --- NEW: On event form submit, ensure selected materials are included ---
    var eventForm = document.getElementById('eventForm');
    eventForm.addEventListener('submit', function(e) {
        // If provide_materials is Yes and no materials selected, prevent submit
        if (select.value === 'Yes' && !selectedMaterialsInput.value) {
            alert('Please select materials to provide.');
            e.preventDefault();
        }
    });
});

// --- Calendar and Booked Dates JS ---
// The following variables must be set in a <script> in userpage.php before including this JS:
// var bookedEvents = [...]; // array of {date, name}
// var bookedDates = [...]; // array of dates
document.addEventListener('DOMContentLoaded', function() {
    // --- Pass booked events from PHP to JS ---
    // var bookedEvents = ...; // set in PHP
    // var bookedDates = ...; // set in PHP

    var calendarEl = document.getElementById('calendar');
    var eventNameInput = document.getElementById('event_name');
    var eventDateInput = document.getElementById('event_date');
    var calendarEvents = bookedEvents.map(function(ev) {
        return {
            title: ev.name,
            start: ev.date,
            allDay: true,
            backgroundColor: '#dc3545', // red for booked
            borderColor: '#dc3545',
            textColor: '#fff'
        };
    });
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        height: 500,
        events: calendarEvents
    });
    calendar.render();

    // Disable booked dates in event_date input
    // Helper to format date to yyyy-mm-dd
    function formatDate(date) {
        var d = new Date(date);
        var month = '' + (d.getMonth() + 1);
        var day = '' + d.getDate();
        var year = d.getFullYear();
        if (month.length < 2) month = '0' + month;
        if (day.length < 2) day = '0' + day;
        return [year, month, day].join('-');
    }

    eventDateInput.addEventListener('focus', function() {
        eventDateInput.removeAttribute('min');
        eventDateInput.removeAttribute('max');
    });

    eventDateInput.addEventListener('input', function() {
        var selected = eventDateInput.value;
        if (bookedDates.includes(selected)) {
            alert('This date is already booked for another event.');
            eventDateInput.value = '';
        }
    });

    eventDateInput.addEventListener('keydown', function(e) {
        setTimeout(function() {
            var selected = eventDateInput.value;
            if (bookedDates.includes(selected)) {
                alert('This date is already booked for another event.');
                eventDateInput.value = '';
            }
        }, 10);
    });

    eventDateInput.addEventListener('change', function() {
        var date = eventDateInput.value;
        var name = eventNameInput.value || 'Event';
        calendar.removeAllEvents();
        calendarEvents.forEach(function(ev) { calendar.addEvent(ev); });
        if (date && !bookedDates.includes(date)) {
            calendar.addEvent({
                title: name,
                start: date,
                allDay: true,
                backgroundColor: '#007bff',
                borderColor: '#007bff',
                textColor: '#fff'
            });
        }
    });
    eventNameInput.addEventListener('input', function() {
        var date = eventDateInput.value;
        var name = eventNameInput.value || 'Event';
        calendar.removeAllEvents();
        calendarEvents.forEach(function(ev) { calendar.addEvent(ev); });
        if (date && !bookedDates.includes(date)) {
            calendar.addEvent({
                title: name,
                start: date,
                allDay: true,
                backgroundColor: '#007bff',
                borderColor: '#007bff',
                textColor: '#fff'
            });
        }
    });
});

// --- Show JS message after form submit (PHP sets JS variable) ---
// In userpage.php, set a <script> block for this logic after form submit.
