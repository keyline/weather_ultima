---
paths:
  - 'resources/views/admin/**'
  - 'resources/views/components/admin/**'
  - 'resources/css/app.css'
---

# Admin form / control design system

**One source of truth** — `@layer components` in `resources/css/app.css`. Never style admin
inputs/buttons/cards with ad-hoc utility strings; use these classes so height, radius, focus
and spacing stay identical everywhere.

- **Controls** (`.admin-input`, `.admin-select`, `.admin-textarea`): 4px radius, 1px slate-300 border, 44px height (textarea min 120px, `resize-y`), 14px text, subtle 1px brand focus ring (no glow), proper `:disabled`. Add `--invalid` modifier (or the components do it from `$errors`) for the error state.
- **Buttons**: `.admin-btn` base (4px radius, 44px, `disabled:opacity-60`) + one of `--primary` / `--ghost` / `--danger` / `--success`, plus optional `--sm` (36px).
- **Files**: `.admin-file`. **Cards/sections**: `.admin-card` / `.admin-section` (8px radius). **Alerts**: `.admin-alert` + `--success` / `--error`. **Badges**: `.admin-badge`. Labels: `.admin-label` (+ `.admin-required` for the `*`). Field help: `.admin-hint`. Field error: `.admin-error`.
- Sidebar nav pills are `rounded-md` (6px); nothing in the admin uses `rounded-xl`/`rounded-2xl`.

## Reusable field components
`<x-admin.input>`, `<x-admin.textarea>`, `<x-admin.select :options="[...]">` — each renders label
(+ required `*`), the control, `old()` value, hint, and the field-level error automatically.
Props: `name` (required), `label`, `type`, `value`/`selected`, `placeholder` (pass through as an
attribute), `hint`, `required`, `rows`. Form-heavy pages (`_form.blade.php`, settings) use these;
one-off search boxes use the raw `.admin-*` classes.

Every field needs a clear label and a context-specific placeholder ("Enter the customer's full
name", not "Enter value"). Mark only required fields with `*`.
