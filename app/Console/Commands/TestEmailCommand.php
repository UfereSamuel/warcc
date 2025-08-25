<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestEmail;

class TestEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test 
                            {email : The email address to send test email to}
                            {--message= : Optional custom message to include}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test email to verify email configuration is working';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $customMessage = $this->option('message');

        $this->info('🔧 Checking email configuration...');
        
        // Check current configuration
        $config = [
            'mailer' => config('mail.default'),
            'host' => config('mail.mailers.smtp.host'),
            'port' => config('mail.mailers.smtp.port'),
            'username' => config('mail.mailers.smtp.username'),
            'from_address' => config('mail.from.address'),
        ];

        $this->table(
            ['Setting', 'Value'],
            [
                ['Mail Driver', $config['mailer']],
                ['SMTP Host', $config['host'] ?: 'Not configured'],
                ['SMTP Port', $config['port'] ?: 'Not configured'],
                ['Username', $config['username'] ?: 'Not configured'],
                ['From Address', $config['from_address'] ?: 'Not configured'],
            ]
        );

        if ($config['mailer'] === 'log') {
            $this->warn('⚠️  Email is configured to use LOG driver - emails will be written to log files instead of being sent.');
        }

        $this->info('📧 Preparing test email...');

        try {
            $testData = [
                'system_name' => 'WARCC Staff Management System',
                'test_time' => now()->format('Y-m-d H:i:s T'),
                'server_info' => php_uname('n'),
                'test_message' => $customMessage,
                'tested_by' => 'Console Command',
            ];

            $this->info("📤 Sending test email to: {$email}");
            
            Mail::to($email)->send(new TestEmail($testData));

            if ($config['mailer'] === 'log') {
                $this->info('✅ Test email logged successfully!');
                $this->info('📂 Check your log files in storage/logs/ for the email content.');
            } else {
                $this->info('✅ Test email sent successfully!');
                $this->info("📬 Please check the inbox for {$email}");
            }

            $this->newLine();
            $this->info('📊 Test Summary:');
            $this->line("   • Email: {$email}");
            $this->line("   • Driver: " . strtoupper($config['mailer']));
            $this->line("   • Time: " . now()->format('Y-m-d H:i:s'));
            if ($customMessage) {
                $this->line("   • Message: {$customMessage}");
            }

        } catch (\Exception $e) {
            $this->error('❌ Failed to send test email!');
            $this->error("Error: {$e->getMessage()}");
            
            $this->newLine();
            $this->warn('🔧 Troubleshooting tips:');
            $this->line('   • Verify SMTP credentials are correct');
            $this->line('   • Check if firewall allows SMTP connections');
            $this->line('   • Ensure email provider allows SMTP access');
            $this->line('   • Try using app-specific passwords');
            
            return 1;
        }

        return 0;
    }
}