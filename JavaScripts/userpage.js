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

// --- FullCalendar and booking logic ---
document.addEventListener('DOMContentLoaded', function() {
    // --- FullCalendar event rendering ---
    var calendarEl = document.getElementById('calendar');
    var eventNameInput = document.getElementById('event_name');
    var ingressInput = document.getElementById('date_time_ingress');
    var egressInput = document.getElementById('date_time_egress');
    var eventForm = document.getElementById('eventForm');

    // bookedEvents is injected by PHP
    if (!calendarEl || typeof bookedEvents === 'undefined') return;

    // Prepare events for calendar
    var calendarEvents = bookedEvents.map(function(ev) {
        return {
            title: ev.name,
            start: ev.start,
            end: ev.end
        };
    });

    // Extract booked dates (yyyy-mm-dd) from bookedEvents ingress and egress ranges
    var bookedDates = [];
    function formatDate(date) {
        var d = new Date(date);
        var month = '' + (d.getMonth() + 1);
        var day = '' + d.getDate();
        var year = d.getFullYear();
        if (month.length < 2) month = '0' + month;
        if (day.length < 2) day = '0' + day;
        return [year, month, day].join('-');
    }
    bookedEvents.forEach(function(ev) {
        var startDate = new Date(ev.start);
        var endDate = new Date(ev.end);
        for (var d = new Date(startDate); d <= endDate; d.setDate(d.getDate() + 1)) {
            var dateStr = formatDate(d);
            if (!bookedDates.includes(dateStr)) {
                bookedDates.push(dateStr);
            }
        }
    });

    // Calendar initialization
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        height: 500,
        events: calendarEvents,
        eventContent: function(arg) {
            // Only show event name, no time
            return { html: '<div style="background:#e53950;color:#fff;padding:2px 8px;border-radius:4px;font-size:13px;">' + arg.event.title + '</div>' };
        }
    });
    calendar.render();

    // Helper: parse datetime string to Date object
    function parseDateTime(dtString) {
        return new Date(dtString);
    }

    // Helper: check if a datetime range overlaps with any booked event
    function isDateTimeRangeBooked(start, end) {
        for (var i = 0; i < bookedEvents.length; i++) {
            var bookedStart = parseDateTime(bookedEvents[i].start);
            var bookedEnd = parseDateTime(bookedEvents[i].end);
            // Check if ranges overlap
            if (start < bookedEnd && end > bookedStart) {
                return true;
            }
        }
        return false;
    }

    function validateDateTimeInputs() {
        if (!ingressInput || !egressInput) return true;
        var start = new Date(ingressInput.value);
        var end = new Date(egressInput.value);
        if (ingressInput.value && egressInput.value) {
            if (start >= end) {
                alert("Ingress datetime must be before egress datetime.");
                return false;
            }
            if (isDateTimeRangeBooked(start, end)) {
                alert("This datetime range overlaps with an already booked event. Please choose a different time.");
                return false;
            }
        }
        return true;
    }

    // Add event listeners to validate on input/change
    if (ingressInput && egressInput) {
        ingressInput.addEventListener('change', validateDateTimeInputs);
        egressInput.addEventListener('change', validateDateTimeInputs);
    }

    // On form submit, prevent booking if overlap
    if (eventForm) {
        eventForm.addEventListener('submit', function(e) {
            var eventDateInput = document.getElementById('event_date');
            if (eventDateInput) {
                var selectedDate = eventDateInput.value;
                if (bookedDates.includes(selectedDate)) {
                    alert("This date is already booked for another event. Please choose a different date.");
                    e.preventDefault();
                    return false;
                }
            }
            // Also validate ingress/egress datetime overlaps
            if (!validateDateTimeInputs()) {
                e.preventDefault();
                return false;
            }
        });
    }

    // Optionally, update calendar preview when inputs change
    function updateCalendarPreview() {
        var name = eventNameInput ? eventNameInput.value : 'Event';
        var start = ingressInput ? ingressInput.value : null;
        var end = egressInput ? egressInput.value : null;

        calendar.removeAllEvents();
        calendarEvents.forEach(function(ev) { calendar.addEvent(ev); });

        if (start && end && !isDateTimeRangeBooked(new Date(start), new Date(end))) {
            calendar.addEvent({
                title: name,
                start: start,
                end: end,
                allDay: false,
                backgroundColor: '#007bff',
                borderColor: '#007bff',
                textColor: '#fff'
            });
        }
    }

    if (ingressInput) ingressInput.addEventListener('input', updateCalendarPreview);
    if (egressInput) egressInput.addEventListener('input', updateCalendarPreview);
    if (eventNameInput) eventNameInput.addEventListener('input', updateCalendarPreview);
});
    

    if (ingressInput) ingressInput.addEventListener('input', updateCalendarPreview);
    if (egressInput) egressInput.addEventListener('input', updateCalendarPreview);
    if (eventNameInput) eventNameInput.addEventListener('input', updateCalendarPreview);

