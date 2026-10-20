<?php

use App\Models\ContactMessage;
use App\Models\User;
use App\Models\WebsiteSetting;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Permission;

function configureWhatsApp(array $overrides = []): void
{
    $settings = $overrides + [
        'whatsapp_notification_number' => '+91 98765-43210',
        'whatsapp_phone_number_id' => '1234567890',
        'whatsapp_access_token' => 'secret-token',
        'whatsapp_template_name' => 'contact_message_alert',
    ];

    foreach ($settings as $key => $value) {
        WebsiteSetting::setValue($key, $value, 'whatsapp');
    }
}

$contact = ['name' => 'Asha', 'email' => 'asha@example.com', 'phone' => '9000000001', 'message' => "Need bulk pricing\nfor 200 units"];

test('contact message is sent to the admin WhatsApp number as a template', function () use ($contact) {
    Http::fake(['graph.facebook.com/*' => Http::response(['messages' => [['id' => 'wamid.1']]])]);
    configureWhatsApp();

    $this->post(route('shop.contact.submit'), $contact)->assertSessionHas('success');

    expect(ContactMessage::count())->toBe(1);

    Http::assertSent(function (Request $request) {
        $body = $request->data();

        return $request->url() === 'https://graph.facebook.com/v21.0/1234567890/messages'
            && $request->hasHeader('Authorization', 'Bearer secret-token')
            && $body['to'] === '919876543210'
            && $body['template']['name'] === 'contact_message_alert'
            && array_column($body['template']['components'][0]['parameters'], 'text')
                === ['Asha', '9000000001', 'asha@example.com', 'Need bulk pricing for 200 units'];
    });
});

test('without a template name the message is sent as plain text', function () use ($contact) {
    Http::fake(['graph.facebook.com/*' => Http::response([])]);
    configureWhatsApp(['whatsapp_template_name' => '']);

    $this->post(route('shop.contact.submit'), $contact);

    Http::assertSent(fn (Request $request) => $request['type'] === 'text'
        && str_contains($request['text']['body'], "Need bulk pricing\nfor 200 units"));
});

test('nothing is sent to WhatsApp when the admin number is not set', function () use ($contact) {
    Http::fake();
    configureWhatsApp(['whatsapp_notification_number' => '']);

    $this->post(route('shop.contact.submit'), $contact)->assertSessionHas('success');

    Http::assertNothingSent();
});

test('a WhatsApp API failure still saves the message and thanks the customer', function () use ($contact) {
    Http::fake(['graph.facebook.com/*' => Http::response(['error' => ['message' => 'bad token']], 401)]);
    configureWhatsApp();

    $this->post(route('shop.contact.submit'), $contact)->assertSessionHas('success');

    expect(ContactMessage::count())->toBe(1);
});

test('saving settings keeps the stored access token when the field is left blank', function () {
    $admin = User::factory()->create();
    $admin->givePermissionTo(Permission::findOrCreate('admin'));
    configureWhatsApp();

    $this->actingAs($admin)->post(route('admin.website-settings.update'), [
        'whatsapp_notification_number' => '919999999999',
        'whatsapp_access_token' => '',
    ]);

    expect(WebsiteSetting::getValue('whatsapp_access_token'))->toBe('secret-token')
        ->and(WebsiteSetting::getValue('whatsapp_notification_number'))->toBe('919999999999');
});
