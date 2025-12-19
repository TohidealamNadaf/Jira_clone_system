<?php
/**
 * Calendar View - Simple Test Version
 */

declare(strict_types=1);

\App\Core\View::extends('layouts.app');
?>

<div class="container-fluid px-4 py-4">
    <h1 class="mb-4">Calendar View</h1>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="alert alert-info">
                <strong>ℹ️ Calendar Loading Test</strong><br>
                This is a simplified calendar view for testing.
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div id="calendar" style="height: 600px; width: 100%; border: 1px solid red; background-color: #f9f9f9; position: relative;">
                <p style="padding: 20px; color: #999;">Calendar will load here...</p>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-body">
            <h5>Debug Info:</h5>
            <pre id="debug-info" style="background: #f5f5f5; padding: 10px; border-radius: 4px; font-size: 12px;">
                Loading debug info...
            </pre>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>

<script>
    const debugLog = [];
    
    function addDebug(msg) {
        const timestamp = new Date().toLocaleTimeString();
        const logMsg = `[${timestamp}] ${msg}`;
        debugLog.push(logMsg);
        console.log(logMsg);
        document.getElementById('debug-info').textContent = debugLog.join('\n');
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        addDebug('🗓️ DOMContentLoaded fired');
        
        const calendarEl = document.getElementById('calendar');
        addDebug('✓ Calendar element found: ' + (calendarEl ? 'YES' : 'NO'));
        
        if (!calendarEl) return;
        
        if (typeof FullCalendar === 'undefined') {
            addDebug('❌ FullCalendar not loaded!');
            return;
        }
        
        addDebug('✓ FullCalendar library loaded');
        
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: '',
                center: 'title',
                right: ''
            },
            events: function(info, successCallback, failureCallback) {
                addDebug('📡 Requesting events: ' + info.start.toISOString().split('T')[0] + ' to ' + info.end.toISOString().split('T')[0]);
                
                const params = new URLSearchParams({
                    start: info.start.toISOString().split('T')[0],
                    end: info.end.toISOString().split('T')[0]
                });
                
                fetch('/jira_clone_system/public/api/v1/calendar/events?' + params.toString(), {
                    credentials: 'include',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => {
                    addDebug('📦 API response: ' + response.status);
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        addDebug('✓ Got ' + data.data.length + ' events');
                        successCallback(data.data);
                    } else {
                        addDebug('❌ API error: ' + data.error);
                        failureCallback(new Error(data.error));
                    }
                })
                .catch(error => {
                    addDebug('❌ Fetch error: ' + error.message);
                    failureCallback(error);
                });
            },
            eventClick: function(info) {
                addDebug('📌 Event clicked: ' + info.event.title);
            },
            height: '100%'
        });
        
        addDebug('✓ Calendar instance created');
        calendar.render();
        addDebug('✓ Calendar rendered');
    });
</script>
