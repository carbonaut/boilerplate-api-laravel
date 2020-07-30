openapi: 3.0.0
info:
  version: 0.0.1
  title: Carbonaut API
  description: Carbonaut API
  termsOfService: https://carbonaut.io/
  contact:
    name: Carbonaut
    email: contato@carbonaut.io
    url: https://carbonaut.io/
  license:
    name: Proprietary
    url: http://carbonaut.io/
servers:
  - description: Select an environment
  - url: http://api.localhost:8000
    description: Local environment
  - url: {{ config('app.development_domain') }}
    description: Development environment
  - url: {{ config('app.production_domain') }}
    description: Production environment
tags:
  - name: auth
    description: Authentication routes
  - name: user
    description: User routes
  - name: maintenance
    description: Maintenance routes
  - name: metadata
    description: Metadata routes
  - name: status
    description: Status routes
  - name: email
    description: Email routes

paths:
  /auth/login:
    post:
      tags:
        - auth
      summary: User Auth
      description: OAuth2 Auth using Email and Password.
      requestBody:
        required: true
        content:
          application/json:
            schema:
              $ref: "#/components/schemas/AuthLogin"
      responses:
        200:
          description: Success
        400:
          description: Bad Request
        401:
          description: Unauthenticated
        403:
          description: Unauthorized
        404:
          description: Not Found
        default:
          $ref: "#/components/responses/Default"

  /auth/refresh:
    post:
      tags:
        - auth
      summary: User Refresh Auth
      description: OAuth2 Auth using Refresh Token.
      requestBody:
        required: true
        content:
          application/json:
            schema:
              $ref: "#/components/schemas/RefreshLogin"
      responses:
        200:
          description: Success
        400:
          description: Bad Request
        401:
          description: Unauthenticated
        403:
          description: Unauthorized
        404:
          description: Not Found
        default:
          $ref: "#/components/responses/Default"

  /auth/register:
    post:
      tags:
        - auth
      summary: Creates a new user account
      description: |
        Creates a new user account.
        Gender codes notation in https://en.wikipedia.org/wiki/ISO/IEC_5218 (only using 1, 2 and 9)
      requestBody:
        description: User registration data
        required: true
        content:
          application/json:
            schema:
              $ref: "#/components/schemas/AuthRegister"
      responses:
        200:
          description: Success
        400:
          description: Bad Request
        401:
          description: Unauthenticated
        403:
          description: Unauthorized
        404:
          description: Not Found
        default:
          $ref: "#/components/responses/Default"

  /auth/email/verification/request:
    post:
      tags:
        - auth
      summary: Requests email verification code
      description: Requests email verification code
      parameters:
      requestBody:
        required: true
        content:
          application/json:
            schema:
              $ref: "#/components/schemas/EmailVerificationRequest"
      responses:
        200:
          description: Success
        400:
          description: Bad Request
        401:
          description: Unauthenticated
        403:
          description: Unauthorized
        404:
          description: Not Found
        default:
          $ref: "#/components/responses/Default"

  /auth/email/verification/confirm:
    post:
      tags:
        - auth
      summary: Confirms email verification code
      description: Confirms email verification code
      requestBody:
        required: true
        content:
          application/json:
            schema:
              $ref: "#/components/schemas/EmailVerificationConfirm"
      responses:
        200:
          description: Success
        400:
          description: Bad Request
        401:
          description: Unauthenticated
        403:
          description: Unauthorized
        404:
          description: Not Found
        default:
          $ref: "#/components/responses/Default"

  /auth/password/reset/request:
    post:
      tags:
        - auth
      summary: Requests password reset email
      description: Requests password reset email
      requestBody:
        required: true
        content:
          application/json:
            schema:
              $ref : "#/components/schemas/PasswordResetRequest"
      responses:
        200:
          description: Success
        400:
          description: Bad Request
        401:
          description: Unauthenticated
        403:
          description: Unauthorized
        404:
          description: Not Found
        default:
          $ref: "#/components/responses/Default"

  /auth/password/reset/submit:
    post:
      tags:
        - auth
      summary: Confirms email verification code
      description: Confirms email verification code
      requestBody:
        required: true
        content:
          application/json:
            schema:
              $ref: "#/components/schemas/PasswordResetSubmit"
      responses:
        200:
          description: Success
        400:
          description: Bad Request
        401:
          description: Unauthenticated
        403:
          description: Unauthorized
        404:
          description: Not Found
        default:
          $ref: "#/components/responses/Default"

  /user:
    get:
      tags:
        - user
      summary: User information
      description: Return the users information
      security:
        - BearerAuth: []
      responses:
        200:
          description: Success
        400:
          description: Bad Request
        401:
          description: Unauthenticated
        403:
          description: Unauthorized
        404:
          description: Not Found
        default:
          $ref: "#/components/responses/Default"
    patch:
      tags:
        - user
      summary: Update the user data
      description: Update the user data
      security:
        - BearerAuth: []
      requestBody:
        content:
          application/json:
            schema:
              $ref: "#/components/schemas/UserPatchData"
      responses:
        200:
          description: Success
        400:
          description: Bad Request
        401:
          description: Unauthenticated
        403:
          description: Unauthorized
        404:
          description: Not Found
        default:
          $ref: "#/components/responses/Default"

  /user/devices:
    post:
      tags:
        - user
      summary: Add or update device from the user
      description: Add or update device from the user
      security:
        - BearerAuth: []
      requestBody:
        content:
          application/json:
            schema:
              $ref: "#/components/schemas/Device"
      responses:
        200:
          description: Success
        400:
          description: Bad Request
        401:
          description: Unauthenticated
        403:
          description: Unauthorized
        404:
          description: Not Found
        default:
          $ref: "#/components/responses/Default"

  /user/pushes/{push_notification_id}:
    post:
      tags:
        - user
      summary: Set an user push notification as opened
      description: Set an user push notification as opened
      security:
        - BearerAuth: []
      parameters:
        - $ref: "#/components/parameters/PushNotificationId"
      requestBody:
        content:
          application/json:
            schema:
              $ref: "#/components/schemas/PushNotificationRead"
      responses:
        200:
          description: Success
        400:
          description: Bad Request
        401:
          description: Unauthenticated
        403:
          description: Unauthorized
        404:
          description: Not Found
        default:
          $ref: "#/components/responses/Default"

  /user/logout:
    post:
      tags:
        - user
      summary: Revoke current user authorization token
      description: Revoke user authorization token that is currently being used
      security:
        - BearerAuth: []
      responses:
        200:
          description: Success
        400:
          description: Bad Request
        401:
          description: Unauthenticated
        403:
          description: Unauthorized
        404:
          description: Not Found
        default:
          $ref: "#/components/responses/Default"

  /user/logout/all:
    post:
      tags:
        - user
      summary: Revoke all user authorization tokens
      description: Revoke all user authorization tokens
      security:
        - BearerAuth: []
      responses:
        200:
          description: Success
        400:
          description: Bad Request
        401:
          description: Unauthenticated
        403:
          description: Unauthorized
        404:
          description: Not Found
        default:
          $ref: "#/components/responses/Default"

  /user/password/change:
    post:
      tags:
        - user
      summary: Change the users password.
      description: Change the users password if given the current one.
      security:
        - BearerAuth: []
      requestBody:
        content:
          application/json:
            schema:
              $ref: "#/components/schemas/PasswordChange"
      responses:
        200:
          description: Success
        400:
          description: Bad Request
        401:
          description: Unauthenticated
        403:
          description: Unauthorized
        404:
          description: Not Found
        default:
          $ref: "#/components/responses/Default"

  /emails/{email_id}/read:
    get:
      tags:
        - email
      summary: Set an email as read
      description: Set an email as read
      parameters:
        - $ref: "#/components/parameters/EmailID"
      responses:
        200:
          description: Success
        400:
          description: Bad Request
        401:
          description: Unauthenticated
        403:
          description: Unauthorized
        404:
          description: Not Found
        default:
          $ref: "#/components/responses/Default"

  /maintenance/enable:
    post:
      tags:
        - maintenance
      summary: Enable maintenance mode
      description: Enable maintenance mode
      security:
        - BearerAuth: []
      requestBody:
        required: true
        content:
          application/json:
            schema:
              $ref: "#/components/schemas/MaintenanceEnable"
      responses:
        200:
          description: Success
        400:
          description: Bad Request
        401:
          description: Unauthenticated
        403:
          description: Unauthorized
        404:
          description: Not Found
        default:
          $ref: "#/components/responses/Default"

  /maintenance/disable:
    post:
      tags:
        - maintenance
      summary: Disable maintenance mode
      description: Disable maintenance mode
      security:
        - BearerAuth: []
      responses:
        200:
          description: Success
        400:
          description: Bad Request
        401:
          description: Unauthenticated
        403:
          description: Unauthorized
        404:
          description: Not Found
        default:
          $ref: "#/components/responses/Default"

  /metadata/languages/{search_string}:
    get:
      tags:
        - metadata
      summary: Search languages by search string.
      description: Search languages by search string.
      security:
        - BearerAuth: []
      parameters:
        - $ref: "#/components/parameters/SearchString"
      responses:
        200:
          description: Success
        400:
          description: Bad Request
        401:
          description: Unauthenticated
        403:
          description: Unauthorized
        404:
          description: Not Found
        default:
          $ref: "#/components/responses/Default"

  /metadata/phrases/{type}:
    get:
      tags:
        - metadata
      summary: Search phrases by locale and type.
      description: Search phrases by locale and type.
      parameters:
        - $ref: "#/components/parameters/PhraseType"
        - $ref: "#/components/parameters/AcceptLanguage"
      responses:
        200:
          description: Success
        400:
          description: Bad Request
        401:
          description: Unauthenticated
        403:
          description: Unauthorized
        404:
          description: Not Found
        default:
          $ref: "#/components/responses/Default"
  /status:
    get:
      tags:
        - status
      summary: Returns the API status
      description: Returns the API status
      responses:
        200:
          description: Success
        400:
          description: Bad Request
        401:
          description: Unauthenticated
        403:
          description: Unauthorized
        404:
          description: Not Found
        418:
          description: API Under Maintenance
        500:
          description: Internal Server Error
        default:
          $ref: "#/components/responses/Default"

