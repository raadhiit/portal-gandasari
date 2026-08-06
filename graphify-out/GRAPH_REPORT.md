# Graph Report - C:/laragon/www/portal-gandasari  (2026-08-06)

## Corpus Check
- Corpus is ~35,315 words - fits in a single context window. You may not need a graph.

## Summary
- 660 nodes · 1090 edges · 116 communities (98 shown, 18 thin omitted)
- Extraction: 95% EXTRACTED · 5% INFERRED · 0% AMBIGUOUS · INFERRED: 52 edges (avg confidence: 0.81)
- Token cost: 62,221 input · 0 output

## Community Hubs (Navigation)
- Livewire Settings & Notification UI
- Item Request Actions & Password Reset
- User Model & Authorization
- Item Request Export Download
- Composer Scripts Config
- Item Request Approved Export & Models
- NPM Frontend Dependencies
- Password Security & Validation
- Database Seeders
- ItemRequest Workflow Redesign Proposal
- Composer Dev Dependencies
- Composer Package Metadata
- Composer Core Dependencies
- CI/CD Pipeline Config
- Profile Validation Rules
- Logout & Session Handling
- Composer Plugin Config
- User Factory
- Permission Registry
- Composer Autoload Mapping
- Pest Test Bootstrap
- Item Request Index Tracking
- Composer Installer Extras
- Graphify Usage Rules
- Item Request Create Form
- Composer Package Keywords
- Laravel Branding Assets
- Notification Center View
- Export Index View
- Profile Settings View
- Community 57
- Community 58
- Community 59
- Community 60
- Community 61
- Community 62
- Community 63
- Community 64
- Community 65
- Community 66
- Community 87

## God Nodes (most connected - your core abstractions)
1. `ItemRequest` - 69 edges
2. `User` - 66 edges
3. `WorkflowNotificationService` - 26 edges
4. `WorkflowAuditService` - 20 edges
5. `ItemRequestExport` - 15 edges
6. `ItemRequestPolicy` - 13 edges
7. `scripts` - 13 edges
8. `ItemRequestHistory` - 12 edges
9. `ExportItemRequest` - 12 edges
10. `require-dev` - 12 edges

## Surprising Connections (you probably didn't know these)
- `Export Feature (WaitingForIT / Finished / All Filters)` --references--> `ExportIndex`  [INFERRED]
  ItemRequest_Workflow_Change_Proposal_v2.md → app/Livewire/ItemRequest/ExportIndex.php
- `AccountItemRequestAction (Required Code Change)` --references--> `AccountItemRequestAction`  [EXTRACTED]
  ItemRequest_Workflow_Change_Proposal_v2.md → Modules/ItemRequest/Actions/AccountItemRequestAction.php
- `SubmitITProofAction (Required Code Change)` --references--> `SubmitITProofAction`  [EXTRACTED]
  ItemRequest_Workflow_Change_Proposal_v2.md → Modules/ItemRequest/Actions/SubmitITProofAction.php
- `ItemRequestApprovedExport` --references--> `ItemRequest`  [EXTRACTED]
  app/Exports/ItemRequestApprovedExport.php → Modules/ItemRequest/Models/ItemRequest.php
- `ItemRequestStatusChangedNotification` --references--> `ItemRequest`  [EXTRACTED]
  app/Notifications/ItemRequestStatusChangedNotification.php → Modules/ItemRequest/Models/ItemRequest.php

## Import Cycles
- None detected.

## Hyperedges (group relationships)
- **CI/CD Pipeline (Tests + Dependency Updates)** — _github_dependabot_config, _github_workflows_tests_ci_workflow, _github_workflows_tests_ci_workflow_composer_setup, _github_workflows_tests_ci_workflow_composer_ci_check [INFERRED 0.85]
- **ItemRequest Workflow Redesign (Proposal v2)** — itemrequest_workflow_change_proposal_v2_existing_workflow, itemrequest_workflow_change_proposal_v2_proposed_workflow, itemrequest_workflow_change_proposal_v2_status_changes, itemrequest_workflow_change_proposal_v2_accountitemrequestaction_ref, itemrequest_workflow_change_proposal_v2_submititproofaction_ref [EXTRACTED 1.00]
- **Laravel Branding Assets (Favicon & Touch Icon)** — public_favicon_icon, public_apple_touch_icon_icon, laravel_framework [INFERRED 0.85]

