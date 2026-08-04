# Project Task List: Item Request Redesign

## Done

- [x] Redesign item request detail page with role-focused operations sheet UI
- [x] Align Item Request inbox with functional palette
- [x] Redesign request index and create pages with new visual system
- [x] Verify frontend changes with tests and project checks
- [x] Implement bulk items creation (UI + DB + Actions)
- [x] Implement business unit table and mapping
- [x] Integrate queue-based Excel export with template mapping
- [x] Simplify workflow (Accounting = Final Approval)
- [x] Move navigation source of truth to `config/navigation.php` and remove unused menu/system seed flow
- [x] Add export inbox pagination and loading states
- [x] Fix Excel column mapping for `Persediaan` and `Penjualan`
- [x] Remove requester draft flow: create now auto-submits and redirects to index
- [x] Move attachment upload to create form; show page attachment section is now read-only
- [x] Remove comments and attachment upload form from item request show page
- [x] Add approval tracking modal on item request index
- [x] Restyle approval tracking modal for clearer hierarchy in light/dark mode
- [x] Implement download/open file authorization flow
- [x] Add item code uniqueness check against DB in warehouse approval flow

## In Progress

- [x] Approval workflow revisions after accounting/export
- [x] Enrich requester-facing approval tracking with deeper metadata (future history/duration integration)
- [x] Admin User Management: CRUD with granular role/permission mapping
- [x] Role CRUD with module-based granular permission checklist

## Next

- [ ] Add final IT approval step after accounting/export, including screenshot proof submission flow
- [ ] Show approver list with timestamps in inbox
- [ ] Add elapsed-time information in history entries
- [ ] Track who downloaded export files
- [ ] Add IT menu history with actor information
- [ ] Build item request master selection so requester chooses from dropdown instead of free text
- [ ] Add audit trail observer for remaining modules (optional)

## Handoff Note

### Latest completed work

- `Modules/ItemRequest/Livewire/Create.php`
  - create flow now uploads optional attachment, auto-submits request, then redirects to `item-requests.index`
- `Modules/ItemRequest/resources/views/livewire/create.blade.php`
  - create form copy updated; attachment field added; draft CTA removed
- `Modules/ItemRequest/Livewire/Show.php`
  - comment/upload logic removed; show page now loads requester, histories, attachments, items only
- `Modules/ItemRequest/resources/views/livewire/show.blade.php`
  - comments block removed; attachment list kept read-only
- `Modules/ItemRequest/Livewire/Index.php`
  - approval tracking summary + step timeline data added for requester modal
- `Modules/ItemRequest/resources/views/livewire/index.blade.php`
  - `Track` modal added and redesigned; dark-mode backdrop increased for better hierarchy
- `app/Jobs/ExportItemRequest.php`
  - corrected Excel template mapping: `BL=Persediaan`, `BM=Penjualan`
- `resources/views/layouts/app/sidebar.blade.php` + `config/navigation.php`
  - sidebar now reads from config instead of duplicated hardcode/data schema
- `Modules/ItemRequest/Livewire/Show.php` + `Modules/ItemRequest/Actions/ValidateItemRequestAction.php`
  - item code uniqueness checked against both `items` (master) and `item_request_items` (other request rows), reusing existing DB `unique()` index

### Verification status

- Targeted feature tests, Pint, lint, and PHPStan were passing at latest checkpoint.
- Most recent checked suites included:
  - `tests/Feature/ItemRequestWorkflowTest.php`
  - `tests/Feature/ItemRequestExportTest.php`

### Important behavior changes now live in code

- Requester no longer saves draft first from create page.
- Attachment must be uploaded during create flow if needed.
- Other roles can still read/download attachments from show page.
- Approval tracking for requester currently lives in modal from request index.

### Suggested next implementation order

1. IT approval lane after accounting/export
2. approver list + timestamps in inbox
3. item code uniqueness validation in warehouse approval
4. richer history duration data
5. export download actor tracking
6. requester master item dropdown
