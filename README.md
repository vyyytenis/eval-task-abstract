# Symfony Docker
## Getting Started

1. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/) (v2.10+)
2. Run `docker compose build --pull --no-cache` to build fresh images
3. Run `docker compose up` (the logs will be displayed in the current shell)
4. Open `docker compose exec php sh`
5. Run migrations (in step 4 window)
`php bin/console doctrine:database:create
 php bin/console doctrine:messenger:setup-transports
 php bin/console make:migration
 php bin/console doctrine:migrations:migrate `
6. Start workers (in step 4 window)
`php bin/console messenger:consume async -vv`
`php bin/console app:notifications:resend`
7. Open `https://localhost`

# Notification providers are registered and injected in services.yaml

# IMPORTANT Twilio!
* To send sms other than to +37060790942, I need to **verify new number in Twilio account**.
* If you send to other than +37060790942 **it will fail and dummy sms provider will be used** as failover. Simulates a random failure

# IMPORTANT AWS SES!
* You send email to vyyytenis@gmail.com or kareiva.vytenis@gmail.com, counter in AWS goes up.
* To send to other, emails have to be verified, otherwise it will fail and go to Dummy sender. Simulates a random failure

vendor/bin/phpunit tests/Functional/SendNotificationHanlderTest.php

# Whats done

    Send notifications via different channels:
    Provide an abstraction between at least two different messaging service providers.
    Use different messaging services/technologies for communication (e.g., SMS, email, push notification, Facebook Messenger, etc.).

    Providers used: AWS SES, Twilio
    
    Failover support
    Define several providers for the same type of notification channel.
        Real providers: AWS SES, Twilio
        Dummy providers for Email and SMS
    
    Delay and resend notifications if all providers fail.
        Resend worker aded with retry count and time (.env for settings)
        Worker is done by using 

    Configuration-driven:
    Enable/disable different communication channels via configuration.
        Channel can be enabled/disabled using .env
    Send the same notification via several different channels.
        You can specify that in the API payload.

    (Bonus) Throttling:
        Rate limiter added rate_limiter.yaml
    (Bonus) Usage tracking:
        There is logging logic to a file and a seperate database table for notifications.
    
# ROUTES
    [GET] - http://localhost/api/list-notifications
    [POST] - http://localhost/api/send-notifications
    
    Payload example:
    
    {
        "notifications": [
            {
            "userId": 1,
            "channel": "sms",
            "content": "postman sms",
            "receiver": "+37060790942"
            },
            {
            "userId": 1,
            "channel": "sms",
            "content": "phone to fail and go to dummy to retry, dummy will fail or pass randomly",
            "receiver": "+370123123"
            },
            {
            "userId": 1,
            "channel": "email",
            "content": "postman email",
            "receiver": "vyyytenis@gmail.com"
            },
            {
            "userId": 1,
            "channel": "email",
            "content": "email to fail and go to dummy to retry, dummy will fail or pass randomly",
            "receiver": "somenew@gmail.com"
            },
            {
            "userId": 1,
            "channel": "push",
            "content": "Example of none existing channel",
            "receiver": "123456"
            }
        ]
    }
