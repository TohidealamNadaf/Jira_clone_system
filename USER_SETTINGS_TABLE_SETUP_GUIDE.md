# User Settings Table Setup Guide

## Problem
When you tried to submit time tracking rates (annual package), you got this error:
```
Database table not initialized. Please create the settings table first.
```

## Solution
The `user_settings` table needs to be created in your database. Follow these steps:

## Step 1: Create the Table
Visit this link in your browser (replace with your actual URL):
```
http://localhost:8080/jira_clone_system/public/setup-settings-table.php
```

Or if you're accessing from IP:
```
http://192.168.x.x:8080/jira_clone_system/public/setup-settings-table.php
```

## Step 2: What Gets Created
The setup script will create a `user_settings` table with these features:

### Display & Language
- `language` - Preferred language (en, es, fr, de)
- `timezone` - User's timezone (UTC, etc.)
- `date_format` - Date display format (MM/DD/YYYY, etc.)

### Layout & Display  
- `items_per_page` - Pagination settings (10, 25, 50, 100)
- `compact_view` - Use compact layout
- `auto_refresh` - Auto-refresh notifications

### Privacy Settings
- `show_profile` - Show profile to others
- `show_activity` - Show activity feed
- `show_email` - Show email address

### Accessibility
- `high_contrast` - High contrast mode
- `reduce_motion` - Reduce animations
- `large_text` - Larger fonts

### Time Tracking Rates (NEW)
- `annual_package` - Your annual salary/package
- `rate_currency` - Currency (USD, EUR, GBP, INR, AUD, CAD, SGD, JPY)
- `hourly_rate` - Auto-calculated hourly rate
- `minute_rate` - Auto-calculated per-minute rate
- `second_rate` - Auto-calculated per-second rate  
- `daily_rate` - Auto-calculated daily rate

## Step 3: Calculate Your Rates
The system automatically calculates rates based on annual package:

```
Working hours per year = 2,210 hours
  (260 working days × 8.5 hours/day)
  (Excludes weekends and typical holidays)

Hourly Rate = Annual Package / 2,210
Minute Rate = Hourly Rate / 60
Second Rate = Minute Rate / 60
Daily Rate = Hourly Rate × 8.5
```

**Example:**
- Annual Package: ₹1,000,000
- Hourly Rate = 1,000,000 / 2,210 = **₹452.49/hour**
- Daily Rate = 452.49 × 8.5 = **₹3,846.17/day**
- Minute Rate = 452.49 / 60 = **₹7.54/minute**

## Step 4: Update Your Settings
1. Log in to your account
2. Go to **Profile → Settings**
3. Scroll to "Time Tracking Rates"
4. Enter your annual package (e.g., 1000000 for 10 lakh)
5. Select your currency (e.g., INR, USD)
6. Click **Save Settings**

## Step 5: Verify Success
- Table created ✓ (via setup script)
- Settings saved ✓ (via Profile Settings page)
- Time tracking now tracks cost automatically ✓

## If Setup Fails

**Error: "Database table not initialized"**
- Clear browser cache: `CTRL + SHIFT + DEL`
- Hard refresh: `CTRL + F5`
- Try setup script again

**Error: "Connection refused"**
- Make sure MySQL is running in XAMPP
- Check `config/config.php` database settings

**Error: "Permission denied"**
- Check database user permissions
- Contact your database administrator

## Files Created/Modified

### New Files
- `database/migrations/002_create_user_settings_table.sql` - Migration SQL
- `public/setup-settings-table.php` - Setup script
- `USER_SETTINGS_TABLE_SETUP_GUIDE.md` - This guide

### Modified Files
- `src/Controllers/UserController.php` - Updated to handle time tracking rates
- `profile/settings` view - Added time tracking fields

## Currency Codes Supported
- **USD** - US Dollar
- **EUR** - Euro
- **GBP** - British Pound
- **INR** - Indian Rupee
- **AUD** - Australian Dollar
- **CAD** - Canadian Dollar
- **SGD** - Singapore Dollar
- **JPY** - Japanese Yen

Add more in `.env` or `config/config.php` as needed.

## After Setup

Once the table is created, you can:
1. ✓ Set your annual package in Profile Settings
2. ✓ Log time with automatic cost calculation
3. ✓ Track project costs and team billable hours
4. ✓ Generate time tracking reports with cost analysis
5. ✓ Export time tracking data with billing details

## Support

If you need help:
1. Check setup script output for detailed error messages
2. Verify database is running
3. Check `config/config.php` database settings
4. Contact your system administrator

## Status
✅ Setup script ready to use  
✅ Time tracking rates feature ready  
✅ Auto-calculation of hourly/daily/minute rates ready  
✅ Multi-currency support ready  

**Next Step**: Visit `http://localhost:8080/jira_clone_system/public/setup-settings-table.php` and click "Create Settings Table"
