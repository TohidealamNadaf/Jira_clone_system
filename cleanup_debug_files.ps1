# Production Cleanup Script
# Removes all debug, test, and temporary files from the project root

$debugFiles = @(
    # Database debug scripts
    "check_all_tables.php", "CHECK_BARAMATI_DATA.php", "check_baramati_issues.php",
    "check_baramati.php", "check_comments_table.php", "check_dates.php",
    "check_foreign_keys.php", "check_json_escaping.php", "check_notification_db.php",
    "check_notifications_schema.php", "check_projects.php", "check_schema.php",
    "check_workflow_setup.php", "show_columns.php", "quick_status_check.php",
    
    # API debug scripts
    "debug_api_error.php", "debug_auth.php", "debug_comment.php",
    "debug_cumulative_flow.php", "debug_cumulative_flow2.php", "debug_cumulative_flow3.php",
    "debug_dropdown.php", "debug_flow_logic.php", "DEBUG_ISSUE_NOT_FOUND.php",
    "debug_issue_types.php", "debug_notification_api.php", "debug_notification_prefs_insert.php",
    "debug_notification_update.php", "debug_project_creation.php", "debug_search.php", "debug.php",
    
    # Test scripts
    "test_api_response.php", "test_api_update_prefs.php", "test_bp7_issue.php",
    "test_button_visibility.php", "test_comment_endpoints.php", "test_comment_flow.php",
    "test_comments_table.php", "test_cumulative_flow_render.php", "test_data_structure.php",
    "test_debug.php", "test_fresh.php", "test_notification_fix.php",
    "test_notification_preferences.php", "test_notification_prefs.php", "test_notifications_api.php",
    "test_notifications_fix.php", "test_notifications_page.php", "test_notifications_setup.php",
    "test_parameter_binding.php", "test_preference_persistence.php", "test_prepared.php",
    "test_quick_create_endpoint.php", "test_schema_fix.php", "test_search_filter.php",
    "test_select2_projects.php", "test_velocity_chart.php", "test_velocity_controller.php",
    "test_velocity_data.php", "test_velocity_direct.php", "test_velocity_endpoint.php",
    "test_velocity_raw.php", "test_velocity_simple.php", "test_velocity_view.php", "test_view_render.php",
    "test_dropdown_scroll.html", "test_modal_responsive.html", "test-create-modal.php",
    
    # Utility test scripts
    "verify_board_2.php", "verify_fixes.php", "verify_notification_fix.php",
    "verify_notification_prefs_fixed.php", "verify_notifications.php",
    
    # Capture/Report scripts
    "capture_report_page.php", "capture_report_page2.php",
    
    # Setup/Seed scripts (review these)
    "setup_notifications.php", "seed_settings.php", "create_baramati.php",
    "create_notification_tables.php", "create_notifications_tables.php",
    "apply_cascade_fix.php", "assign_admin_issues.php", "assign_test_issues.php",
    "fix_missing_roles.php", "fix_notifications_schema.php", "fix_notifications_tables.php",
    "initialize_notification_preferences.php", "install_notifications.php",
    "run_fix_notifications.php", "simulate_full_test.php", "simple-test.php",
    "PRODUCTION_AUDIT_NOTIFICATION_SYSTEM.php", "COMPREHENSIVE_FIX.php", "EXECUTE_FIX_NOW.php",
    "FIX_MISSING_ISSUE_KEYS.php", "FIX_PROJECT_ISSUE_COUNT.php", "DIAGNOSE_ISSUE_COUNT.php",
    "diagnose_project_issue.php", "diagnose_velocity.php"
)

$deletedCount = 0
$skippedCount = 0

Write-Host "Starting production cleanup..." -ForegroundColor Green
Write-Host "======================================" -ForegroundColor Green
Write-Host ""

foreach ($file in $debugFiles) {
    $fullPath = Join-Path (Get-Location) $file
    
    if (Test-Path $fullPath) {
        try {
            Remove-Item $fullPath -Force
            Write-Host "✓ Deleted: $file" -ForegroundColor Green
            $deletedCount++
        } catch {
            Write-Host "✗ Failed to delete: $file - $($_.Exception.Message)" -ForegroundColor Red
            $skippedCount++
        }
    } else {
        Write-Host "  Skipped: $file (not found)" -ForegroundColor Gray
    }
}

Write-Host ""
Write-Host "======================================" -ForegroundColor Green
Write-Host "Cleanup Summary" -ForegroundColor Green
Write-Host "======================================" -ForegroundColor Green
Write-Host "Deleted: $deletedCount files" -ForegroundColor Green
Write-Host "Failed: $skippedCount files" -ForegroundColor Yellow
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Yellow
Write-Host "1. Check for debug code in source files"
Write-Host "2. Update config/config.php for production"
Write-Host "3. Run tests to verify system integrity"
Write-Host "4. Take full database backup"
Write-Host "5. Deploy to production"
