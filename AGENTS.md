# E-Voting System Guidelines

This repository is a production-oriented Flexible E-Voting System built with Laravel 13, Livewire 4, and Tailwind CSS.

## Architecture Principles
- Thin Controllers & Livewire Components (receive request -> validate -> authorize -> call Action/Service -> response)
- Single-use Action Classes for business flows (e.g. `SubmitBallotAction`, `CreateElectionAction`)
- Domain Services for reusable business logic (e.g. `VotingService`, `ResultService`, `AuditLogService`)
- Typed Readonly DTOs for inter-layer data transfer
- Query Objects for complex aggregates & reads (e.g. `GetElectionResults`, `GetLiveVotingStatistics`)
- Domain Events & Listeners for audit logging & side effects
- Policies for granular authorization
- UUID for all primary entity keys
- Database transactions with row locking (`lockForUpdate`) for double-vote protection
- Strict privacy: no direct linkage between user and ballot choices
- No inline scripts/styles in Blade views (all custom CSS/JS in `public/css` and `public/js`)