## Communities (116 total, 18 thin omitted)

### Community 0 - "Livewire Settings & Notification UI"
Cohesion: 0.06
Nodes (18): NotificationCenter, Appearance, DeleteUserForm, Illuminate\Contracts\View\View, Illuminate\Foundation\Auth\Access\AuthorizesRequests, Illuminate\Http\UploadedFile, Illuminate\Support\Facades\Notification, Livewire\Component (+10 more)

### Community 1 - "Item Request Actions & Password Reset"
Cohesion: 0.07
Nodes (15): HasOne, Illuminate\Database\Eloquent\Relations\HasMany, Illuminate\Support\Collection, AccountItemRequestAction, CreateItemRequestAction, RejectItemRequestAction, ReviseItemRequestAction, SubmitItemRequestAction (+7 more)

### Community 2 - "User Model & Authorization"
Cohesion: 0.09
Nodes (11): User, UserObserver, RolePolicy, UserPolicy, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable, UserEdit (+3 more)

### Community 3 - "Item Request Export Download"
Cohesion: 0.08
Nodes (12): Controller, ItemRequestExportDownloadController, ExportItemRequest, ExportIndex, ItemRequestExport, ItemRequestExportReadyNotification, ItemRequestStatusChangedNotification, Illuminate\Bus\Queueable (+4 more)

### Community 4 - "Composer Scripts Config"
Cohesion: 0.06
Nodes (37): scripts, ci:check, dev, lint, lint:check, post-autoload-dump, post-create-project-cmd, post-root-package-install (+29 more)

### Community 5 - "Item Request Approved Export & Models"
Cohesion: 0.10
Nodes (12): ItemRequestApprovedExport, Coa, ItemCategory, Illuminate\Database\Eloquent\Model, Illuminate\Database\Eloquent\Relations\BelongsTo, Maatwebsite\Excel\Concerns\FromArray, Maatwebsite\Excel\Concerns\ShouldAutoSize, Maatwebsite\Excel\Concerns\WithHeadings (+4 more)

### Community 6 - "NPM Frontend Dependencies"
Cohesion: 0.07
Nodes (26): concurrently, laravel-vite-plugin, lightningcss-linux-x64-gnu, dependencies, concurrently, laravel-vite-plugin, tailwindcss, @tailwindcss/vite (+18 more)

### Community 7 - "Password Security & Validation"
Cohesion: 0.11
Nodes (7): ResetUserPassword, Security, AppServiceProvider, FortifyServiceProvider, Illuminate\Support\ServiceProvider, Laravel\Fortify\Actions\DisableTwoFactorAuthentication, Laravel\Fortify\Contracts\ResetsUserPasswords

### Community 8 - "Database Seeders"
Cohesion: 0.12
Nodes (8): AdminUserSeeder, CoaSeeder, DatabaseSeeder, DepartmentSeeder, ItemRequestExportDemoSeeder, MasterDataSeeder, RoleAndPermissionSeeder, Illuminate\Database\Seeder

### Community 9 - "ItemRequest Workflow Redesign Proposal"
Cohesion: 0.15
Nodes (14): AccountItemRequestAction (Required Code Change), One Action = One Use Case Pattern, Current Workflow Problems, Existing ItemRequest Workflow (Draft to Approved), Expected Benefits, Export Feature (WaitingForIT / Finished / All Filters), Export Query Default & Filters, Implementation Principles (+6 more)