components:
  securitySchemes:
    BearerAuth:
      description: Token obtained in the `access_token` property of the `/auth/login` or `/auth/refresh` responses (no need to add the `Bearer` in front)
      type: http
      scheme: bearer
  responses:
    Sucess:
      description: Sucess
    NotFound:
      description: Not Found
    BadRequest:
      description: Bad Request
    Unauthenticated:
      description: Unauthenticated
    Unauthorized:
      description: Unauthorized
    UnderMaintenance:
      description: API Under Maintenance
    InternalServerError:
      description: Internal Server Error
    Default:
      content:
        application/json:
          schema:
            $ref: "#/components/schemas/Error"

  parameters:
    SearchString:
      name: "search_string"
      in: "path"
      description: "Search String"
      required: false
      schema:
        type: "string"
    PhraseType:
      name: "type"
      in: "path"
      description: "Phrase Type"
      required: true
      schema:
        type: "string"
    AcceptLanguage:
      name: "Accept-Language"
      in: "header"
      description: "Accept-Language expects an existing locale property of the `/metadata/languages/{search_string}` response"
      schema:
        type: string
    PushNotificationId:
      name: "push_notification_id"
      in: "path"
      description: "Push Notification Id"
      required: true
      schema:
        type: "string"
    EmailID:
      name: email_id
      in: "path"
      description: Email ID
      required: true
      schema:
        type: string

  schemas:
    Error:
      type: object
      properties:
        error:
          type: string
          example: The password is incorrect
        message:
          type: string
          description: Localized error message,proper to be shown to the user
          example: Das Passwort ist falsch.
    AuthLogin:
      type: object
      properties:
        username:
          type: string
        password:
          type: string
        client:
          type: string
    RefreshLogin:
      type: object
      properties:
        refresh_token:
          type: string
        client:
          type: string
    AuthRegister:
      type: object
      properties:
        first_name:
          type: string
          required: true
        last_name:
          type: string
          required: true
        date_of_birth:
          type: string
          example: '1970-01-13'
          required: true
        email:
          type: string
          required: true
        password:
          type: string
          required: true
        password_confirmation:
          type: string
          required: true
        gender:
          type: integer
          required: true
          enum:
            - 1
            - 2
            - 9
        language_id:
          type: "string"
    EmailVerificationRequest:
      type: object
      properties:
        email:
          type: string
    EmailVerificationConfirm:
      type: object
      properties:
        email:
          type: string
        email_verification_code:
          type: integer
    MaintenanceEnable:
      type: object
      properties:
        message:
          type: string
        allow:
          type: string
    PasswordResetRequest:
      type: object
      properties:
        email:
          type: string
    PasswordResetSubmit:
      type: object
      properties:
        email:
          type: string
        password:
          type: string
        password_confirmation:
          type: string
        token:
          type: string
    PushNotificationRead:
      type: "object"
      properties:
        coldstart:
          type: "boolean"
          required: false
        foreground:
          type: "boolean"
          required: false
    UserPatchData:
      type: "object"
      properties:
        language_id:
          type: "string"
    PasswordChange:
      type: object
      properties:
        old_password:
          type: string
          required: true
        new_password:
          type: string
          required: true
        new_password_confirmation:
          type: string
          required: true
    Device:
      type: "object"
      properties:
        manufacturer:
          type: "string"
          required: true
        model:
          type: "string"
          required: true
        platform:
          type: "string"
          required: true
        version:
          type: "string"
          description: "OS Version"
          required: true
        uuid:
          description: "Unique identifier for the device"
          type: "string"
          required: true
        is_virtual:
          type: "boolean"
          required: true
        serial:
          type: "string"
          required: false
        app_version:
          type: "string"
          required: false
        push_token:
          type: "string"
          required: false
