<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>
<div class="container">
    <h1>Velocity Chart Test</h1>
    <p>This is a test version</p>
    <button id="testBtn">Click Me</button>
</div>
<?php \App\Core\View::endSection(); ?>

<?php \App\Core\View::section('scripts'); ?>
<script>
console.log('TEST SCRIPT LOADED');
document.getElementById('testBtn').addEventListener('click', function() {
    console.log('Button clicked');
    alert('Button works!');
});
</script>
<?php \App\Core\View::endSection(); ?>
