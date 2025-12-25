<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmailTemplate;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'newsletter',
                'type' => 'newsletter',
                'subject' => '{{newsletter_title}} - Rhymes Platform Newsletter',
                'body' => '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
                    <h1 style="color: #333;">{{newsletter_title}}</h1>
                    <div style="margin: 20px 0;">
                        {{newsletter_content}}
                    </div>
                    <p style="color: #666; font-size: 14px;">
                        Best regards,<br>
                        The Rhymes Platform Team
                    </p>
                </div>',
                'variables' => ['newsletter_title', 'newsletter_content'],
                'description' => 'Template for sending newsletters to authors',
                'is_active' => true,
            ],
            [
                'name' => 'announcement',
                'type' => 'announcement',
                'subject' => 'Important Announcement: {{announcement_title}}',
                'body' => '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
                    <div style="background: #f8f9fa; padding: 20px; border-left: 4px solid #007bff;">
                        <h2 style="color: #007bff; margin-top: 0;">{{announcement_title}}</h2>
                        <div style="color: #333;">
                            {{announcement_content}}
                        </div>
                    </div>
                    <p style="color: #666; font-size: 14px; margin-top: 20px;">
                        This is an important announcement from the Rhymes Platform team.
                    </p>
                </div>',
                'variables' => ['announcement_title', 'announcement_content'],
                'description' => 'Template for sending announcements to authors',
                'is_active' => true,
            ],
            [
                'name' => 'sales_report',
                'type' => 'sales_report',
                'subject' => 'Your Sales Performance Report - {{period}}',
                'body' => '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
                    <h1 style="color: #333;">Sales Performance Report</h1>
                    <p>Dear {{author_name}},</p>
                    <p>Here is your sales performance report for <strong>{{period}}</strong>:</p>
                    
                    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
                        <h3 style="margin-top: 0; color: #28a745;">Summary</h3>
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="padding: 10px 0; border-bottom: 1px solid #dee2e6;"><strong>Total Books:</strong></td>
                                <td style="padding: 10px 0; border-bottom: 1px solid #dee2e6; text-align: right;">{{total_books}}</td>
                            </tr>
                            <tr>
                                <td style="padding: 10px 0; border-bottom: 1px solid #dee2e6;"><strong>Total Sales:</strong></td>
                                <td style="padding: 10px 0; border-bottom: 1px solid #dee2e6; text-align: right;">{{total_sales}}</td>
                            </tr>
                            <tr>
                                <td style="padding: 10px 0; border-bottom: 1px solid #dee2e6;"><strong>Total Revenue:</strong></td>
                                <td style="padding: 10px 0; border-bottom: 1px solid #dee2e6; text-align: right; color: #28a745; font-size: 18px;"><strong>₦{{total_revenue}}</strong></td>
                            </tr>
                            <tr>
                                <td style="padding: 10px 0;"><strong>Wallet Balance:</strong></td>
                                <td style="padding: 10px 0; text-align: right; color: #007bff; font-size: 16px;"><strong>₦{{wallet_balance}}</strong></td>
                            </tr>
                        </table>
                    </div>

                    <div style="margin: 20px 0;">
                        <h3 style="color: #333;">Book Performance</h3>
                        {{book_details}}
                    </div>

                    <p style="color: #666; font-size: 14px; margin-top: 30px;">
                        Keep up the great work!<br>
                        The Rhymes Platform Team
                    </p>
                </div>',
                'variables' => ['author_name', 'period', 'total_books', 'total_sales', 'total_revenue', 'wallet_balance', 'book_details'],
                'description' => 'Template for sending sales performance reports to authors',
                'is_active' => true,
            ],
            [
                'name' => 'custom_message',
                'type' => 'custom',
                'subject' => '{{subject}}',
                'body' => '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
                    <div style="padding: 20px;">
                        {{message_content}}
                    </div>
                    <p style="color: #666; font-size: 14px; margin-top: 20px;">
                        Best regards,<br>
                        The Rhymes Platform Team
                    </p>
                </div>',
                'variables' => ['subject', 'message_content'],
                'description' => 'Template for custom messages',
                'is_active' => true,
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::updateOrCreate(
                ['name' => $template['name']],
                $template
            );
        }
    }
}
