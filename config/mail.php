<?php

declare(strict_types=1);

/**
 * Email Configuration
 * Production-ready email service configuration
 * 
 * Supports:
 * - SMTP (SendGrid, Mailgun, AWS SES, Office365, custom)
 * - PHP mail() function fallback
 * - Environment variable configuration
 * 
 * Environment Variables:
 * - MAIL_DRIVER: 'smtp' or 'mail' (default: 'smtp')
 * - MAIL_HOST: SMTP host
 * - MAIL_PORT: SMTP port (default: 587 for TLS, 25 for plain)
 * - MAIL_ENCRYPTION: 'tls' or 'ssl' (default: 'tls')
 * - MAIL_USERNAME: SMTP username (optional)
 * - MAIL_PASSWORD: SMTP password (optional)
 * - MAIL_FROM_ADDRESS: From email address
 * - MAIL_FROM_NAME: From name (default: 'Jira Clone')
 */

return [
    'mail' => [
        // Mail driver: 'smtp' or 'mail'
        // Use 'smtp' for professional email services
        // Use 'mail' for PHP mail() fallback
        'driver' => getenv('MAIL_DRIVER') ?: 'smtp',

        // SMTP Host
        // Examples:
        // - SendGrid: smtp.sendgrid.net
        // - Mailgun: smtp.mailgun.org
        // - AWS SES: email-smtp.region.amazonaws.com
        // - Office365: smtp.office365.com
        // - Gmail: smtp.gmail.com
        // - Local: localhost
        'host' => getenv('MAIL_HOST') ?: 'smtp.mailtrap.io',

        // SMTP Port
        // 587: TLS (recommended)
        // 465: SSL
        // 25: Plain (for local SMTP)
        'port' => (int) (getenv('MAIL_PORT') ?: 587),

        // SMTP Encryption
        // 'tls' or 'ssl'
        // TLS is recommended for port 587
        // SSL is recommended for port 465
        'encryption' => getenv('MAIL_ENCRYPTION') ?: 'tls',

        // SMTP Username (optional)
        // Required for most email services
        // For SendGrid: 'apikey'
        // For Office365/Mailgun: Your username/email
        'username' => getenv('MAIL_USERNAME') ?: null,

        // SMTP Password
        // For SendGrid: Your API key
        // For others: Your password
        // IMPORTANT: Use environment variables in production!
        'password' => getenv('MAIL_PASSWORD') ?: null,

        // From Address
        // Email address that appears as sender
        // Should be a real email address you control
        'from_address' => getenv('MAIL_FROM_ADDRESS') ?: 'noreply@yourdomain.com',

        // From Name
        // Display name for sender
        'from_name' => getenv('MAIL_FROM_NAME') ?: 'Jira Clone',
    ],
];

/**
 * SETUP INSTRUCTIONS FOR COMMON PROVIDERS
 * 
 * ### SendGrid (Recommended - Free tier available)
 * 1. Sign up at sendgrid.com
 * 2. Create API key
 * 3. Set environment variables:
 *    MAIL_HOST=smtp.sendgrid.net
 *    MAIL_PORT=587
 *    MAIL_ENCRYPTION=tls
 *    MAIL_USERNAME=apikey
 *    MAIL_PASSWORD=SG.xxxxxx_your_api_key_xxxxxx
 *    MAIL_FROM_ADDRESS=noreply@yourcompany.com
 * 
 * ### Mailgun
 * 1. Sign up at mailgun.com
 * 2. Get SMTP credentials from dashboard
 * 3. Set environment variables:
 *    MAIL_HOST=smtp.mailgun.org
 *    MAIL_PORT=587
 *    MAIL_ENCRYPTION=tls
 *    MAIL_USERNAME=postmaster@yourcompany.mailgun.org
 *    MAIL_PASSWORD=your_password
 *    MAIL_FROM_ADDRESS=noreply@yourcompany.mailgun.org
 * 
 * ### AWS SES
 * 1. Create AWS account and set up SES
 * 2. Verify sender email address
 * 3. Get SMTP credentials from AWS console
 * 4. Set environment variables:
 *    MAIL_HOST=email-smtp.us-east-1.amazonaws.com
 *    MAIL_PORT=587
 *    MAIL_ENCRYPTION=tls
 *    MAIL_USERNAME=your_smtp_username
 *    MAIL_PASSWORD=your_smtp_password
 *    MAIL_FROM_ADDRESS=verified-email@yourcompany.com
 * 
 * ### Office365/Outlook
 * 1. Get SMTP credentials from your account settings
 * 2. Set environment variables:
 *    MAIL_HOST=smtp.office365.com
 *    MAIL_PORT=587
 *    MAIL_ENCRYPTION=tls
 *    MAIL_USERNAME=your-email@company.onmicrosoft.com
 *    MAIL_PASSWORD=your_password
 *    MAIL_FROM_ADDRESS=your-email@company.onmicrosoft.com
 * 
 * ### Local SMTP (For development/testing)
 * 1. Install Mailtrap (mailtrap.io) or local mail server
 * 2. Get credentials
 * 3. Set environment variables:
 *    MAIL_HOST=smtp.mailtrap.io
 *    MAIL_PORT=587
 *    MAIL_USERNAME=your_username
 *    MAIL_PASSWORD=your_password
 * 
 * ### PHP mail() Function (Fallback)
 * 1. Set driver to 'mail'
 * 2. Configure your server's mail settings (sendmail)
 * 3. No additional setup needed
 *    MAIL_DRIVER=mail
 * 
 * ---
 * 
 * PRODUCTION CHECKLIST:
 * ✓ Use strong, random passwords in environment variables
 * ✓ Never commit credentials to git
 * ✓ Use .env file (in .gitignore)
 * ✓ Test with sendTest() before deployment
 * ✓ Monitor email delivery logs
 * ✓ Set up bounce handling
 * ✓ Configure SPF, DKIM, DMARC records
 * ✓ Set sender policy framework records
 */
