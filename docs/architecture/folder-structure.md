# Key File Map

This section helps you locate the most important files and directories in the URLC codebase.

```text
urlc-system/
├── app/
│   ├── Http/
│   │   ├── Controllers/       # Request Handlers & Business Logic
│   │   │   ├── Admin/         # Admin Management (Users, Proposals, Settings)
│   │   │   ├── Auth/          # Login, Register, Password Reset controllers
│   │   │   └── Controller.php # Base Controller class
│   │   └── Middleware/        # Authentication, Guest, and Role Middlewares
│   ├── Models/                # Eloquent Database Models
│   │   ├── User.php           # User accounts (roles, approvals)
│   │   ├── ResearchProposal.php # Proposal states, tracks, and metadata
│   │   └── ActivityLog.php    # Audit Logs (handled by Spatie)
│   └── Notifications/         # Mail and database system notifications
│
├── config/                    # System Configuration files
│   ├── database.php           # DB credentials & connection setup
│   └── filesystems.php        # S3 storage bucket configuration
│
├── database/
│   ├── migrations/            # DB Schema definitions & updates
│   └── seeders/               # Test data seeds (roles, users)
│
├── resources/
│   ├── views/                 # Blade HTML templates
│   │   ├── admin/             # Administrator dashboards
│   │   ├── researcher/        # Researcher creation and edit forms
│   │   ├── layouts/           # Master layouts (app, navigation, guest)
│   │   └── auth/              # Auth pages (login, registration)
│   └── css/                   # Tailwind config & style definitions
│
└── routes/
    └── web.php                # Application Routing
```

## Key Files to Remember

### 1. `routes/web.php`
Defines all web endpoints. Routes are grouped by authentication (`auth` middleware) and role-specific permissions (e.g. `role:admin`).

### 2. `app/Models/ResearchProposal.php`
Defines the database schema bindings and relationships for research proposals. Important attributes:
*   `status`: Tracked as `draft`, `submitted`, `under_review`, `approved`, `rejected`.
*   `phase`: Development milestones.
*   `collaborators()`: Relationship with secondary researchers.

### 3. `app/Http/Controllers/Auth/RegisteredUserController.php`
Controls registration validation. Enforces that all registering users provide a valid `role` and determines if approval is required before logging in.
