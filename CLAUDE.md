## Pre-execution context rules

Before executing any prompt (planning, coding, or answering a question), always read these first, in order, so work is grounded in existing project rules and decisions:

1. `.agents/` — project rules, guidelines, and skills (e.g. `laravel-best-practices`, `livewire-development`, `fluxui-development`, `fortify-development`, `pest-testing`, `tailwindcss-development`, `Frontend-Design`, `design-interface`, `blaze-optimize`). Check for a skill relevant to the task before writing code.
2. `.claude/` — skills and frontend (FE) rules/conventions used by Claude Code specifically (`.claude/CLAUDE.md`, `.claude/skills/`).
3. `AGENTS.md` (project root) — cross-agent operating rules for this repo.
4. `docs/` — numbered project specification docs (architecture, database, authorization, models, workflow, naming convention, module structure, state machine, permission matrix, etc.). Read the doc(s) relevant to the area being touched before making changes.

Do not skip this step even for small tasks — these folders contain the authoritative, up-to-date rules for this project and take precedence over assumptions made from code alone.

## graphify

This project has a knowledge graph at graphify-out/ with god nodes, community structure, and cross-file relationships.

Rules:
- For codebase questions, first run `graphify query "<question>"` when graphify-out/graph.json exists. Use `graphify path "<A>" "<B>"` for relationships and `graphify explain "<concept>"` for focused concepts. These return a scoped subgraph, usually much smaller than GRAPH_REPORT.md or raw grep output.
- If graphify-out/wiki/index.md exists, use it for broad navigation instead of raw source browsing.
- Read graphify-out/GRAPH_REPORT.md only for broad architecture review or when query/path/explain do not surface enough context.
- After modifying code, run `graphify update .` to keep the graph current (AST-only, no API cost).
