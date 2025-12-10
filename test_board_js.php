<?php
/**
 * Test the board JavaScript to verify drag-and-drop is working
 */
declare(strict_types=1);

require 'bootstrap/autoload.php';
$app = require 'bootstrap/app.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Board JavaScript Test</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css' rel='stylesheet'>
</head>
<body style='padding: 20px;'>
    <h1>Board JavaScript Diagnostic</h1>
    
    <div class='alert alert-info'>
        <h3>Test Results</h3>
        <div id='results'></div>
    </div>
    
    <div class='alert alert-warning'>
        <h3>Instructions</h3>
        <ol>
            <li>Open your browser's Developer Console (F12)</li>
            <li>Look for the test results below</li>
            <li>Check for any JavaScript errors in the Console tab</li>
            <li>Try dragging an issue card on the board</li>
            <li>Check the Network tab to see if the API call is made</li>
        </ol>
    </div>
    
    <script>
        const results = [];
        
        // Test 1: Check if board cards exist
        function testCardElements() {
            const cards = document.querySelectorAll('.board-card');
            results.push({
                test: 'Board cards exist',
                passed: cards.length > 0,
                details: `Found ${cards.length} cards`
            });
        }
        
        // Test 2: Check if board columns exist
        function testColumnElements() {
            const columns = document.querySelectorAll('.board-column');
            results.push({
                test: 'Board columns exist',
                passed: columns.length > 0,
                details: `Found ${columns.length} columns`
            });
        }
        
        // Test 3: Check if draggable attribute is set
        function testDraggableAttribute() {
            const cards = document.querySelectorAll('.board-card');
            let allDraggable = true;
            let draggableCount = 0;
            
            cards.forEach(card => {
                if (card.getAttribute('draggable') === 'true') {
                    draggableCount++;
                } else {
                    allDraggable = false;
                }
            });
            
            results.push({
                test: 'Cards have draggable=true',
                passed: allDraggable && draggableCount > 0,
                details: `${draggableCount} / ${cards.length} cards are draggable`
            });
        }
        
        // Test 4: Check if data attributes are set
        function testDataAttributes() {
            const cards = document.querySelectorAll('.board-card');
            let cardsWithId = 0;
            let cardsWithKey = 0;
            
            cards.forEach(card => {
                if (card.dataset.issueId) cardsWithId++;
                if (card.dataset.issueKey) cardsWithKey++;
            });
            
            results.push({
                test: 'Cards have data attributes',
                passed: cardsWithId > 0 && cardsWithKey > 0,
                details: `${cardsWithId} with ID, ${cardsWithKey} with KEY`
            });
        }
        
        // Test 5: Check if event listeners are attached
        function testEventListeners() {
            const cards = document.querySelectorAll('.board-card');
            let allHaveListeners = true;
            
            cards.forEach((card, idx) => {
                // Try to trigger dragstart to see if listener is attached
                const event = new DragEvent('dragstart', { bubbles: true });
                let listenerFired = false;
                
                const listener = (e) => { listenerFired = true; };
                card.addEventListener('dragstart', listener, { once: true });
                card.dispatchEvent(event);
                
                if (!listenerFired) {
                    allHaveListeners = false;
                }
            });
            
            results.push({
                test: 'Event listeners are attached',
                passed: allHaveListeners,
                details: allHaveListeners ? 'All cards have dragstart listener' : 'Some cards missing listeners'
            });
        }
        
        // Run tests when page loads
        window.addEventListener('load', () => {
            testCardElements();
            testColumnElements();
            testDraggableAttribute();
            testDataAttributes();
            testEventListeners();
            
            // Display results
            const resultsDiv = document.getElementById('results');
            let html = '<table class=\"table\"><tbody>';
            
            results.forEach(result => {
                const icon = result.passed ? '✓' : '✗';
                const badge = result.passed ? 'success' : 'danger';
                html += `<tr>
                    <td><span class=\"badge bg-${badge}\">${icon}</span></td>
                    <td><strong>${result.test}</strong></td>
                    <td>${result.details}</td>
                </tr>`;
            });
            
            html += '</tbody></table>';
            resultsDiv.innerHTML = html;
            
            // Log to console
            console.log('=== BOARD JAVASCRIPT TESTS ===');
            results.forEach(r => {
                console.log(`[${r.passed ? '✓' : '✗'}] ${r.test}: ${r.details}`);
            });
        });
    </script>
</body>
</html>";
?>