### Community 10 - "Composer Dev Dependencies"
Cohesion: 0.17
Nodes (12): require-dev, fakerphp/faker, larastan/larastan, laravel/boost, laravel/pail, laravel/pao, laravel/pint, laravel/sail (+4 more)

### Community 11 - "Composer Package Metadata"
Cohesion: 0.18
Nodes (10): autoload-dev, psr-4, description, license, minimum-stability, name, prefer-stable, Tests\\ (+2 more)

### Community 12 - "Composer Core Dependencies"
Cohesion: 0.18
Nodes (11): require, laravel/chisel, laravel/fortify, laravel/framework, laravel/tinker, livewire/blaze, livewire/flux, livewire/livewire (+3 more)

### Community 13 - "CI/CD Pipeline Config"
Cohesion: 0.33
Nodes (7): Dependabot GitHub Actions Update Config, Tests CI Workflow (GitHub Actions), actions/checkout Action, composer ci:check Script Step, composer setup Script Step, actions/setup-node Action (Node 22), shivammathur/setup-php Action (PHP 8.4)

### Community 14 - "Profile Validation Rules"
Cohesion: 0.38
Nodes (4): emailRules(), nameRules(), profileRules(), Profile

### Community 15 - "Logout & Session Handling"
Cohesion: 0.38
Nodes (3): Logout, Illuminate\Http\RedirectResponse, Livewire\Features\SupportRedirects\Redirector

### Community 16 - "Composer Plugin Config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 17 - "User Factory"
Cohesion: 0.43
Nodes (3): UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 19 - "Composer Autoload Mapping"
Cohesion: 0.33
Nodes (6): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\, Modules\\

### Community 22 - "Composer Installer Extras"
Cohesion: 0.40
Nodes (5): extra, laravel, post-create-project, dont-discover, installer

### Community 23 - "Graphify Usage Rules"
Cohesion: 0.50
Nodes (4): graphify-out/graph.json, graphify-out/GRAPH_REPORT.md, Graphify Usage Rules (CLAUDE.md), graphify-out/wiki/index.md

### Community 25 - "Composer Package Keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

### Community 52 - "Laravel Branding Assets"
Cohesion: 1.00
Nodes (3): Laravel Framework (Brand Identity), Apple Touch Icon (Laravel Logo PNG), Favicon (Laravel Logo SVG)

## Knowledge Gaps
- **105 isolated node(s):** `deleteRole({{ $role->id }})`, `addItem`, `removeItem({{ $index }})`, `submit`, `$schema` (+100 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **18 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `ItemRequest` connect `Item Request Actions & Password Reset` to `Livewire Settings & Notification UI`, `User Model & Authorization`, `Item Request Export Download`, `Item Request Approved Export & Models`, `Database Seeders`, `Item Request Index Tracking`?**
  _High betweenness centrality (0.079) - this node is a cross-community bridge._
- **Why does `User` connect `User Model & Authorization` to `Livewire Settings & Notification UI`, `Item Request Actions & Password Reset`, `Item Request Approved Export & Models`, `Password Security & Validation`, `Database Seeders`?**
  _High betweenness centrality (0.069) - this node is a cross-community bridge._
- **Why does `scripts` connect `Composer Scripts Config` to `Composer Package Metadata`?**
  _High betweenness centrality (0.012) - this node is a cross-community bridge._
- **Are the 5 inferred relationships involving `ItemRequest` (e.g. with `.queueExport()` and `.render()`) actually correct?**
  _`ItemRequest` has 5 INFERRED edges - model-reasoned connections that need verification._
- **Are the 6 inferred relationships involving `User` (e.g. with `.configureDefaults()` and `.run()`) actually correct?**
  _`User` has 6 INFERRED edges - model-reasoned connections that need verification._
- **What connects `deleteRole({{ $role->id }})`, `addItem`, `removeItem({{ $index }})` to the rest of the system?**
  _105 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Livewire Settings & Notification UI` be split into smaller, more focused modules?**
  _Cohesion score 0.06498599439775911 - nodes in this community are weakly interconnected._