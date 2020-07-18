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

## Verify : Verify User Email / Phone [POST /auth/verify]


+ Request (application/json)
    + Body

            {
                "otp": "[1,2,3,4,5,6]"
            }

+ Response 200 (application/json)
    + Body

            {
                "message": "success"
            }

+ Response 401 (application/json)
    + Body

            {
                "error": {
                    "message": [
                        "failed"
                    ]
                }
            }

## Test Route / Test If User Verified [GET /auth/verified]


+ Response 200 (application/json)
    + Body

            {
                "message": "Verified"
            }

+ Response 403 (application/json)
    + Body

            {
                "error": {
                    "message": "Your email address is not verified."
                }
            }

## Test Route / Test If User Verified [GET /auth/reverify]


+ Response 200 (application/json)
    + Body

            {
                "message": "Verified"
            }

+ Response 403 (application/json)
    + Body

            {
                "error": {
                    "message": "Your email address is already verified."
                }
            }

# Users [/auth/login]

## Login Route [GET /auth/login/{service}]


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
                        "Failed"
                    ]
                }
            }

## Callback Url [GET /auth/login/{service}/callback]


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
                        "Failed"
                    ]
                }
            }