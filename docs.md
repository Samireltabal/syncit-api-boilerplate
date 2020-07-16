FORMAT: 1A

# Syncit

# Users [/auth]

## Login Route [POST /auth/login]


+ Request (application/json)
    + Body

            {
                "email": "foo@example.com",
                "password": "bar"
            }

+ Response 200 (application/json)
    + Body

            {
                "access_token": 10,
                "token_type": "Bearer",
                "expires_in": "token lifetime"
            }

+ Response 401 (application/json)
    + Body

            {
                "error": {
                    "message": [
                        "unauthorised"
                    ]
                }
            }

## Register : Register New User [POST /auth/signup]


+ Request (application/json)
    + Body

            {
                "name": "John doe",
                "email": "foo@example.com",
                "password": "bar",
                "password_confirmation": "bar"
            }

+ Response 200 (application/json)
    + Body

            {
                "access_token": 10,
                "token_type": "Bearer",
                "expires_in": "token lifetime"
            }

+ Response 401 (application/json)
    + Body

            {
                "error": {
                    "message": [
                        "unauthorised"
                    ]
                }
            }