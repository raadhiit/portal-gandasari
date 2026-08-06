# Portal Gandasari - Item Request Workflow Change Proposal

> This document describes the proposed workflow changes for the Item
> Request module and also serves as the implementation guideline for
> future development.

------------------------------------------------------------------------

# Project Context

This proposal follows the existing architecture of Portal Gandasari.

## Architecture

-   Modular Monolith
-   Feature-first architecture (`Modules/*`)
-   `ItemRequest` is the reference implementation for future modules.
-   Business logic belongs inside **Actions**.
-   Livewire acts only as the UI orchestration layer.
-   Changes should be incremental and compatible with the existing
    codebase.

------------------------------------------------------------------------

# Development Rules

The implementation must continue following the existing project
conventions.

## Backend

-   One Action = One Use Case.
-   Do not move business logic into Livewire components.
-   Database transactions belong inside Actions.
-   Respect module boundaries.
-   Avoid generic Service classes when an Action is more appropriate.
-   Reuse existing patterns from `Modules/ItemRequest`.

## Frontend

Based on the provided frontend skills:

-   Work with the existing UI instead of rewriting pages.
-   Prioritize consistency over introducing new design patterns.
-   Always consider loading, empty, and error states.
-   Use responsive layouts.
-   Use GPU-friendly animations (`transform` / `opacity` only).
-   Avoid unnecessary motion in internal application screens.
-   Follow the existing design language of the project.
-   Keep changes incremental and reviewable.

------------------------------------------------------------------------

# Existing Workflow

``` text
Draft
    │
Submit
    ▼
Submitted
    │
Validate
    ▼
Validated
    │
Accounting Approve
    ▼
WaitingForIT
    │
IT Upload Proof
    ▼
Approved
```

## Current Problems

-   Export mixes pending and completed requests.
-   Users cannot determine which requests still require IT processing.
-   "Approved" is ambiguous because approvals happen at multiple stages.

------------------------------------------------------------------------

# Proposed Workflow

``` text
Draft
    │
Submit
    ▼
Submitted
    │
Validate
    ▼
Validated
    │
Accounting Approve
    ▼
WaitingForIT
    │
Approve & Finalize (IT)
    ▼
Finished
```

## Status Changes

  Current        Proposed
  -------------- --------------
  Draft          Draft
  Submitted      Submitted
  Validated      Validated
  WaitingForIT   WaitingForIT
  Approved       Finished
  Rejected       Rejected
  Revised        Revised
  Cancelled      Cancelled

------------------------------------------------------------------------

# Menu Behavior

## After Accounting Approval

When Accounting approves a request:

-   Status becomes **WaitingForIT**
-   The record appears in:
    -   IT Inbox
    -   Export

These represent the active IT processing queue.

------------------------------------------------------------------------

## Export

### Default Filter

WaitingForIT

### Available Filters

-   WaitingForIT
-   Finished
-   All

### WaitingForIT

Display requests that:

-   Have been approved by Accounting.
-   Still require IT processing.
-   Can be exported.

### Finished

Display requests that:

-   Have already been finalized by IT.
-   Act as historical records.
-   No longer appear in the default export queue.

------------------------------------------------------------------------

## IT Inbox

Primary action:

**Approve & Finalize**

This action should:

-   Upload IT proof.
-   Finalize the workflow.
-   Change status:
    -   WaitingForIT → Finished
-   Create history.
-   Send notifications.

------------------------------------------------------------------------

# Required Code Changes

## AccountItemRequestAction

No workflow changes.

    Validated
        ↓
    WaitingForIT

------------------------------------------------------------------------

## SubmitITProofAction

Current:

    WaitingForIT
        ↓
    Approved

Proposed:

    WaitingForIT
        ↓
    Finished

Responsibilities remain:

-   Upload proof.
-   Create history.
-   Send notifications.

Only the final status changes.

------------------------------------------------------------------------

# Export Query

Default:

-   WaitingForIT

Filters:

-   WaitingForIT
-   Finished
-   All

------------------------------------------------------------------------

# Implementation Principles

This change must:

-   Preserve the modular architecture.
-   Follow the ItemRequest module as the implementation reference.
-   Keep business logic inside Actions.
-   Avoid unnecessary refactoring.
-   Keep existing UI intact where possible.
-   Improve workflow visibility instead of introducing unnecessary
    workflow states.

------------------------------------------------------------------------

# Expected Benefits

-   Clear separation between active IT work and completed requests.
-   Export becomes an operational queue instead of a mixed history page.
-   IT Inbox and Export stay synchronized.
-   Final workflow state clearly represents business completion.
-   Minimal architectural impact.
-   Fully aligned with the project's backend architecture and frontend
    development rules.
